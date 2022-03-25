<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\AbstractApacheConfigTest;

class ApacheModRewrite extends AbstractApacheConfigTest
{
    protected $name          = 'mod_rewrite';
    protected $requiredState = 'on';
    protected $description   = 'Wir benötigt um Weiterleitungen zu realisieren.';
    protected $isOptional    = true;
    protected $isRecommended = true;

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        $hasModRewrite      = \in_array('mod_rewrite', \apache_get_modules());
        $this->currentState = $hasModRewrite ? 'on' : 'off';

        return $hasModRewrite;
    }
}
