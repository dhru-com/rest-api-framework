<?php

namespace Dhru\Exceptions;


class SuccessException extends ExceptionHandler
{
    public function __construct(string $message, array $data = [],  array $info = [])
    {
        parent::__construct($message, 0, $data , $info);
    }
}