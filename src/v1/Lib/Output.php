<?php

namespace Dhru\Lib;

use Dhru\Lib\Comm;

class Output
{
    public $out = [];
    public $httpstatus = 200;

    function __set($name, $value)
    {
        $this->out[$name] = $value;
    }

    function Done()
    {

        if (isset($this->out['data']['token'])) {
            $this->out['token'] = $this->out['data']['token'];
            unset($this->out['data']['token']);
        }
        if (isset($this->out['data']['license_token'])) {
            $this->out['license_token'] = $this->out['data']['license_token'];
            unset($this->out['data']['license_token']);
        }


        if (ENV == 'dev') {
            $this->out['details']['dev']['php_version'] = phpversion();
            $this->out['details']['dev']['server'] = $_SERVER['SERVER_SOFTWARE'];
            if (defined('ENDPOINT_BASE_DIR')) {
                $this->out['details']['dev']['api_base'] = ENDPOINT_BASE_DIR;
            }
            $this->out['details']['dev']['version'] = Comm::getAppVersion();
        }
        if (defined('DEVELOPMENT_STAGE')) {
            $this->out['details']['build'] = DEVELOPMENT_STAGE;
        }

        $this->out['details']['application'] = (APPNAME);
        $this->out['details']['request_time'] = microtime(true) - APICORE_START;
        $this->out['details']['reference_id'] = Comm::UUID();

        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Origin: *');


        header('Content-Type: application/json; charset=utf-8');
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        header('X-Powered-By: ' . APPNAME);


        header_remove('pragma');
        header_remove('server');


        header('Status: ' . $this->httpstatus, TRUE, $this->httpstatus);

        unset($this->out['httpstatus']);

        if (defined('BUILDDOC')) {
            exit(json_encode($this->out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        exit(json_encode($this->out, JSON_UNESCAPED_UNICODE));
    }
}

