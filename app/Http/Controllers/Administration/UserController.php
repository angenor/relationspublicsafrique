<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profil( Request $request )
    {
        return view('administration.profil-admin');
    }
}
