<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\TranslatorExtractor\CategorySource;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;

final class CategoryTest extends TestCase
{
    public function testName(): void
    {
        $this->assertInstanceOf(CategorySource::class, new CategorySource(
            'testcategoryname',
            $this->createMessageReader(),
            $this->createMessageWriter()
        ));
    }

    public function testNameException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Category name is invalid. Only letters and numbers are allowed.');
        new CategorySource(
            'test Category name',
            $this->createMessageReader(),
            $this->createMessageWriter()
        );
    }

    private function createMessageReader(): MessageReaderInterface
    {
        return new class () implements MessageReaderInterface {
            public function getMessage(string $id, string $category, string $locale, array $parameters = []): ?string
            {
                return null;
            }

            public function getMessages(string $category, string $locale): array
            {
                return [];
            }
        };
    }

    private function createMessageWriter(): MessageWriterInterface
    {
        return new class () implements MessageWriterInterface {
            public array $messages;

            public function write(string $category, string $locale, array $messages): void
            {
                $this->messages[$category][$locale] = $messages;
            }
        };
    }
}
