@extends('layouts.master')
@section('style')
    <style>
        .subject {
            display: inline-block;
            width: 300px;
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
        }

        .tooltip_body {
            word-wrap: break-word;
            white-space: normal;
            font-size: 12px;
        }

    </style>
@endsection
@section('menu')
    @include('siderbar.dashboard')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>{{ ucfirst($condition) }} request</h4>
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
                            Filter
                        </li>
                        <li class="breadcrumb-item"><a href="#!">{{ ucfirst($condition) }}</a> </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <!-- Zero config.table start -->
        <div class="card">
            <div class="card-header">
                <h5>View all your requests</h5>
            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="simpletable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Req.ID</th>
                                <th>Subject</th>
                                <th>Req.By</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Req.Type</th>
                                <th>Req.Status</th>
                                <th>Req.Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $key => $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        <a href="{{ url($value->description . '/' . Crypt::encrypt($value->req_recid . '___' . 'no')) }}"
                                            ​>
                                            {{ $value->req_recid }}
                                        </a>
                                    </td>
                                    <td>
                                        <p style="height: 5px;"><a class="mytooltip tooltip-effect-9" href="javascript:void(0)" style="font-weight: 400;">
                                                <span class="subject"> {{ $value->subject }}</span>
                                                <span class="tooltip-content5">
                                                    <span class="tooltip-text3">
                                                        <span class="tooltip-inner2" style="padding: 2px;">
                                                            <span class="tooltip_body">
                                                                {{ $value->subject }}</span>

                                                        </span>
                                                    </span>
                                                </span>
                                            </a></p>
                                    </td>

                                    <td>{{ $value->req_name }}</a>
                                    </td>
                                    <td>{{ $value->req_branch }}</td>
                                    <td>{{ $value->req_position }}</td>
                                    <td>{{ $value->formname }}</td>
                                    <td>{{ $value->record_status_description }}</td>
                                    <td>{{ $value->req_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>

@endsection
@section('script')

@endsection
