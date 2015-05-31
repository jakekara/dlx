


@extends('layouts.master')
@section('content')

    <div>
        <div class="jumbotron">
          <h1>The best of the best</h1>
          <p>Check out some of our top-scoring games.</p>
        </div>
        <table class="leaderboard table table-hover">
            <?php if (isset($games)) :?>
                <?php foreach ($games as $game) :?>
                    <tr>
                        <td><span class="score"></span><?= number_format($game->score); ?></span></td>
                        <td class="lead glomItem"><a href='/game/<?= $game->id ?>'><?= $game->glomPreview(); ?> ...</a></td>
                        <td>
                            <?php $nameString = ""; ?>
                            <?php foreach($game->getPlayersArray() as $player) : ?>
                                <?php $nameString .= $player["first_name"] . ", " ?>
                            <?php endforeach ; ?>
                            <span class="small"><?php $nameString = rtrim($nameString, ", "); ?></span>
                            <?= $nameString ; ?>
                        </td>

                    </tr>
                
                <?php endforeach ;?>
            
            <?php endif; ?>

                
        </table>
    </div>
@stop