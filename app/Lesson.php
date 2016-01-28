<?php

namespace LaravelAcademy;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = ['title', 'description', 'start', 'end', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo('LaravelAcademy\Teacher');
    }
}
