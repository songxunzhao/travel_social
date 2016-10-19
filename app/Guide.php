<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    //
	protected $table = 'guides';
	 protected $fillable=['title','description', 'address', 'img', 'lat', 'lng',
                        'type'];	

	public function toCompleteArray() {
        $obj = $this->toArray();

        return $obj;
    }


}
