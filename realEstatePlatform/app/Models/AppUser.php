<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Contracts\Auth\MustVerifyEmail;

class AppUser extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact',
        'gender',
        'profile_picture',
    ];
    use HasFactory;
}





