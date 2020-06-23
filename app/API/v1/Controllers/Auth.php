<?php namespace App\API\v1\Controllers;

/**
 * @package    App\Controller\Api
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;

class Auth extends BaseController
{
    public function authenticate()
    {
        $auth = \Config\Services::auth();
        if ($auth->isLoggedIn()) {
            return $this->respond(['User already connected'], 409);
        }
        $request = \Config\Services::apiRequest();
        $userName = $request->getParam('username');
        $password = $request->getParam('password');
        $authProvider = new \App\Models\AuthProviderModel();
        $authProvider->authenticate($userName, $password);
        if ($authProvider->successed()) {
            return $this->respond([
                "message" => "Successful login",
                "token" => $authProvider->getToken(),
                "id" => \is_numeric($authProvider->user()->user_id) ? (int) $authProvider->user()->user_id : $authProvider->user()->user_id,
                "expireAt" => $authProvider->getTokenExpiration()
            ], 200);
        }
        return $this->respond([$authProvider->cause()], $authProvider->errorCode(), $authProvider->errorDescription());
    }

    public function activeAccount()
    {
        $auth = \Config\Services::auth();
        if ($auth->isLoggedIn()) {
            return $this->respond(['User already actived and connected'], 409);
        }
        $params = \Config\Services::apiRequest();
        $token = $params->getParam('token');
        if ($token === null) {
            return $this->respond(['User activation token required'], 400);
        }
        $db = \Config\Database::connect();
        $row = $db->table('users')->where('reset_psswd_token', $token)->get()->getFirstRow();
        if ($row === null) {
            return $this->respond(['User reset password token not found'], 404);
        }
        unset($token);
        $row = (object) $row;
        helper('security');
        $tokenDecoded = safe_b64decode($row->reset_psswd_token);
        $segment = explode('@@', $tokenDecoded);
        $time = Time::createFromFormat('Y-m-d H:i:s', $segment[1]);
        if ($time->isBefore(Time::now())) {
            return $this->respond(['User activation token not found'], 404);
        }
        if ($this->doActiveAccount($row->user_id)) {
            return $this->respond(['User actived'], 200);
        }
        return $this->respond(['Error on system'], 501);
    }

    public function logout()
    {
        $auth = \Config\Services::auth();
        if (!$auth->isLoggedIn()) {
            return $this->respond(['User token not found'], 406);
        }
        $auth->userSessionDestroy();
        return $this->respond(['User disconnected'], 200);
    }

    private function doActiveAccount($user_id): bool
    {
        $userModel = new \App\Models\UserModel();
        $data = array(
            'status' => "active",
            'deleted' => 0,
            'deleted_at' => "0000-00-00 00:00:00",
            'reset_psswd_token' => null,
            'reset_psswd_validity' => "0000-00-00 00:00:00",
        );
        return $userModel
            ->set($data)
            ->where('user_id', $user_id)
            ->update();
    }

}
