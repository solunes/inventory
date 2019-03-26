<?php

namespace Solunes\Inventory\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Asset;

class CustomAdminController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->middleware('auth');
	  $this->middleware('permission:dashboard');
	  $this->prev = $url->previous();
	  $this->module = 'admin';
	}

	public function getIndex() {
		$user = auth()->user();
		//$array['tasks'] = $user->active_inventory_tasks;
		$array['tasks'] = \Solunes\Inventory\App\InventoryTask::limit(2)->get();
		$array['active_issues_inventorys'] = \Solunes\Inventory\App\Inventory::has('active_inventory_issues')->with('active_inventory_issues')->get();
      	return view('inventory::list.dashboard', $array);
	}

	public function getStock($view = NULL, $script = NULL) {
		$array['items'] = \App\AttendancePoint::get();
		$array['prizes'] = \App\Prize::get();
		if($view=='excel'){
        	if($script=='script'){
	        	$dir = public_path('excel-report/'.date('Y-m-d').'/stock');
	        } else {
	        	$dir = public_path('excel');
	        }
	        array_map('unlink', glob($dir.'/*'));
        	$file = \Excel::create('stock-'.date('Y-m-d'), function($excel) use($array) {
        	  $excel->sheet('stock-productos', function($sheet) use($array) {
        	  	$col_array[] = 'Ciudad';
        	  	$col_array[] = 'Punto de Atención';
        	  	foreach($array['prizes'] as $prize){
		          $prizes_count[$prize->id] = 0;
		          $prizes_initial_count[$prize->id] = 0;
        	  	  $col_array[] = $prize->name.' - Inicial';
        	  	  $col_array[] = $prize->name.' - Actual';
        	  	}
	            $sheet->row(1, $col_array);
	            $sheet->row(1, function($row) {
	              $row->setFontWeight('bold');
	            });
	            $sheet->freezeFirstRow();
	            $count = 1;
        	  	foreach($array['items'] as $item){
        	  	  $col_array = [];
        	  	  $count++;
			      $col_array[] = $item->city->name;
			      $col_array[] = $item->name;
        	  	  foreach($array['prizes'] as $prize){
		            $quantity = $item->attendance_point_prizes()->where('prize_id', $prize->id)->first()->quantity;
		            $initial_quantity = $item->attendance_point_prizes()->where('prize_id', $prize->id)->first()->initial_quantity;
		            $prizes_count[$prize->id] += $quantity;
		            $prizes_initial_count[$prize->id] += $initial_quantity;
			        $col_array[] = $initial_quantity;
			        $col_array[] = $quantity;
        	  	  }
	              $sheet->row($count, $col_array);
        	  	}
        	  	$col_array = [];
	        	$count++;
		        $col_array[] = 'TOTALES';
		        $col_array[] = ' ';
		        foreach($array['prizes'] as $prize){
		          $col_array[] = $prizes_initial_count[$prize->id];
		          $col_array[] = $prizes_count[$prize->id];
		        }
	            $sheet->row($count, $col_array);
	            $sheet->row($count, function($row) {
	              	$row->setFontWeight('bold');
	            });
        	  });
        	})->store('xlsx', $dir, true);
        	if($script=='script'){
        		return $file['full'];
        	}
	        return response()->download($file['full']);
		}
		return view('content.stock-report', $array);
	}

    public function getAddStockExcel() {
	    if(auth()->check()){
	      $user = auth()->user();
	    }
	    $prizes = \App\Prize::get();
	    return view('content.add-stock-excel', ['prizes'=>$prizes]);
    }

    public function postAddStockExcel(Request $request) {
	    if(auth()->check()&&$request->hasFile('file')){
	      $user = auth()->user();
	      $count = \Excel::load($request->file('file'), function($reader) use($user) {
	        $sheet = $reader->getSheetByName('INCREMENTO-STOCK');
	        if($sheet){     
	          $row = 1;
	          $points = \App\AttendancePoint::with('attendance_point_prizes','attendance_point_prizes.prize')->get();
	          foreach($points as $point){
	            $row++;
	            foreach($point->attendance_point_prizes as $attendance_point_prize){
	            	$prize = $attendance_point_prize->prize;
	            	if($prize->id==1){
	            		$letter = 'D';
	            	} else if($prize->id==2){
	            		$letter = 'E';
	            	} else if($prize->id==3){
	            		$letter = 'F';
	            	} else if($prize->id==4){
	            		$letter = 'G';
	            	} else if($prize->id==5){
	            		$letter = 'H';
	            	} else if($prize->id==6){
	            		$letter = 'I';
	            	} else if($prize->id==7){
	            		$letter = 'J';
	            	} else if($prize->id==8){
	            		$letter = 'K';
	            	} else if($prize->id==9){
	            		$letter = 'L';
	            	} else if($prize->id==10){
	            		$letter = 'M';
	            	}
	            	$new_quantity = intval($sheet->getCell($letter.$row)->getValue());
	            	if($new_quantity>0){
		            	$total = 0;
		            	$entry_new_quantity = round($new_quantity * $attendance_point_prize->entry_initial_percentage);
		            	$total += $entry_new_quantity;
		            	$mid_new_quantity = round($new_quantity * $attendance_point_prize->mid_initial_percentage);
		            	$total += $mid_new_quantity;
		            	$high_new_quantity = round($new_quantity * $attendance_point_prize->high_initial_percentage);
		            	$total += $high_new_quantity;
		            	$premium_new_quantity = round($new_quantity * $attendance_point_prize->premium_initial_percentage);
		            	$total += $premium_new_quantity;
		                if($new_quantity!=$total){
		                	$new_total = $total-$new_quantity;
		                	$entry_new_quantity -= $new_total;
		                }
		                if($entry_new_quantity<0){
		                	$mid_new_quantity += $entry_new_quantity;
		                	$entry_new_quantity = 0;
		                }
		                if($mid_new_quantity<0){
		                	$high_new_quantity += $mid_new_quantity;
		                	$mid_new_quantity = 0;
		                }
		                if($high_new_quantity<0){
		                	$premium_new_quantity += $high_new_quantity;
		                	$high_new_quantity = 0;
		                }
		                if($entry_new_quantity>0){
			                $attendance_point_prize->entry = $attendance_point_prize->entry + $entry_new_quantity;
			                $attendance_point_prize->entry_initial = $attendance_point_prize->entry_initial + $entry_new_quantity;
		                }
		                if($mid_new_quantity>0){
			                $attendance_point_prize->mid = $attendance_point_prize->mid + $mid_new_quantity;
			                $attendance_point_prize->mid_initial = $attendance_point_prize->mid_initial + $mid_new_quantity;
		                }
		                if($high_new_quantity>0){
			                $attendance_point_prize->high = $attendance_point_prize->high + $high_new_quantity;
			                $attendance_point_prize->high_initial = $attendance_point_prize->high_initial + $high_new_quantity;
		                }
		                if($premium_new_quantity>0){
			                $attendance_point_prize->premium = $attendance_point_prize->premium + $premium_new_quantity;
			                $attendance_point_prize->premium_initial = $attendance_point_prize->premium_initial + $premium_new_quantity;
		                }
		                $attendance_point_prize->quantity = $attendance_point_prize->quantity + $new_quantity;
		                $attendance_point_prize->initial_quantity = $attendance_point_prize->initial_quantity + $new_quantity;
		                $attendance_point_prize->save();
	            	}
	            }
	          }
	        }
	      });

	      return redirect($this->prev)->with('message_success', 'Su stock fue subido correctamente.');
	    } else {
	      return redirect($this->prev)->with('message_error', 'Por favor, seleccione un archivo válido.');
	    }
    }

	public function getTransferStock() {
		$attendance_points = [''=>'Seleccionar']+\Solunes\Master\App\Agency::lists('name','id')->toArray();
		$segments = [''=>'Seleccionar', 'entry'=>'Entry', 'mid'=>'Mid', 'high'=>'High', 'premium'=>'Premium'];
		$array = ['attendance_points'=>$attendance_points, 'segments'=>$segments, 'prizes'=>[], 'quantities'=>[0=>0], 'prizes_to'=>[], 'attendance_point_id_from'=>NULL, 'segment_from'=>NULL, 'prize_id_from'=>NULL, 'attendance_point_id_to'=>NULL, 'segment_to'=>NULL];
		if(request()->has('attendance_point_id_from')){
			$array['attendance_point_id_from']  = request()->input('attendance_point_id_from');
		}
		if(request()->has('attendance_point_id_to')){
			$array['attendance_point_id_to']  = request()->input('attendance_point_id_to');
		}
		if(request()->has('segment_from')){
			$array['segment_from']  = request()->input('segment_from');
		}
		if(request()->has('segment_to')){
			$array['segment_to']  = request()->input('segment_to');
		}
		if($array['attendance_point_id_from']&&$array['attendance_point_id_to']&&$array['segment_from']&&$array['segment_to']){
			$prizes = \App\Prize::lists('name', 'id')->toArray();
			$attendance_point_segment_from = \App\AttendancePointPrize::where('attendance_point_id', $array['attendance_point_id_from'])->where($array['segment_from'], '>', 0)->lists($array['segment_from'], 'prize_id')->toArray();
			foreach($attendance_point_segment_from as $subitem_prize_id => $subitem){
				$array['prizes'][$subitem_prize_id] = $prizes[$subitem_prize_id].' - '.$subitem;
			}
			$attendance_point_segment_to = \App\AttendancePointPrize::where('attendance_point_id', $array['attendance_point_id_to'])->where($array['segment_to'], '>', 0)->lists($array['segment_to'], 'prize_id')->toArray();
			$array['prizes_to'] = [];
			foreach($prizes as $prize_id => $prize_name){
				$array['prizes_to'][$prize_id] = $prize_name.' - 0';
			}
			foreach($attendance_point_segment_to as $subitem_prize_id => $subitem){
				$array['prizes_to'][$subitem_prize_id] = $prizes[$subitem_prize_id].' - '.$subitem;
			}
			if(request()->has('prize_id_from')){
				$array['prize_id_from']  = request()->input('prize_id_from');
				$attendance_point_segment_to_2 = \App\AttendancePointPrize::where('attendance_point_id', $array['attendance_point_id_from'])->where('prize_id', $array['prize_id_from'])->first()->$array['segment_from'];
				$quantities = range(1, $attendance_point_segment_to_2);
				$array['quantities']  = array_combine($quantities, $quantities);				
			}
		}
		return view('content.transfer-stock', $array);
	}

	public function postTransferStock(Request $request) {
		$attendance_point_id_from = \App\AttendancePoint::find($request->input('attendance_point_id_from'));
		$attendance_point_id_to = \App\AttendancePoint::find($request->input('attendance_point_id_to'));
		$segment_from = $request->input('segment_from');
		$segment_to = $request->input('segment_to');
		$prize = \App\Prize::find($request->input('prize_id_from'));
		$quantity = $request->input('quantity');
		if($attendance_point_id_from&&$attendance_point_id_to&&$segment_from&&$segment_to&&$prize&&$quantity>0){
			\Func::reduce_inventory($attendance_point_id_from, $prize, $segment_from, $quantity);
			\Func::increase_inventory($attendance_point_id_to, $prize, $segment_to, $quantity);
			return redirect($this->prev)->with('message_success', 'El item fue transferido correctamente.');
		}
		return redirect($this->prev)->with('message_error', 'El item no pudo ser transferido. Consulte con el administrador.');
	}


}