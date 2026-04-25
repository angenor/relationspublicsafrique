<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestProfilStore;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function profil()
    {

        return view('user.profil') ;
    }


    public function update(RequestProfilStore $request)
    {



      $data['nom']=$request->nom;
      $data['prenom']=$request->prenom;
      $data['name']=$request->prenom;
      $data['slug']=Str::slug($request->prenom);
      $data['title']=$request->title;
      $data['fonction']=$request->fonction;
      $data['domaine']=$request->domaine;
      $data['email']=$request->email;
      $data['facebook']=$request->facebook;
      $data['twitter']=$request->twitter;
      $data['youtube']=$request->youtube;
      $data['linkding']=$request->linkding;
      $data['site']=$request->site;
      $data['contact']=$request->contact;
      $data['adresse']=$request->adresse;
      $data['tel']=$request->tel;
      $data['bio']=$request->bio;
      $data['online']=$request->online;
      $data['image']=$request->image;

      $request->user()->profil()->update($data) ;
      return response()->json(['success'=>true]) ;
    }

    public function saveimage( Request $request)
    {
        $request->validate([
            'image'=>['file']
        ]) ;


        $file = $request->file('image');
        if ($file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $filename =  'uploads/'.$filename;
            return response()->json(['success'=>true,'image'=>$filename]) ;
        } else {
            return response()->json(['success'=>false,'image'=>null]) ;
        }



    }

    public function show( $id)
    {

        $profil = Profil::with(['user','pays'])->find($id) ;

        return view('user.profilshow',compact('profil')) ;
    }



}
