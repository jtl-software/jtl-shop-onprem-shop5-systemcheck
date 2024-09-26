<?php

declare(strict_types=1);

namespace Systemcheck\Tests;

use PDO;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\Shop5\PhpDBConnection;

/**
 * Class DBConnectionTest
 * @package Systemcheck\Tests
 */
abstract class DBConnectionTest extends ProgramTest
{
    private ?PDO $pdoDB = null;

    protected bool $isRecommended = true;

    protected function getPdoDB(): ?PDO
    {
        return $this->pdoDB ?? ($this->pdoDB = PDOConnection::getInstance()->getConnection());
    }

    protected function handleNotSupported(): bool
    {
        $this->isOptional    = true;
        $this->isRecommended = false;
        $this->currentState  = 'nicht unterstützt';

        return false;
    }

    abstract protected function handleDBAvailable(): bool;

    public function execute(): bool
    {
        $dbTest = new PhpDBConnection();
        if ($dbTest->execute()) {
            return $this->handleDBAvailable();
        }

        return $this->handleNotSupported();
    }
}
