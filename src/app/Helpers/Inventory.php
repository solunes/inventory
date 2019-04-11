<?php 

namespace Solunes\Inventory\App\Helpers;

class Inventory {

    public static function successful_sale($sale, $sale_payment) {
        if($sale->status=='paid'&&$sale->agency){
            foreach($sale->sale_items as $sale_item){
                $product_bridge = $sale_item->product_bridge;
                if($product_bridge&&$product_bridge->delivery_type=='normal'&&$product_bridge->stockable){
                    if(config('business.product_variations')){
                        $product_bridge_variation = $sale_item->product_bridge_variation;
                    } else {
                        $product_bridge_variation = NULL;
                    }
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
          if($product_bridge_variation){
            $agency_product_stock = $product_bridge->product_bridge_stocks()->where('product_bridge_variation_id', $product_bridge_variation->id)->where('agency_id', $agency->id)->first();
          } else {
            if(config('business.product_variations')){
                $agency_product_stock = $product_bridge->product_bridge_stocks()->whereNull('product_bridge_variation_id')->where('agency_id', $agency->id)->first();
            } else {
                $agency_product_stock = $product_bridge->product_bridge_stocks()->where('agency_id', $agency->id)->first();
            }
          }
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
            if($variation){
                $agency_product_stock->product_bridge_variation_id = $variation->id;
                $agency_product_stock->name = $agency_product_stock->name.' - '.$variation->name;
            }
            $agency_product_stock->initial_quantity = 0;
            $agency_product_stock->quantity = 0;
            $agency_product_stock->save();
          }
        } 
        return -1;
    }

    public static function increase_inventory($agency, $product_bridge, $variation = NULL, $units = 1) {
        if($agency){
          $agency_product_stock = NULL;
          if($variation){
            $agency_product_stock = $product_bridge->product_bridge_stocks()->where('product_bridge_variation_id', $variation->id)->where('agency_id', $agency->id)->first();
          } else {
            if(config('business.product_variations')){
                $agency_product_stock = $product_bridge->product_bridge_stocks()->whereNull('product_bridge_variation_id')->where('agency_id', $agency->id)->first();
            } else {
                $agency_product_stock = $product_bridge->product_bridge_stocks()->where('agency_id', $agency->id)->first();
            }
          }
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
            if($variation){
                $agency_product_stock->product_bridge_variation_id = $variation->id;
                $agency_product_stock->name = $agency_product_stock->name.' - '.$variation->name;
            }
            $agency_product_stock->initial_quantity = $units;
            $agency_product_stock->quantity = $units;
            $agency_product_stock->save();
          }
          return $agency_product_stock->quantity;
        } else {
          return -1;
        }
    }

    public static function inventory_movement($agency, $product, $type, $quantity, $name, $transaction, $item, $transaction_code = NULL) {

        // Crear Movimiento de Inventario
        $product_movement = new \Solunes\Inventory\App\InventoryMovement;
        $product_movement->agency_id = $agency->id;
        $product_movement->product_id = $product->id;
        $product_movement->type = $type;
        $product_movement->quantity = $quantity;
        $product_movement->name = $name;
        $product_movement->save();

        // Stock Product
        $real_quantity = $quantity;
        if($type=='move_out'){
            $real_quantity = -$quantity;
        }
        if($product_stock = \Solunes\Intenvory\App\ProductStock::where('parent_id', $product->id)->where('agency_id', $agency->id)->first()){
            $product_stock->quantity = $product_stock->quantity + $real_quantity;
        } else {
            $product_stock = new \Solunes\Intenvory\App\ProductStock;
            $product_stock->parent_id = $product->id;
            $product_stock->agency_id = $agency->id;
            $product_stock->initial_quantity = $quantity;
            $product_stock->quantity = $quantity;
        }
        $product_stock->save();

        // Purchase Product para Capital de Socios
        if($transaction!='register_product_purchase'&&$transaction!='register_product_movement'){
            $amount = 0;
            $purchase_products = $product->purchase_products()->where('status', 'holding')->orderBy('created_at','DESC');
            $purchase_products = $purchase_products->get();
            $remaining_quantity = $quantity;
            foreach($purchase_products as $purchase_product){
                // Ajuste de Inventario de Socio y Ganancia
                if($remaining_quantity>0){
                    if($type=='move_out'){
                        $remaining_quantity = $purchase_product->quantity - $remaining_quantity;
                        if($remaining_quantity<0){
                            $remaining_quantity = 0;
                            $quantity_purchase = $purchase_product->quantity;
                        } else {
                            $quantity_purchase = $remaining_quantity;
                        }
                        $purchase_product->quantity = $remaining_quantity;
                        if($transaction=='register_sale_item'){
                            $paid_amount = $item->total;
                            $paid_amount -= $item->pending;
                            if($item->parent->invoice){
                                $taxes = $paid_amount * 0.16;
                                $paid_amount -= $taxes;
                            }
                            $difference = $purchase_product->transport_investment - $purchase_product->transport_return;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->transport_return = $purchase_product->transport_return + $difference; 
                            } 
                            $difference = $purchase_product->investment - $purchase_product->return;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->return = $purchase_product->return + $difference; 
                            } 
                            if($paid_amount>0){
                                $purchase_product->profit = $purchase_product->profit + $paid_amount; 
                            } 
                            $max_profit = $purchase_product->investment * ($purchase_product->partner->return_percentage/100);
                            if($purchase_product->profit>$max_profit){
                                $purchase_product->profit = $max_profit;
                            }
                        } 
                    } else if($type=='move_in'){
                        $real_quantity = $purchase_product->initial_quantity - $purchase_product->quantity;
                        $remaining_quantity = $real_quantity - $remaining_quantity;
                        if($remaining_quantity<0){
                            $remaining_quantity = 0;
                            $quantity_purchase = $real_quantity;
                        } else {
                            $quantity_purchase = $remaining_quantity;
                        }
                        $purchase_product->quantity = $purchase_product->quantity + $quantity_purchase;
                        if($transaction=='register_refund_item') {
                            $paid_amount = $item->refund_amount;
                            if($paid_amount>0&&$purchase_product->profit > 0){
                                $paid_amount -= $purchase_product->profit;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->profit = $purchase_product->profit - $difference; 
                            } 
                            $difference = $purchase_product->transport_return - $purchase_product->transport_investment;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->transport_return = $purchase_product->transport_return - $difference; 
                            } 
                            $difference = $purchase_product->transport_return - $purchase_product->transport_investment;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->transport_return = $purchase_product->transport_return - $difference; 
                            } 
                        }
                    }
                    $purchase_product->save();
                }
            }
        }

        /* Crear cuentas de inventario */
        if($transaction!='register_product_purchase'){
            $currency_id = $product->currency_id;
            $amount = $product->cost * $quantity;
            $asset_stock = \Solunes\Accounting\App\Account::getCode('asset_stock')->id;
            if($transaction=='register_product_drop'){
                $expense_sale = \Solunes\Accounting\App\Account::getCode('expense_inventory_loss')->id;
            } else {
                $expense_sale = \Solunes\Accounting\App\Account::getCode('expense_sale')->id;
            }
            if($type=='move_out'){
                $stock_type = 'credit';
                $expense_type = 'debit';
            } else {
                $stock_type = 'debit';
                $expense_type = 'credit';
            }
            $arr[] = \Accounting::register_account($place->id, $stock_type, $asset_stock, $currency_id, $amount, $name);
            $arr[] = \Accounting::register_account($place->id, $expense_type, $expense_sale, $currency_id, $amount, $name);
            \Accounting::register_account_array($arr, $item->created_at, $transaction_code);
        }
    }
    
}