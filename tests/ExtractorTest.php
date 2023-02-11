<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\TranslatorExtractor\CategorySource;
use Yiisoft\TranslatorExtractor\Exception\NoCategorySourceConfigException;
use Yiisoft\TranslatorExtractor\Extractor;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;

final class ExtractorTest extends TestCase
{
    private Extractor $extractor;
    private ConsoleOutputInterface $output;

    /** @var CategorySource[] */
    private array $categorySource;

    private array $correctMessagesApp = [
        'test' => ['message' => 'test'],
        'test2' => ['message' => 'test2'],
    ];

    private array $correctMessagesApp2 = [
        'test_app2' => ['message' => 'test_app2'],
        'test2_app2' => ['message' => 'test2_app2'],
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

    public function testEmpty(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/empty', $categoryName, [$language], $this->output);

        $this->assertEquals([], $this->categorySource[$categoryName]->readMessages($categoryName, $language));
    }

    public function testEmptyCategorySource(): void
    {
        $this->expectException(NoCategorySourceConfigException::class);

        $this->extractor = new Extractor([]);
    }

    public function testEmptyCategorySourceException(): void
    {
        try {
            $this->extractor = new Extractor([]);
        } catch (NoCategorySourceConfigException $e) {
            $this->assertEquals('Please provide a list of CategorySource', $e->getName());
            $this->assertStringContainsString(
                '`CategorySource` to be used should be specified in your console container definitions file:',
                $e->getSolution()
            );
        }
    }

    public function testSimple(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);

        $this->assertEquals(
            $this->correctMessagesApp,
            $this->categorySource[$categoryName]->readMessages($categoryName, $language)
        );
    }

    public function testExcept(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->setExcept(['**/**.php']);
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);

        $this->assertEquals([], $this->categorySource[$categoryName]->readMessages($categoryName, $language));
    }

    public function testOnly(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName);

        $this->extractor->setOnly(['**/1.php']);
        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);

        $this->assertEquals([], $this->categorySource[$categoryName]->readMessages($categoryName, $language));
    }

    public function testSimpleWithTwoLanguages(): void
    {
        $categoryName = 'app';
        $language1 = 'de';
        $language2 = 'ru';
        $this->initCategory($categoryName);

        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language1, $language2], $this->output);

        $this->assertEquals(
            $this->correctMessagesApp,
            $this->categorySource[$categoryName]->readMessages($categoryName, $language1)
        );
        $this->assertEquals(
            $this->correctMessagesApp,
            $this->categorySource[$categoryName]->readMessages($categoryName, $language2)
        );
    }

    public function testSimpleWithTwoCategories(): void
    {
        $categoryName1 = 'app';
        $categoryName2 = 'app2';
        $language = 'en';

        $this->initCategory($categoryName1);
        $this->initCategory($categoryName2);

        $this->extractor->process(__DIR__ . '/multi-categories', $categoryName1, [$language], $this->output);

        $this->assertEquals(
            $this->correctMessagesApp,
            $this->categorySource[$categoryName1]->readMessages($categoryName1, $language)
        );
        $this->assertEquals(
            $this->correctMessagesApp2,
            $this->categorySource[$categoryName2]->readMessages($categoryName2, $language)
        );
    }

    public function testSimpleWithExceptSecondCategory(): void
    {
        $categoryName1 = 'app';
        $categoryName2 = 'app2';
        $language = 'en';

        $this->initCategory($categoryName1);
        $this->initCategory($categoryName2);

        $this->extractor->setExcept(['./app2/**']);
        $this->extractor->process(__DIR__ . '/multi-categories', $categoryName1, [$language], $this->output);

        $this->assertEquals(
            $this->correctMessagesApp,
            $this->categorySource[$categoryName1]->readMessages($categoryName1, $language)
        );
        $this->assertEquals(
            [],
            $this->categorySource[$categoryName2]->readMessages($categoryName2, $language)
        );
    }

    public function testSimpleWithOnlySecondCategory(): void
    {
        $categoryName1 = 'app';
        $categoryName2 = 'app2';
        $language = 'en';

        $this->initCategory($categoryName1);
        $this->initCategory($categoryName2);

        $this->extractor->setOnly(['./app2/**']);
        $this->extractor->process(__DIR__ . '/multi-categories', $categoryName1, [$language], $this->output);

        $this->assertEquals(
            [],
            $this->categorySource[$categoryName1]->readMessages($categoryName1, $language)
        );
        $this->assertEquals(
            $this->correctMessagesApp2,
            $this->categorySource[$categoryName2]->readMessages($categoryName2, $language)
        );
    }

    public function testNotRewriteExistingTranslations(): void
    {
        $categoryName = 'app';
        $language = 'en';
        $this->initCategory($categoryName, [$categoryName => [$language => $this->changedMessages]]);

        $this->extractor->process(__DIR__ . '/not-empty', $categoryName, [$language], $this->output);

        $this->assertEquals(
            $this->changedMessages,
            $this->categorySource[$categoryName]->readMessages($categoryName, $language)
        );
    }

    private function initCategory(string $category, array $messages = []): void
    {
        $this->categorySource[$category] = $this->getCategorySource($category, $messages);
        $this->extractor = new Extractor($this->categorySource);
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
}
