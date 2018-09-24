<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {
	
	protected $table = 'purchases';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'agency_id'=>'required',
		'currency_id'=>'required',
        'name'=>'required',
        'type'=>'required',
        'status'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'agency_id'=>'required',
        'currency_id'=>'required',
        'name'=>'required',
        'type'=>'required',
        'status'=>'required',
	);
                        
    public function agency() {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Business\App\Currency');
    }

    public function purchase_products() {
        return $this->hasMany('Solunes\Inventory\App\PurchaseProduct', 'parent_id');
    }

    public function getTotalAttribute(){
    	$total = 0;
    	$currency = \Solunes\Business\App\Currency::first();
    	if(count($this->purchase_products)>0){
	    	foreach($this->purchase_products as $batch){
	    		$total += \Business::calculate_currency($batch->total, $currency, $batch->currency);
	    	}
    	}
    	return $total.' '.$currency->name;
    }

    public function item_get_after_vars($module, $node, $single_model, $id, $variables){
    	$variables['product_node_id'] = \Solunes\Master\App\Node::where('name', 'product-bridge')->first()->id;
    	return $variables;
    }

}