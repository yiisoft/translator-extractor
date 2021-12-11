<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\TranslatorExtractor\CategorySource as ExtractorCategorySource;
use Yiisoft\TranslatorExtractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            [
                new ExtractorCategorySource(
                    'app',
                    // Please set the following to use extractor.
                    // MessageReader and MessageWriter should be set to the SAME MessageSource.
                    DynamicReference::to(static function () {
                        throw new \RuntimeException('You should configure MessageReader.');
                    }),
                    DynamicReference::to(static function () {
                        throw new \RuntimeException('You should configure MessageWriter.');
                    }),
                ),
            ],
        ],
    ],
];
