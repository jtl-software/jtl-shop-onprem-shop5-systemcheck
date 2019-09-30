<?php declare(strict_types=1);
/**
 * @package jtl\Systemcheck\Shop5
 * @author Clemens Rudolph <clemens.rudolph@jtl-software.com>
 * @copyright 2016 JTL-Software-GmbH
 */

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpPdoMysqlSupport
 * @package Systemcheck\Tests\Shop5
 */
class PhpPdoMysqlSupport extends PhpModuleTest
{
    protected $name          = 'PDO::MySQL - Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'Für JTL-Shop wird die Unterstützung für PHP-Data-Objects ' .
    '(<code>php-pdo</code> und <code>php-mysql</code>) benötigt.';
    protected $isOptional    = false;
    protected $isRecommended = false;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('pdo') && \extension_loaded('pdo_mysql');
    }
}

