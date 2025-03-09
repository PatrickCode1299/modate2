<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'avatar',
        'caption',
        'email',
        'post_img1',
        'post_img2',
        'post_img3',
        'post_img4',
        'video',
        'postid',
        'isReply'
       
    ];
}

