@extends('layouts.master')
@section('page_js')
    <script src="/scripts/user.game.js"></script>
    <script type="application/javascript">var game_id = <?= $game_id ?></script>
    <script type="application/javascript">var user_id = <?= Auth::user()->id ?></script>
    <script type="application/javascript">var csrf_token = "{{ csrf_token() }}"</script>
@stop
@section('content')
<div id="info">
    <h1>Info</h1>
    
    <p> There are <span id="playerCount"><?= count($game->players) ?></span> active players, including: <span id="playerList"><?= $game->active ?></span></p>
    <p>It is <span id="turn"><?= $game->turn ?></span>'s turn.</p>
    <p>There have been <span id="wordCount"><?= count($words); ?></span> words played.</p>
    <p>The score is <span id="score"><?= $game->score ?></span>.</p>
    <div id="wordList">
        Words:<br/>
        
        <ul>
        
        <?php foreach($words as $word): ?>
            <li><?= $word->word ?></li>    
        <?php endforeach ;?>
        </ul>
    </div>
</div>
<div id="glom">
</div>
<div id="input">
    <form id="playWord" class="form-inline" action="/playWord/" method="post">
        <div class="form-group">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="game_id" value="{{$game_id}}">
            <label for="new_word" name="new_word">Play a word: </label>
            <input class="form-control" name="word" id="wordInput" placeholder="Play a word..." type="text"></input>
        </div>
        <div class="form-group">
            <input disabled class="btn btn-primary" id="playWordButton" type="submit" value="Add word"></input>
        </div>
    
    </form>

</div>

@stop