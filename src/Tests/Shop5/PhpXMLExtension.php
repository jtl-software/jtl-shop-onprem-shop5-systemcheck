<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpXMLExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpXMLExtension extends PhpModuleTest
{
    protected string $name          = 'XML';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-xml</code>.';
    protected bool $isOptional    = false;
    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('xml');
    }
}
