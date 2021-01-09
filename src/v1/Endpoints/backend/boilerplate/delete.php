<?php

namespace Dhru\Endpoints\backend\boilerplate;

use Dhru\Exceptions\SuccessException;

class delete extends Boilerplate
{

    function __construct($Base)
    {
        parent::__construct($Base);

        /*
         * $this->token  login user details .
         */

        throw new SuccessException("Unique Item Delete Process executed successfully");
    }


    static function schema()
    {
        /* API CONFIG */
        $RETURN['name'] = 'Delete Item';
        $RETURN['description'] = 'Delete Item By ID';
        $RETURN['loginreq'] = true;

        $BODY['clientid']['type'] = 'int';
        $BODY['clientid']['isrequired'] = true;
        $BODY['clientid']['example'] = 12;

        $RETURN['body']['type'] = 'obj'; /*  obj,list */
        $RETURN['body']['isrequired'] = true;
        $RETURN['body']['schema'] = $BODY;

        return $RETURN;
    }
}
