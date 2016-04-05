@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center">
           <div class="panel panel-default">
                <!-- <div class="panel-heading">Batch Details</div> -->
                <br>
                <div class="row">
                 @foreach ($batch as $req)
                    <table class="table">
                      <thead>
                        <tr>
                            <td><b>Batch Name</b></td>
                            <td>SKU</td>
                            <td>Module</td>
                            <td>CB Qty</td>
                            <td>CB Code</td>
                            <td>CB finished</td>
                            <td>Batch Qty</td>
                            <td>MAX Rejected</td>
                            <td>Category</td>
                        </tr>
                      </thead>
                      <tbody class="searchable">
                        <tr>
                            <td>{{ $req->batch_name }}</td>
                            <td>{{ $req->sku }}</td>
                            <td>{{ $req->module_name }}</td>
                            <td>{{ $req->cartonbox_produced }}</td>
                            <td>{{ $req->cartonbox }}</td>
                            <td>{{ $req->cartonbox_finish_date }}</td>
                            <td>{{ $req->batch_qty }}</td>
                            <td>{{ $req->batch_brand_max_reject }}</td>
                            <td>{{ $req->category_name }}</td>
                        </tr>
                      </tbody>
                    </table>
                @endforeach
                </div>
            </div>

            {{--
            <div class="panel panel-default">
                <div class="panel-heading">Garment Details</div>
                <br>
                <div class="row">
                  @foreach ($garment as $req)
                      <div class="col-md-6">Garment Name: <big><b>{{ $req->garment_name }}</b></big></div>
                      <div class="col-md-6">Garment Status: <big><b>{{ $req->garment_status }}</b></big></div>
                  @endforeach
                </div>
                <br>
            </div>
            --}}

            <div class="row">
              <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">Defect Details</div>
                    <br>
                    {!! Form::open(['method'=>'POST', 'url'=>'/defect_insert']) !!}
                    @include('errors.list')
                      
                      @foreach ($garment as $req)
                        {!! Form::hidden('garment_name', $req->garment_name, ['class' => 'form-control']) !!}
                        {!! Form::hidden('garment_order', $req->garment_order, ['class' => 'form-control']) !!}
                      @endforeach

                      @foreach ($batch as $req)
                        {!! Form::hidden('batch_name', $req->batch_name, ['class' => 'form-control']) !!}
                        {!! Form::hidden('batch_date', $req->batch_date, ['class' => 'form-control']) !!}
                        {!! Form::hidden('batch_user', $req->batch_user, ['class' => 'form-control']) !!}
                        {!! Form::hidden('batch_order', $req->batch_order, ['class' => 'form-control']) !!}
                      @endforeach

                      <ul class="nav nav-tabs nav-justified" role="tablist">
                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><big><b>Defects <span style="color:red">*</span></b></big></a></li>
                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><big><b>Positions</b></big></a></li>
                        <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><big><b>Machines</b></big></a></li>
                      </ul>

                      <!-- Tab panes -->
                      <div class="tab-content">

                        <div role="tabpanel" class="tab-pane fade in active" id="home">
                          <div class="row">
                            <br><br>
                                <div class="panel-body">
                                  @foreach ($defect_types as $type)
                                    <div class="col-md-2 visina">
                                       <div class="visina_text">{{ $type->defect_type_name }}</div>
                                      {!! Form::radio('defect_type_id', $type->defect_type_id, null, ['id' => 'check', 'class' => 'form-control']); !!}
                                      <br>
                                    </div>
                                  @endforeach
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div role="tabpanel" class="tab-pane fade" id="profile">
                          <div class="row">
                            <br><br>
                                <div class="panel-body">
                                  @foreach ($positions as $position)
                                    <div class="col-md-1 visina">
                                       <div class="visina_text">{{ $position->position_name }}</div>
                                      {!! Form::radio('position_id', $position->position_id, null, ['id' => 'check', 'class' => 'form-control']); !!}
                                      <br>
                                    </div>
                                  @endforeach
                                    
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade" id="messages">
                          <div class="row">
                            <br><br>
                                <div class="panel-body">
                                  @foreach ($machines as $machine)
                                    <div class="col-md-1 visina">
                                      <div class="visina_text">{{ $machine->machine_type }}</div>
                                      {!! Form::radio('machine_id', $machine->machine_id, null, ['id' => 'check', 'class' => 'form-control']); !!}
                                      <br>
                                    </div>
                                  @endforeach
                                    
                                </div>
                            </div>
                        </div>

                      </div>
                    <br>
                </div>
              </div>

              <div class="col-md-2">
                <div class="panel panel-default">
                   <div class="panel-heading">Options</div>
                  <br>
                  <br>
                  <div class="row">  
                    {!! Form::submit('Confirm Defect', ['class' => 'btn  btn-success center-block side-button']) !!}
                    {!! Form::close() !!}
                  </div>
                  
                  <div class="row">
                    @foreach ($garment as $req)
                      <a href="{{url('/defect/by_garment/'.$req->garment_name)}}" class="btn btn-warning side-button"><br>Cancel Defect</a>
                    @endforeach
                  </div>
                  
                </div>
              </div>
            </div>

        </div>
    </div>
</div>
@endsection