@extends('layouts.master')
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
    .btnRemoveItemDetail{
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
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('form/advances') }}">Advance Request</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/advances/save-draft') }}" name="frmCreateNew" id="frmCreateNew"
            enctype="multipart/form-data">
            @csrf
            <div class="myheader" id="myHeader">
                <button type="submit" name="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                        class="fa fa-save" aria-hidden="true" style="color: green"></i> Save</button>

                <button type="button" onclick="go_home();" name="submit" value="reject"
                    style="padding: 5px 10px 5px 10px;cursor: pointer;">
                    <i class="fa fa-undo" aria-hidden="true" style="color: red"></i> Cancel</button>
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
                                         <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{$references}}" readonly="">
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
                                        <input type="text" class="form-control" value="{{ $department}}" readonly="" name="department">
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
                                            value="{{ date('d/m/y') }}" readonly="">
                                    </div>
                                    <div class="col-sm-2">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Due Date</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="date" class="form-control" id="due_date" name="due_date">
                                    </div>
                                </div>
                               
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Currency</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{$currency}}"  name="currency" readonly=""> 
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
                                        <textarea class="form-control" rows="2" id="subject" name="subject"></textarea>
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
                                                name="category[]" value="Ordinary">
                                            <label class="border-checkbox-label" for="category">Ordinary</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox chb_2" type="checkbox" id="category1"
                                                name="category[]" value="Event/Project">
                                            <label class="border-checkbox-label" for="category1">Event/Project</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox chb_2" type="checkbox" id="category2"
                                                name="category[]" value="Staff Benefit">
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
                                            <input type="text" class="form-control" id="account_name" name="account_name">
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
                                                name="account_number">
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
                                            <input type="text" class="form-control" id="bank_name" name="bank_name">
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
                                                name="bank_address"></textarea>
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
                                            <input type="text" id="phone_number" name="phone_number" class="form-control">
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
                                            <input type="text" id="company" name="company" class="form-control">
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
                                            <input type="text" id="id_no" name="id_no" class="form-control">
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
                                            <input type="text" id="contact_no" name="contact_no" class="form-control">
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
                                            <textarea class="form-control" rows="2" id="address" name="address"></textarea>
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
                                    <table class="table table-striped table-bordered" id="table_advance_form" style="font-size: 12.5px;">
                                        <thead>
                                            <tr class="table-info">
                                                <th rowspan="2" style="vertical-align:middle;">No</th>
                                                <th rowspan="2" style="vertical-align:middle;">Inv. No</th>
                                                <th rowspan="2" style="vertical-align:middle;">Description</th>
                                                <th rowspan="2" style="vertical-align:middle;">Br./Dep Code</th>
                                                <th rowspan="2" style="vertical-align:middle;">Budget Code</th>
                                                <th rowspan="2" style="vertical-align:middle;">Alternative Budget Code</th>
                                                <th rowspan="2" style="vertical-align:middle;">Unit</th>
                                                <th rowspan="2" style="vertical-align:middle;">QTY</th>
                                                <th hidden rowspan="2" style="vertical-align:middle;">VAT</th>
                                                <th rowspan="2" style="vertical-align:middle;">Unit price</th>
                                                <th rowspan="2" style="vertical-align:middle;"> Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($advanceFormDetails as $key => $value)
                                                <tr id="{{++$key}}">
                                                    <td style="display:none;"><input type="text" id="item_ids" name="item_ids[]" class="form-control" value="{{$value->pr_item_id}}"></td>
                                                    <td > {{$key}} </td>

                                                    <td >
                                                        {{ $value->invoice_number }}
                                                        <textarea cols="30" rows="1"  id="invoices" name="invoices[]" readonly="" style="display: none">
                                                            {{$value->invoice_number}}
                                                        </textarea>
                                                    </td>
                                                    <td >
                                                        {{ $value->description }}
                                                        <textarea cols="30" rows="1"  id="descriptions" name="descriptions[]" readonly="" style="display: none">
                                                            {{$value->description}}
                                                        </textarea>
                                                    </td>
                                                    <td >
                                                        {{ $value->department_code }}
                                                        <textarea cols="30" rows="1"  id="department_codes" name="department_codes[]" readonly="" style="display: none">
                                                            {{$value->department_code}}
                                                        </textarea>
                                                    </td>
                                                    <td >
                                                        {{ $value->budget_code }}
                                                        <textarea cols="30" rows="1"  id="budget_codes" name="budget_codes[]" readonly="" style="display: none">
                                                            {{$value->budget_code}}
                                                        </textarea>
                                                    </td>
                                                    <td >
                                                        
                                                        @if ($value->alternative_budget_code > 0)
                                                            {{ $value->alternative_budget_code }}
                                                            <textarea cols="30" rows="1"  id="alternative_budget_codes" name="alternative_budget_codes[]" readonly="" style="display: none">
                                                                {{$value->alternative_budget_code}}
                                                            </textarea> 
                                                        @else
                                                            N/A
                                                            <textarea cols="30" rows="1"  id="alternative_budget_codes" name="alternative_budget_codes[]" readonly="" style="display: none">
                                                                N/A
                                                            </textarea> 
                                                        @endif

                                                    </td>
                                                    <td > 
                                                        {{$value->unit}}
                                                        <textarea cols="30" rows="1"  id="units" name="units[]" readonly="" style="display: none">
                                                            {{$value->unit}}
                                                        </textarea>  
                                                    </td>
                                                    <td > 
                                                        {{ $value->quantity }}
                                                        <textarea cols="30" rows="1"  id="qtys" name="qtys[]" readonly="" style="display: none">
                                                            {{$value->quantity}}
                                                        </textarea>  
                                                    </td>
                                                    <td hidden>
                                                        <input type="hidden" name="vat_item[]" value="0">
                                                    </td>
                                                    <td >
                                                        @if ($currency == 'KHR')
                                                            <span style="font-size: 18px;">៛</span>@money($value->unit_price_khr)
                                                            <textarea cols="30" rows="1"  id="unit_prices" name="unit_prices[]" readonly="" style="display: none">
                                                                {{$value->unit_price_khr}}
                                                            </textarea>  
                                                        @else
                                                            $@money($value->unit_price_usd)
                                                            <textarea cols="30" rows="1"  id="unit_prices" name="unit_prices[]" readonly="" style="display: none">
                                                                {{$value->unit_price_usd}}
                                                            </textarea>  
                                                        @endif
                                                    </td>
                                                    <td>
                                                       <i class="fa fa-trash btnRemoveItemDetail" onmouseh style="font-size: 20px;color: red" data-rec_id="{{$key}}"></i>   
                                                    </td>
                                                </tr>
                                            @endforeach
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
                                        <textarea name="additional_remarks" class="form-control "></textarea>
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
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_general" id="product_general"
                                                    onpaste="return false;">

                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_loan_general"
                                                    id="product_loan_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_mortgage"
                                                    id="product_mortgage" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_business"
                                                    id="product_business" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_personal"
                                                    id="product_personal" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_card_general"
                                                    id="product_card_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_debit_card"
                                                    id="product_debit_card" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_credit_card"
                                                    id="product_credit_card" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_trade_general"
                                                    id="product_trade_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_bank_guarantee"
                                                    id="product_bank_guarantee" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_letter_of_credit"
                                                    id="product_letter_of_credit" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_deposit_general"
                                                    id="product_deposit_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_casa_individual"
                                                    id="product_casa_individual" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_td_individual"
                                                    id="product_td_individual" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_casa_corporate"
                                                    id="product_casa_corporate" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="product_td_corporate"
                                                    id="product_td_corporate" onpaste="return false;">
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
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="segment_general"
                                                    id="segment_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="segment_bfs" id="segment_bfs"
                                                    onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="segment_rfs" id="segment_rfs"
                                                    onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="segment_pb" id="segment_pb"
                                                    onpaste="return false;">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="segment_pcp" id="segment_pcp"
                                                    onpaste="return false;">
                                                    <input type="hidden" class="grand_total" name="" value="100">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="segment_afs" id="segment_afs"
                                                    onpaste="return false;">
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
                                        class="form-control "></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>File Upload</h5>
                        <span id="validate_file_upload_pannel"></span>
                    </div>
                    <div class="card-block">
                        <input type="file" name="fileupload[]" id="filer_input" multiple="multiple" required>
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
<script>
  $('.btnRemoveItemDetail').on('click',function(){
      var id_remove=$(this).data('rec_id')
      $('table#table_advance_form tr#'+id_remove).remove();
  });
</script>

<script src="{{ URL::to('js/PDF/xlsx.js') }}"></script>

    <script>
        document.getElementById('upload-excel').addEventListener('change', handleFileSelect, false);
        var ExcelToJSON = function() {
            this.parseExcel = function(file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type: 'binary'
                    });
                    workbook.SheetNames.forEach(function(sheetName) {
                        // Here is your object
                        var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[
                            sheetName]);
                        var json_object = JSON.stringify(XL_row_object);
                        BindTable(JSON.parse(json_object), "#tableAdvanceForm");

                    })
                };
                reader.onerror = function(ex) {
                    console.log(ex);
                };
                reader.readAsBinaryString(file);
            };
        };

        function handleFileSelect(evt) {
            var files = evt.target.files; // FileList object
            var xl2json = new ExcelToJSON();
            xl2json.parseExcel(files[0]);
        }

        function checkIfStringHasOnlyDigits(_string) {
            if (_string.match(/^[0-9]*\.?[0-9]*$/) != null) {
                return true;
            }
            return false;
        }

        $('.addRowBtn').click(function() {
            $("#upload-excel").attr('disabled', 'disabled');
            $(".box-excel").css('color', 'gray');
        });
    </script>
    <script>
        $('#fetch_data_btn').on('click', function() {
            var references = $('#procurementReferences').val();
            if (references == '') {
                alert('No record select');
            } else {
                var url_redirect = "{{ url('/') }}" + '/form/advances/save-procurement-references/' +
                    "{{ Crypt::encrypt('"+references+"') }}" + '/' + references;
                window.location = url_redirect;
            }
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
    </script>


    <script>
        $(document).ready(function() {

            $("input.numbers").keypress(function(event) {
                return /\d/.test(String.fromCharCode(event.keyCode));
            });
            //iterate through each textboxes and add keyup
            //handler to trigger sum event
            $(".sagement").each(function() {
                $(this).keyup(function() {
                    calculateSum1();
                });
            });

        });

        function calculateSum1() {

            var sum = 0;
            //iterate through each textboxes and add the values
            $(".sagement").each(function() {

                //add only if the value is number
                if (!isNaN(this.value) && this.value.length != 0) {
                    sum += parseFloat(this.value);
                }

                $(".grand_total").val(100 - sum)

            });
            //.toFixed() method will roundoff the final sum to 2 decimal places
            $("#sum").html(sum.toFixed(2));
            if ($("#sum").text() > 100) {
                $('#btn_alert').click();
                // alert('Must be less than 100');
                $('.sagement').val('')
                $(".grand_total").val('100')
            }
        }
    </script>


    <script>
        $(document).ready(function() {

            //iterate through each textboxes and add keyup
            //handler to trigger sum event
            $(".counter").each(function() {

                $(this).keyup(function() {
                    // alert('hello')
                    calculateSum();
                });
            });

        });

        function calculateSum() {

            var sum = 0;
            //iterate through each textboxes and add the values
            $(".counter").each(function() {

                //add only if the value is number
                if (!isNaN(this.value) && this.value.length != 0) {
                    sum += parseFloat(this.value);
                }

                $(".total").val(100 - sum)

            });
            //.toFixed() method will roundoff the final sum to 2 decimal places
            $("#sum").html(sum.toFixed(2));
            if ($("#sum").text() > 100) {
                $('#btn_alert').click();
                // alert('Must be less than 100');
                $('.counter').val('')
                $(".total_segment_percentage").val('')
            }
        }
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
            window.location.href = "{{ route('form/advances') }}";
        }
    </script>

    <script>
        $(document).on("keyup", ".counter", function() {
            var sum = 0;
            $(".counter").each(function() {
                sum += +$(this).val();
            });
            $(".total_segment_percentage").val(sum);
        });
    </script>
    <script>
        $("#frmCreateNew").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

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

                //===== For =====
                'descriptions[]': {required: true,},
                'department_codes[]': {required: true,},
                'budget_codes[]': {required: true,},
                'qtys[]': {required: true,},
                'unit_prices[]': {required: true,},
                'fileupload[]': {required: true,},
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

                //===== avance form detail =====
                'descriptions[]':"Please input mandatory field",
                'department_codes[]': "Please input mandatory field",
                'budget_codes[]': "Please input mandatory field",
                'qtys[]': "Please input mandatory field",
                'unit_prices[]':"Please input mandatory field",

                //===== file upload =====
                'fileupload[]':"Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                //== validate currency pannel =====
                if(element.attr("name") == "currency"){
                    $('#validate_currency_pannel').empty();
                    error.appendTo('#validate_currency_pannel');
                }

                //=== category ===
                if( element.attr("name") == "subject"){
                    $('#validate_subject_pannel').empty();
                    error.appendTo('#validate_subject_pannel');
                }
                //=== validate advance pannel ===
                if(
                    element.attr("name") == "department" || 
                    element.attr("name") == "due_date" || 
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

                //===== avance form detail =====
                if(
                    element.attr("name") == "descriptions[]" ||
                    element.attr("name") == "department_codes[]" ||
                    element.attr("name") == "budget_codes[]" ||
                    element.attr("name") == "qtys[]"  ||
                    element.attr("name") == "unit_prices[]"
                    ){
                    $('#validate_advance_detail_pannel').empty();
                    error.appendTo('#validate_advance_detail_pannel');
                }

                //===== file upload =====
                if(element.attr("name") == "fileupload[]"){
                    $('#validate_file_upload_pannel').empty();
                    error.appendTo('#validate_file_upload_pannel');
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
                    $(".overlay").show();
                    form.submit();
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

           $totalGeneral = $general ? $general : 0;
           $totalBFS = $bfs ? $bfs : 0;
           $totalRFS = $rfs ? $rfs : 0;
           $totalPB = $pb ? $pb : 0;

           $grandTotalAmount = parseFloat($totalGeneral) + parseFloat($totalBFS) + parseFloat($totalRFS) + parseFloat($totalPB);
           return $grandTotalAmount;

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
@endsection
