<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use App\DictionaryWord;

class DictionaryWord extends Model {
    public $timestamps = false;

    protected $table = 'dictionary';
}
