<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthorizationController;

class SpotifyController extends Controller
{
    public function get_artists(Request $request){
        AuthorizationController::refresh_access('refresh');
        

        return response()->json();
    }

    public function create_playlist(Request $request){

    }
}
