<?php

namespace Shion\Aliyun\OSS\Adapter;

use Shion\Aliyun\OSS\Client\OSSClient;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use League\Flysystem\Util;
use League\Flysystem\Util\MimeType;

class OSSAdapter extends AbstractAdapter
{
    /**
     * @var OSSClient
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @inheritdoc
     */
    public function __construct(OSSClient $client, $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    /**
     * @inheritdoc
     */
    public function listBucket()
    {
        return $this->client->listBuckets();
    }

    /**
     * Create bucket
     *
     * @param string $bucket
     * @param string $region
     * @param string $acl
     * @return bool
     */
    public function putBucket($bucket, $region = '', $acl = 'public')
    {
        $region = empty($region) ? $this->client->getLocaltion() : $region;
        
        $options = [
            'bucket' => $bucket,
            'region' => $region,
            'acl' => $acl,
        ];

        return $this->client->putBucket($options);
    }

    /**
     * Set bucket acl
     *
     * @param string $acl
     * @param string $bucket
     * @return bool
     */
    public function putBucketAcl($acl = 'public', $bucket = '')
    {
        $options = [
            'bucket' => empty($bucket) ? $this->bucket : $bucket,
            'acl' => $acl,
        ];

        return $this->client->putBucketAcl($options);
    }

    /**
     * Get bucket(List object)
     *
     * @return object
     */
    public function getBucket($bucket = '')
    {
        $options = [
            'bucket' => empty($bucket) ? $this->bucket : $bucket,
        ];

        return $this->client->getBucket($options);
    }

    /**
     * Get bucket acl
     *
     * @return string public|private
     */
    public function getBucketAcl($bucket = '')
    {
        $options = [
            'bucket' => empty($bucket) ? $this->bucket : $bucket,
        ];

        return $this->client->getBucketAcl($options);
    }

    /**
     * Delete bucket
     *
     * @return bool
     */
    public function delBucket($bucket = '')
    {
        $options = [
            'bucket' => empty($bucket) ? $this->bucket : $bucket,
        ];

        return $this->client->delBucket($options);
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        $options = [
            'marker' => 2000,
            'max-keys' => 1000,
            'bucket' => $this->bucket,
            'prefix' => $directory,
        ];

        $data = $this->client->getBucket($options);

        $result = [];
        foreach ($data->Contents as $v) {
            $result[] = [
                'type' => (int) $v->Size > 0 ? 'file' : 'dir',
                'dirname' => Util::dirname($v->Key),
                'path' => rtrim($v->Key, '/'),
                'timestamp' => strtotime($v->LastModified),
                'mimetype' => MimeType::detectByFileExtension(pathinfo($v->Key, PATHINFO_EXTENSION)),
                'size' => (int) $v->Size,
            ];
        }
        
        return Util::emulateDirectories($result);
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, Config $config)
    {
        $options = [
            'path' => $path,
            'contents' => $contents,
        ];

        return $this->client->putObject($options);
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        $options = [
            'path' => $path,
            'contents' => $resource,
        ];

        return $this->client->putObject($options);
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, Config $config)
    {
        return $this->write($dirname . '/', '', $config);
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        return $this->delete($dirname . '/');
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $options = [
            'path' => $newpath,
            'x-oss-copy-source' => '/' . $this->bucket . '/' . $path,
        ];

        return $this->client->copyObject($options);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->getObject($options);

        return ['contents' => $response->getBody()->getContents()];
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->getObject($options);

        return ['contents' => $response->getBody()];
    }

    /**
     * @inheritdoc
     */
    public function has($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->headerObject($options);

        return $response->getStatusCode() == 200 || $response->getStatusCode() == 304;
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $options = [
            'path' => $path,
        ];
        
        return $this->client->deleteObject($options);
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        $options = [
            'path' => $path,
        ];

        $acl = $this->client->getObjectAcl($options);
        if ($acl == 'default') {
            $acl = $this->getBucketAcl();
        }

        return ['visibility' => $acl];
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        $options = [
            'path' => $path,
            'acl' => $visibility,
        ];

        return $this->client->putObjectAcl($options);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->headerObject($options);

        return ['timestamp' => strtotime($response->getHeaderLine('Last-Modified'))];
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->headerObject($options);

        $metaData = [];
        foreach ($response->getHeaders() as $k => $v) {
            $metaData[$k] = $response->getHeaderLine($k);
        }

        return $metaData;
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->headerObject($options);

        return ['size' => $response->getHeaderLine('content-length')];
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        $options = [
            'path' => $path,
        ];

        $response = $this->client->headerObject($options);

        return ['mimetype' => $response->getHeaderLine('Content-Type')];
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $this->copy($path, $newpath);
        $this->delete($path);

        return true;
    }    
}
