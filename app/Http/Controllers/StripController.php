<?php

namespace App\Http\Controllers;

use App\Models\AchatCours;
use App\Models\Cart;
use App\Notifications\AchatcoursNatifications;
use Exception;
use Illuminate\Http\Request;
use Intervention\Image\Exception\NotFoundException;
use Stripe\Stripe;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripController extends Controller
{

    public function index()
    {
        return view('strip.index');
    }

    public function checkout( Request $request)
    {


        $course_id =  $request->get('course_id');

        $existCours =  \App\Models\AchatCours::where(['course_id'=>$course_id, 'user_id'=>$request->user()->id,'status'=>'payer'])->exists() ;

        if ($existCours){
            return redirect()->back()->with('danger', 'Cours deja achete');
        }


        $course = \App\Models\Course::findOrFail($course_id);
        $montant =  $course->price * 100 ;
//        dd($montant);

        Stripe::setApiKey(env("STRIPE_SECRET"));
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $course->name,
                            'images' => [ $course->img ],
                            'description' => $course->description,
                        ],
                        'unit_amount' => $montant,
                    ],
                    'quantity' => 1,
                ],
            ],
            'metadata' => ['course_id' => $course->id],
            'mode' => 'payment',
            'success_url' => route('user.payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('user.payment.cancel'),
        ]);

        $achatCourse = \App\Models\AchatCours::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'stripe_session_id' => $session->id,
            'reference' => $session->id,
            'montant' => $montant,
            'status'=>'en-attente'
        ]);
        return redirect()->away($session->url);
    }

    public function success( Request $request)
    {


//        AchatCours::truncate();
//        Cart::truncate();
        $message = '';
        $achatCourse = '';
        try{
            $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'));

            if (!$session || $session->payment_status !== 'paid') {
                throw new  NotFoundHttpException()  ;
            }

//            $achatCourse  = \App\Models\AchatCours::where(['stripe_session_id'=>$session->id, 'user_id'=>$request->user()->id,'status'=>'en-attente'])->first() ;
            $achatCourse  = \App\Models\AchatCours::where(['course_id'=>$session->metadata->course_id, 'user_id'=>$request->user()->id,'status'=>'en-attente'])->first() ;
//            $achatCourse  = \App\Models\AchatCours::where(['course_id'=>$session->metadata->course_id, 'user_id'=>$request->user()->id,'status'=>'payer'])->firstOrFail() ;

            if(!$achatCourse){
                throw  new NotFoundHttpException() ;
            }



//            dd($achatCourse->toArray());
            $achatCourse->status = 'payer';
            $achatCourse->save();
            $achatCourse->load('course');

            $request->user()->notify(new AchatcoursNatifications($achatCourse));
            $message = "Abonnement souscrit avec succes !" ;

        }catch (Exception $exception){
            $message = $exception->getMessage();
//            dd($message);
            throw new NotFoundHttpException($message);
        }
        return view('strip.success',compact('message','achatCourse'));
    }
    public function cancel()
    {

    }
}
