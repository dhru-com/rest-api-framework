<?php
declare(strict_types=1);
defined("DHRU_ACCESS") or die (header('Status: ' . 401, TRUE, 401) . '401 Unauthorized');

error_reporting(0);
ini_set('display_errors', 'Off');

if(ENV=='dev'){
    //error_reporting(E_ALL);
    //ini_set('display_errors', 'On');
}
if(defined('BUILDDOC')){
    error_reporting(E_ERROR);
    ini_set('display_errors', 'On');

}
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Dhru\\';

    // base directory for the namespace prefix

    // $base_dir = __DIR__ . '/' . ENDPOINT_BASE . '/';
    $base_dir = __DIR__ . '/' ;


    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // echo "no, move to the next registered autoloader";
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);


    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {


            require $file;



    }
    else {
        //  exit($file);
    }
});

register_shutdown_function('Dhru\Exceptions\RegisterShutdownException::RegisterShutdownException');

set_exception_handler('Dhru\Exceptions\UncaughtException::UncaughtException');

