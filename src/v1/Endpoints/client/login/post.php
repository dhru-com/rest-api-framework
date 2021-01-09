<?php

namespace Dhru\Endpoints\client\login;

use Dhru\Exceptions\ErrorException;
use Dhru\Exceptions\SuccessException;
use Dhru\Lib\StatusCodes;

class post extends Login
{


    function __construct($Base)
    {
        parent::__construct($Base);

        if($user_details = $this->validateLogin($this->para['username'], $this->para['password'])){

            $tokenId = \Dhru\Lib\Comm::UUID();
            $userId = $user_details['id'];
            $OUT['token'] = \Dhru\Lib\Token::generate($tokenId, 'web', $userId, $user_details);
            $OUT['profile'] = $user_details;
            throw new SuccessException("Login Success!", $OUT);

        }else{
            throw new ErrorException('Invalid Login',StatusCodes::UNAUTHORIZED);
        }


    }




    static function schema()
    {
        /* API CONFIG */
        $RETURN['name'] = 'Login Client';
        $RETURN['description'] = 'Login Client';
        $RETURN['loginreq'] = false;
        $RETURN['captcha_req'] = true;


        /* BODY */
        $BODY['email']['type'] = 'email';
        $BODY['email']['isrequired'] = true;
        $BODY['email']['example'] = 'user@demo.com';

        $BODY['password']['type'] = 'string';
        $BODY['password']['isrequired'] = true;
        $BODY['password']['example'] = 'user';




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
