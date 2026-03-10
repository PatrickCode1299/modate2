<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_email',
        'user_image',
        'user_text',
        'background',
        'user_video',
        'date_posted'

    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
