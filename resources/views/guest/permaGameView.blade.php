<!-- guest game view -->

@extends('layouts.master')
@section('page_js')

    <script type="application/javascript" src="/scripts/perma.game.js"></script>
    <script type="application/javascript">masterScript.wordList = <?= json_encode($wordList) ?></script>
    <link rel="stylesheet" href="/css/guest.game.css">

@stop
@section('content')

<div id="glom">
    <?php if ($wordList == null): ?>
        <div class="glomItem">No such game.</div>
    <?php else : ?>
    <?php endif ;?>
</div>

@stop