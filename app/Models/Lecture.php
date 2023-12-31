<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id', 
        'title', 
        'description'
    ];
    public function attendances(){

        return $this->hasMany(Attendance::class);
        
    }
}
