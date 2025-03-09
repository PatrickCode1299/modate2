<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    protected $fillable = [
        'channel_name',
        'channel_owner',
        'channel_bio',
        'channel_avatar',
        'channel_cover',
        'channel_category'
       
    ];
}
