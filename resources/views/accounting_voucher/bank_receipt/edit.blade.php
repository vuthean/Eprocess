@extends('layouts.master')
<style>
    .error {
        color: red;
        border-color: red;
    }

    .resizetext {
        /*background-color: #000;*/
        color: black;
        /*border-radius: 10px;*/
        /*border-style: none;*/

        transition: width ease 0.2s;
    }

    .resizetext:focus {
        width: 200px;
    }

    .timeline {
        margin-top: 20px;
        position: relative;

    }

    .timeline:before {
        position: absolute;
        content: '';
        width: 4px;
        height: calc(100% + 50px);
        background: rgb(138, 145, 150);
        background: -moz-linear-gradient(left, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(98, 105, 109, 1) 100%);
        background: -webkit-linear-gradient(left, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(98, 105, 109, 1) 100%);
        background: linear-gradient(to right, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(98, 105, 109, 1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#8a9196', endColorstr='#62696d', GradientType=1);
        left: 14px;
        top: 5px;
        border-radius: 4px;
    }

    .timeline-month {
        position: relative;
        padding: 4px 15px 4px 35px;
        background-color: #444950;
        display: inline-block;
        width: auto;
        border-radius: 40px;
        border: 1px solid #17191B;
        border-right-color: black;
        margin-bottom: 30px;
    }

    .timeline-month span {
        position: absolute;
        top: -1px;
        left: calc(100% - 10px);
        z-index: -1;
        white-space: nowrap;
        display: inline-block;
        background-color: #111;
        padding: 4px 10px 4px 20px;
        border-top-right-radius: 40px;
        border-bottom-right-radius: 40px;
        border: 1px solid black;
        box-sizing: border-box;
    }

    .timeline-month:before {
        position: absolute;
        content: '';
        width: 20px;
        height: 20px;
        background: rgb(138, 145, 150);
        background: -moz-linear-gradient(top, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(112, 120, 125, 1) 100%);
        background: -webkit-linear-gradient(top, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(112, 120, 125, 1) 100%);
        background: linear-gradient(to bottom, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(112, 120, 125, 1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#8a9196', endColorstr='#70787d', GradientType=0);
        border-radius: 100%;
        border: 1px solid #17191B;
        left: 5px;
    }

    .timeline-section {
        padding-left: 35px;
        display: block;
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-date {
        margin-bottom: 15px;
        padding: 2px 15px;
        background: linear-gradient(#74cae3, #5bc0de 60%, #4ab9db);
        position: relative;
        display: inline-block;
        border-radius: 20px;
        border: 1px solid #17191B;
        color: #fff;
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);
    }

    .timeline-section:before {
        content: '';
        position: absolute;
        width: 30px;
        height: 1px;
        background-color: #444950;
        top: 12px;
        left: 20px;
    }

    .timeline-section:after {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        background: linear-gradient(to bottom, rgba(138, 145, 150, 1) 0%, rgba(122, 130, 136, 1) 60%, rgba(112, 120, 125, 1) 100%);
        top: 7px;
        left: 11px;
        border: 1px solid #17191B;
        border-radius: 100%;
    }

    .timeline-section .col-sm-4 {
        margin-bottom: 15px;
    }

    .timeline-box {
        position: relative;

        background-color: #444950;
        border-radius: 15px;
        border-top-left-radius: 0px;
        border-bottom-right-radius: 0px;
        border: 1px solid #17191B;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .box-icon {
        position: absolute;
        right: 5px;
        top: 0px;
    }

    .box-title {
        padding: 5px 15px;
        border-bottom: 1px solid #17191B;
    }

    .box-title i {
        margin-right: 5px;
    }

    .box-content {
        padding: 5px 15px;
        background-color: #17191B;
    }

    .box-content strong {
        color: #666;
        font-style: italic;
        margin-right: 5px;
    }

    .box-item {
        margin-bottom: 5px;
    }

    .box-footer {
        padding: 5px 15px;
        border-top: 1px solid #17191B;
        background-color: #444950;
        text-align: right;
        font-style: italic;
    }

    .btnRemoveDocument {
        cursor: pointer;
    }

    .addRowBtn {
        cursor: pointer;
    }
    .myheader {
        margin: 0;
        padding: 8px 20px;
        color: #f1f1f1;
        z-index: 9999;
        margin-left: -18px;
    }
    .sticky {
        position: fixed;
        top: 0;
        width: 100%;
    }
    .sticky+.content {
        padding-top: 102px;
    }
</style>
@section('menu')
    @include('siderbar.dashboard')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>Bank Receipt Voucher Requests</h4>
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
                            BRV
                        </li>
                        <li class="breadcrumb-item"><a href="">{{$bankReceipt->req_recid}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="col-sm-12">
        <div class="myheader" id="myHeader">
            <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/bank-receipt-vouchers/export-to-pdf/' . Crypt::encrypt($bankReceipt['req_recid'].'___no')) }}"><i
                    class="fa fa-save" aria-hidden="true"></i>
                    EXPORT PDF</a>
            <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/bank-receipt-vouchers/export-to-excel/' . Crypt::encrypt($bankReceipt['req_recid'].'___no')) }}"><i
                    class="fa fa-save" aria-hidden="true"></i>
                    EXPORT EXCEL</a>
        </div>
        <form method="post" action="{{ route('form/bank-receipt-vouchers/submit') }}" name="frmSubmit" id="frmSubmit"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="grand_total" name="currency" id="currency" value="USD">
            <input type="hidden" class="grand_total" name="req_recid" id="req_recid" value="{{$bankReceipt->req_recid}}">
            <!-- Page-body start -->
            <div class="page-body">

                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Bank Receipt Voucher</h4>
                        <span id="validate_currency"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Reference No.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        <select class="js-example-basic-multiple col-sm-12" multiple="multiple" name="references[]" id="references" >
                                            @foreach($choosedReferences as $key => $reference)
                                                <option value="{{ $reference }}" selected>{{ $reference }}</option>
                                            @endforeach
                                            @foreach ($references as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Department</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $bankReceipt->department}}" readonly="" name="department">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Request Date</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="request_date" name="request_date"
                                        value="{{date('d/m/Y', strtotime($bankReceipt->created_at))}}" readonly="">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Voucher No.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="voucher_number" name="voucher_number"  value="{{ $bankReceipt->voucher_number }}">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Exchange rate</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input readonly type="text" class="form-control" value="{{$bankReceipt->exchange_rate}}"  name="exchange_rate">

                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary" id="btn_update_exchange_rate">Update rate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <div class="sub-title">
                            <span >Form details</span>
                        </div>

                        <div class="card-block">
                            <button type="button" class="btn btn-primary new_item" data-toggle="modal"
                                data-target="#modal_add_new_item"
                                data-req_recid="{{ $bankReceipt->req_recid }}"
                                data-currency="{{ $bankReceipt->currency }}"
                                style="float: right"
                                id="btn_add_new">
                                <i class="fa fa-plus"></i>ADD NEW ITEM
                            </button>

                            <div class="table-responsive dt-responsive">
                                <table class="table table-striped table-bordered" id="example-1" style="font-size: 12.5px;">
                                    <thead>
                                        <tr class="table-info">
                                            <th rowspan="2" style="vertical-align:middle;">GL CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">ACCOUNT NAME</th>
                                            <th rowspan="2" style="vertical-align:middle;">BRANCH CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">DR/CR</th>
                                            <th rowspan="2" style="vertical-align:middle;">CURRENCY</th>
                                            <th rowspan="2" style="vertical-align:middle;">AMOUNT</th>
                                            <th rowspan="2" style="vertical-align:middle;">BUDGET CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">AL BUDGET CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">TAX CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">SUPP CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">DEPT CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">PRO CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">SEG CODE</th>
                                            <th rowspan="2" style="vertical-align:middle;">NARRATIVES</th>
                                            <th rowspan="2" style="vertical-align:middle;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bankReceiptDetails as $key => $value)
                                        <tr>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->gl_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->account_name }}</td>
                                            <td class="tabledit-view-mode">{{ $value->branch_code }}</td>
                                            <td > @if ($value->dr_cr == 'DEBIT') DR @else CR @endif  </td>
                                            <td > {{ $value->currency }}</td>
                                            <td > @money($value->amount)</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->budget_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->al_budget_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->tax_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->supp_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->department_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->product_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{{ $value->segment_code }}</td>
                                            <td class="tabledit-view-mode"style="padding: 5px;">{!! nl2br(e($value->naratives)) !!}</td>
                                            <td >
                                                <a  href="#" class="edit_item" data-toggle="modal"
                                                data-target="#modal_update_item"
                                                data-item_id="{{ $value->id }}"
                                                data-req_recid="{{ $bankReceipt->req_recid }}"
                                                data-gl_code="{{ $value->gl_code }}"
                                                data-account_name="{{ $value->account_name }}"
                                                data-branch_code="{{ $value->branch_code }}"
                                                data-dr_cr="{{ $value->dr_cr }}"
                                                data-item_currency="{{ $value->currency }}"
                                                data-amount="{{ $value->amount }}"
                                                data-budget_code="{{ $value->budget_code }}"
                                                data-al_budget_code="{{ $value->al_budget_code }}"
                                                data-tax_code="{{ $value->tax_code }}"
                                                data-supp_code="{{ $value->supp_code }}"
                                                data-department_code="{{ $value->department_code }}"
                                                data-product_code="{{ $value->product_code }}"
                                                data-segment_code="{{ $value->segment_code }}"
                                                data-naratives="{{ $value->naratives }}"
                                                >
                                                    <i class="fa fa-edit" style="font-size: 20px;color: #0ac282;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" style="text-align: right;">
                                                TOTAL DEBIT
                                            </td>
                                            <td class="tabledit-view-mode" style="padding: 5px;">
                                                @if ($defaultCurrency == 'USD')
                                                    $@money($totalDRCR->total_DR)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($totalDRCR->total_DR)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="text-align: right;">
                                                TOTAL CREDIT
                                            </td>
                                            <td class="tabledit-view-mode" style="padding: 5px;">
                                                @if ($defaultCurrency == 'USD')
                                                    $@money($totalDRCR->total_CR)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($totalDRCR->total_CR)
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @if($budgetcode_na === 'N')
                                <div >
                                    <i ><b>+ Budget details <br><br></b></i>
                                </div>
                                <div class="table-responsive dt-responsive">
                                    <table class="table" style="font-size: 12.5px;">
                                        <tr class="table-active">
                                            <th><b> Buget Code</b></th>
                                            <th ><b> Buget Item</b></th>
                                            <th>Total</th>
                                            <th>YTD</th>
                                            <th>Remaining</th>
                                        </tr>
                                        @foreach($totalAndYTD as $data)
                                            @if($data->budget_code !== 'NA')
                                                <tr>
                                                    <td>{{$data->budget_code}}</td>
                                                    <td><textarea rows="1" class="fix-col">{{$data->budget_item}}</textarea></td>
                                                    <td>@money($data->total)</td>
                                                    <td>@money($data->total_YTD)</td>
                                                    <td> @money($data->remaining)</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                            @if($totalAndYTDAL->count() > 0 and $al_budgetcode_na === 'N')
                                <div >
                                    <i ><b>+ AL Budget details <br><br></b></i>
                                </div>
                                <div class="table-responsive dt-responsive">
                                    <table class="table" style="font-size: 12.5px;">
                                        <tr class="table-active">
                                            <th style=" width: 30%;"><b>AL Buget Code</b></th>
                                            <th ><b> Buget Item</b></th>
                                            <th>Total</th>
                                            <th>YTD</th>
                                            <th>Remaining</th>
                                        </tr>
                                        @foreach($totalAndYTDAL as $data)
                                            @if($data->budget_code !== 'NA')
                                                <tr>
                                                    <td>{{$data->budget_code}}</td>
                                                    <td><textarea rows="1" class="fix-col">{{$data->budget_item}}</textarea></td>
                                                    <td>@money($data->total)</td>
                                                    <td>@money($data->total_YTD)</td>
                                                    <td> @money($data->remaining)</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Account Information</h4>
                        <span id="validate_account_pannel"></span>
                        <div class="row">
                        <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Payment Method</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        <select class="tabledit-input form-control input-sm" id="payment_method" name="payment_method" style="margin-bottom: 0px;">
                                            <option value="" disabled="" >Select</option>
                                            @foreach ($paymentMethods as $value)
                                                @if ($value->name == $bankReceipt->payment_method_code)
                                                    <option value="{{ $value->name }}" selected>{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->name }}">{{ $value->name }}</option>
                                                @endif

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Beneficiary Bank</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{ $bankReceipt->bank_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Swift Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="swift_code" id="swift_code" value="{{ $bankReceipt->swift_code }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Address</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_currency" id="account_currency" value="{{ $bankReceipt->account_currency }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Cheque/Account Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_name" id="account_name" value="{{ $bankReceipt->account_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Account Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_number" id="account_number" value="{{ $bankReceipt->account_number }}">
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Address</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="benificiary_name" id="benificiary_name" value="{{ $bankReceipt->beneficiary_number }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Purpose</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="invoice_number" id="invoice_number" value="{{ $bankReceipt->invoice_number }}">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row form-group" style="padding-top: 15px;">
                            <div class="col-sm-12 mobile-inputs">
                                <label for="">Note :</label>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <textarea name="note" class="form-control ">{{ $bankReceipt->note }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <ul class="nav nav-tabs  tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home1" role="tab" aria-expanded="true">ACTION</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#profile1" role="tab"  aria-expanded="false">REFERENCE DOCUMENT</a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content tabs card-block">
                        <span id="approve_review"></span>
                        <span id="validate_approver"></span>
                        <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                @if(count($approvalUsers) > 0)
                                    @foreach ($approvalUsers as $key => $value)
                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <div>
                                                    <span style="color: red;">*</span>
                                                    <span>{{$value->label}}</span>
                                                    <span style="float: right;">:</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 ">
                                                @if ($value->checker == 'accounting_voucher')
                                                    <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $value->checker }}">
                                                @else
                                                    <select class="js-example-basic-single" name="{{$value->checker}}" id="{{$value->checker}}">
                                                        <option selected="" disabled="" value="">Select One</option>
                                                        @foreach($value->users as $user)
                                                            @if ($user->email == $value->default_user_email && $value->checker =='approver')
                                                                <option value="{{ $user->email . '/' . $user->role_id }}" selected>
                                                                    {{ $user->firstname }} {{ $user->lastname }}</option>
                                                            @else
                                                                <option value="{{ $user->email . '/' . $user->role_id }}">
                                                                    {{ $user->firstname }} {{ $user->lastname }}</option>
                                                            @endif

                                                        @endforeach
                                                    </select>
                                                @endif

                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <span>Comment</span>
                                        </div>
                                        <div class="col-sm-12">
                                        <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="float: right;">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success waves-effect" name="activity_voucher" value="submit_voucher" id="btnConfirmClearAdvanceForm" >
                                                <i class="fa fa-save"></i>
                                                SUMIT
                                            </button>
                                            <button type="button" class="btn btn-danger waves-effect" name="activity_voucher" value="delete_voucher" id="btn_delete_current_request"
                                                data-toggle="modal"
                                                data-target="#delete_request"
                                            >
                                                <i class="fa fa-trash"></i>
                                                DELETE
                                            </button>

                                        </div>
                                    </div>
                                @else
                                    <div class="form-group row">
                                        <div class="col-sm-12" style="color: red; font-weight: bold;">
                                            <span>
                                                You cannot sumit this request becuase your total DEBIT is not equalt of Total CREDIT.
                                            </span>
                                            <br>
                                            <span>
                                                TOTAL DEBIT : {{ $defaultCurrency }} @money($totalDRCR->total_DR)
                                            </span>
                                            <br>
                                            <span>
                                                TOTAL CREDIT : {{ $defaultCurrency }} @money($totalDRCR->total_CR)
                                            </span>
                                        </div>
                                    </div>
                                @endif


                            </div>
                        </div>
                        </div>
                        <div class="tab-pane" id="profile1" role="tabpanel" aria-expanded="false">
                            <table class="table" style="font-size: 12.5px;" id="table_attach">
                                <thead>
                                    <tr class="table-info">
                                        <th>File</th>
                                        <th>File name</th>
                                        <th>Upload by</th>
                                        <th>Date</th>
                                        <th style="text-align: center">Action</th>
                                    </tr>
                                </thead>

                                @foreach ($documents as $value)
                                        <tr id="{{ $value['id'] }}">
                                            <td>
                                                <?php
                                                $ext = explode('.', $value['filename']);
                                                $file_ext = $ext[1];
                                                ?>
                                                @if ($file_ext == 'docx' or $file_ext == 'doc')
                                                    <i class="fa fa-file-word-o" style="font-size: 30px;color: blue"></i>
                                                @elseif($file_ext=='jpg' or $file_ext=='jpeg' or $file_ext=='PNG')
                                                    <i class="fa fa-file-image-o" style="font-size: 30px;color: pink"></i>
                                                @elseif($file_ext=='xls' or $file_ext=='xlsx' or $file_ext=='csv')
                                                    <i class="fa fa-file-excel-o" style="font-size: 30px;color: green"></i>
                                                @elseif($file_ext=='pdf')
                                                    <i class="fa fa-file-pdf-o" style="font-size: 30px;color: red"></i>
                                                @elseif($file_ext=='zip' or $file_ext=='rar')
                                                    <i class="fa fa-file-zip-o" style="font-size: 30px;color: yellow"></i>
                                                @elseif($file_ext=='ppt' or $file_ext=='pptx')
                                                    <i class="fa fa-file-powerpoint-o"
                                                        style="font-size: 30px;color: orange"></i>
                                                @else
                                                    <i class="fa fa-file" style="font-size: 30px;color: blue"></i>
                                                @endif
                                            </td>
                                            <td><a href="{{ url('download/' . $value->uuid) }}">
                                                    {{ $value['filename'] }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $value['doer_name'] }}
                                            </td>
                                            <td>
                                                {{ $value['activity_datetime'] }}
                                            </td>
                                            <td style="text-align: center">
                                            <a href="#" class="delete_attach" data-attachid="{{$value->id}}"><i class="fa fa-trash" aria-hidden="true" style="font-size: 20px; color:red;"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr id="add_more" >
                                        <td colspan="5" style="font-weight: bold;">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>File Upload</h5>
                                                    <span id="document_spn"></span>
                                                </div>
                                                <div class="card-block">
                                                    <input type="file" name="fileupload[]" id="filer_input"
                                                        multiple="multiple">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <input type="hidden" value="" name="att_remove" id="att_remove">
                                <span id="att_remove1" style="display: none;"></span>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <div class="modal fade" id="modal_add_new_item" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 600px;">
                    <div class="modal-header">
                        <h4 class="modal-title">CREATE NEW</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('form/bank-receipt-vouchers/items/add-new') }}" enctype="multipart/form-data"
                        id="frmCreateNew">
                        <span id="validate_create_new_item"></span>
                        <input type="hidden" name="item_req_recid" id="item_req_recid">
                        <input type="hidden" name="item_currency" id="item_currency">
                        @csrf
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>GL CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_gl_code" id="item_gl_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($generalLedgerCodes as $value)
                                                    <option value="{{ $value->account_number }}" data-account_name="{{ $value->account_name }}" >{{ $value->account_number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Account Name</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <textarea name="item_account_name" id="item_account_name" class="form-control " readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Branch CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_branch_code" id="item_branch_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($brancheCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>DR/CR</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select  class="tabledit-input form-control input-sm" id="dr_cr" name="dr_cr" style="margin-bottom: 0px;">
                                                <option value="DEBIT" selected>DR</option>
                                                <option value="CREDIT">CR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Currency</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select class="tabledit-input form-control input-sm" id="currency" name="currency" style="margin-bottom: 0px;">
                                                <option value="USD" selected>USD</option>
                                                <option value="KHR">KHR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Amount</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="number" style="min-width: 80px; margin-bottom: 0px;" class="abledit-input form-control input-sm  numbers"
                                            id="amount" name="amount" onpaste="return false;" value="0" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Budget CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_budget_code" id="item_budget_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($budgetCodes as $value)
                                                    <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>AL Budget CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_al_budget_code" id="item_al_budget_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($budgetCodes as $value)
                                                    <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>TAX CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_tax_code" id="item_tax_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($taxCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>SUPP CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_supplier_code" id="item_supplier_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($supplierCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Department CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_department_code" id="item_department_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($departmentCodes as $value)
                                                    <option value="{{ $value->branch_code }}">{{ $value->branch_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Product CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_product_code" id="item_product_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($productCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Segment CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="item_segment_code" id="item_segment_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($segmentCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Narative</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <textarea name="item_narative" id="item_narative" class="form-control " ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success waves-effect" name="submit" ><i
                                class="fa fa-save"></i> Create</button>
                            <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                class="fa fa-close"></i> Cancel</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_update_item" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 600px;">
                    <div class="modal-header">
                        <h4 class="modal-title">UPDATE ITEM</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('form/bank-receipt-vouchers/items/update') }}" enctype="multipart/form-data"
                        id="frmUpdateItem">
                        <span id="validate_create_new_item"></span>
                        <input type="hidden" name="update_item_id" id="update_item_id">
                        <input type="hidden" name="update_req_recid" id="update_req_recid">
                        @csrf
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>GL CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_gl_code" id="update_gl_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($generalLedgerCodes as $value)
                                                    <option value="{{ $value->account_number }}" data-account_name="{{ $value->account_name }}" >{{ $value->account_number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Account Name</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <textarea name="update_account_name" id="update_account_name" class="form-control " readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Branch CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_branch_code" id="update_branch_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($brancheCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>DR/CR</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select  class="tabledit-input form-control input-sm" id="update_dr_cr" name="update_dr_cr" style="margin-bottom: 0px;">
                                                <option value="DEBIT" selected>DR</option>
                                                <option value="CREDIT">CR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Currency</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select class="tabledit-input form-control input-sm" id="update_currency" name="update_currency" style="margin-bottom: 0px;">
                                                <option value="USD" selected>USD</option>
                                                <option value="KHR">KHR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Amount</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="number" style="min-width: 80px; margin-bottom: 0px;" class="abledit-input form-control input-sm  numbers"
                                            id="update_amount" name="update_amount" onpaste="return false;" value="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Budget CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_budget_code" id="update_budget_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($budgetCodes as $value)
                                                    <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>AL Budget CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_al_budget_code" id="update_al_budget_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($budgetCodes as $value)
                                                    <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>TAX CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_tax_code" id="update_tax_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($taxCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>SUPP CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_supplier_code" id="update_supplier_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($supplierCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Department CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_department_code" id="update_department_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($departmentCodes as $value)
                                                    <option value="{{ $value->branch_code }}">{{ $value->branch_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Product CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_product_code" id="update_product_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($productCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Segment CODE</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="update_segment_code" id="update_segment_code" class="form-control">
                                                <option value="" selected="">Select</option>
                                                @foreach ($segmentCodes as $value)
                                                    <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span>Narative</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <textarea name="update_narative" id="update_narative" class="form-control " ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success waves-effect" name="activity" value="update_item" ><i
                                class="fa fa-save"></i> Modify</button>
                            <button type="submit" class="btn btn-danger waves-effect" name="activity" value="delete_item"><i
                                    class="fa fa-save"></i> Remove</button>
                            <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                    class="fa fa-close"></i> Cancel</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 600px;">
                    <div class="modal-header">
                        <h4 class="modal-title">DELETE REQUEST</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('form/bank-receipt-vouchers/delete') }}" enctype="multipart/form-data">
                        <span id="validate_create_new_item"></span>
                        <input type="hidden" class="grand_total" name="req_recid" id="req_recid" value="{{$bankReceipt->req_recid}}">
                        @csrf
                        <div class="modal-body">
                            <span><h4>Are you sure you want to delete this request ?</h4></span>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger waves-effect" ><i
                                class="fa fa-trash"></i> YEST</button>
                            <button type="button" class="btn btn-default waves-effect"  data-dismiss="modal"><i
                                    class="fa fa-close"></i> NO</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="updateExchangeRateModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 600px;">
                    <div class="modal-header">
                        <h4 class="modal-title">UPDATE Exchange rate</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('form/bank-receipt-vouchers/update-exchange-rate') }}" enctype="multipart/form-data">
                        <span id="validate_create_new_item"></span>
                        <input type="hidden" class="grand_total" name="req_recid" id="req_recid" value="{{$bankReceipt->req_recid}}">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Exchange Rate</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="number" style="min-width: 80px; margin-bottom: 0px;"
                                            class="abledit-input form-control input-sm  numbers"
                                            id="exchange_rate" name="exchange_rate" onpaste="return false;" value="{{$bankReceipt->exchange_rate}}" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success waves-effect" >SAVE</button>
                            <button type="button" class="btn btn-default waves-effect"  data-dismiss="modal"><i
                                    class="fa fa-close"></i> NO</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#item_gl_code').on('change',function(){
            var selected = $(this).find('option:selected');
            var account_name = selected.data('account_name');
            $("textarea#item_account_name").val(account_name);
        });

        $('#update_gl_code').on('change',function(){
            var selected = $(this).find('option:selected');
            var account_name = selected.data('account_name');
            $("textarea#update_account_name").val(account_name);
        });

        $(document).on('click', '.new_item', function() {
            var item_currency = $(this).data('currency');
            $('#item_currency').val(item_currency);

            var req_recid = $(this).data('req_recid');
            $('#item_req_recid').val(req_recid);
        });

        $(document).on('click', '#btn_delete_current_request', function() {
            $('#confirmDeleteModal').modal("show");
        });

        $(document).on('click', '#btn_update_exchange_rate', function() {
            $('#updateExchangeRateModal').modal("show");
        });


        $(document).on('click', '.edit_item', function() {
            var item_id = $(this).data('item_id');
            var req_recid = $(this).data('req_recid');
            var gl_code = $(this).data('gl_code');
            var account_name = $(this).data('account_name');
            var branch_code = $(this).data('branch_code');

            var dr_cr = $(this).data('dr_cr');
            var currency = $(this).data('item_currency');
            var amount = $(this).data('amount');

            var budget_code = $(this).data('budget_code');
            var al_budget_code = $(this).data('al_budget_code');
            var tax_code = $(this).data('tax_code');
            var supp_code = $(this).data('supp_code');
            var department_code = $(this).data('department_code');
            var product_code = $(this).data('product_code');
            var segment_code = $(this).data('segment_code');
            var naratives = $(this).data('naratives');

            $('#update_item_id').val(item_id);
            $('#update_req_recid').val(req_recid);
            $('#update_gl_code').val(gl_code);
            $('#update_account_name').val(account_name);
            $('#update_branch_code').val(branch_code);
            $('#update_budget_code').val(budget_code);
            $('#update_al_budget_code').val(al_budget_code);
            $('#update_tax_code').val(tax_code);
            $('#update_supplier_code').val(supp_code);
            $('#update_department_code').val(department_code);
            $('#update_product_code').val(product_code);
            $('#update_segment_code').val(segment_code);
            $('#update_narative').val(naratives);
            $('#update_dr_cr').val(dr_cr);
            $('#update_currency').val(currency);
            $('#update_amount').val(amount);
        });
    </script>
    <script>
        $("#frmCreateNew").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                'item_gl_code'    : {required: true, },
                'item_branch_code': {required: true, },
                'item_debit'      : {required: true, },
                'item_credit'     : {required: true, },
                'item_budget_code': {required: true, },
            },

            messages: {
                item_gl_code: "Please input mandatory field",
                item_branch_code: "Please input mandatory field",
                item_debit: "Please input mandatory field",
                item_credit: "Please input mandatory field",
                item_budget_code: "Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                if(
                    element.attr("name") == "item_gl_code" ||
                    element.attr("name") == "item_branch_code" ||
                    element.attr("name") == "item_debit" ||
                    element.attr("name") == "item_credit" ||
                    element.attr("name") == "item_budget_code"
                    ){
                    $('#validate_create_new_item').empty();
                    error.appendTo('#validate_create_new_item');
                }
            },
            submitHandler: function(form) {
                $(".overlay").show();
                    form.submit();
            }
        });
        $("#frmSubmit").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                'first_reviewer'    : {required: true, },
                'approver': {required: true, },
            },

            messages: {
                first_reviewer: "Please input mandatory field",
                approver: "Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                if(
                    element.attr("name") == "first_reviewer" ||
                    element.attr("name") == "approver"
                    ){
                    $('#validate_approver').empty();
                    error.appendTo('#validate_approver');
                }
            },
            submitHandler: function(form) {
                var comment = $("#comment").val();
                if (comment == '') {
                    if (confirm('Are you sure without any comment?')) {
                        $(".overlay").show();
                        form.submit();
                    } else {
                        return false;
                    }
                }else{
                    $(".overlay").show();
                    form.submit();
                }
            }
        });

    </script>
    <script>
	$(document).on("click", ".delete_attach", function() {
		var r = confirm("Are you sure?");
		if (r == true) {
			var attach_id = $(this).data('attachid');
			// alert(attach_id)
			$.ajax({
				url: "/removeattach/" + attach_id,
				type: 'get',
				dataType: 'json',
				success: function(response) {
					var ac_no = response['data'];
					location.reload();
					// alert(ac_no)
				},
				async: false
			});
		}
		// alert(txt)
	});
</script>
@endsection
