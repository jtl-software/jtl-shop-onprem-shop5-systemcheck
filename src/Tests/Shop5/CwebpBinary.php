<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\ProgramTest;

/**
 * Class CwebpBinary
 * @package Systemcheck\Tests\Shop5
 */
class CwebpBinary extends ProgramTest
{
    protected string $name = 'cwebp (WebP Encoder)';

    protected string $requiredState = 'installiert';

    protected string $description = 'Der cwebp-Encoder wird für die Konvertierung von Bildern ins WebP-Format benötigt. '
        . 'WebP bietet bessere Kompression als JPEG/PNG und verbessert die Ladezeiten. '
        . 'Weitere Informationen: https://developers.google.com/speed/webp/docs/cwebp';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $disabledFunctions = \ini_get('disable_functions');
        $shellDisabled = $disabledFunctions !== false
            && \preg_match('/\b(exec|shell_exec|passthru|system)\b/', $disabledFunctions) === 1;

        if ($shellDisabled) {
            $this->currentState = 'Shell-Funktionen deaktiviert - manuelle Prüfung erforderlich';
            return false;
        }

        $path = \trim((string)@\shell_exec('command -v cwebp 2>/dev/null'));
        if ($path === '') {
            $path = \trim((string)@\shell_exec('which cwebp 2>/dev/null'));
        }

        if ($path === '') {
            foreach (['/usr/bin/cwebp', '/usr/local/bin/cwebp'] as $candidate) {
                if (\is_executable($candidate)) {
                    $path = $candidate;
                    break;
                }
            }
        }

        if ($path !== '') {
            $versionOut = @\shell_exec('cwebp -version 2>&1');
            $version = 'unbekannt';
            if ($versionOut !== null && $versionOut !== false && $versionOut !== '') {
                $lines = \preg_split('/\r\n|\r|\n/', $versionOut);
                if ($lines !== false && isset($lines[0])) {
                    $version = \trim($lines[0]);
                }
            }
            $this->currentState = \sprintf('gefunden: %s | Version: %s', $path, $version);
            return true;
        }

        $this->currentState = 'nicht gefunden';
        return false;
    }
}
