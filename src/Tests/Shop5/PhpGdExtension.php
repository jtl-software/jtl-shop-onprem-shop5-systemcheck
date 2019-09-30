<?php declare(strict_types=1);
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpGdExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpGdExtension extends PhpModuleTest
{
    protected $name            = 'GD-Unterstützung';
    protected $requiredState   = 'enabled';
    protected $description     = '';
    protected $isOptional      = false;
    protected $isRecommended   = false;
    protected $isReplaceableBy = PhpImagickExtension::class;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('gd');
    }
}
