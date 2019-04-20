<?php 

namespace Solunes\Inventory\App\Helpers;

class Inventory {

    public static function successful_sale($sale, $sale_payment) {
        if($sale->status=='paid'&&$sale->agency){
            foreach($sale->sale_items as $sale_item){
                $product_bridge = $sale_item->product_bridge;
                if($product_bridge&&$product_bridge->delivery_type=='normal'&&$product_bridge->stockable){
                    /*if(config('business.product_variations')){
                        $product_bridge_variation = $sale_item->product_bridge_variation_option_id;
                    } else {*/
                        $product_bridge_variation = NULL;
                    //}
                    \Inventory::reduce_inventory($sale->agency, $product_bridge, $product_bridge_variation, $sale_item->quantity);
                }
            }
            $sale->status = 'pending-delivery';
            $sale->save();
        }
        return true;
    }

    public static function reduce_inventory($agency, $product_bridge, $product_bridge_variation = NULL, $units = 1) {
        if($agency){
          $agency_product_stock = NULL;
          \Inventory::inventory_movement('move_out', $agency, $product_bridge, $product_bridge_variation, $units);
          /*if($product_bridge_variation){
            $agency_product_stock = $product_bridge->product_bridge_stocks()->where('variation_id', $product_bridge_variation->variation_id)->where('variation_option_id', $product_bridge_variation->variation_option_id)->where('agency_id', $agency->id)->first();
          } else {
            if(config('business.product_variations')){
                $agency_product_stock = $product_bridge->product_bridge_stocks()->whereNull('variation_id')->where('agency_id', $agency->id)->first();
            } else {*/
                $agency_product_stock = $product_bridge->product_bridge_stocks()->where('agency_id', $agency->id)->first();
            /*}
          }*/
          if($agency_product_stock){
            $quantity = $agency_product_stock->quantity;
            $new_qunatity = $quantity - $units;
            if($new_qunatity>=0){
                $agency_product_stock->quantity = $new_qunatity;
                $agency_product_stock->save();
                return $agency_product_stock->quantity;
            }
          } else {
            $agency_product_stock = new \Solunes\Inventory\App\ProductBridgeStock;
            $agency_product_stock->parent_id = $product_bridge->id;
            $agency_product_stock->agency_id = $agency->id;
            $agency_product_stock->name = $product_bridge->name;
            /*if($product_bridge_variation){
                $agency_product_stock->variation_id = $product_bridge_variation->variation_id;
                $agency_product_stock->variation_option_id = $product_bridge_variation->variation_option_id;
                $agency_product_stock->name = $agency_product_stock->name.' - '.$product_bridge_variation->name;
            }*/
            $agency_product_stock->initial_quantity = 0;
            $agency_product_stock->quantity = 0;
            $agency_product_stock->save();
          }
        } 
        return -1;
    }

    public static function increase_inventory($agency, $product_bridge, $product_bridge_variation = NULL, $units = 1) {
        if($agency){
          $agency_product_stock = NULL;
          \Inventory::inventory_movement('move_in', $agency, $product_bridge, $product_bridge_variation, $units);
          /*if($product_bridge_variation){
            $agency_product_stock = $product_bridge->product_bridge_stocks()->where('variation_id', $product_bridge_variation->variation_id)->where('variation_option_id', $product_bridge_variation->variation_option_id)->where('agency_id', $agency->id)->first();
          } else {
            if(config('business.product_variations')){
                $agency_product_stock = $product_bridge->product_bridge_stocks()->whereNull('variation_id')->where('agency_id', $agency->id)->first();
            } else {*/
                $agency_product_stock = $product_bridge->product_bridge_stocks()->where('agency_id', $agency->id)->first();
            /*}
          }*/
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
            /*if($product_bridge_variation){
                $agency_product_stock->variation_id = $product_bridge_variation->variation_id;
                $agency_product_stock->variation_option_id = $product_bridge_variation->variation_option_id;
                $agency_product_stock->name = $agency_product_stock->name.' - '.$product_bridge_variation->name;
            }*/
            $agency_product_stock->initial_quantity = $units;
            $agency_product_stock->quantity = $units;
            $agency_product_stock->save();
          }
          return $agency_product_stock->quantity;
        } else {
          return -1;
        }
    }

    public static function inventory_movement($type, $agency, $product_bridge, $product_bridge_variation, $quantity = 1) {
        // Crear Movimiento de Inventario
        $product_movement = new \Solunes\Inventory\App\InventoryMovement;
        $product_movement->agency_id = $agency->id;
        $product_movement->product_bridge_id = $product_bridge->id;
        $product_movement->name = $product_bridge->name;
        $product_movement->type = $type;
        /*if($product_bridge_variation){
            $product_movement->variation_id = $product_bridge_variation->variation_id;
            $product_movement->variation_option_id = $product_bridge_variation->variation_option_id;
            $product_movement->name = $product_movement->name.' - '.$product_bridge_variation->name;
        }*/
        $product_movement->quantity = $quantity;
        $product_movement->save();
    }
    
}