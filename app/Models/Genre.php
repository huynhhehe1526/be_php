<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    protected $fillable = [
        'nameGenre',
        'valueVi',
        'valueEn'

    ];
    public $timestamps = true;
    public function associate_movie()
    {
        return $this->hasMany(Movie::class, 'genreId', 'id');
    }
}
