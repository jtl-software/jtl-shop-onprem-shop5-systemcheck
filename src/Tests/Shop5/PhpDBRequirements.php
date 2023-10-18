<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Platform\MySQLVersion;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\ProgramTest;

/**
 * Class PhpDBRequirements
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBRequirements extends ProgramTest
{
    protected $name          = 'Datenbank-Voraussetzungen';
    protected $requiredState = 'InnoDB, UTF-8';
    protected $description   = 'Für JTL-Shop ist Datenbankunterstützung für InnoDB und UTF-8 erforderlich.';
    protected $isRecommended = true;

    /**
     * @return bool
     */
    private function handleNotSupported(): bool
    {
        $this->isOptional    = true;
        $this->isRecommended = false;
        $this->currentState  = 'nicht unterstützt';

        return false;
    }

    /**
     * @return bool
     */
    private function handleDBAvailable(): bool
    {
        $pdoCon = PDOConnection::getInstance();
        $pdoDB  = $pdoCon->getConnection();

        if ($pdoDB === null) {
            return $this->handleNotSupported();
        }

        $version       = new MySQLVersion($pdoDB);
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

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        $dbTest = new PhpDBConnection();
        if ($dbTest->execute()) {
            return $this->handleDBAvailable();
        }

        return $this->handleNotSupported();
    }
}
