<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\Lecture;
use App\Models\Material;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'name',
        'seats',
        'description',
        'category_id',
    ];

    public function category(){
    return $this->belongsTo(Category::class);
    }

    
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollment_courses', 'course_id', 'user_id')
            ->where('users.user_type_id', '=', 3);
    }

    public function quizes()
    {
        return $this->hasMany(Quiz::class)->orderBy('created_at', 'desc');;
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class)->orderBy('created_at', 'desc');;
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class)->orderBy('created_at', 'desc');;
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('created_at', 'desc');;
        return $this->belongsTo(Category::class,'category_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, )->withTimestamps();;
    }
}

