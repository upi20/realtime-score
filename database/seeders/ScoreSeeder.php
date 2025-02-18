<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Score;
use App\Models\GameMatch;

class ScoreSeeder extends Seeder
{
    public function run()
    {
        $matches = GameMatch::all();

        foreach ($matches as $match) {
            Score::create([
                'game_match_id' => $match->id,
                'team1_score' => rand(0, 5), // Skor acak
                'team2_score' => rand(0, 5),
            ]);
        }
    }
}
