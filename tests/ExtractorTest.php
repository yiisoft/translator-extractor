<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\TranslatorExtractor\Extractor;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;

final class ExtractorTest extends TestCase
{
    private Extractor $extractor;
    private ConsoleOutputInterface $output;

    /** @var MessageReaderInterface|MessageWriterInterface */
    private $messageSource;

    private array $correctMessages = [
        'test' => ['message' => 'test'],
        'test2' => ['message' => 'test2'],
    ];

    private array $changedMessages = [
        'test' => ['message' => 'test_changed'],
        'test2' => ['message' => 'test2'],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageSource = $this->getMessageSource();
        $this->extractor = new Extractor($this->messageSource, $this->messageSource);
        $this->output = new ConsoleOutput();
        $this->output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    public function testEmpty(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->extractor->process(__DIR__ . '/empty', $categoryName, [$language], $this->output);
        $this->assertEquals([], $this->messageSource->messages);
    }

    public function testSimple(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals([$categoryName => [$language => $this->correctMessages]], $this->messageSource->messages);
    }

    public function testExcept(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->extractor->setExcept(['**/**.php']);
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals([], $this->messageSource->messages);
    }

    public function testOnly(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->extractor->setOnly(['**/1.php']);
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals([], $this->messageSource->messages);
    }

    public function testSimpleWithTwoLanguages(): void
    {
        $categoryName = 'app';
        $language1 = 'de';
        $language2 = 'ru';
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language1, $language2], $this->output);
        $this->assertEquals([
            $categoryName =>
                [
                    $language1 => $this->correctMessages,
                    $language2 => $this->correctMessages,
                ],
        ], $this->messageSource->messages);
    }

    public function testRewrite(): void
    {
        $categoryName = 'app';
        $language = 'en';

        $this->messageSource->messages = [$categoryName => [$language => $this->changedMessages]];
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals('test_changed', $this->messageSource->getMessage('test', $categoryName, $language));
    }

    private function getMessageSource()
    {
        return new class () implements MessageReaderInterface, MessageWriterInterface {
            public array $messages = [];

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
