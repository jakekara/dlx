@extends('layouts.master')
@section('page_js')
    <script src="/scripts/user.home.js"></script>
@stop
@section('content')

    <div>

        <table class="table">
            <?php if (isset($games)) :?>
                <?php foreach ($games as $game) :?>
                    <tr>
                        <td><?= number_format($game->score); ?> </td>
                        <td class="lead"><a href='/game/<?= $game->id ?>'><?= $game->glom(25); ?> ...</a></td>
                        <td>
                            <span class='small'>
                            <?php $nameString = ""; ?>
                            <?php foreach($game->getPlayersArray() as $player) : ?>
                                <?php $nameString .= $player["name"] . ", " ?>
                            <?php endforeach ; ?>
                            <?php $nameString = rtrim($nameString, ", "); ?>
                            <?= $nameString ; ?>
                            </span>
                        </td>
                        <td>
                        
                            <?php if ($game->turn == Auth::user()->id) : ?>
                                <!--<span class="small">you're up</span>-->
                            <?php endif ;?>
                        </td>
                        <td>  
                            <button type="button" id="quitGame_<?= $game->id ?>" class="quitGameButton btn btn-default btn-sm" aria-label="Left Align"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Leave game</button>
                        </td>
                    </tr>
                
                <?php endforeach ;?>
            
            <?php endif; ?>
            
            
            <?php if (isset($gamesWithInvites)) :?>
                <?php foreach ($gamesWithInvites as $game) :?>
                    <tr>
                        <td><?= number_format($game->score); ?> </td>
                        <td class="lead"><a href='/game/<?= $game->id ?>'><?= $game->glom(25); ?> ...</a></td>
                        <td>
                            <span class='small'>
                            <?php $nameString = ""; ?>
                            <?php foreach($game->getPlayersArray() as $player) : ?>
                                <?php $nameString .= $player["name"] . ", " ?>
                            <?php endforeach ; ?>
                            <?php $nameString = rtrim($nameString, ", "); ?>
                            <?= $nameString ; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($game->turn == Auth::user()->id) : ?>
                                <span class="small">you're up</span>
                            <?php endif ;?>
                        <td>  
                            <button type="button" id="quitGame_<?= $game->id ?>" class="quitGameButton btn btn-default btn-sm" aria-label="Left Align"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Leave game</button>
                        </td>
                    </tr>
                
                <?php endforeach ;?>
            
            <?php endif; ?>

                
        </table>
        
        <div class="controls">
            <button id="startNewGameButton" type="button" class="btn btn-default" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New game
            </button>
        </div>
        
    </div>
@stop