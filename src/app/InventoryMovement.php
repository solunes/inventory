<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model {
	
	protected $table = 'inventory_movements';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'product_bridge_id'=>'required',
		'agency_id'=>'required',
		'type'=>'required',
		'name'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'product_bridge_id'=>'required',
		'agency_id'=>'required',
		'type'=>'required',
		'name'=>'required',
	);
                        
    public function product_bridge() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge');
    }

    public function product_bridge_variation() {
        if(config('solunes.product')){
            return $this->belongsTo('\Solunes\Product\App\Variation');
        } else {
            return $this->belongsTo('\App\Variation');
        }
    }

    public function agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

}