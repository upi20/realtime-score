<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $table = 'scores';
    protected $fillable = ['game_match_id', 'team1_score', 'team2_score'];

    public function gameMatch()
    {
        return $this->belongsTo(GameMatch::class, 'game_match_id', 'id');
    }
}
