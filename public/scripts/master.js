/**
    master.js
**/

$(function()
{
    $("#pageContent").fadeIn();
    
    // code via http://laravel.com/docs/master/routing
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }});
    
    
    /*
    // load facebook stuff
    // via https://developers.facebook.com/docs/javascript/quickstart/v2.3
    window.fbAsyncInit = function() {
    FB.init({
    appId      : <?= env('FB_APPID'); ?>,
    xfbml      : true,
    version    : 'v2.3'
    });
    };

    (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));*/
});



var masterScript = masterScript || {};

masterScript.wordListToGlomHtml = function (wordList)
{
    glomHtml = "";
    
    startOfOverlap = 0;
    endOfOverlap = 0;

    giantWord = "";
    for (i = 0; i < wordList.length; i++)
    {
        giantWord = giantWord + wordList[i];
        
        // determine how much of the current word
        // overlaps the next
        
        // ... if there is a next word
        if( i+1 < wordList.length)
        {
            console.log(masterScript.overlapIndex (wordList[i], wordList[i+1]));
        }
        glomHtml += "<span class='glomItem'>" +
            wordList[i] + 
            "</span>";
    }
    
    return glomHtml;
}

/**
    Determine the index of the overlap
    example:
        first word: computer
        second word: terestrial
        
        index is 5
**/
masterScript.overlapIndex = function (firstWord, secondWord)
{
    console.log(firstWord + " " + secondWord);
    shortest = Math.min(firstWord.length, secondWord.length);
   
    // can only overlap by the length of the shortest, minus two
    // since at least two characters must be added
    for (i = 0; i < shortest - 2; i++)
    {
        // compare the last i letters in firstWord
        // to the first i letters in secondWord
        if (firstWord.substring(firstWord.length - 1 - i, firstWord.length - 1) ==
            secondWord.substring(0, i))
        {
            return i;
        }
        
    }
    return -1;
}