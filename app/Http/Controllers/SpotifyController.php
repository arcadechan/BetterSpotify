<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthorizationController;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp;

class SpotifyController extends Controller
{
    public function get_artists(Request $request){
        $access_token = AuthorizationController::refresh_access('refresh');

        $artists = [];

        // dd(Cookie::get('spotify_access_token'));

        $client = new GuzzleHttp\Client();
        $request = $client->get('https://api.spotify.com/v1/me/following?type=artist', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token
            ]
        ]);

        $responseStatus = $request->getStatusCode();
        $response = json_decode($request->getBody());

        if($responseStatus == 200){
            $artists = array_merge($artists, $response->artists->items);

            $totalArtistsFollowed = $response->artists->total;
            $nextUrl = $response->artists->next;
            $iterationsLeft = floor(($totalArtistsFollowed - 20) / 20);

            for($i = 0; $i < $iterationsLeft; $i++){
                $client = new GuzzleHttp\Client();
                $request = $client->get($nextUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token
                    ]
                ]);

                $response = json_decode($request->getBody());
                $artists = array_merge($artists, $response->artists->items);
                $nextUrl = $response->artists->next;
            }
        }

        $access_token_cookie = Cookie::forever('spotify_access_token', $access_token);
        
        return response()->json($artists, $responseStatus)->withCookie($access_token_cookie);
    }

    public function create_playlist(Request $request){

    }
}
