<?php

namespace Shion\Aliyun\Oss\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class CopyObjectCommand extends Command 
{
    /**
     * @var array
     */
    private $oss_header_keys = [
        'x-oss-copy-source',
        'x-oss-copy-source-if-match',
        'x-oss-copy-source-if-none-match',
        'x-oss-copy-source-if-unmodified-since',
        'x-oss-copy-source-if-modified-since',
        'x-oss-metadata-directive',
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
            ->setOSSHeaders($this->getParams($options, $this->oss_header_keys))
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        return $response->hasHeader('ETag');
    }
}
