@extends('app')

@section('content')
<div class="container-table">
	<div class="row vertical-center-row">
		<div class="text-center col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Edit:</div>
				<br>
					{!! Form::model($ecommerce , ['method' => 'POST', 'url' => '/ecommerce/'.$ecommerce->id /*, 'class' => 'form-inline'*/]) !!}

					<!-- <div class="panel-body">
						<span>Id:</span>
						{{--  {!! Form::input('number', 'id', null, ['class' => 'form-control']) !!} --}}
					</div> -->
					<div class="panel-body">
						<span>SKU: <span style="color:red;">*</span></span>
						{!! Form::input('string', 'sku', null, ['class' => 'form-control']) !!}
					</div>
					<div class="panel-body">
						<span>Style: <span style="color:red;">*</span></span>
						{!! Form::input('string', 'style', null, ['class' => 'form-control']) !!}
					</div>
					<div class="panel-body">
						<span>Color: <span style="color:red;">*</span></span>
						{!! Form::input('string', 'color', null, ['class' => 'form-control']) !!}
					</div>
					<div class="panel-body">
						<span>Size: <span style="color:red;">*</span></span>
						{!! Form::input('string', 'size', null, ['class' => 'form-control']) !!}
					</div>

					<div class="panel-body">
						<span>Scanned: <span style="color:red;">*</span></span>
						{!! Form::select('scanned', array(''=>'','NO'=>'NO','YES'=>'YES'), null, array('class' => 'form-control')); !!} 
					</div>

					<div class="panel-body">
						<span>Collected: <span style="color:red;">*</span></span>
						{!! Form::select('collected', array(''=>'','NO'=>'NO','YES'=>'YES'), null, array('class' => 'form-control')); !!} 
					</div>

					<div class="panel-body">
						<span>Scanned: <span style="color:red;">*</span></span>
						{!! Form::select('shipped', array(''=>'','NO'=>'NO','YES'=>'YES'), null, array('class' => 'form-control')); !!} 
					</div>
					
					<div class="panel-body">
						{!! Form::submit('Save', ['class' => 'btn btn-success center-block']) !!}
					</div>

					@include('errors.list')

					{!! Form::close() !!}
					<br>
					
					{!! Form::open(['method'=>'POST', 'url'=>'/ecommerce/scanned/'.$ecommerce->id]) !!}
					{!! Form::hidden('id', $ecommerce->id, ['class' => 'form-control']) !!}
					{!! Form::submit('Scanned', ['class' => 'btn  btn-danger /*btn-xs*/ center-block']) !!}
					{!! Form::close() !!}
					<br>
					{!! Form::open(['method'=>'POST', 'url'=>'/ecommerce/collected/'.$ecommerce->id]) !!}
					{!! Form::hidden('id', $ecommerce->id, ['class' => 'form-control']) !!}
					{!! Form::submit('Collected', ['class' => 'btn  btn-danger /*btn-xs*/ center-block']) !!}
					{!! Form::close() !!}
					<br>
					{!! Form::open(['method'=>'POST', 'url'=>'/ecommerce/shipped/'.$ecommerce->id]) !!}
					{!! Form::hidden('id', $ecommerce->id, ['class' => 'form-control']) !!}
					{!! Form::submit('Shipped', ['class' => 'btn  btn-danger /*btn-xs*/ center-block']) !!}
					{!! Form::close() !!}

				<hr>
				<div class="panel-body">
					<div class="">
						<a href="{{url('/ecommerce')}}" class="btn btn-default">Back</a>
					</div>
				</div>
					
			</div>
		</div>
	</div>
</div>

@endsection