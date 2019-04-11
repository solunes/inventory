<?php

namespace Solunes\Inventory\App\Listeners;

class StockAdditionCreated {

    public function handle($event) {
        $product_bridge_stock = $event->parent;
        $product_bridge = $product_bridge_stock->parent;
        if($product_bridge_stock->product_bridge_variation_id){
        	$product_bridge_variation = $product_bridge->product_bridge_variation()->where('variation_id', $product_bridge_stock->product_bridge_variation_id)->first();
        } else {
        	$product_bridge_variation = NULL;
        }
        $response = \Inventory::increase_inventory($event->agency, $product_bridge, $product_bridge_variation, $event->quantity);
    }

}
