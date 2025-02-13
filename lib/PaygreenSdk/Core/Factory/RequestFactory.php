<?php

namespace Paygreen\Sdk\Core\Factory;

use GuzzleHttp\Psr7\Request;
use Paygreen\Sdk\Core\Environment;
use Psr\Http\Message\StreamInterface;

class RequestFactory
{
    const SDK_VERSION = '1.2.1';

    /** @var Request */
    public $request;

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param string                               $url
     * @param null|resource|StreamInterface|string $body
     * @param string                               $method
     * @param array                                $headers
     *
     * @return RequestFactory
     */
    public function create(
        $url,
        $body = null,
        $method = 'POST',
        $headers = []
    ) {
        $url = $this->environment->getEndpoint().$url;

        $headers['User-Agent'] = $this->buildUserAgentHeader();

        $this->request = new Request($method, $url, $headers, $body);

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return RequestFactory
     */
    public function withAuthorization()
    {
        $this->request = $this->request->withAddedHeader(
            'Authorization',
            'Bearer '.$this->environment->getBearer()
        );

        return $this;
    }

    /**
     * @return RequestFactory
     */
    public function withTestMode()
    {
        if ($this->environment->isTestMode()) {
            $this->request = $this->request->withAddedHeader('X-TEST-MODE', 1);
        }

        return $this;
    }

    /**
     * @return RequestFactory
     */
    public function isJson()
    {
        $size = 0;
        $body = $this->request->getBody();
        if (null !== $body) {
            $size = (string) $body->getSize();
        }

        $this->request = $this->request->withAddedHeader('Content-Type', 'application/json');
        $this->request = $this->request->withAddedHeader('Accept', 'application/json');
        $this->request = $this->request->withAddedHeader('Content-Length', $size);

        return $this;
    }

    /**
     * @return string
     */
    private function buildUserAgentHeader()
    {
        $applicationName = $this->environment->getApplicationName();
        $applicationVersion = $this->environment->getApplicationVersion();
        $isPhpMajorVersionDefined = defined('PHP_MAJOR_VERSION');
        $isPhpMinorVersionDefined = defined('PHP_MINOR_VERSION');
        $isPhpReleaseVersionDefined = defined('PHP_RELEASE_VERSION');

        if ($isPhpMajorVersionDefined && $isPhpMinorVersionDefined && $isPhpReleaseVersionDefined) {
            $phpVersion = PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION;
        } else {
            $phpVersion = phpversion();
        }

        $sdkVersion = self::SDK_VERSION;

        return "Application:$applicationName:$applicationVersion sdk:{$sdkVersion} php:$phpVersion;";
    }
}
