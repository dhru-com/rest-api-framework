<?php

namespace Dhru\Exceptions;


class UncaughtException extends ErrorException
{
    static function UncaughtException($e){
        $_info = [];
        if (ENV === 'dev') {
            $_info['file'] = $e->getFile();
            $_info['line'] = $e->getline();
        }
        throw new ErrorException( $e->getMessage(),100 , [] , $_info);
    }
}