<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $casts = [
        'hidden_from_guests' => 'boolean',
    ];

    public function scopeVisibleToGuests(Builder $query): void
    {
        $query->where('hidden_from_guests', false);
    }

    public function artist(){
        return $this->belongsTo(Artist::class);
    }

    public function lines(): array
    {
        return array_map(function ($line) {
            $isChord = str_ends_with($line, '_');

            return (object) ['isChord' => $isChord, 'text' => $isChord ? rtrim($line, '_') : $line];
        }, preg_split("/\r\n|\n|\r/", $this->text));
    }

    public function chordNotation(): array
    {
        $notation = [];
        foreach (preg_split("/\r\n|\n|\r/", $this->text) as $line) {
            if (str_ends_with($line, '_')) {
                $notation[] = rtrim($line, '_');
            } elseif (preg_match('/^\s*capo\b/i', $line)) {
                $notation[] = $line;
            }
        }

        return $notation;
    }
}
