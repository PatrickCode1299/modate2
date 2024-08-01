<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'avatar',
        'caption',
        'email',
        'postid',
        'isReply'
        
       
    ];
}
