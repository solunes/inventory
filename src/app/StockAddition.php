<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class StockAddition extends Model {
	
	protected $table = 'stock_additions';
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
        return $this->belongsTo('Solunes\Business\App\ProductBridge');
    }
                                                     
    public function product_bridge() {
        return $this->belongsTo('Solunes\Business\App\ProductBridge', 'parent_id');
    }
    
    public function getNameAttribute() {
        return $this->product_bridge->name;
    }

    public function variation() {
        return $this->belongsTo('\Solunes\Business\App\Variation');
    }

    public function variation_option() {
        return $this->belongsTo('\Solunes\Business\App\VariationOption');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

}