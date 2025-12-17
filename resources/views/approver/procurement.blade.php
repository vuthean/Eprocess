@extends('layouts.master')
@section('style')
<style>
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

    .error {
        color: red;
        /*border-color: red;*/
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
@endsection
@section('menu')
@include('siderbar.procurement')
@endsection
@section('breadcrumb')
@include('breadcrumb.procurement_new')
@endsection
@section('content')


<div class="col-md-12">

    <form id="registration" method="post" action="{{ route('form/procurement/save') }}" name="registration" enctype="multipart/form-data">
        {{ csrf_field() }}
        <!-- Page-body start -->

        <!-- Page-body start -->
        <div class="page-body">
            <!-- DOM/Jquery table start -->
            <div class="card">
                <div class="card-block">
                    <h4 class="sub-title">Procurement Request Detail </h4>
                    <div class="row">
                        <div class="col-sm-8 mobile-inputs">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Department :</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="department" id="department" value="{{ $top['req_branch'] }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="">Request Date:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <input readonly type="text" class="form-control" id="requestDate" name="requestDate" value="{{ $top_mid['req_date'] }}">
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
                                            <input type="text" class="form-control" id="expDate" name="expDate" value="{{ \Carbon\Carbon::parse($top['due_expect_date'])->format('d/m/Y') }}">
                                            @else
                                            <input type="text" class="form-control" id="expDate" name="expDate" value="N/A">
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
                                    <textarea class="form-control" id="refNumber" name="refNumber">{{ $top['ref'] }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Subject :</label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="subject" name="subject">{{ $top['subject'] }}</textarea>
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
                                    <input readonly type="text" class="form-control" id="pr_ref_no" name="pr_ref_no" value="{{ $top['req_recid'] }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="">Currency:</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="currency" name="currency" value="{{ $top['ccy'] }}">
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
                            <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#procurementbody-Modal" style="float: right" id="btn_add_new">Add</button>

                            <table class="table table-striped table-bordered" id="example-1" style="margin-top: 50px !important;">
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
                                    </tr>
                                </thead>
                                <tbody>

                                    @if (count($body) > 0)
                                    <?php
                                    $total = 0;
                                    $total_khr = 0;
                                    $total_vat =0;
                                    ?>
                                    @foreach ($body as $key => $value)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td class="tabledit-view-mode">
                                            <a href="#" class="edit_item" data-toggle="modal" data-target="#procurementbody-Modal" data-req_recid="{{ $value->req_recid }}" data-req_description="{{ $value->description }}" data-department="{{ $value->br_dep_code }}" data-budgetcode="{{ $value->budget_code }}" data-alternativebudget="{{ $value->alternativebudget_code }}" data-unit="{{ $value->unit }}" data-qty="{{ $value->qty }}" data-unit_price="{{ $value->unit_price }}" data-unit_price_khr="{{ $value->unit_price_khr }}" data-total_estimate="{{ $value->total_estimate }}" data-total_estimate_khr="{{ $value->total_estimate_khr }}" data-delivery_date="{{ $value->delivery_date }}" data-withinbudget="{{ $value->within_budget_code }}" data-bodyid="{{ $value->id }}" data-within="{{ $value->within_budget_code }}" data-budget_his="{{ $budget_his[$key]['id']  }}">
                                                {{ $value['description'] }}
                                            </a>

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
                                            <span style="color:green;font-size: 14px;font-weight: bold;">YES</span>
                                            @else
                                            <span style="color:red;font-size: 14px;font-weight: bold;">NO</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <?php if ($top['ccy'] == 'KHR') {
                                        $total_khr += $value['total_estimate_khr'];
                                    } else {
                                        $total += $value['total_estimate'];
                                    } 
                                    $total_vat = $value['vat']
                                    ?>
                                    
                                    @endforeach
                                    @endif

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
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" style="font-size: 16px; font-weight: bold;" colspan="8">
                                            VAT:</td>
                                        <td id="" style="font-size: 16px; font-weight: bold;">
                                            @if (count($body) > 0)
                                                @if ($top['ccy'] == 'KHR')
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_vat*4000)
                                                @else
                                                        $@money($total_vat)
                                                @endif   
                                            @endif
                                            <input type="hidden" name="" id = "td_vat" value="{{$total_vat}}">
                                        </td>
                                        <td>
                                            <div class="col-sm-2 border-checkbox-section">
                                                <div class="border-checkbox-group border-checkbox-group-primary">
                                                    <input class="border-checkbox chb_2 checkbox_vat" type="checkbox" id="checkbox_vat"
                                                        name="checkbox_vat" value="1">
                                                    <label class="border-checkbox-label" for="checkbox_vat"></label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" style="font-size: 16px; font-weight: bold;" colspan="8">
                                            GRAND TOTAL:</td>
                                        <td style="font-size: 16px; font-weight: bold;">
                                            @if (count($body) > 0)
                                                @if ($top['ccy'] == 'KHR')
                                                        <input type="hidden" name="grand_total" value="{{$total_khr + ($total_vat*4000)}}">
                                                        <span style="font-size: 18px;">៛</span>
                                                        @money($total_khr + ($total_vat*4000))
                                                @else
                                                        $@money($total+$total_vat)
                                                        <input type="hidden" name="grand_total" value="{{$total+$total_vat}}">
                                                @endif   
                                            @endif
                                        </td>
                                        <td></td>
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
                    <span id="tbl1_procurement_error"></span>
                    <div class="row form-group">
                        <div class="col-sm-3 mobile-inputs">
                            <label for="">PURPOSE / RATIONALE:</label>
                        </div>
                        <div class="col-sm-9 mobile-inputs">
                            <textarea class="form-control" id="purpose_rationale" name="purpose_rationale">{{ $top_mid['purpose'] }}</textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-3 mobile-inputs">
                            <label for="">BID WAIVER/SOLE SOURCE REQUEST:</label>
                        </div>
                        <div class="col-sm-9 mobile-inputs">
                            <select id="bid_waiver_sole" name="bid_waiver_sole" style="min-width: 80px; margin-bottom: 0px;" class="form-control bid_waiver_sole">
                                        @if($top_mid['bid'] == 'yes')
                                        <option value="{{$top_mid['bid']}}">{{Str::upper($top_mid['bid'])}}</option>
                                        <option value="no">NO</option>
                                        @elseif($top_mid['bid'] == 'no')
                                        <option value="{{$top_mid['bid']}}">{{Str::upper($top_mid['bid'])}}</option>
                                        <option value="yes">YES</option>
                                        @else
                                            <option value="{{$top_mid['bid']}}">{{Str::upper($top_mid['bid'])}}</option>
                                            <option value="no">NO</option>
                                            <option value="yes">YES</option>
                                        @endif
                                </select>
                            <!-- <input readonly type="text" class="form-control bid_waiver_sole" id="bid_waiver_sole" name="bid_waiver_sole" value="{{ $top_mid['bid'] }}"> -->
                        </div>
                    </div>
                    @if($top_mid['bid'] == 'yes' or $top_mid['bid'] != 'no')
                    <?php
                        if($footer->count() == 1 and $footer[0]['vender_name'] == null){
                            $is_null = 1;
                        }else{
                            $is_null = 0;
                        }
                    ?>
                    <div class="card-block" id="vendor_bloch">
                        <div class="table-responsive dt-responsive">
                            <div>
                                <label for="">Recommended vendor(s)</label>
                                <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#vendor-add-Modal" style="float: right">Add</button> -->
                                <button type="button" class="btn btn-primary" data-vender_id="{{$footer->count()}}" data-is_null="{{$is_null}}" id="add_vender_more" style="float: right">Add</button>
                            </div>
                            <span hidden class="text-danger" id="show_error_vendor_name"><i>Vendor Name and Justification can not empty!</i></span>
                            <table class="table table-striped table-bordered" id="example-3">
                                <thead>
                                    <tr class="table-info">
                                        <th>No</th>
                                        <th>Vendor Name</th>
                                        <th>Justification</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_vender">
                                    @if ($footer->count() <= 0 or ($footer->count() == 1 and $footer[0]['vender_name'] == null) )
                                    <tr hidden id="id_no_record">
                                        <td>1</td>
                                        <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm" id="vendor_name"
                                                    name="vendor_name[]" cols="30" rows="1"></textarea>
                                            </td>
                                            <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm"
                                                    id="vendor_description" name="vendor_description[]" cols="30"
                                                    rows="1"></textarea>
                                            </td>
                                    </tr>
                                    @else
                                    @foreach ($footer as $key => $value)
                                    @if($value->vender_name)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm" id="vendor_name"
                                                    name="vendor_name[]" cols="30" rows="1">{{ $value->vender_name }}</textarea>
                                            </td>
                                            <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm"
                                                    id="vendor_description" name="vendor_description[]" cols="30"
                                                    rows="1"> {{ $value->description }}</textarea>
                                            </td>
                                            <td class="tabledit-view-mode">
                                                <i class="fa fa-times remove_vender_more" id="remove_vender_more" data-id="{{ $value->id }}" style="font-size: 20px;color: red">
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
                    @else
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-block">
                    <div class="card-block">
                        <div class="table-responsive dt-responsive">
                            <span id="product_error" style="color: red;font-size: 15px; font-weight: bold;"></span>
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
                                        <td class="table-success" rowspan="2" style="text-align: left;"><b>Type :</b>
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
                                        <td class="table-success" style="text-align: left;"><b>Code :</b></td>
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
                                                {{ $bottom['general'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="general" id="general-text" onpaste="return false;" value="{{ $bottom['general'] ?? '0' }}">

                                        </td>
                                        <td align="center" class="content" data-value="loan_general">
                                            <div id="loan_general-div" style="width: 70px">
                                                {{ $bottom['loan_general'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="loan_general" id="loan_general-text" onpaste="return false;" value="{{ $bottom['loan_general'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="mortgage">
                                            <div id="mortgage-div" style="width: 70px">
                                                {{ $bottom['mortage'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="mortgage" id="mortgage-text" onpaste="return false;" value="{{ $bottom['mortage'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="business">
                                            <div id="business-div" style="width: 70px">
                                                {{ $bottom['busines'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="business" id="business-text" onpaste="return false;" value="{{ $bottom['busines'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="personal">
                                            <div id="personal-div" style="width: 70px">
                                                {{ $bottom['personal'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="personal" id="personal-text" onpaste="return false;" value="{{ $bottom['personal'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="card_general">
                                            <div id="card_general-div" style="width: 70px">
                                                {{ $bottom['card_general'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="card_general" id="card_general-text" onpaste="return false;" value="{{ $bottom['card_general'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="debit_card">
                                            <div id="debit_card-div" style="width: 70px">
                                                {{ $bottom['debit_card'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="debit_card" id="debit_card-text" onpaste="return false;" value="{{ $bottom['debit_card'] ?? '0' }}">
                                        </td>

                                        <td align="center" class="content" data-value="credit_card">
                                            <div id="credit_card-div" style="width: 70px">
                                                {{ $bottom['credit_card'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="credit_card" id="credit_card-text" onpaste="return false;" value="{{ $bottom['credit_card'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="trade_general">
                                            <div id="trade_general-div" style="width: 70px">
                                                {{ $bottom['trade_general'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="trade_general" id="trade_general-text" onpaste="return false;" value="{{ $bottom['trade_general'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="bank_guarantee">
                                            <div id="bank_guarantee-div" style="width: 70px">
                                                {{ $bottom['bank_guarantee'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="bank_guarantee" id="bank_guarantee-text" onpaste="return false;" value="{{ $bottom['bank_guarantee'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="letter_of_credit">
                                            <div id="letter_of_credit-div" style="width: 70px">
                                                {{ $bottom['letter_of_credit'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="letter_of_credit" id="letter_of_credit-text" onpaste="return false;" value="{{ $bottom['letter_of_credit'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="deposit_general">
                                            <div id="deposit_general-div" style="width: 70px">
                                                {{ $bottom['deposit_general'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="deposit_general" id="deposit_general-text" onpaste="return false;" value="{{ $bottom['deposit_general'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="casa_individual">
                                            <div id="casa_individual-div" style="width: 70px">
                                                {{ $bottom['casa_individual'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="casa_individual" id="casa_individual-text" onpaste="return false;" value="{{ $bottom['casa_individual'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="td_individual">
                                            <div id="td_individual-div" style="width: 70px">
                                                {{ $bottom['td_individual'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="td_individual" id="td_individual-text" onpaste="return false;" value="{{ $bottom['td_individual'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="casa_corporate">
                                            <div id="casa_corporate-div" style="width: 70px">
                                                {{ $bottom['casa_corporate'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="casa_corporate" id="casa_corporate-text" onpaste="return false;" value="{{ $bottom['casa_corporate'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="td_corporate">
                                            <div id="td_corporate-div" style="width: 70px">
                                                {{ $bottom['td_corporate'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers txt product" style="width: 50px !important;" type="hidden" name="td_corporate" id="td_corporate-text" onpaste="return false;" value="{{ $bottom['td_corporate'] ?? '0' }}">
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
                                        <td class="table-success" style="text-align: left;"><b>Code :</b></td>
                                        <td>999</td>
                                        <td>100</td>
                                        <td>200</td>
                                        <td>300</td>
                                        <td>400</td>
                                        <td>500</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;" class="table-success"><b>Allocated % :</b></td>
                                        <td align="center" class="content" data-value="sagement_general">
                                            <input type="hidden" id="total-segment" value="100">
                                            <div id="sagement_general-div" style="width: 70px">
                                                {{ $bottom['sagement_general'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers counter-segment" type="hidden" style="width: 150px !important;" name="general_segment" id="sagement_general-text" onpaste="return false;" value="{{ $bottom['sagement_general'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="sagement_bfs">
                                            <div id="sagement_bfs-div" style="width: 70px">
                                                {{ $bottom['sagement_bfs'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers counter-segment" type="hidden" style="width: 150px !important;" name="bfs" id="sagement_bfs-text" onpaste="return false;" value="{{ $bottom['sagement_bfs'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="sagement_rfs">
                                            <div id="sagement_rfs-div" style="width: 70px">
                                                {{ $bottom['sagement_rfs'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers counter-segment" type="hidden" style="width: 150px !important;" name="rfs" id="sagement_rfs-text" onpaste="return false;" value="{{ $bottom['sagement_rfs'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="sagement_pb">
                                            <div id="sagement_pb-div" style="width: 70px">
                                                {{ $bottom['sagement_pb'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers counter-segment" type="hidden" style="width: 150px !important;" name="pb" id="sagement_pb-text" onpaste="return false;" value="{{ $bottom['sagement_pb'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="sagement_pcp">
                                            <div id="sagement_pcp-div" style="width: 70px">
                                                {{ $bottom['sagement_pcp'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers counter-segment" type="hidden" style="width: 150px !important;" name="pcp" id="sagement_pcp-text" onpaste="return false;" value="{{ $bottom['sagement_pcp'] ?? '0' }}">
                                        </td>
                                        <td align="center" class="content" data-value="sagement_afs">
                                            <div id="sagement_afs-div" style="width: 70px">
                                                {{ $bottom['sagement_afs'] ?? '0' }}%
                                            </div>
                                            <input class="form-control numbers counter-segment" type="hidden" style="width: 150px !important;" name="afs" id="sagement_afs-text" onpaste="return false;" value="{{ $bottom['sagement_afs'] ?? '0' }}">
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
                                    @if($top_mid['justification'])
                                        <textarea readonly="" name="remarks_product_segment"class="form-control ">{!! nl2br(e($bottom['remarks'] ))."\r\n"."Justification for Sole Source Request: ".$top_mid['justification']  !!}</textarea>
                                    @else
                                        <textarea name="remarks_product_segment"class="form-control ">{{ $bottom['remarks']   }}</textarea>
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
                    @if ($query == '1')
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#query" role="tab" aria-expanded="true">ACTION</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab" aria-expanded="true">ACTIVITY LOG</a>
                    </li>
                    @endif
                    @if ($requester == '1' or $review == '1' or $approve == '1')
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home1" role="tab" aria-expanded="true">ACTION</a>
                    </li>


                    @endif
                    @if (!empty($condition_view))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#activitylog" role="tab" aria-expanded="true">ACTIVITY LOG</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile1" role="tab" aria-expanded="false">REFERENCE DOCUMENT</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs card-block">
                    <span id="approve_review"></span>
                    @if ($requester == '1')
                    <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                        <div class="table-responsive">
                            <table class="table" style="width: 100%" id="multi-reviewer">
                                <thead>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            First Reviewer
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <select class="js-example-basic-single slc_review" name="slc_review">
                                                <option selected="" disabled="" value="">Select One</option>
                                                <option value="">Skip Reviewer</option>
                                                @foreach ($group_requester as $value)
                                                <option value="{{ $value->email . '/' . $value->role_id }}">
                                                    {{ $value->firstname }} {{ $value->lastname }}
                                                </option>
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
                            @if($total_spent < 30000 && $many_review=='Y' ) 
                            <table class="table" style="margin-top: -1.3%;width: 100%">
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        Accounting Reviewer
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <input readonly type="text" class="form-control" name="accounting_team" id="accounting_team" value="Accounting">
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        Procurement Reviewer
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <input readonly type="text" class="form-control" name="procurement_team_name" id="procurement_team_name">
                                        <input type="hidden" class="form-control" name="procurement_team" id="procurement_team">
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        Budget Owner
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $budgetOwner->firstname }} {{ $budgetOwner->lastname }}">
                                        <div hidden>
                                            <select class="js-example-basic-single" name="slc_cfo">
                                                <option value="{{ $budgetOwner->email . '/' . $budgetOwner->role_id }}">
                                                    {{ $budgetOwner->firstname }} {{ $budgetOwner->lastname }}
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                @if($top_mid['bid'] == 'yes' and $total_spent > 7500 )
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        CEO Office
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $ceoOffice->firstname }} {{ $ceoOffice->lastname }}">
                                        <div hidden>
                                            <select class="js-example-basic-single" name="slc_ceo_office">
                                                <option value="{{ $ceoOffice->email . '/' . $ceoOffice->role_id }}">
                                                    {{ $ceoOffice->firstname }} {{ $ceoOffice->lastname }}
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        Approver
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $ceo->firstname }} {{ $ceo->lastname }}">
                                        <div hidden>
                                            <select class="js-example-basic-single" name="slc_approve">
                                                <option value="{{ $ceo->email . '/' . $ceo->role_id }}">{{ $ceo->firstname }} {{ $ceo->lastname }}</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        <span style="color: red;">*</span> Procure By
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <select class="js-example-basic-single" name="slc_final" id='slc_final'>
                                            <option selected="" disabled="" value="">Select One</option>
                                            @foreach ($group_final as $value)
                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                {{ $value->firstname }} {{ $value->lastname }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                @else

                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        <span style="color: red;">*</span> Approver
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <select class="js-example-basic-single" name="slc_approve" id="slc_approve">
                                            <option selected="" disabled="" value="">Select One</option>
                                            @foreach ($group_approver as $value)
                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                {{ $value->firstname }} {{ $value->lastname }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                @if($top_mid['bid'] == 'yes')
                                <tr id="co_approver">
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        <span style="color: red;">*</span> Co-Approver
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <select class="js-example-basic-single" name="slc_approve_co" id="slc_approve_co">
                                            <option selected="" disabled="" value="">Select One</option>
                                            @foreach ($group_approver_co as $value)
                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                {{ $value->firstname }} {{ $value->lastname }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        <span style="color: red;">*</span> Procure By
                                    </td>
                                    <td style="border: none !important; width: 75%">
                                        <select class="js-example-basic-single" name="slc_final" id='slc_final'>
                                            <option selected="" disabled="" value="">Select One</option>
                                            @foreach ($group_final as $value)
                                            <option value="{{ $value->email . '/' . $value->role_id }}">
                                                {{ $value->firstname }} {{ $value->lastname }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="border: none !important;width: 5%">
                                    </td>
                                </tr>
                                @endif

                                <tr>
                                    <td style="text-align: right;border: none !important;">Comment</td>
                                    <td style="border: none !important; ">
                                        <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none !important;">

                                    </td>
                                    <td style="border: none !important;">
                                        <button type="submit" name="submit" id="submit_save" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                            Submit</button>
                                        <button type="button" name="submit" id="delete_btn" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-trash" aria-hidden="true" style="color: red"></i>
                                            Delete</button>
                                    </td>
                                </tr>
                                </table>
                                @endif
                                @if($total_spent >= 15000 && $many_review == 'Y')
                                <table class="table" style="margin-top: -1.3%;width: 100%">
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            Accounting Reviewer
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="accounting_team" id="accounting_team" value="Accounting">
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            Procurement Reviewer
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="procurement_team_name" id="procurement_team_name">
                                            <input type="hidden" class="form-control" name="procurement_team" id="procurement_team">
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            Budget Owner
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $budgetOwner->firstname }} {{ $budgetOwner->lastname }}">
                                            <div hidden>
                                                <select class="js-example-basic-single" name="slc_cfo" style="display: none;">
                                                    <option value="{{ $budgetOwner->email . '/' . $budgetOwner->role_id }}">
                                                        {{ $budgetOwner->firstname }} {{ $budgetOwner->lastname }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            CEO Office
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $ceoOffice->firstname }} {{ $ceoOffice->lastname }}">
                                            <div hidden>
                                                <select class="js-example-basic-single" name="slc_ceo_office">
                                                    <option value="{{ $ceoOffice->email . '/' . $ceoOffice->role_id }}">
                                                        {{ $ceoOffice->firstname }} {{ $ceoOffice->lastname }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            Approver
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="budgetOwner" value="{{ $ceo->firstname }} {{ $ceo->lastname }}">
                                            <div hidden>
                                                <select class="js-example-basic-single" name="slc_approve">
                                                    <option value="{{ $ceo->email . '/' . $ceo->role_id }}">{{ $ceo->firstname }} {{ $ceo->lastname }}</option>
                                                </select>
                                            </div>

                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            <span style="color: red;">*</span> Procure By
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <select class="js-example-basic-single" name="slc_final" id='slc_final'>
                                                <option selected="" disabled="" value="">Select One</option>
                                                @foreach ($group_final as $value)
                                                <option value="{{ $value->email . '/' . $value->role_id }}">
                                                    {{ $value->firstname }} {{ $value->lastname }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;border: none !important;">Comment</td>
                                        <td style="border: none !important;">
                                            <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none !important;">

                                        </td>
                                        <td style="border: none !important;">
                                            <button type="submit" name="submit" id="submit_save" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                                Submit</button>
                                            <button type="button" name="submit" id="delete_btn" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-trash" aria-hidden="true" style="color: red"></i>
                                                Delete</button>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                                @if($many_review == 'N')
                                <table class="table" style="margin-top: -1.3%;width: 100%">
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            CFO
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="cfo" value="{{ $cfo->firstname }} {{ $cfo->lastname }}">
                                            <div hidden>
                                                <select class="js-example-basic-single" name="slc_cfo">
                                                    <option value="{{ $cfo->email . '/' . $cfo->role_id }}">
                                                        {{ $cfo->firstname }} {{ $cfo->lastname }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            CEO Office
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="cfo_office" value="{{ $ceoOffice->firstname }} {{ $ceoOffice->lastname }}">
                                            <div hidden>
                                                <select class="js-example-basic-single" name="slc_ceo_office">
                                                    <option value="{{ $ceoOffice->email . '/' . $ceoOffice->role_id }}">
                                                        {{ $ceoOffice->firstname }} {{ $ceoOffice->lastname }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            Approver
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <input readonly type="text" class="form-control" name="ceo" value="{{ $ceo->firstname }} {{ $ceo->lastname }}">
                                            <div hidden>
                                                <select class="js-example-basic-single" name="slc_approve">
                                                    <option value="{{ $ceo->email . '/' . $ceo->role_id }}">
                                                        {{ $ceo->firstname }} {{ $ceo->lastname }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;width: 20% !important;border: none !important;">
                                            <span style="color: red;">*</span> Procure By
                                        </td>
                                        <td style="border: none !important; width: 75%">
                                            <select class="js-example-basic-single" name="slc_final" id='slc_final'>
                                                <option selected="" disabled="" value="">Select One</option>
                                                @foreach ($group_final as $value)
                                                <option value="{{ $value->email . '/' . $value->role_id }}">
                                                    {{ $value->firstname }} {{ $value->lastname }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="border: none !important;width: 5%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;border: none !important;">Comment</td>
                                        <td style="border: none !important;">
                                            <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none !important;">

                                        </td>
                                        <td style="border: none !important;">
                                            <button type="submit" name="submit" id="submit_save" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                                Submit</button>
                                            <button type="button" name="submit" id="delete_btn" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-trash" aria-hidden="true" style="color: red"></i>
                                                Delete</button>
                                        </td>
                                    </tr>
                                </table>
                                @endif
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
                                        <textarea class="form-control" name="comment" rows="5" id="comment"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none !important;">

                                    </td>
                                    <td style="border: none !important;">
                                        <button type="submit" name="submit" id="submit_save" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-save" aria-hidden="true" style="color: green"></i>
                                            Re-Submit</button>
                                        <button type="button" name="submit" id="delete_btn" value="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-trash" aria-hidden="true" style="color: red"></i>
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
                                    <td style="text-align: right;width: 20% !important;border: none !important;">
                                        Comment</td>
                                    <td style="border: none !important;">
                                        <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="border: none !important;">

                                    </td>
                                    <td style="border: none !important;">

                                        <button type="submit" name="submit" value="query" class="btn_click" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-commenting" aria-hidden="true" style="color:orange"></i> Query</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="activitylog" role="tabpanel" aria-expanded="false" style="padding-bottom: 15px;">
                        <div class="timeline">
                            <div class="timeline-month bg-c-yellow" style="font-weight: bold;font-size: 16px;color: white;background: #FF7814">
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


                    @if (!empty($condition_view))
                    <div class="tab-pane" id="activitylog" role="tabpanel" aria-expanded="false" style="padding-bottom: 15px;">
                        <div class="timeline">
                            <div class="timeline-month bg-c-yellow" style="font-weight: bold;font-size: 16px;color: white;background: #FF7814">
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
                        @if (count($document) > 0)
                        <div class="col-sm-12" style="text-align: right;padding: 5px 0 15px 0;">
                            <i class="fa fa-plus-square addRowBtn" style="font-size: 20px;color: #0ac282;"></i>
                        </div>
                        @endif
                        <table class="table" id="table_attach">
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
                                    <i class="fa fa-file-powerpoint-o" style="font-size: 30px;color: orange"></i>
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
                                <td style="text-align: center">
                                    <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red" data-id_attach={{ $value['id'] }} data-id_attach={{ $value['id'] }}></i>
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
                                            <input type="file" name="fileupload[]" id="filer_input" multiple="multiple">
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
                                            <input type="file" name="fileupload[]" id="filer_input" multiple="multiple">
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

<div class="modal fade" id="procurementbody-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-gear"></i> Update Request</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('form/procurement/editrow') }}" enctype="multipart/form-data" id="frm_save">
                <input type="hidden" name="req_recid_edit" id="req_recid_edit">
                <input type="hidden" name="within_budget_code" id="within_budget_edit">
                <input type="hidden" name="req_recid_update" id="req_recid_update" value="{{ $top['req_recid'] }}">
                <input type="hidden" name="budget_his_id" id="budget_his_id">
                @csrf
                <div class="modal-body">

                    <div class="table-responsive" style="overflow-y: scroll;max-height: 350px;">
                        <table class="table table-hover">

                            <tr>
                                <td style="text-align: right;width: 10px !important">Description</td>
                                <td>
                                    <textarea class="form-control" name="description_new" id="description_dis"></textarea>
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
                                    <select class="tabledit-input form-control input-sm" id="budget_code_dis" name="budget_code">
                                        @foreach ($budget_code as $value)
                                        <option value="{{ $value->budget_code }}">{{ $value->budget_code }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Alternative Budget Code</td>
                                <td>
                                    <select readonly="" class="tabledit-input form-control input-sm" id="alternativebudget_code_dis" name="alternativebudget_code">
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
                                    <input type="text" class="form-control" name="qty" id="qty_dis">
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right;">Unit Price</td>
                                <td>
                                    <input type="text" class="form-control " id="unit_price_dis" name="unit_price">
                                </td>
                            </tr>
                            <tr id="hide_vat">
                                <td style="text-align: right;">Vat</td>
                                <td>
                                    <input type="text" class="form-control" name="vat_dis" id="vat_dis">
                                </td>   
                            </tr>
                            <tr>
                                <td style="text-align: right;">Total Estimate</td>
                                <td>
                                    <input type="text" class="form-control" name="total_estimate" id="total_estimate_dis" readonly="">
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success waves-effect" name="submit" value="update"><i class="fa fa-save"></i> Modify</button>
                    <button type="submit" class="btn btn-danger waves-effect" name="submit" value="delete"><i class="fa fa-save"></i> Remove</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete_frm" method="post" action="{{ url('form/delete') }}">
    {{ csrf_field() }}
    <input type="hidden" name="param" value="{{ $top['req_recid'] }}">
</form>
</div>
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
    $('#btn_add_new').on('click', function() {
        // alert('ooo');
        $('#frm_save').trigger("reset");
        $('#req_recid_edit').val('');
        $('#within_budget_edit').val('');
        $('#budget_his_id').val('');
        

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
        var qty = $(this).val();
        var unit_price = $('#unit_price_dis').val();
        var total = qty * unit_price;
        var vat_dis =(total *0.1).toFixed(2);
        $('#total_estimate_dis').val(total.toFixed(2))
        $('#vat_dis').val(vat_dis)
    });
    $("#unit_price_dis").keyup(function() {
        var unit_price = $(this).val();
        var qty = $('#qty_dis').val();
        var total = qty * unit_price;
        var vat_dis =(total *0.1).toFixed(2);
        $('#total_estimate_dis').val(total.toFixed(2))
        $('#vat_dis').val(vat_dis)
    });
</script>
<script>
    $(document).on('click', '.edit_item', function() {

        // $('#groupidshow').text('');
        var description = $(this).data('req_description');
        var recid = $(this).data('req_recid');
        var budget_code = $(this).data('budgetcode');
        var alternativebudget_code = $(this).data('alternativebudget');
        // alert(alternativebudget_code)
        var department = $(this).data('department');
        var unit = $(this).data('unit');
        var qty = $(this).data('qty');
        var unit_price = $(this).data('unit_price');
        var unit_price_khr = $(this).data('unit_price_khr');
        var total_estimate = $(this).data('total_estimate');
        var total_estimate_khr = $(this).data('total_estimate_khr');
        var delivery_date = $(this).data('delivery_date');
        var withinbudget = $(this).data('withinbudget');
        var bodyid = $(this).data('bodyid');
        var within = $(this).data('within');
        var budget_his = $(this).data('budget_his');
        var ccy = $('#currency').val();
        if (ccy == 'KHR') {
            $('#unit_price_dis').val(unit_price_khr);
            $('#total_estimate_dis').val(total_estimate_khr);
        } else {
            $('#unit_price_dis').val(unit_price);
            $('#total_estimate_dis').val(total_estimate);
        }
        $('#description_dis').val(description);
        $('#branchcode_dis').val(department);
        $('#budget_code_dis').val(budget_code);
        $('#alternativebudget_code_dis').val(alternativebudget_code);
        $('#unit_dis').val(unit);
        $('#qty_dis').val(qty);



        $('#delivery_date_dis').val(delivery_date);
        $('#req_recid_edit').val(bodyid);
        $('#within_budget_edit').val(within);
        $('#budget_his_id').val(budget_his);
        $('#hide_vat').hide();
        // alert(description);      
    });
    $(document).on('click', '.edit_item_vendor', function() {
        var vendor_name = $(this).data('vendor_name_update');
        var justification = $(this).data('justification_update');
        var id = $(this).data('id');

        $('#id_vendor').val(id);
        $('#vendor_name_update').val(vendor_name);
        $('#justification_update').val(justification);
    });
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
    });

    function calculateSumProduct() {
        var sum = 0;
        var vendor_name = $('#vendor_name').va();
        $(".product").each(function() {
            if (!isNaN(this.value) && this.value.length != 0) {
                sum += parseFloat(this.value);
            }
            $("#total-product").val(sum);
            if (sum != 100) {
                $('#product_error').show();
                $('#product_error').text('Sum of value Product and Sagment must be 100');
            } else {
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
            } else {
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
        $('#show_error_vendor_name').fadeOut(800);
        $('#show_error_vendor_name').fadeIn(800);
    }

    setInterval(blink_text, 1000);

    function go_home() {
        $('#registration')[0].reset();
    }
</script>
<script>
    $("#registration").validate({
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        ignore: "[readonly]",

        rules: {
            slc_approve: {
                required: true,
            },
            slc_approve_co:{
                required: {
                        depends: function(element) {
                            var ceo_email = @json($ceo_email);
                            var dceo_email = @json($dceo_email);
                            if ($('#slc_approve').val() == ceo_email || $('#slc_approve').val() == dceo_email ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    },
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
                required: false,
            },
            subject: {
                required: true,
            },
            pr_ref_no: {
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
            purpose_rationale: {
                required: true,
            },
            bid_waiver_sole: {
                required: false,
            },
            'vendor_name[]': {
                    required: {
                        depends: function(element) {
                            if ($('#bid_waiver_sole').val() == "yes" ) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                },
                'vendor_description[]': {
                    required: {
                        depends: function(element) {
                            if ($('#bid_waiver_sole').val() == "yes" ) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
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
            slc_approve_co: "(Please fill all *)",
            slc_final: "(Please fill all *)",
            comment: "(Please fill all *)"

        },

        errorPlacement: function(error, element) {

            if (element.attr("name") == "department" || element.attr("name") == "requestDate" || element
                .attr("name") == "expDate" ||
                element.attr("name") == "refNumber" || element.attr("name") == "subject" || element.attr(
                    "name") == "pr_ref_no") {
                $('#spn_procurement_error').empty();
                error.appendTo('#spn_procurement_error');
            } else if (element.attr("name") == "description[]" || element.attr("name") == "unit[]" ||
                element.attr("name") == "qty[]" ||
                element.attr("name") == "unit_price[]" || element.attr("name") == "total_estimate[]" ||
                element.attr("name") == "delivery_date[]" || element.attr("name") == "within_budget[]" ||
                element.attr("name") == "br_dep_code[]" || element.attr("name") == "budget_code[]" ||
                element.attr("name") == "alternative_budget_code[]") {
                $('#tbl_procurement_error').empty();
                error.appendTo('#tbl_procurement_error');
            } else if (element.attr("name") == "purpose_rationale" || element.attr("name") ==
                "bid_waiver_sole" || element.attr("name") == "vendor_name[]" ||
                element.attr("name") == "vendor_description[]" || element.attr("name") ==
                "justification_for_request" ||
                element.attr("name") == "comment_by_procurement") {
                $('#tbl1_procurement_error').empty();
                $('#show_error_vendor_name').removeAttr('hidden');
                error.appendTo('#tbl1_procurement_error');
                
            } else if (
                element.attr("name") == "slc_approve" ||
                element.attr("name") == "slc_approve_co" ||
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
            var vendor_name = $('#vendor_name').val();
            
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
    $("#slc_approve").change(function() {
        var ceo_email = @json($ceo_email);
        var dceo_email = @json($dceo_email);
        if ($(this).val() == ceo_email || $(this).val() == dceo_email) {
            $('#co_approver').hide();
        } else {
            $('#co_approver').show();
        }
    });
</script>
<script>
    $(".bid_waiver_sole").change(function() {
        if ($(this).val() == "yes") {
            $("#vendor_bloch").show();
        } else {
            $("#vendor_bloch").hide();
            $('#vendor_name').val('');
            $('#vendor_description').val('');
        }
    });
</script>
<script>
    $(document).on('click', '#btn_update_bid', function() {
        $('#updateExchangeRateModal').modal("show");
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
                    <td style="text-align: right;width: 20% !important;border: none !important;"> ${label_reviewer}</td>
                    <td class="td_reviewer${rowIdx}" style="border: none !important; width: 75%">
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
    $(document).on("click", ".checkbox_vat", function() {
        var request_id = $('#pr_ref_no').val();
			if($(this).not(":checked")){
                check_box =0;
            }
            if($(this).is(":checked")){
                check_box =1;
            }
			$.ajax({
				url:  "update_vat",
				type: 'post',   
				dataType: 'json',
                data:{'check_box':check_box,'request_id':request_id,_token: '{{csrf_token()}}'},
				success: function(response) {
					var ac_no = response['data'];
                    location.reload();
                    
				},
				async: false
			});
	});
    $(document).on("change", ".bid_waiver_sole", function() {
        var request_id = $('#pr_ref_no').val();
        var bid_waiver_sole_update = $(this).val();
			$.ajax({
				url:  "/update-bid",
				type: 'post',   
				dataType: 'json',
                data:{'bid_waiver_sole_update':bid_waiver_sole_update,'request_id':request_id,_token: '{{csrf_token()}}'},
				success: function(response) {
					var ac_no = response['data'];
                    location.reload();
                    
				},
				async: false
			});
	});
    $('#slc_approve_co').change(function(){
        var slc_approve = $('#slc_approve').val();
        if(slc_approve == $(this).val()){
            alert('Approver and Co-Approver can not be the same person!');
            $(this).val('');
        }
    });
    $(document).ready(function() {
        var td_vat = $('#td_vat').val();
        if(td_vat == 0 ){
            $('.checkbox_vat').prop( "checked", false )
        }else{
            $('.checkbox_vat').prop( "checked", true )
        }

    });
    var slc_review = $('.slc_review').val();
        var slc_review1 = $('.slc_review1').val();
        var slc_review2 = $('.slc_review2').val();
    $('.slc_review').change(function(){
        var slc_review = $(this).val();
        if(!slc_review){
            $('#R1').remove();
            $('#R2').remove();
            $('#R3').remove();
        }
        
    });
    $('#row_reviewer').on('change', '.slc_review1', function() {
            var slc_review1 = $(this).val();
            if(!slc_review1){
            $('#R2').remove();
            $('#R3').remove();
        }
    });
    $('#row_reviewer').on('change', '.slc_review2', function() {
            var slc_review2 = $(this).val();
            if(!slc_review2){
            $('#R3').remove();
        }
    });
    $('.slc_review').change(function(){
        var slc_review = $(this).val();
        var slc_review_new = slc_review.split('/')[0]+'/'
        var dceo_office_email = @json($dceo_email_office);
        if(slc_review_new == dceo_office_email){
            alert('Please add DCEO as the next reviewer.');
        }
    });
    
    $(document).on('change','#reviewer1',function(){
        var slc_review1 = $(this).val();
        var slc_review_new = slc_review1.split('/')[0]+'/'
        var dceo_office_email = @json($dceo_email_office);
        if(slc_review_new == dceo_office_email){
            alert('Please add DCEO as the next reviewer.');
        }
    });

    $(document).on('change','#reviewer2',function(){
        var slc_review2 = $(this).val();
        var slc_review_new = slc_review2.split('/')[0]+'/'
        var dceo_office_email = @json($dceo_email_office);
        if(slc_review_new == dceo_office_email){
            alert('Please add DCEO as the next reviewer.');
        }
    });
    $(document).on('change','#slc_final',function(){
        var final = $(this).val();
        var final_name = $('#slc_final option:selected').text().trim();
        $('#procurement_team_name').val(final_name);
        $('#procurement_team').val(final);
    });
    
</script>

@endsection