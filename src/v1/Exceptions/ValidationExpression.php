<?php

namespace Dhru\Exceptions;

class ValidationExpression extends ErrorException{

    public function __construct(string $message, int $code = 0 , array $data = [],  array $info = [])
    {
        parent::__construct($message, 98, $data , $info);

    }

}