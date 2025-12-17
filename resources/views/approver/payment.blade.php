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

    .removeRowBtn {
        cursor: pointer;
    }

    .addRowBtn {
        cursor: pointer;
    }
    .table td,
    .table th {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 0px solid #e9ecef;
    }

</style>
@section('menu')
    @include('siderbar.payment')
@endsection
@section('breadcrumb')
    @include('breadcrumb.payment')
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/payment/save') }}" enctype="multipart/form-data" id="approve_form">
            @csrf

            <!-- Page-body start -->
            <div class="page-body">
                <!-- DOM/Jquery table start -->
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Payment Request
                        </h4>
                        <span id="top_error"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Procurement Ref. :</label>
                                    </div>
                                    <div class="col-sm-9">
                                        @if (!empty($top_mid['ref']))
                                        <div class="form-control bg-light">
                                            @foreach($rp_ref_no_pr as $data)
                                                <a class="" href="{{$data['href']}}">{{$data['value']}}</a>
                                            @endforeach
                                        </div>
                                        @else
                                            <input type="text" name="pr_ref" id="pr_ref" class="form-control" value="N/A"
                                                readonly="">
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Department :</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $top['req_branch'] }}"
                                            readonly="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Request Date:</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="requestDate" name="requestDate"
                                            value="{{ $top_mid['req_date'] }}" readonly="">
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="">Due Date :</label>
                                    </div>
                                    <div class="col-sm-4">

                                        @if (!empty($top['due_expect_date']) and $top['due_expect_date'] != 'N/A')
                                            <input type="date" class="form-control" id="expDate" name="expDate"
                                                value="{{ \Carbon\Carbon::parse($top['due_expect_date'])->format('Y-m-d') }}">
                                        @else
                                            <input type="text" class="form-control" id="expDate" name="expDate"
                                                value="N/A">
                                        @endif


                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Currency:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="" value="{{ $top['ccy'] }}"
                                            id="currency" name="currency" readonly="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mobile-inputs">
                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <label for="">Subject:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" placeholder="Subject" id="subject"
                                                    name="subject" rows="2">{{ $top['subject'] }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Type :</label>
                                    </div>
                                    @if ($top_mid['type'] == 'Payment')
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type" name="type[]"
                                                    value="Payment" checked="">
                                                <label class="border-checkbox-label" for="type">Payment</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type1"
                                                    name="type[]" value="Deposit">
                                                <label class="border-checkbox-label" for="type1">Deposit</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type" name="type[]"
                                                    value="Payment">
                                                <label class="border-checkbox-label" for="type">Payment</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type1"
                                                    name="type[]" value="Deposit" checked="">
                                                <label class="border-checkbox-label" for="type1">Deposit</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Category :</label>
                                    </div>
                                    @if ($top_mid['category'] == 'Ordinary')
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" id="category"
                                                    name="category[]" checked="" value="Ordinary">
                                                <label class="border-checkbox-label" for="category">Ordinary</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" id="category"
                                                    name="category[]" value="Ordinary">
                                                <label class="border-checkbox-label" for="category">Ordinary</label>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($top_mid['category'] == 'Event/Project')
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" id="category1"
                                                    name="category[]" checked="" value="Event/Project">
                                                <label class="border-checkbox-label" for="category1">Event/Project</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" id="category1"
                                                    name="category[]" value="Event/Project">
                                                <label class="border-checkbox-label" for="category1">Event/Project</label>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($top_mid['category'] == 'Staff Benefit')
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" id="category2"
                                                    name="category[]" checked="" value="Staff Benefit">
                                                <label class="border-checkbox-label" for="category2">Staff Benefit</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" id="category2"
                                                    name="category[]" value="Staff Benefit">
                                                <label class="border-checkbox-label" for="category2">Staff Benefit</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Paid to:</h4>
                        <span id="paid_to"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Account Name:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="account_name" name="account_name"
                                                value="{{ $top_mid['account_name'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Account Number:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="account_number"
                                                name="account_number" value="{{ $top_mid['account_number'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Bank Name:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="bank_name" name="bank_name"
                                                value="{{ $top_mid['bank_name'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Bank Address:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="2" id="bank_address"
                                                name="bank_address">{{ $top_mid['bank_address'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Swift Code:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="swift_code" name="swift_code"
                                                value="{{ $top_mid['swift_code'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Tel:</label>
                                        </div>
                                        <div class="col-sm-9">


                                            @if (!empty($top_mid['tel']))
                                                <input type="text" id="tel" name="tel" class="form-control numbers"
                                                    value="{{ $top_mid['tel'] }}">
                                            @else
                                                <input type="text" id="tel" name="tel" class="form-control " value="N/A">
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">For:</h4>
                        <span id="for"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Mr./Ms./Company:</label>
                                        </div>
                                        <div class="col-sm-9 ">
                                            <input type="text" id="for_who" name="for_who" class="form-control"
                                                value="{{ $top_mid['company'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">ID No.:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="id_no" name="id_no" class="form-control"
                                                value="{{ $top_mid['id_no'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Contact No:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="contact_no" name="contact_no"
                                                class="form-control numbers" value="{{ $top_mid['contact_no'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Address:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="2" id="address_who"
                                                name="address_who">{{ $top_mid['address'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">PAYMENT REQUEST Detail</h4>
                        <span id="tbl_procurement_error"></span>
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->
                                @if (empty($top_mid['ref']) || $top_mid['ref'] == 'N/A')
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#procurementbody-Modal" style="float: right"
                                        id="btn_add_new">Add</button>
                                @endif

                                <table class="table table-striped table-bordered" id="example-1"
                                    style="margin-top: 50px !important; font-size: 12.5px;">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="vertical-align:middle;">No</th>
                                            <th style="display: none;">Procurement Ref</th>
                                            <th rowspan="2" style="vertical-align:middle;">Inv.No</th>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($body) > 0)
                                            <?php
                                            $total = 0;
                                            $total_khr = 0;
                                            ?>
                                            @foreach ($body as $key => $value)
                                                <tr>
                                                    <td>
                                                        {{ $key + 1 }}
                                                    </td>
                                                    <td style="display: none">
                                                        {{ $value->pr_col_id }}
                                                        <input type="text" name="pr_col_id[]"
                                                            value="{{ $value->pr_col_id }}">
                                                    </td>
                                                    <td>

                                                        {{ $value->inv_no }}

                                                    </td>
                                                    <td>

                                                        {{ $value->description }}

                                                    </td>
                                                    <td>

                                                        {{ $value->br_dep_code }}

                                                    </td>
                                                    <td>

                                                        {{ $value->budget_code }}

                                                    </td>

                                                    <td>
                                                        @if ($value->alternativebudget_code > 0)
                                                            {{ $value->alternativebudget_code }}
                                                        @else
                                                            N/A
                                                        @endif

                                                    </td>
                                                    <td>
                                                        {{ $value->unit }}
                                                    </td>
                                                    <td>
                                                        {{ $value->qty }}
                                                    </td>
                                                    <td>
                                                        @if ($top['ccy'] == 'KHR')
                                                            <span style="font-size: 18px;">៛</span>
                                                            @money($value['unit_price_khr'])
                                                        @else
                                                            $@money($value['unit_price'])
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($top['ccy'] == 'KHR')
                                                            <span style="font-size: 18px;">៛</span>
                                                            @money($value['vat_item_khr'])
                                                        @else
                                                            $@money($value['vat_item'])
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($top['ccy'] == 'KHR')
                                                            <span style="font-size: 18px;">៛</span>
                                                            @money($value['total_khr'])
                                                        @else
                                                            $@money($value['total'])
                                                        @endif
                                                    </td>
                                                    <td>
                                                        $@money($value->ytd_expense)
                                                    </td>
                                                    <td>
                                                        $@money($value->total_budget)
                                                    </td>
                                                    <td>
                                                        @if ($value->within_budget_code == 'Y')
                                                            <span
                                                                style="color:green;font-size: 14px;font-weight: bold;">YES</span>
                                                        @else
                                                            <span
                                                                style="color:red;font-size: 14px;font-weight: bold;">NO</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="edit_item" data-toggle="modal"
                                                            data-target="#procurementbody-Modal"
                                                            data-inv_no="{{ $value->inv_no }}"
                                                            data-req_recid="{{ $value->req_recid }}"
                                                            data-req_description="{{ $value->description }}"
                                                            data-department="{{ $value->br_dep_code }}"
                                                            data-budgetcode="{{ $value->budget_code }}"
                                                            data-alternativebudget="{{ $value->alternativebudget_code }}"
                                                            data-unit="{{ $value->unit }}"
                                                            data-qty="{{ $value->qty }}"
                                                            data-vat_item="{{ $value->vat_item }}"
                                                            data-vat_item="{{ $value->vat_item_khr }}"
                                                            data-unit_price="{{ $value->unit_price }}"
                                                            data-unit_price_khr="{{ $value->unit_price_khr }}"
                                                            data-total_estimate="{{ $value->total }}"
                                                            data-withinbudget="{{ $value->within_budget_code }}"
                                                            data-bodyid="{{ $value->id }}"
                                                            data-within="{{ $value->within_budget_code }}"
                                                            data-pr_col_id="{{ $value->pr_col_id }}"
                                                            data-budget_his="{{ $budget_his->id + $key }}">
                                                            <i class="fa fa-edit"
                                                                style="font-size: 20px;color: #0ac282;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php if ($top['ccy'] == 'KHR') {
                                                    $total_khr += $value->total_khr;
                                                } else {
                                                    $total += $value->total;
                                                } ?>
                                            @endforeach

                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    SUB TOTAL
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_khr)
                                                        <input type="hidden" value="{{$total_khr}}" id="sum">
                                                    @else
                                                        $@money($total)
                                                        <input type="hidden" value="{{$total}}" id="sum">
                                                    @endif

                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    DISCOUNT
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                    <input type="text" class="form-control"
                                                            value="@money($body_bottom->discount_khr)" name="discount" id="discount">
                                                    @else
                                                    <input type="text" class="form-control"
                                                            value="@money($body_bottom->discount)" name="discount" id="discount">
                                                    @endif
                                                    @if ($top['ccy'] == 'KHR')
                                                        <input type="hidden" class="form-control"
                                                            value="@money($body_bottom->vat_khr)" name="vat_change" id="vat">

                                                    @else
                                                        <input type="hidden" class="form-control"
                                                            value="@money($body_bottom->vat)" name="vat_change" id="vat">

                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    WHT
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                        <input type="text" class="form-control"
                                                            value="@money($body_bottom->wht_khr)" id="wht_change" name="wht_change">
                                                    @else
                                                        <input type="text" class="form-control"
                                                            value="@money($body_bottom->wht)" id="wht_change" name="wht_change">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    DEPOSIT
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                    <input type="text" class="form-control"
                                                            value="@money($body_bottom->deposit_khr)" name="deposit" id="deposit">
                                                    @else
                                                    <input type="text" class="form-control"
                                                            value="@money($body_bottom->deposit)" name="deposit" id="deposit">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    NET PAYABLE
                                                </td>
                                                <td>

                                                    @if ($top['ccy'] == 'KHR')
                                                        <?php $net_payable = $total_khr - $body_bottom->discount_khr + $body_bottom->vat_khr - $body_bottom->wht_khr - $body_bottom->deposit_khr; ?>
                                                        <span style="font-size: 18px;">៛</span>
                                                        <span id='net_payable'>@money($net_payable)</span>
                                                        
                                                    @else
                                                        <?php $net_payable = $total - $body_bottom->discount + $body_bottom->vat - $body_bottom->wht - $body_bottom->deposit; ?>
                                                        <span id='net_payable'>$@money($net_payable)</span>
                                                        
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
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
                                <div class="col-sm-2 mobile-inputs">
                                    <label for="">Additional Remarks (if any):</label>
                                </div>
                                <div class="col-sm-10 mobile-inputs">
                                    <textarea name="remarkable"
                                        class="form-control ">{{ $top_mid['remarkable'] }}</textarea>
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
                                            <th>General</th>
                                            <th colspan="4" class="text-center">Loan</th>
                                            <th colspan="3" class="text-center">Card</th>
                                            <th colspan="3" class="text-center">Trade</th>
                                            <th colspan="5" class="text-center">Deposit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left;" class="table-success" rowspan="2"><b>Type :</b>
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
                                            <td style="text-align: left;" class="table-success"><b>Code :</b></td>
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
                                            <td style="text-align: left;" class="table-success"><b>Allocated % :</b></td>
                                            <td align="center" class="content" data-value="general">
                                                <input type="hidden" id="total-product" value="100">
                                                <div id="general-div" style="width: 70px">
                                                    {{ $bottom['general'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="general"
                                                    id="general-text" onpaste="return false;"
                                                    value="{{ $bottom['general'] ?? '0' }}">

                                            </td>
                                            <td align="center" class="content" data-value="loan_general">
                                                <div id="loan_general-div" style="width: 70px">
                                                    {{ $bottom['loan_general'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="loan_general"
                                                    id="loan_general-text" onpaste="return false;"
                                                    value="{{ $bottom['loan_general'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="mortgage">
                                                <div id="mortgage-div" style="width: 70px">
                                                    {{ $bottom['mortage'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="mortgage"
                                                    id="mortgage-text" onpaste="return false;"
                                                    value="{{ $bottom['mortage'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="business">
                                                <div id="business-div" style="width: 70px">
                                                    {{ $bottom['busines'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="business"
                                                    id="business-text" onpaste="return false;"
                                                    value="{{ $bottom['busines'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="personal">
                                                <div id="personal-div" style="width: 70px">
                                                    {{ $bottom['personal'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="personal"
                                                    id="personal-text" onpaste="return false;"
                                                    value="{{ $bottom['personal'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="card_general">
                                                <div id="card_general-div" style="width: 70px">
                                                    {{ $bottom['card_general'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="card_general"
                                                    id="card_general-text" onpaste="return false;"
                                                    value="{{ $bottom['card_general'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="debit_card">
                                                <div id="debit_card-div" style="width: 70px">
                                                    {{ $bottom['debit_card'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="debit_card"
                                                    id="debit_card-text" onpaste="return false;"
                                                    value="{{ $bottom['debit_card'] ?? '0' }}">
                                            </td>

                                            <td align="center" class="content" data-value="credit_card">
                                                <div id="credit_card-div" style="width: 70px">
                                                    {{ $bottom['credit_card'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="credit_card"
                                                    id="credit_card-text" onpaste="return false;"
                                                    value="{{ $bottom['credit_card'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="trade_general">
                                                <div id="trade_general-div" style="width: 70px">
                                                    {{ $bottom['trade_general'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="trade_general"
                                                    id="trade_general-text" onpaste="return false;"
                                                    value="{{ $bottom['trade_general'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="bank_guarantee">
                                                <div id="bank_guarantee-div" style="width: 70px">
                                                    {{ $bottom['bank_guarantee'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="bank_guarantee"
                                                    id="bank_guarantee-text" onpaste="return false;"
                                                    value="{{ $bottom['bank_guarantee'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="letter_of_credit">
                                                <div id="letter_of_credit-div" style="width: 70px">
                                                    {{ $bottom['letter_of_credit'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="letter_of_credit"
                                                    id="letter_of_credit-text" onpaste="return false;"
                                                    value="{{ $bottom['letter_of_credit'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="deposit_general">
                                                <div id="deposit_general-div" style="width: 70px">
                                                    {{ $bottom['deposit_general'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="deposit_general"
                                                    id="deposit_general-text" onpaste="return false;"
                                                    value="{{ $bottom['deposit_general'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="casa_individual">
                                                <div id="casa_individual-div" style="width: 70px">
                                                    {{ $bottom['casa_individual'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="casa_individual"
                                                    id="casa_individual-text" onpaste="return false;"
                                                    value="{{ $bottom['casa_individual'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="td_individual">
                                                <div id="td_individual-div" style="width: 70px">
                                                    {{ $bottom['td_individual'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="td_individual"
                                                    id="td_individual-text" onpaste="return false;"
                                                    value="{{ $bottom['td_individual'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="casa_corporate">
                                                <div id="casa_corporate-div" style="width: 70px">
                                                    {{ $bottom['casa_corporate'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="casa_corporate"
                                                    id="casa_corporate-text" onpaste="return false;"
                                                    value="{{ $bottom['casa_corporate'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="td_corporate">
                                                <div id="td_corporate-div" style="width: 70px">
                                                    {{ $bottom['td_corporate'] ?? '0' }}%</div>
                                                <input class="form-control numbers txt product"
                                                    style="width: 50px !important;" type="hidden" name="td_corporate"
                                                    id="td_corporate-text" onpaste="return false;"
                                                    value="{{ $bottom['td_corporate'] ?? '0' }}">
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
                                <span id="segment_error" style="color: red;font-size: 15px; font-weight: bold;"></span>
                                <table class="text-center table table-striped table-bordered" id="segment_input"
                                    style="font-size: 12.5px">
                                    <thead>
                                        <tr class="table-info">
                                            <th>Segment</th>
                                            <th colspan="6">Input Allocated % (in total equal 100%)</th>
                                        </tr>
                                        <tr class="table-success">
                                            <th style="text-align: left;">Categories:</th>
                                            <th style="text-align: center;">General</th>
                                            <th class="text-center">BFS</th>
                                            <th class="text-center">RFS (ex PB)</th>
                                            <th class="text-center">PB</th>
                                            <th class="text-center">PCP</th>
                                            <th class="text-center">AFS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left;"><b>Code :</b></td>
                                            <td>999</td>
                                            <td>100</td>
                                            <td>200</td>
                                            <td>300</td>
                                            <td>400</td>
                                            <td>500</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;" class="table-success"><b>Allocated % :</b></td>
                                            <td align="center"  class="content" data-value="sagement_general">
                                                <input type="hidden" id="total-segment" value="100">
                                                <div id="sagement_general-div" style="width: 70px">
                                                    {{ $bottom['sagement_general'] ?? '0' }}%</div>
                                                <input class="form-control numbers counter-segment" type="hidden"
                                                    style="width: 150px !important;" name="general_segment"
                                                    id="sagement_general-text" onpaste="return false;" value="{{ $bottom['sagement_general'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="sagement_bfs">
                                                <div id="sagement_bfs-div" style="width: 70px">
                                                    {{ $bottom['sagement_bfs'] ?? '0' }}%</div>
                                                <input class="form-control numbers counter-segment" type="hidden"
                                                    style="width: 150px !important;" name="bfs" id="sagement_bfs-text"
                                                    onpaste="return false;" value="{{ $bottom['sagement_bfs'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="sagement_rfs">
                                                <div id="sagement_rfs-div" style="width: 70px">
                                                    {{ $bottom['sagement_rfs'] ?? '0' }}%</div>
                                                <input class="form-control numbers counter-segment" type="hidden"
                                                    style="width: 150px !important;" name="rfs" id="sagement_rfs-text"
                                                    onpaste="return false;" value="{{ $bottom['sagement_rfs'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="sagement_pb">
                                                <div id="sagement_pb-div" style="width: 70px">
                                                    {{ $bottom['sagement_pb'] ?? '0' }}%</div>
                                                <input class="form-control numbers counter-segment" type="hidden"
                                                    style="width: 150px !important;" name="pb" id="sagement_pb-text"
                                                    onpaste="return false;" value="{{ $bottom['sagement_pb'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="sagement_pcp">
                                                <div id="sagement_pcp-div" style="width: 70px">
                                                    {{ $bottom['sagement_pcp'] ?? '0' }}%</div>
                                                <input class="form-control numbers counter-segment" type="hidden"
                                                    style="width: 150px !important;" name="pcp" id="sagement_pcp-text"
                                                    onpaste="return false;" value="{{ $bottom['sagement_pcp'] ?? '0' }}">
                                            </td>
                                            <td align="center" class="content" data-value="sagement_afs">
                                                <div id="sagement_afs-div" style="width: 70px">
                                                    {{ $bottom['sagement_afs'] ?? '0' }}%</div>
                                                <input class="form-control numbers counter-segment" type="hidden"
                                                    style="width: 150px !important;" name="afs" id="sagement_afs-text"
                                                    onpaste="return false;" value="{{ $bottom['sagement_afs'] ?? '0' }}">
                                            </td>
                                           

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row form-group" style="padding-top: 15px;">
                                <div class="col-sm-4 mobile-inputs">
                                    <label for="">Additional Remarks (if any) for product and segment :</label>
                                </div>
                                <div class="col-sm-8 mobile-inputs">
                                    <textarea name="remarks_product_segment"
                                        class="form-control">{{ $bottom['remarks'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <input type="hidden" name="req_recid" value="{{ $top['req_recid'] }}" id="req_recid">
                <input type="hidden" name="req_email" value="{{ $top['req_email'] }}">
                <input type="hidden" name="req_name" value="{{ $top['req_name'] }}">
                <input type="hidden" name="req_department" value="{{ $top['req_branch'] }}">
                <input type="hidden" name="req_position" value="{{ $top['req_position'] }}">

                <div class="card">


                    <ul class="nav nav-tabs  tabs" role="tablist">
                        @if ($query == '1')
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#query" role="tab"
                                    aria-expanded="true">ACTION</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab"
                                    aria-expanded="true">ACTIVITY LOG</a>
                            </li>
                        @endif
                        @if ($requester == '1' or $review == '1' or $approve == '1')
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home1" role="tab"
                                    aria-expanded="true">ACTION</a>
                            </li>


                        @endif
                        @if (!empty($condition_view))
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab"
                                    aria-expanded="true">ACTIVITY LOG</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#profile1" role="tab"
                                aria-expanded="false">REFERENCE DOCUMENT</a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content tabs card-block">
                        <span id="approve_review"></span>
                        @if ($requester == '1')
                            <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td style="text-align: right;width: 200px !important;border: none !important;">
                                                    Reviewer </td>
                                                <td style="border: none !important;">
                                                    <select class="js-example-basic-single slc_review" name="slc_review">
                                                        <option selected="" disabled="" value="">Select One</option>
                                                        <option value="">Skip Reviewer</option>
                                                        @foreach ($group_requester as $value)
                                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                                {{ $value->firstname }} {{ $value->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="border: none !important; width: 5%">
                                                    <i id="add_reviewer" class="fa fa-plus-square" style="font-size: 20px;color: #0ac282"></i>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody id="row_reviewer">
                                        </tbody>
                                    </table>
                                    <table class="table" style="margin-top: -15px;">
                                        @if ($group_approver == 'CFO_ONLY')
                                            <input type="hidden" class="form-control" name="slc_approve" value="{{$ceo->email . '/' . $value->role_id}}">
                                        @elseif($group_approver=='MDOFFICE_ONLY')
                                            <input type="hidden" class="form-control" name="slc_approve" value="{{$ceo->email . '/' . $value->role_id}}">
                                        @else
                                            <tr>
                                                <td
                                                    style="text-align: right;width: 200px !important;border: none !important;">
                                                    Accounting Reviewer
                                                </td>
                                                <td style="border: none !important;">

                                                    <input readonly type="text" class="form-control" name="" 
                                                value="accounting">
                                                </td>
                                                <td style="border: none !important; width: 5%">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="text-align: right;width: 200px !important;border: none !important;">
                                                    <span style="color: red;">*</span>Approver
                                                </td>
                                                <td style="border: none !important;">

                                                    <select class="js-example-basic-single" name="slc_approve">
                                                        <option selected="" disabled="" value="">Select One</option>
                                                        @foreach ($group_approver as $value)
                                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                                {{ $value->firstname }} {{ $value->lastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="border: none !important; width: 5%">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="text-align: right;width: 200px !important;border: none !important;">
                                                    Payment Process
                                                </td>
                                                <td style="border: none !important;">

                                                    <input readonly type="text" class="form-control" name="" 
                                                value="accounting">
                                                </td>
                                                <td style="border: none !important; width: 5%">
                                                </td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td style="text-align: right;width: 200px !important;border: none !important;">Comment</td>
                                            <td style="border: none !important;">
                                                <textarea class="form-control" rows="5" name="comment"
                                                    id="comment"></textarea>
                                            </td>
                                            <td style="border: none !important; width: 5%">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: none !important;">

                                            </td>
                                            <td style="border: none !important;">
                                                <button type="button" name="submit" value="submit" id="btnConfirmPayment"
                                                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                        class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                                    Submit</button>

                                                <button type="submit" name="submit" value="submit" id="submitForm"
                                                    style="padding: 5px 10px 5px 10px;cursor: pointer; display: none;" ><i
                                                        class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                                    Submit</button>
                                                    
                                                <button type="button" name="submit" value="submit" id="delete_btn"
                                                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                        class="fa fa-trash" aria-hidden="true" style="color:red"></i>
                                                    Delete</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        @endif

                        @if (!empty($condition_view))
                            <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td style="border: none !important;width: 80px !important">Comment</td>
                                            <td style="border: none !important;">
                                                <textarea class="form-control" name="comment" rows="5"
                                                    id="comment"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: none !important;">

                                            </td>
                                            <td style="border: none !important;">

                                                <button type="submit" name="submit" id="submit_save" value="submit"
                                                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                        class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                                    Re-Submit</button>
                                                <button type="button" name="submit" id="delete_btn" value="submit"
                                                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                        class="fa fa-trash" aria-hidden="true" style="color: red"></i>
                                                    Delete</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($query == '1')
                            <div class="tab-pane active" id="query" role="tabpanel" aria-expanded="true">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td style="text-align: right;width: 150px !important;border: none !important;">
                                                Comment</td>
                                            <td style="border: none !important;">
                                                <textarea class="form-control" rows="5" name="comment"
                                                    id="comment"></textarea>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="border: none !important;">

                                            </td>
                                            <td style="border: none !important;">


                                                <button type="submit" name="submit" class="btn btn-warning"
                                                    value="query">Query</button>



                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="activitylog" role="tabpanel" aria-expanded="false"
                                style="padding-bottom: 15px;">
                                <div class="timeline">
                                    <div class="timeline-month bg-c-yellow"
                                        style="font-weight: bold;font-size: 16px;color: white;background: #FF7814">
                                        PAYMENT REQUEST
                                    </div>
                                    @foreach ($auditlog as $value)
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
                                                                <i class="fa fa-user"></i> {{ $value->doer_role }}: {{ $value->name }}
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
                        @endif


                        @if (!empty($condition_view))
                            <div class="tab-pane" id="activitylog" role="tabpanel" aria-expanded="false"
                                style="padding-bottom: 15px;">
                                <div class="timeline">
                                    <div class="timeline-month bg-c-yellow"
                                        style="font-weight: bold;font-size: 16px;color: white;background: #FF7814">
                                        PAYMENT REQUEST
                                    </div>
                                    @foreach ($auditlog as $value)
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
                                                                <i class="fa fa-user"></i> {{ $value->doer_role }}: {{ $value->name }}
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

                        @endif
                        
                        <div class="tab-pane" id="profile1" role="tabpanel" aria-expanded="false">
                            @if (count($document) > 0)
                                <div class="col-sm-12" style="text-align: right;padding: 5px 0 15px 0;">
                                    <i class="fa fa-plus-square addRowBtn" style="font-size: 20px;color: #0ac282;"></i>
                                </div>
                            @endif

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



                                @if (count($document) > 0)
                                    @foreach ($document as $value)
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
                                                <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"
                                                    data-id_attach={{ $value['id'] }}></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr id="add_more" style="display: none">
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
                                @else
                                    <tr>
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
                                @endif
                                <input type="hidden" value="" name="att_remove" id="att_remove">
                                <span id="att_remove1" style="display: none"></span>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
    <div class="modal fade" id="modalPaymentYTDPreview" tabindex="-1" role="dialog">
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
                                <table id="tbPaymentPreview" class="table table-striped dt-responsive nowrap">
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
    <div class="modal fade" id="procurementbody-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-gear"></i> Add Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('form/payment/editrow') }}" enctype="multipart/form-data"
                    id="frm_save">
                    <input type="hidden" name="req_recid_edit" id="req_recid_edit">
                    <input type="hidden" name="within_budget_code" id="within_budget_edit">
                    <input type="hidden" name="req_recid_update" id="req_recid_update" value="{{ $top['req_recid'] }}">
                    <input type="hidden" name="pr_id_s" id="pr_id_s">
                    <input type="hidden" name="budget_his_id" id="budget_his_id">
                    @csrf
                    <div class="modal-body">

                        <div class="table-responsive" style="overflow-y: scroll;max-height: 350px;">
                            <table class="table table-hover">
                                <tr>
                                    <td style="text-align: right;width: 10px !important">Invoice No</td>
                                    <td>
                                        <textarea class="form-control" name="invoice_new" id="invoice_dis"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;width: 10px !important">Description</td>
                                    <td>
                                        <textarea class="form-control" name="description_new"
                                            id="description_dis"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Br/Dep Code</td>
                                    <td>
                                        <select name="branchcode" id="branchcode_dis" class="form-control">
                                            @foreach ($dep_code as $value)
                                                <option value="{{ $value->branch_code }}">{{ $value->branch_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align: right;">Budget Code</td>
                                    <td>
                                        <select class="tabledit-input form-control input-sm" id="budget_code_dis"
                                            name="budget_code">
                                            @foreach ($budget_code as $value)
                                                <option value="{{ $value->budget_code }}">{{ $value->budget_code }} {{ $value->budget_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Alternative Budget Code</td>
                                    <td>
                                        <select readonly= "" class="tabledit-input form-control input-sm"
                                            id="alternativebudget_code_dis" name="alternativebudget_code">
                                            <option value="0">No</option>
                                            <!-- @foreach ($alternative_budget_codes as $value)
                                                <option value="{{ $value->budget_code }}">{{ $value->budget_code }}
                                                </option>
                                            @endforeach -->
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Unit</td>
                                    <td>
                                        <input type="text" class="form-control" name="unit" id="unit_dis">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">QTY</td>
                                    <td>
                                        <input type="text" class="form-control numbers" name="qty" id="qty_dis">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Unit Price</td>
                                    <td>
                                        <div class="input-group">
                                            @if ($top['ccy'] == 'USD')
                                                <span class="input-group-addon usd">$</span>
                                            @else
                                                <span class="input-group-addon khr"
                                                    style="font-size: 20px;display: none;">៛</span>
                                            @endif

                                            <input type="number" class="form-control" min="0" step="any"
                                                name="unit_price" id="unit_price_dis">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">VAT</td>
                                    <td>
                                        <input type="text" class="form-control vat_item" name="vat_item" id="vat_item_dis">
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align: right;">Total</td>
                                    <td>
                                        <input type="text" class="form-control" name="total_estimate"
                                            id="total_estimate_dis" readonly="">
                                    </td>
                                </tr>
                                @if (!empty($body_bottom))
                                    @if ($top['ccy'] == 'USD')
                                        <input type="hidden" name="deposit" value="{{ $body_bottom->deposit }}">
                                        <input type="hidden" name="vat" id="vat_modal" value="{{ $body_bottom->vat }}">
                                        <input type="hidden" name="wht" id="wht_modal" value="{{ $body_bottom->wht }}">
                                        <input type="hidden" name="discount" value="{{ $body_bottom->discount }}">
                                    @else
                                        <input type="hidden" name="deposit" value="{{ $body_bottom->deposit_khr }}">
                                        <input type="hidden" name="vat" id="vat_modal"
                                            value="{{ $body_bottom->vat_khr }}">
                                        <input type="hidden" name="wht" id="wht_modal"
                                            value="{{ $body_bottom->wht_khr }}">
                                        <input type="hidden" name="discount" value="{{ $body_bottom->discount_khr }}">
                                    @endif
                                @endif
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success waves-effect" name="submit" value="update"><i
                                class="fa fa-save"></i> Modify</button>
                        <button type="submit" class="btn btn-danger waves-effect" name="submit" value="delete"><i
                                class="fa fa-save"></i> Remove</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <form id="delete_frm" method="post" action="{{ url('form/delete') }}">
        {{ csrf_field() }}
        <input type="hidden" name="param" value="{{ $top['req_recid'] }}">
    </form>
@endsection
@section('script')
    <script>
        $('.removeRowBtn').on('click', function() {
            var id_remove = $(this).data('id_attach');
            $('table#table_attach tr#' + id_remove).remove();
            $('#att_remove1').append(',' + id_remove);
            $('#att_remove').val($('#att_remove1').text());
        });

        $('.addRowBtn').on('click', function() {
            // alert('ok')
            $('#add_more').show();
        });
    </script>
    <script>
        $('#vat_change').on('keyup', function() {
            $('#vat_modal').val($(this).val());
        });
        $('#wht_change').on('keyup', function() {
            $('#wht_modal').val($(this).val());
        });
    </script>
    <script>
        $('#btn_add_new').on('click', function() {
            // alert('ooo');

            $('#frm_save').trigger("reset");
            $('#within_budget_edit').val('');
            $('#pr_id_s').val('');
            $('#budget_his_id').val('');
            $('#req_recid_edit').val('');

        });
    </script>

    <script>
        $('#delete_btn').on('click', function() {
            // alert('hello')
            $("#delete_frm").submit();
        });
    </script>

    <script>
        $(".modal").on("hidden.bs.modal", function() {
            $('#description_dis').val('')
            $('#description_dis').val('')
            $('#description_dis').val('')
            $('#description_dis').val('')
        });
        $("#qty_dis").keyup(function() {
            var currency = $('#currency').val();
            var vat_item_dis = $('#vat_item_dis').val();
            var qty = $(this).val();
            var unit_price = $('#unit_price_dis').val();
            var total = (parseFloat(qty) * parseFloat(unit_price)) + parseFloat(vat_item_dis);
            $('#total_estimate_dis').val('$' + total.toFixed(2))
        });
        $("#unit_price_dis").keyup(function() {
            var vat_item_dis = $('#vat_item_dis').val();
            var unit_price = $(this).val();
            var qty = $('#qty_dis').val();
            var total = (parseFloat(qty) * parseFloat(unit_price))+ parseFloat(vat_item_dis);
            $('#total_estimate_dis').val('$' + total.toFixed(2))
        });
        $("#vat_item_dis").keyup(function() {
            var vat_item_dis = $(this).val();
            var unit_price = $('#unit_price_dis').val();
            var qty = $('#qty_dis').val();
            var total = (parseFloat(qty) * parseFloat(unit_price))+ parseFloat(vat_item_dis);
            $('#total_estimate_dis').val('$' + total.toFixed(2))
        });
    </script>
    <script>
        $(document).on('click', '.edit_item', function() {

            // $('#groupidshow').text('');
            var inv_no = $(this).data('inv_no');
            var description = $(this).data('req_description');
            var recid = $(this).data('req_recid');
            var budget_code = $(this).data('budgetcode');
            var alternativebudget_code = $(this).data('alternativebudget');
            var department = $(this).data('department');
            var unit = $(this).data('unit');
            var qty = $(this).data('qty');
            var unit_price = $(this).data('unit_price');
            var unit_price_khr = $(this).data('unit_price_khr');
            var total_estimate = $(this).data('total_estimate');
            var vat_item = $(this).data('vat_item');
            var delivery_date = $(this).data('delivery_date');
            var withinbudget = $(this).data('withinbudget');
            var bodyid = $(this).data('bodyid');
            var within = $(this).data('within');
            var pr_col_id = $(this).data('pr_col_id');
            var budget_his = $(this).data('budget_his');
            $('#description_dis').val(description);
            $('#invoice_dis').val(inv_no);
            $('#branchcode_dis').val(department);
            $('#budget_code_dis').val(budget_code);
            $('#alternativebudget_code_dis').val(alternativebudget_code);
            $('#unit_dis').val(unit);
            $('#qty_dis').val(qty);
            $('#vat_item_dis').val(vat_item);
            // $('#unit_price_dis').val(unit_price);
            $('#total_estimate_dis').val(total_estimate);
            $('#delivery_date_dis').val(delivery_date);
            $('#req_recid_edit').val(bodyid);
            $('#within_budget_edit').val(within);
            $('#pr_id_s').val(pr_col_id);
            $('#budget_his_id').val(budget_his);
            var ccy = $('#currency').val();
            if (ccy == 'KHR') {
                var vat_item_khr = parseFloat(vat_item) * 4000;
                var total_estimate_khr = parseFloat(total_estimate) *4000;
                $('#unit_price_dis').val(unit_price_khr);
                $('#total_estimate_dis').val(total_estimate_khr);
                $('#vat_item_dis').val(vat_item_khr);
            } else {
                $('#unit_price_dis').val(unit_price);
                // $('#total_estimate_dis').val(total_estimate);
            }
            // alert(description);      
        });
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
    <script>
        $(document).on('click', '.chb_1', function() {
            $('.chb_1').not(this).prop('checked', false);
        });
        $(document).on('click', '.chb_2', function() {
            $('.chb_2').not(this).prop('checked', false);
        });
    </script>
    <script>
        document.querySelector('.alert-confirm1').onclick = function() {
            swal({
                title: "Payment Request",
                text: "Procurement Reference ID",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Write something"
            }, function(inputValue) {
                if (inputValue === false) return false;
                // if (inputValue === false) window.location="{{ route('/') }}";          
                if (inputValue === "") {
                    swal.showInputError("You need to write something!");
                    return false
                }
                var url_redirect = "{{ url('/') }}" + '/form/payment/new/ref/' +
                    "{{ Crypt::encrypt('"+inputValue+"') }}" + '/' + inputValue;
                // alert(url_redirect)
                window.location = url_redirect;
                // swal("Nice!", "You wrote: " + inputValue, "success");
            });
        };
    </script>
    <script>
        $(document).ready(function() {
            $("input.numbers").keypress(function(event) {
                return /\d/.test(String.fromCharCode(event.keyCode));
            });
            //iterate through each textboxes and add keyup
            //handler to trigger sum event
            $(".product").each(function() {
                $(this).keyup(function() {
                    calculateSumProduct();
                });
            });
            $(".counter-segment").each(function() {
                $(this).keyup(function() {
                    calculateSumSegment();
                });
            });
            $("input.vat_item").keypress(function(event) {
                return /^(\d)*(\.)?([0-9]{1})?$/.test(String.fromCharCode(event.keyCode));
            });
        }); 

        function calculateSumProduct() {
            var sum = 0;
            $(".product").each(function() {
                if (!isNaN(this.value) && this.value.length != 0) {
                    sum += parseFloat(this.value);
                }
                $("#total-product").val(sum);
                if (sum != 100) {
                    $('#product_error').show();
                    $('#product_error').text('Sum of value Product and Sagment must be 100');
                }else{
                    $('#product_error').hide();
                    $('#product_error').text('');
                }
            });
        }
        function calculateSumSegment() {
            var sum = 0;
            $(".counter-segment").each(function() {
                if (!isNaN(this.value) && this.value.length != 0) {
                    sum += parseFloat(this.value);
                }
                $("#total-segment").val(sum)
                if (sum != 100) {
                    $('#segment_error').show();
                    $('#segment_error').text('Sum of value Product and Sagment must be 100');
                }else{
                    $('#segment_error').hide();
                    $('#segment_error').text('');
                }
            });
        }
        $(document).on("dblclick", ".content", function() {
            var data = $(this).attr('data-value');
            $(`#${data}-div`).css('display', 'none');
            $(`#${data}-text`).attr('type', 'text');
            $(`#${data}-text`).focus();
            $(`#${data}-text`).focus(function() {
                console.log('in');
            }).blur(function() {
                var newcont = $(`#${data}-text`).val();
                $(`#${data}-div`).text(`${newcont?newcont:0}%`);
                $(`#${data}-div`).css('display', 'block');
                $(`#${data}-text`).attr('type', 'hidden');
            });
        })
        $(document).on('change','#discount',function(){
            var discount = $(this).val();
            var vat = $('#vat').val();
            var wht = $('#wht_change').val();
            var deposit =$('#deposit').val();
            var sum =$('#sum').val();
            var net_pay = parseFloat(sum)  + parseFloat(vat) - parseFloat(discount) - parseFloat(wht) - parseFloat(deposit);
            $("#net_payable").text('$' + net_pay.toFixed(2));
        })
        $(document).on('change','#vat',function(){
            var discount = $('#discount').val();
            var vat = $(this).val();
            var wht = $('#wht_change').val();
            var deposit =$('#deposit').val();
            var sum =$('#sum').val();
            var net_pay = parseFloat(sum)  + parseFloat(vat) - parseFloat(discount) - parseFloat(wht) - parseFloat(deposit);
            $("#net_payable").text('$' + net_pay.toFixed(2));
        })
        $(document).on('change','#wht_change',function(){
            var discount = $('#discount').val();
            var vat = $('#vat').val();
            var wht = $(this).val();
            var deposit =$('#deposit').val();
            var sum =$('#sum').val();
            var net_pay = parseFloat(sum)  + parseFloat(vat) - parseFloat(discount) - parseFloat(wht) - parseFloat(deposit);
            $("#net_payable").text('$' + net_pay.toFixed(2));
        })
        $(document).on('change','#deposit',function(){
            var discount = $('#discount').val();
            var vat = $('#vat').val();
            var wht = $('#wht_change').val();
            var deposit =$(this).val();
            var sum =$('#sum').val();
            var net_pay = parseFloat(sum)  + parseFloat(vat) - parseFloat(discount) - parseFloat(wht) - parseFloat(deposit);
            $("#net_payable").text('$' + net_pay.toFixed(2));
        })

    </script>



    <script>
        $("#approve_form").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                slc_approve: {
                    required: true,
                },

                slc_final: {
                    required: true,
                },
                comment: {
                    required: false,
                },
                department: {
                    required: true,
                },
                requestDate: {
                    required: true,
                },
                expDate: {
                    required: false,
                },
                refNumber: {
                    required: true,
                },
                subject: {
                    required: true,
                },
                bank_name: {
                    required: true,
                },
                bank_address: {
                    required: true,
                },
                account_name: {
                    required: true,
                },
                account_number: {
                    required: true,
                },
                for_who: {
                    required: true,
                },
                id_no: {
                    required: true,
                },
                contact_no: {
                    required: true,
                },
                address_who: {
                    required: true,
                },
                'invoice[]': {
                    required: true,
                },
                'type[]': {
                    required: true,
                },
                'category[]': {
                    required: true,
                },

                'description[]': {
                    required: true,
                },
                'unit[]': {
                    required: true,
                },
                'qty[]': {
                    required: true,
                },
                'unit_price[]': {
                    required: true,
                },
                'total_estimate[]': {
                    required: true,
                },
                'delivery_date[]': {
                    required: false,
                },
                'within_budget[]': {
                    required: true,
                },
                'alternative_budget_code[]': {
                    required: true,
                },
                'budget_code[]': {
                    required: true,
                },
                'br_dep_code[]': {
                    required: true,
                },
                discount: {
                    required: true,
                },
                vat: {
                    required: true,
                },
                wht: {
                    required: true,
                },
                deposit: {
                    required: true,
                },
                purpose_rationale: {
                    required: true,
                },
                bid_waiver_sole: {
                    required: false,
                },
                'vendor_name[]': {
                    required: false,
                },
                'vendor_description[]': {
                    required: false,
                },
                justification_for_request: {
                    required: false,
                },
                comment_by_procurement: {
                    required: true,
                },
                currency: {
                    required: true,
                },

            },


            messages: {
                department: "(Please fill all *)",
                requestDate: "(Please fill all *)",
                expDate: "(Please fill all *)",
                refNumber: "(Please fill all *)",
                subject: "(Please fill all *)",
                pr_ref_no: "(Please fill all *)",

                bank_name: "(Please fill all *)",
                bank_address: "(Please fill all *)",
                account_name: "(Please fill all *)",
                account_number: "(Please fill all *)",
                for_who: "(Please fill all *)",
                id_no: "(Please fill all *)",
                contact_no: "(Please fill all *)",
                address_who: "(Please fill all *)",

                discount: "(Please fill all *)",
                vat: "(Please fill all *)",
                wht: "(Please fill all *)",
                deposit: "(Please fill all *)",

                'type[]': "(Please fill all *)",
                'category[]': "(Please fill all *)",
                'invoice[]': "(Please fill all *)",
                'description[]': "(Please fill all *)",
                'unit[]': "(Please fill all *)",
                'qty[]': "(Please fill all *)",
                'unit_price[]': "(Please fill all *)",
                'total_estimate[]': "(Please fill all *)",
                'delivery_date[]': "(Please fill all *)",
                'within_budget[]': "(Please fill all *)",
                'br_dep_code[]': "(Please fill all *)",
                'budget_code[]': "(Please fill all *)",
                'alternative_budget_code[]': "(Please fill all *)",

                purpose_rationale: "(Please fill all *)",
                bid_waiver_sole: "(Please fill all *)",
                'vendor_name[]': "(Please fill all *)",
                'vendor_description[]': "(Please fill all *)",
                justification_for_request: "(Please fill all *)",
                comment_by_procurement: "(Please fill all *)",

                slc_approve: "(Please fill all *)",
                slc_final: "(Please fill all *)",
                comment: "(Please fill all *)"

            },

            errorPlacement: function(error, element) {

                if (element.attr("name") == "account_number" || element.attr("name") == "account_name" ||
                    element.attr("name") == "bank_name" ||
                    element.attr("name") == "bank_address") {
                    $('#paid_to').empty();
                    error.appendTo('#paid_to');
                } else if (element.attr("name") == "description[]" || element.attr("name") == "unit[]" ||
                    element.attr("name") == "qty[]" ||
                    element.attr("name") == "unit_price[]" || element.attr("name") == "total_estimate[]" ||
                    element.attr("name") == "delivery_date[]" || element.attr("name") == "within_budget[]" ||
                    element.attr("name") == "br_dep_code[]" || element.attr("name") == "budget_code[]" ||
                    element.attr("name") == "alternative_budget_code[]" || element.attr("name") ==
                    "invoice[]" || element.attr("name") == "discount" || element.attr("name") == "vat" ||
                    element.attr("name") == "wht" || element.attr("name") == "deposit") {
                    $('#tbl_procurement_error').empty();
                    error.appendTo('#tbl_procurement_error');
                } else if (element.attr("name") == "for_who" || element.attr("name") == "id_no" || element.attr(
                        "name") == "contact_no" ||
                    element.attr("name") == "address_who") {
                    $('#for').empty();
                    error.appendTo('#for');
                } else if (element.attr("name") == "currency" || element.attr("name") == "subject" || element
                    .attr("name") == "type[]" ||
                    element.attr("name") == "category[]") {
                    $('#top_error').empty();
                    error.appendTo('#top_error');
                } else if (element.attr("name") == "slc_approve" ||
                    element.attr("name") == "slc_final" ||
                    element.attr("name") == "comment") {
                    $('#approve_review').empty();
                    error.appendTo('#approve_review');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                var comment = $("#comment").val();
                var total_product = $('#total-product').val();
                var total_segment = $('#total-segment').val();
               
                if (parseInt(total_product) != 100) {
                    $('#product_error').show();
                    $('#product_error').text('Sum of value Product and Sagment must be 100');
                    return false;
                }
                if (parseInt(total_segment) != 100) {
                    $('#segment_error').show();
                    $('#segment_error').text('Sum of value Product and Sagment must be 100');
                    return false;
                }
                if (comment == '') {
                    if (confirm('Are you sure without any comment?')) {
                        $(".overlay").show();
                        form.submit();
                    } else {
                        return false;
                    }
                } else {
                    $(".overlay").show();
                    form.submit();
                }
            }

        });
    </script>

    <script>
        function blink_text() {
            $('#spn_procurement_error').fadeOut(800);
            $('#spn_procurement_error').fadeIn(800);

            $('#tbl_procurement_error').fadeOut(800);
            $('#tbl_procurement_error').fadeIn(800);

            $('#tbl1_procurement_error').fadeOut(800);
            $('#tbl1_procurement_error').fadeIn(800);
            $('#paid_to').fadeOut(800);
            $('#paid_to').fadeIn(800);
            $('#for').fadeOut(800);
            $('#for').fadeIn(800);
            $('#top_error').fadeOut(800);
            $('#top_error').fadeIn(800);
            $('#approve_review').fadeOut(800);
            $('#approve_review').fadeIn(800);
            $('#segment_error').fadeOut(800);
            $('#segment_error').fadeIn(800);
            $('#product_error').fadeOut(800);
            $('#product_error').fadeIn(800);

        }

        setInterval(blink_text, 1000);

        function go_home() {
            $('#registration')[0].reset();
        }
    </script>

<!-- ============================= -->
<script type="text/javascript">
    $('#confirmProcess').click(function(e){
        e.preventDefault();
        $('#modalPaymentYTDPreview').modal("hide");
        var button = document.getElementById("submitForm");
        button.click();
    });

    $('#btnConfirmPayment').click(function(e) {
        e.preventDefault();
        let req_recid = $('#req_recid').val();
        $.ajax({
            url: "/payment/ytd-expense/preview",
            type: 'post',
            dataType: 'json',
            data:{req_recid,_token: '{{csrf_token()}}'},
            success:function(response){
                console.log(response.data);
                if(response['reposnseCode'] == '401'){
                    alert('Something went wrong');
                }else{
                    $("#confirmTitle span").html("");
                    $("#tbPaymentPreview tbody > tr").remove();
                    let payments = response.data;
                    let index = 1;
                    for (let i = 0; i < payments.length; i++) {
                        let statusStyle =  "style='font-weight: bold; color: #0ac282;'";
                        if(payments[i].status == 'NO'){
                            statusStyle =  "style='font-weight: bold; color: red;'";
                        }
                        $('#tbPaymentPreview tbody').append(
                            "<tr>"+
                                "<td>" + index + "</td>"+
                                "<td>" + payments[i].budget_code + "</td>"+
                                "<td>" + payments[i].alternative_budget_code + "</td>"+
                                "<td> USD " + parseFloat(payments[i].total_request).toFixed(2) + "</td>"+
                                "<td> USD " + parseFloat(payments[i].ytd_expense).toFixed(2) + "</td>"+
                                "<td> USD " +  parseFloat(payments[i].total_budget).toFixed(2) + "</td>"+
                                "<td> USD " +parseFloat( payments[i].total_remaining_amount).toFixed(2) + "</td>"+
                                "<td " + statusStyle+ ">" + payments[i].status + "</td>"+
                            "</tr>"
                        );
                        index++;
                    }
                    $('#confirmTitle span').append("Are you sure you want to submit this request : "+req_recid+" ?");
                    $('#modalPaymentYTDPreview').modal("show");
                }
            },
        });
    });
</script>
<script>
    var rowIdx = 0;

    // jQuery button click event to add a row.
    $('#add_reviewer').on('click', function() {
        var group_multiReviewer = @json($group_multiReviewer);
        var slc_review = $('.slc_review').val();
        var slc_review1 = $('.slc_review1').val();
        var slc_review2 = $('.slc_review2').val();
        if(!slc_review){
            alert('You have to select First Reviewer first!');  
        }
        
        // auto select dceo when select dceo office
        var dceo_office_email = @json($dceo_email_office);
        var dceos = @json($dceos);
        var dceo_email_office = dceo_office_email.split("/")[0];
        var new_slc_review = slc_review.split("/")[0];
        var empty_reviewer= 1;
        if(slc_review == ''){
            rowIdx=0;
        }
        if(slc_review1 == ''){
            rowIdx=1
        }
        if(slc_review2 == ''){
            rowIdx=2
        }
        if(slc_review1){
            var new_slc_review1 = slc_review1.split("/")[0];
        }
        if(slc_review2){
            var new_slc_review2 = slc_review2.split("/")[0];
        }
        
        if(rowIdx==0){
             label_reviewer = 'Second Reviewer';
             if(!slc_review){
                empty_reviewer =0;
            }
        }else if(rowIdx==1){
            label_reviewer = 'Third Reviewer';
            if(!slc_review1){
                empty_reviewer =0;
            }
        }else{
            label_reviewer = 'Forth Reviewer';
            if(!slc_review2){
                empty_reviewer =0;
            }
        }
        var data_num = rowIdx + 1;
        if (data_num <= 3 && empty_reviewer == 1) {
            var selectbox_budcode =
                `<select class="js-example-basic-single slc_review${data_num}" id="reviewer${data_num}" name="reviewer${data_num}"> `;
            if(new_slc_review == dceo_email_office && data_num == 1){
                selectbox_budcode +=
                    `<option value="${dceos['email']}/${dceos['role']}" selected >${dceos['fullname']}</option>`;
            }else if (new_slc_review1 == dceo_email_office && data_num == 2){
                selectbox_budcode +=
                    `<option value="${dceos['email']}/${dceos['role']}" selected >${dceos['fullname']}</option>`;
            }else if (new_slc_review2 == dceo_email_office && data_num == 3){
                selectbox_budcode +=
                    `<option value="${dceos['email']}/${dceos['role']}" selected >${dceos['fullname']}</option>`;
            }else{
                selectbox_budcode += '<option value="" selected>Select</option>';
                for (var j = 0; j < group_multiReviewer.length; j++) {
                    if(data_num == 3){
                        if(group_multiReviewer[j]['email'] != dceo_email_office){
                            selectbox_budcode +=
                                `<option value="${group_multiReviewer[j]['email']+'/'+group_multiReviewer[j]['role_id']}">${group_multiReviewer[j]['firstname']} ${group_multiReviewer[j]['lastname']}</option>`;
                        }
                       
                    }else{
                        selectbox_budcode +=
                    `<option value="${group_multiReviewer[j]['email']+'/'+group_multiReviewer[j]['role_id']}">${group_multiReviewer[j]['firstname']} ${group_multiReviewer[j]['lastname']}</option>`;
                    }
                }
            }
            
            selectbox_budcode += '</select>'
            // Adding a row inside the tbody.
            $('#row_reviewer').append(`<tr id="R${++rowIdx}">
                    <td style="text-align: right;width: 150px !important;border: none !important;"> ${label_reviewer}</td>
                    <td class="td_reviewer${rowIdx}" style="border: none !important; ">
                            ` + selectbox_budcode + `
                    </td>
                    <td style="border: none !important; width: 5%">
                    <i id="remove_reviewer" class="fa fa-times" style="font-size: 20px;color: red"></i>
                    </tr>`);
            $(".js-example-basic-single").select2();
     
        }
         else {
            if(rowIdx==0 && !slc_review){
                alert('You have to select First Reviewer first!');
            }else if(rowIdx==1 && !slc_review1){
                alert('You have to select Second Reviewer first!');
            }else if(rowIdx==2 && !slc_review2){
                alert('You have to select Third Reviewer first!');
            }else{
                alert('You can not add more Reviewers because the maximum number of Reviewers is 4!');
            }
        }

    });
    $('#add_vender_more').on('click', function() {
        var vender_id = $(this).data('vender_id');
        var is_null = $(this).data('is_null');
        $('#id_no_record').remove();
        var data_num = rowIdx + 1;
        
        if(is_null == 1){
            var number_vender = rowIdx + 1;
        }else{
            var number_vender = vender_id + data_num;
        }
            // Adding a row inside the tbody.
            $('#row_vender').append(`<tr id="R${++rowIdx}">
                    <td style="width: 20% !important;border: none !important;"> ${number_vender}</td>
                    <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm vendor_name" id="vendor_name"
                                                    name="vendor_name[]" cols="30" rows="1"></textarea>
                                            </td>
                    <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm vendor_description"
                                                    id="vendor_description" name="vendor_description[]" cols="30"
                                                    rows="1"> </textarea>
                                            </td>
                    <td style="border: none !important; width: 5%">
                    <i id="remove_vender_more" data-id="" class="fa fa-times" style="font-size: 20px;color: red"></i>
                    </tr>`);
            $(".js-example-basic-single").select2();
    });
    $('#row_vender').on('click', '#remove_vender_more', function() {
        var vender_id = $(this).data('id');
        if(vender_id == ""){
            var child = $(this).closest('tr').nextAll();
            child.each(function() {
                var id = $(this).attr('id');
                var idx = $(this).children('.row-index').children('p');
                var dig = parseInt(id.substring(1));
                idx.html(`Row ${dig - 1}`);
                $(this).attr('id', `R${dig - 1}`);
            });
            $(this).closest('tr').remove();
            rowIdx--;
        }else{
            $.ajax({
				url:  "update_vendor",
				type: 'post',   
				dataType: 'json',
                data:{'submit':'delete','id':vender_id,_token: '{{csrf_token()}}'},
				success: function(response) {
					var ac_no = response['data'];
                    location.reload();
                    
				},
				async: false
			});
        }
        
    });
    
    $('#row_reviewer').on('click', '#remove_reviewer', function() {
        var child = $(this).closest('tr').nextAll();
        child.each(function() {
            var id = $(this).attr('id');
            var idx = $(this).children('.row-index').children('p');
            var dig = parseInt(id.substring(1));
            idx.html(`Row ${dig - 1}`);
            $(this).attr('id', `R${dig - 1}`);
        });
        $(this).closest('tr').remove();
        rowIdx--;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById("expDate");

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
@endsection
