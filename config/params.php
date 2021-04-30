<?php

declare(strict_types=1);

use Yiisoft\Translator\Extractor\Command\ExtractCommand;

return [
    'yiisoft/yii-console' => [
        'commands' => [
            'translator/extract' => ExtractCommand::class,
        ],
    ],
    /*
     * Examples of params for config PHP MessageSource. Using only one option - with relative path or `Alias`
    'yiisoft/translator-extractor' => [
        'messagePath' => dirname(__DIR__, 5) . '/messages', // example with relative path
        'messagePath' => fn (Aliases $aliases) => $aliases->get('@message'), // example with usage alias of application
    ],
    */
];
