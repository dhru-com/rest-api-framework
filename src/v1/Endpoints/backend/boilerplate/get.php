<?php
namespace Dhru\Endpoints\backend\boilerplate;

use Dhru\Lib\StaticData;
use Dhru\Exceptions\SuccessException;

class get extends Boilerplate
{

    function __construct($Base)
    {
        parent::__construct($Base);

        throw new SuccessException("Boilerplate GET Loaded" , (array) $this->token);


    }



    static function schema()
    {
        /* API CONFIG */
        $RETURN['name'] = 'Get Boilerplate';
        $RETURN['description'] = 'Get data from Boilerplate';
        $RETURN['loginreq'] = false;

        $QUERY['id']['type'] = 'uuid';
        $QUERY['id']['isrequired'] = true;
        $QUERY['id']['example'] = '29bbc125-914b-4ad4-8140-ff0c01bcf326';
        $QUERY['id']['description'] = 'user id UUID';

        $QUERY['id2']['type'] = 'uuid';
        $QUERY['id2']['isrequired'] = false;
        $QUERY['id2']['example'] = '29bbc125-914b-4ad4-8140-ff0c01bcf326';
        $QUERY['id2']['description'] = 'Optional para';


        /* BODY,querystring BASE */
        $RETURN['querystring']['type'] = 'obj'; /*  obj,list */
        $RETURN['querystring']['isrequired'] = true;
        $RETURN['querystring']['schema'] = $QUERY;


        return $RETURN;
    }
}
