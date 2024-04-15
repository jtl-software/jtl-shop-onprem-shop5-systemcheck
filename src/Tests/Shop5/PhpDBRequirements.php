<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Platform\DBServerInfo;
use Systemcheck\Tests\DBConnectionTest;

/**
 * Class PhpDBRequirements
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBRequirements extends DBConnectionTest
{
    protected string $name          = 'Datenbank-Voraussetzungen';
    protected $requiredState = 'InnoDB, UTF-8';
    protected $description   = 'Für JTL-Shop ist Datenbankunterstützung für InnoDB und UTF-8 erforderlich.';

    /**
     * @inheritdoc
     */
    protected function handleDBAvailable(): bool
    {
        $pdoDB = $this->getPdoDB();
        if ($pdoDB === null) {
            return $this->handleNotSupported();
        }

        $version       = new DBServerInfo($pdoDB);
        $utf8Support   = $version->hasUTF8Support();
        $innoDBSupport = $version->hasInnoDBSupport();

        $this->isOptional = false;
        if ($innoDBSupport && $utf8Support) {
            $this->currentState = $this->requiredState;
        } elseif ($innoDBSupport) {
            $this->currentState = 'InnoDB';
        } elseif ($utf8Support) {
            $this->currentState = 'UTF-8';
        } else {
            $this->currentState = 'keine Unterstützung';
        }

        return $utf8Support && $innoDBSupport;
    }
}
