<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class GetObjectCommand extends Command 
{
    /**
     * @var array
     */
    private $params_keys = [
        'response-content-type',
        'response-content-language',
        'response-expires',
        'response-cache-control',
        'response-content-disposition',
        'response-content-encoding',
    ];

    /**
     * @var array
     */
    private $headers_keys = [
        'Range',
        'If-Modified-Since',
        'If-Unmodified-Since',
        'If-Match',
        'If-None-Match',
    ];

    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();
        return $build->setMethod('GET')
            ->setBucket($options['bucket'])
            ->setPath($options['path'])
            ->setHeaders($this->getParams($options, $this->headers_keys))
            ->setParams($this->getParams($options, $this->params_keys))
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        return $response;
    }
}
