<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Config\Connection;
use App\Contract\Operations;

class AuthController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function getData() {
        return json_encode($this->con);
    }

    public function login($logData) {
        if (isset($logData['username']) && empty($logData['username'])) {
            return json_encode([
                'status' => false,
                'data'   => 'Username can not be null'
            ]);
        }

        if (isset($logData['password']) && empty($logData['password'])) {
            return json_encode([
                'status' => false,
                'data'   => 'password can not be null'
            ]);
        }

        $user = $this->joinQuery('SELECT * FROM users WHERE username="'.$logData['username'].'" AND password="'.md5($logData['password']).'" LIMIT 1')->fetch(\PDO::FETCH_ASSOC);
        if (isset($user['username'])) {
            $auth_token = rand(1000,10000);
            $user['auth_token'] = $auth_token;
            $this->update('users', ['auth_token'=> $auth_token], 'id="'.$user['id'].'"');
            return json_encode([
                'status' => true,
                'message'=> 'Login Successfully',
                'data'   =>  $user
            ]);
        } else {
            return json_encode([
                'status' => false,
                'data'   => 'Credentials Do not match our recoreds'
            ]);
        }
    }


    public function registration($formData)  {
        if (isset($formData['username']) && empty($formData['username'])) {
            return json_encode([
                'status' => false,
                'data'   => 'Username can not be null'
            ]);
        }

        if (isset($formData['password']) && empty($formData['password'])) {
            return json_encode([
                'status' => false,
                'data'   => 'password can not be null'
            ]);
        }
        $formData['password'] = md5($formData['password']);
        $formData['created_at'] = date('Y-m-d H:i:s');
        $is_exist = $this->joinQuery('SELECT username FROM users WHERE username="'.$formData['username'].'" limit 1')->fetch(\PDO::FETCH_ASSOC);
        if (isset($is_exist['username'])) {
            return json_encode([
                'status' => false,
                'data'   => 'Username has already been taken'
            ]);
        }
        $user = $this->insert('users', $formData);
        if ($user) {
            $formData['id'] = $this->getInsertId();
            return json_encode([
                'status' => true,
                'message' => 'User has been successfully created',
                'data'   => $formData
            ]);
        }
    }

    public function logout($logData) {
        if (isset($logData['id']) && empty($logData['id'])) {
            return json_encode([
                'status' => false,
                'data'   => 'User ID can not be null'
            ]);
        }
        $updated = $this->update('users', ['auth_token'=> null], 'id="'.$logData['id'].'"');
        if ($updated) {
            return json_encode([
                'status' => true,
                'data'   => 'Successfully logged out'
            ]);
        }
    }
}