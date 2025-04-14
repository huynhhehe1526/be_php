<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;
    protected $fillable = [
        'movie_id',
        'location_id',
        'time',
        'date'
    ];
    public $timestamps = true;
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
    public function showtime_booking()
    {
        return $this->hasMany(Booking::class, 'showtimeId', 'id');
    }
}
