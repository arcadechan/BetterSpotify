@extends('layouts.app')

@section('content')

<section class="intro">
    <div class="container">
        <div class="row d-flex  align-items-start">
            <div class="col-md-11">
                <h1 class="display-3">A tool for a <em>better</em> Spotify experience.</h1>
                <br>
                <h3>What does Better Release Radar do?</h3>
                <ul>
                    <li><span class="title">Better Release Radar</span> creates a custom <span class='title'>Your Release Radar</span> straight into your account.</li>
                    <li><span class="title">Better Release Radar</span> fixes the issue with seeing wrong artist releases from artists you don't follow (caused by other artists having the same name as some of those you do.)</li>
                    <li><span class="title">Better Release Radar</span> looks at the last 3 albums and 3 singles released by each artist you follow and if they've been released within the last month, they will be added to your playlist.</li>
                </ul>
                <p>If you'd like to know more about the purpose of this tool and the specifics of what it fixes, please visit the <a href="/about" class="text-spotify link">About page</a></p>
                <br>
                <a href="/better_release_radar" class="btn btn-spotify">Get Started</a>
            </div>
        </div>
    </div>
</section>

@endsection