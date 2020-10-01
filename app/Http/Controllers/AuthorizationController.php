<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class AuthorizationController extends Controller
{
    public function authorize_access(Request $request){
        if($request->has('code') && $request->has('state') && $request->state == env('SPOTIFY_ACCESS_STATE', getenv('SPOTIFY_ACCESS_STATE'))){
            $code = $request->code;
            
            return redirect()->action('AuthorizationController@get_access', compact('code'));
        }

        return redirect()->action('AuthorizationController@get_access');
    }

    public static function get_access(Request $request){
        $refresh_token = Cookie::get('spotify_refresh_token');
        $spotify_access_code = Cookie::get('spotify_access_code');
        $user_id = Cookie::get('spotify_user_id');
        $user_country = Cookie::get('spotify_user_country');

        if($request->has('code') && $spotify_access_code === null){
            $code = $request->code;
            $spotify_access_code = Cookie::forever('spotify_access_code', $code);
            $data = null;

            $response = self::get_access_token('authorization_code', $code);

            if($response->getStatusCode() == 200){
                $data = json_decode($response->getContent());
                $refresh_token = Cookie::forever('spotify_refresh_token', $data->refresh_token);
            }

            $request = Http::withToken($data->access_token)->get('https://api.spotify.com/v1/me');

            $response = json_decode($request->getBody());
            $user_id = Cookie::forever('spotify_user_id', $response->id);
            $user_country = Cookie::forever('spotify_user_country', $response->country);
        }

        return redirect()->action('PagesController@better_release_radar')
            ->withCookie($spotify_access_code)
            ->withCookie($refresh_token)
            ->withCookie($user_id)
            ->withCookie($user_country);
    }

    public static function refresh_access(){
        $response = self::get_access_token('refresh_token');
        
        if($response->getStatusCode() == 200){
            $data = json_decode($response->getContent());
            return $data->access_token;
        }
        
        return false;
    }

    private static function get_access_token($grant_type, $spotify_access_code = null){
        
        $client_id = env('SPOTIFY_APP_CLIENT_ID', getenv('SPOTIFY_APP_CLIENT_ID'));
        $client_secret = env('SPOTIFY_APP_CLIENT_SECRET', getenv('SPOTIFY_APP_CLIENT_SECRET'));

        $form_params = [
            'grant_type' => $grant_type
        ];

        if($spotify_access_code !== null){
            $form_params['code'] = $spotify_access_code;
            $form_params['redirect_uri'] = env('APP_URL', getenv('APP_URL')) . '/authorize_access';
        }

        if($grant_type == 'refresh_token'){
            $form_params['refresh_token'] = Cookie::get('spotify_refresh_token');
        }

        $token = base64_encode( $client_id . ":" . $client_secret );
        $request = Http::asForm()->withToken($token,'Basic')->post('https://accounts.spotify.com/api/token', $form_params);

        $responseStatus = $request->getStatusCode();
        $response = json_decode($request->getBody());
        
        return response()->json($response, $responseStatus);
    }
}
