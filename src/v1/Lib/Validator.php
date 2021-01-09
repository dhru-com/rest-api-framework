<?php
namespace Dhru\Lib;

class Validator{


    static function ip($data){
        return filter_var($data, FILTER_VALIDATE_IP);
    }



}