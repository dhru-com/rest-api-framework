<?php

namespace Dhru\Endpoints\backend\boilerplate;

use Dhru\Exceptions\ErrorException;
use Dhru\Exceptions\SuccessException;

class post extends Boilerplate
{

    function __construct($Base)
    {
        parent::__construct($Base);
        /*
         * $this->token  login user details .
         */
        throw new SuccessException("Boilerplate POST Loaded" , (array) $this->token);
    }

    static function schema()
    {
        /* API CONFIG */
        $RETURN['name'] = 'Add Boilerplate';
        $RETURN['description'] = 'Add new data to Boilerplate';
        $RETURN['loginreq'] = false;
        //$RETURN['captcha_req'] = true;



        /* BODY */
        $BODY['stringfield']['type'] = 'string';
        $BODY['stringfield']['isrequired'] = true;
        $BODY['stringfield']['example'] = 'This is string';

        $BODY['ingerfield']['type'] = 'int';
        $BODY['ingerfield']['isrequired'] = true;
        $BODY['ingerfield']['example'] = 101;

        $BODY['floatfield']['type'] = 'float';
        $BODY['floatfield']['isrequired'] = true;
        $BODY['floatfield']['example'] = 10.5;

        $BODY['regx']['type'] = 'regx';
        $BODY['regx']['regx'] = '/^[A-Za-z0-9]+$/';
        $BODY['regx']['isrequired'] = true;
        $BODY['regx']['example'] = 'ABC123';

        $BODY['enumfield']['type'] = 'enum';
        $BODY['enumfield']['enumoptions'] = ['one', 'tow', 'three'];
        $BODY['enumfield']['isrequired'] = true;
        $BODY['enumfield']['example'] = 'one';

        $BODY['customfunction']['type'] = 'function';
        $BODY['customfunction']['functionname'] = 'verifyIMEI'; /* verifyEmail, */
        $BODY['customfunction']['isrequired'] = true;
        $BODY['customfunction']['example'] = '111111111111119';

        $BODY['uuid']['type'] = 'uuid';
        $BODY['uuid']['isrequired'] = true;
        $BODY['uuid']['example'] = '29bbc125-914b-4ad4-8140-ff0c01bcf326';


        $BODY['base64']['type'] = 'base64';
        $BODY['base64']['isrequired'] = true;
        $BODY['base64']['example'] = 'YmFzZSA2NCBlbmNvZGU=';

        $BODY['domain_name']['type'] = 'domain';
        $BODY['domain_name']['isrequired'] = true;
        $BODY['domain_name']['example'] = 'dhru.com';

        $BODY['phone']['type'] = 'phone';
        $BODY['phone']['isrequired'] = true;
        $BODY['phone']['example'] = '9090909090';

        $BODY['color']['type'] = 'colorcode';
        $BODY['color']['isrequired'] = true;
        $BODY['color']['example'] = '#00FF00';

        $BODY['date']['type'] = 'yyyy-mm-dd';
        $BODY['date']['isrequired'] = true;
        $BODY['date']['example'] = '2021-12-30';

        $BODY['time']['type'] = 'time';
        $BODY['time']['isrequired'] = true;
        $BODY['time']['example'] = '11:11 AM';


        $BODY['email']['type'] = 'email';
        $BODY['email']['isrequired'] = true;
        $BODY['email']['example'] = 'test@dhru.com';



        $BODY['jsonobj']['type'] = 'jsonobj';
        $BODY['jsonobj']['isrequired'] = true;
        $BODY['jsonobj']['example'] = array("name" => "test user");

        {
            /* User Obj */
            $USER['firstname']['type'] = 'string';
            $USER['firstname']['isrequired'] = true;
            $USER['firstname']['example'] = 'John';

            $USER['lastname']['type'] = 'string';
            $USER['lastname']['isrequired'] = true;
            $USER['lastname']['example'] = 'Doe';

            {
                /* Address Obj*/
                $USERADDR['city']['type'] = 'string';
                $USERADDR['city']['isrequired'] = true;
                $USERADDR['city']['example'] = 'Anand';

                $USERADDR['state']['type'] = 'string';
                $USERADDR['state']['isrequired'] = true;
                $USERADDR['state']['isrequired'] = true;
                $USERADDR['state']['example'] = 'Gujarat';

                $USERADDR['pin']['type'] = 'regx';
                $USERADDR['pin']['regx'] = '/^[0-9]+$/';
                $USERADDR['pin']['isrequired'] = true;
                $USERADDR['pin']['example'] = '12345';

            }
            /* Address Obj*/
            $USER['address']['type'] = 'list'; /*  obj,list */
            $USER['address']['isrequired'] = true;
            $USER['address']['schema'] = $USERADDR;
        }
        /* User Obj */
        $BODY['usersdetails']['type'] = 'obj'; /*  obj,list */
        $BODY['usersdetails']['isrequired'] = true;
        $BODY['usersdetails']['schema'] = $USER;

        /* BODY,querystring BASE */
        $RETURN['body']['type'] = 'obj'; /*  obj,list */
        $RETURN['body']['isrequired'] = true;
        $RETURN['body']['schema'] = $BODY;


        /* Postman Events */
        /*
        $_set_token['listen'] = 'test';
        $_set_token['script']['exec'][0] = "pm.environment.set(\"AUTH_TOKEN\", pm.response.json().token);";
        $_set_token['script']['type'] = "text/javascript";
        $RETURN['postmanevent'][] = $_set_token;
        */

        return $RETURN;
    }

}
