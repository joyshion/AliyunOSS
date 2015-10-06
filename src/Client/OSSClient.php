<?php

namespace Shion\Aliyun\OSS\Client;

class OSSClient
{
    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get OSS region prefix
     *
     * @return string
     */
    private function _getRegionPrefix()
    {
        switch ($this->config['region']) {
            case 'west-1':
                return 'us';
                break;
            case 'southeast-1':
                return 'ap';
                break;
            default:
                return 'cn';
        }
    }

    /**
     * Get OSS host name
     *
     * @return string
     */
    private function _getHost()
    {
        if ($this->config['internal_mode']) {
            return 'oss-' . $this->_getRegionPrefix() . '-' . $this->config['region'] . '-internal.aliyuncs.com';
        } else {
            return 'oss-' . $this->_getRegionPrefix() . '-' . $this->config['region'] . '.aliyuncs.com';
        }
    }

    /**
     * Get OSS endpoint
     *
     * @return string
     */
    private function _getEndpoint()
    {
        return 'http://' . $this->_getHost();
    }

    /**
     * Get options
     *
     * @param array $options
     * @return string
     */
    private function _getOptions($options)
    {
        return array_merge([
            'bucket' => $this->config['bucket'],
            'endpoint' => $this->_getEndpoint(),
            'host' => $this->_getHost(),
            'access_id' => $this->config['access_id'],
            'access_key' => $this->config['access_key'],
        ], $options);
    }

    /**
     * Get config
     *
     * @param string $name
     * @return mix
     */
    public function getConfig($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : '';
    }

    /**
     * Get bucket location
     *
     * @return string
     */
    public function getLocaltion()
    {
        return 'oss-' . $this->_getRegionPrefix() . '-' . $this->config['region'];
    }

    /**
     * List buckets
     *
     * @return array
     */
    public function listBuckets($options = [])
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Write the contents of a file.
     *
     * @param  array  $options
     * @return bool
     */
    public function putObject($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Copy a file to a new location.
     *
     * @param  array  $options
     * @return bool
     */
    public function copyObject($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  array  $options
     * @return bool
     */
    public function deleteObject($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Get the header of a given file.
     *
     * @param  array  $options
     * @return array
     */
    public function headerObject($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Get the contents of a file.
     *
     * @param  array  $options
     * @return string
     */
    public function getObject($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Get the acl of a file.
     *
     * @param  array  $options
     * @return string
     */
    public function getObjectAcl($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Get the acl of bucket.
     *
     * @param  array  $options
     * @return string
     */
    public function getBucketAcl($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Set the acl of file.
     *
     * @param  array  $options
     * @return bool
     */
    public function putObjectAcl($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Create bucket.
     *
     * @param  array  $options
     * @return bool
     */
    public function putBucket($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Set the acl of bucket.
     *
     * @param  array  $options
     * @return bool
     */
    public function putBucketAcl($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Get files from the bucket.
     *
     * @param  array  $options
     * @return array
     */
    public function getBucket($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Delete bucket.
     *
     * @param  array  $options
     * @return bool
     */
    public function deleteBucket($options)
    {
        return $this->execute(__FUNCTION__, $options);
    }

    /**
     * Execute commond.
     *
     * @param  string  $command
     * @param  array   $options
     * @return mix
     */
    public function execute($command, $options)
    {
        $class_name = 'Shion\Aliyun\OSS\Commands\\' . ucfirst($command) . 'Command';
        $class = new $class_name();
        return $class->execute($this->_getOptions($options));
    }
}
