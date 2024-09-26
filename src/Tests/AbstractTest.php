<?php

declare(strict_types=1);

namespace Systemcheck\Tests;

use JsonSerializable;

/**
 * Class AbstractTest
 * @package Systemcheck\Tests
 */
abstract class AbstractTest implements JsonSerializable
{
    public const RESULT_OK = 0;

    public const RESULT_FAILED = 1;

    public const RESULT_UNKNOWN = 2;

    protected string $name;

    protected string $currentState = '';

    protected int $result = self::RESULT_FAILED;

    protected string $requiredState;

    protected string $description = '';

    protected bool $isRecommended = false;

    protected bool $isOptional = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function getRequiredState(): string
    {
        return $this->requiredState;
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIsOptional(): bool
    {
        return $this->isOptional;
    }

    public function getIsRecommended(): bool
    {
        return $this->isRecommended;
    }

    /**
     * @return string|false
     */
    public function getIsReplaceableBy(): bool|string
    {
        return \property_exists($this, 'isReplaceableBy')
            ? $this->isReplaceableBy
            : false;
    }

    public function getRunStandAlone(): ?bool
    {
        return \property_exists($this, 'runStandAlone')
            ? $this->runStandAlone
            : null; // do not change to 'false'! we need three states here!
    }

    /**
     * @return self::RESULT_FAILED|self::RESULT_OK|self::RESULT_UNKNOWN
     */
    public function getResult(): int
    {
        return $this->result;
    }

    public function setResult(bool $result): void
    {
        $this->result = $result === true ? self::RESULT_OK : self::RESULT_FAILED;
    }

    /**
     * @return bool - true if the test was successful, false otherwise
     */
    abstract public function execute(): bool;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
