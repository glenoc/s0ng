<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_transpositions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->integer('semitones');
            $table->unsignedTinyInteger('capo');
            $table->timestamps();
            $table->primary(['user_id', 'song_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_transpositions');
    }
};
