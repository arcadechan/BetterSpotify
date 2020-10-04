<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PagesController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function detoxed_release_radar(Request $request){
        
        if(Cookie::get('spotify_access_code') !== null) return view('detoxed_release_radar');

        $client_id = env('SPOTIFY_APP_CLIENT_ID', getenv('SPOTIFY_APP_CLIENT_ID'));
        $redirect_uri = env('APP_URL', getenv('APP_URL')) . env('AUTHORIZE_ACCESS_ENDPOINT');
        $scopes = "user-read-private ugc-image-upload user-follow-read playlist-modify-public playlist-modify-private";

        return view('detoxed_release_radar', compact([
            'client_id',
            'redirect_uri',
            'scopes'
        ]));
    
    }

    public function about(){
        return view('about');
    }

    public function credits(){
        return view('credits');
    }

    public function contact(){
        $recaptchaSiteKey = env('CAPTCHA_SITE_KEY', getenv('CAPTCHA_SITE_KEY'));

        return view('contact', ["recaptchaSiteKey" => $recaptchaSiteKey]);
    }

}
