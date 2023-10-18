<?php declare(strict_types=1);

namespace Systemcheck\Platform;

use Exception;
use PDO;

/**
 * Class MySQLVersion
 * @package JTL\DB
 */
final class MySQLVersion
{
    public const SERVER_MYSQL   = 'MySQL';
    public const SERVER_MARIADB = 'MariaDB';
    public const SERVER_UNKNOWN = 'unbekannt';

    public const NOT_SUPPORTED = 0;
    public const MIN_SUPPORTED = 1;
    public const SUPPORTED     = 2;

    private const MIN_VERSION             = [
        self::SERVER_MYSQL   => '5.7.0',
        self::SERVER_MARIADB => '10.1.0'
    ];
    private const MIN_RECOMMENDED_VERSION = [
        self::SERVER_MYSQL   => '8.0.0',
        self::SERVER_MARIADB => '10.6.0'
    ];
    private const MAX_VERSION             = [
        self::SERVER_MYSQL   => null,
        self::SERVER_MARIADB => '10.12.0'
    ];

    /** @var PDO */
    private PDO $pdoDb;

    /** @var string|null */
    private ?string $serverInfo = null;

    /** @var string|null */
    private ?string $serverVersion = null;

    /** @var string|null */
    private ?string $server = null;

    /** @var bool|null */
    private ?bool $innoDBSupport = null;

    /** @var bool|null */
    private ?bool $utf8Support = null;

    /**
     * MySQLVersion constructor
     * @param PDO $pdoDb
     */
    public function __construct(PDO $pdoDb)
    {
        $this->pdoDb = $pdoDb;
    }

    /**
     * @return string
     */
    private function getServerInfo(): string
    {
        return $this->serverInfo ?? ($this->serverInfo = $this->pdoDb->getAttribute(PDO::ATTR_SERVER_VERSION));
    }

    /**
     * @param string $info
     * @return string
     */
    private function splitServerInfo(string $info = 'server'): string
    {
        // 8.0.34-0ubuntu0.20.04.1 / 10.6.14-MariaDB-1:10.6.14+maria~ubu1804
        $serverInfo = $this->getServerInfo();
        if (preg_match('/([\d.]+)-([\da-zA-Z]+)/', $serverInfo, $hits)) {
            $this->server        = stripos($hits[2], 'maria') !== false ? self::SERVER_MARIADB : self::SERVER_MYSQL;
            $this->serverVersion = $hits[1];
        } else {
            $this->server        = self::SERVER_UNKNOWN;
            $this->serverVersion = '';
        }

        return $info === 'server' ? $this->server : $this->serverVersion;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->serverVersion ?? $this->splitServerInfo('version');
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server ?? $this->splitServerInfo();
    }

    /**
     * @param bool $strict
     * @return int
     */
    public function isShopSupported(bool $strict = true): int
    {
        if ($strict === false) {
            return \in_array($this->getServer(), [self::SERVER_MYSQL, self::SERVER_MARIADB])
                ? self::SUPPORTED
                : self::NOT_SUPPORTED;
        }

        $version = $this->getVersion();
        if (\version_compare($version, $this->getMinSupportedVersion(), '>=')
            && \version_compare($version, $this->getRecommendedVersion(), '<')
        ) {
            return self::MIN_SUPPORTED;
        }
        if (\version_compare($version, $this->getRecommendedVersion(), '>=')
            && ($this->getMaxSupportedVersion() === null
                || \version_compare($version, $this->getMaxSupportedVersion(), '<'))
        ) {
            return self::SUPPORTED;
        }

        return self::NOT_SUPPORTED;
    }

    /**
     * @return bool
     */
    public function hasInnoDBSupport(): bool
    {
        if ($this->innoDBSupport !== null) {
            return $this->innoDBSupport;
        }

        try {
            $res = $this->pdoDb->prepare(
                /** @lang text */
                "SELECT `SUPPORT`
                    FROM information_schema.ENGINES
                    WHERE `ENGINE` = 'InnoDB'"
            );
            if ($res->execute() === false) {
                return false;
            }
            $this->innoDBSupport = \in_array($res->fetchObject()->SUPPORT ?? 'NONE', ['YES', 'DEFAULT'], true);
        } catch (Exception) {
            return false;
        }

        return $this->innoDBSupport;
    }

    /**
     * @return bool
     */
    public function hasUTF8Support(): bool
    {
        if ($this->utf8Support !== null) {
            return $this->utf8Support;
        }

        try {
            $res = $this->pdoDb->prepare(
                /** @lang text */
                "SELECT `IS_COMPILED`
                    FROM information_schema.COLLATIONS
                    WHERE `COLLATION_NAME` RLIKE 'utf8(mb4)?_unicode_ci'"
            );
            if ($res->execute() === false) {
                return false;
            }
            $this->utf8Support = \strcasecmp($res->fetchObject()->IS_COMPILED ?? 'no', 'yes') === 0;
        } catch (Exception) {
            return false;
        }

        return $this->utf8Support;
    }

    /**
     * @param string|null $server
     * @return string
     */
    public function getMinSupportedVersion(?string $server = null): string
    {
        return self::MIN_VERSION[$server ?? $this->getServer()] ?? '';
    }

    /**
     * @param string|null $server
     * @return string
     */
    public function getRecommendedVersion(?string $server = null): string
    {
        return self::MIN_RECOMMENDED_VERSION[$server ?? $this->getServer()] ?? '';
    }

    /**
     * @param string|null $server
     * @return string|null
     */
    public function getMaxSupportedVersion(?string $server = null): ?string
    {
        return self::MAX_VERSION[$server ?? $this->getServer()] ?? null;
    }
}
