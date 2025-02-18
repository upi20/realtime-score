<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\GameMatch;

class EventSeeder extends Seeder
{
    public function run()
    {
        $matches = GameMatch::all();

        foreach ($matches as $match) {
            $match->load(['scores']);
            Event::insert([
                [
                    'game_match_id' => $match->id,
                    'scoring_team' => 'team1',
                    'point_type' => 'goal',
                    'points' => $match->scores->team1_score,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'game_match_id' => $match->id,
                    'scoring_team' => 'team2',
                    'point_type' => 'penalty',
                    'points' => $match->scores->team2_score,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
