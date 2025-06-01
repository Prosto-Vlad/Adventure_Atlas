<?php

namespace App\Models;

use CodeIgniter\Model;

class MapTypeModel extends Model
{
    protected $table = 'maptypes'; 
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description'];

}