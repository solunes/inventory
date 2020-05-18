<?php

namespace Solunes\Inventory\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class TruncateSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!config('inventory.basic_inventory')){
            \Solunes\Inventory\App\InventoryMovement::truncate();
            \Solunes\Inventory\App\PurchaseProduct::truncate();
            \Solunes\Inventory\App\Purchase::truncate();
            \Solunes\Inventory\App\StockRemoval::truncate();
            \Solunes\Inventory\App\StockTransfer::truncate();
            \Solunes\Inventory\App\StockAddition::truncate();
            \Solunes\Inventory\App\ProductBridgeStock::truncate();
        }
    }
}