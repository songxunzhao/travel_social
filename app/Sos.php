<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sos extends Model
{
    //

	protected $table = 'sos';

	 protected $fillable=['message','creator_id', 'lat', 'lng'];
}
