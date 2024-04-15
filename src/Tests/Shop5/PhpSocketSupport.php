<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpSocketSupport
 * @package Systemcheck\Tests\Shop5
 */
class PhpSocketSupport extends PhpModuleTest
{
    protected string $name          = 'Sockets';
    protected $requiredState = 'enabled';
    protected $description   = '';
    protected bool $isOptional    = true;
    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \function_exists('fsockopen');
    }
}
