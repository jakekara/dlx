<!DOCTYPE html>
<html>

    <head>
        <title>Dyslexicon | @yield('title')</title>
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>

        @yield('page_js')
    </head>
    
    <body>
        <div id="info">
            <?php if (Auth::check()): ?>
                Welcome, <?= Auth::user()->name ;?>. Not you? <a href="/logout">logout</a>
            <?php else: ?> 
                <a href="login">Log in</a> to play!
            <?php endif ?>
        </div>
        <div id="message">
            <?php if (isset($message)): ?>
                <?= $message ?>
            <?php endif; ?>
        </div>
        @yield('content')
    </body>
    
</html>