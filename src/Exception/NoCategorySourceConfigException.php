<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Exception;

use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class NoCategorySourceConfigException extends \RuntimeException implements FriendlyExceptionInterface
{
    public function getName(): string
    {
        return 'Please provide a list of CategorySource';
    }

    public function getSolution(): ?string
    {
        return <<<'SOLUTION'
CategorySource to be used should be specified in your console config file:

return [
    Extractor::class => [
        '__construct()' => [
            'messageReader' => DynamicReference::to(static fn (Aliases $aliases) => new MessageSource($aliases->get('@message'))),
            'messageWriter' => DynamicReference::to(static fn (Aliases $aliases) => new MessageSource($aliases->get('@message'))),
            [
                DynamicReference::to([
                    'class' => ExtractorCategorySource::class,
                    '__construct()' => [
                        'app',
                        'messageReader' => DynamicReference::to(static fn (Aliases $aliases) => new MessageSource($aliases->get('@message'))),
                        'messageWriter' => DynamicReference::to(static fn (Aliases $aliases) => new MessageSource($aliases->get('@message'))),
                    ],
                ]),
            ],
        ],
    ],
];
SOLUTION;
    }
}
