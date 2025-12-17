@extends('layouts.master')
@section('style')
    <style>
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

        .myheader {
            margin: 0;
            padding: 8px 20px;
            background: white;
            color: #f1f1f1;
            z-index: 9999;
        }

        .sticky {
            position: fixed;
            top: 0;
            width: 100%;
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

        ul li {

            list-style: none;
        }

    </style>
@endsection
@section('menu')
    @include('siderbar.tasklist')
@endsection
@section('breadcrumb')
    @include('breadcrumb.payment')
@endsection
@section('content')


    <div class="col-md-12">

        <form id="registration" method="post" action="{{ route('form/payment/action') }}">
            {{ csrf_field() }}
            <!-- Page-body start -->
            <!-- create button pdf  -->
            <div class="myheader" id="myHeader">
             
                    <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/payment/pdf/' . Crypt::encrypt($top['req_recid'].'___no')) }}"><i
                        class="fa fa-save" aria-hidden="true"></i>
                    PDF</a>
            </div>
            <!-- end  -->

            <!-- Page-body start -->
            <div class="page-body" id="invoice">
                <!-- DOM/Jquery table start -->
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Payment Request</h4>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">

                                <div class="form-group row">


                                    <div class="col-sm-3">
                                        <label for="">Status</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input readonly type="text" class="form-control " id="requestDate"
                                            name="requestDate" value="{{ $request_status->status }}">
                                    </div>



                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Procurement Ref. :</label>
                                    </div>
                                    <div class="col-sm-9">
                                         @if (!empty($procurement_ref['ref']))
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
                                            <input type="text" class="form-control" id="expDate" name="expDate"
                                                value="{{ \Carbon\Carbon::parse($top['due_expect_date'])->format('d/m/Y') }}">
                                        @else
                                            <input type="text" class="form-control" id="expDate" name="expDate" value="N/A">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Currency:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control " id="currency" name="currency" disabled="">
                                            <option value="" disabled="">Select One</option>
                                            @if ($top['ccy'] == 'USD')
                                                <option value="USD" selected="">USD</option>
                                                <option value="KHR">KHR</option>
                                            @else
                                                <option value="USD">USD</option>
                                                <option value="KHR" selected="">KHR</option>
                                            @endif


                                        </select>
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
                                                    readonly="" name="subject" rows="2">{{ $top['subject'] }}</textarea>
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
                                                    readonly="" value="Payment" checked="">
                                                <label class="border-checkbox-label" for="type">Payment</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type1" readonly=""
                                                    name="type[]" value="Deposit">
                                                <label class="border-checkbox-label" for="type1">Deposit</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type" readonly=""
                                                    name="type[]" value="Payment">
                                                <label class="border-checkbox-label" for="type">Payment</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_1" type="checkbox" id="type1" readonly=""
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
                                                    readonly="" name="category[]" checked="" value="Ordinary">
                                                <label class="border-checkbox-label" for="category">Ordinary</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" readonly=""
                                                    id="category" name="category[]" value="Ordinary">
                                                <label class="border-checkbox-label" for="category">Ordinary</label>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($top_mid['category'] == 'Event/Project')
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" readonly=""
                                                    id="category1" name="category[]" checked="" value="Event/Project">
                                                <label class="border-checkbox-label" for="category1">Event/Project</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-2 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" readonly=""
                                                    id="category1" name="category[]" value="Event/Project">
                                                <label class="border-checkbox-label" for="category1">Event/Project</label>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($top_mid['category'] == 'Staff Benefit')
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" readonly=""
                                                    id="category2" name="category[]" checked="" value="Staff Benefit">
                                                <label class="border-checkbox-label" for="category2">Staff Benefit</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-3 border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox chb_2" type="checkbox" readonly=""
                                                    id="category2" name="category[]" value="Staff Benefit">
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
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Account Name:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="account_name" readonly=""
                                                name="account_name" value="{{ $top_mid['account_name'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Account Number:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="account_number" readonly=""
                                                name="account_number" value="{{ $top_mid['account_number'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Bank Name:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="bank_name" readonly=""
                                                name="bank_name" value="{{ $top_mid['bank_name'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Bank Address:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="2" id="bank_address" readonly=""
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
                                                <input type="text" id="tel" name="tel" class="form-control" readonly=""
                                                    value="{{ $top_mid['tel'] }}">
                                            @else
                                                <input type="text" id="tel" name="tel" class="form-control " readonly=""
                                                    value="N/A">
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
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Mr./Ms./Company:</label>
                                        </div>
                                        <div class="col-sm-9 ">
                                            <input type="text" id="for_who" name="for_who" readonly="" class="form-control"
                                                value="{{ $top_mid['company'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">ID No.:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="id_no" name="id_no" readonly="" class="form-control"
                                                value="{{ $top_mid['id_no'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Contact No:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="contact_no" readonly="" name="contact_no"
                                                class="form-control" value="{{ $top_mid['contact_no'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="">Address:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="2" readonly="" id="address_who"
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
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->


                                <table class="table table-striped table-bordered" id="example-1"
                                    style="margin-top: 50px !important;font-size: 12.5px;">


                                    <thead>
                                        <tr class="table-info">
                                            <th rowspan="2" style="vertical-align:middle;">No</th>
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
                                        </tr>
                                    </thead>


                                    <tbody>

                                        @if (count($body) > 0)
                                            <?php $total = 0;
                                            $total_khr = 0; ?>
                                            @foreach ($body as $key => $value)
                                                <tr>
                                                    <td>
                                                        {{ ++$key }}
                                                    </td>
                                                    <td class="tabledit-view-mode">

                                                        {{ $value->inv_no }}


                                                    </td>
                                                    <td class="tabledit-view-mode">

                                                        {{ $value->description }}

                                                    </td>
                                                    <td class="tabledit-view-mode">

                                                        {{ $value->br_dep_code }}

                                                    </td>
                                                    <td class="tabledit-view-mode">

                                                        {{ $value->budget_code }}

                                                    </td>

                                                    <td class="tabledit-view-mode">
                                                        @if ($value->alternativebudget_code > 0)
                                                            {{ $value->alternativebudget_code }}
                                                        @else
                                                            N/A
                                                        @endif

                                                    </td>
                                                    <td class="tabledit-view-mode">
                                                        {{ $value->unit }}
                                                    </td>
                                                    <td class="tabledit-view-mode">
                                                        {{ $value->qty }}
                                                    </td>
                                                    <td class="tabledit-view-mode">
                                                        @if ($top['ccy'] == 'KHR')
                                                            <span style="font-size: 18px;">៛</span>
                                                            @money($value['unit_price_khr'])
                                                        @else
                                                            $@money($value->unit_price)
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
                                                    <td class="tabledit-view-mode">

                                                        @if ($top['ccy'] == 'KHR')
                                                            <span style="font-size: 18px;">៛</span>
                                                            @money($value->total_khr)
                                                        @else
                                                            $@money($value->total)
                                                        @endif
                                                    </td>


                                                    <td class="tabledit-view-mode">
                                                        @if ($value->budget_code == 'NA' || $value->alternativebudget_code == 'NA')
                                                            $@money(0)
                                                        @else
                                                            $@money($value->ytd_expense)
                                                        @endif

                                                    </td>
                                                    <td class="tabledit-view-mode">
                                                        @if ($value->budget_code == 'NA' || $value->alternativebudget_code == 'NA')
                                                            $@money(0)
                                                        @else
                                                            $@money($value->total_budget)
                                                        @endif
                                                    </td>


                                                    <td class="tabledit-view-mode">
                                                        @if ($value->within_budget_code == 'Y')
                                                            <span
                                                                style="color:green;font-size: 14px;font-weight: bold;">YES</span>
                                                        @else
                                                            <span
                                                                style="color:red;font-size: 14px;font-weight: bold;">NO</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <?php
                                                
                                                if ($top['ccy'] == 'KHR') {
                                                    $total_khr += $value->total_khr;
                                                } else {
                                                    $total += $value->total;
                                                }
                                                
                                                ?>
                                            @endforeach

                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    SUB TOTAL
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_khr)
                                                    @else
                                                        $@money($total)
                                                    @endif

                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    DISCOUNT
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($body_bottom->discount_khr)
                                                    @else
                                                        $@money($body_bottom->discount)
                                                    @endif

                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    WHT
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($body_bottom->wht_khr)
                                                    @else
                                                        $@money($body_bottom->wht)
                                                    @endif

                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align: right;">
                                                    DEPOSIT
                                                </td>
                                                <td>
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($body_bottom->deposit_khr)
                                                    @else
                                                        $@money($body_bottom->deposit)
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
                                                        @money($net_payable)
                                                    @else
                                                        <?php $net_payable = $total - $body_bottom->discount + $body_bottom->vat - $body_bottom->wht - $body_bottom->deposit; ?>
                                                        $@money($net_payable)
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
                                   <textarea readonly class="form-control ">{{$top_mid['remarkable']}}</textarea>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->
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
                                            <td class="table-success" style="text-align: left"><b>Allocated % :</b></td>
                                            <td>{{ $bottom['general'] ?? '0' }}%</td>
                                            <td>{{ $bottom['loan_general'] ?? '0' }}%</td>
                                            <td>{{ $bottom['mortage'] ?? '0' }}%</td>
                                            <td>{{ $bottom['busines'] ?? '0' }}%</td>
                                            <td>{{ $bottom['personal'] ?? '0' }}%</td>
                                            <td>{{ $bottom['card_general'] ?? '0' }}%</td>
                                            <td>{{ $bottom['debit_card'] ?? '0' }}%</td>
                                            <td>{{ $bottom['credit_card'] ?? '0' }}%</td>
                                            <td>{{ $bottom['trade_general'] ?? '0' }}%</td>
                                            <td>{{ $bottom['bank_guarantee'] ?? '0' }}%</td>
                                            <td>{{ $bottom['letter_of_credit'] ?? '0' }}%</td>
                                            <td>{{ $bottom['deposit_general'] ?? '0' }}%</td>
                                            <td>{{ $bottom['casa_individual'] ?? '0' }}%</td>
                                            <td>{{ $bottom['td_individual'] ?? '0' }}%</td>
                                            <td>{{ $bottom['casa_corporate'] ?? '0' }}%</td>
                                            <td>{{ $bottom['td_corporate'] ?? '0' }}%</td>
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
                                <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->
                                <table class="text-center table table-striped table-bordered" id="segment_input"
                                    style="font-size: 12.5px">
                                    <thead>
                                        <tr class="table-info">
                                            <th>Segment</th>
                                            <th colspan="6">Input Allocated % (in total equal 100%)</th>
                                        </tr>
                                        <tr class="table-success">
                                            <th>Categories:</th>
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
                                            <td style="text-align: left;" class="table-success"><b>Code :</b></td>
                                            <td>999</td>
                                            <td>100</td>
                                            <td>200</td>
                                            <td>300</td>
                                            <td>400</td>
                                            <td>500</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;" class="table-success"><b>Allocated % :</b></td>
                                            <td>{{ $bottom['sagement_general'] ?? '0' }}%</td>
                                            <td>{{ $bottom['sagement_bfs'] ?? '0' }}%</td>
                                            <td>{{ $bottom['sagement_rfs'] ?? '0' }}%</td>
                                            <td>{{ $bottom['sagement_pb'] ?? '0' }}%</td>
                                            <td>{{ $bottom['sagement_pcp'] ?? '0' }}%</td>
                                            <td>{{ $bottom['sagement_afs'] ?? '0' }}%</td>

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
                                    <textarea readonly="" name="remarks_product_segment"
                                        class="form-control ">{{ $bottom['remarks'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="req_recid" value="{{ $top['req_recid'] }}">
                <input type="hidden" name="req_email" value="{{ $top['req_email'] }}">
                <input type="hidden" name="req_name" value="{{ $top['req_name'] }}">
                <input type="hidden" name="req_department" value="{{ $top['req_branch'] }}">
                <input type="hidden" name="req_position" value="{{ $top['req_position'] }}">

                <div class="card">


                    <ul class="nav nav-tabs  tabs" role="tablist">


                        @if ($requester == '1' or $review == '1' or $approve == '1')
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home1" role="tab"
                                    aria-expanded="true">ACTION</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab"
                                    aria-expanded="true">ACTIVITY LOG</a>
                            </li>
                        @elseif($query=='1')
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#query" role="tab"
                                    aria-expanded="true">ACTION</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab"
                                    aria-expanded="true">ACTIVITY LOG</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#activitylog" role="tab"
                                    aria-expanded="true">ACTIVITY LOG</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#profile1" role="tab"
                                aria-expanded="false">REFERENCE DOCUMENT</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#progress" role="tab"
                                aria-expanded="false">PROGRESS</a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content tabs card-block">
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
                                        @if (!empty($pending_at))
                                            Pending At: <span
                                                style="font-size: 14px; color: red;font-weight: bold;">{{ $pending_at->firstname }}
                                                {{ $pending_at->lastname }}</span>
                                        @elseif(!empty($pending_at_team))
                                            <span style="font-size: 14px; font-weight: bold;"> Pending At:
                                                {{ $pending_at_team }}</span>
                                        @else
                                            <span style="font-size: 14px; font-weight: bold;"> Pending At: N/A</span>
                                        @endif

                                    </div>

                                </div>
                            </div>
                        </div>


                        @if ($requester == '1' or $review == '1' or $approve == '1')
                            <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                                <div class="table-responsive">
                                    <table class="table">
                                        @if (!empty($insuficient))
                                            <tr>
                                                <td
                                                    style="text-align: right;border: none !important;width: 150px !important;">
                                                    Note</td>
                                                <td style="border: none !important;">
                                                    <span>Process flow change due to insuffucient amount</span>
                                                </td>
                                            </tr>

                                        @endif
                                        <tr>
                                            <td style="text-align: right;border: none !important;width: 150px !important;">
                                                Comment</td>
                                            <td style="border: none !important;">
                                                <textarea class="form-control" rows="5" name="comment"
                                                    id="comment"></textarea>
                                            </td>
                                        </tr>
                                        @if (!empty($insuficient))
                                            <tr>
                                                <td style="border: none !important;">

                                                </td>
                                                <td style="border: none !important;">
                                                    <button type="submit" name="submit" class="btn_click" value="reject"
                                                        style="padding: 5px 10px 5px 10px;"><i class="fa fa-times"
                                                            aria-hidden="true" style="color: red"></i> Reject</button>
                                                    <button type="submit" name="submit" class="btn_click" value="backtocfo"
                                                        style="padding: 5px 10px 5px 10px;"><i class="fa fa-backward"
                                                            aria-hidden="true" style="color: blue"></i> Back to CFO</button>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td style="border: none !important;">

                                                </td>
                                                <td style="border: none !important;">

                                                    @if ($final_res == 'Y')
                                                        <button type="submit" name="submit" class="btn_click" id="received"
                                                            value="approve"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-download" aria-hidden="true"
                                                                style="color: blue"></i> Received</button>
                                                        <button type="submit" name="submit" class="btn_click" value="query"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-commenting" aria-hidden="true"
                                                                style="color:orange"></i> Query</button>
                                                        <button type="submit" name="submit" id="transfer" class="btn_click"
                                                            value="transfer"
                                                            style="display: none;padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-refresh" aria-hidden="true"
                                                                style="color: deeppink"></i> Transfer</button>
                                                    @else
                                                        <button type="submit" name="submit" value="approve"
                                                            class="btn_click"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-check" aria-hidden="true"
                                                                style="color: green"></i> Approve</button>
                                                        <button type="submit" name="submit" value="reject" class="btn_click"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-times" aria-hidden="true"
                                                                style="color: red"></i> Reject</button>
                                                        <button type="submit" name="submit" value="back" class="btn_click"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-backward" aria-hidden="true"
                                                                style="color: blue"></i> Assign Back</button>
                                                        <button type="submit" name="submit" value="query" class="btn_click"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-commenting" aria-hidden="true"
                                                                style="color:orange"></i> Query</button>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endif
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

                        @elseif($query=='1')
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

                                                <button type="submit" name="submit" value="query" class="btn_click"
                                                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                        class="fa fa-commenting" aria-hidden="true"
                                                        style="color:orange"></i> Query</button>




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
                        @else

                            <div class="tab-pane active" id="activitylog" role="tabpanel" aria-expanded="true"
                                style="padding-bottom: 15px;">
                                <div class="timeline">
                                    <div class="timeline-month bg-c-yellow"
                                        style="font-weight: bold;font-size: 15px;color: white;background: #FF7814">
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
                            <table class="table table-hover" style="font-size: 12.5px;">
                                <thead>
                                    <tr class="table-info">
                                        <th>File</th>
                                        <th>File name</th>
                                        <th>Upload by</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                @if (count($document) > 0)
                                    @foreach ($document as $value)
                                        <tr>
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
                                @else
                                    <tr>
                                        <td colspan="4" style="font-weight: bold;">
                                            No Data
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
             $(".btn_click").on("click", function() {
            var comment = $("#comment").val();
            // alert(comment)
            if (comment == '') {
                if (confirm('Are you sure without any comment?')) {
                    $(".overlay").show();
                    $("#registration").submit();
                } else {
                    return false;
                }
            }else{
                $(".overlay").show();
                $("#registration").submit();
            }
        });
    </script>
    <script>
        // Initiate an Ajax request on button click
        //   $(document).on("click", "button", function(){
        //       // Adding timestamp to set cache false
        //       $.get("/examples/php/customers.php?v="+ $.now(), function(data){
        //           $("body").html(data);
        //       });       
        //   });

        // Add remove loading class on body element depending on Ajax request status
        $(document).on({
            ajaxStart: function() {
                $("body").addClass("loading");
            },
            ajaxStop: function() {
                $("body").removeClass("loading");
            }
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
    <!-- alert blink text -->

@endsection
