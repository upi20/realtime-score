<?php

use App\Models\GameMatch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create((new GameMatch)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->enum('sport', ['sepakbola', 'basket', 'voli']);
            $table->string('team1_name');
            $table->string('team2_name');
            $table->enum('status', ['ongoing', 'finished'])->default('ongoing');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists((new GameMatch)->getTable());
    }
};
