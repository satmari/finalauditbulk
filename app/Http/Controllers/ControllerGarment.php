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
			$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_name = '".$batch_name."'"));
			$garments = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE batch_name = '".$batch_name."' ORDER BY id asc"));

			return view('garment.index', compact('garments','batch'));
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/garment/by_batch/'.$batch_name);
		}
	}

}
