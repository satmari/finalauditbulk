@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center">
            <div class="panel panel-default">
                <!-- <div class="panel-heading">Batch Details</div> -->
                <!-- <br> -->
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

             <div class="row">
              <div class="col-md-10">
                  <div class="panel panel-default">
                      <div class="panel-heading">Garments in this Batch</div>
                      <!-- <div class="input-group"> <span class="input-group-addon">Filter</span>
                          <input id="filter" type="text" class="form-control" placeholder="Type here...">
                      </div> -->
                
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
                                  <!-- <td>Id</td> -->
                                  <td><b>Garment Name</b></td>
                                  <td>SKU</td>
                                  <td>Prod Order</td>
                                  <td>Brand</td>
                                  <td><b>Final Status</b></td>
                                  <td></td>
                                  <!-- <td></td> -->
                              </tr>
                          </thead>
                          <tbody class="searchable">
                          @foreach ($garments as $req)
                              <tr>
                                  {{-- <td>{{ $req->id }}</td> --}}
                                  <td>{{ $req->garment_name }}</td>
                                  <td>{{ $req->sku }}</td>
                                  <td>{{ $req->po }}</td>
                                  <td>{{ $req->brand }}</td>
                                  <td>{{ $req->garment_status }}</td>
                                  <td><a href="{{ url('/defect/by_garment/'.$req->garment_name) }}" class="btn btn-info btn-xs center-block">Garment Details</a></td>
                                  {{-- <td><a href="{{ url('/garment/edit/'.$req->id) }}" class="btn btn-info btn-xs center-block">Edit</a></td> --}}
                              </tr>
                          @endforeach
                          
                          </tbody>
                          </table>
                  </div>
              </div>
             
              <div class="col-md-2">
                  <div class="panel panel-default">
                    <div class="panel-heading">Options</div>
                      <br>
                      <!-- <br> -->
                      @foreach ($batch as $req)
                        <div class="row">
                          <a href="{{ url('/garment/by_batch/'.$req->batch_name) }}" class="btn btn-default side-button"><br>Suspend Batch <br><br></a>
                        </div>
                        
                        <div class="row">
                          <a href="{{ url('/garment/by_batch/'.$req->batch_name) }}" class="btn btn-success side-button"><br>Confirm Batch <br><br></a>
                        </div>

                        <div class="row">
                          <a href="{{ url('/batch/delete/'.$req->id) }}" class="btn btn-danger side-button"><br>Delete Batch <br><br></a>
                        </div>

                        <div class="row">
                          <a href="{{ url('/batch') }}" class="btn btn-warning side-button"><br>Back<br><br></a>
                        </div>
                        
                      @endforeach
                    </div>
                    <div class="row">
                      <br>
                  </div>
              </div>
            </div>

        </div>
    </div>
</div>
@endsection