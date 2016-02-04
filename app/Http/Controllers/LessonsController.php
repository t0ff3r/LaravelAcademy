<?php

namespace LaravelAcademy\Http\Controllers;

use Illuminate\Http\Request;

use LaravelAcademy\Lesson;
use LaravelAcademy\Http\Requests;
use EllipseSynergie\ApiResponse\Contracts\Response;
use LaravelAcademy\Transformers\LessonTransformer;

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
