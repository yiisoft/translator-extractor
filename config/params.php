<?php

declare(strict_types=1);

use Yiisoft\TranslatorExtractor\Command\ExtractCommand;

return [
    'yiisoft/yii-console' => [
        'commands' => [
            'translator/extract' => ExtractCommand::class,
        ],
    ],
];
