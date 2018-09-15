<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model {
	
	protected $table = 'inventory_movements';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'product_id'=>'required',
		'place_id'=>'required',
		'type'=>'required',
		'name'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'product_id'=>'required',
		'place_id'=>'required',
		'type'=>'required',
		'name'=>'required',
	);
                        
    public function product_bridge() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge');
    }

    public function place() {
        return $this->belongsTo('Solunes\Business\App\Place');
    }

}