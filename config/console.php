<?php

declare(strict_types=1);

use Yiisoft\Translator\Extractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
// Please - config next file, for using extractor
            'messageReader' => function() { throw new \RuntimeException('You can configure MessageReader');},
            'messageWriter' => function() { throw new \RuntimeException('You can configure MessageWriter');},
// For example: with usages PHP message Source
//            'messageReader' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(getcwd() . '/messages'),
//            'messageWriter' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(getcwd() . '/messages'),
        ],
    ],
];
