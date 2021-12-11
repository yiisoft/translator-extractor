<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Psr\Container\ContainerInterface;
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
    private ContainerInterface $container;
    private Application $application;
    private CommandTester $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configContainer($this->getDefinitions());

        $this->command = new CommandTester($this->application->find('translator/extract'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
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
        $this->assertStringContainsString('Category: "app", messages found: 2', $output);
    }

    public function testWithoutDefaultCategory(): void
    {
        $this->configContainer($this->getWithoutDefaultCategoryDefinitions());
        $this->command = new CommandTester($this->application->find('translator/extract'));

        $this->command->execute(['path' => __DIR__ . '/not-empty']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Default category not found in list of Categories', $output);
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

    public function testSimpleWithSettedCategory(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '--category' => 'app2']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Category: "app2", messages found: 2', $output);
    }

    public function testSimpleWithSettedLanguages(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '-L' => 'ru,en']);
        $output = $this->command->getDisplay();
        $this->assertStringContainsString('Languages: ru, en', $output);
    }

    private function configContainer(array $definitions): void
    {
        $config = ContainerConfig::create()
            ->withDefinitions($definitions);
        $this->container = new Container($config);
        $this->application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
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
                    ]
                ]
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
                    ]
                ]
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
