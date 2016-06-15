<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Ecommerce;
use DB;
use Auth;

class ControllerEcommerce extends Controller {

	public function __construct()
	{
		// Auth::loginUsingId(5);
		$this->middleware('auth');
	}

	public function index()
	{
		//
		try {
			$ecommerce = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM ecommerce ORDER BY id asc"));
			return view('ecommerce.index', compact('ecommerce'));	
		}
		catch (\Illuminate\Database\QueryException $e) {
			return Redirect::to('/ecommerce');
		}
	}

	public function edit($id) {

		$ecommerce = Ecommerce::findOrFail($id);		
		return view('ecommerce.edit', compact('ecommerce'));
	}

	public function update($id, Request $request) {
		//
		$this->validate($request, ['sku'=>'required']);

		$ecommerce = Ecommerce::findOrFail($id);		
		$input = $request->all(); 
		//dd($input);

		$sku = $input['sku'];
		$stlye = $input['style'];
		$color = $input['color'];
		$size = $input['size'];

		$scanned = $input['scanned'];
		$collected = $input['collected'];
		$shipped = $input['shipped'];


		try {
			//$defect_level->id = $input['id'];
			// $defect_level->defect_level_id = $input['sku'];
			// $defect_level->defect_level_name = $input['defect_level_name'];
			// $defect_level->defect_level_rejected = $input['defect_level_rejected'];
			//$defect_level->pcs_rejected = $input['pcs_rejected'];
					
			// $defect_level->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('defectlevel.error');			
		}
		
		return Redirect::to('/ecommerce');
	}



}
