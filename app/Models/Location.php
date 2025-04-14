<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'nameLocation',
        'valueVi',
        'valueEn'
    ];
    public $timestamps = true;

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'showtimes', 'locationId', 'movieId')->withPivot('time', 'date');
    }
    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'location_id', 'id');
    }
}
