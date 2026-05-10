<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'location',
        'phone_number',
        'profile_picture',
        'coverText',
        'coverPhoto',
        'isPaid',
        'school',
        'religion',
        'interest',
        'isSound',
        'isPrivate',
        'isColor'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'phone_number',
         'email',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function channel() {
    return $this->hasOne(Channel::class);
    }
    public function user_post(){
        return $this->hasMany(User_Post::class);
    }
    public function story(){
    return $this->hasMany(Story::class);
    }
    public function comment(){
        return $this->hasMany(Comment::class);
    }
}
