<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user1_id', 
        'user2_id', 
        'message',
    ];
}
