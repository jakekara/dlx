@extends('layouts.master')
@section('content')

    <h3>Leaderboard</h3>

    <table>
        <tr>
            <?php var_dump($games); ?>
            <td>Rank</td>
            <td>Game...</td>
        </tr>
    </table>
@stop