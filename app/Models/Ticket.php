<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_function_id',
        'seat_number',
        'status',
        'ticket_code',
    ];

    protected $casts = [
        'seat_number' => 'array', // Convierte seat_number a un array
    ];

    // Relación con MovieFunction
    public function movieFunction()
    {
        return $this->belongsTo(MovieFunction::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

