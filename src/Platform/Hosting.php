<?php

declare(strict_types=1);

namespace Systemcheck\Platform;

/**
 * Class Hosting
 * @package Systemcheck\Platform
 */
class Hosting
{
    public const PROVIDER_1UND1 = '1und1';

    public const PROVIDER_STRATO = 'strato';

    public const PROVIDER_HOSTEUROPE = 'hosteurope';

    public const PROVIDER_ALFAHOSTING = 'alfahosting';

    public const PROVIDER_JTL = 'jtl';

    public const PROVIDER_HETZNER = 'hetzner';

    protected ?string $hostname = null;

    protected string $documentRoot;

    protected ?string $provider = null;

    public function __construct()
    {
        $this->documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '?';
        $this->detect();
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function getDocumentRoot(): ?string
    {
        return $this->documentRoot;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function getPhpVersion(): string
    {
        return \PHP_VERSION;
    }

    private function detect(): void
    {
        $host = \gethostbyaddr($_SERVER['SERVER_ADDR'] ?? '127.0.0.1');
        if ($host === false) {
            return;
        }
        $this->hostname = $host;
        if (\preg_match('/jtl-software\.de$/', $this->hostname)) {
            $this->provider = self::PROVIDER_JTL;
        } elseif (\preg_match('/hosteurope\.de$/', $this->hostname)) {
            $this->provider = self::PROVIDER_HOSTEUROPE;
        } elseif (\preg_match('/your-server\.de$/', $this->hostname)) {
            $this->provider = self::PROVIDER_HETZNER;
        } elseif (\preg_match('/kundenserver\.de$/', $this->hostname)) {
            $this->provider = self::PROVIDER_1UND1;
        } elseif (\preg_match('/stratoserver\.net$/', $this->hostname)) {
            $this->provider = self::PROVIDER_STRATO;
        } elseif (\preg_match('/alfahosting-server\.de$/', $this->hostname)) {
            $this->provider = self::PROVIDER_ALFAHOSTING;
        }
    }
}
