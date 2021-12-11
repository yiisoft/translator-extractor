<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\TranslatorExtractor\CategorySource;
use Yiisoft\TranslatorExtractor\Extractor;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;

final class ExtractorTest extends TestCase
{
    private Extractor $extractor;
    private ConsoleOutputInterface $output;

    /** @var CategorySource[] */
    private $categorySource;

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
        $this->output = new ConsoleOutput();
        $this->output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    private function initCategory(string $category, array $messages = []): void
    {
        $this->categorySource[$category] = $this->getCategorySource($category, $messages);
        $this->extractor = new Extractor($this->categorySource);
    }

    public function testEmpty(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/empty', $categoryName, [$language], $this->output);
        $this->assertEquals([], $this->categorySource[$categoryName]->getReader()->getMessages($categoryName, $language));
    }

    public function testSimple(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals(
            $this->correctMessages,
            $this->categorySource[$categoryName]->getReader()->getMessages($categoryName, $language)
        );
    }

    public function testExcept(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->setExcept(['**/**.php']);
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals([], $this->categorySource[$categoryName]->getReader()->getMessages($categoryName, $language));
    }

    public function testOnly(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->setOnly(['**/1.php']);
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals([], $this->categorySource[$categoryName]->getReader()->getMessages($categoryName, $language));
    }

    public function testSimpleWithTwoLanguages(): void
    {
        $categoryName = 'app';
        $language1 = 'de';
        $language2 = 'ru';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language1, $language2], $this->output);
        $this->assertEquals(
            $this->correctMessages,
            $this->categorySource[$categoryName]->getReader()->getMessages($categoryName, $language1)
        );
        $this->assertEquals(
            $this->correctMessages,
            $this->categorySource[$categoryName]->getReader()->getMessages($categoryName, $language2)
        );
    }

    public function testRewrite(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName, [$categoryName => [$language => $this->changedMessages]]);

        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);
        $this->assertEquals(
            'test_changed',
            $this->categorySource[$categoryName]->getReader()->getMessage('test', $categoryName, $language)
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
            public array $messages;

            public function __construct(array $messages = [])
            {
                $this->messages = $messages;
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
}
