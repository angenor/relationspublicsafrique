<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCoursAchete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!auth()->check()) {
            return redirect('/login')->with('danger', 'Veuillez vous connecter');
        }
        $existsCoursAchete = \App\Models\Course::hasUserAbonned(\App\Models\Course::find($request->id));


        if(!$existsCoursAchete){
           return redirect('/acheter/cours/'.$request->slug.'-'.$request->id.'/')->with('danger', "Merci de vous inscrire pour pouvoir acheter ce cours");
        }

        return $next($request);
    }
}
