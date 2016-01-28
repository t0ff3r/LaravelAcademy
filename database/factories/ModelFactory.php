<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(LaravelAcademy\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(LaravelAcademy\Teacher::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'funfact' => $faker->paragraph,
        'age' => $faker->numberBetween(20, 67)
    ];
});

$factory->define(LaravelAcademy\Lesson::class, function (Faker\Generator $faker) {
    $start = Carbon\Carbon::createFromTimeStamp($faker->dateTimeBetween('-1 month', '+1 month')->getTimestamp());

    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'start' => $start->toDateTimeString(),
        'end' => $start->addHours($faker->numberBetween(1, 2))->toDateTimeString(),
    ];
});