<?php

namespace App\API\v1\Services;

use CodeIgniter\HTTP\RequestInterface;

class ApiRequestService
{
    public function getParam(string $param)
    {
        return $this->_getParam($param);
    }

    public function params(){
        return $this->_getParam();
    }

    public function getToken(){
        $request = \Config\Services::request();
        $authorization = $request->getHeaderLine( 'Authorization' );
        if( ! empty($authorization) ){
            $segment = explode(" ", $authorization);
            if(count($segment) == 2){
                return $segment[1];
            }
        }
        return$this-> _getParam( 'token' );
    }

    private function _getParam(string $param = null){
        $request = \Config\Services::request();
        $jsonParams = (array) $request->getJSON();
        $varParams = (array) $request->getVar();
        $postGetParams = (array) $request->getPostGet();
        $getPostParams = (array) $request->getGetPost();
        $rawParams = (array) $request->getRawInput();
        $params = array_merge($jsonParams, $varParams, $postGetParams, $getPostParams, $rawParams);
        if(! is_null($param)) return (isset($params[$param])) ? $params[$param] : null;
        return $params;
    }

}
