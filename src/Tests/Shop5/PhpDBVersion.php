<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Platform\DBServerInfo;
use Systemcheck\Tests\DBConnectionTest;

/**
 * Class PhpDBVersion
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBVersion extends DBConnectionTest
{
    protected $name          = 'Datenbank-Version';
    protected $requiredState = '';
    protected $description   = '';

    /**
     * @inheritdoc
     */
    protected function handleDBAvailable(): bool
    {
        $pdoDB = $this->getPdoDB();
        if ($pdoDB === null) {
            return $this->handleNotSupported();
        }

        $version             = new DBServerInfo($pdoDB);
        $this->isOptional    = false;
        $this->requiredState = '>= ' . $version->getMinSupportedVersion();
        $this->currentState  = $version->getVersion();
        if ($version->getMaxSupportedVersion() !== null) {
            $this->requiredState .= ' und < ' . $version->getMaxSupportedVersion();
        }

        switch ($version->isSupportedVersion()) {
            case DBServerInfo::MIN_SUPPORTED:
                $this->isOptional    = true;
                $this->description   = 'Für JTL-Shop wird ' . $version->getServer()
                    . ' ab Version ' . $version->getRecommendedVersion() . ' empfohlen.';

                return false;
            case DBServerInfo::SUPPORTED:
                return true;
        }

        return false;
    }
}
