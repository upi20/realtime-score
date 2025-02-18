<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameMatch;

class GameMatchSeeder extends Seeder
{
    public function run()
    {
        GameMatch::insert([
            [
                'sport' => 'sepakbola',
                'team1_name' => 'Team A',
                'team2_name' => 'Team B',
                'status' => 'ongoing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sport' => 'basket',
                'team1_name' => 'Lakers',
                'team2_name' => 'Bulls',
                'status' => 'ongoing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sport' => 'voli',
                'team1_name' => 'Eagles',
                'team2_name' => 'Hawks',
                'status' => 'finished',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
