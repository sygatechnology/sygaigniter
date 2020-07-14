<?php 

namespace App\Services;

class ApiAuthService
{
    private $errors;
    public function isLoggedIn(): bool
    {
        $request = \Config\Services::apiRequest();
        $token = $request->getToken();
        if(! empty($token) ) {
            $jwtService = \Config\Services::JWT();
            if( $payload = $jwtService::decode($token) ){
                $payload = (object) $payload;
                $db = \Config\Database::connect();
                if($db->table('users')
                    ->where("user_id", $payload->user->id)
                    ->where("current_token", $token)
                    ->countAllResults() === 0){
                        return false;
                }
                if( $this->needRefresh($payload->exp) ){
                    $jwtService::refresh($payload);
                }
                $jwtService::setLastError(null);
                return true;
            }
            $this->errors = $jwtService::getLastError();
        }
        return false;
    }

    private function needRefresh($expiration){
        return ($expiration - time() <= 60);
    }

    public function userSessionDestroy()
    {
        $request = \Config\Services::apiRequest();
        $token = $request->getToken();
        $jwtService = \Config\Services::JWT();
        $payload = $jwtService::decode($token);
        $payload = (object) $payload;
        $db = \Config\Database::connect();
        $db
            ->table('users')
            ->set('current_token', '')
            ->where('user_id', $payload->user->id)
            ->update();
        $jwtService::setLastError(null);
        return true;
    }

    public function errors(){
        return $this->errors;
    }
}
