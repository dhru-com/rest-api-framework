<?php
/**
 * DHRU APi CORE - A PHP Framework For REST APIs.
 *
 * @author Bhavanesh V. <info@dhru.com>
 * @copyright 2009-2020 DHRU CLOUD Pvt. Ltd.
 */




define('APICORE_START', microtime(true));
define('ENDPOINT_BASE_DIR', 'client');
define('ENDPOINT_VERSION', 'v1');


//require_once __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../src/v1/bootstrap.php';
