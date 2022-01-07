<?php

namespace EasyUpload\struct;

/**
 * oss配置
 */
class OssConfigStruct
{
    private string $keyId = '';  // 您的Access Key ID
    private string $keySecret = '';   // 您的Access Key Secret
    private string $networkProtocol = 'http';  // 阿里云oss 外网协议 默认http
    private string $endpoint = 'oss-cn-shenzhen.aliyuncs.com';  // 阿里云oss 外网地址endpoint 不带Bucket名称
    private string $bucket = '';   // Bucket名称
    private string $httpHost = '';

    /**
     * @return string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * @param string $keyId
     */
    public function setKeyId(string $keyId): void
    {
        $this->keyId = $keyId;
    }

    /**
     * @return string
     */
    public function getKeySecret(): string
    {
        return $this->keySecret;
    }

    /**
     * @param string $keySecret
     */
    public function setKeySecret(string $keySecret): void
    {
        $this->keySecret = $keySecret;
    }

    /**
     * @return string
     */
    public function getNetworkProtocol(): string
    {
        return $this->networkProtocol;
    }

    /**
     * @param string $networkProtocol
     */
    public function setNetworkProtocol(string $networkProtocol): void
    {
        $this->networkProtocol = $networkProtocol;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket(string $bucket): void
    {
        $this->bucket = $bucket;
    }

    /**
     * @return string
     */
    public function getHttpHost(): string
    {
        return $this->httpHost;
    }

    /**
     * @param string $httpHost
     */
    public function setHttpHost(string $httpHost): void
    {
        $this->httpHost = $httpHost;
    }  // 自定义域名


}