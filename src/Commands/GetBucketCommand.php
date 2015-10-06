<?php

namespace Shion\Aliyun\OSS\Commands;

use Shion\Aliyun\OSS\Utils\RequestBuilder;
use GuzzleHttp\Psr7\Response;

class GetBucketCommand extends Command 
{
    /**
     * @var array
     */
    private $params_keys = [
        'delimiter',
        'marker',
        'max-keys',
        'prefix',
        'encoding-type',
    ];

    /**
     * @inheritdoc
     */
    public function build($options)
    {
        $build = new RequestBuilder();

        return $build->setMethod('GET')
            ->setBucket($options['bucket'])
            ->setParams($this->getParams($options, $this->params_keys))
            ->build($options);
    }

    /**
     * @inheritdoc
     */
    public function parser(Response $response)
    {
        $result = $response->getBody()->getContents();
        $data = simplexml_load_string($result);
        return $data;
    }
}
