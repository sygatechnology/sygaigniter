<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Request;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class ApiAuth implements FilterInterface
{
    public function before(RequestInterface $request)
    {
      $auth = \Config\Services::auth();
  		if(!$auth->isLoggedIn()){
            return Services::response()
                                ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                                ->setJSON([$auth->errors()]);
  		}
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        // Do something here
    }
}
