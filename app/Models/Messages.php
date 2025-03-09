<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender',
        'reciever',
        'conversation',
        'file',
        'file_status',
        'unique_id',
        'isRead',

        
    ];
}
