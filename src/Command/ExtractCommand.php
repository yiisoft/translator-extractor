<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Yiisoft\TranslatorExtractor\Extractor;

/**
 * Console command that allows extracting translator IDs from files.
 */
#[AsCommand('translator/extract', 'Extracts translator IDs from files')]
final class ExtractCommand extends Command
{
    private string $defaultCategory = 'app';

    public function __construct(private Extractor $extractor)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('languages', 'L', InputOption::VALUE_OPTIONAL, 'Comma separated list of languages to write message sources for. By default it is `en`.', 'en')
            ->addOption('category', 'C', InputOption::VALUE_OPTIONAL, 'Default message category to use when category is not set.', $this->defaultCategory)
            ->addOption('except', 'E', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Exclude path from extracting.', [])
            ->addOption('only', 'O', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Use the only specified path for extracting.', [])
            ->addArgument('path', InputArgument::OPTIONAL, 'Path for extracting message IDs.')
            ->setHelp('This command Extracts translator IDs from files within a given path.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string */
        $path = $input->getArgument('path') ?? getcwd();

        /** @var string */
        $languages = $input->getOption('languages');

        /** @var string */
        $category = $input->getOption('category');

        /** @var string[] */
        $except = $input->getOption('except');

        /** @var string[] */
        $only = $input->getOption('only');

        /** @var string[] */
        $languagesList = explode(',', $languages);

        $this->extractor->setExcept($except);
        $this->extractor->setOnly($only);

        $this->extractor->process($path, $category, $languagesList, $output);

        return 0;
    }
}
