<?php

namespace LaravelAcademy\Transformers;

use LaravelAcademy\Teacher;
use League\Fractal\TransformerAbstract;

class TeacherTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'lessons'
    ];

    /**
     * Transform data object
     *
     * @param Teacher $teacher
     * @return array
     */
    public function transform(Teacher $teacher)
    {
        return [
            'id' => (int) $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            'funfact' => $teacher->funfact,
            'age' => $teacher->age
        ];
    }

    /**
     * Include sub collection
     *
     * @param Teacher $teacher
     * @return \League\Fractal\Resource\Collection
     */
    public function includeLessons(Teacher $teacher)
    {
        $lessons = $teacher->lessons;

        return $this->collection($lessons, new LessonTransformer);
    }

}
