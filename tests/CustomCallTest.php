<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;
use Yiisoft\TranslatorExtractor\CategorySource;
use Yiisoft\TranslatorExtractor\Extractor;

final class CustomCallTest extends TestCase
{
    /** @var CategorySource[] */
    private array $categorySource;
    private Extractor $extractor;
    private ConsoleOutputInterface $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output = new ConsoleOutput();
        $this->output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    public function testCustomCall(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/custom-call', $categoryName, [$language], $this->output);

        $this->assertSame(
            [
                'custom-test' => ['message' => 'custom-test'],
                'custom-test2' => ['message' => 'custom-test2'],
            ],
            $this->categorySource[$categoryName]->readMessages($categoryName, $language)
        );
    }

    private function getCategorySource(string $category, array $messages = []): CategorySource
    {
        $rw = $this->getReaderWriter($messages);
        return new CategorySource($category, $rw, $rw);
    }

    private function getReaderWriter(array $messages = [])
    {
        return new class ($messages) implements MessageReaderInterface, MessageWriterInterface {
            public function __construct(public array $messages = [])
            {
            }

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

    private function initCategory(string $category, array $messages = []): void
    {
        $this->categorySource[$category] = $this->getCategorySource($category, $messages);
        $this->extractor = new Extractor($this->categorySource, '$app->t');
    }
}
