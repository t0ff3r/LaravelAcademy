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
php artisan app:name YourAppName
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

We can now re-run our migrations to get the new fields:

```bash
php artisan migrate:refresh
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

## Routes and controllers

Now that we have data its time to make our API endpoints and routes. Lets define our routes first:

routes.php
```php
<?php

/*
|--------------------------------------------------------------------------
| REST API Routes
|--------------------------------------------------------------------------
|
| This route group for all our REST API endpoints
|
*/
Route::resource('teachers', 'TeachersController');
Route::resource('lessons', 'LessonsController');

```

Now we need to create our controllers from. This can again be done with artisan (--resource is a flag to get all CRUD operations out of the box in our controller):

```bash
php artisan make:controller TeachersController --resource
php artisan make:controller LessonsController --resource
```

We can see all defined routes with this command:

```bash
php artisan route:list
```

Straight out of the box we should improve our routes by removing view related functions. Like so:

routes.php
```php
<?php

/*
|--------------------------------------------------------------------------
| REST API Routes
|--------------------------------------------------------------------------
|
| This route group for all our REST API endpoints
|
*/
Route::resource('teachers', 'TeachersController', ['except' => [
'create', 'edit'
]]);

Route::resource('lessons', 'LessonsController', ['except' => [
    'create', 'edit'
]]);

```

Now we can implement our controllers:

TeachersController.php
```php
<?php

namespace LaravelAcademy\Http\Controllers;

use Illuminate\Http\Request;

use YourAppName\Teacher;
use YourAppName\Http\Requests;
use YourAppName\Http\Controllers\Controller;

class TeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Teacher::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Teacher::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Teacher::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $teacher->fill($request->all())->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);

        $teacher->delete();
    }
}

```

LessonsController.php
```php
<?php

namespace YourAppName\Http\Controllers;

use Illuminate\Http\Request;

use YourAppName\Lesson;
use YourAppName\Http\Requests;
use YourAppName\Http\Controllers\Controller;

class LessonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Lesson::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Lesson::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Lesson::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $lesson->fill($request->all())->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        $lesson->delete();
    }
}

```

## Setup Fractal

Fractal is a library built to simplify and abstract transformation and formatting of our returned JSON responses.
It includes nesting of data and pagination out of the box and adds consistent HTTP responses. We will use a Laravel package called api-response to simplify this even more.

+ [Fractal](http://fractal.thephpleague.com/)
+ [Laravel api-response package](https://github.com/ellipsesynergie/api-response/)

To install the package all we need to do is:

```bash
composer require ellipsesynergie/api-response
```

And we need to add the package to our service providers in config/app.php:

app.php
```php
<?php
// A lot of stuff above...

return [

  'providers' => [
    // A lot of other providers before...
    
    EllipseSynergie\ApiResponse\Laravel\ResponseServiceProvider::class,
  ],
  
```

## Implement Transformers:

We can now create transformers for our data. Create a new folder "Transformers" inside the app folder and add the following files:

TeacherTransformer:
```php
<?php

namespace YourAppName\Transformers;

use YourAppName\Teacher;
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

```

LessonTransformer.php
```php
<?php

namespace YourAppName\Transformers;

use YourAppName\Lesson;
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

```

Now we need to update our controllers to use the new library and our transformers

TeachersController.php
```php
<?php

namespace YourAppName\Http\Controllers;

use Illuminate\Http\Request;

use YourAppName\Teacher;
use YourAppName\Http\Requests;
use EllipseSynergie\ApiResponse\Contracts\Response;
use YourAppName\Transformers\TeacherTransformer;

class TeachersController extends Controller
{
    /**
     * @param Response $response
     * @internal param $Response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::paginate(15);

        return $this->response->withPaginator(
            $teachers,
            new TeacherTransformer
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Teacher::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacher = Teacher::find($id);

        if(!$teacher) {
            return $this->response->errorNotFound('Teacher not found');
        }

        return $this->response->withItem(
            $teacher,
            new TeacherTransformer
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);

        if(!$teacher) {
            return $this->response->errorNotFound('Teacher not found');
        }

        $teacher->fill($request->all())->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teacher = Teacher::find($id);

        if(!$teacher) {
            return $this->response->errorNotFound('Teacher not found');
        }

        $teacher->delete();
    }
}

```

LessonsController.php
```php
<?php

namespace YourAppName\Http\Controllers;

use Illuminate\Http\Request;

use YourAppName\Lesson;
use YourAppName\Http\Requests;
use EllipseSynergie\ApiResponse\Contracts\Response;
use YourAppName\Transformers\LessonTransformer;

class LessonsController extends Controller
{
    /**
     * @param Response $response
     * @internal param $Response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $lessons = Lesson::paginate(15);

        return $this->response->withPaginator(
            $lessons,
            new LessonTransformer
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Lesson::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lesson = Lesson::find($id);

        if(!$lesson) {
            return $this->response->errorNotFound('Lesson not found');
        }

        return $this->response->withItem(
            $lesson,
            new LessonTransformer
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);

        if(!$lesson) {
            return $this->response->errorNotFound('Lesson not found');
        }

        $lesson->fill($request->all())->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if(!$lesson) {
            return $this->response->errorNotFound('Lesson not found');
        }

        $lesson->delete();
    }
}

```

## Validation

We should definitely validate all input data in our API. This is easy with Laravel. We are only going to present the simplest possible solution here, but you can experiment further with this on your own. We implement this in our store and update functions. We are only going to cover the TeachersController here.

TeachersController.php
```php

  public function store(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'name' => 'required|max:100|string',
          'email' => 'required|email',
          'funfact' => 'required|string',
          'age' => 'required|numeric|min:18|max:67',
      ]);

      if ($validator->fails()) {
          // TODO: Improve error messages ($validator->errors())
          return $this->response->errorWrongArgs("Validation failed");
      }

      Teacher::create($request->all());
  }

  public function update(Request $request, $id)
  {
      $teacher = Teacher::find($id);

      if(!$teacher) {
          return $this->response->errorNotFound('Teacher not found');
      }

      $validator = Validator::make($request->all(), [
          'name' => 'max:100|string',
          'email' => 'email',
          'funfact' => 'string',
          'age' => 'numeric|min:18|max:67',
      ]);
      
      // This will actually fail on update if not all inputs are present.
      // Should consider improving this.
      if ($validator->fails()) {
          // TODO: Improve error messages ($validator->errors())
          return $this->response->errorWrongArgs("Validation failed");
      }

      $teacher->fill($request->all())->save();
  }

```

## Cache

Sometimes its a good idea to cache your database results. This might be if your MySQL cache doesnt hold up or if you want to store other things together with your database results. No matter why its no problem to fix this easily in Laravel. This is just a really quick demo of the simplest possible solution. But it is infinitely extendable and its easy to configure with either memcached or redis.

TeachersController.php
```php

  // Lets cache a single teacher
  public function show(Request $request, $id)
  {
      // Bad
      $teacher = Cache::remember('teacher-' . $id . '-' . serialize($request->all()), 5, function() use ($id) {
          return Teacher::with('lessons')->find($id);
      });

      // Better
      // $teacher = Cache::tags(['teachers', 'teacher-' . $id])->remember('teacher-' . $id . '-' . serialize($request->all()), 5, function() use ($id) {
      //     return Teacher::with('lessons')->find($id);
      // });

      if(!$teacher) {
          return $this->response->errorNotFound('Teacher not found');
      }

      return $this->response->withItem(
          $teacher,
          new TeacherTransformer
      );
  }
  
  // Remember to flush on destroy or update
  public function destroy($id)
  {
      $teacher = Teacher::find($id);

      if(!$teacher) {
          return $this->response->errorNotFound('Teacher not found');
      }

      $teacher->delete();

      // Bad
      Cache::flush();

      // Better
      //Cache::tags('teacher-' . $id)->flush();
  }
  
```

## Documentation

Last but not least you should document your API. We think [apidocjs](http://apidocjs.com/) is awesome. But its up to you.

It can be installed with npm by doing:

```bash
npm install apidoc -g
```

Now we can add api doc to our controllers like this:

TeachersController.php
```php
  /**
   * @api {get} /teachers Get all teachers
   * @apiGroup Teachers
   * @apiVersion 1.0.0
   * @apiDescription Returns a listing of teachers
   *
   * @apiParam {String} [include=lessons] Include the teachers lessons
   *
   * @apiExample Example usage:
   * /teachers?include=lessons
   *
   * @apiSuccess {Number} id Teacher ID
   * @apiSuccess {String} name Name
   * @apiSuccess {String} email Email
   * @apiSuccess {String} funfact Something funny
   * @apiSuccess {Number} age Teachers age between 18 and 67
   * @apiSuccess {Object} lessons List of lessons
   *
   * @apiSuccessExample {json} Success-Response:
   *  HTTP/1.1 200 OK
   *      {
   *          "data": [
   *              {
   *                  "id": 6,
   *                  "name": "Bertram Senger II",
   *                  "email": "qNolan@hotmail.com",
   *                  "funfact": "Nisi occaecati aliquid molestiae necessitatibus in culpa. Praesentium dignissimos voluptatem ut quibusdam pariatur voluptas facilis. Est ipsum eligendi suscipit eum rem. Sint asperiores est exercitationem unde in.",
   *                  "age": 21,
   *                  "lessons": {
   *                      "data": [...]
   *                  }
   *              }
   *          ],
   *          "meta": {
   *              "pagination": {
   *                  "total": 1,
   *                  "count": 1,
   *                  "per_page": 15,
   *                  "current_page": 1,
   *                  "total_pages": 1,
   *                  "links": []
   *              }
   *          }
   *     }
   */

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      $teachers = Teacher::paginate(15);

      return $this->response->withPaginator(
          $teachers,
          new TeacherTransformer
      );
  }
  
```

Lets generate a public documentation by running:

```bash
apidoc -i app/Http/Controllers -o public/docs
```

Your docs should now be viewable on http://yourdomain.dev

# Deploy

Its time to deploy our server. We will use Laravel Forge for this. This requires that you already have an account at Digital Ocean and one at Laravel Forge. Log in to Forge and create a new server with the default options (you can choose the cheapest server aswell).

Now wait...

When its done go to "Sites -> YourNewServerWhatever" and setup your github repository. You need to choose "Custom" and then you need to copy the SSH key you get, login to GitHub and add the SSH key to your settings there. Copy your repository URL from GitHub and add it to the field in Forge.

Next you need to add your environment variables in Forge. Go to the "Environment" tab and click "Edit environment". 
You can copy most of the details from your local .env file but need to change the database name, user and password to the details you received in an email after starting your Forge server.

Now all should be good!

Bonus: You can setup auto deploy on push by copying the trigger url from Forge and add it to your GitHub repository settings.

# Next steps

+ [Envoyer](https://envoyer.io/) - Deployment with zero downtime
+ [Bugsnag](https://bugsnag.com/) - Catch your exceptions in a fashinable way
+ [Laracasts](https://laracasts.com/) - Learn more about Laravel
