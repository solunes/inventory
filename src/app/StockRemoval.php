<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class StockRemoval extends Model {
	
	protected $table = 'stock_removals';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'agency_id'=>'required',
		'quantity'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'parent_id'=>'required',
		'agency_id'=>'required',
		'quantity'=>'required',
	);
                        
    public function parent() {
        return $this->belongsTo('Solunes\Inventory\App\ProductBridgeStock');
    }
                                                                
    public function product_bridge() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge', 'parent_id');
    }
    
    public function product_bridge_variation() {
        if(config('solunes.product')){
            return $this->belongsToMany('\Solunes\Product\App\Variation', 'product_bridge_variation', 'product_bridge_id', 'variation_id');
        } else {
            return $this->belongsToMany('\App\Variation', 'product_bridge_variation', 'variation_id');
        }
    }
  
    public function product_bridge_stock() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge', 'parent_id');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

}