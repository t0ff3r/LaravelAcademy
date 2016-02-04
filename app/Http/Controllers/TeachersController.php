<?php

namespace LaravelAcademy\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use LaravelAcademy\Teacher;
use LaravelAcademy\Http\Requests;
use EllipseSynergie\ApiResponse\Contracts\Response;
use LaravelAcademy\Transformers\TeacherTransformer;

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

        $validator = Validator::make($request->all(), [
            'name' => 'max:100|string',
            'email' => 'email',
            'funfact' => 'string',
            'age' => 'numeric|min:18|max:67',
        ]);

        if ($validator->fails()) {
            // TODO: Improve error messages ($validator->errors())
            return $this->response->errorWrongArgs("Validation failed");
        }

        $teacher->fill($request->all())->save();


        // Bad
        Cache::flush();

        // Better
        //Cache::tags('teacher-' . $id)->flush();
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

        // Bad
        Cache::flush();

        // Better
        //Cache::tags('teacher-' . $id)->flush();
    }
}
