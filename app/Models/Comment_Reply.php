<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment_Reply extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment_reply',
        'comment_file',
        'initial_comment_id',
        'reply_id',
        'user_being_replied',
        'user_who_replied'

    ];

}
