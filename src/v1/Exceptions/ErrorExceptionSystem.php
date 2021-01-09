<?php

namespace Dhru\Exceptions;

define('ERRORCODE_REQUIRED_PARAMETERS',592);

class ErrorExceptionSystem extends ExceptionHandler
{

    public function __construct(string $message, int $code , array $data = [],  array $info = [])
    {
        parent::__construct($message, $code, $data , $info);

    }

}
