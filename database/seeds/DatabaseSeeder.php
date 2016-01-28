<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(LaravelAcademy\Teacher::class, 15)->create()->each(function($teacher) {
            $teacher->lessons()->save(factory(LaravelAcademy\Lesson::class)->make());
        });
    }
}
