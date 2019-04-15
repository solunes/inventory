<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model {
	
	protected $table = 'stock_transfers';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'from_agency_id'=>'required',
		'to_agency_id'=>'required',
		'quantity'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'parent_id'=>'required',
		'from_agency_id'=>'required',
		'to_agency_id'=>'required',
		'quantity'=>'required',
	);
                        
    public function parent() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge');
    }
                                          
    public function product_bridge() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge', 'parent_id');
    }
    
    public function getNameAttribute() {
        return $this->product_bridge->name;
    }

    public function product_bridge_variation() {
        if(config('solunes.product')){
            return $this->belongsTo('\Solunes\Product\App\Variation');
        } else {
            return $this->belongsTo('\App\Variation');
        }
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function from_agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

    public function to_agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

}