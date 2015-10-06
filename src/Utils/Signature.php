<?php

namespace Shion\Aliyun\OSS\Utils;

class Signature
{
    /**
     * @var RequestBuilder
     */
    private $build;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $oss_headers;

    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $overrides;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $param_keys = [
        'acl',
        'uploadId',
        'partNumber',
        'uploads',
        'logging',
        'website',
        'location',
        'lifecycle',
        'referer',
        'cors',
        'delete',
        'append',
        'position',
        'security-token',
    ];

    /**
     * @var array
     */
    private $override_keys = [
        'response-content-type',
        'response-content-language',
        'response-expires',
        'response-cache-control',
        'response-content-disposition',
        'response-content-encoding',
    ];

    /**
     * Construct a OSS Signature.
     *
     * @param RequestBuilder $build
     */
    public function __construct(RequestBuilder $build)
    {
        $this->build = $build;
        $this->headers = $build->getHeaders();
        $this->oss_headers = $build->getOSSHeaders();
        $this->params = $build->getParams();
        $this->overrides = $build->getOverride();
        $this->body = $build->getBody();
    }

    /**
     * Create the signature string
     *
     * @param  string $access_key
     * @return string
     */
    public function create($access_key)
    {
        $str = $this->build->getMethod() . "\n";
        $str .= $this->headers['Content-Md5'] . "\n";
        $str .= $this->headers['Content-Type'] . "\n";
        $str .= $this->headers['Date'] . "\n";

        foreach ($this->oss_headers as $k => $v) {
            $str .= $k . ':' . $v . "\n";
        }

        $str .= $this->build->getPath();

        $params = [];
        $overrides = [];
        foreach ($this->params as $k => $v) {
            if (in_array($k, $this->param_keys)) {
                $params[$k] = $v;
            }
        }
        foreach ($this->overrides as $k => $v) {
            if (in_array($k, $this->override_keys)) {
                $overrides[$k] = $v;
            }
        }

        $query = array_merge($params, $overrides);
        $query_str = http_build_query($query);
        if (!empty($query_str)) {
            $query_str = str_replace('acl=', 'acl', $query_str);
            $str .= '?' . $query_str;
        }

        return base64_encode(hash_hmac('sha1', $str, $access_key, true));
    }
}
