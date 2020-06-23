<?php namespace Config;

use CodeIgniter\Config\Services as CoreServices;

require_once SYSTEMPATH . 'Config/Services.php';

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{
    public static function auth()
    {
        return new \App\API\v1\Services\ApiAuthService();
    }

    public static function apiRequest()
    {
        return new \App\API\v1\Services\ApiRequestService();
    }

    public static function ApiResult()
    {
        return new \App\API\v1\Services\ApiResultService();
    }

    public static function currentUser()
    {
        return new \App\API\v1\Services\CurrentUser();
    }

    public static function options()
    {
        return new \App\API\v1\Services\OptionsService();
    }

    public static function JWT()
    {
        return new \App\API\v1\Services\JWTService();
    }

}
