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
        $node_product = \Solunes\Master\App\Node::where('name','product-bridge')->first();
        if(!config('inventory.basic_inventory')){
            $node_product_bridge_stock = \Solunes\Master\App\Node::create(['name'=>'product-bridge-stock', 'type'=>'child', 'location'=>'inventory', 'parent_id'=>$node_product->id]);
            \Solunes\Master\App\NodeExtra::create(['parent_id'=>$node_product_bridge_stock->id, 'type'=>'action_field', 'parameter'=>'field', 'value_array'=>json_encode(['view'])]);
            $node_stock_addition = \Solunes\Master\App\Node::create(['name'=>'stock-addition', 'location'=>'inventory', 'folder'=>'products']);
            \Solunes\Master\App\NodeExtra::create(['parent_id'=>$node_stock_addition->id, 'type'=>'action_field', 'parameter'=>'field', 'value_array'=>json_encode(['view'])]);
            $node_stock_transfer = \Solunes\Master\App\Node::create(['name'=>'stock-transfer', 'location'=>'inventory', 'folder'=>'products']);
            \Solunes\Master\App\NodeExtra::create(['parent_id'=>$node_stock_transfer->id, 'type'=>'action_field', 'parameter'=>'field', 'value_array'=>json_encode(['view'])]);
            $node_stock_removal = \Solunes\Master\App\Node::create(['name'=>'stock-removal', 'location'=>'inventory', 'folder'=>'products']);
            \Solunes\Master\App\NodeExtra::create(['parent_id'=>$node_stock_removal->id, 'type'=>'action_field', 'parameter'=>'field', 'value_array'=>json_encode(['view'])]);
            $node_purchase = \Solunes\Master\App\Node::create(['name'=>'purchase', 'location'=>'inventory', 'folder'=>'products']);
            $node_purchase_product = \Solunes\Master\App\Node::create(['name'=>'purchase-product', 'type'=>'child', 'location'=>'inventory', 'parent_id'=>$node_purchase->id]);
            $node_inventory_movement = \Solunes\Master\App\Node::create(['name'=>'inventory-movement', 'location'=>'inventory', 'folder'=>'products']);
        }
        
        $menu = \Solunes\Master\App\Menu::create(['menu_type'=>'admin','icon'=>'dollar','name'=>'Stock','permission'=>'business','link'=>'admin/stock-report']);
        $menu = \FuncNode::generate_translations($menu);

        // Usuarios
        $admin = \Solunes\Master\App\Role::where('name', 'admin')->first();
        $member = \Solunes\Master\App\Role::where('name', 'member')->first();
        if(!$products_perm = \Solunes\Master\App\Permission::where('name','products')->first()){
            $products_perm = \Solunes\Master\App\Permission::create(['name'=>'products', 'display_name'=>'Productos']);
            $admin->permission_role()->attach([$products_perm->id]);
        }

    }
}