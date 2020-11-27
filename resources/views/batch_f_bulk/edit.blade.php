@extends('app')

@section('content')
<div class="container-table">
	<div class="row vertical-center-row">
		<div class="text-center col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Edit BULK Batch:</div>
				<br>
					{!! Form::model($batch_f , ['method' => 'POST', 'url' => 'batch_f_bulk/'.$batch_f->id /*, 'class' => 'form-inline'*/]) !!}

					<div class="panel-body">
						
						{!! Form::hidden('batch_id', $batch_f->batch_id, ['class' => 'form-control']) !!}
						
						<div class="panel-body">
						<p>Batch Id:  <span style="color:red;">*</span></p>
							{!! Form::input('string', 'batch_id', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Pcs Box MIN: <span style="color:red;">*</span></p>
							{!! Form::input('number', 'batch_min', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Pcs Box MAX: <span style="color:red;">*</span></p>
							{!! Form::input('number', 'batch_max', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Batch Qty (Percentage: <span style="color:red;">*</span></p>
							{!! Form::input('number', 'batch_check', null, ['class' => 'form-control']) !!}
						</div>
						<div class="panel-body">
						<p>Max Rejected Qty (Percentage): <span style="color:red;">*</span></p>
							{!! Form::input('number', 'batch_reject', null, ['class' => 'form-control']) !!}
						</div>

					<div class="panel-body">
						{!! Form::submit('Save', ['class' => 'btn btn-success center-block']) !!}
					</div>

					@include('errors.list')

					{!! Form::close() !!}
					<br>
					
					{!! Form::open(['method'=>'POST', 'url'=>'/batch_f_bulk/delete/'.$batch_f->id]) !!}
					{!! Form::hidden('id', $batch_f->id, ['class' => 'form-control']) !!}
					{!! Form::submit('Delete', ['class' => 'btn  btn-danger btn-xs center-block']) !!}
					{!! Form::close() !!}
					
				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/batch_f_bulk')}}" class="btn btn-default">Back</a>
					</div>
				</div>
					
			</div>
		</div>
	</div>
</div>

@endsection