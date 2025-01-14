<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpImagickExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpImagickExtension extends PhpModuleTest
{
    protected string $name = 'ImageMagick';

    protected string $requiredState = 'enabled';

    protected string $description = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-imagick</code>'
    . ' für die dynamische Generierung von Bildern.';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('imagick');
    }
}
