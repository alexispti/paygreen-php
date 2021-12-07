<?php

namespace Paygreen\Sdk\Core;

use InvalidArgumentException;

abstract class Environment
{
    const API_VERSION_2 = 2;
    const API_VERSION_3 = 3;

    const ENVIRONMENT_SANDBOX = 'SANDBOX';
    const ENVIRONMENT_PRODUCTION = 'PRODUCTION';

    /** @var string */
    protected $bearer;

    /** @var string */
    protected $environment;

    /** @var int */
    protected $apiVersion;

    /** @var string */
    protected $applicationName = 'sdk';

    /** @var string */
    protected $applicationVersion = '1.0.0';

    /**
     * @param string     $environment
     * @param int|string $apiVersion
     */
    public function __construct($environment, $apiVersion)
    {
        $this->environment = strtoupper($environment);
        $this->apiVersion = (int) $apiVersion;
        $availableEnvironments = [self::ENVIRONMENT_PRODUCTION, self::ENVIRONMENT_SANDBOX];

        if (!in_array($this->environment, $availableEnvironments)) {
            throw new InvalidArgumentException('Environment only accept: '.implode(', ', $availableEnvironments).'. Current: "'.$environment.'"');
        }
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return int
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @return string
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * @return string
     */
    public function getApplicationVersion()
    {
        return $this->applicationVersion;
    }

    /**
     * @return string
     */
    public function getBearer()
    {
        return $this->bearer;
    }

    /**
     * @param string $bearer
     */
    public function setBearer($bearer)
    {
        $this->bearer = $bearer;
    }

    /**
     * @param string $applicationName
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;
    }

    /**
     * @param string $applicationVersion
     */
    public function setApplicationVersion($applicationVersion)
    {
        $this->applicationVersion = (string) $applicationVersion;
    }

    /**
     * @return string
     */
    abstract public function getEndpoint();
}
