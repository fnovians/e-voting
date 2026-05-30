<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    public $timestamps = false; // Managed via timestamp column
    protected $fillable = ['encrypted_candidate', 'iv', 'tag', 'timestamp'];
}
