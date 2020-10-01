<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function contact(Request $request){
        try {
            $form = $request->form ?? NULL;
            $reCAPTCHA = $request->reCAPTCHA ?? NULL;

            $verifyCaptcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('CAPTCHA_SECRET_KEY'),
                'response' => $reCAPTCHA
            ]);

            $response = json_decode($verifyCaptcha->getBody());

            if($response->success && $form !== NULL){
        
                Mail::to(env('MAIL_TO_ADDRESS'))->send(new ContactMessage($form));
                
                return response()->json([], 200);
            }

            return response()->json([], 500);
        }catch(\Exception $e){
            return response($e->getMessage(), 500);
        }
    }
}
