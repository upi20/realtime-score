<?php

use App\Models\Event;
use App\Models\GameMatch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create((new Event)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_match_id')->constrained((new GameMatch())->getTable())->onDelete('cascade');
            $table->enum('scoring_team', ['team1', 'team2']);
            $table->string('point_type');
            $table->integer('points');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists((new Event)->getTable());
    }
};
