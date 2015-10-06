<?php

namespace Shion\Aliyun\OSS\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class Command
{
    /**
     * Build the request
     *
     * @param array $options
     *
     * @return RequestBuilder
     */
    abstract public function build($options);

    /**
     * Parser the response
     *
     * @param GuzzleHttp\Psr7\Response $response
     *
     * @return mix
     */
    abstract public function parser(Response $response);

    /**
     * Get params by the keys
     *
     * @param array $options
     * @param array $params_keys
     *
     * @return array
     */
    public function getParams($options, $params_keys)
    {
        $params = [];
        foreach ($options as $k => $v) {
            if (in_array($k, $params_keys)) {
                $params[$k] = $v;
            }
        }
        
        return $params;
    }

    /**
     * Execute
     *
     * @param array $options
     *
     * @return mix
     */
    public function execute($options)
    {
        $request = $this->build($options);
        
        $client = new Client([
            'base_uri' => '',
            'timeout' => 10,
            'allow_redirects' => [
                'max' => 5,
                'strict' => true,
                'referer' => true,
                'protocols' => ['http'],
            ],
            'http_errors' => false,
        ]);

        $response = $client->request($request->getMethod(),
            $request->getUrl(),
            [
                'headers' => $request->getHeaders(),
                'body' => $request->getBody(),
            ]
        );

        return $this->parser($response);
    }

    /**
     * Check whether the request is successful
     *
     * @param GuzzleHttp\Psr7\Response $response
     *
     * @throws Exception
     *
     * @return bool
     */
    public function isSuccess(Response $response)
    {
        $code = $response->getStatusCode();
        return ($code >= 200 && $code < 300) || $code == 304 ? true : false;
    }
}
