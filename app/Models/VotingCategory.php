<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
