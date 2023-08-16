<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'lecture_id', 
        'attend'
    ];
    public function lecture() {

        return $this->belongsTo(Lecture::class);

    }

    public function student(){
        
        return $this->belongsTo(User::class, 'user_id');
    }
}
