<?php

namespace LaravelAcademy\Transformers;

use LaravelAcademy\Lesson;
use League\Fractal\TransformerAbstract;

class LessonTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'teacher'
    ];

    /**
     * Transform data object
     *
     * @param Lesson $lesson
     * @return array
     */
    public function transform(Lesson $lesson)
    {
        return [
            'id' => (int) $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'start' => $lesson->start,
            'end' => $lesson->end
        ];
    }

    /**
     * Include sub collection
     *
     * @param Lesson $lesson
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTeacher(Lesson $lesson)
    {
        $teacher = $lesson->teacher;

        return $this->item($teacher, new TeacherTransformer);
    }

}
