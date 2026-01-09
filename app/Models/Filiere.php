<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    protected $fillable = ['name', 'status', 'total_courses'];

    public function students()
    {
        return $this->belongsToMany(User::class, 'filiere_user')
                    ->where('type', 'student');
    }

    // Helper method to get users through students
    public function studentUsers()
    {
        return $this->belongsToMany(User::class, 'students', 'id', 'user_id')
                    ->through('filiere_user')
                    ->withPivot('student_id');
    }
}