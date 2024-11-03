<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('position', ['handler', 'cutter', 'both'])->nullable();
            $table->integer('jersey_number')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->integer('speed_rating')->default(5);
            $table->integer('endurance_rating')->default(5);
            $table->boolean('is_active')->default(true);
            $table->text('throws_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_profiles');
    }
};
