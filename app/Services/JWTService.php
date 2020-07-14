<?php 

namespace App\Services;

use \Firebase\JWT\JWT;

class JWTService
{

    private static $lastError = null;

    private static $isRefreshed = false;

    private static $refreshedToken = null;

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
        $JWTConfig = new \Config\JWT();
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
        $JWTConfig = new \Config\JWT();
        try{
            return JWT::decode($jwt, $JWTConfig->getKey(), array('HS256'));
        } catch(\Exception $e){
            static::setLastError($e->getMessage());
            return false;
        }
    }

    public static function refresh($payload){
        static::$refreshedToken = static::encode($payload->user);
        $db = \Config\Database::connect();
        $db->table('users')
            ->where("user_id", $payload->user->id)
            ->where("current_token", static::$refreshedToken)
            ->update();
        static::$isRefreshed = true;
    }

    public static function isRefreshed(){
        return static::$isRefreshed;
    }

    public static function refreshedToken(){
        return static::$refreshedToken;
    }

    public static function publicTokenExists($publicToken){
        $JWTConfig = new \Config\JWT();
        return in_array($publicToken, $JWTConfig->getPublicTokens());
    }

    public static function setLastError($error){
        static::$lastError = $error;
    }

    public static function getLastError(){
        return static::$lastError;
    }
}
