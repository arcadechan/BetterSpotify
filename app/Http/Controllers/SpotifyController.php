<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthorizationController;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Storage;

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
        $artists = $request->artists ?? [];
        $user_id = Cookie::get('spotify_user_id');
        $playlist_id = Cookie::get('playlist_id');

        $access_token = AuthorizationController::refresh_access('refresh');

        if($playlist_id == NULL){

            //Create Playlist
            $client = new GuzzleHttp\Client();
            
            $request = $client->post('https://api.spotify.com/v1/users/'.$user_id.'/playlists', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'json' => [
                    'name' => 'Better Release Radar',
                    'description' => 'Finally, no more wrong aritsts.'
                ]
            ]);

            $request = json_decode($request->getBody());
            $playlist_id = Cookie::forever('playlist_id', $request->id);

            //Set playlist image
            $base64image = 'data:image/jpg;base64,' . base64_encode(Storage::disk('public')->get('/img/better-release-radar.jpg'));

            $client = new GuzzleHttp\Client();
            $request = $client->put('https://api.spotify.com/v1/playlists/'.$request->id.'/images',[
                'headers' => [
                    'Content-Type' => 'image/jpeg',
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'body' => base64_encode(Storage::disk('public')->get('/img/better-release-radar.jpg'))
            ]);

        } else {
            //delete contents in playlist
        }

        dd($artists);

        for($i = 0; $i < count($artists); $i++){
            $artist = $artists[1];

            $artist_id = $artist['id'];
            
        }

        return response()->json($response, $responseStatus)->withCookie($playlist_id);
    }
}
