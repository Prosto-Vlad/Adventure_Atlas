<?php

namespace App\Models;

use CodeIgniter\Model;

class IconsModel extends Model
{
    protected $table = 'icons';  
    protected $primaryKey = 'id';  

    protected $allowedFields = ['name', 'image_path', 'created_at'];
    
    public function getAll()
    {
        return $this->findAll();
    }

}