<?php

declare(strict_types=1);

use Yiisoft\Factory\Definition\DynamicReference;
use Yiisoft\Translator\Extractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            // Please set the following to use extractor.
            // MessageReader and MessageWriter should be set to the SAME MessageSource.
            'messageReader' => DynamicReference::to(static function () {
                throw new \RuntimeException('You should configure MessageReader.');
            }),
            'messageWriter' => DynamicReference::to(static function () {
                throw new \RuntimeException('You should configure MessageWriter.');
            }),
        ],
    ],
];
