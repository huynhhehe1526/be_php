<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seating extends Model
{
    use HasFactory;
    protected $fillable = [
        'chairId',
        'statusSeat',
        'price'
    ];
    public $timestamps = true;
    public function chair_allcodes()
    {
        return $this->belongsTo(Allcode::class, 'chairId', 'keyMap');
    }
    public function statusSeat_allcodes()
    {
        return $this->belongsTo(Allcode::class, 'statusSeat', 'keyMap');
    }
    public function price_allcodes()
    {
        return $this->belongsTo(Allcode::class, 'priceId', 'keyMap');
    }
    public function seat_booking()
    {
        return $this->hasMany(Booking::class, 'seatId', 'id');
    }
}
