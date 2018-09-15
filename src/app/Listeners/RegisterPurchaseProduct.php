<?php

namespace Solunes\Store\App\Listeners;

class RegisterPurchaseProduct {

    public function handle($event) {
        // Revisar que tenga una sesión y sea un modelo del sitio web.
        if($event){
            
            /* Crear movimiento de inventario */
            $place = $event->parent->place;
            $name = 'Compra de Productos';
            $response = \Store::inventory_movement($place, $event->product, 'move_in', $event->quantity, $name, 'register_product_purchase', $event);
            
            $partner = $event->partner;
            //$partener_capital = $partner->capital - $event->investment;
            /* Crear cuentas de aporte de capital si no existe */
            //if($partener_capital<0){
                //$partener_capital = abs($partener_capital);
                $partener_capital = $event->investment;
                $partner_movement = new \Solunes\Store\App\PartnerMovement;
                $partner_movement->parent_id = $event->partner_id;
                $partner_movement->place_id = $event->parent->place_id;
                $partner_movement->name = 'Aporte de capital para compra de mercadería.';
                $partner_movement->type = 'move_in';
                $partner_movement->currency_id = $event->currency_id;
                $partner_movement->amount = $partener_capital;
                $partner_movement->created_at = $event->created_at;
                $partner_movement->save();
                $partener_capital = 0;
            //}
            $partner->capital = $partener_capital;
            $partner->save();

            $event->load('partner_transport');
            $partner_transport = $event->partner_transport;
            //$partener_capital = $partner_transport->capital - $event->transport_investment;
            /* Crear cuentas de aporte de capital si no existe */
            //if($partener_capital<0){
                //$partener_capital = abs($partener_capital);
                $partener_capital = $event->transport_investment;
                $partner_movement = new \Solunes\Store\App\PartnerMovement;
                $partner_movement->parent_id = $event->partner_transport_id;
                $partner_movement->place_id = $event->parent->place_id;
                $partner_movement->name = 'Aporte de capital para el transporte de mercaderia.';
                $partner_movement->type = 'move_in';
                $partner_movement->currency_id = $event->currency_id;
                $partner_movement->amount = $partener_capital;
                $partner_movement->created_at = $event->created_at;
                $partner_movement->save();
                $partener_capital = 0;
//
            $partner_transport->capital = $partener_capital;
            $partner_transport->save();
            
            /* Crear cuentas de compra de Inventario */
            $asset_cash = \Solunes\Store\App\Account::getCode('asset_cash_big')->id;
            $asset_stock = \Solunes\Store\App\Account::getCode('asset_stock')->id;
            $name = 'Compra de Mercadería en Inventario';
            $place_id = $event->parent->place_id;
            $arr[] = \Store::register_account($place_id, 'credit', $asset_cash, $event->currency_id, $event->total, $name);
            $arr[] = \Store::register_account($place_id, 'debit', $asset_stock, $event->currency_id, $event->total, $name);
            \Store::register_account_array($arr, $event->created_at);

            return $event;
        }

    }

}
