<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
</p>
<h1 align="center">Yii Message Extractor</h1>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/translator-extractor/v/stable.png)](https://packagist.org/packages/yiisoft/translator-extractor)
[![Total Downloads](https://poser.pugx.org/yiisoft/translator-extractor/downloads.png)](https://packagist.org/packages/yiisoft/translator-extractor)
[![Build status](https://github.com/yiisoft/translator-extractor/workflows/build/badge.svg)](https://github.com/yiisoft/translator-extractor/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/translator-extractor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/translator-extractor/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/translator-extractor/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/translator-extractor/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Ftranslator-extractor%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/translator-extractor/master)
[![static analysis](https://github.com/yiisoft/translator-extractor/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/translator-extractor/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/translator-extractor/coverage.svg)](https://shepherd.dev/github/yiisoft/translator-extractor)

The package allows automatically extracting translation IDs from PHP source files and writing them to
[one of the translator message sources](https://github.com/yiisoft/translator#message-sources).

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/translator-extractor
```

## Configuration

You need configure `MessageReader` and `MessageWriter` in config file of the package:

`config/packages/yiisoft/translator-extractor/console.php`

For example, when using PHP `MessageSource` the config will be the following using **relative path**:

```php
use \Yiisoft\Translator\Message\Php\MessageSource;

return [
    Extractor::class => [
        '__construct()' => [
            [
                DynamicReference::to([
                    'class' => ExtractorCategorySource::class,
                    '__construct()' => [
                        'app',
                        'messageReader' => DynamicReference::to(static fn () => new MessageSource($params['yiisoft/translator-extractor']['messagePath'])),
                        'messageWriter' => DynamicReference::to(static fn () => new MessageSource($params['yiisoft/translator-extractor']['messagePath'])),
                    ],
                ]),
            ],
        ],
    ],
];
```

And in `params.php` file you can configure parameters of a message source:

```php
return [
    'yiisoft/yii-console' => [
        'commands' => [
            'translator/extract' => ExtractCommand::class,
        ],
    ],
    'yiisoft/translator-extractor' => [
        // Using relative path:
        'messagePath' => dirname(__DIR__, 5) . '/messages',
    ],
];
```

Or if with using  PHP `MessageSource` the config will be the following **using Aliases**:

```php
use \Yiisoft\Translator\Message\Php\MessageSource;

return [
    Extractor::class => [
        '__construct()' => [
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
```


> **Attention**: Both `MessageReader` and `MessageWriter` should be configured for using _the same_ `MessageSource`. The extractor needs it to work with existing messages.

## General usage

```shell
./yii translator/extract
```

This command will recursively find all messages in the code starting with the current directory and will save it into
a message source for default language `en`. You can specify the path explicitly:

```shell
./yii translator/extract /path/to/your/project
```

**Notice:** By default extractor has `vendor` directory in the application directory excluded. To include it you can specify empty value for `except`:
```shell
./yii translator/extract /path/to/your/project --except=''
```

Full list of options:

```shell
Usage:
  translator/extract [options] [--] [<path>]

Arguments:
  path                       Path for extracting message IDs.

Options:
  -L, --languages=LANGUAGES  Comma separated list of languages to write message sources for. By default it is `en`. [default: "en"]
  -C, --category=CATEGORY    Default message category to use when category is not set. [default: "app"]
  -E, --except[=EXCEPT]      Exclude path from extracting. (multiple values allowed)
  -O, --only[=ONLY]          Use the only specified path for extracting. (multiple values allowed)

```


### Specify languages

You can specify multiple languages to write IDs into:

```shell
./yii translator/extract --languages=en,ru
```

Or in short format:

```shell
./yii translator/extract -Lru
```


### Specify default category

Also, you can specify default message category to use when category is not set.

```shell
./yii translator/extract --category=your_category_name
```


### Using `except` option

To exclude all directories named `dir1` use `--except`:

```shell
./yii translator/extract --except=**/dir1/**
```

To exclude both `vendor` and `tests` directories the following options could be used:

```shell
./yii translator/extract --except=./vendor/** --except=./tests/**
```

### Using `only` option

To parse only `test.php` files in any directory use `--only` option:

```shell
./yii translator/extract --only=**/test.php
```

To parse only `/var/www/html/test.php` file use:

```shell
./yii translator/extract --only=/var/www/html/test.php
```

For more info about `except` and `only` parameters check documentation of
[yiisoft/files package](https://github.com/yiisoft/files).

## Working with gettext

The package currently does not support extracting messages into gettext format. To extract messages for gettext,
you may use the following shell script (in Linux-based OS):

```shell
find src/ -name *.php | xargs xgettext --from-code=utf-8 --language=PHP --no-location --omit-header --sort-output --keyword=translate --output="locales/category.pot"

for d in locales/*/ ; do
    for i in locales/*.pot; do
        if [ ! -f "$d$(basename "$i" .pot).po" ]; then
            touch "$d$(basename "$i" .pot).po"
        fi

        msgmerge --update --silent --backup=off "$d$(basename "$i" .pot).po" $i
    done
done
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii translator extractor is free software. It is released under the terms of the BSD License. Please
see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
