<?php

namespace LaravelAcademy;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';

    protected $fillable = ['name', 'email', 'funfact', 'age'];

    public function lessons()
    {
        return $this->hasMany('LaravelAcademy\Lesson');
    }
}
