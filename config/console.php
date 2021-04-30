<?php

declare(strict_types=1);

use Yiisoft\Translator\Extractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            // Please set the following to use extractor. MessageReader and MessageWriter should be set to SAME MessageSource (One folder in PHP MessageSource)
            'messageReader' => static function () {
                throw new \RuntimeException('You should configure MessageReader.');
            },
            'messageWriter' => static function () {
                throw new \RuntimeException('You should configure MessageWriter.');
            },
            // For example we use PHP MessageSource
            // 'messageReader' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource($params['yiisoft/translator']['categorySources']),
            // 'messageWriter' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource($params['yiisoft/translator']['categorySources']),
        ],
    ],
];
