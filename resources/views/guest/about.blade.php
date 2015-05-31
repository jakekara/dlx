<!-- guest game view -->

@extends('layouts.master')
@section('page_js')
@stop
@section('content')

<div class="jumbotron">
          
    <h1>Dyslexicon</h1>
         
    <p>Start combining words, competing and collaborating</p>
</div>

<div>
    <div class="sheet">
<article>
       
        <h1>How to play</h1>
        <p class="lead">Start with a word. Any word. Then add another to the end or the beginning.</p>
        <blockquote id="glom">
            <p><span class="glomItem">computer</span></p>
            <p><span class="glomItem">compu</span><span class="glomItem glomFullWord">terrestrial</span></p>
            <p><p><span class="glomItem">compu</span><span class="glomItem">terrestri</span><span class="glomItem glomFullWord">always</span></p>
        </blockquote>
        
        <p class="small"><strong>Some rules:</strong    > Words must be four letters or more and overlap the existing 'glom' by at least two words. 
        So that means at least two letters must be added to the beginning or end with each turn. Finally, a word can only be used once per game. 
            Many proper names are are acceptable, and, no, not every word in the English language is in our dictionary. Only 90,000 or so. 
        </p>

        <h1>Why?</h1>
        <p class="lead">'Cause.</p>
        
        <h1>No, seriously. Why?</h1>
        <p class="lead">Well, now that you put it that way, this is my final project for CS50. So from here, it'll get kind of nerdy.</p>
        
        <h1>How?</h1>
        <p class="lead"><span class=Composer>Laravel. PHP. Facebook PHP SDK. Facebook Javascript SDK. Bootstrap. jQuery... and very little sleep.</span></p>
                
        <h4>Platform, design decisions</h4>

        <p>This web app is built using the Laravel 5 PHP web framework, hosted on a Dreamhost VPS (for now).</p>
        
        <h4>Laravel 5</h4>
        <p>It would be hard to overstate how steap the Laravel 5 learning curve was for me. Until this point I had never used a web framework, only CMSes 
            such as WordPress, Concrete5 and Joomla to develop sites, or simply hand-coded the functionality I needed. Learning Laravel was more
         difficult than picking up another programming language, since the point of a framework is to abstract a lot of programming. What you're really 
            learning is the concepts that its developers felt would be the best way to program the web. Fortunately, the way they chose to abstract web 
            technologies really does make a lot of sense.
        </p>
        <p>
            Since this was my first Laravel project, I learned a lot about it as I went, so not all of my initial design decisions were the best. For instance,
            I didn't think twice when I wrote a small class to package simple JSON responses, such as a two-element array with a status ("SUCCESS" or "FAILURE")
             and a detailed status describing the result. Turns out, that was completely unnecessary; in Laravel you can just return an array of data as 
            a parameter of the function for rendering views, and when it's called with an ajax request, it will be turned into a Javascript object. It didn't really
            occur to me that Laravel would handle this for me, since just wrapping an array in json_encode() and json_decode() "manually" isn't all that difficult.
        </p>
        <p>
            As a result of this learning on the fly, there are some ways I do things in the earlier code that I abandoned halfway through, but haven't yet spent
             a lot of time fixing things that weren't "broken." I've done my best to identify new and old approaches.
        </p>

                
    <h4>Facebook</h4>
        
        <p>I use Facebook mainly for authentication, so I don't have to deal with storing people's usernames and passwords — but more importantly,
         so users don't have to sign up for yet another website just to play. All you have to do is accept the app. Hopefully this encourages
         more people to play.</p>
        
        <p>I first thought I might be able to get away with using a Laravel package called socialize to allow login via Facebook, so I started coding 
         that way but soon determined I needed more functionality. I was able to obtain facebook IDs for users who "approve" the app, and that's what 
            I chose to use in lieu of passwords. That was well within the scope of Socialize. However, to be able to load to let people invite their friends
             to use the app and see their friends who are using the app, I had to use the PHP and Javascript SDKs.
        </p>
        <p>
            There are many ways to use the facebook APIs, so the choice alone was a little daunting. I ended up using Javascript to retrieve tokens, not 
            a "redirect" approach, where you redirect a user to Facebook, then Facebook sends them back to you with necessary access tokens to make requests
             on the logged-in user's behalf. I used Javascript to asynchronously get a token and check its validity, then store that in the users table.
        </p>
        <p> I use PHP SDK (v4), to get lists of friends — both "invitable" (not current users of my app) and friends who are using my app, sometimes called
            "app friends." I use Javascript again when users invite their friends to join the app.
        </p>
        <p>When people invite their "app friends" to join a specific game, those kind of invitations are all done within my app, not through Facebook.
        </p>
        
        <h4>Facebook and Laravel</h4>
        <p>
            While there is no "compatability" problem between Facebook and Laravel, they don't really work that well together right out of the box. For instance,
             Laravel has CSRF protection built in, which looks for a valid token with every post request, which breaks the app when you run it with standard
            CSRF protection on within the Facebook Canvas. This took me a long time ("long" is relative — most of this app was developed over a seven-day period I
             took off from work) to figure out why I was getting these CSRF Token Mismatch errors and much longer to come up with a viable solution, which I'll
             fully document at a later date, since I didn't actually find this approach documented. In short, what I did was bypass CSRF protection when 
            I could verify Facebook's "signed_request" data it sends — hashed data that would be very hard to fake.
        </p>
        
    
        </article> 
    </div>
    
    
</div>
@stop