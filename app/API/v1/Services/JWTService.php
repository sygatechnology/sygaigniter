<?php namespace App\API\v1\Services;

use \Firebase\JWT\JWT;

class JWTService
{

    private static $lastError = null;

    /**
     * Encode user entity to JWT
     *
     * @param User  $user    The user
     *
     * @return array Details of a signed JWT
     *
     */
    public static function encode($userData)
    {
        $JWTConfig = new \Syga\Config\JWT();
        $time = time();
        $iss =  $JWTConfig->getIssuer();
        $aud =  $JWTConfig->getAudience();
        $nbf =  $JWTConfig->getNbfTime();
        $exp =  $JWTConfig->getExpireTime();
        $payload = array(
            "iss"       => $iss,
            "aud"       => $aud,
            "iat"       => $time,
            "nbf"       => $time + $nbf,
            "exp"       => $time + $exp,
            "user"      => $userData
        );
        $jwt = JWT::encode($payload, $JWTConfig->getKey(), 'HS256');
        return array(
            'jwt' => $jwt,
            'exp' => $payload['exp']
        );
    }

    /**
     * Generate JWT for a user
     *
     * @param string  $user    The user
     *
     * @return string A signed JWT
     *
     */
    public static function decode($jwt)
    {
        $JWTConfig = new \Syga\Config\JWT();
        try{
            return JWT::decode($jwt, $JWTConfig->getKey(), array('HS256'));
        } catch(\Exception $e){
            static::setLastError($e->getMessage());
            return false;
        }
    }

    public static function refresh($payload){
        return static::encode($payload->user);
    }

    public static function publicTokenExists($publicToken){
        $JWTConfig = new \Syga\Config\JWT();
        return in_array($publicToken, $JWTConfig->getPublicTokens());
    }

    public static function setLastError($error){
        static::$lastError = $error;
    }

    public static function getLastError(){
        return static::$lastError;
    }
}
