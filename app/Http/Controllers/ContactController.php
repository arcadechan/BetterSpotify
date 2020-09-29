<?php

namespace App\Http\Controllers;

use App\Mail\Mail as ContactMessage;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function contact(Request $request){
        try {
            $form = $request->form ?? NULL;
            $reCAPTCHA = $request->reCAPTCHA ?? NULL;

            $client = new GuzzleHttp\Client();

            $apiRequest = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'query' => [
                    'secret' => env('CAPTCHA_SECRET_KEY'),
                    'response' => $reCAPTCHA
                ]
            ]);

            $response = json_decode($apiRequest->getBody());

            if($response->success && $form !== NULL){
                // $contactMessage = (object)[
                //     'name' => $name,
                //     'email' => $email,
                //     'message' => $message
                // ];
        
                // Mail::to('email', array(
                //     'name' => $name,
                //     'email' => $email,
                //     'message' => $message
                // ), function($message) use ($email){
                //     $message->from( $email );
                //     $message->to(env('MAIL_TO_NAME', getenv('MAIL_TO_NAME')))->subject('Message From BetterSpotify Contact Form');
                // });
        
                Mail::to(env('MAIL_TO_ADDRESS'))->send(new ContactMessage($form));
                
                return response()->json([], 200);
            }

            return response()->json([], 500);
        }catch(\Exception $e){
            return response($e->getMessage(), 500);
        }
    }
}
