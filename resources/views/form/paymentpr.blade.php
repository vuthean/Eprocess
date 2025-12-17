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
        background: white;
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

    .removeRowBtn1 {
        cursor: pointer;
    }

    .addRowBtn {
        cursor: pointer;
    }
</style>
@section('menu')
@include('siderbar.payment')
@endsection
@section('breadcrumb')
@include('breadcrumb.payment_new')
@endsection
@section('content')

<div class="col-sm-12">
    <form method="post" action="{{route('form/payment/save')}}" enctype="multipart/form-data" id="registration" name="registration">
        @csrf
        <div class="myheader" id="myHeader">
            <button type="submit" name="submit" id="submit_save" value="approve" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-save" aria-hidden="true" style="color: green"></i> Save</button>
            <button type="button" onclick="go_home();" name="submit" value="reject" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-undo" aria-hidden="true" style="color: red"></i> Cancel</button>
        </div>
        <!-- Page-body start -->
        <div class="page-body">
            <!-- DOM/Jquery table start -->
            <div class="card">
                <div class="card-block">
                    <h4 class="sub-title">Payment Request</h4>
                    <span id="top_error"></span>
                    <div class="row">
                        <div class="col-sm-12 mobile-inputs">

                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Procurement Ref. :</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="pr_ref" id="pr_ref" class="form-control" value="{{$pr_id_from}}" readonly="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Department :</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="{{Auth::user()->department}}" readonly="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Request Date:</label>
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" id="requestDate" name="requestDate" value="{{date('d/m/y')}}" readonly="">
                                </div>
                                <div class="col-sm-2">
                                    <label for=""><span style="color: red;">* </span>Due Date :</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="expDate" name="expDate">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for=""><span style="color: red;">*</span> Currency:</label>
                                </div>
                                <div class="col-sm-9">

                                    <input type="text" class="form-control" value="{{$cur_ccy[0]['ccy']}}" id="currency" name="currency" readonly="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mobile-inputs">
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for=""><span style="color: red;">*</span> Subject:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" placeholder="Subject" id="subject" name="subject" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for=""><span style="color: red;">*</span> Type :</label>
                                </div>
                                <div class="col-sm-2 border-checkbox-section">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox chb_1" type="checkbox" id="type" name="type[]" value="Payment">
                                        <label class="border-checkbox-label" for="type">Payment</label>
                                    </div>
                                </div>
                                <div class="col-sm-3 border-checkbox-section">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox chb_1" type="checkbox" id="type1" name="type[]" value="Deposit">
                                        <label class="border-checkbox-label" for="type1">Deposit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for=""><span style="color: red;">*</span> Category :</label>
                                </div>
                                <div class="col-sm-2 border-checkbox-section">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox chb_2" type="checkbox" id="category" name="category[]" value="Ordinary">
                                        <label class="border-checkbox-label" for="category">Ordinary</label>
                                    </div>
                                </div>
                                <div class="col-sm-2 border-checkbox-section">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox chb_2" type="checkbox" id="category1" name="category[]" value="Event/Project">
                                        <label class="border-checkbox-label" for="category1">Event/Project</label>
                                    </div>
                                </div>
                                <div class="col-sm-3 border-checkbox-section">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <input class="border-checkbox chb_2" type="checkbox" id="category2" name="category[]" value="Staff Benefit">
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
                    <h4 class="sub-title">Paid to:</h4>
                    <span id="paid_to"></span>
                    <div class="row">
                        <div class="col-sm-12 mobile-inputs">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> Account Name:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="account_name" name="account_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> Account Number:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="account_number" name="account_number">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> Bank Name:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="bank_name" name="bank_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> Bank Address:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="2" id="bank_address" name="bank_address"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for=""><span style="color: red;">*</span> SWIFT Code:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="swift_code" name="swift_code">
                                        </div>
                                    </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Tel:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="tel" name="tel" class="form-control">
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
                                        <label for=""><span style="color: red;">*</span> Mr./Ms./Company:</label>
                                    </div>
                                    <div class="col-sm-9 ">
                                        <input type="text" id="for_who" name="for_who" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> ID No.:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="id_no" name="id_no" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> Contact No:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="contact_no" name="contact_no" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for=""><span style="color: red;">*</span> Address:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="2" id="address_who" name="address_who"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-block add-remove-tbl">
                    <h4 class="sub-title">Procurement Request </h4>
                    <span id="tbl_procurement_error"></span>
                    <div class="card-block">
                        <div class="table-responsive dt-responsive">
                            <table class="table table-striped table-bordered" id="table_payment" style="font-size: 12.5px">
                                <thead>
                                    <tr class="table-info">

                                        <th><span style="color: red;">*</span> Inv.No</th>
                                        <th>Procurement Ref</th>
                                        <th><span style="color: red;">*</span> Description</th>
                                        <th><span style="color: red;">*</span> Br./Dep Code</th>
                                        <th><span style="color: red;">*</span> Budget Code</th>
                                        <th>Alternative Budget Code</th>
                                        <th><span style="color: red;">*</span> Unit</th>
                                        <th><span style="color: red;">*</span> QTY</th>
                                        <th><span style="color: red;">*</span> Unit price</th>
                                        <th>VAT</th>
                                        <th>Action</th>
                                        <th style="display: none;">Total</th>
                                        <th style="display: none;">YTD Expense</th>
                                        <th style="display: none;">Total Budget</th>
                                        <th style="display: none;">Within Budget</th>
                                        <th style="display: none;">pr_col_id</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($pr_body as $key => $value)
                                    <tr id="{{$key}}">
                                        <td>
                                            <label>
                                                <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="invoice" name="invoice[]"></textarea>
                                            </label>
                                        </td>
                                        <td>
                                            {{$value->pr_id}}
                                        </td>
                                        <td>
                                            {{$value->description}}
                                            <textarea cols="30" rows="1" id="description" name="description[]" style="display: none">{{$value->description}}</textarea>

                                        </td>
                                        <td>
                                            {{$value->br_dep_code}}
                                            <textarea cols="30" rows="1" id="br_dep_code" name="br_dep_code[]" style="display: none">{{$value->br_dep_code}}</textarea>
                                        </td>
                                        <td>
                                            {{$value->budget_code}}
                                            <textarea cols="30" rows="1" id="budget_code" name="budget_code[]" style="display: none">{{$value->budget_code}}</textarea>
                                        </td>
                                        <td>
                                            @if(!empty($value->alternativebudget_code))
                                            {{$value->alternativebudget_code}}@else N/A @endif
                                            <textarea cols="30" rows="1" name="alternative_budget_code[]" style="display: none">@if(!empty($value->alternativebudget_code))
                              {{$value->alternativebudget_code}}@else N/A @endif</textarea>
                                        </td>
                                        <td>
                                            {{$value->unit}}
                                            <textarea cols="30" rows="1" id="unit" name="unit[]" readonly="" style="display: none">{{$value->unit}}</textarea>
                                        </td>
                                        <td>
                                            {{$value->qty}}
                                            <textarea cols="30" rows="1" hidden="" id="qty" name="qty[]">{{$value->qty}}</textarea>
                                        </td>
                                        <td>

                                            <div class="input-group">

                                                @if($cur_ccy[0]['ccy']=='KHR')
                                                <span class="input-group-addon">៛</span>
                                                <input type="text" class="form-control resizetext numbers" id="unit_price" name="unit_price[]" value="{{$value->unit_price_khr}}" style="width: 70px !important">

                                                @else
                                                <span class="input-group-addon">$</span>
                                                <input type="text" class="form-control resizetext numbers" id="unit_price" name="unit_price[]" value="{{$value->unit_price}}" style="width: 70px !important">
                                                @endif



                                            </div>
                                        </td>
                                        <td>
                                            <label>
                                                <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext vat_item" type="text" id="vat_item" name="vat_item[]"></textarea>
                                            </label>
                                        </td>
                                        <td>
                                            <i class="fa fa-trash removeRowBtn1" onmouseh style="font-size: 20px;color: red" data-rec_id="{{$key}}"></i>
                                        </td>
                                        <td style="display: none;">
                                            <textarea cols="30" rows="1" class="tabledit-input tabledit-input form-control input-sm resizetext" id="total" name="total">{{$value->total}}</textarea>
                                        </td>
                                        <td style="display: none;">
                                            <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" id="ytd" name="ytd">{{$value->payment}}</textarea>
                                        </td>
                                        <td style="display: none;">
                                            <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" id="totalbgt" name="totalbgt">{{$value->total_bu}}</textarea>
                                        </td>
                                        <td style="display: none;">
                                            <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" id="within" name="within">{{$value->within_budget_code}}</textarea>
                                        </td>

                                        <td style="display: none;">
                                            {{$value->col_id}}
                                            <input type="text" name="pr_col_id[]" value="{{$value->col_id}}">
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr style="display: none;">
                                        <td colspan="7" style="text-align: right;">
                                            SUB TOTAL
                                        </td>
                                        <td>
                                            <input type="text" class="tabledit-input form-control input-sm resizetext" id="sub_total" name="sub_total">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" style="text-align: right;">
                                            DISCOUNT
                                        </td>
                                        <td>
                                            <input type="text" class="tabledit-input form-control input-sm resizetext" id="discount" name="discount">
                                            <input type="hidden" class="tabledit-input form-control input-sm resizetext" id="vat" name="vat">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" style="text-align: right;">
                                            WHT
                                        </td>
                                        <td>
                                            <input type="text" class="tabledit-input form-control input-sm resizetext" id="wht" name="wht">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" style="text-align: right;">
                                            DEPOSIT
                                        </td>
                                        <td>
                                            <input type="text" class="tabledit-input form-control input-sm resizetext" id="deposit" name="deposit">
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                        <div class="row form-group" style="padding-top: 15px;">
                            <div class="col-sm-2 mobile-inputs">
                                <label for="">Additional Remarks (if any):</label>
                            </div>
                            <div class="col-sm-10 mobile-inputs">
                                <textarea name="remarkable" class="form-control "></textarea>
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
                            <span id="sagement_error" style="color: red;font-size: 15px; font-weight: bold;"></span>
                            <table class="text-center table table-striped table-bordered" id="product_input" style="font-size: 12.5px">
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
                                        <td rowspan="2" class="table-success" style="text-align: left"><b>Type :</b></td>
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
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="general" id="general">

                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="loan_general" id="loan_general">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="mortgage" id="mortgage">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="business" id="business">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="personal" id="personal">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="card_general" id="card_general">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="debit_card" id="debit_card">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="credit_card" id="credit_card">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="trade_general" id="trade_general">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="bank_guarantee" id="bank_guarantee">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="letter_of_credit" id="letter_of_credit">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="deposit_general" id="deposit_general">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="casa_individual" id="casa_individual">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="td_individual" id="td_individual">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers txt sagement" style="width: 50px !important;" type="text" name="casa_corporate" id="casa_corporate">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers sagement" style="width: 50px !important;" type="text" name="td_corporate" id="td_corporate">

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
                            <!--                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">-->
                            <table class="text-center table table-striped table-bordered" id="segment_input" style="font-size: 12.5px">
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
                                            <input class="form-control numbers counter" type="text" style="width: 150px !important;" name="general_segment" id="general_segment">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers counter" type="text" style="width: 150px !important;" name="bfs" id="bfs">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers counter" type="text" style="width: 150px !important;" name="rfs" id="rfs">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers counter" type="text" style="width: 150px !important;" name="pb" id="pb">
                                            <input type="hidden" name="" class="total_sage">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers counter" type="text" style="width: 150px !important;" name="pcp" id="pcp">
                                            <input type="hidden" name="" class="total_sage">
                                        </td>
                                        <td align="center">
                                            <input class="form-control numbers counter" type="text" style="width: 150px !important;" name="afs" id="afs">
                                            <input type="hidden" name="" class="total_sage">
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
                                <textarea name="remarks_product_segment" name="remarks_product_segment" class="form-control "></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>File Upload</h5>
                    <span id="document_spn"></span>
                </div>
                <div class="card-block">
                    <input type="file" name="fileupload[]" id="filer_input" multiple="multiple" required>
                </div>
            </div>
        </div>
        <!-- Row Created Callback table end -->
    </form>


    <button type="button" style="display: none;" class="btn btn-primary sweet-1 m-b-10" id="btn_alert" onclick="_gaq.push(['_trackEvent', 'example', 'try', 'sweet-1']);">Basic</button>
</div>
<form id="procurement_reference" method="post" action="{{url('form/payment/new/ref/')}}">
    {{ csrf_field() }}
</form>
@endsection
@section('script')

<script>
    $('.removeRowBtn1').on('click', function() {
        var id_remove = $(this).data('rec_id')
        $('table#table_payment tr#' + id_remove).remove();
        // alert(id_remove)
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

    function go_home() {
        window.location.href = "{{ route('/')}}";
    }
</script>
<script>
    $(document).on('click', '.chb_1', function() {
        $('.chb_1').not(this).prop('checked', false);
    });
    $(document).on('click', '.chb_2', function() {
        $('.chb_2').not(this).prop('checked', false);
    });
    $(".vat_item").keypress(function(event) {
        return /^(\d)*(\.)?([0-9]{1})?$/.test(String.fromCharCode(event.keyCode));
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
            // if (inputValue === false) window.location="{{route('/')}}";          
            if (inputValue === "") {
                swal.showInputError("You need to write something!");
                return false
            }
            var url_redirect = "{{url('/')}}" + '/form/payment/new/ref/' + "{{Crypt::encrypt('" + inputValue + "')}}" + '/' + inputValue;
            // alert(url_redirect)
            window.location = url_redirect;
            // swal("Nice!", "You wrote: " + inputValue, "success");
        });
    };
</script>
<script type="text/javascript">
    $(window).on('beforeunload', function() {
        var c = confirm();
        // if(!c){
        // console.log('leave')
        var rec_id = $('#txt_req_recid').val();
        // alert(rec_id)
        $.ajax({
            url: "{{url('form/delete/')}}" + "/"
            rec_id,
            type: 'get',
            dataType: 'json',
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            success: function(data) {
                alert(data);

            },
            error: function() {
                alert("failure From php side!!! ");
            }
        });
        // return true; 
        //   }
        // else
        console.log('still there')
        return false;
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
            return /^\d*\.?\d*$/.test(String.fromCharCode(event.keyCode));
        });
        //iterate through each textboxes and add keyup
        //handler to trigger sum event
        $(".sagement").each(function() {

            $(this).keyup(function() {
                // alert('hello')
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
            $(".total_sage").val('')
        }
    }
</script>
<script src="{{URL::to('static/clone/patuta.min.js')}}"></script>
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
        $('#spn_procurement_error').fadeOut(800);
        $('#spn_procurement_error').fadeIn(800);

        $('#tbl_procurement_error').fadeOut(800);
        $('#tbl_procurement_error').fadeIn(800);

        $('#tbl1_procurement_error').fadeOut(800);
        $('#tbl1_procurement_error').fadeIn(800);

        $('#sagement_error').fadeOut(800);
        $('#sagement_error').fadeIn(800);

    }

    setInterval(blink_text, 1000);

    function go_home() {
        window.location.href = "{{ route('/')}}";
    }
</script>

<script>
    $(document).on("keyup", ".counter", function() {
        var sum = 0;
        $(".counter").each(function() {
            sum += +$(this).val();
        });
        $(".total_sage").val(sum);
    });
</script>
<script>
    $("#registration").validate({
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        ignore: "[readonly]",

        rules: {
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
            swift_code:{
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
                required: false,
            },
            vat: {
                required: false,
            },
            wht: {
                required: false,
            },
            deposit: {
                required: false,
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
            expDate: {
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
            swift_code: "(Please fill all *)",
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



        },

        errorPlacement: function(error, element) {
            
            if (element.attr("name") == "account_number" || element.attr("name") == "account_name" || element.attr("name") == "bank_name" || element.attr("name") == "swift_code" ||
                element.attr("name") == "bank_address") {
                $('#paid_to').empty();
                error.appendTo('#paid_to');
            } else if (element.attr("name") == "description[]" || element.attr("name") == "unit[]" || element.attr("name") == "qty[]" ||
                element.attr("name") == "unit_price[]" || element.attr("name") == "total_estimate[]" ||
                element.attr("name") == "delivery_date[]" || element.attr("name") == "within_budget[]" ||
                element.attr("name") == "br_dep_code[]" || element.attr("name") == "budget_code[]" ||
                element.attr("name") == "alternative_budget_code[]" || element.attr("name") == "invoice[]" || element.attr("name") == "discount" || element.attr("name") == "vat" || element.attr("name") == "wht" || element.attr("name") == "deposit") {
                $('#tbl_procurement_error').empty();
                error.appendTo('#tbl_procurement_error');
            } else if (element.attr("name") == "for_who" || element.attr("name") == "id_no" || element.attr("name") == "contact_no" ||
                element.attr("name") == "address_who") {
                $('#for').empty();
                error.appendTo('#for');
            } else if (element.attr("name") == "currency" || element.attr("name") == "subject" || element.attr("name") == "expDate" || element.attr("name") == "type[]" ||
                element.attr("name") == "category[]") {
                $('#top_error').empty();
                error.appendTo('#top_error');
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            var sagement_con = $('.total_sage').val();
            var product_con = $('.grand_total').val();


            if (sagement_con !== '100') {
                $('#sagement_error').show();
                $('#sagement_error').text('Sum of value Product and Sagment must be 100')
            } else {
                if (product_con !== '0') {
                    $('#sagement_error').show();
                    $('#sagement_error').text('Sum of value Product and Sagment must be 100')
                } else {
                    $(".overlay").show();

                    form.submit();
                }
            }
        }

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