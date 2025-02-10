<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shared_Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_of_user_who_shared',
        'email_of_user_who_shared',
        'name',
        'avatar',
        'caption',
        'email',
        'postid',
        'prev_id',
        'isReply',
        'post_img1',
        'post_img2',
        'post_img3',
        'post_img4',
        'video',
        'quote',
      
        
       
    ];
}
