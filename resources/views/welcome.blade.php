@extends('layouts.app')

@section('content')

<section class="intro">
    <div class="container">
        <div class="row d-flex  align-items-start">
            <div class="col-md-11">
                <h1 class="display-3 text-center text-lg-left">A tool for a better experience.</h1>
                <br>
                <h3 class="text-center text-lg-left">What does Detoxed Release Radar do?</h3>
                <ul>
                    <li><span class="title">Detoxed Release Radar</span> creates a custom <span class='title'>Your Release Radar</span> in your library.</li>
                    <li><span class="title">Detoxed Release Radar</span> fixes the issue with seeing wrong artist releases from artists you don't follow.</li>
                    <li><span class="title">Detoxed Release Radar</span> looks at the last 3 albums and 3 singles released within the last month by each artist you follow to add to your playlist.</li>
                    <li><span class="title">Detoxed Release Radar</span> includes complete singles and album releases, as opposed to Release Radar's 30 track limit.</li>
                </ul>
                <p class="text-center text-lg-left">If you'd like to know more about the purpose of this tool and the specifics of what it fixes, please visit the <a href="/about" class="link">About page</a>.</p>
                <br>
                <div class="d-block text-center">
                    <a href="/detoxed_release_radar" class="btn btn-spotify d-block d-md-inline">Get Started</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection