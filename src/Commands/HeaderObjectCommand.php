<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class HeaderObjectCommand extends Command 
{
    /**
     * @var array
     */
    private $headers_keys = [
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
        return $build->setMethod('HEAD')
            ->setBucket($options['bucket'])
            ->setPath($options['path'])
            ->setHeaders($this->getParams($options, $this->headers_keys))
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
