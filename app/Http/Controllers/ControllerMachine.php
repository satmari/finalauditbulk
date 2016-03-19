<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Machine;
use DB;

class ControllerMachine extends Controller {

	public function index()
	{
		//
		$machines = DB::connection('sqlsrv')->select(DB::raw("SELECT * FROM machines ORDER BY machine_id asc"));
		return view('machine.index', compact('machines'));
	}

	public function create()
	{
		//
		return view('machine.create');
	}

	public function insert(Request $request)
	{
		//
		$this->validate($request, ['machine_id'=>'required','machine_type'=>'required']);

		$machine_input = $request->all(); // change use (delete or comment user Requestl; )
		
		$machine_id = $machine_input['machine_id'];
		$machine_type = $machine_input['machine_type'];
		$machine_description = $machine_input['machine_description'];
		
		try {
			$machine = new Machine;

			$machine->machine_id = $machine_id;
			$machine->machine_type = $machine_type;
			$machine->machine_description = $machine_description;
						
			$machine->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('machine.error');			
		}
		
		//return view('defectlevel.index');
		return Redirect::to('/machine');

	}

	public function edit($id) {

		$machine = Machine::findOrFail($id);		
		return view('machine.edit', compact('machine'));
	}

	public function update($id, Request $request) {
		//
		$this->validate($request, ['machine_id'=>'required','machine_type'=>'required']);

		$machine = Machine::findOrFail($id);		
		//$machine->update($request->all());

		$input = $request->all(); 
		//dd($input);

		try {
			//$machine->id = $input['id'];
			$machine->machine_id = $input['machine_id'];
			$machine->machine_type = $input['machine_type'];
			$machine->machine_description = $input['machine_description'];
					
			$machine->save();
		}
		catch (\Illuminate\Database\QueryException $e) {
			return view('machine.error');			
		}
		
		return Redirect::to('/machine');
	}

	public function delete($id) {

		$machine = Machine::findOrFail($id);
		$machine->delete();

		return Redirect::to('/machine');
	}

}
