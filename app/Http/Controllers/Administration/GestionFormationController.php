<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Chapitre;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GestionFormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $coursId, $chapitreId)
    {
        $request->validate([
            'name' => 'required|max:255',

        ]) ;
        $chapitre = Chapitre::find($chapitreId);
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['online'] = ( $request->online == 0 ) ? 0 : 1;
        $chapitre->update($data);

        return response()->json([
            'success' => true,
            'chapitre' => $chapitre,
            'message' => 'Mise a jour effectuee avec succes'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function  getChapitre( Request $request , $coursId, $chapitreId )
    {
//        dd($coursId,$chapitreId);

        if ($chapitreId == 0){
            $cours = Course::find($coursId)->chapitres()->firstOrCreate([ 'name' => '','slug' => '',  'online'=>-1]) ;

//            $cours = Chapitre::firstOrCreate() ;
        }else {
            $cours = Chapitre::find($chapitreId) ;
        }
        $cours->online = ( $cours->online == 0 ||  $cours->online == -1) ? false : true;
        return response()->json([
            'chapitre' => $cours,
            'success' => true
        ]) ;
    }
}
