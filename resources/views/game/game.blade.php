@extends('layouts.master')
@section('content')
<div id="info">
    <h1>Info</h1>
    
    <p> There are <?= count($game->players) ?> active players, including: <?= $game->active ?></p>
    <p>It is <?= $game->turn ?>'s turn.</p>
    <p>There have been <?= count($words); ?> words played.</p>
    <p>The score is <?= $game->score ?>.</p>
</div>
<div id="glom">
Words:<br/>
    <ul>
    <?php foreach($words as $word): ?>
        <li><?= $word->word ?></li>    
    <?php endforeach ;?>
    </ul>
</div>
<div id="input">
    <form class="form-inline" action="/playWord/" method="post">
        <div class="form-group">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="game_id" value="{{$game_id}}">
            <label for="new_word" name="new_word">Play a word: </label>
            <input class="form-control" name="word" id="word" placeholder="New word..." type="text"></input>
        </div>
        <div class="form-group">
            <input class="btn btn-primary" id="submit_button" type="submit" value="add"></input>
        </div>
    
    </form>

</div>

@stop