<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';  
    protected $primaryKey = 'id';  

    protected $allowedFields = ['username', 'email', 'password'];

    public function getUser($username)
    {
        return $this->where('username', $username)->first();
    }

    public function createUser($data)
    {
        return $this->insert($data);
    }

    public function getAllUsers()
    {
        return $this->findAll();
    }


}