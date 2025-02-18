<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $fillable = ['game_match_id', 'scoring_team', 'point_type', 'points'];

    public function gameMatch()
    {
        return $this->belongsTo(GameMatch::class, 'game_match_id', 'id');
    }
}
