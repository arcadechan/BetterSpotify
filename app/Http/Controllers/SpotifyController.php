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

        //junk info to reduce the filesize of json payload set in local storage.
        $junkArtistInfo = ['followers', 'genres', 'href', 'popularity'];

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

            //clean up artists by removing unnecessary fields
            foreach($artists as $artist){
                foreach($junkArtistInfo as $info_key){
                    unset($artist->{$info_key});
                }
            }
        }

        $access_token_cookie = Cookie::forever('spotify_access_token', $access_token);
        
        return response()->json($artists, $responseStatus)->withCookie($access_token_cookie);
    }

    public function create_playlist(Request $request){

        $artists = $request->artists ?? [];
        $user_id = Cookie::get('spotify_user_id');
        $spotify_playlist_id = Cookie::get('spotify_playlist_id');
        $user_country = Cookie::get('spotify_user_country');

        $access_token = AuthorizationController::refresh_access('refresh');

        if($spotify_playlist_id == NULL){

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
            $spotify_playlist_id = Cookie::forever('spotify_playlist_id', $request->id);
            $temp_playlist_id = $request->id;

            //Set playlist image
            $client = new GuzzleHttp\Client();
            $request = $client->put('https://api.spotify.com/v1/playlists/'.$request->id.'/images',[
                'headers' => [
                    'Content-Type' => 'image/jpeg',
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'body' => base64_encode(Storage::disk('public')->get('/img/better-release-radar.jpg'))
            ]);

        } else {

            //Follow the playlist in case a user has deleted it from their account
            $client = new GuzzleHttp\Client();
            $request = $client->put('https://api.spotify.com/v1/playlists/'.$spotify_playlist_id.'/followers',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'json' => []
            ]);

            //delete contents in playlist
            $client = new GuzzleHttp\Client();
            $request = $client->put('https://api.spotify.com/v1/playlists/'.$spotify_playlist_id.'/tracks', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'json' => [
                    'uris' => []
                ]
            ]);
        }

        $oneMonthBack = date('Y-m-d', strtotime('-1 months')); //today minus one month
        $albums = [];
        $album_tracks = [];
        $playlist_id = isset($temp_playlist_id) ? $temp_playlist_id : $spotify_playlist_id;

        //junk info to reduce the filesize of json payload set in local storage.
        $junkAlbumInfo = ['album_group', 'available_markets', 'href', 'release_date_precision'];
        $junkTrackInfo = ['artists', 'available_markets', 'disc_number', 'explicit', 'external_urls', 'href', 'is_local', 'type'];

        for($i = 0; $i < count($artists); $i++){            
            $artist = $artists[$i];

            $artist_id = $artist['id'];

            
            $client = new GuzzleHttp\Client();
            $singlesRequest = $client->get('https://api.spotify.com/v1/artists/'.$artist_id.'/albums', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'query' => [
                    'include_groups' => 'single',
                    'limit' => 3
                ]
            ]);

            $albumsRequest = $client->get('https://api.spotify.com/v1/artists/'.$artist_id.'/albums', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'query' => [
                    'include_groups' => 'album',
                    'limit' => 3
                ]
            ]);

            if($singlesRequest->getStatusCode() == 200 && $albumsRequest->getStatusCode() == 200){
                $singlesResponse = json_decode($singlesRequest->getBody());
                $albumsResponse = json_decode($albumsRequest->getBody());

                $artist_albums = array_merge($singlesResponse->items, $albumsResponse->items);
                
                if(count($artist_albums) > 0){
                    foreach($artist_albums as $key => $album){
                        $release_date = $album->release_date;

                        if($release_date >= $oneMonthBack && in_array($user_country, $album->available_markets)){
                            foreach($junkAlbumInfo as $info_key){
                                unset($album->{$info_key});
                            }

                            $albums[] = $album;

                            //get an albums tracks and add to playlist
                            $client = new GuzzleHttp\Client();
                            $request = $client->get('https://api.spotify.com/v1/albums/'.$album->id.'/tracks', [
                                'headers' => [
                                    'Authorization' => 'Bearer ' .$access_token
                                ],
                                'query' => [
                                    'limit' => 50
                                ]
                            ]);

                            $response = json_decode($request->getBody());
                            $tracks = $response->items;

                            foreach($tracks as $track){
                                foreach($junkTrackInfo as $info_key){
                                    if(isset($track->{$info_key})) unset($track->{$info_key});
                                }
                            }

                            $album_tracks[$album->id] = $tracks;

                            $track_uris = [];

                            //add tracks to playlist
                            foreach($tracks as $track){
                                $track_uris[] = $track->uri; 
                            }

                            $client = new GuzzleHttp\Client();
                            $request = $client->post('https://api.spotify.com/v1/playlists/'.$playlist_id.'/tracks',[
                                    'headers' => [
                                        'Content-Type' => 'application/json',
                                        'Authorization' => 'Bearer ' . $access_token
                                    ],
                                    'json' => [
                                        'uris' => $track_uris
                                    ]
                            ]);
                        }
                    }
                }
            }
            
            $data = [
                'albums' => $albums,
                'tracks' => $album_tracks
            ];
            
        }

        return response()->json($data, 200)->withCookie($spotify_playlist_id);
    }
}
