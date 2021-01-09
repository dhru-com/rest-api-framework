<?php

/**
 * APi Version specific bootstrap
 *
 * @author   Bhavanesh V. <info@dhru.com>
 */


defined("ENDPOINT_BASE_DIR") or die ("ENDPOINT_BASE_DIR not defined");
defined("ENDPOINT_VERSION") or die ("ENDPOINT_VERSION not defined");



define('DHRU_ACCESS', true);
define('ROOTDIR', __DIR__);



/**
 * App configuration
 */
require __DIR__.'/app.config.php';



/**
 * Register Project's specific Auto Loader
 */
require __DIR__ . '/autoload.php';


$app = new Dhru\Lib\Base();

exit();