<?php

namespace Solunes\Inventory\App\Console;

use Illuminate\Console\Command;

class AccountCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa que todo el sistema contable cuadre.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->info('Comenzando la prueba.');
        $places = \Solunes\Inventory\App\Place::get();
        foreach($places as $key => $place){
            $this->info('Cuenta de '.$place->name.': '.$key);
            $accounts_array[$key] = $place->id;
            $accounts_names[] = $key;
        }
        $selection = $this->anticipate('Elija una sucursal: ', $accounts_names);
        $id = $accounts_array[$selection];
        $place = \Solunes\Inventory\App\Place::find($id);
        $items = \Solunes\Inventory\App\TransactionCode::get();
        foreach($items as $item){
            $accounts = $place->place_accountability()->where('transaction_code', $item->code)->get();
            $total = 0;
            foreach($accounts as $account){
                $total += $account->real_amount;
            }
            if($total==0){
                $this->info('Asiento Correcto: '.$item->code);
            } else {
                $this->info('ERROR EN ASIENTO: '.$item->code.' de '.$total);
            }
        }
        $this->info('Finalizaron las pruebas.');
    }
}
