<?php namespace App\API\v1\Services;

class ApiAuthService
{
    private $errors;
    public function isLoggedIn(): bool
    {
        $session = \Config\Services::session();
        $request = \Config\Services::apiRequest();
        $token = $request->getToken();
        if(! empty($token) ) {
            $jwtService = \Config\Services::JWT();
            if( $payload = $jwtService::decode($token) ){
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
        return false;
    }

    public function userSessionDestroy()
    {
        $session = \Config\Services::session();
        $session->destroy();
    }

    public function errors(){
        return $this->errors;
    }
}
