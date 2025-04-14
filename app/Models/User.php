<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    //thay thế
    protected $fillable = [
        'userName',
        'password',
        'fullName',
        'email',
        'address',
        'phoneNumber',
        'gender',
        'roleId',
        'typeAccount',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = true;
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //association
    public function role()
    {
        return $this->belongsTo(Allcode::class, 'keyMap', 'id');
    }

    public function pass()
    {
        return $this->hasMany(ForgotPass::class, 'userId', 'id');
    }
    public function user_booking()
    {
        return $this->hasMany(Booking::class, 'userId', 'id');
    }
}
