<?php

namespace Solunes\Inventory\App\Listeners;

class StockAdditionCreated {

    public function handle($event) {
        $product_bridge = $event->parent;
        $response = \Inventory::increase_inventory($event->agency, $product_bridge, $event->quantity);
    }

}
