<?php

namespace Solunes\Inventory\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class MasterSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $node_product_stock = \Solunes\Master\App\Node::create(['name'=>'product-stock', 'type'=>'child', 'location'=>'inventory', 'parent_id'=>$node_product->id]);
        $node_purchase = \Solunes\Master\App\Node::create(['name'=>'purchase', 'location'=>'inventory', 'folder'=>'products']);
        $node_purchase_product = \Solunes\Master\App\Node::create(['name'=>'purchase-product', 'type'=>'child', 'location'=>'inventory', 'parent_id'=>$node_purchase->id]);
        $node_inventory_movement = \Solunes\Master\App\Node::create(['name'=>'inventory-movement', 'location'=>'inventory', 'folder'=>'products']);
        // Usuarios
        $admin = \Solunes\Master\App\Role::where('name', 'admin')->first();
        $member = \Solunes\Master\App\Role::where('name', 'member')->first();
        $inventory_perm = \Solunes\Master\App\Permission::create(['name'=>'inventory', 'display_name'=>'Negocio']);
        $admin->permission_role()->attach([$inventory_perm->id]);

    }
}