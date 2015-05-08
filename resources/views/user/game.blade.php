@extends('layouts.master')
@section('page_js')
    <script type="application/javascript">var game_id = <?= $game_id ?></script>
    <script type="application/javascript">var user_id = <?= Auth::user()->id ?></script>
    
    <script src="/scripts/user.game.js"></script>
@stop
@section('content')
<div id="info">
    <h1>Info</h1>
    
    <p> There are <span id="playerCount"><?= count($game->players) ?></span> active players, including: <span id="playerList"><?= $game->active ?></span></p>
    <p>It is <span id="turn"><?= $game->turn ?></span>'s turn.</p>
    <p>There have been <span id="wordCount"><?= count($wordList); ?></span> words played.</p>
    <p>The score is <span id="score"><?= $game->score ?></span>.</p>
    <div id="wordList">
        Words:<br/>
        
        <ul>
        
        <?php foreach($wordList as $word): ?>
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
<div id="friends">
    <div id="friendsList">
        <ul>
            <?php if (count(json_decode($friendList)) > 0) : ?>
                <h2>Invite friends to Dyslexicon</h2>
                <p>Dyslexicon is better with friends. Invite your friends to join. Once they do you can invite them to games.</p>
                <?php foreach (json_decode($friendList) as $friend) :?>
                    <li id='inviteFriendToDyslexiconListItem_<?= $friend->id ?>'><a class="inviteFriendToDyslexiconButton" id="inviteFriendToDyslexicon_<?= $friend->id ?>" href="#"><img alt="Invite <?= $friend->name ?>" class="img-rounded" src='<?= $friend->picture->data->url ?>' />Invite <?= $friend->name ?></a></li>
                <?php endforeach ; ?>
            <?php endif ;?>
            

        </ul>
        
    </div>
</div>

<?php if (isset($appFriendsList)) : ?>
    <?php if (count(json_decode($appFriendsList)) > 0) :?>
        <div id="friends">
            <div id="appFriendsList">
                <h2>Let your friends play (<?= count(json_decode($appFriendsList)); ?>)</h2>
                <p>These are your friends who are playing Dyslexicon. Invite them to join this game</p>
                <ul>
                    <?php foreach (json_decode($appFriendsList) as $friend) :?>
                    <li><a class="inviteAppFriendButton" id="inviteAppFriend_<?= $friend->id ?>">Invite <?= $friend->name ?></a></li>
                    <?php endforeach ; ?>

                </ul>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($joinRequestsList)) : ?>
    <?php if (count(json_decode($joinRequestsList)) > 0) :?>
        <div id="joinRequests">
            <h2>Requests (<?= count(json_decode($joinRequestsList)); ?>)</h2>

            <p>The following people are waiting to join this game</p>
            <ul>
                <?php foreach (json_decode($joinRequestsList) as $friend) :?>
                    <li><?= $friend->name ?>: <a class="acceptLink" id="accept_<?= $friend->id ?>" href="#">Accept</a> | <a class="rejectLink" id="reject_<?= $friend->id ?>" href="#">Reject</a></li>
                <?php endforeach ; ?>

            </ul>
        </div>
    <?php endif; ?>
<?php endif; ?>
    
    
<?php if (isset($invitedFriendsList)) : ?>
    <?php if (count(json_decode($invitedFriendsList)) > 0) :?>
        <div id="invitedFriends">
            <h2>Invites sent (<?= count(json_decode($invitedFriendsList)); ?>)</h2> 
            <p>You've invited these friends to play.</p>
            <ul>
                <?php foreach (json_decode($invitedFriendsList) as $friend) :?>
                    <li><?= $friend->name ?></li>
                <?php endforeach ; ?>

            </ul>
        </div>
    <?php endif; ?>
<?php endif ;?>



@stop