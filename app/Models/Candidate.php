<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = ['name', 'vision', 'mission', 'photo', 'voting_category_id'];

    public function votingCategory()
    {
        return $this->belongsTo(VotingCategory::class);
    }
}
