<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieFunction extends Model
{
    protected $fillable = ['movie_id', 'room_id', 'start_time', 'end_time'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}
