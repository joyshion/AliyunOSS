<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class PutObjectCommand extends Command 
{
    /**
     * @var array
     */
    private $header_keys = [
        'Cache-Control',
        'Content-Disposition',
        'Content-Encoding',
        'Content-MD5',
        'Expires',
    ];

    /**
     * @var array
     */
    private $oss_header_keys = [
        'x-oss-server-side-encryption',
        'x-oss-object-acl',
    ];

    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();
        return $build->setMethod('PUT')
            ->setBucket($options['bucket'])
            ->setPath($options['path'])
            ->setBody($options['contents'])
            ->addHeaders($this->getParams($options, $this->header_keys))
            ->addOSSHeaders($this->getParams($options, $this->oss_header_keys))
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        return $response->getStatusCode() == 200 && $response->hasHeader('ETag');
    }
}
