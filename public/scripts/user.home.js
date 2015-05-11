/**
    
    Dyslexicon
    by Jake Kara
    jkara@g.harvard.edu
    CS50 final project
    Spring 2015

    user.home.js
    
    JS controls for a logged-in user's
    home screen
    
**/

var userHomeView = userHomeView || {};


// javascript for logged in user's home page

$(function(){
    
    // document has loaded
    
    //bind buttons to ajax functions
    $("#startNewGameButton").click(userHomeView.newGame);
    $(".quitGameButton").click(userHomeView.quitGame);
    
});


/**
    create new game via ajax
**/
userHomeView.newGame = function()
{
    $.post("/game/new", {_token: csrf_token }, userHomeView.handleNewGame );
    $("#startNewGameButton").prop("disabled", true);
}

/**
    handle new game creation status
**/
userHomeView.handleNewGame = function (data)
{
    console.log(data);
    if (typeof(data.newGameId != 'undefined'))
    {
        window.location.replace("/game/" + data.newGameId);
    }
}

/**
    quit a game
**/
userHomeView.quitGame = function(e)
{
    $gameId = e.target.id.replace("quitGame_", "");
    console.log ("Quitting game " + $gameId);
    
    $.post("/game/quit/" + $gameId, {_token: csrf_token }, userHomeView.handleQuitGame );

    $(this).parent().parent().html("");
}

/**
    handle quit game status
**/
userHomeView.handleQuitGame = function (data)
{
    console.log(data);
    if (data.status)
    {
        masterScript.displayMessage(data.detailedStatus, 'alert-danger');
    }
}
