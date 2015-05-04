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
        
    userGameView.refresh();
});


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