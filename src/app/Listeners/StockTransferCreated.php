<?php

namespace Solunes\Inventory\App\Listeners;

class StockTransferCreated {

    public function handle($event) {
        $product_bridge = $event->parent;
        $response = \Inventory::reduce_inventory($event->from_agency, $product_bridge, $event->quantity);
        $response = \Inventory::increase_inventory($event->to_agency, $product_bridge, $event->quantity);
    }

}
