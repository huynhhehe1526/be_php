<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPass extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId',
        'new_pass',
        'confirm_pass',
        'token',
        'statusId',
    ];

    public $timestamps = true;
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function status_pass()
    {
        return $this->belongsTo(Allcode::class, 'statusId', 'keyMap');
    }
}