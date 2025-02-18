<?php

use App\Models\GameMatch;
use App\Models\Score;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create((new Score)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_match_id')->constrained((new GameMatch())->getTable())->onDelete('cascade');
            $table->integer('team1_score')->default(0);
            $table->integer('team2_score')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists((new Score)->getTable());
    }
};
