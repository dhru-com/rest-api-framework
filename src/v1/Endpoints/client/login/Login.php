<?php

namespace Dhru\Endpoints\client\login;


use Dhru\Lib\Comm;
use Dhru\Interfaces\EndpointInterface;
use Dhru\Traits\EndpointTrait;

class Login implements EndpointInterface
{
    use EndpointTrait;

    /*
    * validate User Login
    */
    function validateLogin($username, $password)
    {

        $user_details['id'] = 101;
        $user_details['name'] = 'user';
        $user_details['email'] = 'user@demo.com';

        return $user_details;

    }

}
