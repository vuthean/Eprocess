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
                        <h4>Advance Form Requests</h4>
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
                            ADVANCE FORM
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('payment_request.list_auth') }}">{{$advanceForm->req_recid}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/advances/items/submit') }}" name="frmUpdateRequest" id="frmUpdateRequest"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden"  name="req_recid" id="req_recid" value="{{$advanceForm->req_recid}}">
            <div class="myheader" id="myHeader">
                <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/advances/export-to-pdf/' . Crypt::encrypt($advanceForm['req_recid'].'___no')) }}"><i
                        class="fa fa-save" aria-hidden="true"></i>
                        EXPORT PDF</a>
            </div>
            <!-- Page-body start -->
            <div class="page-body">
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Advance form</h4>
                        <span id="validate_advance_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Procurement Ref.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        @if($advanceForm->ref)
                                            <div class="form-control bg-light">
                                                @foreach($request_id as $data)
                                                    <a class="" href="{{$data['href']}}">{{$data['value']}}</a>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="reference_number" id="reference_number" class="form-control" value="{{$advanceForm->ref}}" readonly="">
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
                                        @if($advanceForm->department)
                                            <input type="text" class="form-control" value="{{$advanceForm->department}}" readonly="" name="department">
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly="" name="department">
                                        @endif
                                       
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
                                            value="{{ \Carbon\Carbon::parse($advanceForm->request_date)->format('d/m/Y') }}" readonly="">
                                    </div>
                                    <div class="col-sm-2">
                                        <div>
                                        <span style="color: red;">*</span>
                                            <span>Due Date</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        @if ($advanceForm->due_date)
                                            <input type="date" class="form-control" id="due_date" name="due_date"
                                                value="{{ \Carbon\Carbon::parse($advanceForm->due_date)->format('Y-m-d') }}">
                                        @else
                                            <input type="date" class="form-control" id="due_date" name="due_date">
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
                                            <input type="text" class="form-control" value="{{$advanceForm->currency}}" readonly="" name="currency">
                                        </div>
                                    </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Subject</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="2" id="subject" name="subject">{{$advanceForm->subject}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Category</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox chb_2" type="checkbox" id="category"
                                                name="category[]" value="Ordinary" {{ ($advanceForm->category) == 'Ordinary' ? "checked" : "" }} >
                                            <label class="border-checkbox-label" for="category" >Ordinary</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox chb_2" type="checkbox" id="category1"
                                                name="category[]" value="Event/Project" {{ ($advanceForm->category) == 'Event/Project' ? "checked" : "" }}>
                                            <label class="border-checkbox-label" for="category1">Event/Project</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox chb_2" type="checkbox" id="category2"
                                                name="category[]" value="Staff Benefit" {{ ($advanceForm->category) == 'Staff Benefit' ? "checked" : "" }}>
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
                                                <span style="color: red;">*</span>
                                                <span>Account Name</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="account_name" name="account_name" value="{{$advanceForm->account_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Account Number</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="account_number"
                                                name="account_number" value="{{$advanceForm->account_number}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Bank Name</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{$advanceForm->bank_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Bank Address</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="2" id="bank_address"
                                                name="bank_address" > {{$advanceForm->bank_address}} </textarea>
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
                                            <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{$advanceForm->phone_number}}">
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
                                                <span style="color: red;">*</span>
                                                <span>Mr./Ms./Company</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 ">
                                            <input type="text" id="company" name="company" class="form-control" value="{{$advanceForm->company_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>ID No.</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="id_no" name="id_no" class="form-control" value="{{$advanceForm->id_number}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Contact No</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="contact_no" name="contact_no" class="form-control" value="{{$advanceForm->contact_number}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                <span style="color: red;">*</span>
                                                <span>Address</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="2" id="address" name="address">{{$advanceForm->address}}</textarea>
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
                            <!-- check if user already select reference, we not allow to add new item -->
                            @if(!$advanceForm->ref || $advanceForm->ref == 'N/A' || $advanceForm->ref == 'NA')
                                <button type="button" class="btn btn-primary new_item" data-toggle="modal"
                                                    data-target="#new-item-Modal"
                                                    data-req_recid="{{ $advanceForm->req_recid }}"
                                                    data-currency="{{ $advanceForm->currency }}"
                                                    
                                                    style="float: right"
                                                    id="btn_add_new"><i class="fa fa-plus"></i>ADD NEW ITEM</button>
                            @endif
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
                                            <th rowspan="2" style="vertical-align:middle;"> Action</th>
                                            <th style="display: none;">pr_col_id</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($advanceFormDetails as $key => $value)
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
                                                    @if ($advanceForm->currency == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value->unit_price_khr)
                                                    @else
                                                        $@money($value->unit_price_usd)
                                                    @endif
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($advanceForm->currency == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value->vat_item_khr)
                                                    @else
                                                        $@money($value->vat_item)
                                                    @endif
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($advanceForm->currency == 'KHR')
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
                                                <td>
                                                    <a href="#" class="edit_item" data-toggle="modal"
                                                            data-target="#update-item-Modal"
                                                            data-item_id="{{ $value->id }}"
                                                            data-req_recid="{{ $value->req_recid }}"
                                                            data-description="{{ $value->description }}"
                                                            data-department_code="{{ $value->department_code }}"
                                                            data-unit="{{ $value->unit }}"
                                                            data-quantity="{{ $value->quantity }}"
                                                            data-vat_item="{{ $value->vat_item }}"
                                                            data-unit_price_usd="{{ $value->unit_price_usd }}"
                                                            data-unit_price_khr="{{ $value->unit_price_khr }}"
                                                            data-budget_code="{{ $value->budget_code }}"
                                                            data-alternative_budget_code="{{ $value->alternative_budget_code }}"
                                                            data-currency="{{ $advanceForm->currency }}"
                                                            data-invoice_no="{{$value->invoice_number}}"
                                                        >
                                                            <i class="fa fa-edit" style="font-size: 20px;color: #0ac282;"></i>
                                                    </a>
                                                </td>
                                                <td style="display:none">
                                                    <input type="text" name="procurement_details[]" value="{{$value->procurment_body_id}}">
                                                </td>
                                            </tr>
                                        @endforeach

                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                TOTAL ADVANCE AMOUNT
                                                </td>
                                                <td>
                                                    @if ($advanceForm->currency == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($advanceForm->total_amount_khr)
                                                    @else
                                                        $@money($advanceForm->total_amount_usd)
                                                    @endif

                                                </td>
                                            </tr>
                                    </tbody>


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
                                <div class="col-sm-12 mobile-inputs">
                                    <label for="">Additional Remarks (if any):</label>
                                </div>
                                <div class="col-sm-12 mobile-inputs">
                                    <textarea name="additional_remarks" class="form-control ">{{$advanceForm->additional_remark}}</textarea>
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
                                                <input type="hidden" class="product_grand_total" name="" value="100">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_general" id="product_general"
                                                    onpaste="return false;" value="{{$product->general}}">

                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_loan_general"
                                                    id="product_loan_general" onpaste="return false;"  value="{{$product->loan_general}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_mortgage"
                                                    id="product_mortgage" onpaste="return false;" value="{{$product->mortgage}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_business"
                                                    id="product_business" onpaste="return false;" value="{{$product->business}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_personal"
                                                    id="product_personal" onpaste="return false;" value="{{$product->personal}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_card_general"
                                                    id="product_card_general" onpaste="return false;" value="{{$product->card_general}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_debit_card"
                                                    id="product_debit_card" onpaste="return false;" value="{{$product->debit_card}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_credit_card"
                                                    id="product_credit_card" onpaste="return false;" value="{{$product->credit_card}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_trade_general"
                                                    id="product_trade_general" onpaste="return false;" value="{{$product->trade_general}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_bank_guarantee"
                                                    id="product_bank_guarantee" onpaste="return false;" value="{{$product->bank_general}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_letter_of_credit"
                                                    id="product_letter_of_credit" onpaste="return false;" value="{{$product->letter_of_credit}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_deposit_general"
                                                    id="product_deposit_general" onpaste="return false;" value="{{$product->deposit_general}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_casa_individual"
                                                    id="product_casa_individual" onpaste="return false;" value="{{$product->casa_individual}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_td_individual"
                                                    id="product_td_individual" onpaste="return false;" value="{{$product->td_individual}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_casa_corporate"
                                                    id="product_casa_corporate" onpaste="return false;" value="{{$product->casa_corporate}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt product"
                                                    style="width: 70px !important;" type="text" name="product_td_corporate"
                                                    id="product_td_corporate" onpaste="return false;" value="{{$product->td_corporate}}">
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
                                                <input class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_general"
                                                    id="segment_general" onpaste="return false;" value="{{$segment->general}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_bfs" id="segment_bfs"
                                                    onpaste="return false;" value="{{$segment->bfs}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_rfs" id="segment_rfs"
                                                    onpaste="return false;" value="{{$segment->rfs_ex_pb}}">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_pb" id="segment_pb"
                                                    onpaste="return false;" value="{{$segment->pb}}">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_pcp" id="segment_pcp"
                                                    onpaste="return false;" value="{{$segment->pcp}}">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement" type="text"
                                                    style="width: 150px !important;" name="segment_afs" id="segment_afs"
                                                    onpaste="return false;" value="{{$segment->afs}}">
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
                                    <textarea name="remarks_product_segment" name="remarks_product_segment"
                                        class="form-control ">{{$advanceForm->additional_remark_product_segment}}</textarea>
                                </div>
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
                        <span id="validate_for_approval_pannel"></span>
                        <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                @foreach ($approvalUsers as $key => $value)
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div>
                                                @if($value->step_number != 1 && $value->form_control != 'input')
                                                    <span style="color: red;">*</span>
                                                @endif
                                                
                                                <span>{{$value->label}}</span>
                                                <span style="float: right;">:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($value->form_control == 'dropdown')
                                                <select class="js-example-basic-single" name="{{$value->checker}}" id="{{$value->checker}}">
                                                    <option selected="" disabled="" value="">Select One</option>
                                                    @if($value->step_number == 1)
                                                        <option value="">Skip Reviewer</option>
                                                    @endif
                                                    
                                                    @foreach($value->users as $user)
                                                        <option value="{{ $user->email . '/' . $user->role_id }}">
                                                        {{ $user->firstname }} {{ $user->lastname }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($value->form_control == 'input')
                                                <input readonly type="text" class="form-control" name="budgetOwner" 
                                                value="{{ $value->users }}">
                                            @endif
                                        </div>

                                        @if($key == 0)
                                            <div class="col-sm-1">
                                                <i id="add_reviewer" class="fa fa-plus-square" style="font-size: 20px;color: #0ac282; cursor: pointer;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Put the container only after first reviewer --}}
                                    @if($key == 0)
                                        <div id="additional-reviewers-container"></div>
                                    @endif
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
                                        <button type="button" class="btn btn-success waves-effect" name="submit" value="submit" id="btnConfirmAdvanceForm" >
                                            <i class="fa fa-save"></i>
                                            SUMIT
                                        </button>
                                        
                                        <button type="submit" name="submit" value="submit" id="submitForm" style="padding: 5px 10px 5px 10px;cursor: pointer; display: none;" >
                                        <i class="fa fa-save" aria-hidden="true" style="color: green"></i>Submit</button>
                                            
                                        <button type="button" class="btn btn-danger waves-effect" name="submit" value="submit" id="btn_delete_current_request" 
                                            data-toggle="modal" 
                                            data-target="#delete_request"
                                        >
                                            <i class="fa fa-trash"></i>
                                            DELETE
                                        </button>
                                    
                                    </div>
                                </div>
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
                                                <i class="fa fa-trash btnRemoveDocument" style="font-size: 20px;color: red; display: block;"
                                                    data-id_attach={{ $value['id'] }}></i>
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
    </div>

    <div class="modal fade" id="delete_request" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">DELETE REQUEST</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('form/advances/delete-request') }}" enctype="multipart/form-data"
                    id="frm_save">
                    <input type="hidden" name="req_recid" id="req_recid" value="{{$advanceForm->req_recid}}">
                    @csrf
                    <div class="modal-body">
                        <span>Are you sure you want to delete this request: {{$advanceForm->req_recid}} ?</span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger waves-effect" name="submit" value="delete"><i
                                class="fa fa-save"></i> Yes</button>
                        <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                class="fa fa-close"></i> No</button>
                        
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-item-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">DELETE ITEM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('form/advances/items/delete') }}" enctype="multipart/form-data"
                    id="frm_save">
                    <input type="hidden" name="item_id" id="item_id">
                    <input type="hidden" name="req_recid" id="req_recid">
                    @csrf
                    <div class="modal-body">
                        <span>Are you sure you want to delete this item ?</span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger waves-effect" name="submit" value="delete"><i
                                class="fa fa-save"></i> Yes</button>
                        <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                class="fa fa-close"></i> No</button>
                        
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="update-item-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h4 class="modal-title">UPDATE ITEM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('form/advances/items/update') }}" enctype="multipart/form-data"
                    id="frm_save">
                    <input type="hidden" name="update_item_id" id="update_item_id">
                    <input type="hidden" name="update_req_recid" id="update_req_recid">
                    <input type="hidden" name="currency" id="currency">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Invoice No</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="invoice_no" id="invoice_no">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Br/Dep Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select name="item_department_code" id="item_department_code" class="form-control">
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
                                            <span style="color: red;">*</span>
                                            <span>Budget Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="tabledit-input form-control input-sm" id="item_budget_code"
                                            name="item_budget_code">
                                            @foreach ($budgetCodes as $value)
                                                <option value="{{ $value->budget_code }}">{{ $value->budget_code }} {{ $value->budget_item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Alternative Budget Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="tabledit-input form-control input-sm"
                                            id="item_alternative_budget_code" name="item_alternative_budget_code">
                                            <option value="0">No</option>
                                            <!-- @foreach ($altBudgetCodes as $value)
                                                <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                            @endforeach -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Unit</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="item_unit" id="item_unit">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>QTY</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="item_qty" id="item_qty">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>VAT</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            @if ($advanceForm->currency == 'USD')
                                                <span class="input-group-addon usd">$</span>
                                            @else
                                                <span class="input-group-addon khr"
                                                    style="font-size: 20px;display: none;">៛</span>
                                            @endif

                                            <input type="number" class="form-control vat_item" min="0" step="any"
                                                name="item_vat" id="item_vat">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Unit Price</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            @if ($advanceForm->currency == 'USD')
                                                <span class="input-group-addon usd">$</span>
                                            @else
                                                <span class="input-group-addon khr"
                                                    style="font-size: 20px;display: none;">៛</span>
                                            @endif

                                            <input type="number" class="form-control" min="0" step="any"
                                                name="item_unit_price" id="item_unit_price">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12">
                                     <span>Description</span>
                                </div>
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="item_description" id="item_description"></textarea>
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

    <!-- create new item -->
    <div class="modal fade" id="new-item-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h4 class="modal-title">CREATE NEW ITEM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <span id="validate_create_new_item"></span>
                <form method="post" action="{{ route('form/advances/items/add-new') }}" enctype="multipart/form-data"
                    id="frm_add_new_item" name="frm_add_new_item">
                    <input type="hidden" name="new_req_recid" id="new_req_recid">
                    <input type="hidden" name="new_currency" id="new_currency">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Invoice No</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="invoice_no" id="invoice_no">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Br/Dep Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select name="new_department_code" id="new_department_code" class="form-control">
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
                                            <span style="color: red;">*</span>
                                            <span>Budget Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="tabledit-input form-control input-sm" id="new_budget_code"
                                            name="new_budget_code">
                                            @foreach ($budgetCodes as $value)
                                                <option value="{{ $value->budget_code }}">{{ $value->budget_code }} {{ $value->budget_item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Alternative Budget Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="tabledit-input form-control input-sm"
                                            id="new_alternative_budget_code" name="new_alternative_budget_code">
                                            <option value="0">No</option>
                                            <!-- @foreach ($altBudgetCodes as $value)
                                                <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                            @endforeach -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Unit</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="new_unit" id="new_unit">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>QTY</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="new_qty" id="new_qty">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>VAT</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control vat_item" name="vat_item" id="vat_item">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Unit Price</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            @if ($advanceForm->currency == 'USD')
                                                <span class="input-group-addon usd">$</span>
                                            @else
                                                <span class="input-group-addon khr"
                                                    style="font-size: 20px;display: none;">៛</span>
                                            @endif

                                            <input type="number" class="form-control" min="0" step="any"
                                                name="new_unit_price" id="new_unit_price">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12">
                                     <span>Description</span>
                                </div>
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="new_description" id="new_description"></textarea>
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

    <!-- preview before confirm  -->
    <div class="modal fade" id="modalAdvanceFormYTDPreview" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 230%;  position: absolute; left: 50%; top: 50%; transform: translate(-50%, 5%);">
                <div class="modal-header">
                    <h4 class="modal-title" id="confirmTitle"><span></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
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
                        <button type="button" class="btn btn-success" id="confirmProcess">CONFIRM</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
                    </div>
            </div>
        </div>
    </div>
    <form id="delete_frm" method="get">
        {{ csrf_field() }}
        <input type="hidden" name="param_req_recid" id="param_req_recid">
    </form>

@endsection

@section('script')
<script type="text/javascript">
    $('#confirmProcess').click(function(e){
        e.preventDefault();
        $('#modalAdvanceFormYTDPreview').modal("hide");
        var button = document.getElementById("submitForm");
        button.click();
    });

    $('#btnConfirmAdvanceForm').click(function(e) {
        e.preventDefault();
        let req_recid = $('#req_recid').val();
        $.ajax({
            url: "/form/advances/ytd-expense/preview",
            type: 'post',
            dataType: 'json',
            data:{req_recid,_token: '{{csrf_token()}}'},
            success:function(response){
                console.log(response.data);
                if(response['reposnseCode'] == '401'){
                    alert('Something went wrong');
                }else{
                    $("#confirmTitle span").html("");
                    $("#tbAdvancePreview tbody > tr").remove();
                    let payments = response.data;
                    let index = 1;
                    for (let i = 0; i < payments.length; i++) {
                        let statusStyle =  "style='font-weight: bold; color: #0ac282;'";
                        if(payments[i].status == 'NO'){
                            statusStyle =  "style='font-weight: bold; color: red;'";
                        }
                        $('#tbAdvancePreview tbody').append(
                            "<tr>"+
                                "<td>" + index + "</td>"+
                                "<td>" + payments[i].budget_code + "</td>"+
                                "<td>" + payments[i].alternative_budget_code + "</td>"+
                                "<td> USD " + payments[i].total_request + "</td>"+
                                "<td> USD " + payments[i].ytd_expense + "</td>"+
                                "<td> USD " + payments[i].total_budget + "</td>"+
                                "<td> USD " + payments[i].total_remaining_amount + "</td>"+
                                "<td " + statusStyle+ ">" + payments[i].status + "</td>"+
                            "</tr>"
                        );
                        index++;
                    }
                    $('#confirmTitle span').append("Are you sure you want to submit this request : "+req_recid+" ?");
                    $('#modalAdvanceFormYTDPreview').modal("show");
                }
            },
        });
    });
</script>
<script>
    $(document).on('click', '.delete_item', function() {
        var item_id = $(this).data('item_id');
        var req_recid = $(this).data('req_recid');
        $('#item_id').val(item_id);
        $('#req_recid').val(req_recid);
    });
    $(document).on('click', '.edit_item', function() {
        var item_id = $(this).data('item_id');
        var req_recid = $(this).data('req_recid');
        var description = $(this).data('description');
        var department_code = $(this).data('department_code');
        var unit = $(this).data('unit');
        var quantity = $(this).data('quantity');
        var vat_item = $(this).data('vat_item');
        var unit_price_usd = $(this).data('unit_price_usd');
        var unit_price_khr = $(this).data('unit_price_khr');
        var budget_code = $(this).data('budget_code');
        var alternative_budget_code = $(this).data('alternative_budget_code');
        var currency = $(this).data('currency');
        var invoice_no = $(this).data('invoice_no');

        $('#update_item_id').val(item_id);
        $('#update_req_recid').val(req_recid);
        $('#item_department_code').val(department_code);
        $('#item_budget_code').val(budget_code);
        $('#item_alternative_budget_code').val(alternative_budget_code);
        $('#item_unit').val(unit);
        $('#invoice_no').val(invoice_no);
        $('#item_qty').val(quantity);
        $('#item_vat').val(vat_item);
        $('#item_description').val(description);
        $('#currency').val(currency);
        if (currency == 'KHR') {
            $('#item_unit_price').val(unit_price_khr);
        } else {
            $('#item_unit_price').val(unit_price_usd);
        }
    });

    $(document).on('click', '.new_item', function() {
        var new_req_recid = $(this).data('req_recid');
        $('#new_req_recid').val(new_req_recid);

        var new_description = $(this).data('new_description');
        $('#new_description').val(new_description);

        var new_currency = $(this).data('currency');
        $('#new_currency').val(new_currency);

        var new_department_code = $(this).data('new_department_code');
        $('#new_department_code').val(new_department_code);

        var new_unit = $(this).data('new_unit');
        $('#new_unit').val(new_unit);

        var new_qty = $(this).data('new_qty');
        $('#new_qty').val(new_qty);

        var new_unit_price = $(this).data('new_unit_price');
        $('#new_unit_price').val(new_unit_price);

        var new_budget_code = $(this).data('new_budget_code');
        ('#new_budget_code').val(new_budget_code);

        var new_alternative_budget_code = $(this).data('new_alternative_budget_code');
        $('#new_alternative_budget_code').val(new_alternative_budget_code);
    });
</script>
<script>
    $('.btnRemoveDocument').on('click', function() {
        var id_remove = $(this).data('id_attach');
        $('table#table_attach tr#' + id_remove).remove();
        $('#att_remove1').append(',' + id_remove);
        $('#att_remove').val($('#att_remove1').text());
    });

    $('.addRowBtn').on('click', function() {
        $('#add_more').show();
    });
</script>

<script>
    $(document).ready(function() {
        $('#sagement_error').hide();
    });
</script>
<script>
    $('#currency').on('change', function() {
        var ccy = $(this).val()
        // alert(ccy)
        if (ccy == 'KHR') {
            $('.khr').show();
            $('.usd').hide();
        } else {
            $('.khr').hide();
            $('.usd').show();
        }
    });
</script>

<script>
    $('#submit_save').on('click', function() {
        $(".se-pre-con").fadeOut("slow");
    });
    $("input.vat_item").keypress(function(event) {
        return /^(\d)*(\.)?([0-9]{1})?$/.test(String.fromCharCode(event.keyCode));
    });
</script>


<script>
    $(document).ready(function() {

        $("input.numbers").keypress(function(event) {
            return /\d/.test(String.fromCharCode(event.keyCode));
        });

        $(".product").each(function() {
            $(this).keyup(function() {
                var sum = 0;
                $(".product").each(function() {
                    if (!isNaN(this.value) && this.value.length != 0) {
                        sum += parseFloat(this.value);
                    }
                    $(".product_grand_total").val(sum);

                    if (sum != 100) {
                        $('#product_error').show();
                        $('#product_error').text('Sum of value Product and Sagment must be 100');
                        if(sum > 100){
                            $('.product').val(0)
                            $(".product_grand_total").val('100')
                        }
                    }else{
                        $('#product_error').hide();
                        $('#product_error').text('');
                    }
                });
            });
        });

        $(".sagement").each(function() {
            $(this).keyup(function() {
                var sum = 0;
                $(".sagement").each(function() {
                    if (!isNaN(this.value) && this.value.length != 0) {
                        sum += parseFloat(this.value);
                    }
                    $(".grand_total").val(100 - sum)

                });
                $("#sum").html(sum.toFixed(2));
                if ($("#sum").text() > 100) {
                    $('#btn_alert').click();
                    $('.sagement').val(0)
                    $(".grand_total").val('100')
                }
            });
        });
    });
</script>

<script src="{{ URL::to('static/clone/patuta.min.js') }}"></script>
<script>
    $('body').patuta();
</script>
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
<!-- alert blink text -->
<script>
    function blink_text() {
        $('#sagement_error').fadeOut(700);
        $('#sagement_error').fadeIn(700);

        $('#product_error').fadeOut(700);
        $('#product_error').fadeIn(700);

    }

    setInterval(blink_text, 1000);

    function go_home() {
        window.location.href = "{{ route('payment_request.list_auth') }}";
    }
</script>

<script>
     $("input.vat_item").keypress(function(event) {
        return /^(\d)*(\.)?([0-9]{1})?$/.test(String.fromCharCode(event.keyCode));
    });
    $(document).on("keyup", ".counter", function() {
        var sum = 0;
        $(".counter").each(function() {
            sum += +$(this).val();
        });
        $(".total_segment_percentage").val(sum);
    });
</script>
<script>
    $('#frm_add_new_item').validate({
        rules: {
            new_department_code: {required: true,},
            new_budget_code: {required: true,},
            new_qty: {required: true,},
            new_unit_price: {required: true,},
        },
        messages: {
            new_department_code:"Please input mandatory field",
            new_budget_code: "Please input mandatory field",
            new_qty:"Please input mandatory field",
            new_unit_price:"Please input mandatory field",
        },
        errorPlacement: function(error, element) {
            if(
                element.attr("name") == "new_department_code" ||
                element.attr("name") == "new_budget_code" ||
                element.attr("name") == "new_qty" ||
                element.attr("name") == "new_unit_price"
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

    $("#frmUpdateRequest").validate({
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        rules: {
            'category[]': {required: true, },
            department: {required: true,},
            request_date: {required: true,},
            currency: {required: true,},
            due_date: {required: true,},
            subject: {required: true,},

            //===== Paid to =====
            bank_name: {required: true,},
            account_name: {required: true,},
            account_number: { required: true,},
            bank_address: {required: true, },

            //===== For =====
            company: {required: true, },
            id_no: {required: true, },
            contact_no: {required: true, },
            address: {required: true, },
            approver: {required: true, },
        },


        messages: {
            'category[]':"Please input mandatory field",
            department: "Please input mandatory field",
            request_date:"Please input mandatory field",
            currency:"Please input mandatory field",
            due_date:"Please input mandatory field",

            //==== subject ====
            subject: "Please input mandatory field",

            //===== Paid to =====
            bank_name: "Please input mandatory field",
            account_name: "Please input mandatory field",
            account_number: "Please input mandatory field",
            bank_address: "Please input mandatory field",

            //===== For =====
            company: "Please input mandatory field",
            id_no: "Please input mandatory field",
            contact_no: "Please input mandatory field",
            address: "Please input mandatory field",
            approver: "Please select approver",
        },

        errorPlacement: function(error, element) {
          

            //=== category ===
            if( element.attr("name") == "subject"){
                $('#validate_subject_pannel').empty();
                error.appendTo('#validate_subject_pannel');
            }
            //=== validate advance pannel ===
            if(
                element.attr("name") == "department" || 
                element.attr("name") == "request_date" ||
                element.attr("name") == "category[]" 
                ){
                $('#validate_advance_pannel').empty();
                error.appendTo('#validate_advance_pannel');
            }

            //==== validate paid To =====
            if(
                element.attr("name") == "bank_name" ||
                element.attr("name") == "account_name" ||
                element.attr("name") == "account_number" ||
                element.attr("name") == "bank_address"
                ){
                $('#validate_paid_to_pannel').empty();
                error.appendTo('#validate_paid_to_pannel');
            }

            //===== For =====
            if(
                element.attr("name") == "company" ||
                element.attr("name") == "id_no" ||
                element.attr("name") == "contact_no" ||
                element.attr("name") == "address"
                ){
                $('#validate_for_pannel').empty();
                error.appendTo('#validate_for_pannel');
            }

            if(element.attr("name") == "approver"){
                $('#validate_for_approval_pannel').empty();
                error.appendTo('#validate_for_approval_pannel');
            }
        },
        submitHandler: function(form) {

            $totalSegment = calculateTotalSegment();
            
            if($totalSegment != 100){
                $('#sagement_error').show();
                $('#sagement_error').text('Sorry! sum of value in segment must be qual 100%');
            }else{
                $('#sagement_error').hide();
                $('#sagement_error').text('');
            }
            
            $totalProduct = calculateTotalProduct();
            if($totalProduct != 100){
                $('#product_error').show();
                $('#product_error').text('Sorry! sum of value in product must be qual 100%');
            }else{
                $('#product_error').hide();
                $('#product_error').text('');
            }

            if($totalSegment == 100 && $totalProduct == 100)
            {
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
        }

    });

    function calculateTotalProduct()
    {
        $general = $('#product_general').val();
        $loanGeneral = $('#product_loan_general').val();
        $mortgage = $('#product_mortgage').val();
        $business = $('#product_business').val();
        $personal = $('#product_personal').val();
        $cardGeneral = $('#product_card_general').val();
        $debitCard = $('#product_debit_card').val();
        $creditCard = $('#product_credit_card').val();
        $tradGeneral = $('#product_trade_general').val();
        $bankGurantee = $('#product_bank_guarantee').val();
        $letterOfCredit = $('#product_letter_of_credit').val();
        $depositGeneral = $('#product_deposit_general').val();
        $casaIndividual = $('#product_casa_individual').val();
        $tdIndividual = $('#product_td_individual').val();
        $casaCorporate = $('#product_casa_corporate').val();
        $tdCorprate = $('#product_td_corporate').val();

        $totalGeneral = $general ? $general : 0;
        $totalLoanGeneral = $loanGeneral ? $loanGeneral : 0;
        $totalMortgage = $mortgage ? $mortgage : 0;
        $totalBusiness = $business ? $business : 0;
        $totalPersonal = $personal ? $personal:0;
        $totalCardGeneral = $cardGeneral ? $cardGeneral : 0;
        $totalDebitCard = $debitCard ? $debitCard : 0;
        $totalCreditCard = $creditCard ? $creditCard : 0;
        $totalTradGeneral = $tradGeneral ? $tradGeneral : 0;
        $totalBankGurantee = $bankGurantee ? $bankGurantee : 0;
        $totalLetterOfCredit = $letterOfCredit ? $letterOfCredit :0;
        $totalDepositGeneral = $depositGeneral ? $depositGeneral : 0;
        $totalCasaIndividual = $casaIndividual ? $casaIndividual : 0;
        $totalTDIndividual = $tdIndividual ? $tdIndividual : 0;
        $totalCasaCorporate = $casaCorporate ? $casaCorporate : 0;
        $totalTDCorporate = $tdCorprate ? $tdCorprate : 0;

        $grandTotal = 
        parseFloat($totalGeneral)+
        parseFloat($totalLoanGeneral)+
        parseFloat($totalMortgage)+
        parseFloat($totalBusiness)+
        parseFloat($totalPersonal)+
        parseFloat($totalCardGeneral)+
        parseFloat($totalDebitCard)+
        parseFloat($totalCreditCard)+
        parseFloat($totalTradGeneral)+
        parseFloat($totalBankGurantee)+
        parseFloat($totalLetterOfCredit)+
        parseFloat($totalDepositGeneral)+
        parseFloat($totalCasaIndividual)+
        parseFloat($totalTDIndividual)+
        parseFloat($totalCasaCorporate)+
        parseFloat($totalTDCorporate);

        return $grandTotal;
    }

    function calculateTotalSegment() 
    {
        $general = $('#segment_general').val();
        $bfs = $('#segment_bfs').val();
        $rfs = $('#segment_rfs').val();
        $pb = $('#segment_pb').val();
        $pcp = $('#segment_pcp').val();
        $afs = $('#segment_afs').val();

        $totalGeneral = $general ? $general : 0;
        $totalBFS = $bfs ? $bfs : 0;
        $totalRFS = $rfs ? $rfs : 0;
        $totalPB = $pb ? $pb : 0;
        $totalPCP = $pcp ? $pcp : 0;
        $totalAFS = $afs ? $afs : 0;

        $grandTotalAmount = parseFloat($totalGeneral) + parseFloat($totalBFS) + parseFloat($totalRFS) + parseFloat($totalPB) + parseFloat($totalPCP) + parseFloat($totalAFS);
        return $grandTotalAmount;

    }
</script>
{{-- multiple reviewer --}}
<script>
    let reviewerCount = 0;
    const maxReviewers = 3;

    const users = @json($multiReviewer); // Assuming all reviewers use the same user list

    const reviewerTemplate = (index) => {
        let options = '<option selected disabled value="">Select One</option>';
        for (let user of users) {
            options += `<option value="${user.email}/${user.role_id}">
                            ${user.firstname} ${user.lastname}
                        </option>`;
        }

        return `
        <div class="form-group row additional-reviewer">
            <div class="col-sm-3">
                <div>
                    <span style="color: red;">*</span>
                    <span>${ordinalLabel(index + 2)} Reviewer</span>
                    <span style="float: right;">:</span>
                </div>
            </div>
            <div class="col-sm-8">
                <select class="js-example-basic-single reviewer-select form-control" name="${ordinalLabel(index + 2)}_reviewer" id="reviewer_${index + 1}" required>
                    ${options}
                </select>
            </div>
            <div class="col-sm-1">
                <i class="fa fa-times remove-reviewer" style="font-size: 20px;color: red;cursor:pointer;"></i>
            </div>
        </div>`;
    }

    $('#add_reviewer').on('click', function () {
        const firstReviewer = $('#first_reviewer').val();

        // Validate First Reviewer
        if (!firstReviewer) {
            alert("Please select the first reviewer before adding additional reviewers.");
            return;
        }

        // Validate existing additional reviewers in order
        for (let i = 1; i <= reviewerCount; i++) {
            const val = $(`#reviewer_${i}`).val();
            if (!val) {
                alert(`Please select Additional ${ordinalLabel(i+1)} Reviewer before adding another.`);
                return;
            }
        }

        // Check for duplicates
        const selectedValues = getAllReviewerValues();
        if (hasDuplicates(selectedValues)) {
            alert("Duplicate reviewers are not allowed. Please choose different users.");
            return;
        }

        // Add new reviewer if limit not exceeded
        if (reviewerCount < maxReviewers) {
            $('#additional-reviewers-container').append(reviewerTemplate(reviewerCount));
            reviewerCount++;

            // Reinitialize select2
            $('.js-example-basic-single').select2();
        } else {
            alert("You can only add up to 3 additional reviewers.");
        }
    });

    // Remove reviewer
    $(document).on('click', '.remove-reviewer', function () {
        $(this).closest('.additional-reviewer').remove();
        reviewerCount--;

        // Re-label and re-index all remaining reviewers
        $('.additional-reviewer').each(function (index) {
            const reviewerIndex = index + 2; // 2 = Second, 3 = Third, 4 = Fourth

            // Update label
            $(this).find('span:contains("Reviewer")').first().text(`${ordinalLabel(reviewerIndex)} Reviewer`);

            // Update select name and id
            const select = $(this).find('select');
            select.attr('name', `${ordinalLabel(index + 1)}_reviewer`);
            select.attr('id', `reviewer_${index + 1}`);
        });
    });


    // Prevent duplicates on any change
    $(document).on('change', '.js-example-basic-single, #first_reviewer', function () {
        const selectedValues = getAllReviewerValues();
        if (hasDuplicates(selectedValues)) {
            alert("Duplicate reviewer selected! Please choose different users.");
            $(this).val('').trigger('change');
        }
    });

    // Helpers
    function getAllReviewerValues() {
        let values = [];

        const first = $('#first_reviewer').val();
        if (first) values.push(first);

        $('.reviewer-select').each(function () {
            const val = $(this).val();
            if (val) values.push(val);
        });

        return values;
    }

    function hasDuplicates(array) {
        return new Set(array).size !== array.length;
    }
    function ordinalLabel(n) {
        switch(n) {
            case 2: return 'second';
            case 3: return 'third';
            case 4: return 'fourth';
            default: return `#${n}`;
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById("due_date");

        // Get today's date in YYYY-MM-DD format
        const today = new Date().toISOString().split('T')[0];

        // Set min (optional, but useful)
        input.min = today;

        input.addEventListener("input", function () {
            const selectedDate = new Date(this.value);
            const todayDate = new Date(today);

            if (selectedDate < todayDate) {
            alert("Date cannot be in the past. Resetting to today.");
            this.value = today;

            // If using jQuery validation, trigger re-check:
            if (typeof $ !== 'undefined' && $.fn.valid) {
                $(this).valid();
            }
            }
        });
    });
</script>
{{-- End for script multiple reviewer --}}
@endsection