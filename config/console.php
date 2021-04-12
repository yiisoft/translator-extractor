<?php

declare(strict_types=1);

use Yiisoft\Translator\Extractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            // Please set the following to use extractor
            'messageReader' => function () {
                throw new \RuntimeException('You can configure MessageReader');
            },
            'messageWriter' => function () {
                throw new \RuntimeException('You can configure MessageWriter');
            },
            // For example we use PHP message Source
            // 'messageReader' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(getcwd() . '/messages'),
            // 'messageWriter' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(getcwd() . '/messages'),
        ],
    ],
];
