<?php

namespace Shion\Aliyun\OSS\Exception;

class OSSException extends \Exception
{
    /**
     * Construct a OSS Exception.
     *
     * @param string $error
     * @param string $message
     */
    public function __construct($error, $message = '')
    {
        parent::__construct($error . ':' . $message);
    }
}
