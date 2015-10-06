<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;
use Shion\Aliyun\OSS\Exception\OSSException;

class ListBucketsCommand extends Command 
{
    /**
     * @var array
     */
    private $params_keys = ['prefix', 'marker', 'max-keys'];

    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();
        return $build->setMethod('GET')
            ->setParams($this->getParams($options, $this->params_keys))
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        $contents = $response->getBody()->getContents();
        $obj = simplexml_load_string($contents);

        if ($this->isSuccess($response)) {
            $data = json_decode(json_encode($obj->Buckets), true);
            return $data['Bucket'];
        } else {
            throw new OSSException($obj->Code, $obj->Message);
        }
    }
}
