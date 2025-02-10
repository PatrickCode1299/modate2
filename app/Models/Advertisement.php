<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'ad_amount',
        'ad_reach',
        'ad_owner',
        'ad_stop_date'
       
    ];
}
