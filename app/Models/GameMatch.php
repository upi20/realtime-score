<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'game_matches';
    protected $fillable = ['sport', 'team1_name', 'team2_name', 'status'];

    public function scores()
    {
        return $this->hasOne(Score::class, 'game_match_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'game_match_id', 'id');
    }
}
