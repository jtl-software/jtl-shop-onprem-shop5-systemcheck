<?php
declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use PDO;
use Systemcheck\Platform\DBServerInfo;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\ProgramTest;

/**
 * Class PhpDBConnection
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBConnection extends ProgramTest
{
    protected $name          = 'Datenbank-Unterstützung';
    protected $requiredState = 'MySQL, MariaDB';
    protected $description   = 'Für JTL-Shop wird eine MySQL oder MariaDB Datenbank benötigt.';
    protected $isRecommended = true;

    /** @var bool */
    private bool $requireAuth = false;

    /**
     * @param PDO|null $pdoDB
     * @return bool
     */
    private function handleAuthenticated(?PDO $pdoDB): bool
    {
        $this->requireAuth   = false;
        $this->isOptional    = false;

        if ($pdoDB === null) {
            $this->currentState = 'Anmeldung fehlgeschlagen';

            return false;
        }

        $version            = new DBServerInfo($pdoDB);
        $this->currentState = $version->getServer();

        return $version->isSupportedServer();
    }

    /**
     * @return bool
     */
    private function handleNotAuthenticated(): bool
    {
        $this->requireAuth   = true;
        $this->isOptional    = true;
        $this->currentState  = 'Login erforderlich';

        return false;
    }

    /**
     * @return bool
     */
    private function handleNotSupported(): bool
    {
        $this->requireAuth   = false;
        $this->isOptional    = false;
        $this->currentState  = 'nicht unterstützt';

        return false;
    }

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $pdoTest = new PhpPdoMysqlSupport();
        if ($pdoTest->execute()) {
            $pdoCon = PDOConnection::getInstance();
            $pdoDB  = $pdoCon->getConnection();
            if ($pdoDB !== null) {
                return $this->handleAuthenticated($pdoDB);
            }
            if ((int)($_POST['dbAuth'] ?? 0) === 1) {
                $pdoDB = $pdoCon->getConnection(PDOConnection::createAuth(
                    $_POST['dbHost'] ?? '',
                    $_POST['dbSocket'] ?? '',
                    $_POST['dbUser'] ?? '',
                    $_POST['dbPassword'] ?? ''
                ));

                return $this->handleAuthenticated($pdoDB);
            }

            return $this->handleNotAuthenticated();
        }

        return $this->handleNotSupported();
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        $default = parent::getDescription();
        if ($this->requireAuth) {
            $default .= '<form method="post">'
                . '<input type="hidden" name="dbAuth" value="1" />'
                . '<table>'
                . '<tr>'
                . '<th><label for="dbHost">Datenbank Host</label>&nbsp;</th>'
                . '<td><input id="dbHost" type="text" name="dbHost" value="localhost" />&nbsp;</td>'
                . '<th><small>oder</small>&nbsp;<label for="dbSocket">Socket (optional)&nbsp;</label></th>'
                . '<td><input id="dbSocket" type="text" name="dbSocket" /></td>'
                . '</tr><tr>'
                . '<th><label for="dbUser">Benutzername</label>&nbsp;</th>'
                . '<td><input id="dbUser" type="text" name="dbUser" />&nbsp;</td>'
                . '<th><label for="dbPassword">Passwort</label>&nbsp;</th>'
                . '<td><input id="dbPassword" type="password" name="dbPassword" /></td>'
                . '</tr><tr>'
                . '<td><button type="submit">Testen</button></td>'
                . '</tr>'
                . '</table>'
                . '</form>';
        }

        return $default;
    }
}
