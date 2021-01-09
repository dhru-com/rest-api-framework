<?php

namespace Dhru\Exceptions;


define('E_FATAL', E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR |
    E_COMPILE_ERROR | E_RECOVERABLE_ERROR);

class RegisterShutdownException extends ErrorException
{
    static function RegisterShutdownException()
    {
        $error = error_get_last();
        if ($error && ($error['type'] & E_FATAL)) {

            $_message = '';
            $_info = [];

            $message = '' . $error['type'] . ': ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'] . ' ';

            if (($error['type'] & E_FATAL) && ENV === 'production') {
                $_message = "500 Internal Server Error";
                $_info['httpstatus'] = 500;
            }
            else {
                $_message = $message;
                $_info = $error;
            }


            throw new ErrorException($_message, 99, [], $_info);

        }

    }
}