@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row vertical-center-row">
        <div class="text-center">
            <div class="panel panel-default">
                <div class="panel-heading">Category-DefectType Links</div>
                
                @if (Auth::check() && Auth::user()->level() != 3)
                <div class="panel-body">
                    <div class="">
                        <a href="{{url('/categorydefecttype_new')}}" class="btn btn-default btn-info">Create new Category-DefectType link</a>
                    </div>
                </div>
                @endif

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
                            <!-- <td>Id</td> -->
                            <td>Defect Type Id</td>
                            <td>Defect Type Name</td>
                            <td>Category Id</td>
                            <td>Category Name</td>
                            <td>Link Type</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody class="searchable">
                    @foreach ($categorydefecttypes as $req)
                        <tr>
                            {{--<td>{{ $req->id }}</td>--}}
                            <td>{{ $req->defect_type_id }}</td>
                            <td>{{ $req->defect_type_name }}</td>
                            <td>{{ $req->category_id }}</td>
                            <td>{{ $req->category_name }}</td>
                            <td>{{ $req->link_type }}</td>
                            @if (Auth::check() && Auth::user()->level() != 3)
                                <td><a href="{{ url('/categorydefecttype/delete/'.$req->id) }}" class="btn btn-danger btn-xs center-block">Delete</a></td>
                            @endif
                        </tr>
                    @endforeach
                    
                    </tbody>                
            </div>
        </div>
    </div>
</div>
@endsection