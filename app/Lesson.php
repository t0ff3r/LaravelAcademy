<?php

namespace LaravelAcademy;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    public function teacher()
    {
        return $this->belongsTo('LaravelAcademy\Teacher');
    }
}
