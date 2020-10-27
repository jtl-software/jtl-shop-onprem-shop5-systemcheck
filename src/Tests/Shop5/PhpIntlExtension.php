<?php declare(strict_types=1);
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpIntlExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpIntlExtension extends PhpModuleTest
{
    protected $name          = 'Intl';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-intl</code> für die Internationalisierung.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        if (\extension_loaded('intl')) {
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return true;
            }
        }

        return false;
    }
}
