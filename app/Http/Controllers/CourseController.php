<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show( $slug, $id)
    {

        $pageTitle = 'Apprendre';
        $pagesousTitle = 'Apprendre';
        $course = Course::with([
            'user',
            'category',
            'chapitres'=>function ($query) {
                $query->where('online', 1)
                    ->orderBy('position', 'asc');

            }
        ])->where(['id' => $id])->first();

        return view('courses.show', [
            'course' => $course,
            'pageTitle' => $pageTitle,
            'pagesousTitle' => $pagesousTitle
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }

    public function suivreLeCours( $slug, $id)
    {

        $pageTitle = 'Apprendre';
        $pagesousTitle = 'Apprendre';
        $course = Course::with([
            'user',
            'category',
            'chapitres'=>function ($query) {
                $query->where('online', 1)
                    ->orderBy('position', 'asc');

            }
        ])->where(['id' => $id])->first();



        return view('cours.suivre-le-cours', [
            'course' => $course,
            'pageTitle' => $pageTitle,
            'pagesousTitle' => $pagesousTitle
        ]);
    }


    public function achetercours( $slug, $id)
    {

        $course = Course::where(['id' => $id])->first();
        return view('cours.acheter-cours', [
            'course' => $course

        ]);
    }
}
