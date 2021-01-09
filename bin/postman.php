<?php
define('DHRU_ACCESS', true);

define('BUILDDOC', true);
define('APICORE_START', microtime(true));

error_reporting(E_ALL);
ini_set('display_errors', true);
require __DIR__ . '/../src/v1/app.config.php';
echo "\n\e[1;34m======================================\nDHRU API CORE - POSTMAN SCHEMA BUILDER \n======================================\n \e[0m \n";
echo 'Building PostMan using PHP version: ' . phpversion() . '
Collecting information......
';

if (!defined('POSTMAN_API_KEY')) {
    echo "ERROR: POSTMAN_API_KEY is not defined in app.config.php \n";
    exit();
}


$COLLECTION = $SUBMIT = [];
/*
 *  COLLECTION INFO
 */
$COLLECTION['info']['name'] = APPNAME;
$COLLECTION['info']['description'] = APPNAME;
//$COLLECTION['info']['_postman_id'] = "174bad7c-07e3-45f3-914f-36cf84e5586f";
$COLLECTION['info']['schema'] = "https://schema.getpostman.com/json/collection/v2.1.0/collection.json";

echo "Postman schema version: v2.1.0 \n";
echo "App name: ".APPNAME . "\n";
echo "App version: ". VERSION_MAJOR .'.'.VERSION_MINOR .'.'.VERSION_REVISION   . "\n";
echo "Development URL: ".API_URL_DEVELOPMENT ."\n";
echo "BETA Testing URL: ".API_URL_BETA ."\n";
echo "Production URL: ".API_URL_PRODUCTION ."\n";

/*
 * API Version as Folders
 */

$_src_root = __DIR__ . '/../src/';

foreach (glob($_src_root . '*', GLOB_ONLYDIR) as $dir) {
    $VERSION = [];
    $version = basename($dir);
    $VERSION['name'] = $version;
    $_version_root = $_src_root . $version . '/';
    require $_version_root . 'autoload.php';
    $_version_root .= 'Endpoints/';

    echo "End Points: \n" ;
    foreach (glob("$_version_root/*", GLOB_ONLYDIR) as $endpountbasedir) {
        $ENDBASE = [];
        $_endpoint_base = basename($endpountbasedir);
        echo '  ├───'.$_endpoint_base . "\n";
        $_TAB = '  │   ';
        $ENDBASE['name'] = $_endpoint_base;

        if ($_endpoint_base !== 'admin') {
           // continue;
        }

        $_endpoint_base_dir = $_version_root . $_endpoint_base . '';


        /*
         * Endpoints
         */
        foreach (glob("$_endpoint_base_dir/*", GLOB_ONLYDIR) as $endpountdir) {
            $ENDPOINT = [];
            $endpoint = basename($endpountdir);
            $ENDPOINT['name'] = $endpoint;

            if ($endpoint !== 'login') {
              //  continue;
            }

            $_endpoint_dir = "$_endpoint_base_dir/$endpoint";


            /*
             * Methods
             */
            $methods = preg_grep('(get.|post.|put.|delete.)', scandir($_endpoint_dir));

            if (is_array($methods) and count($methods) > 0) {
                echo $_TAB . '├──' . $endpoint . " [" ;
            }else{
                echo $_TAB . '├──'.$endpoint . " [ NO METHODS FOUND ";
            }

            foreach ($methods as $method) {
                $method = str_replace(".php", "", $method);

                if ($method === 'get') {
                    echo "\e[1;32m GET \e[0m";
                }
                if ($method === 'post') {
                    echo "\e[1;33m POST \e[0m";
                }
                if ($method === 'put') {
                    echo "\e[1;34m PUT \e[0m";
                }
                if ($method === 'delete') {
                    echo "\e[1;31m DELETE \e[0m";
                }



               // echo $_TAB . '│  ├──'.$method . "\n";
                /*
                 * Get config of method
                 */
                $methodConfig = 'Dhru\Endpoints' . '\\' . $_endpoint_base . '\\' . $endpoint . '\\' . $method;


                if (method_exists($methodConfig, 'schema')) {


                    $methodConfig = $methodConfig::schema();


                } else {
                    $methodConfig = "";
                    exit("$endpoint [$method] - schema not found" . ' 
');
                }


                $METHOD = [];
                $METHOD['name'] = $methodConfig['name'] ? $methodConfig['name'] : ucfirst($method) . " " . ucfirst($endpoint);
                $METHOD['request']['url']['raw'] = "{{API_URL}}/$_endpoint_base/".PUBLIC_API_FOLDER_NAME."/" . $VERSION['name'] . "/" . $ENDPOINT['name'] . "";
                $METHOD['request']['url']['host'][] = "{{API_URL}}";
                $METHOD['request']['url']['path'][] = $_endpoint_base;
                $METHOD['request']['url']['path'][] = PUBLIC_API_FOLDER_NAME;
                $METHOD['request']['url']['path'][] = $VERSION['name'];
                $METHOD['request']['url']['path'][] = $ENDPOINT['name'];
                $METHOD['request']['method'] = strtoupper($method);

                if ($methodConfig['loginreq']) {
                    $_auth['type'] = 'bearer';
                    {
                        $_bearer['key'] = 'token';
                        $_bearer['value'] = '{{AUTH_TOKEN}}';
                        $_bearer['type'] = 'string';
                        $_auth['bearer'][] = $_bearer;
                    }

                    $METHOD['request']['auth'] = $_auth;
                    unset($_auth, $_bearer);
                }


                if ($methodConfig['captcha_req']) {
                    $methodConfig['body']['schema']['g-recaptcha-response']['type'] = 'string';
                    $methodConfig['body']['schema']['g-recaptcha-response']['isrequired'] = true;
                    $methodConfig['body']['schema']['g-recaptcha-response']['example'] = 'g-recaptcha-response-gjgjh-kjkljkl-mjbkjhkj-bbkj';
                }


                if ($methodConfig['body']) {
                    /* Req Headers */
                    $_header = [];
                    $_header['key'] = "Content-Type";
                    $_header['value'] = "application/json";
                    $METHOD['request']['header'][] = $_header;
                    /* Example */
                    $METHOD['request']['body']['mode'] = 'raw';
                    $METHOD['request']['body']['raw'] = json_encode(buildExample($methodConfig['body']));
                    $METHOD['request']['description'] = $methodConfig['description'];
                }
                if ( is_array($methodConfig['querystring']['schema'])  && count($methodConfig['querystring']['schema']) > 0) {
                    $_q =  $q_x =[];
                    foreach ($methodConfig['querystring']['schema'] as $k => $v) {
                        $_q['key'] = $k;
                        $_q['value'] = "$v[example]";
                        $_q['description'] = utf8_encode($v['description']);
                        $_q['disabled'] = $v['isrequired'] ? false : true;
                        $q_x[] = $_q;
                    }
                    $METHOD['request']['url']['query'] = $q_x;
                   // print_r($METHOD['request']);
                   // exit();

                }


                if (is_array($methodConfig['postmanevent'])) {
                    $METHOD['event'] = $methodConfig['postmanevent'];
                }


                /* --------  */
                $ENDPOINT['item'][] = $METHOD;


                //print_r($ENDPOINT);
            }

            echo "] \n";


            /* --------  */
            if (count($ENDPOINT['item']) > 0) {
                $ENDBASE['item'][] = $ENDPOINT;
            }


        }

        /* --------  */
        if (count($ENDBASE['item']) > 0) {
            $VERSION['item'][] = $ENDBASE;
        }


    }

    /* --------  */
    if (count($VERSION['item']) > 0) {
        $COLLECTION['item'][] = $VERSION;
    }


}


$SUBMIT['collection'] = $COLLECTION;

//echo json_encode($SUBMIT);
//exit();

echo "Syncing All Endpoints apis....";
$url = "https://api.getpostman.com/collections/" . COLLECTION_UID;
$response = json_decode(initAPi($url, $SUBMIT), true);
if ($response['error']) {
    echo "ERROR: " . $response['error']['message'] . "\n";
    // print_r($response);
    exit();
} else {
    echo "OK \n";
}


if (ENV_UID_PRODUCTION) {
    echo "Syncing PRODUCTION Environment....";
    $_values = $_environment = [];
    $_values['API_URL'] = API_URL_PRODUCTION;

    foreach ($_values as $k => $v) {
        $__values['key'] = $k;
        $__values['value'] = $v;
        $_environment['values'][] = $__values;
    }
    $_environment['name'] = APPNAME . " - PRODUCTION";
    $SUBMIT = [];
    $SUBMIT['environment'] = $_environment;

    $url = "https://api.getpostman.com/environments/" . ENV_UID_PRODUCTION;
    $response = json_decode(initAPi($url, $SUBMIT), true);
    if ($response['error']) {
        echo "ERROR: " . $response['error']['message'] . "\n";
        //   print_r($response);
        exit();
    } else {
        echo "Ok [" . APPNAME . " - PRODUCTION" . "]\n";
    }
}

if (ENV_UID_BETA) {
    echo "Syncing BETA Environment....";
    $_values = $_environment = [];
    $_values['API_URL'] = API_URL_BETA;

    foreach ($_values as $k => $v) {
        $__values['key'] = $k;
        $__values['value'] = $v;
        $_environment['values'][] = $__values;
    }
    $_environment['name'] = APPNAME . " - BETA";
    $SUBMIT = [];
    $SUBMIT['environment'] = $_environment;

    $url = "https://api.getpostman.com/environments/" . ENV_UID_BETA;
    $response = json_decode(initAPi($url, $SUBMIT), true);
    if ($response['error']) {
        echo "ERROR: " . $response['error']['message'] . "\n";
        //   print_r($response);
        exit();
    } else {
        echo "Ok [" . APPNAME . " - BETA" . "]\n";
    }
}

if (ENV_UID_DEVELOPMENT) {
    echo "Syncing DEVELOPMENT Local Environment....";
    $_values = $_environment = [];
    $_values['API_URL'] = API_URL_DEVELOPMENT;

    foreach ($_values as $k => $v) {
        $__values['key'] = $k;
        $__values['value'] = $v;
        $_environment['values'][] = $__values;
    }
    $_environment['name'] = APPNAME . " - DEVELOPMENT";
    $SUBMIT = [];
    $SUBMIT['environment'] = $_environment;

    $url = "https://api.getpostman.com/environments/" . ENV_UID_DEVELOPMENT;
    $response = json_decode(initAPi($url, $SUBMIT), true);
    if ($response['error']) {
        echo "ERROR: " . $response['error']['message'] . "\n";
        //   print_r($response);
        exit();
    } else {
        echo "Ok [" . APPNAME . " - DEVELOPMENT" . "]\n";
    }
}





/*
 *      $BODY['username']['type']= 'string';
        $BODY['username']['isrequired']= true;
        $BODY['username']['example']= 'UserName';

        $BODY['password']['type']= 'string';
        $BODY['password']['isrequired']= true;
        $BODY['password']['example']= 'UserName';

 */


function initAPi($url, $data)
{
    $data = json_encode($data);
    $ch = curl_init();
    $headers =
        ['Content-Type: application/json'
            , 'Content-Length: ' . strlen($data)
            , 'x-api-key:' . POSTMAN_API_KEY];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $exec =  curl_exec($ch);
    curl_close($ch);
    return $exec;
}


function buildExample($data)
{

    if ($data['type'] == 'obj') {
        $_OUT = loop1($data['schema']);
    } elseif ($data['type'] == 'list') {
        $_OUT[] = loop1($data['schema']);
    }

    return $_OUT;
}

function loop1($data, $r = false)
{
    foreach ($data as $k => $v) {

        if ($v['type'] == 'obj') {
            $_OUT[$k] = loop1($v['schema'], true);
        } elseif ($v['type'] == 'list') {
            $_OUT[$k][] = loop1($v['schema'], true);
        } else if ($v['example']) {
            $_OUT[$k] = $v['example'];
        }

        /* foreach ($v as $k1 => $v1){
             if($k1 == 'example'){
                 $_OUT[$k] = $v1;
             }
         }*/
    }

    /*    if(!$r) {
            print_r($_OUT);
            exit();
        }*/


    return $_OUT;
}



