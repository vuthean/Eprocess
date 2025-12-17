@extends('layouts.master')
@section('style')
    <style>
        .overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 99999999;
            background: rgba(255, 255, 255, 0.8) url("/examples/images/loader.gif") center no-repeat;
        }

        /* Turn off scrollbar when body element has the loading class */
        body.loading {
            overflow: hidden;
        }

        /* Make spinner image visible when body element has the loading class */
        body.loading .overlay {
            display: block;
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

        .sticky+.content {
            padding-top: 102px;
        }

        .error {
            color: red;
            border-color: red;
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

        .no-js #loader {
            display: none;
        }

        .js #loader {
            display: block;
            position: absolute;
            left: 100px;
            top: 0;
        }

        .se-pre-con {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url(images/loader-64x/Preloader_2.gif) center no-repeat #fff;
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

        <form id="registration" method="post" action="{{ route('form/procurement/save') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <!-- Page-body start -->
            <div class="myheader" id="myHeader">

                <button type="submit" name="submit" id="submit_save" value="approve"
                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-save" aria-hidden="true"
                        style="color: green"></i> Save</button>
                <button type="button" onclick="go_home();" name="submit" value="reject"
                    style="padding: 5px 10px 5px 10px;cursor: pointer;"><i class="fa fa-undo" aria-hidden="true"
                        style="color: red"></i> Cancel</button>
            </div>
            <div class="page-body">
                <!-- DOM/Jquery table start -->
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Procurement Request</h4>
                        <span class="text-right" style="font-size: 12.5px;" id="spn_procurement_error"></span>
                        <div class="row">
                            <div class="col-sm-6 mobile-inputs">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-2 text-right" style="font-size: 12.5px;font-weight: bold">
                                        <label for="">Department :</label>
                                    </div>
                                    <div class="col-sm-10 text-right" style="font-size: 12.5px;">
                                        <input type="text" class="form-control " name="department" id="department"
                                            value="{{ Auth::user()->department }}" readonly="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4 text-right" style="font-size: 12.5px;font-weight: bold">
                                                <label for="">Request Date:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control " id="requestDate"
                                                    name="requestDate" value="{{ date('d/m/y') }}" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-4 text-right" style="font-size: 12.5px;font-weight: bold">
                                                <label for="">Exp. Deliv. Date :</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control " id="expDate" name="expDate">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 text-right" style="font-size: 12.5px;font-weight: bold">
                                        <label for="">Ref. :</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control" id="refNumber"
                                            name="refNumber">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 text-right" style="font-size: 12.5px;font-weight: bold">
                                        <label for=""><span style="color: red;">*</span> Subject :</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" placeholder="Subject" id="subject" name="subject"
                                            rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 text-right" style="font-size: 12.5px;font-weight: bold">
                                        <label for=""><span style="color: red;">*</span> Currency:</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="currency" name="currency">
                                            <option selected="" value="" disabled="">Select One</option>
                                            <option value="USD">USD</option>
                                            <option value="KHR">KHR</option>
                                        </select>
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
                                <div class="col-sm-12"
                                    style="text-align: right;display:flex;align-items: center;justify-content: end;">
                                    <a class="download-template" href="{{ route('download/procurement-excel') }}"><i
                                            class="fa fa-download"></i> Download Template</a>
                                    <div class="box-excel" style="margin-right:10px;">
                                        <input type="file" id="upload-excel" style="display: none;"
                                            accept=".xlsx, .xls, .csv">
                                        <label for="upload-excel"><i class="fa fa-upload"></i> <span>Choose a
                                                file</span></label>
                                    </div>
                                    <button class="addRowBtn" type="button"><i class="fa fa-plus-square"
                                            style="font-size: 20px;color: #0ac282;"></i></button>
                                </div>

                                <table class="table table-striped table-bordered" id="tableProcurement"
                                    style="font-size: 12.5px">
                                    <thead>
                                        <tr class="table-info">
                                            <th><span style="color: red;">*</span> Description</th>
                                            <th><span style="color: red;">*</span> Br./Dep Code</th>
                                            <th><span style="color: red;">*</span> Budget Code</th>
                                            <th>Alternative Budget Code</th>
                                            <th><span style="color: red;">*</span> Unit</th>
                                            <th><span style="color: red;">*</span> QTY</th>
                                            <th><span style="color: red;">*</span> Unit price</th>
                                            <th>Total Estimate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label>
                                                    <textarea cols="30" rows="1"
                                                        class="tabledit-input form-control input-sm resizetext" type="text"
                                                        id="description" name="description[]"></textarea>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <select class="tabledit-input form-control input-sm" id="br_dep_code"
                                                        name="br_dep_code[]">
                                                        <option value="" disabled="" selected="">Select</option>
                                                        @foreach ($dep_code as $value)
                                                            <option value="{{ $value->branch_code }}">
                                                                {{ $value->branch_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                            </td>
                                            <td>
                                                <select class="tabledit-input form-control input-sm" id="budget_code"
                                                    name="budget_code[]">
                                                    <option value="" disabled="" selected="">Select</option>
                                                    @foreach ($budget_code as $value)
                                                        <option value="{{ $value->budget_code }}">
                                                            {{ $value->budget_code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select readonly="" class="tabledit-input form-control input-sm"
                                                    id="alternative_budget_code" name="alternative_budget_code[]">
                                                    <option value="0" selected="">Select</option>
                                                    <!-- @foreach ($alternative_budget_codes as $value)
                                                        <option value="{{ $value->budget_code }}">
                                                            {{ $value->budget_code }}
                                                        </option>
                                                    @endforeach -->
                                                </select>
                                            </td>
                                            <td>
                                                <textarea cols="30" rows="1"
                                                    class="tabledit-input form-control input-sm resizetext" id="unit"
                                                    name="unit[]"></textarea>
                                            </td>
                                            <td>

                                                <input type="text"
                                                    class="abledit-input form-control input-sm resizetext numbers qty" id="qty" 
                                                    name="qty[]" onpaste="return false;">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input type="number" class="form-control resizetext unit_price" placeholder="0"
                                                        required min="0" step="0,01" id="unit_price" name="unit_price[]">
                                                </div>

                                            </td>

                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                        <input readonly type="text" class="form-control resizetext total_price_item" id="total_price_item" name="total_price_item[]">
                                                </div>
                                                


                                            </td>

                                            <td>
                                                <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr hidden="">
                                            <td class="text-right" style="font-size: 12.5px;" colspan="5"> SUB TOTAL:</td>
                                            <td><input class="tabledit-input form-control input-sm" type="text" name="Last">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right;vertical-align:middle;">
                                            Sub Total:
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div class="input-group vertical-align:middle;">
                                                <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input readonly type="text" class="form-control resizetext subtotal" id="subtotal">
                                                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right;vertical-align:middle;">
                                                VAT:
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input readonly type="number" class="form-control resizetext vat" value="0" min="0"
                                                        step="0,01" id="vat" name="vat" onpaste="return false;">
                                                </div>

                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div class="col-sm-2 border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input checked class="border-checkbox chb_2 checkbox_vat" type="checkbox" id="checkbox_vat"
                                                            name="checkbox_vat" value="1">
                                                        <label class="border-checkbox-label" for="checkbox_vat"></label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right;vertical-align:middle;">
                                            Grand Total:
                                            </td>
                                            <td style="vertical-align:middle; ">
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input readonly type="number" class="form-control resizetext grand_total_after_vat" min="0"
                                                        step="0,01" id="grand_total_after_vat" name="grand_total_after_vat" onpaste="return false;">
                                                </div>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Procurement Request</h4>
                        <span id="tbl1_procurement_error"></span>
                        <div class="row form-group">
                            <div class="col-sm-2 mobile-inputs text-right" style="font-size: 12.5px;font-weight: bold">
                                <label for=""><span style="color: red;">*</span> PURPOSE / RATIONALE:</label>
                            </div>
                            <div class="col-sm-10 mobile-inputs">
                                <textarea class="form-control" id="purpose_rationale" name="purpose_rationale"
                                    rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-2 mobile-inputs text-right" style="font-size: 12.5px;font-weight: bold">
                                <label for=""><span style="color: red;">*</span>BID WAIVER/SOLE SOURCE REQUEST:</label>
                            </div>
                            <div class="col-sm-10 mobile-inputs">
                               
                                    <select class="form-control bid_waiver_sole" id="bid_waiver_sole" name="bid_waiver_sole">
                                            <option selected="" value="" disabled="">Select One</option>
                                            <option value="yes">YES</option>
                                            <option value="no">NO</option>
                                    </select>
                            </div>
                        </div>
                        <div class="card-block" style="display:none" id="vendor_bloch">
                            <div class="table-responsive dt-responsive">
                                <label for="">Recommended vendor(s)</label>
                                <table class="table table-striped table-bordered" id="example-3">
                                    <thead>
                                        <tr class="table-info">
                                            <th><span style="color: red;">*</span>Vendor Name</th>
                                            <th><span style="color: red;">*</span>Justification</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm" id="vendor_name"
                                                    name="vendor_name[]" cols="30" rows="1"></textarea>
                                            </td>
                                            <td class="tabledit-view-mode">
                                                <textarea class="tabledit-input form-control input-sm"
                                                    id="vendor_description" name="vendor_description[]" cols="30"
                                                    rows="1"></textarea>
                                            </td>
                                            <td class="tabledit-view-mode">
                                                <i class="fa fa-plus-square" onclick="add_row_2();"
                                                    style="font-size: 20px;color: #0ac282"></i>
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
                                <span id="sagement_error"
                                    style="display: none;color: red;font-size: 15px; font-weight: bold;"></span>
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
                                            <td class="table-success text-left"><b>Allocated % :</b></td>
                                            <td align="center">
                                                <input type="hidden" class="grand_total" name="" value="100">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="general" id="general"
                                                    onpaste="return false;">

                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="loan_general"
                                                    id="loan_general" onpaste="return false;">
                                            </td>
                                            <td>
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="mortgage"
                                                    id="mortgage" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="business"
                                                    id="business" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="personal"
                                                    id="personal" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="card_general"
                                                    id="card_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="debit_card"
                                                    id="debit_card" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="credit_card"
                                                    id="credit_card" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="trade_general"
                                                    id="trade_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="bank_guarantee"
                                                    id="bank_guarantee" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="letter_of_credit"
                                                    id="letter_of_credit" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="deposit_general"
                                                    id="deposit_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="casa_individual"
                                                    id="casa_individual" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="td_individual"
                                                    id="td_individual" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="casa_corporate"
                                                    id="casa_corporate" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="td_corporate"
                                                    id="td_corporate" onpaste="return false;">
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
                                            <td class="table-success" style="text-align: left;"><b>Code :</b></td>
                                            <td>999</td>
                                            <td>100</td>
                                            <td>200</td>
                                            <td>300</td>
                                            <td>400</td>
                                            <td>500</td>
                                        </tr>
                                        <tr>
                                            <td class="table-success" style="text-align: left;"><b>Allocated % :</b></td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="general_segment"
                                                    id="general_segment" onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="bfs" id="bfs"
                                                    onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="rfs" id="rfs"
                                                    onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="pb" id="pb"
                                                    onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="pcp" id="pcp"
                                                    onpaste="return false;">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="afs" id="afs"
                                                    onpaste="return false;">

                                                <input type="hidden" name="" class="total_sage">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row form-group" style="padding-top: 15px;">
                                <div class="col-sm-2 mobile-inputs text-right" style="font-size: 12.5px;font-weight: bold">
                                    <label for="">Additional Remarks (if any) for product and segment :</label>
                                </div>
                                <div class="col-sm-10 mobile-inputs">
                                    <textarea name="remarks_product_segment" name="remarks_product_segment"
                                        class="form-control "></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" style="display: none;" class="btn btn-primary sweet-1 m-b-10" id="btn_alert"
                    onclick="_gaq.push(['_trackEvent', 'example', 'try', 'sweet-1']);">Basic</button>
                <div class="card">
                    <div class="card-header">
                        <h5>File Upload</h5>
                    </div>
                    <div class="card-block">

                        <input type="file" name="fileupload[]" id="filer_input" multiple="multiple">
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
@section('script')
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
                        BindTable(JSON.parse(json_object), "#tableProcurement");

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

        function BindTable(jsondata, tableid) {
            /*Function used to convert the JSON array to Html Table*/
            var row$ = "<tr>";
            for (var i = 0; i < jsondata.length; i++) {
              
                if (jsondata[i].hasOwnProperty('Description') && jsondata[i]
                    .hasOwnProperty('Branch DepCode') && jsondata[i].hasOwnProperty('Budget Code') && jsondata[i].hasOwnProperty('Unit') && jsondata[i]
                    .hasOwnProperty('QTY') && jsondata[i].hasOwnProperty('Unit Price')) {
                    if (checkIfStringHasOnlyDigits(jsondata[i]['QTY']) == false || checkIfStringHasOnlyDigits(jsondata[i][
                            'Unit Price'
                        ]) == false) {
                        alert('Please contact admin!');
                        location.reload();
                        return;
                    }
                    /*Select box dep code*/
                    var $dep_code = @json($dep_code);
                    var selectbox_depcode =
                        '<select class="tabledit-input form-control input-sm selectbox_depcode" id="br_dep_code"  name="br_dep_code[]">';
                    selectbox_depcode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < $dep_code.length; j++) {
                        if ($dep_code[j]['branch_code'] == jsondata[i]['Branch DepCode']) {
                            selectbox_depcode +=
                                `<option value="${$dep_code[j]['branch_code']}" selected>${$dep_code[j]['branch_code']}</option>`;
                        } else {
                            selectbox_depcode +=
                                `<option value="${$dep_code[j]['branch_code']}">${$dep_code[j]['branch_code']}</option>`;
                        }
                    }
                    selectbox_depcode += '</select>'
                    /*End Select box budget code*/
                    /*Select box budget code*/
                    var $budget_code = @json($budget_code);
                    var selectbox_budcode =
                        '<select class="tabledit-input form-control input-sm" id="budget_code" name="budget_code[]">';
                    selectbox_budcode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < $budget_code.length; j++) {
                        if ($budget_code[j]['budget_code'] == jsondata[i]['Budget Code']) {
                            selectbox_budcode +=
                                `<option value="${$budget_code[j]['budget_code']}" selected>${$budget_code[j]['budget_code']}</option>`;
                        } else {
                            selectbox_budcode +=
                                `<option value="${$budget_code[j]['budget_code']}">${$budget_code[j]['budget_code']}</option>`;
                        }
                    }
                    selectbox_budcode += '</select>'
                    /*End Select box budget code*/
                    /*Select box Alternative Budget Code*/
                    var $alternative_budget_codes = @json($alternative_budget_codes);
                    var selectbox_altercode =
                        '<select readonly="" class="tabledit-input form-control input-sm" id="alternative_budget_code" name="alternative_budget_code[]">';
                    selectbox_altercode += '<option value="0" selected="">Select</option>';
                    // for (var j = 0; j < $alternative_budget_codes.length; j++) {
                    //     if ($alternative_budget_codes[j]['budget_code'] == jsondata[i]['Alternative Budget Code']) {
                    //         selectbox_altercode +=
                    //             `<option value="${$alternative_budget_codes[j]['budget_code']}" selected>${$alternative_budget_codes[j]['budget_code']}</option>`;
                    //     } else {
                    //         selectbox_altercode +=
                    //             `<option value="${$alternative_budget_codes[j]['budget_code']}">${$alternative_budget_codes[j]['budget_code']}</option>`;
                    //     }
                    // }
                    selectbox_altercode += '</select>'
                    /*End Select box Alternative Budget Code*/
                    row$ += ` <td>
                          <label>
                            <textarea cols="30" rows="1"
                                                    class="tabledit-input form-control input-sm resizetext" type="text"
                                                    id="description" name="description[]">${jsondata[i]['Description']}</textarea>
                          </label>
                       </td>`;
                    row$ += `<td>
                        <label>
                            ${selectbox_depcode}
                        </label>
                       </td>`;
                    row$ += `<td>
                        <label>
                            ${selectbox_budcode}
                        </label>
                       </td>`;
                    row$ += `<td>
                        <label>
                            ${selectbox_altercode}
                        </label>
                       </td>`;
                    row$ += `<td>
                        <textarea cols="30" rows="1"
                                                class="tabledit-input form-control input-sm resizetext" id="unit"
                                                name="unit[]">${jsondata[i]['Unit']}</textarea>
                       </td>`;
                    row$ += `<td>
                        <input type="number" value="${jsondata[i]['QTY']}"
                                                class="abledit-input form-control input-sm resizetext numbers qty" id="qty"
                                                name="qty[]" onpaste="return false;">
                       </td>`;
                    row$ += `<td>
                        <div class="input-group">
                                                <span class="input-group-addon usd">$</span>
                                                <span class="input-group-addon khr"
                                                    style="font-size: 20px;display: none;">៛</span>

                                                <input type="number" class="form-control resizetext unit_price" placeholder="0"
                                                    required min="0" step="0,01" id="unit_price" name="unit_price[]"
                                                    onpaste="return false;" value="${jsondata[i]['Unit Price']}">
                                            </div>
                       </td>`;
                    row$ += ` <td>
                        <div class="input-group">
                            <span class="input-group-addon usd">$</span>
                            <span class="input-group-addon khr"
                                   style="font-size: 20px;display: none;">៛</span>
                                <input readonly type="text" class="form-control resizetext total_price_item" id="total_price_item" name="total_price_item[]"
                                value="${jsondata[i]['Unit Price']*jsondata[i]['QTY']}">
                        </div>
                       </td>`;
                    if (jsondata.length > 1) {
                        row$ += ` <td>
                                            <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i>
                                        </td>`;
                    } else {
                        row$ += ` <td>
                                         
                                        </td>`;
                    }

                    row$ += "</tr>"

                } else {
                    alert('Please contact admin!');
                    location.reload();
                    return false;
                }
            }


            $(`${tableid} tbody`).html(row$);
            calc_total();
            $(".addRowBtn").attr('disabled', 'disabled');
            $('.addRowBtn i').css('color', 'gray');
        }
        $('.addRowBtn').click(function() {
            $("#upload-excel").attr('disabled', 'disabled');
            $(".box-excel").css('color', 'gray');
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

    <script type="text/javascript">
        $(window).on('beforeunload', function() {
            var c = confirm();
            // if(!c){
            // console.log('leave')
            var rec_id = $('#txt_req_recid').val();
            // alert(rec_id)
            $.ajax({
                url: "{{ url('form/delete/') }}" + "/"
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
                return /\d/.test(String.fromCharCode(event.keyCode));
            });
            //iterate through each textboxes and add keyup
            //handler to trigger sum event
            $(".sagement").each(function() {

                $(this).keyup(function() {
                    // alert('hello')
                    if (this.value.length == this.maxLength) {
                        //   alert("max")
                        $(this).next('.sagement').focus();

                    }
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
            window.location.href = "{{ route('procurement_request.list') }}";
        }
    </script>

    <!-- end alert blink text -->
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
                    required: false,
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
                    required: true,
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
                    error.appendTo('#tbl1_procurement_error');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                //       $("#first").show();
                //       $('html, body').css({
                //     overflow: 'hidden',
                //     height: '100%'
                // });
                var sagement_con = $('.total_sage').val();
                var product_con = $('.grand_total').val();

                // alert(product_con)
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
            $(".bid_waiver_sole").change(function(){
                if($(this).val() == "yes"){  
                    $("#vendor_bloch").show();
                }else{
                    $("#vendor_bloch").hide();
                    $('#vendor_name').val('');
                    $('#vendor_description').val('');
                }
            });
            
    </script>
    <script>
        $("#tableProcurement tbody").on("click", ".removeRowBtn", function () {
        var child = $(this).closest("tr").nextAll();
        $(this).closest("tr").remove();
        
        calc_total();
      });
      $(".addRowBtn").on("click", function () {
            var total = parseFloat($(".total_price_item").val());
            var sum = 0;
            $(".total_price_item").each(function () {
            sum += parseFloat($(this).val());
            })
            var final_total = sum+total;
            $('.vat').val((final_total*0.1).toFixed(2))
            var vat = parseFloat($('.vat').val());
            $('.grand_total_after_vat').val(final_total+vat);
            $(".subtotal").val(final_total);
      });
        $("#tableProcurement tbody").on("input", ".unit_price", function () {
            var unit_price = parseFloat($(this).val());
            var qty = parseFloat($(this).closest("tr").find(".qty").val());
            var total = $(this).closest("tr").find(".total_price_item");
            total.val(unit_price * qty);
            calc_total();
        });
        
        $("#tableProcurement tbody").on("input", ".qty", function () {
            var qty = parseFloat($(this).val());
            var unit_price = parseFloat($(this).closest("tr").find(".unit_price").val());
            var total = $(this).closest("tr").find(".total_price_item");
            total.val(unit_price * qty);
            calc_total();
        });
        function calc_total() {
            var sum = 0;
            $(".total_price_item").each(function () {
                sum += parseFloat($(this).val());
            });
            $('.vat').val((sum*0.1).toFixed(2))
            $(".subtotal").val(sum);
            var vat = parseFloat($('.vat').val());
            $('.grand_total_after_vat').val(sum+vat);
      }
      $('.checkbox_vat').change(function(){
        if($(this).not(":checked")) {
            $("#vat").removeAttr('readonly');
            $('#vat').val(0);
            var subtotal = $('.subtotal').val();
            $('.grand_total_after_vat').val(subtotal);
        }
        if($(this).is(":checked")){
            $('#vat').prop("readonly", true);
            var subtotal = parseFloat($('.subtotal').val());
            $('#vat').val((subtotal*0.1).toFixed(2));
            var vat = parseFloat($('.vat').val());
            $('.grand_total_after_vat').val(subtotal+vat);
        }
      });
      $('.vat').change(function(){
            var subtotal = parseFloat($('.subtotal').val());
            var vat = parseFloat($('.vat').val());
            $('.grand_total_after_vat').val(subtotal+vat);
      })
        
    </script>

@endsection
