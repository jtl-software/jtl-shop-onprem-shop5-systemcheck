<?php

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

class PhpFileinfoExtension extends PhpModuleTest
{

    protected $name          = 'fileinfo';
    protected $requiredState = 'enabled';
    protected $description   = '';
    protected $isOptional    = false;
    protected $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('fileinfo');
    }
}