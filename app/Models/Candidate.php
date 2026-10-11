<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'vision_mission', 'photo'];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
