<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PagesController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function better_release_radar(Request $request){
        if(Cookie::get('spotify_access_code') !== null){

            

            return view('better_release_radar');
        } else {
            $client_id = env('SPOTIFY_APP_CLIENT_ID', getenv('SPOTIFY_APP_CLIENT_ID'));
            $redirect_uri = env('APP_URL', getenv('APP_URL')) . ":8000/authorize_access";
            $scopes = "user-follow-read playlist-modify-public";

            return view('better_release_radar', compact([
                'client_id',
                'redirect_uri',
                'scopes'
            ]));
        }
    }

    public function about(){
        return view('about');
    }

    public function contact(){
        return view('contact');
    }

    public function authorize_access(Request $request){
        if($request->has('code') && $request->has('state') && $request->state == env('SPOTIFY_ACCESS_STATE', getenv('SPOTIFY_ACCESS_STATE'))){
            $code = $request->code;
            $cookie = Cookie::forever('spotify_access_code', $code);
            return redirect()->action('PagesController@better_release_radar')->withCookie($cookie);
        }
        
        return redirect()->action('PagesController@better_release_radar');
    }
}
