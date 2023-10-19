<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Platform\DBServerInfo;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\ProgramTest;

/**
 * Class PhpDBVersion
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBVersion extends ProgramTest
{
    protected $name          = 'Datenbank-Version';
    protected $requiredState = '';
    protected $description   = '';
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

    /**
     * @inheritdoc
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
