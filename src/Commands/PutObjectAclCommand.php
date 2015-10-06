<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class PutObjectAclCommand extends Command 
{
    /**
     * Get oss acl
     *
     * @param  string $acl
     * @return array
     */
    private function _getAcl($acl)
    {
        $acl = $acl == 'public' ? 'public-read' : 'private';
        return ['x-oss-object-acl' => $acl];
    }

    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();

        return $build->setMethod('PUT')
            ->setBucket($options['bucket'])
            ->setPath($options['path'])
            ->addOSSHeaders($this->_getAcl($options['acl']))
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        return $response->getStatusCode() == 200;
    }
}
