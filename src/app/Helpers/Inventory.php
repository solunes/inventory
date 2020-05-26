<?php 

namespace Solunes\Inventory\App\Helpers;

class Inventory {

    public static function successful_sale($sale, $sale_payment) {
        if(($sale->status=='paid'||$sale->status=='to-pay')&&$sale->agency){
            $sale->status = 'pending-delivery';
            $sale->save();
        }
        return true;
    }

    public static function reduce_inventory($agency, $product_bridge, $units = 1) {
        if(config('inventory.basic_inventory')){
          $product_bridge->quantity = $product_bridge->quantity - $units;
          $product_bridge->save();
          /*$product->quantity = $product_bridge->quantity;
          $product->save();*/
          return -1;
        } else {
          if($agency){
            $agency_product_stock = NULL;
            \Inventory::inventory_movement('move_out', $agency, $product_bridge, $units);
            $agency_product_stock = $product_bridge->product_bridge_stocks()->where('agency_id', $agency->id)->first();
            if($agency_product_stock){
              $quantity = $agency_product_stock->quantity;
              $new_qunatity = $quantity - $units;
              if($new_qunatity<0){
                $new_qunatity = 0;
              }
              $agency_product_stock->quantity = $new_qunatity;
              $agency_product_stock->save();
              return $agency_product_stock->quantity;
            } else {
              $agency_product_stock = new \Solunes\Inventory\App\ProductBridgeStock;
              $agency_product_stock->parent_id = $product_bridge->id;
              $agency_product_stock->agency_id = $agency->id;
              $agency_product_stock->name = $product_bridge->name;
              $agency_product_stock->initial_quantity = 0;
              $agency_product_stock->quantity = 0;
              $agency_product_stock->save();
            }
          } 
          return -1;
        }

    }

    public static function increase_inventory($agency, $product_bridge, $units = 1) {
        if(config('inventory.basic_inventory')){
          $product_bridge->quantity = $product_bridge->quantity + $units;
          $product_bridge->save();
          $product = $product_bridge->product;
          $product->quantity = $product_bridge->quantity;
          $product->save();
          return -1;
        } else {
          if($agency){
            $agency_product_stock = NULL;
            \Inventory::inventory_movement('move_in', $agency, $product_bridge, $units);
            $agency_product_stock = $product_bridge->last_product_bridge_stocks()->where('agency_id', $agency->id)->first();
            if($agency_product_stock){
              $quantity = $agency_product_stock->quantity;
              $new_qunatity = $quantity + $units;
              $agency_product_stock->quantity = $new_qunatity;
              $agency_product_stock->save();
            } else {
              $agency_product_stock = new \Solunes\Inventory\App\ProductBridgeStock;
              $agency_product_stock->parent_id = $product_bridge->id;
              $agency_product_stock->agency_id = $agency->id;
              $agency_product_stock->name = $product_bridge->name;
              $agency_product_stock->initial_quantity = $units;
              $agency_product_stock->quantity = $units;
              $agency_product_stock->save();
            }
            return $agency_product_stock->quantity;
          } else {
            return -1;
          }
        }
    }

    public static function inventory_movement($type, $agency, $product_bridge, $quantity = 1) {
        // Crear Movimiento de Inventario
        $product_movement = new \Solunes\Inventory\App\InventoryMovement;
        $product_movement->agency_id = $agency->id;
        $product_movement->product_bridge_id = $product_bridge->id;
        $product_movement->name = $product_bridge->name;
        $product_movement->type = $type;
        $product_movement->quantity = $quantity;
        $product_movement->save();
    }
    
}