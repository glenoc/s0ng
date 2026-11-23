<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Song;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('song_starts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(User::class, 'started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('song_name');
            $table->string('artist_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_starts');
    }
};
