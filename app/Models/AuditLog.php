<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false; // We managed it via created_at only
    protected $fillable = ['event', 'details', 'ip_address'];
}
