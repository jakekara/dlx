


@extends('layouts.master')
@section('content')

    <h1>Leaderboard</h1>

    <table class="table">
        <tr>
            <th>Score</th>
            <th>Game</th>
            <th>Players</th>
        </tr>
        <?php foreach ($games as $game):?>
            <tr>
                <td><?= $game->score ?></td>
                <td><a href="/game/<?= $game->id ?>">Dyslexicondominium....</a></td>
                <td><?= $game->players ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
@stop