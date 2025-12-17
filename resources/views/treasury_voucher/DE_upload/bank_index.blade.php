@extends('layouts.master')
@section('style')
    <style>
       .resizetext {
        /*background-color: #000;*/
        color: black;
        border-radius: 10px;
        /*border-style: none;*/

        transition: width ease 0.2s;
    }

    .resizetext:focus {
        width: 200px;
    }

    .myheader {
        margin: 0;
        padding: 8px 20px;
        color: #f1f1f1;
        z-index: 9999;
    }

    .sticky {
        position: fixed;
        top: 0;
        width: 100%;
    }

    .error {
        color: red;
        border-color: red;
    }

    .sticky+.content {
        padding-top: 102px;
    }

    .removeRowBtn {
        cursor: pointer;
    }

    .addRowBtn {
        cursor: pointer;
        border: none;
        background: none;

    }

    .addRowBtn:focus {
        outline: none !important;
    }

    .box-excel label {
        padding: 10px;
        border: 1px solid #01a9ac;
    }

    .download-template {
        padding: 10px;
        margin-bottom: 8px;
        margin-right: 10px;
        border: 1px solid #01a9ac;
    }
    .select2-close-mask{
        z-index: 2099;
    }
    .select2-dropdown{
        z-index: 3051;
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
                        <h4>Treasury Voucher </h4>
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
                        <li class="breadcrumb-item"><a href="{{ route('payment_request.list_auth') }}">Export DE</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <form action="{{route('DE-uploads-bank')}}" method="post">
            @csrf
            <div class="form-inline">
                <input type="date" id="start_date" name="dStart" class="form-control mb-2 mr-sm-2">
                <span style="padding-right: 8px;">TO</span>
                <input type="date" id="end_date" name="dEnd" class="form-control mb-2 mr-sm-2">
                <input type="text" id="req_num" name="req_num" class="form-control mb-2 mr-sm-2" placeholder="VOUCHER NO">
                <button type="submit" id="btn_search" class="btn btn-sm btn-primary mb-2"
                    style="height: 36px; font-size: 14px;">Search</button>
            </div>
        </form>
        <div class="card">
            <div class="card-header">
                <a href="#"​ style="float: right;" data-toggle="modal" data-target="#export_DE"
                    ><button type="submit" class="btn btn-success waves-effect" name="submit" value="update"><i
                        class="fa fa-download"></i> EXPORT MANY</button></a>
            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="simpletable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NO</th>
                                <th>DEPARTMENT </th>
                                <th>REQUESTER</th>
                                <th>REVIEWER</th>
                                <th>APPROVER</th>
                                <th>APPROVED DATE</th>
                                <th>EXPORTED DATE</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($final_result as $key => $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        <a href="{{ url('form/bank-vouchers/detail' . '/' . Crypt::encrypt($value->req_recid . '___' . 'no')) }}">
                                            {{ $value->req_recid }}
                                        </a>
                                    </td>
                                    <td>{{ $value->department }}</td>
                                    <td>{{ $value->req_name }}</td>
                                    <td>{{ $value->reviewer_name }}</td>
                                    <td>{{ $value->approver_name }}</td>
                                    <td>{{ $value->approval_date }}</td>
                                    <td>{{ $value->exported_at }}</td>
                                    <td>
                                        <a href="{{ url('DE-uploads/export-bank-voucher/'.$value->formname . '/' . Crypt::encrypt($value->req_recid . '___' . 'no')) }}">
                                            <button type="submit" class="btn btn-success waves-effect" name="submit" value="update"><i
                                                class="fa fa-download"></i> EXPORT</button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Zero config.table end -->

        <div class="modal fade" id="export_DE" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 600px;">
                    <div class="modal-header">
                        <h4 class="modal-title">EXPORT DE</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <span id="validate_create_new_item"></span>
                    <form method="post" action="{{route('DE-uploads/exports-bank-voucher')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="updated_id" id="updated_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Date</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="request_date" name="request_date"
                                            value="{{ date('d/m/y') }}" readonly="">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Vouchers</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7" >
                                            <select class="update-select2-container form-control" multiple="multiple" id="vouchers" name="vouchers[]">
                                                @foreach ($final_result as $value)
                                                    <option value="{{ $value->req_recid }}">{{ $value->req_recid }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success waves-effect" name="submit" value="update"><i
                                class="fa fa-download"></i> EXPORT</button>
                            <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                    class="fa fa-close"></i> CANCEL</button>
                        </div>
    
                    </form>
    
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.update-select2-container').select2({
                dropdownParent: $('#export_DE')
            });
        });
    </script>
   
@endsection