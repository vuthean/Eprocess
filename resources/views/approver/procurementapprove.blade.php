@extends('layouts.master')
@section('style')
    <style>
        /* for button pdf  */
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

        /* end  */
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

    </style>
@endsection
@section('menu')
    @include('siderbar.tasklist')
@endsection
@section('breadcrumb')
    @include('breadcrumb.procurement_approve')
@endsection
@section('content')
    <div class="col-md-12">
        <form id="registration" method="post" action="{{ route('form/procurement/action') }}">
            {{ csrf_field() }}
            <div class="myheader" id="myHeader">
                <a style="padding: 5px 10px 5px 10px;cursor: pointer;" class="btn btn-primary" href="{{ url('form/procurement/pdf/' . Crypt::encrypt($top['req_recid'].'___no')) }}"><i
                        class="fa fa-save" aria-hidden="true"></i>
                    PDF</a>
            </div>
            <div class="page-body" id="invoice">
                <!-- DOM/Jquery table start -->
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Requester</h4>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="">Req.By</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input readonly type="text" class="form-control " id="requestDate"
                                                    name="requestDate" value="{{ $top['req_name'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="">Branch\Department</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input readonly type="text" class="form-control " id="expDate"
                                                    name="expDate" value="{{ $top['req_branch'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="">Position</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input readonly type="text" class="form-control " id="requestDate"
                                                    name="requestDate" value="{{ $top['req_position'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="">Rquest Date</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input readonly type="text" class="form-control " id="expDate"
                                                    name="expDate" value="{{ $top['req_date'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="">Status</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input readonly type="text" class="form-control " id="requestDate"
                                                    name="requestDate" value="{{ $request_status->status }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Procurement Request Detail</h4>
                        <div class="row">
                            <div class="col-sm-8 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Department :</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input readonly type="text" class="form-control " name="department" id="department"
                                            value="{{ $top['req_branch'] }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="">Request Date:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <input readonly type="text" class="form-control " id="requestDate"
                                                    name="requestDate" value="{{ $top_mid['req_date'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="">Exp. Deliv. Date :</label>
                                            </div>
                                            <div class="col-sm-6">
                                                @if (!empty($top['due_expect_date']))
                                                    <input type="text" class="form-control" id="expDate" name="expDate"
                                                        value="{{ \Carbon\Carbon::parse($top['due_expect_date'])->format('d/m/Y') }}"
                                                        readonly="">
                                                @else
                                                    <input type="text" class="form-control" id="expDate" name="expDate"
                                                        value="N/A" readonly="">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Ref. :</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input readonly type="text" class="form-control " id="refNumber" name="refNumber"
                                            value="{{ $top['ref'] }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Subject :</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="2" id="subject" name="subject"
                                            readonly="">{{ $top['subject'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mobile-inputs">
                                <div class="form-group">
                                    <br>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="">PR Ref NO. :</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input readonly type="text" class="form-control " id="pr_ref_no" name="pr_ref_no"
                                            value="{{ $top['req_recid'] }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="">Currency:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input readonly type="text" class="form-control " id="currency" name="currency"
                                            value="{{ $top['ccy'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Procurement Request Detail</h4>
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <table class="table table-striped table-bordered" id="example-1">
                                    <thead>
                                        <tr class="table-info">
                                            <th rowspan="2" style="vertical-align:middle;">No</th>
                                            <th rowspan="2" style="vertical-align:middle;">Description</th>
                                            <th rowspan="2" style="vertical-align:middle;">Br./Dep <br> Code</th>
                                            <th rowspan="2" style="vertical-align:middle;">Budget <br> Code</th>
                                            <th rowspan="2" style="vertical-align:middle;">Alternative <br> Budget Code</th>
                                            <th rowspan="2" style="vertical-align:middle;">Unit</th>
                                            <th rowspan="2" style="vertical-align:middle;">QTY</th>
                                            <th rowspan="2" style="vertical-align:middle;">Unit price</th>
                                            <th rowspan="2" style="vertical-align:middle;">Total <br> Estimate</th>
                                            <th rowspan="2" style="vertical-align:middle;">Within Budget <br> (Yes/No)</th>
                                            @if ($param_url_response == 'yes' and Session::get('is_procurement') == '1' or Session::get('is_markating') == '1' or Session::get('is_admin_team') == '1' or Session::get('PLD_team') == '1' or Session::get('is_infra_team') == '1' or Session::get('is_alternative_team') == '1')
                                                <th rowspan="2" style="vertical-align:middle;">Payment <br> (Yes/No)</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0;
                                        $total_khr = 0; 
                                        $total_vat=0;?>
                                        @foreach ($body as $key => $value)
                                        
                                            <tr>
                                                <td>
                                                    {{ ++$key }}
                                                </td>
                                                <td class="tabledit-view-mode">

                                                    {{ $value['description'] }}

                                                </td>
                                                <td class="tabledit-view-mode">

                                                    {{ $value['br_dep_code'] }}

                                                </td>
                                                <td class="tabledit-view-mode">

                                                    {{ $value['budget_code'] }}

                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($value['alternativebudget_code'] > 0)
                                                        {{ $value['alternativebudget_code'] }}
                                                    @else
                                                        N/A
                                                    @endif

                                                </td>
                                                <td class="tabledit-view-mode">
                                                    {{ $value['unit'] }}
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    {{ $value['qty'] }}
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value['unit_price_khr'])
                                                    @else
                                                        $@money($value['unit_price'])
                                                    @endif

                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($value['total_estimate_khr'])
                                                    @else
                                                        $@money($value['total_estimate'])
                                                    @endif
                                                </td>
                                                <td class="tabledit-view-mode">
                                                    @if ($value['within_budget_code'] == 'Y')
                                                        <span
                                                            style="color:green;font-size: 14px;font-weight: bold;">YES</span>
                                                    @else
                                                        <span style="color:red;font-size: 14px;font-weight: bold;">NO</span>
                                                    @endif
                                                </td>
                                                @if ($param_url_response == 'yes' and Session::get('is_procurement') == '1' or Session::get('is_markating') == '1' or Session::get('is_admin_team') == '1' or Session::get('PLD_team') == '1' or Session::get('is_infra_team') == '1' or Session::get('is_alternative_team') == '1')
                                                <td class="tabledit-view-mode">
                                                        @if ($value['paid'] == 'Y')
                                                            <label class="transactionSheetToolbarShowDraft">
                                                                <input type="checkbox" value="Y" checked
                                                                    onclick="return false">
                                                            </label>
                                                        @else
                                                            <label class="transactionSheetToolbarShowDraft">
                                                                <input type="checkbox" value="N" onclick="return false">
                                                            </label>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            <?php

                                            if ($top['ccy'] == 'KHR') {
                                                $total_khr += $value['total_estimate_khr'];
                                            } else {
                                                $total += $value['total_estimate'];
                                            }
                                            $total_vat = $value['vat']
                                            ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="text-right" style="font-size: 16px; font-weight: bold;" colspan="8">
                                                Sub Total:</td>
                                            <td style="font-size: 16px; font-weight: bold;">
                                                @if (count($body) > 0)
                                                    @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_khr)
                                                    @else
                                                        $@money($total)
                                                    @endif

                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="text-right" style="font-size: 16px; font-weight: bold;" colspan="8">
                                            VAT:</td>
                                        <td style="font-size: 16px; font-weight: bold;">
                                            @if (count($body) > 0)
                                                @if ($top['ccy'] == 'KHR')
                                                    <input type="hidden" name="grand_total" value="{{$total_khr + ($total_vat*4000)}}">
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_vat*4000)
                                                @else
                                                        $@money($total_vat)
                                                        <input type="hidden" name="grand_total" value="{{$total+$total_vat}}">
                                                @endif   
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" style="font-size: 16px; font-weight: bold;" colspan="8">
                                            GRAND TOTAL:</td>
                                        <td style="font-size: 16px; font-weight: bold;">
                                            @if (count($body) > 0)
                                                @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_khr + ($total_vat*4000))
                                                @else
                                                        $@money($total+$total_vat)
                                                @endif   
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
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Procurement Request Detail</h4>
                        <div class="row form-group">
                            <div class="col-sm-3 mobile-inputs">
                                <label for="">PURPOSE / RATIONALE:</label>
                            </div>
                            <div class="col-sm-9 mobile-inputs">
                                <textarea class="form-control" rows="2" id="purpose_rationale" name="purpose_rationale"
                                    readonly="">{{ $top_mid['purpose'] }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3 mobile-inputs">
                                <label for="">BID WAIVER/SOLE SOURCE REQUEST:</label>
                            </div>
                            <div class="col-sm-9 mobile-inputs">
                                @if (!empty($top_mid['bid']))
                                    <textarea class="form-control" id="bid_waiver_sole" name="bid_waiver_sole"
                                        readonly="">{{Str::upper($top_mid['bid'])}}</textarea>
                                @else
                                    <textarea class="form-control" id="bid_waiver_sole" name="bid_waiver_sole"
                                        readonly="">N/A</textarea>
                                @endif
                            </div>
                        </div>
                        @if($top_mid['bid'] == 'yes' or $top_mid['bid'] != 'no')
                            <div class="card-block">
                                <div class="table-responsive dt-responsive">
                                    <label for="">Recommended vendor(s)</label>
                                    <table class="table table-striped table-bordered" id="example-3">
                                        <thead>
                                            <tr class="table-info">
                                                <th>No</th>
                                                <th>Vendor Name</th>
                                                <th>Justification</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if ($footer->count() <= 0 )
                                                <tr>
                                                    <td colspan="3">
                                                        <span>No Record</span>
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach ($footer as $key => $value)
                                                    @if($value->vender_name)
                                                        <tr>
                                                            <td>{{ ++$key }}</td>
                                                            <td class="tabledit-view-mode">
                                                                {{ $value->vender_name }}
                                                            </td>
                                                            <td class="tabledit-view-mode">
                                                                {{ $value->description }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->
                                <table class="text-center table table-striped table-bordered" id="product_input">
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
                                            <td class="table-success"><b>Allocated % :</b></td>
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
                                <table class="text-center table table-striped table-bordered" id="segment_input">
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
                                    @if($top_mid['justification'])
                                        <textarea readonly="" name="remarks_product_segment"class="form-control ">{!! nl2br(e($bottom['remarks'] ))."\r\n"."Justification for Sole Source Request: ".$top_mid['justification']  !!}</textarea>
                                    @else
                                        <textarea readonly="" name="remarks_product_segment"class="form-control ">{{$bottom['remarks'] }}</textarea>
                                    @endif
                                    
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
                                        <div class="panel panel-primary">
                                            <div class="panel-heading bg-primary">
                                                Requester
                                            </div>
                                            <div class="panel-body">
                                                @if (!empty($requester_progress))
                                                    <span
                                                        style="font-size: 14px; font-weight: bold;">{{ $requester_progress->req_name }}</span>
                                                @else
                                                    <span style="font-size: 14px; font-weight: bold;">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @foreach ($approvers as $approver)
                                        @if($approver->full_name !='N/A')
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
                                        @endif
                                    @endforeach
                                    <div class="col-xl-12 col-lg-4 col-md-4 col-sm-6">
                                        @if (!empty($pending_at))
                                            Pending At: <span
                                                style="font-size: 14px; color: red;font-weight: bold;">{{$pending_at}}</span>
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
                                        @if ($final_res == 'Y')
                                            <tr style="display: none;">
                                                <td style="text-align: right;">
                                                    Wrong request
                                                </td>
                                                <td>
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input class="border-checkbox chb_1" type="checkbox" id="type"
                                                            name="transfer[]" value="Yes">
                                                        <label class="border-checkbox-label" for="type"
                                                            style="color: red">Yes</label>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input class="border-checkbox chb_1" type="checkbox" id="type1"
                                                            name="transfer[]" value="No">
                                                        <label class="border-checkbox-label" for="type1"
                                                            style="color: green">No</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="tr_transfer">
                                                <td style="text-align: right;">
                                                    <span>Transfer To</span>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="transfer_to" id="transfer_selected">
                                                        <option selected="" value="">Select One</option>
                                                        @foreach ($group_final as $value)
                                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                                {{ $value->firstname }} {{ $value->lastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endif
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
                                                        <button type="submit" name="submit" value="reject" class="btn_click"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-times" aria-hidden="true"
                                                                style="color: red"></i> Reject</button>
                                                        <button type="submit" name="submit" value="back" class="btn_click"
                                                            style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                                                                class="fa fa-backward" aria-hidden="true"
                                                                style="color: blue"></i> Assign Back</button>
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
                                        PROCUREMENT REQUEST
                                    </div>
                                    <?php $owner_name = 0;$owner_activity = 0; $reviewer =0;?>
                                    @foreach ($auditlog as $value)
                                    <div class="timeline-section">
                                            <div class="timeline-date" style="background: #3D9CDD">
                                                {{ $value->datetime }}
                                            </div>
                                            @if(!empty($value->doer_role))
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="timeline-box bg-c-lite-green">
                                                            <div class="box-title"
                                                                    style="font-weight: bold;color: white;font-size: 16px;">
                                                                    <i class="fa fa-user"></i> {{$value->doer_role}}: {{ $value->name }}
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                        </strong>{{$value->doer_action}}
                                                                </div>
                                                                <div class="box-item" style="color: white"><strong
                                                                            style="color: white">Comment:
                                                                        </strong>{{ $value->comment }}
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="timeline-box bg-c-lite-green">
                                                        <div class="box-title"
                                                                style="font-weight: bold;color: white;font-size: 16px;">
                                                                @if($value->activity_code == 'A001' )
                                                                    <i class="fa fa-user"></i> Requester: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A005')
                                                                <i class="fa fa-user"></i> Requester: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A009')
                                                                    <i class="fa fa-user"></i> Transfer By: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A007')
                                                                    @if($value->step_action)
                                                                        @if($value->email == $review_progress->review and $value->step_action < 2)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review and $value->step_action < 3)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review and $value->step_action < 4)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer and $value->step_action < 5)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner and $value->step_action < 6)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->final)
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                        @endif
                                                                    @else
                                                                        @if($value->email == $review_progress->review)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->final)
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                        @endif
                                                                    @endif
                                                                @elseif($value->activity_code == 'A003')
                                                                    @if($value->email == $review_progress->review )
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->second_review )
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->third_review )  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                    @elseif($value->email == $review_progress->fourth_reviewer )
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                    @elseif($value->email == $review_progress->budget_owner)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->approve)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->co_approver)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @endif
                                                                @elseif($value->activity_code == 'A008')
                                                                    @if($value->step_action)
                                                                        @if($value->email == $review_progress->review and $value->step_action < 2)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review and $value->step_action < 3)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review and $value->step_action < 4)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer and $value->step_action < 5)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner and $value->step_action < 6)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $top['req_email'] and $value->step_action < 8)
                                                                            <i class="fa fa-user"></i>Requester: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @endif
                                                                    @else
                                                                        @if($value->email == $review_progress->review )
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review )
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review )  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer )
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @endif
                                                                    @endif
                                                                @elseif($review_progress->budget_owner == 'NA')
                                                                    @if($review_progress->review == null && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+5;?>
                                                                    @elseif($review_progress->second_review == null && $reviewer == 1)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+4;?>
                                                                    @elseif($review_progress->third_review == null && $reviewer == 2)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+3;?>
                                                                    @elseif($review_progress->fourth_reviewer == null && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+2;?>
                                                                    @elseif($value->email == $review_progress->review && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->second_review && $reviewer == 1)
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->third_review && $reviewer == 2)  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->fourth_reviewer && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->budget_owner && $reviewer == 4)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->approve && $reviewer == 5)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($review_progress->co_approver == null && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->co_approver && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->final && $reviewer == 7)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                    @endif
                                                                @else
                                                                    @if($review_progress->review == null && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+5;?>
                                                                    @elseif($review_progress->second_review == null && $reviewer == 1)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+4;?>
                                                                    @elseif($review_progress->third_review == null && $reviewer == 2)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+3;?>
                                                                    @elseif($review_progress->fourth_reviewer == null && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+2;?>
                                                                    @elseif($value->email == $review_progress->review && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->second_review && $reviewer == 1)
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->third_review && $reviewer == 2)  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->fourth_reviewer && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->budget_owner && $reviewer == 4)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->approve && $reviewer == 5)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($review_progress->co_approver == null && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->co_approver && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->final && $reviewer == 7)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                    @endif
                                                                    
                                                                @endif
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                        @if($value->activity_code == 'A001')
                                                                        </strong>Submitted Request</div>
                                                                        @elseif($value->activity_code == 'A007')
                                                                        </strong>Assigned Back Request</div>
                                                                        @elseif($value->activity_code == 'A005')
                                                                        </strong>Resubmitted Request</div>
                                                                        @elseif($value->activity_code == 'A009')
                                                                        </strong>Transferred Request</div>
                                                                        @elseif($value->activity_code == 'A008')
                                                                        </strong>Queried Request</div>
                                                                        @elseif($value->activity_code == 'A003')
                                                                        </strong>Rejected Request</div>
                                                                        @elseif($review_progress->budget_owner == 'NA')
                                                                            @if($review_progress->review == null && $owner_activity == 0)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+5;?>
                                                                            @elseif($review_progress->second_review == null && $owner_activity == 1)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+4;?>
                                                                            @elseif($review_progress->third_review == null && $owner_activity == 2)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+3;?>
                                                                            @elseif($review_progress->fourth_reviewer == null && $owner_activity == 3)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+2;?>
                                                                            @elseif($value->email == $review_progress->review && $owner_activity == 0)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->second_review && $owner_activity == 1)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->third_review && $owner_activity == 2)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->fourth_reviewer && $owner_activity == 3)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->budget_owner && $owner_activity == 4)
                                                                                    </strong>Approved request</div>
                                                                                    <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->approve && $owner_activity == 5)
                                                                                    </strong>Approved Request</div>
                                                                                    <?php $owner_activity++;?> 
                                                                            @elseif($review_progress->co_approver == null && $owner_activity == 6)   
                                                                                </strong>Received Request</div>
                                                                                <?php $owner_activity = $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->co_approver && $owner_activity == 6)
                                                                                </strong>Approved Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->final && $owner_activity == 7)
                                                                                </strong>Received Request</div>
                                                                            @else
                                                                                </strong>Reviewed Request</div>
                                                                            @endif
                                                                        @else 
                                                                            @if($review_progress->review == null && $owner_activity == 0)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+5;?>
                                                                            @elseif($review_progress->second_review == null && $owner_activity == 1)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+4;?>
                                                                            @elseif($review_progress->third_review == null && $owner_activity == 2)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+3;?>
                                                                            @elseif($review_progress->fourth_reviewer == null && $owner_activity == 3)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+2;?>
                                                                            @elseif($value->email == $review_progress->review && $owner_activity == 0)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->second_review && $owner_activity == 1)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->third_review && $owner_activity == 2)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->fourth_reviewer && $owner_activity == 3)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->budget_owner && $owner_activity == 4)
                                                                                    </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->approve && $owner_activity == 5)
                                                                                    </strong>Approved Request</div>
                                                                                    <?php $owner_activity++;?> 
                                                                            @elseif($review_progress->co_approver == null && $owner_activity == 6)   
                                                                                </strong>Received Request</div>
                                                                                <?php $owner_activity = $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->co_approver && $owner_activity == 6)
                                                                                </strong>Approved Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->final && $owner_activity == 7)
                                                                                </strong>Received Request</div>
                                                                            @else
                                                                                </strong>Reviewed Request</div>
                                                                            @endif
                                                                        
                                                                        @endif
                                                                    
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Comment:
                                                                    </strong>{{ $value->comment }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
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
                                        PROCUREMENT REQUEST
                                    </div>
                                    <?php $owner_name = 0;$owner_activity = 0; $reviewer=0?>
                                    @foreach ($auditlog as $value)
                                    <div class="timeline-section">
                                            <div class="timeline-date" style="background: #3D9CDD">
                                                {{ $value->datetime }}
                                            </div>
                                            @if(!empty($value->doer_role))
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="timeline-box bg-c-lite-green">
                                                            <div class="box-title"
                                                                    style="font-weight: bold;color: white;font-size: 16px;">
                                                                    <i class="fa fa-user"></i> {{$value->doer_role}}: {{ $value->name }}
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                        </strong>{{$value->doer_action}}
                                                                </div>
                                                                <div class="box-item" style="color: white"><strong
                                                                            style="color: white">Comment:
                                                                        </strong>{{ $value->comment }}
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="timeline-box bg-c-lite-green">
                                                        <div class="box-title"
                                                                style="font-weight: bold;color: white;font-size: 16px;">
                                                                @if($value->activity_code == 'A001' )
                                                                    <i class="fa fa-user"></i> Requester: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A005')
                                                                <i class="fa fa-user"></i> Requester: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A009')
                                                                    <i class="fa fa-user"></i> Transfer By: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A007')
                                                                    @if($value->step_action)
                                                                        @if($value->email == $review_progress->review and $value->step_action < 2)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review and $value->step_action < 3)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review and $value->step_action < 4)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer and $value->step_action < 5)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner and $value->step_action < 6)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->final)
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                        @endif
                                                                    @else
                                                                        @if($value->email == $review_progress->review)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->final)
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                        @endif
                                                                    @endif
                                                                @elseif($value->activity_code == 'A003')
                                                                    @if($value->email == $review_progress->review )
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->second_review )
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->third_review )  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                    @elseif($value->email == $review_progress->fourth_reviewer )
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                    @elseif($value->email == $review_progress->budget_owner)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->approve)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->co_approver)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @endif
                                                                @elseif($value->activity_code == 'A008')
                                                                    @if($value->step_action)
                                                                        @if($value->email == $review_progress->review and $value->step_action < 2)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review and $value->step_action < 3)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review and $value->step_action < 4)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer and $value->step_action < 5)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner and $value->step_action < 6)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $top['req_email'] and $value->step_action < 8)
                                                                            <i class="fa fa-user"></i>Requester: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @endif
                                                                    @else
                                                                        @if($value->email == $review_progress->review )
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review )
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review )  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer )
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @endif
                                                                    @endif
                                                                @elseif($review_progress->budget_owner == 'NA')
                                                                    @if($review_progress->review == null && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+5;?>
                                                                    @elseif($review_progress->second_review == null && $reviewer == 1)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+4;?>
                                                                    @elseif($review_progress->third_review == null && $reviewer == 2)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+3;?>
                                                                    @elseif($review_progress->fourth_reviewer == null && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+2;?>
                                                                    @elseif($value->email == $review_progress->review && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->second_review && $reviewer == 1)
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->third_review && $reviewer == 2)  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->fourth_reviewer && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->budget_owner && $reviewer == 4)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->approve && $reviewer == 5)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($review_progress->co_approver == null && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->co_approver && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->final && $reviewer == 7)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                    @endif
                                                                @else
                                                                    @if($review_progress->review == null && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+5;?>
                                                                    @elseif($review_progress->second_review == null && $reviewer == 1)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+4;?>
                                                                    @elseif($review_progress->third_review == null && $reviewer == 2)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+3;?>
                                                                    @elseif($review_progress->fourth_reviewer == null && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+2;?>
                                                                    @elseif($value->email == $review_progress->review && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->second_review && $reviewer == 1)
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->third_review && $reviewer == 2)  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->fourth_reviewer && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->budget_owner && $reviewer == 4)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->approve && $reviewer == 5)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($review_progress->co_approver == null && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->co_approver && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->final && $reviewer == 7)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                    @endif
                                                                    
                                                                @endif
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                        @if($value->activity_code == 'A001')
                                                                        </strong>Submitted Request</div>
                                                                        @elseif($value->activity_code == 'A007')
                                                                        </strong>Assigned Back Request</div>
                                                                        @elseif($value->activity_code == 'A005')
                                                                        </strong>Resubmitted Request</div>
                                                                        @elseif($value->activity_code == 'A009')
                                                                        </strong>Transferred Request</div>
                                                                        @elseif($value->activity_code == 'A008')
                                                                        </strong>Queried Request</div>
                                                                        @elseif($value->activity_code == 'A003')
                                                                        </strong>Rejected Request</div>
                                                                        @elseif($review_progress->budget_owner == 'NA')
                                                                            @if($review_progress->review == null && $owner_activity == 0)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+5;?>
                                                                            @elseif($review_progress->second_review == null && $owner_activity == 1)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+4;?>
                                                                            @elseif($review_progress->third_review == null && $owner_activity == 2)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+3;?>
                                                                            @elseif($review_progress->fourth_reviewer == null && $owner_activity == 3)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+2;?>
                                                                            @elseif($value->email == $review_progress->review && $owner_activity == 0)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->second_review && $owner_activity == 1)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->third_review && $owner_activity == 2)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->fourth_reviewer && $owner_activity == 3)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->budget_owner && $owner_activity == 4)
                                                                                    </strong>Approved request</div>
                                                                                    <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->approve && $owner_activity == 5)
                                                                                    </strong>Approved Request</div>
                                                                                    <?php $owner_activity++;?> 
                                                                            @elseif($review_progress->co_approver == null && $owner_activity == 6)   
                                                                                </strong>Received Request</div>
                                                                                <?php $owner_activity = $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->co_approver && $owner_activity == 6)
                                                                                </strong>Approved Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->final && $owner_activity == 7)
                                                                                </strong>Received Request</div>
                                                                            @else
                                                                                </strong>Reviewed Request</div>
                                                                            @endif
                                                                        @else 
                                                                            @if($review_progress->review == null && $owner_activity == 0)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+5;?>
                                                                            @elseif($review_progress->second_review == null && $owner_activity == 1)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+4;?>
                                                                            @elseif($review_progress->third_review == null && $owner_activity == 2)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+3;?>
                                                                            @elseif($review_progress->fourth_reviewer == null && $owner_activity == 3)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+2;?>
                                                                            @elseif($value->email == $review_progress->review && $owner_activity == 0)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->second_review && $owner_activity == 1)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->third_review && $owner_activity == 2)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->fourth_reviewer && $owner_activity == 3)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->budget_owner && $owner_activity == 4)
                                                                                    </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->approve && $owner_activity == 5)
                                                                                    </strong>Approved Request</div>
                                                                                    <?php $owner_activity++;?> 
                                                                            @elseif($review_progress->co_approver == null && $owner_activity == 6)   
                                                                                </strong>Received Request</div>
                                                                                <?php $owner_activity = $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->co_approver && $owner_activity == 6)
                                                                                </strong>Approved Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->final && $owner_activity == 7)
                                                                                </strong>Received Request</div>
                                                                            @else
                                                                                </strong>Reviewed Request</div>
                                                                            @endif
                                                                        
                                                                        @endif
                                                                    
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Comment:
                                                                    </strong>{{ $value->comment }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
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
                                        PROCUREMENT REQUEST
                                    </div>
                                    <?php $owner_name = 0;$owner_activity = 0;$reviewer = 0;?>
                                    @foreach ($auditlog as $value)
                                    <div class="timeline-section">
                                            <div class="timeline-date" style="background: #3D9CDD">
                                                {{ $value->datetime }}
                                            </div>
                                            @if(!empty($value->doer_role))
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="timeline-box bg-c-lite-green">
                                                            <div class="box-title"
                                                                    style="font-weight: bold;color: white;font-size: 16px;">
                                                                    <i class="fa fa-user"></i> {{$value->doer_role}}: {{ $value->name }}
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                        </strong>{{$value->doer_action}}
                                                                </div>
                                                                <div class="box-item" style="color: white"><strong
                                                                            style="color: white">Comment:
                                                                        </strong>{{ $value->comment }}
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="timeline-box bg-c-lite-green">
                                                            <div class="box-title"
                                                                style="font-weight: bold;color: white;font-size: 16px;">
                                                                @if($value->activity_code == 'A001' )
                                                                    <i class="fa fa-user"></i> Requester: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A005')
                                                                <i class="fa fa-user"></i> Requester: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A009')
                                                                    <i class="fa fa-user"></i> Transfer By: {{ $value->name }}
                                                                @elseif($value->activity_code == 'A007')
                                                                    @if($value->step_action)
                                                                        @if($value->email == $review_progress->review and $value->step_action < 2)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review and $value->step_action < 3)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review and $value->step_action < 4)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer and $value->step_action < 5)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner and $value->step_action < 6)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->final)
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                        @endif
                                                                    @else
                                                                        @if($value->email == $review_progress->review)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->final)
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                        @endif
                                                                    @endif
                                                                @elseif($value->activity_code == 'A003')
                                                                    @if($value->email == $review_progress->review )
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->second_review )
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->third_review )  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                    @elseif($value->email == $review_progress->fourth_reviewer )
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                    @elseif($value->email == $review_progress->budget_owner)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->approve)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    @elseif($value->email == $review_progress->co_approver)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @endif
                                                                @elseif($value->activity_code == 'A008')
                                                                    @if($value->step_action)
                                                                        @if($value->email == $review_progress->review and $value->step_action < 2)
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review and $value->step_action < 3)
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review and $value->step_action < 4)  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer and $value->step_action < 5)
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner and $value->step_action < 6)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @elseif($value->email == $top['req_email'] and $value->step_action < 8)
                                                                            <i class="fa fa-user"></i>Requester: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @endif
                                                                    @else
                                                                        @if($value->email == $review_progress->review )
                                                                            <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->second_review )
                                                                            <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->third_review )  
                                                                            <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->fourth_reviewer )
                                                                            <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        @elseif($value->email == $review_progress->budget_owner)
                                                                            <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->approve)
                                                                            <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                        @elseif($value->email == $review_progress->co_approver)
                                                                            <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                        @else
                                                                            <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        @endif
                                                                    @endif
                                                                @elseif($review_progress->budget_owner == 'NA')
                                                                    @if($review_progress->review == null && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+5;?>
                                                                    @elseif($review_progress->second_review == null && $reviewer == 1)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+4;?>
                                                                    @elseif($review_progress->third_review == null && $reviewer == 2)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+3;?>
                                                                    @elseif($review_progress->fourth_reviewer == null && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+2;?>
                                                                    @elseif($value->email == $review_progress->review && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->second_review && $reviewer == 1)
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->third_review && $reviewer == 2)  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->fourth_reviewer && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->budget_owner && $reviewer == 4)
                                                                    <i class="fa fa-user"></i>CFO: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->approve && $reviewer == 5)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($review_progress->co_approver == null && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->co_approver && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->final && $reviewer == 7)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                    @endif
                                                                @else
                                                                    @if($review_progress->review == null && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+5;?>
                                                                    @elseif($review_progress->second_review == null && $reviewer == 1)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+4;?>
                                                                    @elseif($review_progress->third_review == null && $reviewer == 2)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+3;?>
                                                                    @elseif($review_progress->fourth_reviewer == null && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer+2;?>
                                                                    @elseif($value->email == $review_progress->review && $reviewer == 0)
                                                                        <i class="fa fa-user"></i>First Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->second_review && $reviewer == 1)
                                                                        <i class="fa fa-user"></i>Second Reviewer: {{ $value->name }}
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->third_review && $reviewer == 2)  
                                                                        <i class="fa fa-user"></i>Third Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->fourth_reviewer && $reviewer == 3)
                                                                        <i class="fa fa-user"></i>Forth Reviewer: {{ $value->name }} 
                                                                        <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->budget_owner && $reviewer == 4)
                                                                    <i class="fa fa-user"></i>Budget Owner: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->approve && $reviewer == 5)
                                                                    <i class="fa fa-user"></i>Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($review_progress->co_approver == null && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                        <?php $reviewer = $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->co_approver && $reviewer == 6)
                                                                    <i class="fa fa-user"></i>Co-Approver: {{ $value->name }}
                                                                    <?php $reviewer++;?>
                                                                    @elseif($value->email == $review_progress->final && $reviewer == 7)
                                                                    <i class="fa fa-user"></i>Procure by: {{ $value->name }}
                                                                    @else
                                                                    <i class="fa fa-user"></i>Reviewer: {{ $value->name }}
                                                                    @endif
                                                                    
                                                                @endif
                                                            </div>
                                                            <div class="box-content bg-c-lite-green">
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Activity:
                                                                        @if($value->activity_code == 'A001')
                                                                        </strong>Submitted Request</div>
                                                                        @elseif($value->activity_code == 'A007')
                                                                        </strong>Assigned Back Request</div>
                                                                        @elseif($value->activity_code == 'A005')
                                                                        </strong>Resubmitted Request</div>
                                                                        @elseif($value->activity_code == 'A009')
                                                                        </strong>Transferred Request</div>
                                                                        @elseif($value->activity_code == 'A008')
                                                                        </strong>Queried Request</div>
                                                                        @elseif($value->activity_code == 'A003')
                                                                        </strong>Rejected Request</div>
                                                                        @elseif($review_progress->budget_owner == 'NA')
                                                                            @if($review_progress->review == null && $owner_activity == 0)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+5;?>
                                                                            @elseif($review_progress->second_review == null && $owner_activity == 1)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+4;?>
                                                                            @elseif($review_progress->third_review == null && $owner_activity == 2)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+3;?>
                                                                            @elseif($review_progress->fourth_reviewer == null && $owner_activity == 3)
                                                                                </strong>Approved request</div>
                                                                                    <?php $owner_activity =  $owner_activity+2;?>
                                                                            @elseif($value->email == $review_progress->review && $owner_activity == 0)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->second_review && $owner_activity == 1)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->third_review && $owner_activity == 2)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->fourth_reviewer && $owner_activity == 3)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->budget_owner && $owner_activity == 4)
                                                                                    </strong>Approved request</div>
                                                                                    <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->approve && $owner_activity == 5)
                                                                                    </strong>Approved Request</div>
                                                                                    <?php $owner_activity++;?> 
                                                                            @elseif($review_progress->co_approver == null && $owner_activity == 6)   
                                                                                </strong>Received Request</div>
                                                                                <?php $owner_activity = $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->co_approver && $owner_activity == 6)
                                                                                </strong>Approved Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->final && $owner_activity == 7)
                                                                                </strong>Received Request</div>
                                                                            @else
                                                                                </strong>Reviewed Request</div>
                                                                            @endif
                                                                        @else 
                                                                            @if($review_progress->review == null && $owner_activity == 0)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+5;?>
                                                                            @elseif($review_progress->second_review == null && $owner_activity == 1)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+4;?>
                                                                            @elseif($review_progress->third_review == null && $owner_activity == 2)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+3;?>
                                                                            @elseif($review_progress->fourth_reviewer == null && $owner_activity == 3)
                                                                                </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity =  $owner_activity+2;?>
                                                                            @elseif($value->email == $review_progress->review && $owner_activity == 0)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->second_review && $owner_activity == 1)
                                                                            </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->third_review && $owner_activity == 2)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->fourth_reviewer && $owner_activity == 3)
                                                                                </strong>Reviewed Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->budget_owner && $owner_activity == 4)
                                                                                    </strong>Approved on Budget code</div>
                                                                                    <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->approve && $owner_activity == 5)
                                                                                    </strong>Approved Request</div>
                                                                                    <?php $owner_activity++;?> 
                                                                            @elseif($review_progress->co_approver == null && $owner_activity == 6)   
                                                                                </strong>Received Request</div>
                                                                                <?php $owner_activity = $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->co_approver && $owner_activity == 6)
                                                                                </strong>Approved Request</div>
                                                                                <?php $owner_activity++;?>
                                                                            @elseif($value->email == $review_progress->final && $owner_activity == 7)
                                                                                </strong>Received Request</div>
                                                                            @else
                                                                                </strong>Reviewed Request</div>
                                                                            @endif
                                                                        
                                                                        @endif
                                                                    
                                                                <div class="box-item" style="color: white"><strong
                                                                        style="color: white">Comment:
                                                                    </strong>{{ $value->comment }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="tab-pane" id="profile1" role="tabpanel" aria-expanded="false">
                            <table class="table table-hover">
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
                                            <td>
                                                <a href="{{ url('download/' . $value->uuid) }}">
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
        // Initiate an Ajax request on button click
        // $(document).on("click", "button", function(){
        //     // Adding timestamp to set cache false
        //     $.get("/examples/php/customers.php?v="+ $.now(), function(data){
        //         $("body").html(data);
        //     });       
        // });

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
        $('#transfer_selected').on('change', function() {
            var select_cond = $(this).val()
            // alert(select_cond)
            if (select_cond !== '') {
                $('#received').hide()
                $('#transfer').show()
            } else {
                $('#received').show()
                $('#transfer').hide()
            }
        });
    </script>
    <script>
        $(function() {
            $('.chb_1').click(function() {
                var checked = $(this).val()
                // alert(checked)  
                if (checked == 'Yes') {
                    // $('.tr_transfer').show()
                    $('#received').hide()
                    $('#transfer').show()
                } else {
                    // $('.tr_transfer').hide()
                    $('#received').show()
                    $('#transfer').hide()
                }
            });
        });
    </script>
    <!-- for button pdf -->
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
    <!-- end  -->
    <!-- alert blink text -->
    <script>
        $("input:checkbox").on('click', function() {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
                // the name of the box is retrieved using the .attr() method
                // as it is assumed and expected to be immutable
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                // the checked state of the group/box on the other hand will change
                // and the current value is retrieved using .prop() method
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });
    </script>


@endsection
