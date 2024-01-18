<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\TranslatorExtractor\CategorySource;
use Yiisoft\TranslatorExtractor\Command\ExtractCommand;
use Yiisoft\TranslatorExtractor\Extractor;
use Yiisoft\Di\Container;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;
use Yiisoft\Yii\Console\Application;

final class ExtractCommandTest extends TestCase
{
    private Application $application;
    private CommandTester $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configContainer($this->getDefinitions());

        $this->command = new CommandTester($this->application->find('translator/extract'));
    }

    public function testEmpty(): void
    {
        $this->command->execute(['path' => __DIR__ . '/empty']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Messages not found', $output);
    }

    public function testSimple(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Category: "app", messages found: 2.', $output);
    }

    public function testWithoutDefaultCategory(): void
    {
        $this->configContainer($this->getWithoutDefaultCategoryDefinitions());
        $this->command = new CommandTester($this->application->find('translator/extract'));

        $this->command->execute(['path' => __DIR__ . '/not-empty']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Default category was not found in a list of Categories.', $output);
    }

    public function testExcept(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '--except' => ['**/**.php']]);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Messages not found', $output);
    }

    public function testOnly(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '--only' => ['**/1.php']]);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Messages not found', $output);
    }

    public function testResult(): void
    {
        $result = $this->command->execute(['path' => __DIR__ . '/not-empty']);

        $this->assertSame(0, $result);
    }

    public function testSimpleWithSetCategory(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '--category' => 'app2']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Category: "app2", messages found: 2', $output);
    }

    public function testSimpleWithSetLanguages(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '-L' => 'ru,en']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Languages: ru, en', $output);
    }

    private function configContainer(array $definitions): void
    {
        $config = ContainerConfig::create()
            ->withDefinitions($definitions);
        $container = new Container($config);
        $this->application = $container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $container,
            [
                'translator/extract' => ExtractCommand::class,
            ]
        );

        $this->application->setCommandLoader($loader);
    }

    private function getDefinitions(): array
    {
        return [
            Extractor::class => [
                '__construct()' => [
                    [
                        new CategorySource('app', $this->getMessageSource(), $this->getMessageSource()),
                        new CategorySource('app2', $this->getMessageSource(), $this->getMessageSource()),
                    ],
                ],
            ],
        ];
    }

    private function getWithoutDefaultCategoryDefinitions(): array
    {
        return [
            Extractor::class => [
                '__construct()' => [
                    [
                        new CategorySource('app2', $this->getMessageSource(), $this->getMessageSource()),
                    ],
                ],
            ],
        ];
    }

    private function getMessageSource()
    {
        return new class () implements MessageReaderInterface, MessageWriterInterface {
            private array $messages = [];

            public function getMessage(string $id, string $category, string $locale, array $parameters = []): ?string
            {
                return $this->messages[$category][$locale][$id]['message'];
            }

            public function getMessages(string $category, string $locale): array
            {
                return $this->messages[$category][$locale] ?? [];
            }

            public function write(string $category, string $locale, array $messages): void
            {
                $this->messages[$category][$locale] = $messages;
            }
        };
    }
}
