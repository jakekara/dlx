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
    var glomHtml = "";
    
    // where the current word starts to overlap with the next
    var startOfOverlap = 0;
    
    // where the current word no longer overlaps with the previous
    var endOfOverlap = 0;
    
    var cutoff = 0
    giantWord = "";
    for (var i = 0; i < wordList.length; i++)
    {
        giantWord = giantWord + wordList[i];
        
        // determine how much of the current word
        // overlaps the next
        word = wordList[i];
        // ... if there is a next word
        if( i+1 < wordList.length )
        {
            startOfOverlap = masterScript.overlapIndex (giantWord, wordList[i+1]);
        }
        else 
        {
            startOfOverlap = 0;
        }
        

        
        var newHtml = "";


        newHtml = 
            "<span class='glomItem'>" +
            "<span hidden class='glomFullWord'>" + 
                word + 
            "</span>";
        newHtml += 
            "<span class='glomWithoutEnd'>" + 
            word.substring(0, word.length - Math.min(startOfOverlap, word.length)) +
            "</span>";
        newHtml += 
            "<span hidden class='glomWithoutStartOrEnd'>" ;
            if (endOfOverlap < word.length - startOfOverlap && i+1 < wordList.length)
            {
                newHtml += 
                    word.substring(endOfOverlap, word.length - startOfOverlap);
            }
            else if (endOfOverlap == word.length - startOfOverlap && i+1 < wordList.length)
            {
                newHtml += "";
            }
            else if (endOfOverlap > word.length - startOfOverlap && i+1 < wordList.length)
            {
                newHtml += ""; //word.substring( endOfOverlap, word.length - startOfOverlap);
                newHtml += "</span><span hidden class='glomCutFromNext'>" + word.substring( endOfOverlap, word.length - startOfOverlap);
            }
            else if (i+1 == wordList.length)
            {
                newHtml +=
                    word.substring(endOfOverlap, word.length);
            }
        newHtml += 
            "</span>";
        newHtml +=
            "<span hidden class='glomWithoutStart'>" + 
            word.substring(endOfOverlap, word.length) +
            "</span>" + 
            "</span>";

        
        
        
        glomHtml +=  newHtml;
        
        endOfOverlap = startOfOverlap;

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
    shortest = Math.max(firstWord.length, secondWord.length);
   
    // can only overlap by the length of the shortest, minus two
    // since at least two characters must be added
    for (j = 2; j < shortest; j++)
    {
        if(firstWord.substring(firstWord.length - j, firstWord.length) ==
            secondWord.substring(0, j))
        {
            return j;
        }
    }
    
    return -1;
}

/**
    Display a bootstrap message
**/
masterScript.displayMessage = function(message, messageType)
{
    console.log("alert: " + message);
    
    $("#message").html(
        
        '<div class="alert ' + messageType + '">' + 
        '<a href="#" class="close" data-dismiss="alert">&times;</a>' + 
        message + 
        '</div>'
    );
    $("#message").show();
}

/**
    Make "#glom" div all fancy
**/

masterScript.updateGlom = function (wordList)
{
    
    console.log("<<<");
    console.log(wordList);
    console.log(">>>");
    $("#glom").html(masterScript.wordListToGlomHtml(wordList));
        var hiddenText = "";
    
        $(".glomItem").hover(function()
        {
            // magnify individual words
            
            $(this).hover(function()
            {       
                if ($(this).hasClass("glomIn"))
                {
                    return;
                }
                
                $(this).addClass("glomIn");
                
                $(this).children().hide();
                $(this).find(".glomFullWord").show();
                
                if ($(this).next().hasClass("glomItem"))
                {
                    $(this).next().children().hide();
                    $(this).next().find(".glomWithoutStartOrEnd").show();
                    if (!$.isEmptyObject($(this).next().find(".glomCutFromNext").html()))
                    {
                        if ($(this).next().find(".glomCutFromNext").html().length > 0)
                        {
                            // if there is text to cut from the next word...
                            $(this).next().next().find(".glomWithoutEnd").html(
                                $(this).next().next().find(".glomWithoutEnd").html().substring($(this).next().find(".glomCutFromNext").html().length)
                            );
                        }
                    }
                    
                }
                
                if ($(this).prev().hasClass("glomItem"))
                {
                    $(this).prev().children().hide();
                    $(this).prev().find(".glomWithoutEnd").show();
                }
                
               

            }, function()
            {
                // ensure the mousein action was caught
                if (!$(this).hasClass("glomIn"))
                {
                    return;
                }
                
                $(this).removeClass("glomIn");

                $(this).children().hide();
                $(this).find(".glomWithoutEnd").show();
                
                if ($(this).next().hasClass("glomItem"))
                {
                    $(this).next().children().hide();
                    $(this).next().find(".glomWithoutEnd").show();
                    
                    if (!$.isEmptyObject($(this).next().find(".glomCutFromNext").html()))
                    { 
                        if ($(this).next().find(".glomCutFromNext").html().length > 0)
                        {
                            // if there is text to cut from the next word...
                            $(this).next().next().find(".glomWithoutEnd").html( 
                                $(this).next().find(".glomCutFromNext").html() +  
                                $(this).next().next().find(".glomWithoutEnd").html()
                            );
                        }
                    }
                }
                
                if ($(this).prev().hasClass("glomItem"))
                {
                    $(this).prev().children().hide();
                    $(this).prev().find(".glomWithoutEnd").show();
                }
                
                
            });             
        });
}