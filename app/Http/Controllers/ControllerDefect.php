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

class ControllerDefect extends Controller {

	public function index()
	{
		//
		$defects = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect ORDER BY id asc"));
		return view('defect.index_all', compact('defects'));
	}

	public function by_garment($garment_name)
	{
		//
		$defects = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect WHERE garment_name = '".$garment_name."' ORDER BY id asc"));

		$garment = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE garment_name = '".$garment_name."'"));
		//dd($garment[0]->batch_name);
		$batch_name = $garment[0]->batch_name;
		//dd($batch_name);

		$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_name = '".$batch_name."'"));
		
		return view('defect.index', compact('defects','garment','batch'));
	}

	public function newdefect($garment_name) {

		//
		$defects = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect WHERE garment_name = '".$garment_name."' ORDER BY id asc"));

		$garment = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM garment WHERE garment_name = '".$garment_name."'"));
		//dd($garment[0]->batch_name);
		$batch_name = $garment[0]->batch_name;
		//dd($batch_name);

		$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_name = '".$batch_name."'"));
		
		return view('defect.new', compact('defects','garment','batch'));
	}

	public function delete($id) {

		$defect = Defect::findOrFail($id);
		$defect->delete();

		return Redirect::to('/defect/by_garment/'.$defect->garment_name);
	}

}
