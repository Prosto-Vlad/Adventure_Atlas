<?php

namespace App\Models;

use CodeIgniter\Model;

class NotesModel extends Model
{
    protected $table = 'notes'; 
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'map_id',
        'content',
        'coordinates',
        'style',
        'created_at',
    ];

}