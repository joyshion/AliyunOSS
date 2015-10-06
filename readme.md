#Shion\Aliyun\OSS
Flysystem adapter for the Aliyun OSS, support for Laravel.

## Installation
Through Composer, obviously:
```bash
composer require shion/aliyun-oss
```
Once Aliyun OSS is installed, you need to register the service provider. Open up config/app.php and add the following to the providers key.
```php
Shion\Aliyun\OSS\OSSServiceProvider::class
```

## Configuration
```php
'oss' => [
    'driver' => 'oss',
    'access_id' => 'your_access_id',
    'access_key' => 'your_access_key',
    'bucket' => 'bucket_name',
    'region' => 'bucket_region',
    'internal_mode' => false,
],
```
> #### region
> The region of Aliyun OSS Data Center. Accepted region are `hangzhou`, `qingdao`, `beijing`, `hongkong`, `shenzhen`, `shanghai`, `west-1` and `southeast-1`.

> #### internal_mode 
> Use the internal mode. Provided that your ECS and Bucket in the same region.

##Usage 
Visit [http://laravel.com/docs/5.1/filesystem](http://laravel.com/docs/5.1/filesystem "http://laravel.com/docs/5.1/filesystem")
