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
                        <h4>Clear Advance Form Requests</h4>
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
                        Clear ADVANCE FORM
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('payment_request.list_auth') }}">{{$clearAdvanceForm->req_recid}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/clear-advances/items/query-back-to-approver') }}" name="frmQueryRequest" id="frmQueryRequest"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="grand_total" name="req_recid" id="req_recid" value="{{$clearAdvanceForm->req_recid}}">
            <!-- Page-body start -->
            <div class="page-body">
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Clear Advance form</h4>
                        <span id="validate_advance_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Status</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{$tasklist->record_status_description}}" readonly="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Advance Form Ref.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        @if($clearAdvanceForm->advance_ref_no)
                                            <div class="form-control bg-light">
                                                @foreach($request_id as $data)
                                                    <a class="" href="{{$data['href']}}">{{$data['value']}}</a>
                                                @endforeach
                                            </div>
                                            <!-- <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{$clearAdvanceForm->advance_ref_no}}" readonly=""> -->
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
                                        <input type="text" class="form-control" value="{{$clearAdvanceForm->department}}"  name="department" readonly="">
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
                                        <input type="text" class="form-control" id="request_date" name="request_date" value="{{ \Carbon\Carbon::parse($clearAdvanceForm->request_date)->format('d/m/y') }}" readonly="" >
                                    </div>
                                    <div class="col-sm-2">
                                        <div>
                                            <span>Due Date</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        @if ($clearAdvanceForm->due_date)
                                            <input type="text" class="form-control" id="due_date" name="due_date"
                                                value="{{ \Carbon\Carbon::parse($clearAdvanceForm->due_date)->format('d/m/Y') }}" readonly="">
                                        @else
                                            <input type="text" class="form-control" id="due_date" name="due_date" value="N/A" readonly="">
                                        @endif
                                    </div>
                                </div>
                               
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Currency</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{$clearAdvanceForm->currency}}"  name="currency" readonly="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Subject</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="2" id="subject" name="subject" readonly>{{$clearAdvanceForm->subject}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Category</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input onclick="return false"  class="border-checkbox chb_2" type="checkbox" id="category"
                                                name="category[]" value="Ordinary" {{ ($clearAdvanceForm->category) == 'Ordinary' ? "checked" : "" }} >
                                            <label class="border-checkbox-label" for="category" >Ordinary</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input onclick="return false"  class="border-checkbox chb_2" type="checkbox" id="category1"
                                                name="category[]" value="Event/Project" {{ ($clearAdvanceForm->category) == 'Event/Project' ? "checked" : "" }}>
                                            <label class="border-checkbox-label" for="category1">Event/Project</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input onclick="return false"  class="border-checkbox chb_2" type="checkbox" id="category2"
                                                name="category[]" value="Staff Benefit" {{ ($clearAdvanceForm->category) == 'Staff Benefit' ? "checked" : "" }}>
                                            <label class="border-checkbox-label" for="category2">Staff Benefit</label>
                                        </div>
                                    </div>
        
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">PAID TO</h4>
                        <span id="validate_paid_to_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>Account Name</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input readonly="" type="text" class="form-control" id="account_name" name="account_name" value="{{$clearAdvanceForm->account_name}}">
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
                                            <input readonly="" type="text" class="form-control" id="account_number"
                                                name="account_number" value="{{$clearAdvanceForm->account_number}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>Bank Name</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input readonly="" type="text" class="form-control" id="bank_name" name="bank_name" value="{{$clearAdvanceForm->bank_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>Bank Address</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea readonly="" class="form-control" rows="2" id="bank_address"
                                                name="bank_address" > {{$clearAdvanceForm->bank_address}} </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>Tel</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input readonly="" type="text" id="phone_number" name="phone_number" class="form-control" value="{{$clearAdvanceForm->phone_number}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">For</h4>
                        <span id="validate_for_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>Mr./Ms./Company</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 ">
                                            <input readonly="" type="text" id="company" name="company" class="form-control" value="{{$clearAdvanceForm->company_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>ID No.</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input readonly="" type="text" id="id_no" name="id_no" class="form-control" value="{{$clearAdvanceForm->id_number}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span>Contact No</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input readonly="" type="text" id="contact_no" name="contact_no" class="form-control" value="{{$clearAdvanceForm->contact_number}}">
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
                                            <textarea readonly="" class="form-control" rows="2" id="address" name="address">{{$clearAdvanceForm->address}}</textarea>
                                        </div>
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
                                            <th rowspan="2" style="vertical-align:middle;">No</th>
                                            <th rowspan="2" style="vertical-align:middle;">Invoice No</th>
                                            <th rowspan="2" style="vertical-align:middle;">Description</th>
                                            <th rowspan="2" style="vertical-align:middle;">Br./Dep Code</th>
                                            <th rowspan="2" style="vertical-align:middle;">Budget Code</th>
                                            <th rowspan="2" style="vertical-align:middle;">Alternative Budget Code</th>
                                            <th rowspan="2" style="vertical-align:middle;">Unit</th>
                                            <th rowspan="2" style="vertical-align:middle;">QTY</th>
                                            <th rowspan="2" style="vertical-align:middle;">Unit price</th>
                                            <th rowspan="2" style="vertical-align:middle;">VAT</th>
                                            <th rowspan="2" style="vertical-align:middle;">Total</th>
                                            <th rowspan="2" style="vertical-align:middle;">YTD Expense</th>
                                            <th rowspan="2" style="vertical-align:middle;">Total Budget</th>
                                            <th rowspan="2" style="vertical-align:middle;">Within Budget</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clearAdvanceFormDetail as $key => $value)
                                            <tr>
                                                <td> {{ ++$key }} </td>
                                                <td class="tabledit-view-mode">{{ $value->invoice_number }}</td>
                                                <td class="tabledit-view-mode">{{ $value->description }}</td>
                                                <td class="tabledit-view-mode">{{ $value->department_code }}</td>
                                                <td class="tabledit-view-mode">{{ $value->budget_code }}</td>
                                                <td class="tabledit-view-mode">
                                                    @if ($value->alternative_budget_code > 0)
                                                        {{ $value->alternative_budget_code }}
                                                    @else
                                                        N/A
                                                    @endif

                                                </td>
                                                <td class="tabledit-view-mode"> {{ $value->unit }}</td>
                                                <td class="tabledit-view-mode"> {{ $value->quantity }} </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($clearAdvanceForm->currency == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value->unit_price_khr)
                                                    @else
                                                        $@money($value->unit_price_usd)
                                                    @endif
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($clearAdvanceForm->currency == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value->vat_item_khr)
                                                    @else
                                                        $@money($value->vat_item)
                                                    @endif
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($clearAdvanceForm->currency == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value->total_amount_khr)
                                                    @else
                                                        $@money($value->total_amount_usd)
                                                    @endif
                                                </td>
                                                <td class="tabledit-view-mode">${{ $value->total_budget_ytd_expense_amount + $value->total_alt_budget_ytd_expense_amount }}</td>
                                                <td class="tabledit-view-mode">${{ $value->total_budget_amount + $value->total_alt_budget_amount }}
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($value->within_budget == 'Y')
                                                        <span
                                                            style="color:green;font-size: 14px;font-weight: bold;">YES</span>
                                                    @else
                                                        <span
                                                            style="color:red;font-size: 14px;font-weight: bold;">NO</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="10" style="text-align: right;">
                                                SUB TOTAL
                                            </td>
                                            <td >
                                                @if ($clearAdvanceForm->currency == 'USD')
                                                    $@money($clearAdvanceForm->total_amount_usd)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($clearAdvanceForm->total_amount_khr)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="10" style="text-align: right;">
                                                DISCOUNT
                                            </td>
                                            <td>
                                                @if ($clearAdvanceForm->currency == 'USD')
                                                    $@money($clearAdvanceForm->discount_amount_usd)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($clearAdvanceForm->discount_amount_khr)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="10" style="text-align: right;">
                                                WHT
                                            </td>
                                            <td>
                                                @if ($clearAdvanceForm->currency == 'USD')
                                                    $@money($clearAdvanceForm->wht_amount_usd)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($clearAdvanceForm->wht_amount_khr)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="10" style="text-align: right;">
                                               ADVANCED AMOUNT
                                            </td>
                                            <td >
                                                @if ($clearAdvanceForm->currency == 'USD')
                                                    $@money($clearAdvanceForm->total_advance_amount_usd)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($clearAdvanceForm->total_advance_amount_khr)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="10" style="text-align: right;">
                                               NET PAYABLE AMOUNT
                                            </td>
                                            <td>
                                                @if ($clearAdvanceForm->currency == 'USD')
                                                    $@money($clearAdvanceForm->net_payable_amount_usd)
                                                @else
                                                    <span style="font-size: 18px;">៛</span>
                                                    @money($clearAdvanceForm->net_payable_amount_khr)
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
                            <div class="row form-group" style="padding-top: 15px;">
                                <div class="col-sm-2 mobile-inputs">
                                    <label for="">Additional Remarks (if any):</label>
                                </div>
                                <div class="col-sm-10 mobile-inputs">
                                    <textarea name="additional_remarks" class="form-control ">{{$clearAdvanceForm->additional_remark}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-block">
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <span id="product_error" style="color: red;font-size: 15px; font-weight: bold;"></span>
                                <table class="text-center table table-striped table-bordered" id="product_input"
                                    style="font-size: 12.5px">
                                    <thead>
                                        <tr class="table-info">
                                            <th>Product</th>
                                            <th colspan="16">Input Allocated % (in total equal 100%)</th>
                                        </tr>
                                        <tr class="table-success">
                                            <th>Categories:</th>
                                            <th style="text-align: center">General</th>
                                            <th colspan="4" class="text-center">Loan</th>
                                            <th colspan="3" class="text-center">Card</th>
                                            <th colspan="3" class="text-center">Trade</th>
                                            <th colspan="5" class="text-center">Deposit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td rowspan="2" class="table-success" style="text-align: left"><b>Type :</b>
                                            </td>
                                            <td rowspan="2">General</td>
                                            <td rowspan="2">Loan- <br> General</td>
                                            <td rowspan="2">Mortgage</td>
                                            <td rowspan="2">Business</td>
                                            <td rowspan="2">Personal</td>
                                            <td rowspan="2">Card- <br> General</td>
                                            <td rowspan="2">Debit <br> Card</td>
                                            <td rowspan="2">Credit <br> Card</td>
                                            <td rowspan="2">Trade- <br> General</td>
                                            <td rowspan="2">Bank <br> Guarantee</td>
                                            <td rowspan="2">Letter <br> of Credit</td>
                                            <td rowspan="2">Deposit- <br> General</td>
                                            <td rowspan="2">CASA- <br> Individual</td>
                                            <td rowspan="2">TD- <br> Individual</td>
                                            <td rowspan="2">CASA- <br> Corporate</td>
                                            <td rowspan="2">TD- <br> Corporate</td>
                                        </tr>
                                        <tr></tr>
                                        <tr>
                                            <td class="table-success" style="text-align: left"><b>Code :</b></td>
                                            <td>999</td>
                                            <td>100</td>
                                            <td>101</td>
                                            <td>102</td>
                                            <td>103</td>
                                            <td>200</td>
                                            <td>201</td>
                                            <td>202</td>
                                            <td>300</td>
                                            <td>301</td>
                                            <td>302</td>
                                            <td>400</td>
                                            <td>401</td>
                                            <td>402</td>
                                            <td>403</td>
                                            <td>404</td>
                                        </tr>
                                        <tr>
                                            <td class="table-success" style="text-align: left"><b>Allocated % :</b></td>
                                            <td align="center">
                                                <input type="hidden" class="grand_total" name="" value="100">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_general" id="product_general"
                                                    onpaste="return false;" value="{{$product->general}}%">

                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_loan_general"
                                                    id="product_loan_general" onpaste="return false;"  value="{{$product->loan_general}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_mortgage"
                                                    id="product_mortgage" onpaste="return false;" value="{{$product->mortgage}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_business"
                                                    id="product_business" onpaste="return false;" value="{{$product->business}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_personal"
                                                    id="product_personal" onpaste="return false;" value="{{$product->personal}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_card_general"
                                                    id="product_card_general" onpaste="return false;" value="{{$product->card_general}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_debit_card"
                                                    id="product_debit_card" onpaste="return false;" value="{{$product->debit_card}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_credit_card"
                                                    id="product_credit_card" onpaste="return false;" value="{{$product->credit_card}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_trade_general"
                                                    id="product_trade_general" onpaste="return false;" value="{{$product->trade_general}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_bank_guarantee"
                                                    id="product_bank_guarantee" onpaste="return false;" value="{{$product->bank_general}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_letter_of_credit"
                                                    id="product_letter_of_credit" onpaste="return false;" value="{{$product->letter_of_credit}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_deposit_general"
                                                    id="product_deposit_general" onpaste="return false;" value="{{$product->deposit_general}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_casa_individual"
                                                    id="product_casa_individual" onpaste="return false;" value="{{$product->casa_individual}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_td_individual"
                                                    id="product_td_individual" onpaste="return false;" value="{{$product->td_individual}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_casa_corporate"
                                                    id="product_casa_corporate" onpaste="return false;" value="{{$product->casa_corporate}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement"
                                                    style="width: 70px !important;" type="text" name="product_td_corporate"
                                                    id="product_td_corporate" onpaste="return false;" value="{{$product->td_corporate}}%">
                                                <span id="sum" style="display: none;">0</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                            <span id="sagement_error" style="color: red;font-size: 15px; font-weight: bold;"></span>
                                <table class="text-center table table-striped table-bordered" id="segment_input"
                                    style="font-size: 12.5px">
                                    <thead>
                                        <tr class="table-info">
                                            <th>Segment</th>
                                            <th colspan="6">Input Allocated % (in total equal 100%)</th>
                                        </tr>
                                        <tr class="table-success">
                                            <th>Categories:</th>
                                            <th style="text-align: center">General</th>
                                            <th class="text-center">BFS</th>
                                            <th class="text-center">RFS (ex PB)</th>
                                            <th class="text-center">PB</th>
                                            <th class="text-center">PCP</th>
                                            <th class="text-center">AFS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="table-success" style="text-align: left"><b>Code :</b></td>
                                            <td>999</td>
                                            <td>100</td>
                                            <td>200</td>
                                            <td>300</td>
                                            <td>400</td>
                                            <td>500</td>
                                        </tr>
                                        <tr>
                                            <td class="table-success" style="text-align: left"><b>Allocated % :</b></td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_general"
                                                    id="segment_general" onpaste="return false;" value="{{$segment->general}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_bfs" id="segment_bfs"
                                                    onpaste="return false;" value="{{$segment->bfs}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_rfs" id="segment_rfs"
                                                    onpaste="return false;" value="{{$segment->rfs_ex_pb}}%">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_pb" id="segment_pb"
                                                    onpaste="return false;" value="{{$segment->pb}}%">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_pcp" id="segment_pcp"
                                                    onpaste="return false;" value="{{$segment->pcp}}%">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                            <td align="center">
                                                <input readonly="" class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_afs" id="segment_afs"
                                                    onpaste="return false;" value="{{$segment->afs}}%">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row form-group" style="padding-top: 15px;">
                                <div class="col-sm-12 mobile-inputs">
                                    <label for="">Additional Remarks (if any) for product and segment :</label>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <textarea readonly="" name="remarks_product_segment" name="remarks_product_segment"
                                        class="form-control ">{{$clearAdvanceForm->additional_remark_product_segment}}</textarea>
                                </div>
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
                                    aria-expanded="true">ACTIVITY LOG</a>
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
                                    <button type="submit" name="activity" value="query" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;">
                                        <i class="fa fa-commenting" aria-hidden="true" style="color:orange"></i> 
                                        Query
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane " id="activitylog" role="tabpanel" aria-expanded="false" style="padding-bottom: 15px;">
                            <div class="timeline">
                                <div class="timeline-month bg-c-yellow"
                                    style="font-weight: bold;font-size: 16px;color: white;background: #FF7814">
                                    CLEAR ADVANCE FORM REQUEST
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
                                                            <i class="fa fa-user"></i>{{ $value->doer_role }}: {{ $value->name }}
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
                                        <div class="panel panel-primary">
                                            <div class="panel-heading bg-primary">
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
            <!-- Row Created Callback table end -->
        </form>
    </div>

    <form id="delete_frm" method="get">
        {{ csrf_field() }}
        <input type="hidden" name="param_req_recid" id="param_req_recid">
    </form>

@endsection

@section('script')
<script type="text/javascript">
    document.getElementById("btnRedirect").onclick = function () {
        location.href = "{{ route('requestlog/listing') }}";
    };
</script>
@endsection