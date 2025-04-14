<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'image',
        'genreId',
        'director',
        'statusId',
        'preview',
        'duration',
        'actor',
        'premiere_date',
        'subtitle',
        'video'
    ];



    public $timestamps = true;

    public function status_movie()
    {
        // return $this->belongsTo(Allcode::class,'statusId', 'keyMap');
        return $this->belongsTo(Allcode::class, 'statusId', 'keyMap');
    }
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'showtimes', 'movieId', 'locationId')->withPivot('time', 'date');
    }
    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movie_id', 'id');
    }
    public function associate_genre()
    {
        return $this->belongsTo(Genre::class, 'genreId', 'id');
    }
}
