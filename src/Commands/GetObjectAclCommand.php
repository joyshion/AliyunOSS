<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class GetObjectAclCommand extends Command 
{
    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();
        return $build->setMethod('GET')
            ->setBucket($options['bucket'])
            ->setPath($options['path'])
            ->setParams('acl')
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        $contents = $response->getBody()->getContents();
        $data = simplexml_load_string($contents);
        return $data->AccessControlList->Grant;
    }
}
