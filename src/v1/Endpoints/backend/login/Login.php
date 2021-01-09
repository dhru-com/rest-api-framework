<?php

namespace Dhru\Endpoints\backend\login;

use Dhru\Interfaces\EndpointInterface;
use Dhru\Traits\EndpointTrait;

class Login implements  EndpointInterface
{
    use EndpointTrait;

    /*
     * validate backend Login
     */
    function validateLogin($username, $password)
    {

        $staff_details['id'] =1;
        $staff_details['name'] ='admin';
        $staff_details['email'] ='admin@demo.com';

        return $staff_details;

    }


}
