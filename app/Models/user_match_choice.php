<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_match_choice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user',
        'choice',
        'isPending'
       
    ];
}
