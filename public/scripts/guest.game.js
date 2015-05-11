/**
    guest.game.js

    control spectator/guest game view

**/

var spectatorGameView = spectatorGameView || {};

$(function()
{
    // document has loaded

    var wordArray = [];
    // convert wordlist object to array of just the words
    for(var key in masterScript.wordList)
    {
        //console.log(masterScript.wordList[key]);
        wordArray.push(masterScript.wordList[key].word);
    }
    console.log(masterScript.wordList);
    
    
    // bind joinGame and acceptInvitation buttons to ajax
    $("#joinGameButton").click(spectatorGameView.joinGame);
    $("#acceptInvitationButton").click(spectatorGameView.acceptInvitation);
    $("#rejectInvitationButton").click(spectatorGameView.rejectInvitation);
    
    masterScript.updateGlom(masterScript.wordList);

});

/** 
    get word glom
**/
spectatorGameView.getGlom()
{
}

/**
    dump ajax resuts
**/
spectatorGameView.dumpResults = function(result)
{
    console.log(result);
    if (result.status == "SUCCESS")
    {
        masterScript.displayMessage(result.detailedStatus);
    }
}

/**
    reject an invitation
**/
spectatorGameView.rejectInvitation = function(e)
{
    e.preventDefault();
    console.log("rejecting invitation");
    $.post("/game/reject/invitation",  {user_id: user_id, game_id: game_id, _token : csrf_token }, spectatorGameView.dumpResults);
    $("#acceptInvitationButton").hide();
    $("rejectInvitationButton").hide()
    return false;
}

/**
    accept an invitation
**/
spectatorGameView.acceptInvitation = function(e)
{
    e.preventDefault();
    $.post("/game/accept/invitation",  {user_id: user_id, game_id: game_id, _token : csrf_token }, spectatorGameView.dumpResults);
    $("#acceptInvitationButton").hide();
    $("rejectInvitationButton").hide()
    return false;
}

/**
    request to join a game
**/
spectatorGameView.joinGame = function (e)
{
    e.preventDefault();
    console.log("Requesting to join game");
    $.post("/game/join",  {user_id: user_id, game_id: game_id, _token : csrf_token }, spectatorGameView.dumpResults);
    $("#joinGameButton").hide();
    return false;
}