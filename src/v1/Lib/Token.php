<?php

namespace Dhru\Lib;

class Token
{

    static function generate(string $tokenId, string $stream, string $userId, array $data, string $fingerprint = '',$expiry=0)
    {

        $issuerClaim = APPNAME;
        $audienceClaim = $issuerClaim;
        $issuedatClaim = time(); // issued at
        $notbeforeClaim = $issuedatClaim + 0; // can use after

        if($expiry>0) {
            $expireClaim = $issuedatClaim + (86400*$expiry);//days to sec
        }else
        if (TOKEN_EXPIRE && (int)TOKEN_EXPIRE > 0) {
            $expireClaim = $issuedatClaim + TOKEN_EXPIRE; // in sec.
        }
        else {
            $expireClaim = $issuedatClaim + 360000; // 10min
        }
        // https://www.iana.org/assignments/jwt/jwt.xhtml


        $token = array(
            "jit" => $tokenId,
            "sub" => $userId,
            "iss" => $issuerClaim,
            "aud" => $stream,
            "iat" => $issuedatClaim,
            "nbf" => $notbeforeClaim,
            "exp" => $expireClaim,
            "enp" => md5(ENDPOINT_BASE_DIR),
            "data" => $data
        );

        if ($fingerprint!=='') {
            $token['fingerprint'] = md5($fingerprint);
            //$token['ip'] = (\Dhru\Lib\Comm::getRemoteIp());
        }
        else if (TOKEN_IP_VALIDATION && $stream == 'web') {
            $token['fingerprint'] = md5(\Dhru\Lib\Comm::getRemoteIp());
            $token['ip'] = (\Dhru\Lib\Comm::getRemoteIp());
        }
        global $tokendata;
        $tokendata=$token;
        return self::encode($token);

    }

    static function refresh($token)
    {
        try {

            $decoded = \Dhru\Lib\Comm::decode($token);
            return $token;
            //TODO: do something if exception is not fired
        } catch (\ExpiredException $e) {
            JWT::$leeway = 720000;

            $decoded = (array)\Dhru\Lib\Comm::decode($token);
            // TODO: test if token is blacklisted
            $decoded['iat'] = time();
            if (TOKEN_EXPIRE && (int)TOKEN_EXPIRE > 0) {
                $decoded['exp'] = time() + TOKEN_EXPIRE;
            }
            else {
                $decoded['exp'] = time() + 36000;
            }
            $decoded['refresh'] = true;
            return self::encode($decoded);
        }
    }


    public static function encode($payload, $key = "", $alg = 'HS256', $keyId = null, $head = null)
    {
        global $privateKey, $publicKey;
        if ($privateKey && $publicKey) {
            return $jwt = \Dhru\Lib\Jwt::encode($payload, $privateKey, 'RS256');
        }
        return \Dhru\Lib\Jwt::encode($payload, $key, $alg, $keyId, $head);
    }

    public static function decode($jwt, $key = "", array $allowed_algs = array())
    {
        global $privateKey, $publicKey;
        if ($privateKey && $publicKey) {
            return \Dhru\Lib\Jwt::decode($jwt, $publicKey, array('RS256'));
        }
        return \Dhru\Lib\Jwt::decode($jwt, $key, $allowed_algs);
    }
}
