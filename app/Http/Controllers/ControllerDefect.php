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
		
		$batch_name = $garment[0]->batch_name;
		$batch = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM batch WHERE batch_name = '".$batch_name."'"));
		
		$defect_types = DB::connection('sqlsrv')->select(DB::raw("SELECT
		g.id,
		dt.defect_type_id,
		dt.defect_type_name,
		c.category_name
		FROM defect_types as dt

		INNER JOIN category_defect_types as cdt ON cdt.defect_type_id = dt.defect_type_id
		INNER JOIN categories as c ON c.category_id = cdt.category_id
		INNER JOIN garment as g ON g.category_id = c.category_id

		WHERE g.garment_name = '".$garment_name."'

		GROUP BY
		g.id,
		dt.defect_type_id,
		dt.defect_type_name,
		c.category_name 
		"));
		//dd($defect_types);

		$positions = DB::connection('sqlsrv')->select(DB::raw("SELECT
		g.id,
		p.position_id,
		p.position_name,
		c.category_name
		FROM positions as p

		INNER JOIN category_positions as cp ON cp.position_id = cp.position_id
		INNER JOIN categories as c ON c.category_id = cp.category_id
		INNER JOIN garment as g ON g.category_id = c.category_id

		WHERE g.garment_name = '".$garment_name."'

		GROUP BY
		g.id,
		p.position_id,
		p.position_name,
		c.category_name 
		"));
		//dd($positions);

		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT id,machine_id,machine_type FROM machines"));
		//dd($machines);

		return view('defect.new', compact('defects','garment','batch','defect_types','positions','machines'));
	}

	public function insert(Request $request)
	{
		//
		$this->validate($request, ['defect_type_id'=>'required']);

		$input = $request->all(); 
		// dd($input);

		$garment_name = $input['garment_name'];
		$garment_order = $input['garment_order'];
		$batch_name = $input['batch_name'];
		$batch_date = $input['batch_date'];
		$batch_user = $input['batch_user'];
		$batch_order = $input['batch_order'];


		if(isset($input['defect_type_id'])){
			$defect_type_id = $input['defect_type_id'];	
			$defect_types = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM defect_types WHERE defect_type_id = '".$defect_type_id."'"));
			$defect_type_name = $defect_types[0]->defect_type_name;
			$defect_level_id = $defect_types[0]->defect_level_id;
			$defect_level_name = $defect_types[0]->defect_level_name;
			$defect_level_rejected = $defect_types[0]->defect_level_rejected;
		} else {
			$msg = 'Cannot find Defect Type';
        	return view('defect.error', compact('msg'));
		}
		
		if(isset($input['position_id'])){
			$position_id = $input['position_id'];	
			$positions = DB::connection('sqlsrv')->select(DB::raw("SELECT position_name FROM positions WHERE position_id = '".$position_id."'"));
			$position_name = $positions[0]->position_name;
		} else {
			$position_id = '';
			$position_name = '';
		}
		
		if(isset($input['machine_id'])) {
			$machine_id = $input['machine_id'];
			$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT machine_type FROM machines	 WHERE machine_id = '".$machine_id."'"));
			$machine_type = $machines[0]->machine_type;
		} else {
			$machine_id = '';
			$machine_type = '';
		}

		$defect_order_bygarment = DB::table('defect')
		                    ->where('garment_name', '=', $garment_name)
		                    ->count();

	   	$defect_order_num = $defect_order_bygarment + 1;
	   	$defect_order = str_pad($defect_order_num, 3, "0", STR_PAD_LEFT);
	   	// dd($defect_order);

	   	$defect_name = $garment_name."-".$defect_order;

	   	try {
			$table = new Defect;

			$table->defect_name = $defect_name;
			$table->defect_order = $defect_order;

			$table->garment_name = $garment_name;
			$table->batch_name = $batch_name;

			$table->batch_date = $batch_date;
			$table->batch_user = $batch_user;
			$table->batch_order = $batch_order;
			
			$table->defect_type_id = $defect_type_id;
			$table->defect_type_name = $defect_type_name;
			$table->defect_level_id = $defect_level_id;
			$table->defect_level_name = $defect_level_name;
			$table->defect_level_rejected = $defect_level_rejected;

			$table->position_id = $position_id;
			$table->position_name = $position_name;
			$table->machine_id = $machine_id;
			$table->machine_type = $machine_type;
					
			$table->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			$msg = "Problem to save defect in table";
			return view('defect.error',compact('msg'));
		}

		return Redirect::to('/defect/by_garment/'.$garment_name);
	}

	public function delete($id) {

		$defect = Defect::findOrFail($id);
		$defect->delete();

		return Redirect::to('/defect/by_garment/'.$defect->garment_name);
	}

}
