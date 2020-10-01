<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthorizationController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SpotifyController extends Controller
{
    public function get_artists(Request $request){
        $access_token = AuthorizationController::refresh_access('refresh');

        $request = Http::withToken($access_token)->get('https://api.spotify.com/v1/me/following?type=artist');
        $responseStatus = $request->getStatusCode();

        $artists = [];
        
        if($responseStatus == 200){
            $response = json_decode($request->getBody());
            $artists = array_merge($artists, $response->artists->items);

            $junkArtistInfo = ['followers', 'genres', 'href', 'popularity']; //junk info to reduce the filesize of json payload set in local storage.

            $totalArtistsFollowed = $response->artists->total;
            $nextUrl = $response->artists->next;
            $iterationsLeft = floor(($totalArtistsFollowed - 20) / 20);

            for($i = 0; $i < $iterationsLeft; $i++){
                $request = Http::withToken($access_token)->get($nextUrl);
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

        ini_set('max_execution_time', 180);

        $user_id = Cookie::get('spotify_user_id');
        $spotify_playlist_id = Cookie::get('spotify_playlist_id');

        $access_token = AuthorizationController::refresh_access('refresh');

        if($spotify_playlist_id == NULL){

            //Create Playlist
            $request = Http::withToken($access_token)->post('https://api.spotify.com/v1/users/'.$user_id.'/playlists', [
                'name' => 'Better Release Radar',
                'description' => 'Finally, no more wrong artists.'
            ]);

            $response = json_decode($request->getBody());
            $spotify_playlist_id = Cookie::forever('spotify_playlist_id', $response->id);

            //Set playlist image
            $request = Http::withToken($access_token)->withBody(
                base64_encode(Storage::disk('public')->get('/img/better-release-radar.jpg')), 'image/jpeg'
            )->put('https://api.spotify.com/v1/playlists/'.$response->id.'/images');

            $response = json_decode($request->getBody());
        } else {

            //Follow the playlist in case a user has deleted it from their account. This reduces cluttering the playlist recovery list.
            $request = Http::withToken($access_token)->put('https://api.spotify.com/v1/playlists/'.$spotify_playlist_id.'/followers', []);

            //Delete contents in playlist before adding new songs.
            $request = Http::withToken($access_token)->put('https://api.spotify.com/v1/playlists/'.$spotify_playlist_id.'/tracks', [
                'uris' => []
            ]);
        }

        return response()->json([], 200)->withCookie($spotify_playlist_id);
    }

    public function inspect_artist(Request $request){
        $artist = $request->artist ?? null; //refactor
        $playlist_id = Cookie::get('spotify_playlist_id'); 
        $user_country = Cookie::get('spotify_user_country'); //refactor?

        
        $access_token = AuthorizationController::refresh_access('refresh');

        $oneMonthBack = date('Y-m-d', strtotime('-1 months')); //today minus one month
        $albums = [];
        $album_tracks = [];

        //junk info to reduce the filesize of json payload set in local storage.
        $junkAlbumInfo = ['album_group', 'available_markets', 'href', 'release_date_precision'];
        $junkTrackInfo = ['available_markets', 'disc_number', 'explicit', 'external_urls', 'href', 'is_local', 'type'];
        $junkTrackArtistInfo = ['external_urls', 'href', 'id', 'type', 'uri'];

        $artist_id = $artist['id'];

        $singlesRequest = Http::withToken($access_token)->get('https://api.spotify.com/v1/artists/'.$artist_id.'/albums', [
            'include_groups' => 'single',
            'limit' => 3
        ]);

        $albumsRequest = Http::withToken($access_token)->get('https://api.spotify.com/v1/artists/'.$artist_id.'/albums', [
            'include_groups' => 'album',
            'limit' => 3
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

                        $request = Http::withToken($access_token)->get('https://api.spotify.com/v1/albums/'.$album->id.'/tracks', [
                            'limit' => 50
                        ]);

                        $response = json_decode($request->getBody());
                        $tracks = $response->items;
                                    
                        foreach($tracks as $track){
                            foreach($junkTrackInfo as $info_key){
                                unset($track->{$info_key});
                            }

                            foreach($track->artists as $artist){
                                foreach($junkTrackArtistInfo as $info_key){
                                    if(isset($artist->{$info_key})) unset($artist->{$info_key});
                                }
                            }
                        }

                        $album_tracks[$album->id] = $tracks;

                        $track_uris = [];

                        //add tracks to playlist
                        foreach($tracks as $track){
                            $track_uris[] = $track->uri; 
                        }

                        $request = Http::withToken($access_token)->post('https://api.spotify.com/v1/playlists/'.$playlist_id.'/tracks', [
                            'uris' => $track_uris
                        ]);

                        $response = json_decode($request->getBody());
                    }
                }
            }
        }
        
        $data = [
            'albums' => $albums,
            'tracks' => $album_tracks
        ];

        return response()->json($data, 200);
    }
}
