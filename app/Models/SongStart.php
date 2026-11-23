<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongStart extends Model
{
    protected $fillable = [
        'song_id',
        'started_by',
        'song_name',
        'artist_name',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
