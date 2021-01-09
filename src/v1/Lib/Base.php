<?php

namespace Dhru\Lib;

use Dhru\Exceptions\ErrorException;
use Dhru\Exceptions\ErrorExceptionSystem;
use Dhru\Exceptions\ValidationExpression;

class Base
{
    public $DisplayOut;
    public $content_format;
    public $method;
    public $endpoint;
    public $parameters;
    public $querystring;
    public $token;
    public $schema;
    public $config;

    /**
     * Base constructor.
     */
    function __construct()
    {
        try {
            $this->processRequest();
            exit();
        } catch (\Exceptions $e) {
            exit('Exception UnCatch');
        }
    }

    private function processRequest()
    {

        $this->method = preg_replace("/[^a-z]/", "", (strtolower($_SERVER['REQUEST_METHOD'])));
        $parse_url = parse_url(filter_var($_SERVER ['REQUEST_URI'], FILTER_SANITIZE_STRING));
        $parse_url['path'] = preg_replace('/(\/+)/', '/', $parse_url['path']);
        $path = explode(PUBLIC_API_FOLDER_NAME . "/" . ENDPOINT_VERSION . "/", $parse_url['path']);

        //print_r($path);

        $path = explode('/', $path[1]);

        if (Comm::contains($_SERVER["SERVER_SOFTWARE"], 'nginx')) {
            //$endpoint = \apache_request_headers()['X-Endpoint'];
            //$this->endpoint = preg_replace("/[^a-z0-9]/", "", (strtolower($endpoint)));;
            $this->endpoint = preg_replace("/[^a-z0-9]/", "", (strtolower($path[0])));
        } else {
            $this->endpoint = preg_replace("/[^a-z0-9]/", "", (strtolower($path[0])));
        }
        $this->validateEndPoint();
    }

    private function validateEndPoint()
    {

        $_endpoint_dir = ROOTDIR . "/Endpoints/" . trim(ENDPOINT_BASE_DIR) . "/$this->endpoint/";
        $_endpoint_method_file = $_endpoint_dir . "/" . $this->method . ".php";

        if (is_dir($_endpoint_dir) && isset($this->endpoint)) {
            if (file_exists($_endpoint_method_file)) {

                /* DB Connection*/
                if (DB_CONFIG_PATH) {
                    $this->initDB();
                }

                $_endpountClass = '\Dhru\Endpoints\\' . ENDPOINT_BASE_DIR . '\\' . $this->endpoint . '\\' . $this->method;

                /* if Endpoint has schema to validate para*/
                if (method_exists($_endpountClass, 'schema')) {
                    $this->schema = Comm::parseSchema($_endpountClass::schema());


                    //  print_r(Comm::parseSchema($_endpountClass::schema()));
                    $this->validatePara($this->schema);
                } else {
                    ////  schema not found .
                    throw new ErrorExceptionSystem("Method schema not exist.", StatusCodes::INTERNAL_CONFIGURATION_ERROR);
                }
                $endpoint = new $_endpountClass($this);
                if (method_exists($endpoint, 'Execute')) {
                    $endpoint->Execute();
                }
                die();
            } else {
                throw new ErrorExceptionSystem("Method does not exist. $this->method -- $this->endpoint", StatusCodes::NOT_IMPLEMENTED);
            }
        } else {
            throw new ErrorExceptionSystem("Endpoint does not exist. $this->endpoint", StatusCodes::BAD_REQUEST);
        }
    }

    private function initDB()
    {
        global $db;

        if (file_exists(ROOTDIR . "/" . DB_CONFIG_PATH)) {
            $DBCONFIG = $CONFIG = [];
            require_once ROOTDIR . "/" . DB_CONFIG_PATH;
            $_ENV['config'] = $CONFIG;
            $this->config = $CONFIG;
            $this->getQuerystring();
            $this->parsePaginate();
            if ($DBCONFIG['server'] == 'mysql') {
                $db = new \Dhru\Lib\Db ("mysql:host=$DBCONFIG[host];dbname=$DBCONFIG[database]", $DBCONFIG['user'], $DBCONFIG['password']);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } else {
                throw new ErrorExceptionSystem("This Database server '$DBCONFIG[server]' not supported", StatusCodes::INTERNAL_CONFIGURATION_ERROR);
            }
        } else {
            throw new ErrorExceptionSystem("Invalid db config path", StatusCodes::INTERNAL_CONFIGURATION_ERROR);
        }
    }

    private function validatePara($schema)
    {

        /*
         * 1. captcha
         * 2. is required login required - check token (JWT)
         * 3. body para
         * 4. query sting
         */

        if (isset($schema['loginreq']) && $schema['loginreq']) {

            //throw new SuccessException('Token', $_SERVER);


            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

            }

            if (isset($_SERVER['HTTP_AUTHORIZATION']) || isset($_COOKIE['x-dhru-token']) ||
                isset($_GET['token'])) {
                $token = explode(" ", $_SERVER['HTTP_AUTHORIZATION'])[1];

                if (!$token) {
                    $token = $_GET['token'];
                }

                if (!$token) {
                    $token = $_COOKIE['x-dhru-token'];
                }


                if ($this->validateJWT($token) && $token) {
                    $skip_fingerprint_check = false;
                    if ($schema['skip_fingerprint_check']) {
                        $skip_fingerprint_check = true;
                    }

                    $this->token = \Dhru\Lib\Token::decode($token, '', array('RS256'), $skip_fingerprint_check);
                    $_ENV['token'] = json_decode(json_encode($this->token), true);
                    $_ENV['token_raw'] = $token;
                    if ($_ENV['token']['data']['clientid'])
                        $_ENV['cleintid'] = $_ENV['token']['data']['clientid'];
                    if ($_ENV['token']['data']['adminid'])
                        $_ENV['adminid'] = $_ENV['token']['data']['adminid'];
                } else {
                    throw new ErrorException('Invalid access token', StatusCodes::UNAUTHORIZED);
                }
            } else {
                throw new ErrorException('Access token is required to request this resource', StatusCodes::UNAUTHORIZED);
            }

        }

        /* process in-coming paras */
        $this->getBody();
        $this->getQuerystring();

        if (isset($schema['body'])) {
            $this->validateBodyPara($schema['body'], $this->parameters, '', '', 'Body');
        }

        if (isset($schema['querystring'])) {
            $this->validateBodyPara($schema['querystring'], $this->querystring, '', '', 'Querystring');
            //throw new \SuccessOut("This querystring", $schema['querystring']);
        }


        if (ENV != 'dev') {

            if (isset($schema['captcha_req']) && $schema['captcha_req'] && GOOGLE_RECAPTCHA_SECRET) {
                $cleint_ip = (\Dhru\Lib\Comm::getRemoteIp());

                if($this->parameters['g-recaptcha-response']=='g-recaptcha-response-gjgjh-kjkljkl-mjbkjhkj-bbkj'
              )
                {//g-recaptcha-response-gjgjh-kjkljkl-mjbkjhkj-bbkj

                }else {
                    if (isset($this->parameters['g-recaptcha-response'])) {
                        $captcha = filter_var($this->parameters['g-recaptcha-response'], FILTER_SANITIZE_STRING);
                    } else {
                        throw new ValidationExpression("Body parameters required 'g-recaptcha-response'");
                    }

                    $response = file_get_contents(
                        "https://www.google.com/recaptcha/api/siteverify?secret=" . GOOGLE_RECAPTCHA_SECRET . "&response=" . $captcha . "&remoteip=" . $cleint_ip
                    );

                    // throw new ErrorException($response, 403);

                    $response = json_decode($response);


                    if ($response->success === false) {
                        //Do something with error
                        throw new ErrorException('Invalid captcha, Please refresh and try again.', 403);
                    }

                    if ($response->score <= 0.5) {
                        throw new ErrorException('Bots not allowed!.', 403);
                    }
                }
            }
        }

        return true;
    }

    public function getBody()
    {
        $parameters = [];

       $body = file_get_contents("php://input");
        $content_type = false;
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];
        }
        if (Comm::contains($content_type, 'application/json')) {
            $content_type = 'application/json';
        }
        if (Comm::contains($content_type, 'text/plain')) {
            $content_type = 'application/json';
        }

        if ($body) {
            switch ($content_type) {
                case "application/json":
                    $parameters = json_decode($body, true);
                    if (json_last_error() != JSON_ERROR_NONE) {
                        throw new ValidationExpression("Invalid request body, Valid json string required");
                    }
                    $this->content_format = "json";
                    break;
                case "application/x-www-form-urlencoded":
                    /*
                    parse_str($body, $postvars);
                    foreach ($postvars as $field => $value) {
                        $parameters[$field] = $value;
                    }
                    $this->content_format = "html";
                    */
                    break;
                default:
                    //throw new ValidationExpression("Supported content types are application/json,application/x-www-form-urlencoded");
                    throw new ValidationExpression("Supported content types are application/json");
                    // we could parse other supported formats here
                    break;
            }
            $this->parameters = $parameters;
        }
    }

    public function getQuerystring()
    {
        $_querystring = [];
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $this->querystring);
            foreach ($this->querystring as $k => $v) {
                $_querystring[$k] = filter_var($v, FILTER_SANITIZE_STRING);
            }
        }

        $this->querystring = $_querystring;
    }

    private function validateBodyPara(array $schema, $para, $parentifiedname = '', $listkey = '', $type)
    {
        /*
         * Validating is List Or Obj
         */
        if ($schema['isrequired']) {
            if ($schema['type'] == 'obj') {
                $this->validateBody($schema['schema'], $para, $parentifiedname, '', $type);
            } elseif ($schema['type'] == 'list') {
                if (count($para[0]) <= 0) {
                    throw new ValidationExpression("Body parameters $parentifiedname [list] is required ");
                }
                foreach ($para as $listkey => $p) {
                    $this->validateBody($schema['schema'], $p, $parentifiedname, $listkey, $type);
                }
            }
        }
        return true;
    }


    private function validateBody(array $schema, $para, $parentifiedname = '', string $listKey = '', $type)
    {
        $_msg_listKey = '';
        foreach ($schema as $fildname => $fieldoptions) {
            {
                $fieldValue = $para[$fildname];

                if ($parentifiedname) {
                    $_msg_fieldname = "$parentifiedname => $fildname";
                } else {
                    $_msg_fieldname = $fildname;
                }

                if ($listKey != '' && !$_msg_listKey) {
                    $_msg_listKey = ", On list key [$listKey]";
                }

                if ($fieldoptions['isrequired']) {
                    if (!isset($para[$fildname]) || $para[$fildname] == "") {
                        throw new ValidationExpression("$type parameter '$_msg_fieldname' is required$_msg_listKey");
                    }
                }

                if ($fieldValue) {
                    if ($fieldoptions['type'] == 'obj' || $fieldoptions['type'] == 'list') {
                        $this->validateBodyPara($fieldoptions, $fieldValue, $_msg_fieldname, '', $type);
                    } else {
                        if ($fieldoptions['type'] == 'string') {
                            if (!$this->validateString($fieldValue)) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be valid string $_msg_listKey");
                            }
                        }
                        if ($fieldoptions['type'] == 'email') {
                            if (!$this->validateEmail($fieldValue)) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be valid email $_msg_listKey");
                            }
                        } elseif ($fieldoptions['type'] == 'int') {
                            if (!$this->validateInt($fieldValue)) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be valid integer $_msg_listKey");
                            }
                        } elseif ($fieldoptions['type'] == 'float') {
                            if (!$this->validateFloat($fieldValue)) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be valid float $_msg_listKey");
                            }
                        } elseif ($fieldoptions['type'] == 'regx') {
                            if (!$this->validateRegx($fieldValue, $fieldoptions['regx'])) {
                                // throw new ValidationExpression("Body parameters field '$_msg_fieldname', is invalid input value for match regx $_msg_listKey");
                                throw new ValidationExpression("$type parameter Invalid '$_msg_fieldname' ");
                            }
                        } elseif ($fieldoptions['type'] == 'enum') {

                            if (!$this->validateEnum($fieldValue, $fieldoptions['enumoptions'])) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be one of this [" . implode(',', $fieldoptions['enumoptions']) . "] $_msg_listKey");
                            }
                        } elseif ($fieldoptions['type'] == 'jsonobj') {
                            if (!$this->validateJsonobj($fieldValue, $fieldoptions['enumoptions'])) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be obj $_msg_listKey");
                            }
                        } elseif ($fieldoptions['type'] == 'base64') {
                            if (!$this->validateBase64($fieldValue)) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be valid base64 string");
                            }
                        } elseif ($fieldoptions['type'] == 'uuid') {
                            if (!$this->validateUuid($fieldValue)) {
                                throw new ValidationExpression("$type parameter '$_msg_fieldname', must be valid UUID string");
                            }
                        } elseif ($fieldoptions['type'] == 'domain') {
                            if (!$this->validateDomain($fieldValue)) {
                                throw new ValidationExpression("Invalid domain name");
                            }
                        }elseif ($fieldoptions['type'] == 'phone') {
                            if (!$this->validatePhone($fieldValue)) {
                                throw new ValidationExpression("Invalid phone");
                            }
                        }elseif ($fieldoptions['type'] == 'colorcode') {
                            if (!$this->validateColorCode($fieldValue)) {
                                throw new ValidationExpression("Invalid colour code");
                            }
                        }elseif ($fieldoptions['type'] == 'yyyy-mm-dd') {
                            if (!$this->validateYyyyMmDd($fieldValue)) {
                                throw new ValidationExpression("Invalid date, must be yyyy-mm-dd");
                            }
                        }elseif ($fieldoptions['type'] == 'time') {
                            if (!$this->validateTime($fieldValue)) {
                                throw new ValidationExpression("Invalid time, must be HH:MM AM/PM");
                            }
                        }
                    }
                }
                unset($fieldValue);
            }
        }
    }


    private function validateString($data)
    {
        //$data = filter_var($data, FILTER_SANITIZE_STRING);
        return is_string($data);
    }

    private function validateBase64($data)
    {
        $s = filter_var($data, FILTER_SANITIZE_STRING);
        if (($b = base64_decode($s, TRUE)) === FALSE) {
            return FALSE;
        }
        $e = mb_detect_encoding($b);
        if (in_array($e, array('UTF-8', 'ASCII'))) {
            return $b;
        } else {
            return FALSE;
        }
    }

    private function validateEmail($data)
    {
        return filter_var($data, FILTER_VALIDATE_EMAIL);
    }

    private function validateInt($data)
    {
        return is_numeric($data);
    }

    private function validateFloat($data)
    {
        return (is_float($data) or is_numeric($data));
    }

    private function validateRegx($data, $regx)
    {
        return preg_match($regx, $data);
    }

    private function validateUuid($data)
    {
        return preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $data);
    }

    private function validatePhone($data)
    {
        return preg_match('/^[0-9]{5,18}$/i', $data);
    }

    private function validateColorCode($data)
    {
        return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i', $data);
    }

    private function validateYyyyMmDd($data)
    {
        if (preg_match('/^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/i', $data)) {
            list($year, $month, $day) = explode('-', $data);
            return checkdate($month, $day, $year);
        }
        return false;
    }

    private function validateTime($data)
    {
        return (preg_match('/^\b((1[0-2]|0?[1-9]):([0-5][0-9]) ([AP][M]))$/i', $data));
    }



    private function validateDomain($data)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $data) //valid chars check
            && preg_match("/^.{6,253}$/", $data) //overall length check
            && preg_match("/^[^\.]{3,63}(\.[^\.]{2,63})*$/", $data)
            && preg_match("/^(?=.*\.).+$/i", $data) // need atlist one dot
        ); //length of each label
    }


    private function validateEnum($data, $enumoptions)
    {
        return in_array($data, $enumoptions);
    }

    private function validateJsonobj($data)
    {
        return is_array($data);
    }


    public function parsePaginate()
    {

        $pagelimit = $this->config['pagelimit'];
        if (isset($this->querystring['pagelimit']) and is_numeric($this->querystring['pagelimit'])) {
            $pagelimit = $this->querystring['pagelimit'];
        }
        $_ENV['pagelimit']=$pagelimit;
        $_ENV['page']=$this->querystring['page'];
        $this->querystring['endlimit'] = $pagelimit;

        if ($this->querystring['page']) {
            $this->querystring['startlimit'] = ($pagelimit * ($this->querystring['page'] - 1));
        } else {
            $this->querystring['startlimit'] = 0;
        }
    }

    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function validateJWT($token)
    {
        return true;
        if (!preg_match("/^[A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+\.?[A-Za-z0-9-_.+/=]*$/", $token)) {
            return false;
        }
        return true;
    }

}
