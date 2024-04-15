<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpZipArchive
 * @package Systemcheck\Tests\Shop5
 */
class PhpZipArchive extends PhpModuleTest
{
    protected string $name          = 'ziparchive';
    protected $requiredState = 'enabled';
    protected $description   = 'Zum Erstellen von diversen Exporten wird die PHP-Klasse "ZipArchive" benötigt.';
    protected bool $isOptional    = false;
    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \class_exists('ZipArchive');
    }
}
