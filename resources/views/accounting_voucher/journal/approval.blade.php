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
                        <h4>Journal Voucher</h4>
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
                            Journal Voucher
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('payment_request.list_auth') }}">{{$journal->req_recid}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <form method="post" action="{{ route('form/journal-vouchers/approve-request') }}" name="frmApproveRequest" id="frmApproveRequest"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="grand_total" name="req_recid" id="req_recid" value="{{$journal->req_recid}}">
            <div class="col-sm-12">
                <div class="myheader" id="myHeader">
                    <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/journal-vouchers/export-to-pdf/' . Crypt::encrypt($journal['req_recid'].'___no')) }}"><i
                            class="fa fa-save" aria-hidden="true"></i>
                            EXPORT PDF</a>
                    <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/journal-vouchers/export-to-excel/' . Crypt::encrypt($journal['req_recid'].'___no')) }}"><i
                        class="fa fa-save" aria-hidden="true"></i>
                        EXPORT EXCEL</a>
                </div>
                <!-- Page-body start -->
                <div class="page-body">

                    <div class="card">
                        <div class="card-block">
                            <h4 class="sub-title">Journal Voucher</h4>
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
                                            @if($journal->ref_no)
                                            <div class="form-control bg-light">
                                                @foreach($rp_ref_no_advance as $data)
                                                    <a class="" href="{{$data['href']}}">{{$data['value']}}</a>
                                                @endforeach
                                            </div>
                                            @else
                                                <input type="text" name="reference_number" id="reference_number" class="form-control" value="N/A" readonly="">
                                            @endif
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
                                            <input type="text" class="form-control" value="{{ $journal->department}}" readonly="" name="department">
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
                                                value="{{date('d/m/Y', strtotime($journal->created_at))}}" readonly="">
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
                                            <input type="text" class="form-control" id="voucher_number" name="voucher_number"  value="{{ $journal->voucher_number }}" readonly="">
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
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{$journal->exchange_rate}}" readonly="" name="exchange_rate">
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
                                <div class="table-responsive dt-responsive">
                                    <table class="table table-striped table-bordered" id="example-1" style="font-size: 12.5px;">
                                        <thead>
                                            <tr class="table-info">
                                                <th >GL CODE</th>
                                                <th >ACCOUNT NAME</th>
                                                <th >BRANCH CODE</th>
                                                <th >DR/CR</th>
                                                <th >CURRENCY</th>
                                                <th >AMOUNT</th>
                                                <th >BUDGET CODE</th>
                                                <th >AL BUDGET CODE</th>
                                                <th >TAX CODE</th>
                                                <th >SUPP CODE</th>
                                                <th >DEPT CODE</th>
                                                <th >PRO CODE</th>
                                                <th >SEG CODE</th>
                                                <th >NARRATIVES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($journalDetails as $key => $value)
                                            <tr>
                                                <td class="tabledit-view-mode">{{ $value->gl_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->account_name }}</td>
                                                <td class="tabledit-view-mode">{{ $value->branch_code }}</td>
                                                <td > @if ($value->dr_cr == 'DEBIT') DR @else CR @endif  </td>
                                                <td > {{ $value->currency }}</td>
                                                <td > @money($value->amount)</td>
                                                <td class="tabledit-view-mode">{{ $value->budget_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->al_budget_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->tax_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->supp_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->department_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->product_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->segment_code }}</td>
                                                <td class="tabledit-view-mode">{!! nl2br(e($value->naratives)) !!}</td>
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
                                                   <td class="w-50"><textarea class="fix-col">{{$data->budget_item}}</textarea></td>
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
                                                   <td class="w-50"><textarea class="fix-col">{{$data->budget_item}}</textarea></td>
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
                                            <input type="text" class="form-control" value="{{$journal->payment_method_code}}" readonly="" name="currency">
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
                                            <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{ $journal->bank_name }}" readonly="">
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
                                            <input type="text" class="form-control" name="swift_code" id="swift_code" value="{{ $journal->swift_code }}" readonly="">
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
                                            <input type="text" class="form-control" name="account_currency" id="account_currency" value="{{ $journal->account_currency }}" readonly="">
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
                                            <input type="text" class="form-control" name="account_name" id="account_name" value="{{ $journal->account_name }}" readonly="">
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
                                            <input type="text" class="form-control" name="account_number" id="account_number" value="{{ $journal->account_number }}" readonly="">
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
                                            <input type="text" class="form-control" name="benificiary_name" id="benificiary_name" value="{{ $journal->beneficiary_number }}" readonly="">
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
                                            <input type="text" class="form-control" name="invoice_number" id="invoice_number" value="{{ $journal->invoice_number }}" readonly="">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row form-group" style="padding-top: 15px;">
                                <div class="col-sm-12 mobile-inputs">
                                    <label for="">Note :</label>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <textarea name="note" class="form-control " readonly="">{{ $journal->note }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <ul class="nav nav-tabs  tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#action" role="tab"
                                        aria-expanded="true">ACTION</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab"
                                        aria-expanded="false" >ACTIVITY LOG</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#preference_document" role="tab"  aria-expanded="false">
                                    REFERENCE DOCUMENT
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#progress" role="tab" aria-expanded="false">
                                PROGRESS
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content tabs card-block">
                            <div class="tab-pane active" id="action" role="tabpanel" aria-expanded="false" style="padding-bottom: 15px;">
                                <div class="row">
                                    @if ($isRequirToSelectPaymentMethod)
                                        <div class="col-sm-12 mobile-inputs">
                                            <div class="form-group row">
                                              <div class="col-sm-12">
                                                  <span>Batch No</span>
                                              </div>
                                              @if($journal->batch_number)
                                              <div class="col-sm-12">
                                                  <input type="text" name="batch_no" id="batch_no" value="{{$journal->batch_number}}" class="tabledit-input form-control input-sm" style="margin-bottom: 0px;">
                                              </div>
                                              @else
                                                <div class="col-sm-12">
                                                    <input type="text" name="batch_no" id="batch_no" value="" class="tabledit-input form-control input-sm" style="margin-bottom: 0px;">
                                                </div>
                                              @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-sm-12 mobile-inputs">
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <span>Comment</span>
                                            </div>
                                            <div class="col-sm-12">
                                                <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 mobile-inputs">
                                        @if($approval_change_status)
                                            <button type="submit" name="activity" value="authorize" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;" id="btnAccountingApprove">
                                                <i class="fa fa-check" aria-hidden="true" style="color: green"></i>
                                                Authorize
                                            </button>
                                        @else
                                            <button type="submit" name="activity" value="approve" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;" id="btnAccountingApprove">
                                                <i class="fa fa-check" aria-hidden="true" style="color: green"></i>
                                                @if($isApprover)
                                                    Approve
                                                @else
                                                    Confirm
                                                @endif
                                            </button>
                                            <button type="submit" name="activity" value="reject" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;">
                                                <i class="fa fa-times" aria-hidden="true" style="color: red"></i>
                                                Reject
                                            </button>
                                            <button type="submit" name="activity" value="assign_back" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;">
                                                <i class="fa fa-backward" aria-hidden="true" style="color: blue"></i>
                                                Assign Back
                                            </button>
                                            <button type="submit" name="activity" value="query" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;">
                                                <i class="fa fa-commenting" aria-hidden="true" style="color:orange"></i>
                                                Query
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="activitylog" role="tabpanel" aria-expanded="false" style="padding-bottom: 15px;">
                                <div class="timeline">
                                    <div class="timeline-month bg-c-yellow"
                                        style="font-weight: bold;font-size: 16px;color: white;background: #FF7814">
                                        Journal Voucher FORM REQUEST
                                    </div>
                                    @foreach ($auditlogs as $value)
                                        <div class="timeline-section">
                                            <div class="timeline-date" style="background: #3D9CDD">
                                                {{ $value->datetime }}
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="timeline-box bg-c-lite-green">
                                                        @if(!empty($value->doer_role))
                                                            <div class="box-title"
                                                                style="font-weight: bold;color: white;font-size: 16px;">
                                                                <i class="fa fa-user"></i> {{$value->doer_role}}: {{ $value->name }}
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                    </strong>{{ $value->doer_action }}</div>
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Comment:
                                                                    </strong>{{ $value->comment }}</div>
                                                            </div>
                                                        @else
                                                            <div class="box-title"
                                                                style="font-weight: bold;color: white;font-size: 16px;">
                                                                <i class="fa fa-user"></i> {{ $value->name }}
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                    </strong>{{ $value->activity }}</div>
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Comment:
                                                                    </strong>{{ $value->comment }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="tab-pane" id="preference_document" role="tabpanel" aria-expanded="false">
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
                                            </tr>
                                        @endforeach
                                    <input type="hidden" value="" name="att_remove" id="att_remove">
                                    <span id="att_remove1" style="display: none;"></span>
                                </table>
                            </div>

                            <div class="tab-pane" id="progress" role="tabpanel" aria-expanded="false">
                                <div class="card-block panels-wells">
                                    <div class="row">
                                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 ">
                                            <div class="{{$tasklist->assign_back_by ? 'panel panel-danger ' :'panel panel-primary' }}">
                                                <div class="{{$tasklist->assign_back_by ? 'panel-heading demo_blink' :'panel-heading bg-primary' }}" style="color: white;">
                                                    Requester
                                                </div>
                                                <div class="panel-body">
                                                <span style="font-size: 14px; font-weight: bold;">{{ $tasklist->req_name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @foreach ($approvalUsers as $approver)
                                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 ">
                                                <div class="{{$approver->is_pending ? 'panel panel-danger ' :'panel panel-primary' }}">
                                                    <div class="{{$approver->is_pending ? 'panel-heading demo_blink' :'panel-heading bg-primary' }}" style="color: white;">
                                                    {{ $approver->label }}
                                                    </div>
                                                    <div class="panel-body">
                                                    <span style="font-size: 14px; font-weight: bold;">
                                                    {{ $approver->full_name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-xl-12 col-lg-4 col-md-4 col-sm-6">
                                        Pending At: <span style="font-size: 14px; color: red;font-weight: bold;">{{$pendingUser }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" id="modalAccountingApprove" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 230%;  position: absolute; left: 50%; top: 50%; transform: translate(-50%, 5%);">
                    <div class="modal-header">
                        <h4 class="modal-title" id="confirmTitle"><span></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">Are you sure you want to use default payment method ?</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="table-responsive" style="overflow-y: scroll;max-height: 350px;">
                            <table id="tbAdvancePreview" class="table table-striped dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Budget Code</th>
                                                    <th>Alternative Budget Code</th>
                                                    <th>Total Req</th>
                                                    <th>YTD Expense</th>
                                                    <th>Total budget</th>
                                                    <th>Total remain</th>
                                                    <th>Status</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="btnConfirmClearAdvance">CONFIRM</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    window.onscroll = function() {
        myFunction()
    };

    var header = document.getElementById("myHeader");
    var sticky = header.offsetTop;

    function myFunction() {
        if (window.pageYOffset > sticky) {
            header.classList.add("sticky");
        } else {
            header.classList.remove("sticky");
        }
    }
</script>
<script type="text/javascript">
    document.getElementById("btnRedirect").onclick = function () {
        location.href = "{{ route('requestlog/listing') }}";
    };
</script>
<script type="text/javascript">
$("#frmApproveRequest").validate({

    rules: {
        batch_no  : {required: true, }
    },

    messages: {
        batch_no: "Please input mandatory field",
    }

});
</script>
@endsection
