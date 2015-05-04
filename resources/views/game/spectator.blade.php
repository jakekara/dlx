<!-- spectator-only view -->

@extends('layouts.master')
@section('content')
<div id="info">
Words:<br/>
    <ul>
    <?php foreach($words as $word): ?>
        <li><?= $word->word ?></li>    
    <?php endforeach ;?>
    </ul>
</div>
<div id="glom">
</div>
@stop