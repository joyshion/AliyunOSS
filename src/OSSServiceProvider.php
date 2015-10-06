<?php

namespace Shion\Aliyun\OSS;

use Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Shion\Aliyun\OSS\Client\OSSClient;
use Shion\Aliyun\OSS\Adapter\OSSAdapter;

class OSSServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        Storage::extend('oss', function($app, $config) {
            $client = new OSSClient($config);
            
            return new Filesystem(new OSSAdapter($client, $config['bucket']));
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        
    }
}
