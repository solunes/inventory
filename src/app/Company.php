<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {
	
	protected $table = 'companies';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'industry'=>'required',
		'type'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'industry'=>'required',
		'type'=>'required',
	);

    public function contacts() {
        return $this->hasMany('Solunes\Inventory\App\Contact');
    }

}