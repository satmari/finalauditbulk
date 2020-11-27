<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Batch;
use App\Garment;
use App\Batch_bulk;
use App\Garment_bulk;
use App\Producer;
use App\Defect;
use App\Category;
use App\Ecommerce;
use App\Sizeset;
use App\ActivityLog;
use DB;
use Auth;
use App\User;

use Session;

use App\BatchCartonbox;

class ControllerBatch_bulk extends Controller {

	public function __construct()
	{	
		// Auth::loginUsingId(5);
		$this->middleware('auth');
	}

	public function index()
	{
		//
		// Set session
    	// Session::set('style', NULL);
    	// Session::set('size', NULL);
    	// Session::set('color', NULL);

    	// $ses_style = Session::get('style');
	    // $ses_size = Session::get('size');
	    // $ses_color = Session::get('color');
	    // $ses_batch_name = Session::get('batch_name');
	    // $ses_producer = Session::get('producer');

	    // if (($ses_producer == NULL)) {
	    // 	return view('batch_bulk.selectproducertype');
	    // } else if (($ses_style == NULL) AND ($ses_size == NULL) AND ($ses_color == NULL) AND ($ses_batch_name == NULL)) {
	    // 	return view('batch_bulk.searchinteos');
	    // }
	

		try {
			$name_id = Auth::user()->name_id;
			// dd($name_id);
			$user = User::find(Auth::id());
			
			if (($user->is('admin')) OR ($user->is('guest'))) { 
			    
			    //$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE deleted = 0 ORDER BY id asc"));

			     $batch = DB::connection('sqlsrv')->select(DB::raw("SELECT 
																*,
																(SELECT COUNT(garment.batch_name) FROM garment WHERE garment.batch_name = batch.batch_name AND garment.garment_status = 'Rejected') as RejectedCount
																FROM batch 
																WHERE (batch.deleted = 0) AND created_at >= DATEADD(day,-30,GETDATE())
																ORDER BY batch.id desc"));

			    $batch_date = date("Ymd");
	    		
			    $total_checked_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '!=', 'Not checked')
			                    ->count();
				// dd($total_checked_batch);

			    $total_accept_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Accept')
			                    ->count();
				// dd($total_accept_batch);

			    $total_reject_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Reject')
			                    ->count();
				// dd($total_reject_batch);

			    $total_suspend_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Suspend')
			                    ->count();
				// dd($total_suspend_batch);

			    $total_not_checked_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Not checked')
			                    ->count();
				// dd($total_not_checked_batch);

				$total_garments_today = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '!=', 'Not checked')
			                    ->sum('batch_qty');
				// dd($total_suspend_batch);		

				$total_garments_not_today = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Not checked')
			                    ->sum('batch_qty');
				// dd($total_garments_not_today);	                    

				return view('batch_bulk.index', compact('batch','total_checked_batch','total_accept_batch','total_reject_batch','total_suspend_batch', 'total_not_checked_batch', 'total_garments_today','total_garments_not_today'));
			}
			if ($user->is('operator')) {
			    
			    //$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_user = '".$name_id."' AND deleted = 0 ORDER BY id asc"));
				
			    $batch = DB::connection('sqlsrv')->select(DB::raw("SELECT 
																*,
																(SELECT COUNT(garment.batch_name) FROM garment WHERE garment.batch_name = batch.batch_name AND garment.garment_status = 'Rejected') as RejectedCount
																FROM batch 
																WHERE (batch.batch_user = '".$name_id."') AND 
																(batch.deleted = 0) AND 
																((CAST(batch.created_at AS DATE) = CAST(GETDATE() AS DATE)) OR
																((batch.batch_status = 'Pending') OR (batch.batch_status = 'Suspend')))
																ORDER BY batch.id asc"));
				
				/* // with mandatory to check
			    $batch = DB::connection('sqlsrv')->select(DB::raw("SELECT 
																*,
																(SELECT COUNT(garment.batch_name) FROM garment WHERE garment.batch_name = batch.batch_name AND garment.garment_status = 'Rejected') as RejectedCount,
																(SELECT mandatory_to_check FROM models WHERE models.model_name = batch.style) as to_check
																FROM batch 
																WHERE (batch.batch_user = '".$name_id."') AND 
																(batch.deleted = 0) AND 
																((CAST(batch.created_at AS DATE) = CAST(GETDATE() AS DATE)) OR ((batch.batch_status = 'Pending') OR (batch.batch_status = 'Suspend') /* OR (batch.batch_status = 'Not checked'))) 
																ORDER BY batch.id asc"));
				*/

			    // dd($batch);
				// $total_checked_garments =  DB::connection('sqlsrv')->select(DB::raw("SELECT (COUNT(*))
				//  											    FROM garment
				//  												JOIN batch ON batch.batch_name = garment.batch_name
				//  											    WHERE (CAST(garment.created_at AS DATE) = CAST(GETDATE() AS DATE)) AND batch.batch_user = '".$name_id."')"));

			    $batch_date = date("Ymd");
	    		$batch_user = $name_id;

			    $total_checked_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('batch_status', '!=', 'Not checked')
			                    ->where('deleted', '=', 0)
			                    ->count();
				// dd($total_checked_batch);

			    $total_accept_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Accept')
			                    ->count();
				// dd($total_accept_batch);

			    $total_reject_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Reject')
			                    ->count();
				// dd($total_reject_batch);

			    $total_suspend_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Suspend')
			                    ->count();
				// dd($total_suspend_batch);

			    $total_not_checked_batch = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Not checked')
			                    ->count();
				// dd($total_not_checked_batch);

			    $total_garments_today = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '!=', 'Not checked')
			                    ->sum('batch_qty');
				// dd($total_suspend_batch);

			    $total_garments_not_today = DB::table('batch')
			                    ->where('batch_date', '=', $batch_date)
			                    ->where('batch_user', '=', $batch_user)
			                    ->where('deleted', '=', 0)
			                    ->where('batch_status', '=', 'Not checked')
			                    ->sum('batch_qty');
				// dd($total_garments_not_today);
			    
		 		$activity = DB::table('activity_log')
			                    ->where('activity_by_id', '=', $batch_user)
			                    ->where('status', '=', 'Active')
			                    ->count();
			    //dd($activity);

				return view('batch.index', compact('batch','total_checked_batch','total_accept_batch','total_reject_batch','total_suspend_batch', 'total_not_checked_batch', 'total_garments_today','total_garments_not_today','activity'));
			}
			
			
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch');
		}
	}

	public function selectproducertype_bulk()
	{
		//
		$company = Auth::user()->company;
		// dd($company);

		if ($company == 'zalli') {
			try {
				return view('batch_bulk.selectproducertype_bulk');
			}
			catch (\Illuminate\Database\QueryException $e) {
				return view('batch_bulk.selectproducertype_bulk');
			}
		} elseif ($company == 'gordon') {

			// Session::set('producer', 'test');
			try {
				return view('batch_bulk.searchinteos');
			}
			catch (\Illuminate\Database\QueryException $e) {
				return view('batch_bulk.searchinteos');
			}
		}

	}

	public function selectproducer_bulk($type)
	{
		//
		// $this->validate($request, ['type' => 'required']);
		// $input = $request->all();
		
		// dd($type);

		$list_of_producers = Producer::orderBy('id')->where('producer_type','=',$type)->lists('producer_name','id');
		
		try {
			return view('batch_bulk.selectproducer_bulk', compact('list_of_producers'));
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('batch_bulk.selectproducer_bulk', compact('list_of_producers'));
		}
	}

	public function searchinteos_bulk(Request $request)
	{	
		if ((Session::get('producer')) == NULL) {
			$this->validate($request, ['producer_id' => 'required']);
			$input = $request->all();

			$producer = Producer::findOrFail($input['producer_id']);
			Session::set('producer', $producer);
			// dd($producer." was null");
		} else {
			//$producer = Session::get('producer');
			// dd($producer."was set");
		}
		
		try {
			return view('batch_bulk.searchinteos');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('batch_bulk.searchinteos');
		}
	}

	public function searchinteos_store_bulk(Request $request)
	{	
		//
		$this->validate($request, ['cb_code' => 'required']);
		$input = $request->all();
		//Zalli
		//600000819685
		//600000819691
		// Gordon
		//700000266053

		$cbcode = $input['cb_code'];
		$cartonbox = $cbcode;
		// dd($cbcode);

		$msg = '';
		$msg1 = '';
		//$msg2 = '';

		
		// User authenticated--------------
		if (Auth::check())
		{
			$name_id = Auth::user()->name_id;
		    $username = Auth::user()->username;
		    $company = Auth::user()->company;
		} else {
			$msg = 'Потребителят не е оторизиран !';
			return view('batch.error',compact('msg'));
		}
		//---------------------------------

	if ($company == 'gordon') {

// Gordon Inteos Live -----------------------------
			
			$inteos = DB::connection('sqlsrv2')->select(DB::raw("SELECT 	
			[CNF_CartonBox].IntKeyPO, 
			[CNF_CartonBox].BoxNum,
			[CNF_CartonBox].BoxQuant,
			[CNF_CartonBox].Produced,
			(CASE	WHEN [CNF_CartonBox].Status = '0' THEN 'New' 
					WHEN [CNF_CartonBox].Status = '20' THEN 'On Module' 
					WHEN [CNF_CartonBox].Status = '99' THEN 'Completed'
			END) AS CB_Status,
			[CNF_CartonBox].Module, 
			[CNF_CartonBox].BBcreated, 
			[CNF_CartonBox].BBalternativ,
			[CNF_CartonBox].CREATEDATE,
			[CNF_CartonBox].EDITDATE,

			[CNF_BlueBox].BlueBoxNum,
			
			[CNF_PO].BoxComplete,
			[CNF_PO].BoxQuant,
			[CNF_PO].Line,
			[CNF_PO].POnum,

			[CNF_SKU].StyDesc,
			[CNF_SKU].Variant,
			[CNF_SKU].ClrDesc,
			
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
	        	$msg = 'Cannot find CB in Inteos, NE POSTOJI KARTONSKA KUTIJA U INTEOSU !';
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

	    	$style = $inteos_array[0]['StyCod'];
	    	$variant = $inteos_array[0]['Variant'];
	    	$sku = $style." ".$variant;
	    	list($color, $size) = explode('-', $variant);

	    	$cartonbox = $cbcode;	//$inteos_array[0]['BoxNum'];
	    	$cartonbox_qty = $inteos_array[0]['BoxQuant'];
	    	$cartonbox_produced = $inteos_array[0]['Produced'];
	    	if ($cartonbox_produced > 0) {
				//continue
			} else {
				$msg = 'Carton box have 0 quantity inside, KUTIJA IMA 0 KOMADA! ';
	        	return view('batch.error', compact('msg'));
			}

			$po = $inteos_array[0]['POnum'];

			// $module_name = $inteos_array[0]['ModNam'];
			$producer_id = NULL;			
	    	$producer = $inteos_array[0]['ModNam'];
	    	$producer_type = NULL;
	    	
	    	$cartonbox_start_date_tmp = $inteos_array[0]['CREATEDATE'];
	    	$timestamp_s = strtotime($cartonbox_start_date_tmp);
			$cartonbox_start_date = date('Y-m-d H:i:s', $timestamp_s);
	    	$cartonbox_finish_date_tmp = $inteos_array[0]['EDITDATE'];
	    	$timestamp_f = strtotime($cartonbox_finish_date_tmp);
			$cartonbox_finish_date = date('Y-m-d H:i:s', $timestamp_f);
	   		$bluebox = $inteos_array[0]['BlueBoxNum']; 	

	   		$cartonbox_status = $inteos_array[0]['CB_Status'];
	    	if ($cartonbox_status == "Completed") {
				//continue
			} else {
				$msg = 'Carton box is NOT completed in Inteos (on Module), KUTIJA NIJE ZAVRSENA U MODULU! ';
	        	return view('batch.error', compact('msg'));
			}		
// ------------------------------------------------
	
	} elseif ($company == 'zalli') {

// Zalli Navision --------------------------------
	    $inteos = DB::connection('sqlsrv3')->select(DB::raw("SELECT 
	      	[Barcode]
	      	,[Item No_]
	      	,[Variant Code]
	      	,[Color Code]
	     	,[Size Code]
	    	,[Pieces in box]
	     	,[Prod_Order No_]
	    	,[ORDER_COMMESSA]
	    	,[Box Number]
	     	,[Subcontractor code]
	    	,[User ID]
	    	,[Creation Date]
	      
	  		FROM [ZALLI_live\$Box Lables]
	  		WHERE [Barcode] = :somevariable"), array(
			'somevariable' => $cbcode,
			));
		
		if ($inteos) {
			//continue
		} else {
        	$msg = 'Няма БарКод в Навижън!';
        	return view('batch_bulk.error', compact('msg'));
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

    	$style = $inteos_array[0]['Item No_'];
    	$variant = $inteos_array[0]['Variant Code'];
    	$sku = $style." ".$variant;
    	//list($color, $size) = explode('-', $variant);

    	// $cartonbox_qty = $inteos_array[0]['BoxQuant'];
	    $cartonbox_produced = intval($inteos_array[0]['Pieces in box']);
	    // $cartonbox_produced = intval($inteos_array[0]['Produced']);

	    // dd($cartonbox_produced);
    	if ($cartonbox_produced > 0) {
			//continue
			// dd($cartonbox_produced);
		} else {
			$msg = 'Кашонът е с нулево количество !';
        	return view('batch_bulk.error', compact('msg'));
		}

    	$color = $inteos_array[0]['Color Code'];
    	$size = $inteos_array[0]['Size Code'];
    	$po = $inteos_array[0]['ORDER_COMMESSA'];

    	$ses_producer = Session::get('producer');

    	if  ($ses_producer != NULL ){
    		$producer = $ses_producer->producer_name;
			$producer_id = $ses_producer->producer_id;
			$producer_type = $ses_producer->producer_type;
    	} else {
    		$msg = 'Producer not selected!';
        	return view('batch.error_back', compact('msg'));
    	}		
//-------------------------------------------------
    
	}

		$checked_by_name = $username;
    	$checked_by_id = $name_id;
    	//dd($name_id);
    	$batch_date = date("Ymd");
    	$batch_user = $name_id;

    	$today_batch_byuser = DB::table('batch')
		                    ->where('batch_date', '=', $batch_date)
		                    ->where('batch_user', '=', $batch_user)
		                    ->count();
		
		//Get from session-----------------
		$ses_style = Session::get('style');
	    $ses_size = Session::get('size');
	    $ses_color = Session::get('color');
	    $ses_batch_name = Session::get('batch_name');
		// --------------------------------


	    if (($ses_style == NULL) AND ($ses_size == NULL) AND ($ses_color == NULL) AND ($ses_batch_name == NULL)) {
	    	// first box and new batch

		   	$batch_order_num = $today_batch_byuser + 1;
		   	$batch_order = str_pad($batch_order_num, 3, "0", STR_PAD_LEFT); 
		   	
	    	$batch_name = $batch_date."-".$batch_user."-".$batch_order."-B";
	    	$batch_type = 'BULK';

	    	$models = DB::connection('sqlsrv')->select(DB::raw("SELECT category_name,category_id,model_brand,mandatory_to_check FROM models WHERE model_name = '".$style."'"));
			
	    	if ($models) {
	    		$brand = $models[0]->model_brand;
				$category_name = $models[0]->category_name;
				$category_id = $models[0]->category_id;
				$mandatory_to_check = $models[0]->mandatory_to_check;
			} else {
	        	$msg = 'Не се открива артикул '.$style.' в таблицата с моделите !';
	        	return view('batch_bulk.error', compact('msg'));
	    	}

	    	/* If User NotCheck */
	    	if ($mandatory_to_check == "YES" AND $name_id == '10') {
	    		$msg = 'Артикул '.$style.' трябва ЗАДЪЛЖИТЕЛНО да се провери!';
		    	return view('batch_bulk.error', compact('msg'));
	    	}

	    	if ($brand == "TEZENIS") {
				$batch_brand = "batch_ts_bulk";
			} elseif ($brand == "INTIMISSIMI") {
				$batch_brand = "batch_is_bulk";
			} elseif ($brand == "CALZEDONIA") {
				$batch_brand = "batch_cs_bulk";
			} elseif ($brand == "FALCONERI") {
				$batch_brand = "batch_fs_bulk";
			}
	    	
	    	$batch_brand_table = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM ".$batch_brand." WHERE batch_min <= '".$cartonbox_produced."' AND batch_max >= '".$cartonbox_produced."'"));

			// dd($batch_brand_table);
		  	if ($batch_brand_table) {
		  		$batch_qty_p = $batch_brand_table[0]->batch_check;
		  		$batch_brand_id = $batch_brand_table[0]->batch_id;
		  		$batch_brand_min = $batch_brand_table[0]->batch_min;
		  		$batch_brand_max = $batch_brand_table[0]->batch_max;
		  		$batch_brand_max_reject_p = $batch_brand_table[0]->batch_reject;
			} else {
		      	$msg = 'Не се открива правилната линия в таблицата със заявки за тази марка !';
		      	return view('batch_bulk.error', compact('msg'));
		  	}

			$rejected = 0; // exist but we are not using
			//$batch_status = "Pending"; // new batch // no Pending anymore
			$batch_status = "Suspend"; // new batch have Suspend status
			$batch_qty = round($cartonbox_produced * $batch_qty_p / 100);
			$batch_brand_max_reject = round($cartonbox_produced * $batch_brand_max_reject_p / 100);
			// dd($batch_brand_max_reject);
			
			// Record Batch Cartonbox-------
				try {
					$table = new BatchCartonbox;

					$table->batch_name = $batch_name;
					$table->cartonbox = $cbcode;
			
					$table->save();
				}
				catch (\Illuminate\Database\QueryException $e) {
					$msg = "Проблем със записване на заявката в таблицата !";
					return view('batch_bulk.error',compact('msg'));
				}
			//----------------------------

			// Record Batch---------------
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

					//$table->module_name = $module_name;
					$table->producer_id = $producer_id;
					$table->producer = $producer;
					$table->producer_type = $producer_type;
										
					// $table->cartonbox = $cartonbox;
					// $table->cartonbox_qty = $cartonbox_qty;
					$table->cartonbox_produced = $cartonbox_produced;
					// $table->cartonbox_status = $cartonbox_status;
					// $table->cartonbox_start_date = $cartonbox_start_date;
					// $table->cartonbox_finish_date = $cartonbox_finish_date;

					// $table->bluebox = $bluebox;
					
					$table->batch_qty = $batch_qty;
					$table->batch_brand_id = $batch_brand_id;
					$table->batch_brand_min = $batch_brand_min;
					$table->batch_brand_max =  $batch_brand_max;
					$table->batch_brand_max_reject = $batch_brand_max_reject;

					$table->rejected = $rejected;

					$table->batch_status = $batch_status;
					$table->batch_type = $batch_type;

					$table->deleted = FALSE;
							
					$table->save();
				}
				catch (\Illuminate\Database\QueryException $e) {
					$msg = "Проблем със записване на заявката в таблицата !";
					return view('batch.error',compact('msg'));
				}
			//--------------------

			// Record Garment -------------
				/*
				for ($i=1; $i < $batch_qty + 1 ; $i++) {
					
					$times = $i;
					// dd($i);

					$garment_order = str_pad($i, 2, "0", STR_PAD_LEFT);
					$garment_name = $batch_date."-".$batch_user."-".$batch_order."-B"."-".$garment_order;
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
						$msg = "Problem to save garment in table!";
						return view('batch.error',compact('msg'));
					}
				}
				*/
			//-----------------------------

			// Set session for garment
			Session::set('batch_date', $batch_date );
			Session::set('batch_user', $batch_user );
			Session::set('batch_order', $batch_order );
			Session::set('batch_qty', $batch_qty );
			Session::set('sku', $sku );
			Session::set('po', $po );
			Session::set('brand', $brand );
			Session::set('category_id', $category_id );
			Session::set('category_name', $category_name );

			// Set session for first box
	    	Session::set('style', $style );
	    	Session::set('size', $size );
	    	Session::set('color', $color );
	    	Session::set('batch_name', $batch_name);

	    	return view('batch_bulk.searchinteos'); //must be bulk

	    } else {

	    	if (($ses_style == $style) AND ($ses_size == $size) AND ($ses_color == $color) AND ($batch_name = $ses_batch_name)) {
	    		//existing SKU and batch
	    		
			   	$batch_order_num = $today_batch_byuser;
			   	$batch_order = str_pad($batch_order_num, 3, "0", STR_PAD_LEFT); 

			   	$batch_name = $ses_batch_name;

			 	//	box already scanned-------------
					$count_batch_cartonbox = DB::table('batch_cartonboxes')
						                    ->where('cartonbox', '=', $cbcode)
											->where('batch_name', '=', $ses_batch_name)	
						                    ->count();

					if ($count_batch_cartonbox > 0) {
						// $msg = 'Box already scanned in this batch!';
				 	 	// return view('batch_bulk.error', compact('msg'));
						return view('batch_bulk.searchinteos');	 	 	
					}
				// ---------------------------------

	    		$models = DB::connection('sqlsrv')->select(DB::raw("SELECT category_name,category_id,model_brand,mandatory_to_check FROM models WHERE model_name = '".$style."'"));
			
		    	if ($models) {
		    		$brand = $models[0]->model_brand;
					$category_name = $models[0]->category_name;
					$category_id = $models[0]->category_id;
					$mandatory_to_check = $models[0]->mandatory_to_check;
				} else {
		        	$msg = 'Не се открива артикул '.$style.' в таблицата с моделите !';
		        	return view('batch_bulk.error', compact('msg'));
		    	}

		    	/* If User NotCheck */
		    	if ($mandatory_to_check == "YES" AND $name_id == '10') {
		    		$msg = 'Артикул '.$style.' трябва ЗАДЪЛЖИТЕЛНО да се провери!';
			    	return view('batch_bulk.error', compact('msg'));
		    	}

		    	if ($brand == "TEZENIS") {
					$batch_brand = "batch_ts_bulk";
				} elseif ($brand == "INTIMISSIMI") {
					$batch_brand = "batch_is_bulk";
				} elseif ($brand == "CALZEDONIA") {
					$batch_brand = "batch_cs_bulk";
				} elseif ($brand == "FALCONERI") {
					$batch_brand = "batch_fs_bulk";
				}
		    	
				//////////////////////////
				$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT id, cartonbox_produced FROM batch WHERE batch_name = '".$ses_batch_name."'"));
				
				$cartonbox_produced_existing = $batch[0]->cartonbox_produced;
				$cartonbox_produced_new = $cartonbox_produced_existing + $cartonbox_produced;
				
		    	$batch_brand_table = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM ".$batch_brand." WHERE batch_min <= '".$cartonbox_produced_new."' AND batch_max >= '".$cartonbox_produced_new."'"));
				// dd($batch_brand_table);

			  	if ($batch_brand_table) {
			  		$batch_qty_p = $batch_brand_table[0]->batch_check;
			  		$batch_brand_id = $batch_brand_table[0]->batch_id;
			  		$batch_brand_min = $batch_brand_table[0]->batch_min;
			  		$batch_brand_max = $batch_brand_table[0]->batch_max;
			  		$batch_brand_max_reject_p = $batch_brand_table[0]->batch_reject;
				} else {
			      	$msg = 'Не се открива правилната линия в таблицата със заявки за тази марка !';
			      	return view('batch_bulk.error', compact('msg'));
			  	}

				$rejected = 0; // exist but we are not using
				//$batch_status = "Pending"; // new batch // no Pending anymore
				$batch_status = "Suspend"; // new batch have Suspend status
				$batch_qty = round($cartonbox_produced_new * $batch_qty_p / 100);
				$batch_brand_max_reject = round($cartonbox_produced_new * $batch_brand_max_reject_p / 100);
				// dd($batch_brand_max_reject);


				// Batch Cartonbox add
				try {
					$table = new BatchCartonbox;

					$table->batch_name = $batch_name;
					$table->cartonbox = $cbcode;
					
					$table->save();
				}
				catch (\Illuminate\Database\QueryException $e) {
					$msg = "Проблем със записване на BatchCartonbox в таблицата !";
					return view('batch_bulk.error',compact('msg'));
				}			
					
				// Update Batch-------
					try {
						$batch = Batch::findOrFail($batch[0]->id);
						$batch->cartonbox_produced = $cartonbox_produced_new;

						$batch->batch_qty = $batch_qty;
						$batch->batch_brand_id = $batch_brand_id;
						$batch->batch_brand_min = $batch_brand_min;
						$batch->batch_brand_max =  $batch_brand_max;
						$batch->batch_brand_max_reject = $batch_brand_max_reject;

						$batch->save();
					}
					catch (\Illuminate\Database\QueryException $e) {
						$msg = "Проблем със записване на заявката в таблицата !";
						return view('batch_bulk.error',compact('msg'));
					}
				//--------------------

				// Record Garment -------------
					/*
					// But continue from already started number
					$garments_by_batch = DB::table('garment')
				                    ->where('batch_name', '=', $batch_name)
				                    ->count();
				    //$garments_by_batch = $garments_by_batch + 1;

				   	for ($i=$garments_by_batch + 1; $i < $batch_qty+$garments_by_batch+1 ; $i++) { 
						
						$times = $i;
						// dd($i);

						$garment_order = str_pad($i, 2, "0", STR_PAD_LEFT);
						$garment_name = $batch_date."-".$batch_user."-".$batch_order."-B"."-".$garment_order;
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
							$msg = "Problem to save garment in table!!";
							return view('batch.error',compact('msg'));
						}
					}
					*/
				//-----------------------------	

				// Set session for garment
				Session::set('batch_date', $batch_date );
				Session::set('batch_user', $batch_user );
				Session::set('batch_order', $batch_order );
				Session::set('batch_qty', $batch_qty );
				Session::set('sku', $sku );
				Session::set('po', $po );
				Session::set('brand', $brand );
				Session::set('category_id', $category_id );
				Session::set('category_name', $category_name );
				
				return view('batch_bulk.searchinteos'); //mora biti batch_bulk

	    	} else {
	    		// different SKU or batch
	    		// $msg = "Style, size or color is not the same for this bulk batch!";
				// return view('batch_bulk.error',compact('msg'));
				return view('batch_bulk.searchinteos');	 	
	    	}
	    }

		if ($msg1 != ''){
			return view('batch.sample', compact('msg1','batch_name'));
		}
			
		//return Redirect::to('/batch/checkbarcode/'.$batch_name);

		// Live database
		// try {

		// }
		// catch (\Illuminate\Database\QueryException $e) {
		// 	//return Redirect::to('/searchinteos');
		// 	$msg = "Problem to save batch in table. try agan.";
		// 	return view('batch.error',compact('msg'));
		// }
	}

	public function stop_store_bulk () {

		$batch_name = Session::get('batch_name');

		$batch_date = Session::get('batch_date');
		$batch_user = Session::get('batch_user');
		$batch_order = Session::get('batch_order');
		$batch_qty = Session::get('batch_qty');
		$sku = Session::get('sku');
		$po = Session::get('po');
		$brand = Session::get('brand');
		$category_id = Session::get('category_id');
		$category_name = Session::get('category_name');

		if (($batch_name == NULL) OR (!isset($batch_name))) {
			$msg = "Any Box must be scanned!";
			return view('batch_bulk.error',compact('msg'));
		}

		// Record Garment -------------
			for ($i=1; $i <= $batch_qty; $i++) { 
				
				$garment_order = str_pad($i, 2, "0", STR_PAD_LEFT);
				$garment_name = $batch_date."-".$batch_user."-".$batch_order."-B"."-".$garment_order;
				$garment_status = "Accepted";

				try {
					$table = new Garment;

					$table->garment_name = $garment_name;
					$table->garment_order = $garment_order;
					$table->batch_name = $batch_name;
					$table->cartonbox;
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
					$msg = "Проблем със записване на артикула в таблицата !";
					return view('batch_bulk.error',compact('msg'));
				}
			}
		//-----------------------------	

		Session::set('batch_date', NULL );
		Session::set('batch_user', NULL );
		Session::set('batch_order', NULL );
		Session::set('batch_qty', NULL );
		Session::set('sku', NULL );
		Session::set('po', NULL );
		Session::set('brand', NULL );
		Session::set('category_id', NULL );
		Session::set('category_name', NULL );

		Session::set('style', NULL );
    	Session::set('size', NULL );
    	Session::set('color', NULL);
    	Session::set('batch_name', NULL);
    	Session::set('producer', NULL);

    	// return view('batch_bulk.searchinteos');
    	return Redirect::to('/garment/by_batch/'.$batch_name);	
	}

/*
	public function batch_checkbarcode ($name)
	{
		try {
			return view('batch_bulk.checkbarcode',compact('name'));
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('batch_bulk.checkbarcode',compact('name'));
		}
	}

	public function batch_checkbarcode_store (Request $request)
	{
		//
		$this->validate($request, ['batch_name' => 'required', 'barcode' => 'required']);

		$input = $request->all(); 
		// dd($input);

		$batch_name = $input['batch_name'];
		$barcode_insert = $input['barcode'];

		try {

			$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT id,style,color,size,batch_user FROM batch WHERE batch_name = '".$batch_name."'"));
			$style = $batch[0]->style;
			$color = $batch[0]->color;
			$size = $batch[0]->size;
			$batch_user = $batch[0]->batch_user;

			$size_to_search = str_replace("/","-",$size);
					
			//$barcode = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM cartiglio WHERE Cod_Bar = '".$barcode."'"));
			$barcode = DB::connection('sqlsrv')->select(DB::raw("SELECT Cod_Bar FROM cartiglio WHERE Cod_Art_CZ = '".$style."' AND Cod_Col_CZ = '".$color."' AND Tgl_ITA = '".$size_to_search."'"));
			
			try {
				if(isset($barcode[0])) {
					if ($barcode[0]->Cod_Bar) {
					$barcode_indb = $barcode[0]->Cod_Bar;
					} else {
						$msg = "Item is not in Cartiglio table, PROIZVOD NE POSTOJI U Cartiglio BAZI!!! (Javi IT sektoru)";
						return view('batch.error',compact('msg'));
					}
				}
				else {
					$msg = "Item is not in Cartiglio table, PROIZVOD NE POSTOJI U Cartiglio BAZI!!! (Javi IT sektoru)";
					return view('batch.error',compact('msg'));
				}
			   	
			} catch (Exception $e) {
			    $msg = "Item is not in Cartiglio table, PROIZVOD NE POSTOJI U Cartiglio BAZI!!! (Javi IT sektoru)";
				return view('batch.error',compact('msg'));
			}

			if ($barcode_insert == $barcode_indb) {
				// dd("Barcode is Ok");
				$barcode_match = "YES";
			} else {
				// dd("Barcode is NOT Ok");
				$barcode_match = "NO";
			}

			$b = Batch::findOrFail($batch[0]->id);
			$b->batch_barcode_match = $barcode_match;
			$b->batch_barcode = $barcode_indb;
			$b->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			$msg = "Barcode not found in cartiglio database, PROIZVOD NE POSTOJI U Cartiglio BAZI!!! (Javi IT sektoru)";
			return view('batch.error',compact('msg'));
		}

		if ($barcode_insert != $barcode_indb) {
			$msg = "Barcode not match with barcode from cartiglio database, BARKODOVI SE NE SLAZU! ";
			return view('batch.error_continue',compact('msg','batch_name'));
		}

		// user NotCheck
		if ($batch_user == '10') {
			return Redirect::to('/notcheck/'.$batch_name);
		} else {
			return Redirect::to('/garment/by_batch/'.$batch_name);	
		}
	}

	public function stop_producer_store_bulk () {
		Session::set('style', NULL );
    	Session::set('size', NULL );
    	Session::set('color', NULL);
    	Session::set('batch_name', NULL);
    	Session::set('producer', NULL);

    	return view('batch.selectproducertype');
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

	public function confirm($id) 
	{
		// 
		// dd($batchid->id);
		try {
			$batchid = Batch::findOrFail($id);
			// dd($batchid->id);
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
				return view('batch_bulk.confirm',compact('batch','batchid','garments','defects','total_defects','total_rejected_defects','total_rejected_garments','suggestion'));
			} else {
				$suggestion = "Accept";
				return Redirect::to('/batch_bulk/accept/'.$batchid->id);
			}
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/');
		}
	}

	public function accept($id) 
	{
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Accept";
			$batch->save();
			return Redirect::to('/batch_bulk/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/accept/'.$id);
		}
	}

	public function acceptwithreservetion($id) 
	{
		try {
			$batch = Batch_bulk::findOrFail($id);
			$batch->batch_status = "Accept with reservation";
			$batch->save();
			return Redirect::to('/batch_bulk/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/acceptwithreservetion/'.$id);
		}
	}

	public function edit_status ($id)
	{
		$batch = Batch::findOrFail($id);
		return view('batch_bulk.edit_status', compact('batch'));
	}

	public function edit_status_update ($id, Request $request)
	{
		$this->validate($request, ['batch_status' => 'required']);
		$input = $request->all(); 

		$batch_status = $input['batch_status'];

		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = $batch_status;

			$batch->save();
			return Redirect::to('/batch_bulk');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk');
		}
	}

	public function reject($id) 
	{
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Reject";
			$batch->repaired = "NO";
			$batch->save();
			return Redirect::to('/batch_bulk/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/reject/'.$id);
		}
	}

	public function suspend($id) 
	{
		try {
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Suspend";
			$batch->save();
			return Redirect::to('/batch_bulk/');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/suspend/'.$id);
		}
	}

	public function not_checked($id) 
	{
		try {
			// Add status to batch
			$batch = Batch::findOrFail($id);
			$batch->batch_status = "Not checked";
			$batch->save();

			// Add status to garments inside batch
			
			$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE batch_name = '".$batch->batch_name."'"));
			foreach ($garments as $garment) {
				$gar = Garment::findOrFail($garment->id);
				$gar->garment_status = "Not checked";
				$gar->save();
			}

			$defects = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect WHERE batch_name = '".$batch->batch_name."'"));
			foreach ($defects as $defect) {
				$def = Defect::findOrFail($defect->id);
				$def->deleted = TRUE;
				$def->save();
			}
			
			// user NotCheck
			$name_id = Auth::user()->name_id;
			
			if ($name_id == '10') {
				return Redirect::to('/');
			} else {
				return Redirect::to('/batch_bulk/');
			}
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/not_checked/'.$id);
		}
	}

	public function delete($id) 
	{
		
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
			return Redirect::to('/batch_bulk');	
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/batch_bulk/delete/'.$id);
		}
	}

	public function cb_to_repair()
	{
		$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT [id]
															      ,[batch_name]
															      ,[sku]
															      ,[po]
															      ,[producer]
															      ,[cartonbox]
															      ,[batch_status]
															      ,[repaired]
															      ,[repaired_by_name]
															  FROM [finalaudit].[dbo].[batch]
															  WHERE batch_status = 'Reject' AND [repaired] = 'NO'
															  ORDER BY [created_at] asc
															  "));

		return view('batch_bulk.cb_to_repair', compact('batch'));
	}

	public function cb_to_repair_edit($id)
	{
		$batch = Batch::findOrFail($id);
		return view('batch_bulk.cb_to_repair_update', compact('batch')); 
	}

	public function cb_to_repair_repair($id)
	{
		try {
			$batch = Batch::findOrFail($id);
			$batch->repaired = "YES";
			$batch->repaired_by_name = Auth::user()->username;
			$batch->repaired_by_id = Auth::user()->name_id;
			$batch->repaired_date = date("Y-m-d H:i:s");

			$batch->save();
			return Redirect::to('/cb_to_repair_bulk');
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/cb_to_repair_bulk');
		}
	}
	*/	

}
