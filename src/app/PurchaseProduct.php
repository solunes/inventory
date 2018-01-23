<?php

namespace Solunes\Inventory\App;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model {
	
	protected $table = 'purchase_products';
	public $timestamps = true;
    
    /* Creating rules */
    public static $rules_create_order = array(
        'product_id'=>'required',
        'status'=>'required',
        'quantity'=>'required',
        'currency_id'=>'required',
        'cost'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
    );

	/* Creating rules */
	public static $rules_create = array(
        'product_id'=>'required',
        'status'=>'required',
        'quantity'=>'required',
        'currency_id'=>'required',
        'cost'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
        'product_id'=>'required',
        'status'=>'required',
        'quantity'=>'required',
        'currency_id'=>'required',
        'cost'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
	);
                        
    public function parent() {
        return $this->belongsTo('Solunes\Inventory\App\Purchase', 'parent_id');
    }
                        
    public function currency() {
        return $this->belongsTo('Solunes\Inventory\App\Currency');
    }

    public function product() {
        return $this->belongsTo('Solunes\Inventory\App\Product');
    }

    public function partner() {
        return $this->belongsTo('Solunes\Inventory\App\Partner');
    }

    public function partner_transport() {
        return $this->belongsTo('Solunes\Inventory\App\Partner','partner_id');
    }

    public function pending_payment() {
        return $this->belongsTo('Solunes\Inventory\App\PendingPayment');
    }

    public function getTotalAttribute(){
        return $this->quantity * $this->cost;
    }

}