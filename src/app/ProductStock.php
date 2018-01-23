<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model {
	
	protected $table = 'product_stocks';
	public $timestamps = true;

	/* Transfer rules */
	public static $rules_transfer = array(
		'product_stock_id'=>'required',
		'place_id'=>'required',
	);

	/* Remove rules */
	public static $rules_remove = array(
		'product_stock_id'=>'required',
		'name'=>'required',
	);

	/* Creating rules */
	public static $rules_create = array(
		'place_id'=>'required',
		'quantity'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'place_id'=>'required',
		'quantity'=>'required',
	);
                        
    public function parent() {
        return $this->belongsTo('Solunes\Inventory\App\Product');
    }

    public function place() {
        return $this->belongsTo('Solunes\Inventory\App\Place');
    }

}