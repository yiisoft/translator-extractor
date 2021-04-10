<?php

declare(strict_types=1);

namespace Yiisoft\Translator\Extractor\Tests;

use Yiisoft\Translator\Extractor\Command\ExtractCommand;
use Yiisoft\Translator\Extractor\Extractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;
use Yiisoft\Yii\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

final class ExtractCommandTest extends TestCase
{
    private ContainerInterface $container;
    private Application $application;
    private $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configContainer();

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
        $this->assertStringContainsString('Category: "app", found messages: 2', $output);
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

        $this->assertStringContainsString('Category: "app2", found messages: 2', $output);
    }

    public function testSimpleWithSettedLanguages(): void
    {
        $this->command->execute(['path' => __DIR__ . '/not-empty', '-L' => 'ru,en']);
        $output = $this->command->getDisplay();

        $this->assertStringContainsString('Languages: ru, en', $output);
    }

    protected function configContainer(): void
    {
        $this->container = new Container($this->config());

        $this->application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            [
                'translator/extract' => ExtractCommand::class,
            ]
        );

        $this->application->setCommandLoader($loader);
    }

    private function config(): array
    {
        return [
            Extractor::class => [
                '__construct()' => [
                'messageReader' => $this->getMessageSource(),
                'messageWriter' => $this->getMessageSource(),
                ],
            ],
        ];
    }

    private function getMessageSource()
    {
        return new class() implements MessageReaderInterface, MessageWriterInterface {
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
