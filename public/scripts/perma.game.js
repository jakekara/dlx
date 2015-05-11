/**
    
    Dyslexicon
    by Jake Kara
    jkara@g.harvard.edu
    CS50 final project
    Spring 2015

    perma.game.js
    
    JS controls for non-logged-in view
    of a game that can be shared via
    its URL

**/

var spectatorGameView = spectatorGameView || {};

$(function()
{
    // document has loaded
    console.log("loaded");
    /*
    var wordArray = [];
    // convert wordlist object to array of just the words
    for(var key in masterScript.wordList)
    {
        console.log(masterScript.wordList[key]);
        wordArray.push(masterScript.wordList[key].word);
    }
    console.log(masterScript.wordList);
    */
    masterScript.updateGlom(masterScript.wordList);

});
