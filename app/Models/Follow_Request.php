<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow_Request extends Model
{
    use HasFactory;
    protected $fillable = [
        'isPending',
        'user_who_is_followed',
        'user_who_wants_to_follow'
        
       
    ];
}
