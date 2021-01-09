<?php

namespace Dhru\Lib;

use Dhru\Exceptions\ErrorException;
use Dhru\Exceptions\SuccessException;
use Dhru\Exceptions\ValidationExpression;

class Comm
{
    public static function initCurl(string $url, array $postfields = [], array $curlopts = [], string $userpwd = "", string $postjson = "")
    {
        $curlopts['CURLOPT_TIMEOUT'] = 300;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($userpwd) {
            curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $userpwd);
        }
        if ($postfields) {
            $fieldstring = urldecode(http_build_query($postfields));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
        }elseif ($postjson) {
            if (is_array($postjson)) {
                $postjson = json_encode($postjson);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postjson);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $curlopts['CURLOPT_TIMEOUT']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if ($curlopts['HEADER']) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlopts['HEADER']);
        }
        $retval = curl_exec($ch);
        if (curl_errno($ch)) {
            $retval = 'CURL Error: ' . curl_errno($ch) . ' - ' . curl_error($ch);
        }
        curl_close($ch);
        return $retval;
    }

    public static function encrypt_RSA($plainData, $privatePEMKey)
    {
        $encrypted = '';
        $plainData = str_split($plainData, 200);
        foreach($plainData as $chunk)
        {
            $partialEncrypted = '';

            //using for example OPENSSL_PKCS1_PADDING as padding
            $encryptionOk = openssl_private_encrypt($chunk, $partialEncrypted, $privatePEMKey, OPENSSL_PKCS1_PADDING);

            if($encryptionOk === false){return false;}//also you can return and error. If too big this will be false
            $encrypted .= $partialEncrypted;
        }
        return base64_encode($encrypted);//encoding the whole binary String as MIME base 64
    }

    public static function decrypt_RSA($publicPEMKey, $data)
    {
        $decrypted = '';
        //decode must be done before spliting for getting the binary String
        $data = str_split(base64_decode($data),256);
        foreach($data as $chunk)
        {
            $partial = '';
            $decryptionOK = openssl_public_decrypt($chunk, $partial, $publicPEMKey, OPENSSL_PKCS1_PADDING);

            if($decryptionOK === false){return false;}//here also processed errors in decryption. If too big this will be false
            $decrypted .= $partial;
        }
        return $decrypted;
    }


    public static function encryptPassword($pass)
    {
        return  md5(md5($pass) . "9486");
    }

    public static function parseQuery($query){
    //    $query=base64_decode($query);
        $query = self::json_decode_(trim(html_entity_decode(urldecode(trim($query)))),true);
        return $query;
    }

    public static function json_decode_($strinjson){
        $parameters = \json_decode($strinjson, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new ValidationExpression("invalid json".$strinjson);
        }
        return $parameters;
    }

    public static function parseSchema($schema){

        foreach($schema['schema'] as $key=>$schemadetail){
            if(isset($schemadetail['extra'])){
                $schema['schema'][$key]['extra']=explode(",",$schemadetail['extra']);
            }
        }
        return $schema;
    }


    public static function getRemoteIp()
    {

        if (isset($_SERVER['HTTP_CLIENT_IP']) && Validator::ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_array = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if (Validator::ip(trim($ip_array[0]))) {
                return trim($ip_array[0]);
            }
        }

        if (isset($_SERVER['HTTP_X_FORWARDED']) && Validator::ip($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && Validator::ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_FORWARDED_FOR']) && Validator::ip($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_FORWARDED']) && Validator::ip($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function contains($string, $find)
    {
        return stripos($string, $find) !== false;
    }


    public static function UUID()
    {

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }


    public static function getAppVersion()
    {
        if (ENV == 'dev') {
            return '1.1.0.' . time();
        }
        $_v = VERSION_MAJOR . '.' . VERSION_MINOR . '.';
        if (DEVELOPMENT_STAGE == 'alpha') {
            $_v .= 0;
        }
        elseif (DEVELOPMENT_STAGE == 'beta') {
            $_v .= 1;
        }
        elseif (DEVELOPMENT_STAGE == 'rc') {
            $_v .= 2;
        }
        else {
            $_v .= 3;
        }
        return $_v .= '.' . VERSION_REVISION;
    }


}
