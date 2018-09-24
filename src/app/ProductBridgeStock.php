<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class ProductBridgeStock extends Model {
	
	protected $table = 'product_bridge_stocks';
	public $timestamps = true;

	/* Transfer rules */
	public static $rules_transfer = array(
		'product_stock_id'=>'required',
		'agency_id'=>'required',
	);

	/* Remove rules */
	public static $rules_remove = array(
		'product_stock_id'=>'required',
		'name'=>'required',
	);

	/* Creating rules */
	public static $rules_create = array(
		'agency_id'=>'required',
		'quantity'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'agency_id'=>'required',
		'quantity'=>'required',
	);
                        
    public function parent() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge');
    }

    public function agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

}