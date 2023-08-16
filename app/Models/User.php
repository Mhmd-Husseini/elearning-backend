<?php
namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_type_id', 
        'parent_id', 
        'name', 
        'email', 
        'password', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function parent(){
    return $this->belongsTo(User::class, 'parent_id');
    }

    public function students(){
        return $this->hasMany(User::class, 'parent_id');
    }

    public function type(){
        return $this->belongsTo(User_type::class,'user_type_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollment_courses')->withTimestamps();;
    }

    public function teacherCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function studentCourses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function coursesTeachers()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }
    
    public function enrollmentCourses(){
        return $this->hasMany(Enrollment_course::class);
    }

}
