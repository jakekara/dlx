<!DOCTYPE html>
<html>

    <head>
        <title>Dyslexicon | @yield('title')</title>
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
        
        <script type="application/javascript"> var csrf_token = "{{ csrf_token() }}"</script>
        <script type="application/javascript" src="/scripts/master.js"></script>
        @yield('page_js')
    </head>
    
    <body>
        <div id="fb-root">
            <script>
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
                }(document, 'script', 'facebook-jssdk'));
            </script>

                <nav class="navbar navbar-default">
                    <!-- We use the fluid option here to avoid overriding the fixed width of a normal container within the narrow content columns. -->
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <!--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-6">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>-->
                            <a class="navbar-brand" href="#">[ Dyslexicon  ]</a>
                        </div>

                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-6">
                        <ul class="nav navbar-nav">
                            <li id="masterHomeButton"><a href="/home">Home</a></li>
                            <li id="masterLeaderboardButton"><a href="/leaderboard">Leaderboard</a></li>
                            <li id="masterLoginStatus">
                                <?php if (Auth::check()): ?>
                                    <a href="/logout">Logout <?= Auth::user()->name ;?></a>
                                <?php else: ?> 
                                    <a href="/facebook/login">Log in</a>
                                <?php endif ?>
                            </li>
                        </ul>
                        </div><!-- /.navbar-collapse -->
                    </div>
                </nav>



            <div id="loginStatus">

            </div>
            <div id="message">
                <?php if (isset($message)): ?>
                    <?= $message ?>
                <?php endif; ?>
            </div>
            @yield('content')
        </div>
    </body>
    
</html>