<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;
use League\Flysystem\AdapterInterface;

class GetBucketAclCommand extends Command 
{
    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();
        return $build->setMethod('GET')
            ->setBucket($options['bucket'])
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
        $oss_acl = $data->AccessControlList->Grant;
        if (in_array($oss_acl, ['public-read', 'public-read-write'])) {
            return AdapterInterface::VISIBILITY_PUBLIC;
        } else {
            return AdapterInterface::VISIBILITY_PRIVATE;
        }
    }
}
