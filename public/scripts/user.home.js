// user.home.js

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
}

/**
    handle new game creation status
**/
userHomeView.handleNewGame = function (data)
{
    console.log(data);
}

/**
    quit a game
**/
userHomeView.quitGame = function(e)
{
    $gameId = e.target.id.replace("quitGame_", "");
    console.log ("Quitting game " + $gameId);
    
    $.post("/game/quit/" + $gameId, {_token: csrf_token }, userHomeView.handleQuitGame );
}

/**
    handle quit game status
**/
userHomeView.handleQuitGame = function (data)
{
    console.log(data);
}