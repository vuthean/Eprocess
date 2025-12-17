@extends('layouts.master')
@section('menu')
    @include('siderbar.dashboard')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>User Tracking</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="page-header-breadcrumb">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="index.html"> <i class="feather icon-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item">
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('payment_request.list_auth') }}">Payment Request</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $key => $log)
                                <tr
                                    data-toggle="modal"
                                    data-target-id="{{ $log->email }}"
                                    data-target="#large-Modal"
                                    data-old-value="{{$log->old_data}}"
                                    data-new-value="{{$log->new_data}}"
                                    data-module-name="{{$log->module}}"
                                >
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $log->fullname }}</td>
                                    <td>{{ $log->email }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->module }}</td>
                                    <td>{{ $log->proceeded_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="large-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Data Comparison</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12"  id="data-compare"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $("#large-Modal").on("show.bs.modal", function (e) {
                /** make sure the current state was clear */
                $("#data-compare").empty()

                var oldValue = JSON.parse($(e.relatedTarget).data('old-value'));
                var newValue = JSON.parse($(e.relatedTarget).data('new-value'));

                var oldValues = oldValue.replace("{","").replace("}","").split(",");
                var newValues = newValue.replace("{","").replace("}","").split(",");
                var countData = oldValues.length > 0 ? oldValues.length : newValues.length;

                var html = '<table class="table table-hover table-bordered">';
                html += '<thead><tr><td>Old Data</td><td>New Data</td></tr><thead/><tbody>'
                for(var i =0; i<countData;i++){
                    if(oldValues[i] !== newValues[i]){
                        html += '<tr style="background-color: lightgoldenrodyellow;"><td>' + oldValues[i] + '</td><td>' + newValues[i] + '</td></tr>';
                    }else{
                        html += '<tr><td>' + oldValues[i] + '</td><td>' + newValues[i] + '</td></tr>';
                    }
                }
                html += '<tbody/></table>';
                $("#data-compare").append(html);
            });
        });

    </script>
@endsection
