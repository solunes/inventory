<?php

namespace Solunes\Inventory\App\Listeners;

class StockRemovalCreated {

    public function handle($event) {
        $product_bridge = $event->parent;
        $response = \Inventory::reduce_inventory($event->agency, $product_bridge, $event->quantity);
    }

}
