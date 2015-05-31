/**
    
    Dyslexicon
    by Jake Kara
    jkara@g.harvard.edu
    CS50 final project
    Spring 2015

    user.game.js
    
    JS controls for game view for player (as opposed to
    spectator).

**/

// namespace technique to avoid collisions via 
// http://elegantcode.com/2011/01/26/basic-javascript-part-8-namespaces/
var userGameView = userGameView || {};

// Use "ticks" to keep track of how many times
// page has automatically refreshed. After some point
// stop auto-refreshing and ask user if they're still there.
// This is meant to cut down on wasted bandwidth refreshing
// games when the browser window is just left open
userGameView.ticks = 0;
userGameView.maxTicks = 10 * 2; // 10 minutes @ 1 check every 30 seconds
userGameView.tickFrequencyInSeconds = 30;

/**
    run when document is loaded
**/
$(function(){
    // document has loaded
    
    $.ajaxSetup({ cache: true });
    
    $.getScript('//connect.facebook.net/en_US/sdk.js', function(){
        FB.init({
            appId: appId,
            version: 'v2.3' // or v2.0, v2.1, v2.0
        });     
        $('#loginbutton,#feedbutton').removeAttr('disabled');
        FB.getLoginStatus(userGameView.statusChangeCallback);
    });



    
    /*    
    
    The above jQuery is a lot less code
    
    // facebook setup code from https://developers.facebook.com/docs/facebook-login/login-flow-for-web/v2.3

    window.fbAsyncInit = function() {

        FB.init({
        appId      : appId,
        cookie     : true,  // enable cookies to allow the server to access 
                    // the session
        xfbml      : 'false', //true,  // parse social plugins on this page
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
        $("body").fadeIn();
        //loginWithFacebook.statusChangeCallback(response);
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

*/

        
    // set handler for playWord button
    $("#playWord").submit(userGameView.playWord);

    // set handler for accept and request links
    $(".acceptLink").click(userGameView.acceptRequest);
    $(".rejectLink").click(userGameView.rejectRequest);
    $(".inviteAppFriendButton").click(userGameView.inviteAppFriend);
    $(".inviteFriendToDyslexiconButton").click(userGameView.inviteFriendToDyslexicon);
    
    
    // prevent non-alphabetic input
    $('#wordTextInput').keypress(userGameView.preventNonLetters);
    
    // load game info every so often
    setInterval(userGameView.refresh, userGameView.tickFrequencyInSeconds * 1000);
    
    // and run that function once right now
    userGameView.refresh();
});



/**
    Handle facebook status change
**/

userGameView.statusChangeCallback = function (result)
{
    console.log("status change callback");
    $('head').append('<link rel="stylesheet" type="text/css" href="/css/master.css">');
}

/**
    dump the result of an ajax operation
**/
userGameView.dumpResult = function(result)
{
    console.log(result);
}

/**
    accept a request to join
**/
userGameView.acceptRequest = function(e)
{  
    e.preventDefault();
    console.log("Accepting request to join");
    var userId = e.target.id.replace("accept_", "");
    $.post( "/game/accept/request", {player_id: userId, game_id: game_id, _token : csrf_token }, userGameView.dumpResult);
    return false;
}

/**
    reject a request to join
**/
userGameView.rejectRequest = function(e)
{
    
    e.preventDefault();
    var userId = e.target.id.replace("reject_", "");
     $.post( "/game/reject/request", {user_id: userId, game_id: game_id, _token : csrf_token }, userGameView.dumpResult);
    return false;
}

/**
    invite a friend to join game
**/
userGameView.inviteAppFriend = function(e)
{
    e.preventDefault();
    var userId = e.target.id.replace("inviteAppFriend_", "");
    
    console.log("Inviting app friend " + userId);
    $.post( "/game/invite", {player_id: userId, game_id: game_id, _token : csrf_token }, userGameView.inviteAppFriendResult);
    return false;
    
    $("#inviteAppFriend_" + userId).html("Invitation sent");
}

/** 
    handle result of invite
**/
userGameView.inviteAppFriendResult = function (result)
{
    userGameView.dumpResult(result);
    if (result.status == 'SUCCESS')
    {
        $("#inviteAppFriend_" + result.playerId).parent().html("Invitation sent to " + result.playerName);
    }
}

/**
    inivite a friend to join app. This is someone who 
    is on facebook but hasn't authenticated the app.
**/
userGameView.inviteFriendToDyslexicon = function (e)
{
    e.preventDefault();
    console.log("e:" + $(e.target).parent().attr('id'));
    var userId = $(e.target).parent().attr('id').replace("inviteFriendToDyslexicon_", "");
    var url = "/facebook/invite/" + userId;
    console.log(url);
    $.post(url, { _token: csrf_token }, userGameView.inviteToAppResult);
    console.log ("Inviting " + userId + " to join Dyslexicon");
    return false;
}

/**
    Handle json from invite to app request
**/
userGameView.inviteToAppResult = function(result)
{
    userGameView.dumpResult(result);
    
    // phasing this kind kind of code out
    // since I learned that Laravel will 
    // automatically encode and decode json
    // objects when returning data with a view.
    // however, since I did a lot of code this
    // way already, I'm not removing all
    // instances of it yet. For now, it works,
    // and that is more important.
    robj = result;
    
    // if the invitation was 'successful',
    // that means we've added a record of the 
    // invitation to our database so the user 
    // can never be invited again, to cut down
    // on annoying people
    console.log("status " + robj.status); 
    if (robj.status == "SUCCESS")
    {
        console.log("Successful invitation")
        
        // delete the div containing the invitable friend
        // to prevent double-inviting someone.
        if (typeof(robj.divToDelete)!= 'undefined')
        {
            divToDelete = "#" + robj.divToDelete;
            console.log ("deleting " + divToDelete);
            $(divToDelete).hide();
            
            // now to send the actual invitation
            if (typeof(robj.friendId) != 'undefined')
            {
                
                // don't need to ask user if they're 
                // "sure" they want to send the invite
                // since this will load Facebook's invite
                // message box and prompt the user
                // to confirm
                FB.ui({
                    method: 'apprequests',
                    message: "Come test out Jake\'s CS50 final project, Dyslexicon. " +
                    "It\'s still being developed, so feel free to have fun and play, " + 
                    "but keep in mind that glitches are par for the course." ,
                    to: robj.friendId
                  }, 
                  userGameView.dumpResult
                );

            }
            
            return;
        }
        console.log("Failed invitation");
    }
}

/**
    prevent user from even entering non alphabetic input.
    Code adapted from https://stackoverflow.com/questions/13236651/allowing-only-alphanumeric-values
    I still find regex hard to memorize...
**/
userGameView.preventNonLetters = function(e)
{
    var regex = new RegExp("^[a-zA-Z]");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
}

/**
    attempt to play a word
**/
userGameView.playWord = function(event)
{
    // suppress reload
    event.preventDefault();
    $("#message").hide();
    // do some input checking
    console.log("Input: " + $("#wordTextInput").val().length);
    if ($("#wordTextInput").val().length < 4)
    {
        console.log("too short");
        
        masterScript.displayMessage("<strong>Ahem...</strong>That word, '" + $("#wordTextInput").val() + "', is too short. It's gotta be at least four letters.", "alert-danger");
    }
    // don't check for alphabetic-ness. 
    // if the user has forced bad input into 
    // this textbox, let them have the well-earned
    // satisfaction of being rejected by the server
    
    else
    {
        // send AJAX request
        $.post( "playWord", $( "#playWord" ).serialize(), userGameView.result );

    }
    
    // clear text field
    $("#wordTextInput").val("");
    
    // reset ticks so auto-refresh doesn't time out
    userGameView.ticks = 0;
    return false;
}

/**
    handle the result jquery
**/
userGameView.playWordResult = function(result)
{
    userGameView.refresh();
}

/**
    refresh game data
**/
userGameView.refresh = function()
{
    
    // if we haven't exceed max ticks
    // then get game data.
    // if we have, set a message checking
    // that the user has a pulse
    if (userGameView.ticks > userGameView.maxTicks)
    {
        masterScript.displayMessage("<strong>Still there?</strong> <a id='resumeTicking' class='snoozeAlarm' href='#'>Yes.</a> <a class='snoozeAlarm' href='#'>No.</a>", "alert-info");
        $("#resumeTicking").click(function(){
            // if yes is clicked, start ticking again.
            userGameView.ticks = 0;
        });
        
        $(".snoozeAlarm").click(function(){
            $("#message").html("");
        });
    }
    else
    {
        // get game data
        $.post( "/game/all", {user_id: user_id, game_id: game_id, _token : csrf_token }, userGameView.result);
        userGameView.ticks ++;
    }    
}

/**
    handle refresh data
**/
userGameView.result = function (result)
{
    console.log ("----[ RESULT ]----");

    console.log(result);
    
    var robj = result;
    

    // if we didn't get any response, return 
    if (typeof(robj) == 'undefined')
    {
        return;
    }

    // detect failures
    if (robj.status == "FAILURE")
    {
        masterScript.displayMessage(robj.detailedStatus, "alert-danger");
    }
    
    /** Update whatever info we receive **/
    
    
    $("#playWordButton").prop('disabled', false);
    /**
    --------------------------------------------------
    TAKING TURNS IS DISABLED. PLAY AS MUCH AS YOU LIKE
    --------------------------------------------------
    // I decided there's really no need to have users take turns
    // adding words. If you want to add five or six words in one
    // sitting, go for it, and you won't get hung up waiting for 
    // a friend to make a move.    
    // if we got a new turn, 
    // update whose turn it is
    // and if button is active or not
    if (typeof(robj.turn) != 'undefined')
    {
        
        if (robj.turn != user_id)
        {
            //disable button
            
            
            
            $("#playWordButton").prop('disabled', true);
            $("#playWordButton").prop("value", "It's not your turn");
         
        }
        else
        {
            // enable button
            $("#playWordButton").prop('disabled', false);
            $("#playWordButton").prop("value", "Add word");
        }
           
    }
    **/
    
    /** 
        no doing this anymore. Now we are bolding
        the player's name in list 
        **
    // update the name of the user who is up
    if (typeof(robj.turnName) != 'undefined')
    {
        $("#turn").html(robj.turnName);
    }*/
    
    // update friend list if we have it
    if (typeof(robj.friendList) != 'undefined')
    {
        console.log("got Friends List");
        
        $friendsListHtml = "<ul>";
        for (i = 0; i < robj.friendList.length; i++)
        {
            $friendsListHtml += "<li>" + robj.friendList[i] + "</li>";
        }
        $friendsListHtml += "</ul>";
    }
    
    // update word list if we have it
    if (typeof(robj.wordList) != 'undefined')
    {
        var wordListHtml = "";
        for (i = 0; i < robj.wordList.length; i++)
        {
            wordListHtml += "<li>" + robj.wordList[i] + "</li>";
        }
        
        $("#wordList").html(wordListHtml);
        $("#wordCount").html(robj.wordList.length);
        console.log( $("#wordCount").html() + " : " + robj.wordList.length);

        masterScript.updateGlom(robj.wordList);
        
    }
    
    // update player list if we have it
    
    if (typeof(robj.players) != 'undefined')
    {
        var newPlayerListHtml = "";

        for (i = 0; i < robj.players.length ; i++)
        {
            newPlayerListHtml += "<li ";
            if (robj.players[i].name == robj.turnName)
            {
                newPlayerListHtml += "class='playerIsUp'>";
            }
            else
            {
                newPlayerListHtml += ">";
            }
            newPlayerListHtml += robj.players[i].name;
            newPlayerListHtml += "</li>";
            
            $("#playerList").html(newPlayerListHtml);
        }
    }
    
    // update score if we have it
    if (typeof(robj.score) != 'undefined')
    {
        $("#score").html(robj.score);
    }
    
    // rewrite invites list
    // modify #invitesList
    if (typeof(robj.invites) != 'undefined')
    {
        invitesListHtml = "";
        for (i = 0; i < robj.invites.length; i++)
        {
            invitesListHtml = invitesListHtml +
                '<li><a class="inviteAppFriendButton" id="inviteAppFriend_' + robj.invitesList[i].id + '">Invite ' + robj.invitesList[i].name + '></a></li>';
        }
       $("#invitesList").html(invitesListHtml);
    }
    
    // rewrite requests list
    if (typeof(robj.requestsList) != 'undefined')
    {
        requestsListHtml = "";
   
        for (i = 0; i < robj.requestsList.length; i++)
        {
            requestsListHtml = requestsListHtml + 
                "<li>" + 
                robj.requestsList[i].name +
                '<a class="acceptLink" id="accept_' + robj.requestsList[i].id + '" href="#">Accept</a> | ' + 
                '<a class="rejectLink" id="reject_' + robj.requestsList[i].id + '" href="#">Reject</a>' + 
                "</li>";
        }
        $("#requestsListHtml").html(requestsListHtml);
    }
    
    console.log("-------------------");
    

}