<?php

namespace Solunes\Inventory\App\Listeners;

class StockTransferCreated {

    public function handle($event) {
        $product_bridge_stock = $event->parent;
        $product_bridge = $product_bridge_stock->parent;
        $product_bridge_variation = $product_bridge->product_bridge_variation()->where('variation_id', $product_bridge_stock->product_bridge_variation_id)->first();
        $response = \Inventory::reduce_inventory($event->from_agency, $product_bridge, $product_bridge_variation, $event->quantity);
        $response = \Inventory::increase_inventory($event->to_agency, $product_bridge, $product_bridge_variation, $event->quantity);
    }

}
