<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Batch;
use App\Garment;
use App\Category;
use DB;
use Auth;

class ControllerGarment extends Controller {

	public function __construct()
	{
		// Auth::loginUsingId(5);
		$this->middleware('auth');
	}

	public function index()
	{
		//
		try {
			$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment ORDER BY id asc"));
			return view('garment.index_all', compact('garments'));
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/garment');
		}
	}

	public function by_batch($batch_name)
	{
		//
		try {
			//$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_name = '".$batch_name."'"));

			// with mandatory to check
			$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT 
																*,
																(SELECT mandatory_to_check FROM models WHERE models.model_name = batch.style) as to_check
																FROM batch 
																WHERE batch_name = '".$batch_name."'"));
			
			//$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE batch_name = '".$batch_name."' ORDER BY id asc"));

			$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT *,
				(SELECT COUNT(defect.garment_name) FROM defect WHERE ((defect.garment_name = garment.garment_name) AND (defect.deleted = 0))) as CountDefects,
				(SELECT COUNT(defect.garment_name) FROM defect WHERE ((defect.garment_name = garment.garment_name) AND (defect.defect_level_rejected = 'YES') AND (defect.deleted = 0))) as CountCriticalDefects
				FROM garment
				WHERE garment.batch_name = '".$batch_name."'
				ORDER BY garment.id asc "));

			return view('garment.index', compact('garments','batch'));
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/garment/by_batch/'.$batch_name);
		}
	}

}
