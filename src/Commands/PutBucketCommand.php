<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class PutBucketCommand extends Command 
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
        return ['x-oss-acl' => $acl];
    }

    /**
     * Get body content
     *
     * @param  string $region
     * @return string
     */
    private function getBody($region)
    {
        return <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<CreateBucketConfiguration>
<LocationConstraint>{$region}</LocationConstraint>
</CreateBucketConfiguration>
EOT;
    }

    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $acl = $this->_getAcl($options['acl']);

        $build = new RequestBuilder();
        return $build->setMethod('PUT')
            ->setBucket($options['bucket'])
            ->setBody($this->getBody($options['region']))
            ->setOSSHeaders($acl)
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
