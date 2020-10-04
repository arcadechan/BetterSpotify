@extends('layouts.app')

@section('content')
    <section class="pt-4">
        <div class="container">
            <h1 class="text-center">about</h1>
            <hr>
            <div class="row d-flex align-items-center">
                <div class="d-none d-lg-block col-lg-3 vh-100">
                    <img class="mt-3 w-100" src="{{ url('/img/release-radar.jpg') }}" alt="" class="img-fluid">
                </div>
                <!-- end:col -->
                <div class="col-lg-9 pl-lg-5 pl-md-5">
                    <h2>
                        The issue with <em>Your Release Radar</em>.
                    </h2>
                    <p class="mt-4">
                        There are various staple features Spotify provides its users, and personalized playlists are one of them.
                        Some personalized playlists include <span class="title">Discover Weekly</span>, <span class="title">Daily Mixes</span>, and of course <span class="title">Your Release Radar</span>. 
                    <p>
                    <p>
                        These playlists are generated based on a user's listening behavior and help in the discovery of new music.
                        This user experience is ruined, however, when the artists recommended have nothing to do with the user's behavior.
                    </p>
                    <p>
                        <span class="title">Your Release Radar's</span> purpose is to provide the latest music from artists a user follows, and additionally some similar artists' singles picked for <em>the user.</em>
                        This playlist is generated weekly for Fridays, although a new generated playlist is ready the night before at 9pm PST.
                    </p>
                    <p>
                        It makes sense that not every artist a user follows is going to put out new music every week, so sometimes the playlist will recommend music from <em>similar</em> artists. That however is not the issue.
                        The issue with <span class="title">Your Release Radar</span> is that it pollutes your playlist with artists who have the same name as some of those you <em>do</em> follow.
                    </p>

                    <h4>An example</h4>
                    <p>
                        Let's choose a recognizable artist: The Beatles. We can assume if a user followed The Beatles on Spotify then they are following the English rock band from Liverpool. Lets assume somewhere out there in the world is another group that also happens to be called The Beatles.
                        Let's call The Beatles we all know from England <b>"The Beatles 1"</b> and the other lesser known group <b>"The Beatles 2"</b>. If a user happens to follow <b>"The Beatles 1"</b>, and <b>"The Beatles 2"</b> happens to have a new release,
                        Release Radar fails to differentiate the two, and displays the new release belonging to <b>"The Beatles 2"</b>, despite a user never following them.
                    </p>
                    <p>
                        <span class="title">Your Release Radar</span> is broken, and instead of searching for an artist by a unique identifier like an artist ID (something Spotify already gives to every artist), <span class="title">Your Release Radar</span> appears to be doing it by artist name.
                        This site aims to provide a better <span class="title">Your Release Radar</span>, that <em>does</em> search for artists based on ID, at the expense of losing <em>similar artists</em> (that's what Discover Weekly and Daily Mixes are for anyways). 
                    </p>
                    <h4>Note</h4>
                    <p>
                        There is an additional bug where, for example, <b>"The Beatles 2"</b> new release appears on <b>"The Beatles 1"</b> artist page, but that's entirely in Spotify's hands, as they are in charge of that functionality.
                        This application cannot take this issue into account as Spotify has failed to upload a song to the correct artist page. If you notice a wrong artist release by using this application, check the artist page first. The only scenario where that happens is when Spotify makes that error.
                    </p>

                    <hr class="mt-5">
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h2>List of API Endpoints Used</h2>
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
                                    <p>Used to create and manage your "Detoxed Release Radar" playlist on your account, whether it's set to public or private.</p>
                                </li>
                                <li>
                                    <h6>ugc-image-upload</h6>
                                    <p>Needed to upload your "Detoxed Release Radar's" playlist cover image (You're free to change this image at any time).</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- end:col -->
            </div>
        </div>
    </section>
@endsection