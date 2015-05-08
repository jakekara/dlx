@extends('layouts.master')
@section('page_js')

    <link href="/css/user.game.css" rel="stylesheet">

    <script type="application/javascript">var game_id = <?= $game_id ?></script>
    <script type="application/javascript">var user_id = <?= Auth::user()->id ?></script>
    
    <script src="/scripts/user.game.js"></script>
@stop
@section('content')

<div id="userGameViewArea" class="row">
    <div class="col-md-3 col-xs-12" id="infoBar">

        <div id="info">
            <h4>Players</h4>
                <ul id="playerList">
                    <?php foreach($game->getPlayersArray() as $player) : ?>
                        <li>
                            <?= $player["name"] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </p>
            <h4>Score: <span id="score"><?= $game->score ?></span></h4>
            <h4>Words played (<span id="wordCount"><?= count($wordList); ?></span>)</h4>
            <div id="wordList">

                <ul>        
                    <?php foreach($wordList as $word): ?>
                        <li><?= $word->word ?></li>    
                    <?php endforeach ;?>
                </ul>
            </div>
        </div>


        <div id ="socialInfo">
        
            <!-- App friends. Already playing and can be invited to game -->
            <?php if (isset($appFriendsList)) : ?>
                <?php if (count($appFriendsList) > 0) :?>
                    <div id="appFriends">
                        <div id="appFriendsList">
                            <h2>Let your friends play (<?= count($appFriendsList); ?>)</h2>
                            <p>These are your friends who are playing Dyslexicon. Invite them to join this game</p>
                            <ul>
                                <?php foreach ($appFriendsList as $friend) :?>
                                <li><a class="inviteAppFriendButton" id="inviteAppFriend_<?= $friend->id ?>">Invite <?= $friend->name ?></a></li>
                                <?php endforeach ; ?>

                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Friends requesting to play -->
            <?php if (isset($joinRequestsList)) : ?>
                <?php if (count($joinRequestsList) > 0) :?>
                    <div id="joinRequests">
                        <h2>Requests (<?= count($joinRequestsList); ?>)</h2>
                        <p>The following people are waiting to join this game</p>
                        <ul id="joinRequestsLists">                
                            <?php foreach ($joinRequestsList as $friend) :?>
                                <li><?= $friend->name ?>: <a class="acceptLink" id="accept_<?= $friend->id; ?>" href="#">Accept</a> | <a class="rejectLink" id="reject_<?= $friend->id ?>" href="#">Reject</a></li>
                            <?php endforeach ; ?>

                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


            <!-- Friends that have been invited to play the game -->
            <?php if (isset($invitedFriendsList)) : ?>
                <?php if (count($invitedFriendsList) > 0) :?>
                    <div id="invitedFriends">
                        <h2>Invites sent (<?= count($invitedFriendsList); ?>)</h2> 
                        <p>You've invited these friends to play.</p>
                        <ul id="invitesList">
                            <?php foreach ($invitedFriendsList as $friend) :?>
                                <li><?= $friend->name ?></li>
                            <?php endforeach ; ?>

                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif ;?>

            <!-- Facebook friends who aren't playing Dyslexicon -->
            <div id="friendsList">
                    <?php if (count($friendList) > 0) : ?>
                        <h2>Invite friends to Dyslexicon</h2>
                        <p>Dyslexicon is better with friends. Invite your friends to join. Once they do you can invite them to games.</p>
                        <ul>
                        <?php foreach ($friendList as $friend) :?>
                            <li class="invitableFriend" id='inviteFriendToDyslexiconListItem_<?= $friend->id ?>'><a class="inviteFriendToDyslexiconButton" id="inviteFriendToDyslexicon_<?= $friend->id ?>" href="#"><img alt="Invite <?= $friend->name ?>" class="img-rounded" src='<?= $friend->picture->data->url ?>' /></a></li>
                        <?php endforeach ; ?>
                        </ul>
                    <?php endif ;?>
            </div>


        </div>
    </div>

    <!-- Game play and controls -->
    <div class="col-md-9 col-xs-12" id="gamePanel">
            <div class="magnifier" id="glom">
            </div>

            <div id="wordInput">
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

        </div>
</div>



@stop