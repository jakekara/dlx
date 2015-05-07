// user.game.js

// javascript for logged in user's game

// namespace technique via 
// http://elegantcode.com/2011/01/26/basic-javascript-part-8-namespaces/
// to avoid naming collisions 

var userGameView = userGameView || {};


/**
    run when document is loaded
**/
$(function(){
    // document has loaded
    console.log("document loaded");
    
    // set handler for playWord button
    $("#playWord").submit(userGameView.playWord);

    // set handler for accept and request links
    $(".acceptLink").click(userGameView.acceptRequest);
    $(".rejectLink").click(userGameView.rejectRequest);
    $(".inviteAppFriendButton").click(userGameView.inviteAppFriend);
    $(".inviteFriendToDyslexButton").click(userGameView.inviteFriendToDyslex);
    userGameView.refresh();
});

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
    userId = e.target.id.replace("accept_", "");
    $.post( "/game/accept/request", {player_id: userId, game_id: game_id, _token : csrf_token }, userGameView.dumpResult);
    return false;
    
}

/**
    reject a request to join
**/
userGameView.rejectRequest = function(e)
{
    
    e.preventDefault();
    userId = e.target.id.replace("reject_", "");
     $.post( "/game/reject/request", {user_id: userId, game_id: game_id, _token : csrf_token }, userGameView.dumpResult);
    return false;
}

/**
    invite a friend to join game
**/
userGameView.inviteAppFriend = function(e)
{
    e.preventDefault();
    userId = e.target.id.replace("inviteAppFriend_", "");
    
    console.log("Inviting app friend " + userId);
    $.post( "/game/invite", {player_id: userId, game_id: game_id, _token : csrf_token }, userGameView.dumpResult);
    return false;
}

/**
    inivite a friend to join app
**/
userGameView.inviteFriendToJoinDyslex = function (e)
{
    e.preventDefault();
    userId = e.target.id.replace("inviteToDyslex_", "");
    console.log ("Inviting " + $userId + " to join Dyslexicon");
    return false;
}

/**
    attempt to play a word
**/
userGameView.playWord = function(event)
{
    // suppress reload
    event.preventDefault();

    // TODO - check that input is textual and length >= 4
 
    
    // send AJAX request
    $.post( "playWord", $( "#playWord" ).serialize(), userGameView.playWordResult );
    
    // clear text field
    $("#wordInput").val("");
    
    return false;
}

// handle the result jquery
userGameView.playWordResult = function(result)
{
    userGameView.refresh();
}

// refresh turn, words, button enabled/disabled
userGameView.refresh = function()
{
    // TODO
    $.post( "all", {user_id: user_id, game_id: game_id, _token : csrf_token }, userGameView.result);

}

/**
    handle refresh data
**/
userGameView.result = function (result)
{
    console.log ("----[ RESULT ]----");
    console.log(result);
    
    // parse return text as object
    var robj = JSON.parse(result);
    
    // if we didn't get any response, return 
    if (typeof(robj) == 'undefined')
    {
        return;
    }

    // detect errors
    if (robj.status == "ERROR")
    {
        // TODO - Handle errors
    }
    
    /** Update whatever info we receive **/
    
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
            //enable button
            $("#playWordButton").prop('disabled', false);
            $("#playWordButton").prop("value", "Add word");
            
        }
    }
    
    // update the name of the user who is up
    if (typeof(robj.turnName) != 'undefined')
    {
        $("#turn").html(robj.turnName);
    }
    
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
        wordListHtml = "<ul>";
        for (i = 0; i < robj.wordList.length; i++)
        {
            wordListHtml += "<li>" + robj.wordList[i] + "</li>";
        }
        wordListHtml += "</ul>";
        
        $("#wordList").html(wordListHtml);
        $("#wordCount").html(wordList.length);
    }
    
    // update player list if we have it
    if (typeof(robj.players) != 'undefined')
    {
        // TODO
        console.log("Updated player list: " + robj.players);
    }
    
    // update score if we have it
    if (typeof(robj.score) != 'undefined')
    {
        $("#score").html(robj.score);
    }
    
    
    console.log("-------------------");
    

}