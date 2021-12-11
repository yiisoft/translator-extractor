<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor;

use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\Translator\Extractor\TranslationExtractor;

/**
 * Extracts translator IDs from files within a given path and writes them into message source given merging
 * results with what is already there.
 */
final class Extractor
{
    /** @var string[]|null */
    private ?array $except = null;

    /** @var string[]|null */
    private ?array $only = null;

    /**
     * @var CategorySource[] Array of category message sources indexed by category names.
     */
    private array $categorySources = [];

    /**
     * @param CategorySource[] $categories
     */
    public function __construct(array $categories)
    {
        foreach ($categories as $category) {
            $this->categorySources[$category->getName()] = $category;
        }
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
        if (!isset($this->categorySources[$defaultCategory])) {
            $output->writeln('<comment>Default category not found in list of Categories</comment>');
            return;
        }

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
                $extractCategory = isset($this->categorySources[$categoryName]) ? $categoryName : $defaultCategory;
                $this->addMessages($extractCategory, $language, $convertedMessages);
            }
        }
    }

    private function addMessages(string $categoryName, string $language, array $messages): void
    {
        $readMessages = $this->categorySources[$categoryName]->getReader()->getMessages($categoryName, $language);
        $convertedMessages = array_merge($messages, $readMessages);
        $this->categorySources[$categoryName]->getWriter()->write($categoryName, $language, $convertedMessages);
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
