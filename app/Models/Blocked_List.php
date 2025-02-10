<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blocked_List extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_who_block',
        'user_getting_blocked'
        
       
    ];
}
