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
            Event::insert([
                [
                    'match_id' => $match->id,
                    'scoring_team' => 'team1',
                    'point_type' => 'goal',
                    'points' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'match_id' => $match->id,
                    'scoring_team' => 'team2',
                    'point_type' => 'penalty',
                    'points' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
