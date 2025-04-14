<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId',
        'showtimeId',
        'seatId',
        'totalPrice',
        'image_payment',
        'statusBook'
    ];
    public $timestamps = true;
    public function booking_user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
    public function booking_seat()
    {
        return $this->belongsTo(Seating::class, 'seatId', 'id');
    }
    public function showtime_booking()
    {
        return $this->belongsTo(Showtime::class, 'showtimeId', 'id');
    }
    public function booking_allcode(){
        return $this->belongsTo(Allcode::class, 'statusBook', 'keyMap');
    }
}