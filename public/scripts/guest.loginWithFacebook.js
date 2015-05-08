// guest.loginWithFacebook

// handle facebook login

// namespace technique via 
// http://elegantcode.com/2011/01/26/basic-javascript-part-8-namespaces/
// to avoid naming collisions 

var loginWithFacebook = loginWithFacebook || {};


/**
    run when document is loaded
**/
$(function(){
    // document has loaded
    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    
    // hide the login status, which is redundant on this page
    $("#loginStatus").hide();
    
    /**
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
          loginWithFacebook.statusChangeCallback(response);
        });
    }**/

    
    window.fbAsyncInit = function() {
    FB.init({
    appId      : appId,
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.2' // use version 2.2
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    loginWithFacebook.statusChangeCallback(response);
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));


});

// code via https://developers.facebook.com/docs/facebook-login/login-flow-for-web/v2.3

// This is called with the results from from FB.getLoginStatus().
loginWithFacebook.statusChangeCallback = function (response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    
    if (response.status === 'connected') 
    {
        // Logged into your app and Facebook.
        // save FB access token
        console.log ("Setting access token to " + response.authResponse.accessToken);
        console.log ("Your id is " + response.authResponse.userID);
        
        $("#status").html("Talking to Facebook. This shouldn't take long...");
        // send only facebook id and access token. we will not have name in response at this point
        $.post( "/facebook/updateUser", {facebookAccessToken: response.authResponse.accessToken, _token : csrf_token, facebookId: response.authResponse.userID}, loginWithFacebook.result); 
        
    } 
    else if (response.status === 'not_authorized') 
    {
        // The person is logged into Facebook, but not your app.
        document.getElementById('status').innerHTML = 'Please log ' +
            'in to this app.';
        $("#fbLoginButton").show();

    } else 
    {
        // The person is not logged into Facebook, so we're not sure if
        // they are logged into this app or not.
        document.getElementById('status').innerHTML = 'Please log ' +
            'in to Facebook.';
        $("#fbLoginButton").show();
    }
}


// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
loginWithFacebook.checkLoginState = function () 
{
    FB.getLoginStatus(function(response) {
      loginWithFacebook.statusChangeCallback(response);
    });
}

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
loginWithFacebook.testAPI = function() {
    console.log('Welcome!  Fetching your information.... ');
    
    FB.api('/me', function(response) {
    
        // send id and name. we won't have access token here.
        $.post( "/facebook/updateUser", { _token : csrf_token, facebookId: response.id, facebookName: response.name }, loginWithFacebook.finish); 
        console.log(response);
        console.log('Successful login for: ' + response.name);
        
       

    });



}
    
loginWithFacebook.result = function(data) {
    console.log("==RESULT==");

    console.log(data);

    console.log("==========");
    
    loginWithFacebook.testAPI();

}

loginWithFacebook.finish = function (response)
{
    console.log("finishing");
    console.log(response);
    //robj = JSON.parse(response);
    
    if (response.status == "SUCCESS")
    {            
        
       $("#status").html(
            'You are logged in as ' + response.facebook_name + '.'
            + " Now you can <a href='/home'>Start playing!</a>"
       ).fadeIn(400);

        $("#fbLoginButton").hide();
    }
}