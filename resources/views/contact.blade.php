@extends('layouts.app')

@section('content')
    <section class="pt-4">
        <div class="container">
            <div class="text-center">
                <h1>contact</h1>
                <p>If you have any questions or feedback, please drop me a line!</p>
                <small>Fields marked with an asterisk (*) are required.</small>
            </div>
            @if(Session::has('success'))
                <div class='alert alert-success'>
                    {{ Session::get('success') }}
                </div>
            @endif
            <contact-form :recaptcha_site_key="{{ json_encode($recaptchaSiteKey) }}"></contact-form>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection