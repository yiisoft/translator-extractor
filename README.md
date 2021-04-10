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

## General usage

```ssh
php yii translator/extract
```
This command founded all messages in current directory and save for default language `en`

```ssh
php yii translator/extract /path/to/your/project
```
With specifying path to project

### Specify languages for extract

Specify two languages
```ssh
php yii translator/extract --languages=en,ru
```
Or in short format
```ssh
php yii translator/extract -Lru
```

### Using `except` option

For exclude directory `vendor`
```ssh
php yii translator/extract --except=**/vendor/**
```

For exclude directories `vendor` && `tests`
```ssh
php yii translator/extract --except=**/vendor/** --except=**/tests/**
```

### Using `only` option

Parse only from files `test.php`
```ssh
php yii translator/extract --only=**/test.php
```

Parse only from concrete file `/var/www/html/test.php`
```ssh
php yii translator/extract --only=/var/www/html/test.php
```

## For Gettext

Package not supported extract to gettext format. For extract messages to gettext files, you may using this shell script (in linux-based OS)
```ssh
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
For more info - read discussion for [this PR](https://github.com/yiisoft/translator-message-gettext/pull/24)

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

The Yii translator extractor is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
