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

            <div class="panel panel-primary">
                <div class="row">
                  <br>
                  @foreach ($batch as $req)
                  <div class="col-md-4">
                    <a href="{{ url('/garment/by_batch/'.$req->batch_name) }}" class="btn btn-default">Suspend Batch</a>
                  </div>
                  <div class="col-md-4">
                    <a href="{{ url('/garment/by_batch/'.$req->batch_name) }}" class="btn btn-success">Confirm Batch</a>
                  </div>
                  <div class="col-md-4">
                    <a href="{{ url('/batch/delete/'.$req->id) }}" class="btn btn-danger">Delete Batch</a>
                  </div>
                  @endforeach
                </div>
                <div class="row">
                  <br>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Garments on this Batch</div>
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
                            <td><b>Garment Name</b></td>
                            <td>SKU</td>
                            <td>Po</td>
                            <td>Brand</td>
                            <td><b>Final Status</b></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody class="searchable">
                    @foreach ($garments as $req)
                        <tr>
                            <td>{{ $req->id }}</td>
                            <td>{{ $req->garment_name }}</td>
                            <td>{{ $req->sku }}</td>
                            <td>{{ $req->po }}</td>
                            <td>{{ $req->brand }}</td>
                            <td>{{ $req->garment_status }}</td>
                            <td><a href="{{ url('/defect/by_garment/'.$req->garment_name) }}" class="btn btn-info btn-xs center-block">Garment Details</a></td>
                            <td><a href="{{ url('/garment/edit/'.$req->id) }}" class="btn btn-info btn-xs center-block">Edit</a></td>
                        </tr>
                    @endforeach
                    
                    </tbody>                
            </div>
        </div>
    </div>
</div>
@endsection