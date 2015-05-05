<!-- List of user's active games -->

@extends('layouts.master')
@section('content')
<div>
    <h1>Game List</h1>
    <table class="table">
        <?php foreach ($games as $game): ?>
            <tr>
                <td>$game->words</td>
                <td>$game->score</td>
                <td>$game->rank</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
@stop