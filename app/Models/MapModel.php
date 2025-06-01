<?php

namespace App\Models;

use CodeIgniter\Model;

class MapModel extends Model
{
    protected $table = 'maps';  
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'type_id', 'user_id', 'image_path', 'updated_at'];
    protected $useTimestamps = true;

    public function getUserMaps($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

}