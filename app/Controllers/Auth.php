<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        $json = $this->request->getJSON();
        $username = $json->username;
        $password = $json->password;

        $user = $this->userModel->getUser($username);

        if ($user && password_verify($password, $user['password'])) {
            session()->set('logged_in', true);
            session()->set('username', $username);
            session()->set('user_id', $user['id']);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Неправильний логін або пароль']);
    }
    
    public function logout()
    {
        session()->destroy();
        return $this->response->setJSON(['success' => true]);
    }

    public function register()
    {
        $json = $this->request->getJSON();
        $username = $json->username;
        $password = password_hash($json->password, PASSWORD_DEFAULT);
        $email = $json->email;



        $this->userModel->createUser([
            'username' => $username,
            'password' => $password,
            'email' => $email
        ]);

        session()->set('logged_in', true);
        session()->set('username', $username);
        return $this->response->setJSON(['success' => true]);
    }
}