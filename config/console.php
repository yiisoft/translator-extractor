<?php

declare(strict_types=1);

use Yiisoft\Translator\Extractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            // Please set the following to use extractor
            'messageReader' => static function () {
                throw new \RuntimeException('You should configure MessageReader.');
            },
            'messageWriter' => static function () {
                throw new \RuntimeException('You should  configure MessageWriter.');
            },
            // For example we use PHP message Source
            // 'messageReader' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(dirname(__DIR__, 5) . '/messages'),
            // 'messageWriter' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(dirname(__DIR__, 5) . '/messages'),
        ],
    ],
];
