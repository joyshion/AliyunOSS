<?php

namespace Shion\Aliyun\OSS\Utils;

use League\Flysystem\Util;
use League\Flysystem\Util\MimeType;

class RequestBuilder
{
    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $oss_headers = [];

    /**
     * @var string|resource
     */
    private $body;

    /**
     * @var array
     */
    private $overrides = [];

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $host;

    /**
     * Get bucket
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Set bucket
     *
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set method
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set header
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $k => $v) {
            $this->headers[$k] = $v;
        }
        return $this;
    }

    /**
     * Get OSS headers
     *
     * @return array
     */
    public function getOSSHeaders()
    {
        ksort($this->oss_headers);
        return $this->oss_headers;
    }

    /**
     * Set OSS header
     *
     * @param array $headers
     */
    public function setOSSHeaders($headers)
    {
        foreach ($headers as $k => $v) {
            $name = strtolower($k);
            if (isset($this->oss_headers[$name])) {
                $this->oss_headers[$name] .= ',' . $v;
            } else {
                $this->oss_headers[$name] = $v;
            }
        }
        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        if (!empty($body)) {
            $this->body = $body;
        }
        return $this;
    }

    /**
     * Get OSS override header
     *
     * @return array
     */
    public function getOverride()
    {
        ksort($this->overrides);
        return $this->overrides;
    }

    /**
     * Set OSS override header
     *
     * @param string $name
     * @param string $value
     */
    public function setOverride($name, $value)
    {
        $this->override[$name] = $value;
        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        ksort($this->params);
        return $this->params;
    }

    /**
     * Set params
     *
     * @param array $params
     */
    public function setParams($params)
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $this->params[$k] = $v;
            }
        } else {
            $this->params[$params] = '';
        }
        
        return $this;
    }

    /**
     * Get url
     */
    public function getUrl()
    {
        return $this->endpoint . $this->path . $this->query;
    }

    /**
     * Get the mime-type from a given path or content
     *
     * @param string $path
     * @param string|resource $content
     * @return string
     */
    private function getMimeType($path, $content)
    {
        if (is_resource($content)) {
            $meta = stream_get_meta_data($content);
            $ext = pathinfo($meta['uri'], PATHINFO_EXTENSION);
            $map = MimeType::getExtensionToMimeTypeMap();
            if (isset($map[$ext])) {
                return $map[$ext];
            } else {
                return '';
            }
        } else {
            return Util::guessMimeType($path, $content);
        }
    }

    /**
     * Get the md5 from a given content
     *
     * @param string|resource $content
     * @return string
     */
    private function getMD5($content)
    {
        if (is_resource($content)) {
            $meta = stream_get_meta_data($content);
            return md5_file($meta['uri'], true);
        } else {
            return md5($content, true);
        }
    }

    /**
     * Build the request
     *
     * @param array $options
     * @return RequestBuilder
     */
    public function build($options)
    {
        $this->endpoint = $options['endpoint'];

        $this->headers['Date'] = gmdate('D, d M Y H:i:s \G\M\T');

        if (isset($this->body)) {
            if (is_resource($this->body)) {
                $this->headers['Content-Length'] = Util::getStreamSize($this->body);
            } else {
                $this->headers['Content-Length'] = strlen($this->body);
            }

            $this->headers['Content-Md5'] = base64_encode($this->getMD5($this->body));

            if (isset($options['Content-Type'])) {
                $this->headers['Content-Type'] = $options['Content-Type'];
            } else {
                $this->headers['Content-Type'] = $this->getMimeType($this->path, $this->body);
            }
        } else {
            $this->headers['Content-Md5'] = '';
            $this->headers['Content-Type'] = '';
        }

        $this->headers['Host'] = $options['host'];

        ksort($this->oss_headers);
        foreach ($this->oss_headers as $k => $v) {
            $this->headers[$k] = $v;
        }

        if (!isset($this->bucket)) {
            $this->path = '/';
        } else {
            $this->path = '/' . $this->bucket . '/' . $this->path;
        }

        ksort($this->params);
        ksort($this->overrides);
        $query = array_merge($this->params, $this->overrides);
        $query_str = http_build_query($query);
        if (!empty($query_str)) {
            $this->query = '?' . $query_str;
            $this->query = str_replace('acl=', 'acl', $this->query);
        }

        ksort($this->headers);
        
        $sign = new Signature($this);
        $this->headers['Authorization'] = 'OSS ' . $options['access_id'] . ':' . $sign->create($options['access_key']);

        return $this;
    }
}
