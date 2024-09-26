<?php

declare(strict_types=1);

namespace Systemcheck\Platform;

use PDO;
use PDOException;
use stdClass;

/**
 * Class PDOConnection
 * @package Systemcheck\Platform
 */
final class PDOConnection
{
    private ?PDO $dbPDO = null;

    private static ?self $instance = null;

    private function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        return self::$instance ?? new self();
    }

    /**
     * @return stdClass&object{dbHost: string, dbSocket: string, dbUser: string, dbPwd: string}
     */
    public static function createAuth(string $host, string $socket, string $user, string $pwd): stdClass
    {
        return (object)[
            'dbHost'   => $host,
            'dbSocket' => $socket,
            'dbUser'   => $user,
            'dbPwd'    => $pwd,
        ];
    }

    private function createConnection(stdClass $auth): ?PDO
    {
        $dsn = 'mysql:';
        if ($auth->dbSocket !== '') {
            $dsn .= 'unix_socket=' . $auth->dbSocket;
        } else {
            $dsn .= 'host=' . $auth->dbHost;
        }

        try {
            $this->dbPDO = new PDO($dsn, $auth->dbUser, $auth->dbPwd);
        } catch (PDOException) {
            return null;
        }

        return $this->dbPDO;
    }

    public function getConnection(?stdClass $auth = null): ?PDO
    {
        return $this->dbPDO ?? ($auth === null ? null : $this->createConnection($auth));
    }

    public function setConnection(PDO $dbPDO): self
    {
        $this->dbPDO = $dbPDO;

        return $this;
    }
}
