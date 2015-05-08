@extends('layouts.master')
@section('page_js')
    <script type="application/javascript"> var appId = <?= env('FB_APPID'); ?>; </script>
    <script type="application/javascript"> var csrf_token = "{{ csrf_token() }}"</script>
    <script type="application/javascript" src="/scripts/guest.loginWithFacebook.js"></script>
@stop
@section('content')
<div class="jumbotron">
    <div class="container">
    
        <h1>Log in with Facebook</h1>

        <p id="status"></p>

        <fb:login-button id="fbLoginButton" scope="public_profile,email,user_friends" onlogin="loginWithFacebook.checkLoginState();">
        </fb:login-button>

    
    </div>
</div>

@stop