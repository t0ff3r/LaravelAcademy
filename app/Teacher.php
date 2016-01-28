<?php

namespace LaravelAcademy;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';

    public function lessons()
    {
        return $this->hasMany('LaravelAcademy\Lesson');
    }
}
