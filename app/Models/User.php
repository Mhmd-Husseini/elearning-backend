<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

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

<<<<<<< HEAD
    public function parent(){
    return $this->belongsTo(User::class, 'parent_id');
    }

=======
    public function students(){
        return $this->hasMany(User::class, 'parent_id');
    }

    public function type(){
        return $this->belongsTo(User_type::class,'user_type_id');
    }
>>>>>>> origin/Admin
}
