<?php

declare(strict_types=1);

namespace Yiisoft\TranslatorExtractor\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\BuildingException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\TranslatorExtractor\Exception\NoCategorySourceConfigException;
use Yiisoft\TranslatorExtractor\Extractor;

final class ConfigTest extends TestCase
{
    public function testDiConsole(): void
    {
        $container = $this->createContainer('console');

        $this->expectException(BuildingException::class);
        $this->expectExceptionMessage(
            'Caught unhandled error "' . NoCategorySourceConfigException::class .
            '" while building "' . Extractor::class . '".'
        );
        $container->get(Extractor::class);
    }

    private function createContainer(?string $postfix = null): Container
    {
        return new Container(
            ContainerConfig::create()->withDefinitions(
                $this->getDiConfig($postfix)
            )
        );
    }

    private function getDiConfig(?string $postfix = null): array
    {
        $params = $this->getParams();
        return require dirname(__DIR__) . '/config/di' . ($postfix !== null ? '-' . $postfix : '') . '.php';
    }

    private function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params.php';
    }
}
