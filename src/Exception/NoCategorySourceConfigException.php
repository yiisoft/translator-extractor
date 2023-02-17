<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Exception;

use RuntimeException;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class NoCategorySourceConfigException extends RuntimeException implements FriendlyExceptionInterface
{
    public function getName(): string
    {
        return 'Please provide a list of CategorySource';
    }

    public function getSolution(): ?string
    {
        return <<<'SOLUTION'
`CategorySource` to be used should be specified in your console container definitions file:

```php
use Yiisoft\Definitions\DynamicReferencesArray;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\TranslatorExtractor\Extractor;

return [
    Extractor::class => [
        '__construct()' => [
            DynamicReferencesArray::from([
                static fn (Aliases $aliases) => new MessageSource($aliases->get('@message')),
            ])
        ],
    ],
];
```
SOLUTION;
    }
}
