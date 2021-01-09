<?php
/**
 * DHRU APi CORE - A PHP Framework For REST APIs.
 *
 * @author Bhavanesh V. <info@dhru.com>
 * @copyright 2009-2020 DHRU CLOUD Pvt. Ltd.
 */


define('APICORE_START', microtime(true));
define('ENDPOINT_VERSION', 'v1'); //this will connect this dir (src/v1/)
define('ENDPOINT_BASE_DIR', 'backend'); //this will connect this dir (src/v1/Endpoints/backend)

/*
 * To enable composer dependency
 */
//require_once __DIR__ . '/../../../vendor/autoload.php';



$app = require_once __DIR__ . '/../../../src/'.ENDPOINT_VERSION.'/bootstrap.php';
