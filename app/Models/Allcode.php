<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allcode extends Model
{
    use HasFactory;
    protected $fillable = [
        'keyMap',
        'type',
        'valueVi',
        'valueEn'
    ];
    public $timestamps = true;

    //association
    public function users()
    {
        return $this->hasMany(User::class, 'keyMap', 'id');
    }

    public function movies()
    {
        return $this->hasMany(Movie::class, 'statusId', 'keyMap');
    }

    public function pass()
    {
        return $this->belongsTo(ForgotPass::class, 'statusId', 'keyMap');
    }
    public function chair()
    {
        return $this->hasMany(Seating::class, 'chairId', 'keyMap');
    }

    public function statusSeat()
    {
        return $this->hasMany(Seating::class, 'statusSeat', 'keyMap');
    }
    public function price()
    {
        return $this->hasMany(Seating::class, 'priceId', 'keyMap');
    }

    public function allcode_booking(){
        return $this->hasMany(Booking::class, 'statusBook', 'keyMap');
    }
}