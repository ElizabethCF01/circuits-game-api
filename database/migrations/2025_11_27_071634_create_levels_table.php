<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('start_x');
            $table->integer('start_y');
            $table->integer('required_circuits');
            $table->integer('max_commands');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->integer('grid_width');
            $table->integer('grid_height');
            $table->json('tiles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
