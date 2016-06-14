<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    //
    protected $fillable = ['user_id', 'type', 'code', 'processed'];

    public static function fromTypeString($type_str){
        if($type_str == 'reset') {
            return 1;
        }
        else if($type_str == 'verify') {
            return 2;
        }
        else{
            return null;
        }

    }
}
