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

            <div class="panel panel-primary">
                <div class="row">
                  <br>
                  @foreach ($garment as $req)
                  <div class="col-md-4">
                    <a href="{{url('/defect_new/'.$req->garment_name)}}" class="btn btn-default">New Defect</a>
                  </div>
                  @endforeach
                  @foreach ($batch as $req)
                  <div class="col-md-4">
                    <a href="{{ url('/garment/by_batch/'.$req->batch_name) }}" class="btn btn-success">Confirm Garment</a>
                  </div>
                  <div class="col-md-4">
                    <a href="{{ url('/garment/by_batch/'.$req->batch_name) }}" class="btn btn-info">Back to Batch</a>
                  </div>
                  @endforeach
                </div>
                <div class="row">
                  <br>
                </div>
            </div>

            @if (count($defects) > 0)
              <div class="panel panel-default">
                  <div class="panel-heading">Defect's on this garment</div>
                  <div class="input-group"> <span class="input-group-addon">Filter</span>
                      <input id="filter" type="text" class="form-control" placeholder="Type here...">
                  </div>
                  
                  <table class="table table-striped table-bordered" id="sort" 
                  >
                  <!--
                  data-show-export="true"
                  data-export-types="['excel']"
                  data-search="true"
                  data-show-refresh="true"
                  data-show-toggle="true"
                  data-query-params="queryParams" 
                  data-pagination="true"
                  data-height="300"
                  data-show-columns="true" 
                  data-export-options='{
                           "fileName": "preparation_app", 
                           "worksheetName": "test1",         
                           "jspdf": {                  
                             "autotable": {
                               "styles": { "rowHeight": 20, "fontSize": 10 },
                               "headerStyles": { "fillColor": 255, "textColor": 0 },
                               "alternateRowStyles": { "fillColor": [60, 69, 79], "textColor": 255 }
                             }
                           }
                         }'
                  -->
                      <thead>
                          <tr>
                              <td>Id</td>
                              <td><b>Defect Name</b></td>
                              <td><b>Defect Type</b></td>
                              <td>Defect Level</td>
                              <td>Defect Level Rejected</td>
                              <td>Position</td>
                              <td>Machine</td>
                              <td></td>
                              
                          </tr>
                      </thead>
                      <tbody class="searchable">
                      @foreach ($defects as $req)
                          <tr>
                              <td>{{ $req->id }}</td>
                              <td>{{ $req->defect_name }}</td>
                              <td>{{ $req->defect_type_name }}</td>
                              <td>{{ $req->defect_level_name }}</td>
                              <td>{{ $req->defect_level_rejected }}</td>
                              <td>{{ $req->position_name }}</td>
                              <td>{{ $req->machine_type }}</td>
                              <td><a href="{{ url('/defect/delete/'.$req->id) }}" class="btn btn-danger btn-xs center-block">Delete</a></td>
                          </tr>
                      @endforeach
                      </tbody>                
              </div>
            @else
              <p style="color:red;">There is no defects on this garment.</p>
            @endif

        </div>
    </div>
</div>
@endsection