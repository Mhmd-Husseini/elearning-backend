<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id', 
        'title', 
        'description', 
        'file', 
        'due', 
        'rubric',
    ];
    
    public function submissions() {
        return $this->hasMany(Submission::class, 'assignment_id');
    }
}
