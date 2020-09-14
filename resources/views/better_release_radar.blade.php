@extends('layouts.app')

@section('content')
    <section class="pt-4">
        <div class="container">
        <h1 class="text-center">better release radar</h1>
            @if(Cookie::get('spotify_access_code') !== null)
                <playlist-generator></playlist-generator>
            @else
                <div id="pre-authentication" class="d-flex flex-column align-items-center px-5">
                    <div class="d-inline-block mb-4">
                        <p>
                            Hit the following button to authenticate this service with Spotify to generate your Better Release Radar playlist! The authentication will ask for two simple permissions:
                        </p>
                        <ul>
                            <li>
                                <h6>user-read-private</h6>
                                <p>Used to get your username, necessary to then save a playlist to your account.</p>
                            </li>
                            <li>
                                <h6>user-follow-read</h6>
                                <p>Used to find all the artists you are following.</p>
                            </li>
                            <li>
                                <h6>playlist-modify-public</h6>
                            </li>
                            <li>
                                <h6>playlist-modify-private</h6>
                                <p>Used to create and manage your "Better Release Radar" playlist on your account, whether it's set to public or private.</p>
                            </li>
                            <li>
                                <h6>ugc-image-upload</h6>
                                <p>Needed to upload your "Better Release Radar's" playlist cover image (You're free to change this image at any time).</p>
                            </li>
                        </ul>
                    </div>
                    <div class="d-inline-block mb-5">
                        <form method="GET" action="https://accounts.spotify.com/authorize">
                            <input type="hidden" name="client_id" value="{{ $client_id ?? ''}}">
                            <input type="hidden" name="response_type" value="code">
                            <input type="hidden" name="redirect_uri" value="{{ $redirect_uri ?? ''}}">
                            <input type="hidden" name="scope" value="{{ $scopes ?? ''}}">
                            <input type="hidden" name="state" value="34fFs29kd09">
                            <button class="btn btn-spotify m-1">Login With Spotify</button>
                        </form>
                    </div>
                    <div class="d-inline-block">
                        <p>
                            <strong>Reminder:</strong> As a Spotify user you can revoke this applications permissions at any time, or any application you've authenticated before, by visiting <a href="https://www.spotify.com/us/account/apps/" target="_blank">https://www.spotify.com/us/account/apps/</a> and removing access.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection