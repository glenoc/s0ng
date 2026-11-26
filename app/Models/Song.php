<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    public function artist(){
        return $this->belongsTo(Artist::class);
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
