<?php 

namespace Solunes\Inventory\App\Helpers;

class Inventory {

    public static function inventory_movement($place, $product, $type, $quantity, $name, $transaction, $item, $transaction_code = NULL) {

        // Crear Movimiento de Inventario
        $product_movement = new \Solunes\Store\App\InventoryMovement;
        $product_movement->place_id = $place->id;
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
        if($product_stock = \Solunes\Store\App\ProductStock::where('parent_id', $product->id)->where('place_id', $place->id)->first()){
            $product_stock->quantity = $product_stock->quantity + $real_quantity;
        } else {
            $product_stock = new \Solunes\Store\App\ProductStock;
            $product_stock->parent_id = $product->id;
            $product_stock->place_id = $place->id;
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
            $asset_stock = \Solunes\Store\App\Account::getCode('asset_stock')->id;
            if($transaction=='register_product_drop'){
                $expense_sale = \Solunes\Store\App\Account::getCode('expense_inventory_loss')->id;
            } else {
                $expense_sale = \Solunes\Store\App\Account::getCode('expense_sale')->id;
            }
            if($type=='move_out'){
                $stock_type = 'credit';
                $expense_type = 'debit';
            } else {
                $stock_type = 'debit';
                $expense_type = 'credit';
            }
            $arr[] = \Store::register_account($place->id, $stock_type, $asset_stock, $currency_id, $amount, $name);
            $arr[] = \Store::register_account($place->id, $expense_type, $expense_sale, $currency_id, $amount, $name);
            \Store::register_account_array($arr, $item->created_at, $transaction_code);
        }
    }
    
}