<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Exception;

use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class IncorrectConfigException extends \RuntimeException implements FriendlyExceptionInterface
{
    public function getName(): string
    {
        return 'Need configure list of CategorySource';
    }

    public function getSolution(): ?string
    {
        return <<<'SOLUTION'
            Need configure list of CategorySource in your config file.
            For example:
            ApplicationExtractorCategorySource::class => [
                'class' => Yiisoft\TranslatorExtractor\CategorySource::class,
                '__construct()' => [
                    'name' => $params['yiisoft/translator']['defaultCategory'],
                    Reference::to(MessageReader::class),
                    Reference::to(MessageWriter::class),
                ],
            ],
            Extractor::class => [
                '__construct()' => [
                    [
                        Reference::to(ApplicationExtractorCategorySource::class),
                    ],
                ],
            ],
            SOLUTION;
    }
}
