<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;
    protected $fillable = [
        'community_name',
        'community_owner',
        'community_bio',
        'community_avatar',
        'community_cover',
        'community_category',
        'community_rules'

    ];
}
