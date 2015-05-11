@extends('layouts.master')
@section('page_js')

    <link href="/css/user.game.css" rel="stylesheet">

    <script type="application/javascript">var game_id = <?= $game_id ?></script>
    <script type="application/javascript">var user_id = <?= Auth::user()->id ?></script>
    <script type="application/javascript"> var appId = <?= env('FB_APPID'); ?> </script>

    <script src="/scripts/user.game.js"></script>
@stop
@section('content')

<div id="userGameViewArea" class="row">
    <div class="col-md-3 col-xs-12" id="infoBar">
        <div class="panel-group" id="info" role="tablist" aria-multiselectable="true">
            <!-- general game info -->            
            <div class="panel panel-primary">

                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Game data
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <table class="table .table-striped">
                            <tr>
                                <td><span class="small">Score:</span></td>
                                <td><h4><span id="score"><?= $game->score ?></span></h4></td>
                            </tr>
                            
                            <tr>
                                <td><span class="small">Players:</span></td>
                                <td>
                                    <ul id="playerList" class="niftyList">
                                        <?php foreach($game->getPlayersArray() as $player) : ?>
                                            <li>
                                                <?= $player["name"] ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><a href="https://dlx.jakekara.com/show/game/<?= $game->id ?>">Share link</a></td>
                            </tr>
                            
                        </table>                        
                    </div>
                </div>
            </div>
            <!-- end general game info -->
            
            
            <!-- word list -->

            <div class="panel panel-primary">
                <div class="panel-heading" role="tab" id="headingTwo">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                         Words (<span id="wordCount"><?= count($wordList); ?></span>)
                        </a>
                    </h4>

                </div>
                <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
                      <div class="panel-body">
                        <div >
                            <ul id="wordList" class="niftyList">        
                                <?php foreach($wordList as $word): ?>
                                    <li><?= $word->word ?></li>    
                                <?php endforeach ;?>
                            </ul>
                        </div>
                      </div>
                </div>
            </div>
            <!-- end word list -->

            <!-- social panel -->
             <div class="panel panel-primary">
                <div class="panel-heading" role="tab" id="headingFour">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Connect
                        </a>
                    </h4>
                </div>
                <div id="collapseFour" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFour">
                    <div class="panel-body">
                        <div id ="socialInfo">
                            <!-- App friends. Already playing and can be invited to game -->
                            <?php if (isset($appFriendsList)) : ?>
                                <?php if (count($appFriendsList) > 0) :?>
                                    <div id="appFriends">
                                        <div id="appFriendsList">
                                            <h3>Play with friends</h3>
                                            <ul>
                                                <?php foreach ($appFriendsList as $friend) :?>
                                                <?php $isAlreadyInvited = false ?>
                                                <?php foreach ($invitedFriendsList as $alreadyInvited)
                                                    {
                                                        if ($friend->name == $alreadyInvited["name"])
                                                        {
                                                            $isAlreadyInvited = true;
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <?php if (!$isAlreadyInvited) : ?>
                                                    <li><a class="inviteAppFriendButton" id="inviteAppFriend_<?= $friend->id ?>">Invite <?= $friend->name ?></a></li>

                                                <?php endif ; ?>
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
                                        <p>The following people are waiting to join this game</p>
                                        <ul id="joinRequestsLists">                
                                            <?php foreach ($joinRequestsList as $friend) :?>
                                                <li><?= $friend["name"] ?>: <button type="button" class="btn btn-sm btn-success acceptLink" id="accept_<?= $friend["id"]; ?>" href="#">Accept</button> | <button type="button" class="btn btn-sm btn-danger rejectLink" id="reject_<?= $friend["id"] ?>" href="#">Reject</a></li>
                                            <?php endforeach ; ?>

                                        </ul>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>


                            <!-- Friends that have been invited to play the game -->
                            <?php if (isset($invitedFriendsList)) : ?>
                                <?php if (count($invitedFriendsList) > 0) :?>
                                    <div id="invitedFriends">
                                        <h3>Invites sent (<?= count($invitedFriendsList); ?>)</h3> 
                                        <ul id="invitesList">
                                            <?php foreach ($invitedFriendsList as $friend) :?>
                                                <li><?= $friend["name"] ?></li>
                                            <?php endforeach ; ?>

                                        </ul>
                                    </div>
                                <?php endif; ?>
                            <?php endif ;?>

                            <!-- Facebook friends who aren't playing Dyslexicon -->
                            <div id="friendsList">
                                    <?php if (count($friendList) > 0) : ?>
                                        <h3>Tell a friend</h3>
                                        <p>Let your Facebook friends know about Dyslexicon.</p>
                                        <ul>
                                        <?php foreach ($friendList as $friend) :?>
                                            <li class="invitableFriend" id='inviteFriendToDyslexiconListItem_<?= $friend->id ?>'><a class="inviteFriendToDyslexiconButton" id="inviteFriendToDyslexicon_<?= $friend->id ?>" href="#"><img alt="Invite <?= $friend->name ?>" class="img-rounded" src='<?= $friend->picture->data->url ?>' /></a></li>
                                        <?php endforeach ; ?>
                                        </ul>
                                    <?php endif ;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <!-- end social panel -->
                
                
            <!-- rules -->
             <div class="panel panel-primary">
                <div class="panel-heading" role="tab" id="headingThree">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                     How to play
                    </a>
                  </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                  <div class="panel-body">
                      <p>
                          <strong>Just start by picking a word,</strong> say "fish," then pick another that will overlap with the beginning or end
                           by at least two letters, and leave at least two new letters sticking off the front or end — say, "shame"
                           for example. The new glom would become "fishame". There are few rules at all. In fact, you don't even
                           have to take turns.
                      </p>
                      <p>
                          <strong>This game is collaborative.</strong> Individuals don't have scores. Each game is scored against every other
                           round that's ever been played and ranked on the leaderboard.
                      </p>
                      <p>
                          Note that many proper names, such as Oklahoma and Ethan are acceptable. On the other hand, not ever English word 
                          is in our official dictionary.
                      </p>
                  </div>
                </div>
            </div>
            <!-- end rules -->
        </div>
    </div> <!-- end of column -->

    <!-- Game play and controls -->
    <div class="col-md-9 col-xs-12" id="gamePanel">
        

        <div class="" id="wordInput">
            <form id="playWord" class="form-inline" action="/playWord/" method="post">
                <div class="form-group">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="game_id" value="{{$game_id}}">
                    <label for="new_word" name="new_word">Play a word: </label>
                    <input class="form-control" name="word" id="wordTextInput" placeholder="Play a word..." type="text"></input>
                </div>
                <div class="form-group">
                    <input disabled class="btn btn-primary" id="playWordButton" type="submit" value="Add word"></input>
                </div>
            </form>
        </div>

        <div class="magnifier" id="glom">
        </div>
    </div>
</div>



@stop