<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor;

use RuntimeException;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;

/**
 * Represents message category for read and write messages
 */
final class CategorySource
{
    private string $name;
    private MessageReaderInterface $reader;
    private MessageWriterInterface $writer;

    /**
     * @param string $name Category name.
     * @param MessageReaderInterface $reader
     * @param MessageWriterInterface $writer
     */
    public function __construct(string $name, MessageReaderInterface $reader, MessageWriterInterface $writer)
    {
        if (!preg_match('/^[a-z0-9_-]+$/i', $name)) {
            throw new RuntimeException('Category name is invalid. Only letters, numbers, dash, and underscore are allowed.');
        }
        $this->name = $name;
        $this->reader = $reader;
        $this->writer = $writer;
    }

    /**
     * @return string Category name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $category Category of messages to get.
     * @param string $locale Locale of messages to get.
     *
     * @psalm-return array<string, array<string, string>>
     *
     * @return array All messages from category. The format is the following:
     *
     * @see \Yiisoft\Translator\MessageReaderInterface::getMessages()
     */
    public function readMessages(string $category, string $locale): array
    {
        return $this->reader->getMessages($category, $locale);
    }

    /**
     * @param string $category
     * @param string $locale
     * @param array $messages
     *
     * @psalm-param array<string, array<string, string>> $messages
     *
     * @see \Yiisoft\Translator\MessageWriterInterface::write
     */
    public function writeMessages(string $category, string $locale, array $messages): void
    {
        $this->writer->write($category, $locale, $messages);
    }
}
