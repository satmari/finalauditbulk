@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center">
            <div class="panel panel-default">
                <div class="panel-heading">Batch Details</div>
                <br>
                <div class="row">
                 @foreach ($batch as $req)
                      <div class="col-md-2">Batch Name: <big><b>{{ $req->batch_name }}</b></big></div>
                      <div class="col-md-2">SKU: <big><b>{{ $req->sku}}</b></big></div>
                      <div class="col-md-2">Module: <big><b>{{ $req->module_name }}</b></big></div>
                      <div class="col-md-2">CB Qty: <big><b>{{ $req->cartonbox_produced }}</b></big></div>
                      <div class="col-md-2">CB: <big><b>{{ $req->cartonbox }}</b></big></div>
                      <div class="col-md-2">CB finished: <big><b>{{ $req->cartonbox_finish_date }}</b></big></div>
                      <br>
                      <br>
                      <br>
                      <div class="col-md-4">Batch Qty: <big><b>{{ $req->batch_qty }}</b></big></div>
                      <div class="col-md-4">MAX Rejected: <big><b>{{ $req->batch_brand_max_reject}}</b></big></div>
                      <div class="col-md-4">Category: <big><b>{{ $req->category_name }}</b></big></div>
                @endforeach
                </div>
                <br>
            </div>

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

            <div class="panel panel-default">
                <div class="panel-heading">Defect Details</div>
                <br>
                

                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Defects</a></li>
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Positions</a></li>
                    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Machines</a></li>
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">

                    <div role="tabpanel" class="tab-pane fade in active" id="home">
                      <div class="row">
                        <div class="col-md-3">
                          <br><br>
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-primary active">
                              <input type="radio" name="options" id="option11" autocomplete="off" checked> Radio 1
                            </label>
                            <label class="btn btn-primary">
                              <input type="radio" name="options" id="option12" autocomplete="off"> Radio 2
                            </label>
                            <label class="btn btn-primary">
                              <input type="radio" name="options" id="option13" autocomplete="off"> Radio 3
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="profile">
                      <div class="row">
                        <div class="col-md-3">
                          <br><br>
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-success active">
                              <input type="radio" name="options1" id="option21" autocomplete="off" checked> Radio 1
                            </label>
                            <label class="btn btn-success">
                              <input type="radio" name="options1" id="option22" autocomplete="off"> Radio 2
                            </label>
                            <label class="btn btn-success">
                              <input type="radio" name="options1" id="option23" autocomplete="off"> Radio 3
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="messages">
                      <div class="row">
                        <div class="col-md-3">
                          <br><br>
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-warning active">
                              <input type="radio" name="options2" id="option31" autocomplete="off" checked> Radio 1
                            </label>
                            <label class="btn btn-warning">
                              <input type="radio" name="options2" id="option32" autocomplete="off"> Radio 2
                            </label>
                            <label class="btn btn-warning">
                              <input type="radio" name="options2" id="option33" autocomplete="off"> Radio 3
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>

                
                
                <br>
            </div>

            <div class="panel panel-primary">
                <div class="row">
                  <br>
                  
                  <div class="col-md-6">
                    <a href="{{ url('#') }}" class="btn btn-success">Confirm Defect</a>
                  </div>
                  
                  <div class="col-md-6">
                    @foreach ($garment as $req)
                      <a href="{{url('/defect/by_garment/'.$req->garment_name)}}" class="btn btn-danger">Cancel Defect</a>
                    @endforeach
                  </div>
                  
                </div>
                <div class="row">
                  <br>
                </div>
            </div>
           

        </div>
    </div>
</div>
@endsection