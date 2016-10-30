@extends('app')

@section('content')
<div class="container container-table">
    <div class="row vertical-center-row">
        <div class="text-center col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Select producer type</b></div>

                <div class="panel-body">
                <br>
                    
                    <table class="table borderless">
                        <tr>
                            <td>
                                <a href="{{url('/selectproducer_bulk/INTERNAL')}}" class="btn btn-default">INTERNAL</a><br>
                            </td>
                            <td>
                                <a href="{{url('/selectproducer_bulk/EXTERNAL')}}" class="btn btn-default">EXTERNAL</a>
                            </td>
                        </tr>
                    </table>
                    
                <br>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection