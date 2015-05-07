/**
    guest.game.js

    control spectator/guest game view

**/

var spectatorGameView = spectatorGameView || {};

$(function()
{
    console.log("document loaded");
    // bind joinGame and acceptInvitation buttons to ajax
    $("#joinGameButton").click(spectatorGameView.joinGame);
    $("#acceptInvitationButton").click(spectatorGameView.acceptInvitation);
    $("#rejectInvitationButton").click(spectatorGameView.rejectInvitation);
});

/**
    dump ajax resuts
**/
spectatorGameView.dumpResults = function(result)
{
    console.log(result);
}

/**
    reject an invitation
**/
spectatorGameView.rejectInvitation = function(e)
{
    e.preventDefault();
    console.log("rejecting invitation");
    $.post("/game/reject/invitation",  {user_id: user_id, game_id: game_id, _token : csrf_token }, spectatorGameView.dumpResults);

    return false;
}

/**
    accept an invitation
**/
spectatorGameView.acceptInvitation = function(e)
{
    e.preventDefault();
    $.post("/game/accept/invitation",  {user_id: user_id, game_id: game_id, _token : csrf_token }, spectatorGameView.dumpResults);
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

    return false;
}