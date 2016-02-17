# Environment and setup
## Services we used

+ [Laravel 5.2](https://laravel.com/docs/5.2/)
+ [GitHub](https://github.com/t0ff3r/LaravelAcademy/)
+ [Digital Ocean](https://www.digitalocean.com/)
+ [Laravel Forge](forge.laravel.com/)

## Prerequisites

+ [Setup Laravel Installer](https://laravel.com/docs/5.2/installation)
+ [Setup Laravel Homestead](https://laravel.com/docs/5.2/homestead)

## Create project

We scaffold the project with the following command:

```bash
laravel new YourAppName
```

This commands performs a number of things:

+ Scaffold project
+ Install dependencies
+ Add unique application key

One last thing we want todo is to give our application its own namespace

```bash
php artisan app:name YourName
```

## Configure Homestead

We now want to configure homestead to find our application and to get a dev domain for it.

First we need to edit our Homestead.yaml file:

```bash
vim ~/.homestead/Homestead.yaml
````

We add the following lines under sites:

```bash
sites:
  - map: yourdomain.dev
    to: /home/vagrant/Code/YourAppName/public
```

Then we need to add our new site to our hosts file:

```bash
sudo vim /etc/hosts
```

And here we add a new entry for our homestead ip:

```bash
192.168.10.10 yourdomain.dev
```

And finally lets provision our homestead box and restart all services:

```bash
homestead provision
```

We should now be able to see Laravel 5 when opening http://yourdomain.dev in a browser.

## Setup Git repository

This is probably a good point to save our work to GitHub. First go to github.com and create a new repository.

Then do all the following from a terminal inside your project folder:

```bash
git init
git add .
git commit -m "Initial commit of our API"
git remote add origin https://github.com/[YourGithubUsername]/YourRepositoryName.git
git push origin -u master
```

## Configure MySQL

We recommend using a GUI client for managing your database like one of the following:

+ [Sequel Pro](http://www.sequelpro.com/)
+ [MySQL Workbench](https://www.mysql.com/products/workbench/)

Connect to your homestead database with the following connection details:

```
Host: 127.0.0.1
Username: homestead
Password: secret
Port: 33060
```

Add a new database with the name you prefer, like e.g. "yourapplication".
After this we need to configure our environment variables to use this database.

In our project root you will find a file called .env. Open it and update your database name:

```
DB_DATABASE: yourapplication
```

# API implementation

Our implementation will be an API returning information about Teachers and their Lessons. Thats it.
Its just an example, so feel free to change to whatever suits your needs.

## Database migrations:

First we want to create some tables for our API. We can easily do this by making a couple of migrations.
We can create the necessary files using the Artisan CLI from our terminal:

```bash
php artisan make:migration create_teachers_table --create=teachers
php artisan make:migration create_lessons_table --create=lessons
```

We can now run this to create our tables. We should ssh into our homestead for this part:

```bash
homestead ssh
cd Code/YourAppName
php artisan migrate
```

Our database should now contain two new tables called teachers and lessons.

We can now add our fields with correct types and so on to our migrations (the timestamps in the filenames will differ in your case):

2016_02_17_213921_create_teachers_table: 

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->string('email', 100);
            $table->text('funfact');
            $table->integer('age')->unsigned();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teachers');
    }
}

```


2016_02_17_213921_create_teachers_table:

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title', 100);
            $table->text('description');
            $table->dateTime('start');
            $table->dateTime('end');

            $table->integer('teacher_id')->unsigned();
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lessons');
    }
}

```

## Models

We also need models to represent our database tables. One model represents one table. Relations between the models needs to be defined in the models. We can scaffold our models with artisan like with everything else:

```bash
php artisan make:model Teacher
php artisan make:model Lesson
```

Teacher.php
```php
<?php

namespace YourAppName;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';

    protected $fillable = ['name', 'email', 'funfact', 'age'];

    // Model relation to their lessons
    public function lessons()
    {
        return $this->hasMany('YourAppName\Lesson');
    }
}

```

Lesson.php
```php
<?php

namespace YourAppName;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = ['title', 'description', 'start', 'end', 'teacher_id'];

    // Model relation to a teacher
    public function teacher()
    {
        return $this->belongsTo('YourAppName\Teacher');
    }
}

```

## Factories and seeding data

To easily get some dummy test data we can create a couple of factories.

ModelFactory.php
```php
<?php

$factory->define(YourAppName\Teacher::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'funfact' => $faker->paragraph,
        'age' => $faker->numberBetween(20, 67)
    ];
});

$factory->define(YourAppName\Lesson::class, function (Faker\Generator $faker) {
    $start = Carbon\Carbon::createFromTimeStamp($faker->dateTimeBetween('-1 month', '+1 month')->getTimestamp());

    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'start' => $start->toDateTimeString(),
        'end' => $start->addHours($faker->numberBetween(1, 2))->toDateTimeString(),
    ];
});

```

And now we can add this to our seeder file

DatabaseSeeder.php
```php
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
        factory(YourAppName\Teacher::class, 15)->create()->each(function($teacher) {
            $teacher->lessons()->save(factory(YourAppName\Lesson::class)->make());
        });
    }
}

```

And finally to re-run our migrations and seeds to fill our database with dummy data we do:

```bash
php artisan migrate:refresh --seed
```
