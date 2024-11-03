<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('player_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->enum('level', ['master', 'basic', 'beginner', 'null'])->default('null');
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('throws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->enum('level', ['master', 'advanced', 'intermediate', 'beginner'])->default('beginner');
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('throws');
        Schema::dropIfExists('player_skills');
        Schema::dropIfExists('skills');
    }
};