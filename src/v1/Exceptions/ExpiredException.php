<?php

namespace Dhru\Exceptions;

class ExpiredException extends ErrorException
{

    public function __construct(string $message, int $code = 0, array $data = [], array $info = [])
    {
        parent::__construct($message, 401, $data, $info);
    }

}