@extends('layouts.app')

@section('content')
    <section class="pt-4">
        <div class="container">
            <h1 class="text-center pb-5">about</h1>
            <div class="row d-flex align-items-center">
                <div class="d-none d-lg-block col-lg-3 vh-100">
                    <img class="mt-3 w-100" src="{{ url('/img/release-radar.jpg') }}" alt="" class="img-fluid">
                </div>
                <!-- end:col -->
                <div class="col-lg-9 pl-lg-5 pl-md-5">
                    <h2>
                        The issue with Release Radar.
                    </h2>
                    <p class="mt-4">
                        There are various staple features Spotify provides its users, and personalized playlists are one of them.
                        Some personalized playlists include "Discover Weekly", "Daily Mixes", and of course "Release Radar". 
                    <p>
                    <p>
                        These playlists are generated based on a user's listening behavior and help in the discovery of new music.
                        This user experience is ruined, however, when the artists recommended have nothing to do with the user's behavior.
                    </p>
                    <p>
                        Release Radar's purpose is to provide the latest music from artists a user follows, and additionally some singles picked <em>for you.</em>
                        This playlist is generated weekly for Fridays, althought a new generated playlist is ready the night before at 9pm PST.
                    </p>
                    <p>
                        It makes sense that not every artist a user follows is going to put out new music every week, so sometimes the playlist will recommend music from <em>similar</em> artist. That however is not the issue.
                        The issue with Release Radar is that it recommends music from artists you follow, but only at first glance.
                    </p>
                    <p>
                        Let's choose a recognizable artist: The Beatles. We can assume if a user followed The Beatles on Spotify then they are following the English rock band from Liverpool. There aren't any other known groups by the exact same name but lets assume there was.
                        Let's call The Beatles we all know <b>The_Beatles_1</b> and the other lesser known group <b>The_Beatles_2</b>. If a user happens to follow <b>The_Beatles_1</b>, and <b>The_Beatles_2</b> happens to have a new release,
                        Release Radar fails to differentiate the two, and displays <b>The_Beatles_2's</b> new release, despite the user never following them.
                    </p>
                    <p>
                        Release Radar is broken, and instead of searching for an artist by a unique identifier like an artist ID (something Spotify has access to already), Release Radar appears to be doing it by the name alone, (something that is never reliable and thus should never be done).
                        This site aims to provide a better Release Radar, that <em>does</em> search for artists based on ID, at the expense of <em>similar artists</em>.
                    </p>
                    <p>
                        There is an additional bug, however, when <b>The_Beatles_2's</b> new release appears on <b>The_Beatles_1's</b> artist page, but that's entirely in Spotify's hands, as they are in charge of that functionality.
                        This application cannot take this issue into account as Spotify has failed to upload a song to the correct artist page.
                    </p>

                    <hr class="mt-5">
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h2>List of API Endpoints Used</h2>
                            <ul>
                                <li>
                                    <h6>user-follow-read</h6>
                                    <p>Used to find every artist a user follows.</p>
                                </li>
                                <li>
                                    <h6>playlist-modify-public</h6>
                                    <p>Used to create a new "Better Release Radar" playlist for the user.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- end:col -->
            </div>
            <hr class="mt-5">
        </div>
    </section>
@endsection