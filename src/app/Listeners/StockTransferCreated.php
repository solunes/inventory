<?php

namespace Solunes\Inventory\App\Listeners;

class StockTransferCreated {

    public function handle($event) {
        $product_bridge = $event->parent;
        if($event->variation_id&&$event->variation_option_id){
            $product_bridge_variation = $product_bridge->product_bridge_variation_options()->where('variation_id', $event->variation_id)->where('variation_option_id', $event->variation_option_id)->first();
        } else {
        	$product_bridge_variation = NULL;
        }
        $response = \Inventory::reduce_inventory($event->from_agency, $product_bridge, $product_bridge_variation, $event->quantity);
        $response = \Inventory::increase_inventory($event->to_agency, $product_bridge, $product_bridge_variation, $event->quantity);
    }

}
