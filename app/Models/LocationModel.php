<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'id';

    protected $allowedFields = ['map_id', 'icon_id', 'name', 'coordinates'];

}