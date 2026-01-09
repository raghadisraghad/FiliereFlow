<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Relations
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'filiere_user');
    }

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'type',
        'profile_photo_path',
        'sex'
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutator to hash password automatically
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value) && !\Illuminate\Support\Str::startsWith($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Scope to get only students
     */
    public function scopeStudents($query)
    {
        return $query->where('type', 'student');
    }
}
