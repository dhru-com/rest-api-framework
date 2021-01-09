<?php

namespace Dhru\Endpoints\backend\login;
use Dhru\Exceptions\ErrorException;
use Dhru\Exceptions\SuccessException;
use Dhru\Lib\StatusCodes;
use Dhru\Objects\AdminObj;


class post extends Login
{

    function __construct($Base)
    {
        parent::__construct($Base);


        if($staff_details = $this->validateLogin($this->para['username'], $this->para['password'])){

            $tokenId = \Dhru\Lib\Comm::UUID();
            $userId = $staff_details['id'];
            $OUT['token'] = \Dhru\Lib\Token::generate($tokenId, 'web', $userId, $staff_details);
            $OUT['profile'] = $staff_details;
            throw new SuccessException("Login Success!", $OUT);

        }else{
            throw new ErrorException('Invalid Login',StatusCodes::UNAUTHORIZED);
        }


    }


    static function schema()
    {
        /* API CONFIG */
        $RETURN['name'] = 'Login Staff';
        $RETURN['description'] = 'Login Staff';
        $RETURN['loginreq'] = false;
        $RETURN['captcha_req'] = true;

        /* BODY */
        {
            $Username['type']='email';
            $Username['isrequierd']=true;
            $Username['example']='admin@demo.com';
        }
        $BODY['username']= $Username;


        {
            $Password['type']='string';
            $Password['isrequierd']=true;
            $Password['example']='admin';
        }
        $BODY['password']= $Password;


        /* BODY,querystring BASE */
        $RETURN['body']['type'] = 'obj'; /*  obj,list */
        $RETURN['body']['isrequired'] = true;
        $RETURN['body']['schema'] = $BODY;

        /* Postman Events */

        $_test['listen'] = 'test';
        $_test['script']['exec'][0] = "pm.environment.set(\"AUTH_TOKEN\", pm.response.json().token);";
        $_test['script']['type'] = "text/javascript";

        $RETURN['postmanevent'][] = $_test;

        return $RETURN;
    }

}
