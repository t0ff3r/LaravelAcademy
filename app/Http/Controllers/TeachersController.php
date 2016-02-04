<?php

namespace LaravelAcademy\Http\Controllers;

use Illuminate\Http\Request;

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