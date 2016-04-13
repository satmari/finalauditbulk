<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Batch;
use App\Garment;
use App\Defect;
use App\Category;
use DB;
use Auth;
use App\User;

class ControllerBatch extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		//
		try {
			$name_id = Auth::user()->name_id;
			// dd($name_id);
			$user = User::find(Auth::id());
			
			if (($user->is('admin')) OR ($user->is('guest'))) { 
			    
			    $batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE deleted = 0 ORDER BY id asc"));
				return view('batch.index', compact('batch'));
			}
			if ($user->is('operator')) { 
			    
			    $batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_user = '".$name_id."' AND deleted = 0 ORDER BY id asc"));
				return view('batch.index', compact('batch'));
			}
			
			
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch');
		}
	}

	public function searchinteos()
	{
		//
		try {
			return view('batch.searchinteos');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('batch.searchinteos');
		}
	}

	public function searchinteos_store(Request $request)
	{	
		//
		$this->validate($request, ['cb_code' => 'required|min:12|max:13']);

		$input = $request->all(); // change use (delete or comment user Requestl; )
		//1971107960

		$cbcode = $input['cb_code'];
		// dd($cbcode);
		
		$msg ='';

		// Test database
		// $inteos = DB::connection('sqlsrv2')->select(DB::raw("SELECT [CNF_BlueBox].INTKEY,[CNF_BlueBox].IntKeyPO,[CNF_BlueBox].BlueBoxNum,[CNF_BlueBox].BoxQuant, [CNF_PO].POnum,[CNF_SKU].Variant,[CNF_SKU].ClrDesc,[CNF_STYLE].StyCod FROM [BdkCLZGtest].[dbo].[CNF_BlueBox] FULL outer join [BdkCLZGtest].[dbo].CNF_PO on [CNF_PO].INTKEY = [CNF_BlueBox].IntKeyPO FULL outer join [BdkCLZGtest].[dbo].[CNF_SKU] on [CNF_SKU].INTKEY = [CNF_PO].SKUKEY FULL outer join [BdkCLZGtest].[dbo].[CNF_STYLE] on [CNF_STYLE].INTKEY = [CNF_SKU].STYKEY WHERE [CNF_BlueBox].INTKEY =  :somevariable"), array(
		// 	'somevariable' => $inteosbbcode,
		// ));

		// Live database
		try {
			$inteos = DB::connection('sqlsrv2')->select(DB::raw("SELECT 	
			/*[CNF_CartonBox].IntKeyPO, */
			[CNF_CartonBox].BoxNum,
			[CNF_CartonBox].BoxQuant,
			[CNF_CartonBox].Produced,
			(CASE	WHEN [CNF_CartonBox].Status = '0' THEN 'New' 
					WHEN [CNF_CartonBox].Status = '20' THEN 'On Module' 
					WHEN [CNF_CartonBox].Status = '99' THEN 'Completed'
			END) AS CB_Status,
			/*[CNF_CartonBox].Module, */
			/*[CNF_CartonBox].BBcreated, */
			/*[CNF_CartonBox].BBalternativ,*/
			[CNF_CartonBox].CREATEDATE,
			[CNF_CartonBox].EDITDATE,

			[CNF_BlueBox].BlueBoxNum,
			
			/*[CNF_PO].BoxComplete,*/
			/*[CNF_PO].BoxQuant,*/
			/*[CNF_PO].Line,*/
			[CNF_PO].POnum,

			/*[CNF_SKU].StyDesc,*/
			[CNF_SKU].Variant,
			/*[CNF_SKU].ClrDesc,*/
			
			[CNF_STYLE].StyCod,
			
			[CNF_Modules].ModNam
			
			FROM [BdkCLZG].[dbo].[CNF_CartonBox]

			FULL outer join [BdkCLZG].[dbo].[CNF_PO] on [CNF_PO].INTKEY = [CNF_CartonBox].IntKeyPO
			FULL outer join [BdkCLZG].[dbo].[CNF_BlueBox] on [CNF_BlueBox].INTKEY = [CNF_CartonBox].BBalternativ
			FULL outer join [BdkCLZG].[dbo].[CNF_Modules] on [CNF_Modules].Module = [CNF_CartonBox].Module
			FULL outer join [BdkCLZG].[dbo].[CNF_SKU] on [CNF_SKU].INTKEY = [CNF_PO].SKUKEY
			FULL outer join [BdkCLZG].[dbo].[CNF_STYLE] on [CNF_STYLE].INTKEY = [CNF_SKU].STYKEY
			
			where BoxNum = :somevariable"), array(
			'somevariable' => $cbcode,
			));

			// dd($inteos);
			
			if ($inteos) {
				//continue
			} else {
	        	$msg = 'Cannot find CB in Inteos';
	        	return view('batch.error', compact('msg'));
	    	}

			function object_to_array($data)
			{
			    if (is_array($data) || is_object($data))
			    {
			        $result = array();
			        foreach ($data as $key => $value)
			        {
			            $result[$key] = object_to_array($value);
			        }
			        return $result;
			    }
			    return $data;
			}
		
	    	$inteos_array = object_to_array($inteos);

	    	if (Auth::check())
			{
				$name_id = Auth::user()->name_id;
			    $username = Auth::user()->username;
			} else {
				$msg = 'User is not autenticated';
				return view('batch.error',compact('msg'));
			}

	    	$checked_by_name = $username;
	    	$checked_by_id = $name_id;
	    	$batch_date = date("Ymd");
	    	$batch_user = $name_id;

	    	$today_batch_byuser = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->count();

		   	$batch_order_num = $today_batch_byuser + 1;
		   	$batch_order = str_pad($batch_order_num, 3, "0", STR_PAD_LEFT); 
		   	
	    	$batch_name = $batch_date."-".$batch_user."-".$batch_order;
	    	
	    	$style = $inteos_array[0]['StyCod'];
	    	$variant = $inteos_array[0]['Variant'];
	    	$sku = $style." ".$variant;
	    	list($color, $size) = explode('-', $variant);

	    	$po = $inteos_array[0]['POnum'];

	  		//$brand = substr($po, 2, 1); // T;I;C
			
			$models = DB::connection('sqlsrv')->select(DB::raw("SELECT category_name,category_id,model_brand FROM models WHERE model_name = '".$style."'"));
			
	    	if ($models) {
	    		$brand = $models[0]->model_brand;
				$category_name = $models[0]->category_name;
				$category_id = $models[0]->category_id;
			} else {
	        	$msg = 'Cannot find Style  '.$style.'  in Model table';
	        	return view('batch.error', compact('msg'));
	    	}

	    	$module_name = $inteos_array[0]['ModNam'];
			
	    	$cartonbox = $cbcode;	//$inteos_array[0]['BoxNum'];
	    	$cartonbox_qty = $inteos_array[0]['BoxQuant'];
	    	$cartonbox_produced = $inteos_array[0]['Produced'];
	    	if ($cartonbox_produced > 0) {
				//continue
			} else {
				$msg = 'Carton box have 0 quantity inside';
	        	return view('batch.error', compact('msg'));
			}

	    	$cartonbox_status = $inteos_array[0]['CB_Status'];
	    	if ($cartonbox_status == "Completed") {
				//continue
			} else {
				$msg = 'Carton box is NOT completed in Inteos (on Module)';
	        	return view('batch.error', compact('msg'));
			}

	    	$cartonbox_start_date_tmp = $inteos_array[0]['CREATEDATE'];
	    	$timestamp_s = strtotime($cartonbox_start_date_tmp);
			$cartonbox_start_date = date('Y-m-d H:i:s', $timestamp_s);
	    	$cartonbox_finish_date_tmp = $inteos_array[0]['EDITDATE'];
	    	$timestamp_f = strtotime($cartonbox_finish_date_tmp);
			$cartonbox_finish_date = date('Y-m-d H:i:s', $timestamp_f);

	    	$bluebox = $inteos_array[0]['BlueBoxNum']; 	
	    	
	    	if ($brand == "TEZENIS") {
				$batch_brand = "batch_ts";
			} elseif ($brand == "INTIMISSIMI") {
				$batch_brand = "batch_is";
			} elseif ($brand == "CALZEDONIA") {
				$batch_brand = "batch_cs";
			} 
	    	
	    	$batch_brand_table = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM ".$batch_brand." WHERE batch_min <= '".$cartonbox_produced."' AND batch_max >= '".$cartonbox_produced."'"));
			// dd($batch_brand_table);

		  	if ($batch_brand_table) {
		  		$batch_qty = $batch_brand_table[0]->batch_check;
		  		$batch_brand_id = $batch_brand_table[0]->batch_id;
		  		$batch_brand_min = $batch_brand_table[0]->batch_min;
		  		$batch_brand_max = $batch_brand_table[0]->batch_max;
		  		$batch_brand_max_reject = $batch_brand_table[0]->batch_reject;
			} else {
		      	$msg = 'Cannot find proper line in Batch table for this Brand';
		      	return view('batch.error', compact('msg'));
		  	}

			$rejected = 0; // exist but ?
			$batch_status = "Pending"; // new batch
			
			try {
				$table = new Batch;

				$table->checked_by_name = $checked_by_name;
				$table->checked_by_id = $checked_by_id;
				
				$table->batch_name = $batch_name;
				$table->batch_date = $batch_date;
				$table->batch_user = $batch_user;
				$table->batch_order = $batch_order;

				$table->sku = $sku;
				$table->style = $style;
				$table->color = $color;
				$table->size = $size;

				$table->po = $po;
				$table->brand = $brand;
				$table->category_name = $category_name;
				$table->category_id = $category_id;

				$table->module_name = $module_name;
				
				$table->cartonbox = $cartonbox;
				$table->cartonbox_qty = $cartonbox_qty;
				$table->cartonbox_produced = $cartonbox_produced;
				$table->cartonbox_status = $cartonbox_status;
				$table->cartonbox_start_date = $cartonbox_start_date;
				$table->cartonbox_finish_date = $cartonbox_finish_date;

				$table->bluebox = $bluebox;
				
				$table->batch_qty = $batch_qty;
				$table->batch_brand_id = $batch_brand_id;
				$table->batch_brand_min = $batch_brand_min;
				$table->batch_brand_max =  $batch_brand_max;
				$table->batch_brand_max_reject = $batch_brand_max_reject;

				$table->rejected = $rejected;
				$table->batch_status = $batch_status;

				$table->deleted = FALSE;
						
				$table->save();
			}
			catch (\Illuminate\Database\QueryException $e) {
				$msg = "Problem to save batch in table";
				return view('batch.error',compact('msg'));
			}

			$batch_qty;

			for ($i=1; $i < $batch_qty+1 ; $i++) { 
				
				$times = $i; //1
				// dd($i);

				$garment_order = str_pad($i, 2, "0", STR_PAD_LEFT);
				$garment_name = $batch_date."-".$batch_user."-".$batch_order."-".$garment_order;
				$garment_status = "Accepted";

				try {
					$table = new Garment;

					$table->garment_name = $garment_name;
					$table->garment_order = $garment_order;

					$table->batch_name = $batch_name;

					$table->cartonbox = $cartonbox;

					$table->sku = $sku;

					$table->po = $po;
					$table->brand = $brand;
					$table->category_id = $category_id;
					$table->category_name = $category_name;
					
					$table->garment_status = $garment_status;

					$table->deleted = FALSE;
							
					$table->save();
				}
				catch (\Illuminate\Database\QueryException $e) {
					$msg = "Problem to save garment in table";
					return view('batch.error',compact('msg'));
				}
			}
			// return Redirect::to('/batch');
			return Redirect::to('/garment/by_batch/'.$batch_name);
		}
		catch (\Illuminate\Database\QueryException $e) {
			//return Redirect::to('/searchinteos');
			$msg = "Problem to save batch in table. try agan.";
			return view('batch.error',compact('msg'));
		}	
	}

	public function inside()
	{
		//
		try {
			return view('batch.searchinteos');	
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('batch.searchinteos');		
		}
	}

	public function confirm($id) {

		// 
		try {
			$batchid = Batch::findOrFail($id);
			$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_name = '".$batchid->batch_name."'"));
			$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE batch_name = '".$batchid->batch_name."'"));
			$defects = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect WHERE batch_name = '".$batchid->batch_name."'"));

			$total_defects = DB::table('defect')
			                    ->where('batch_name', '=', $batchid->batch_name)
			                    ->where('deleted', '=', FALSE)
			                    ->count();

			$total_rejected_defects = DB::table('defect')
			                    	->where('batch_name', '=', $batchid->batch_name)
			                    	->where('deleted', '=', FALSE)
			                    	->where('defect_level_rejected', '=', "YES")
			                    	->count();

			$total_rejected_garments = DB::table('garment')
			                    	->where('batch_name', '=', $batchid->batch_name)
			                    	->where('deleted', '=', FALSE)
			                    	->where('garment_status', '=', "Rejected")
			                    	->count();

			foreach ($batch as $b) {
				$batch_brand_max_reject = $b->batch_brand_max_reject;
			}

			if ($batch_brand_max_reject < $total_rejected_garments) {
				$suggestion = "Reject";
			} else {
				$suggestion = "Accept";
			}

			return view('batch.confirm',compact('batch','batchid','garments','defects','total_defects','total_rejected_defects','total_rejected_garments','suggestion'));		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch/confirm/'.$id);
		}
	}

	public function accept($id) {
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Accept";
			$batch->save();
			return Redirect::to('/batch/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch/accept/'.$id);
		}
	}

	public function acceptwithreservetion($id) {
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Accept with reservation";
			$batch->save();
			return Redirect::to('/batch/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch/acceptwithreservetion/'.$id);
		}
	}

	public function reject($id) {
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Reject";
			$batch->save();
			return Redirect::to('/batch/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch/reject/'.$id);
		}
	}

	public function suspend($id) {
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Suspend";
			$batch->save();
			return Redirect::to('/batch/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch/suspend/'.$id);
		}
	}

	public function delete($id) {

		try {
			$batch = Batch::findOrFail($id);
			$batch->deleted = TRUE;
			$batch->batch_status = "Deleted";
			$batch->save();

			$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE batch_name = '".$batch->batch_name."'"));
			foreach ($garments as $garment) {
				$gar = Garment::findOrFail($garment->id);
				$gar->deleted = TRUE;
				$gar->garment_status = "Deleted";
				$gar->save();
			}

			$defects = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect WHERE batch_name = '".$batch->batch_name."'"));
			foreach ($defects as $defect) {
				$def = Defect::findOrFail($defect->id);
				$def->deleted = TRUE;
				$def->save();
			}
			return Redirect::to('/batch');	
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch/delete/'.$id);
		}
	}
}
