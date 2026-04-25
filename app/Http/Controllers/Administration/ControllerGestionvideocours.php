<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ControllerGestionvideocours extends Controller
{

    public function index()
    {
        $videos = Course::orderBy('created_at')->paginate(5);

        return view('administration.gestionvideocours.index',compact('videos'));
    }


    public function store(Request $request)
    {
        return view('administration.gestionvideocours.index');
    }

    public function create( Request $request )
    {
        $dataCond =  ['name' => '','slug' => '','image' => '','online'=>-1 ] ;
        $video = auth()->user()->courses()->firstOrCreate($dataCond);
        $video->load('chapitres');
        return view('administration.gestionvideocours.edit',compact('video'));
    }

    public function edit( Request $request ,$id )
    {
        $video = Course::with(['user','chapitres'])->find($id);

        return view('administration.gestionvideocours.edit',compact('video'));
    }

    public function destroy ( Request $request , $id )
    {

    }
    public function update ( Request $request , $id )
    {


        $request->validate([
            "name" => ["required",'max:255'],
        ]);
        $data = $request->all();
        $cours = Course::find($id);
        $data['online'] = ( $request->online == 0 ) ? 0 : 1;
        $data['slug'] = Str::slug($request->name);
        $cours->update($data);
        $cours->refresh();
        return response()->json(['success' => true,'message' => 'Mise a jour effectuee avec succes','video' => $cours   ]);
    }


    public function getCours( Request $request , $id )
    {
        $cours = Course::with([ 'chapitres'])->find($id);
        $cours->online = ( $cours->online == 0 ||  $cours->online == -1) ? false : true;
        return response()->json([
            'cours' => $cours,
            'success' => true
        ]) ;
    }
}
