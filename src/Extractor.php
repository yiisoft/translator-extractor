<?php

declare(strict_types=1);

namespace Yiisoft\Translator\Extractor;

use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\MessageWriterInterface;

/**
 * Extracts translator IDs from files within a given path and writes them into message source given merging
 * results with what is already there.
 */
final class Extractor
{
    private MessageWriterInterface $messageWriter;
    private MessageReaderInterface $messageReader;

    /** @var string[]|null */
    private ?array $except = null;

    /** @var string[]|null */
    private ?array $only = null;

    public function __construct(MessageReaderInterface $messageReader, MessageWriterInterface $messageWriter)
    {
        $this->messageReader = $messageReader;
        $this->messageWriter = $messageWriter;
    }

    /**
     * Set list of patterns that the files or directories should not match.
     *
     * @see PathMatcher
     *
     * @param string[] $except
     */
    public function setExcept(array $except): void
    {
        if (!empty($except)) {
            $this->except = $except;
        }
    }

    /**
     * Set list of patterns that the files or directories should match.
     *
     * @see PathMatcher
     *
     * @param string[] $only
     */
    public function setOnly(array $only): void
    {
        if (!empty($only)) {
            $this->only = $only;
        }
    }

    /**
     * @param string $filesPath Path to files to extract from.
     * @param string $defaultCategory Category to use if category isn't set in translation call.
     * @param string[] $languages Languages to write extracted IDs to.
     * @param OutputInterface $output
     */
    public function process(string $filesPath, string $defaultCategory, array $languages, OutputInterface $output): void
    {
        $translationExtractor = new TranslationExtractor($filesPath, $this->only, $this->except);

        $messagesList = $translationExtractor->extract($defaultCategory);

        if (empty($messagesList)) {
            $output->writeln('<comment>Messages not found</comment>');
            return;
        }

        $output->writeln('Languages: ' . implode(', ', $languages));

        /**
         * @var string $categoryName
         * @var array<array-key, array<string, string>|mixed> $messages
         */
        foreach ($messagesList as $categoryName => $messages) {
            $output->writeln('<info>Category: "' . $categoryName . '", messages found: ' . count($messages) . '</info>');

            /** @var array<string, array<string, string>> $convertedMessages */
            $convertedMessages = $this->convert($messages);
            foreach ($languages as $language) {
                $readMessages = $this->messageReader->getMessages($categoryName, $language);
                $convertedMessages = array_merge($convertedMessages, $readMessages);
                $this->messageWriter->write($categoryName, $language, $convertedMessages);
            }
        }
    }

    private function convert(array $messages): array
    {
        $returningMessages = [];

        /** @var array<string, string> $messages */
        foreach ($messages as $message) {
            $returningMessages[$message] = ['message' => $message];
        }

        return $returningMessages;
    }
}
