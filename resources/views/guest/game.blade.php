<!-- guest game view -->

@extends('layouts.master')
@section('page_js')
    <script type="application/javascript">var game_id = <?= $game_id ?></script>
    <script type="application/javascript">var user_id = <?= Auth::user()->id ?></script>

    <script src="/scripts/guest.game.js"></script>
@stop
@section('content')
<div id="info">
Words:<br/>
    <ul>
    <?php foreach($wordList as $word): ?>
        <li><?= $word->word ?></li>    
    <?php endforeach ;?>
    </ul>
</div>

<div id="glom">
</div>
<div>
    <?php if ($invited == "YES") : ?>
        <p>You're invited to play in this game.</p>
        <button type="button" id="acceptInvitationButton" class="btn btn-success" value="aAccept">Accept</button>
        <button type="button" id="rejectInvitationButton" class="btn btn-danger" value="Reject">Reject</button>
    <?php else : ?>
        <?php if ($requested == "YES") : ?>
            <p>You've requested to join the game.</p>
        <?php else: ?>
            <button type="button" id="joinGameButton" class="btn btn-primary" value="Join">Join game</button>
        <?php endif ; ?>
    <?php endif ; ?>

</div>
@stop