@extends('layouts.master')
@section('page_js')
    <script src="/scripts/user.home.js"></script>
@stop
@section('content')

    <div>
        <h2>Your games</h2>
        <table class="table">
            <?php if (isset($games)) :?>
                <?php $games = json_decode($games); ?>
                <?php for($i = 0; $i < count($games); $i++) :?>
                    <tr>
                        <td><a href='/game/<?= $games[$i]->id ?>'>Game <?= $games[$i]->id ?></a></td>
                        <td><?= $games[$i]->score ?> </td>
                        <td>  
                            <button type="button" id="quitGame_<?= $games[$i]->id ?>" class="quitGameButton btn btn-default btn-sm" aria-label="Left Align"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Leave game</button>                             </td>
                    </tr>
                
                <?php endfor ;?>
            
            <?php endif; ?>

                
        </table>
        
        <div class="controls">
            <button id="startNewGameButton" type="button" class="btn btn-default" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New game
            </button>
        </div>
        
    </div>
@stop