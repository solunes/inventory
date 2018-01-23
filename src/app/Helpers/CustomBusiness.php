<?php 

namespace Solunes\Inventory\App\Helpers;

use Form;

class CustomInventory {
   
    public static function after_seed_actions() {
        return 'After seed realizado correctamente.';
    }
       
    public static function get_custom_field($name, $parameters, $array, $label, $col, $i, $value, $data_type) {
        // Type = list, item
        $return = NULL;
        /*if($name=='parcial_cost'){
            $return .= \Field::form_input($i, $data_type, ['name'=>'quantity', 'required'=>true, 'type'=>'string'], ['value'=>1, 'label'=>'Cantidad Comprada', 'cols'=>4]);
            //$return .= \Field::form_input($i, $data_type, ['name'=>'total_cost', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Costo Total de Lote', 'cols'=>6], ['readonly'=>true]);
            if(request()->has('purchase_id')){
                $return .= '<input type="hidden" name="purchase_id" value="'.request()->input('purchase_id').'" />';
            }
        }*/
        return $return;
    }

    public static function after_login($user, $last_session, $redirect) {
        return true;
    }
    
    public static function check_permission($type, $module, $node, $action, $id = NULL) {
        // Type = list, item
        $return = 'none';
        /*if($node->name=='accounts-payable'||$node->name=='accounts-receivable'){
            if($type=='item'&&$action=='edit'){
                if($node->name=='accounts-payable'){
                    $pending = \App\AccountsPayable::find($id);
                } else if($node->name=='accounts-receivable'){
                    $pending = \App\AccountsReceivable::find($id);
                }
                if($pending->status=='paid'){
                    $return = 'false';
                }
            }
        }*/
        return $return;
    }

    public static function get_options_relation($submodel, $field, $subnode, $id = NULL) {
        /*if($field->relation_cond=='account_concepts'){
            $node_name = request()->segment(3);
            if($id){
                $node = \Solunes\Master\App\Node::where('name', request()->segment(3))->first();
                $model = \FuncNode::node_check_model($node);
                $model = $model->find($id);
                $submodel = $submodel->where('id', $model->account_id);
            } else {
                if(auth()->check()&&auth()->user()->hasRole('admin')){
                    if($node_name=='income'||$node_name=='accounts-receivable'){
                        $submodel = $submodel->where('code', 'income_other');
                    } else if($node_name=='expense'||$node_name=='accounts-payable'){
                        $submodel = $submodel->whereIn('code', ['expense_operating_com','expense_operating_adm','expense_operating_dep','expense_operating_int','expense_other']);
                    }
                } else {
                    if($node_name=='income'){
                        $submodel = $submodel->where('code', 'income_other');
                    } else if($node_name=='expense'){
                        $submodel = $submodel->where('code', 'expense_other');
                    }
                }
            }
        }*/
        return $submodel;
    }

}