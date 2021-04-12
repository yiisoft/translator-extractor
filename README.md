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

The package ...

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/translator-extractor --prefer-dist
```

## Configuration

You need configure MessageReader and MessageWriter in config file of package:

`config/packages/yiisoft/translator-extractor/console.php`

For example: with usages PHP MessageSource
```php
return [
    Extractor::class => [
        '__construct()' => [
            'messageReader' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(getcwd() . '/messages'),
            'messageWriter' => fn () => new \Yiisoft\Translator\Message\Php\MessageSource(getcwd() . '/messages'),
        ],
    ],
];
```

## General usage

```shell
php yii translator/extract
```

This command will recursively find all messages in the code starting with current directory and will save it into
message source for default language `en`. You can specify path exclicitly:

```shell
php yii translator/extract /path/to/your/project
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

### Specify languages for extract

You can specify multiple languages

```shell
php yii translator/extract --languages=en,ru
```

Or in short format

```shell
php yii translator/extract -Lru
```

### Specify default category

Also you can specify default message category to use when category is not set.

```shell
php yii translator/extract --category=your_category_name
```


### Using `except` option

To exclude `vendor` directory use `--except`:

```shell
php yii translator/extract --except=**/vendor/**
```

To exclude both `vendor` and `tests` directories:

```shell
php yii translator/extract --except=**/vendor/** --except=**/tests/**
```

### Using `only` option

To parse only `test.php` files use `--only` option:

```shell
php yii translator/extract --only=**/test.php
```

To parse only `/var/www/html/test.php` file use:

```shell
php yii translator/extract --only=/var/www/html/test.php
```

For more info about `except` and `only` parameters - you may reading in [documentation of module yiisoft/files](https://github.com/yiisoft/files)

## For Gettext

The package does not support extracting messages into gettext format. To extract messages for gettext, you may use the
following shell script (in linux-based OS):

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
