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

</style>
@section('menu')
    @include('siderbar.payment')
@endsection
@section('breadcrumb')
    @include('breadcrumb.payment_new')
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/payment/save') }}" name="registration" id="registration"
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
                                    <div class="col-sm-8">
                                        <select class="js-example-basic-multiple col-sm-12" multiple="multiple"
                                            name="pr_ref[]" id="pr_ref">

                                            @foreach ($pr_approve_notpaid as $value)
                                                <option value="{{ $value->req_recid }}">{{ $value->req_recid }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="btn btn-info" id="fetch_data_btn"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Department :</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ Auth::user()->department }}"
                                            readonly="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">Request Date:</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="requestDate" name="requestDate"
                                            value="{{ date('d/m/y') }}" readonly="">
                                    </div>
                                    <div class="col-sm-2">
                                        <label for=""><span style="color: red;">* </span> Due Date :</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="date" class="form-control" id="expDate" name="expDate">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="">
                                            <span style="color: red;">*</span> Currency:
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control " id="currency" name="currency">
                                            <option value="" selected="" disabled="">Select One</option>
                                            <option value="USD">USD</option>
                                            <option value="KHR">KHR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mobile-inputs">
                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <label for=""><span style="color: red;">*</span> Subject:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" placeholder="Subject" id="subject"
                                                    name="subject" rows="2"></textarea>
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
                                            <input class="border-checkbox chb_1" type="checkbox" id="type" name="type[]"
                                                value="Payment">
                                            <label class="border-checkbox-label" for="type">Payment</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox chb_1" type="checkbox" id="type1" name="type[]"
                                                value="Deposit">
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
                                            <input type="text" class="form-control" id="account_number"
                                                name="account_number">
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
                                            <textarea class="form-control" rows="2" id="bank_address"
                                                name="bank_address"></textarea>
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
                                            <textarea class="form-control" rows="2" id="address_who"
                                                name="address_who"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-block add-remove-tbl">
                        <h4 class="sub-title">Payment Request </h4>
                        <span id="tbl_procurement_error"></span>
                        <div class="card-block">
                            <div class="table-responsive dt-responsive">
                                <div class="col-sm-12"
                                    style="text-align: right;display:flex;align-items: center;justify-content: end;">
                                    <a class="download-template" href="{{ route('download/payment-excel') }}"><i
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
                                <table class="table table-striped table-bordered" style="font-size: 12.5px"
                                    id="tablePayment">
                                    <thead>
                                        <tr class="table-info">
                                            <th><span style="color: red;">*</span> Inv.No</th>
                                            <th><span style="color: red;">*</span> Description</th>
                                            <th><span style="color: red;">*</span> Br./Dep Code</th>
                                            <th><span style="color: red;">*</span> Budget Code</th>
                                            <th>Alternative Budget Code</th>
                                            <th><span style="color: red;">*</span> Unit</th>
                                            <th><span style="color: red;">*</span> QTY</th>
                                            <th><span style="color: red;">*</span> Unit price</th>
                                            <th>VAT</th>
                                            <th style="display: none;">Total</th>
                                            <th style="display: none;">YTD Expense</th>
                                            <th style="display: none;">Total Budget</th>
                                            <th style="display: none;">Within Budget</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label>
                                                    <textarea cols="30" rows="1"
                                                        class="tabledit-input form-control input-sm resizetext" type="text"
                                                        id="invoice" name="invoice[]"></textarea>
                                                </label>
                                            </td>
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
                                                            {{ $value->budget_code }} {{ $value->budget_item }}
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
                                                    class="abledit-input form-control input-sm resizetext numbers" id="qty"
                                                    name="qty[]" onpaste="return false;">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>

                                                    <input type="number" class="form-control resizetext" placeholder="0"
                                                        required min="0" step="0,01" id="unit_price" name="unit_price[]"
                                                        onpaste="return false;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>

                                                    <input type="text"
                                                        class="abledit-input form-control input-sm resizetext vat_item" id="vat_item"
                                                        name="vat_item[]" onpaste="return false;">
                                                </div>
                                               
                                            </td>
                                            <td style="display: none;">
                                                <textarea cols="30" rows="1"
                                                    class="tabledit-input tabledit-input form-control input-sm resizetext"
                                                    id="total" name="total"></textarea>
                                            </td>
                                            <td style="display: none;">
                                                <textarea cols="30" rows="1"
                                                    class="tabledit-input form-control input-sm resizetext" id="ytd"
                                                    name="ytd"></textarea>
                                            </td>
                                            <td style="display: none;">
                                                <textarea cols="30" rows="1"
                                                    class="tabledit-input form-control input-sm resizetext" id="totalbgt"
                                                    name="totalbgt"></textarea>
                                            </td>
                                            <td style="display: none;">
                                                <textarea cols="30" rows="1"
                                                    class="tabledit-input form-control input-sm resizetext" id="within"
                                                    name="within"></textarea>
                                            </td>
                                            <td>
                                                <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr style="display: none;">
                                            <td colspan="8" style="text-align: right;">
                                                SUB TOTAL
                                            </td>
                                            <td>
                                                <input type="text" class="tabledit-input form-control input-sm resizetext"
                                                    id="sub_total" name="sub_total" onpaste="return false;">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" style="text-align: right;">
                                                DISCOUNT
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input type="number" class="form-control resizetext" value="0" min="0"
                                                        step="0,01" id="discount" name="discount" onpaste="return false;">
                                                    <input type="hidden" class="form-control resizetext" value="0" min="0"
                                                        step="0,01" id="vat" name="vat" onpaste="return false;">
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <!-- <tr>
                                            <td colspan="8" style="text-align: right;">
                                                VAT
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input type="number" class="form-control resizetext" value="0" min="0"
                                                        step="0,01" id="vat" name="vat" onpaste="return false;">
                                                </div>

                                            </td>
                                            <td></td>
                                        </tr> -->
                                        <tr>
                                            <td colspan="8" style="text-align: right;">
                                                WHT
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input type="number" class="form-control resizetext" value="0" required
                                                        min="0" step="0,01" id="wht" name="wht" onpaste="return false;">
                                                </div>

                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" style="text-align: right;">
                                                DEPOSIT
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>
                                                    <input type="number" class="form-control resizetext" value="0" min="0"
                                                        step="0,01" id="deposit" name="deposit" onpaste="return false;">
                                                </div>

                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
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
                                <span id="sagement_error" style="color: red;font-size: 15px; font-weight: bold;"></span>
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
                                                    style="width: 50px !important;" type="text" name="general" id="general"
                                                    onpaste="return false;">

                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers txt sagement"
                                                    style="width: 50px !important;" type="text" name="loan_general"
                                                    id="loan_general" onpaste="return false;">
                                            </td>
                                            <td align="center">
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

                                                <input type="hidden" name="" class="total_sage">
                                            </td>
                                            <td align="center">
                                                <input class="form-control numbers counter" type="text"
                                                    style="width: 150px !important;" name="pcp" id="pcp"
                                                    onpaste="return false;">

                                                <input type="hidden" name="" class="total_sage">
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
                                <div class="col-sm-4 mobile-inputs">
                                    <label for="">Additional Remarks (if any) for product and segment :</label>
                                </div>
                                <div class="col-sm-8 mobile-inputs">
                                    <textarea name="remarks_product_segment" name="remarks_product_segment"
                                        class="form-control "></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                    <h5><i style="color: red;">* </i> File Upload</h5>
                        <span id="document_spn"></span>
                    </div>
                    <div class="card-block">
                        <input type="file" name="fileupload[]" id="filer_input" multiple="multiple" required>
                    </div>
                </div>
            </div>
            <!-- Row Created Callback table end -->
        </form>


        <button type="button" class="btn btn-warning alert-confirm1 m-b-10" id="btn_reload"
            style="display: none;">Confirm</button>

    </div>
    <form id="delete_frm" method="get">
        {{ csrf_field() }}
        <input type="hidden" name="param_req_recid" id="param_req_recid">
    </form>

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
                        BindTable(JSON.parse(json_object), "#tablePayment");

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
            if (_string.match(/^[-+]?[0-9]*\.?[0-9]+$/) != null) {
                return true;
            }
            return false;
        }
        function BindTable(jsondata, tableid) {
            /*Function used to convert the JSON array to Html Table*/
            var row$ = "<tr>";
            for (var i = 0; i < jsondata.length; i++) {
                if (jsondata[i].hasOwnProperty('Inv.No') && jsondata[i].hasOwnProperty('Description') && jsondata[i]
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
                              <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="invoice" name="invoice[]">${jsondata[i]['Inv.No']}</textarea>
                              </label>
                           </td>`;
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
                            <input type="text" value="${jsondata[i]['QTY']}"
                                                    class="abledit-input form-control input-sm resizetext numbers" id="qty"
                                                    name="qty[]" onpaste="return false;">
                           </td>`;
                    row$ += `<td>
                            <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>

                                                    <input type="text" class="form-control resizetext" placeholder="0"
                                                        required min="0" step="0,01" id="unit_price" name="unit_price[]"
                                                        onpaste="return false;" value="${jsondata[i]['Unit Price']}">
                                                </div>
                           </td>`;
                    row$ += `<td>
                            <input type="text" value="${jsondata[i]['VAT']}"
                                                    class="abledit-input form-control input-sm resizetext numbers" id="vat_item"
                                                    name="vat_item[]" onpaste="return false;">
                           </td>`;
                    if (jsondata.length > 1) {
                        row$ += ` <td>
                                                <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i>
                                            </td>`;
                    }else{
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

            $(".addRowBtn").attr('disabled', 'disabled');
            $('.addRowBtn i').css('color', 'gray');
        }
        $('.addRowBtn').click(function() {
            $("#upload-excel").attr('disabled', 'disabled');
            $(".box-excel").css('color', 'gray');
        });
    </script>
    <script>
        $('#fetch_data_btn').on('click', function() {
            var pr_ref = $('#pr_ref').val();
            // alert(pr_ref)
            if (pr_ref == '') {
                alert('No record select');
            } else {
                var url_redirect = "{{ url('/') }}" + '/form/payment/new/ref/' +
                    "{{ Crypt::encrypt('"+pr_ref+"') }}" + '/' + pr_ref;
                window.location = url_redirect;
            }
            // var url_redirect="{{ url('/') }}"+'/form/payment/new/ref/'+pr_ref;

            // $('#delete_frm').prop('action', url_redirect);
            // // alert(pr_ref)
            // $("#delete_frm").submit();
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
        $("input.vat_item").keypress(function(event) {
            return /^(\d)*(\.)?([0-9]{1})?$/.test(String.fromCharCode(event.keyCode));
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
            window.location.href = "{{ route('payment_request.list_auth') }}";
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
            // ignore: "[readonly]",

            rules: {
                department: {
                    required: true,
                },
                requestDate: {
                    required: true,
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
                    required: false,
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
                expDate: {
                    required: true,
                },
                vat_item: {
                    required: false,
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
                // 'alternative_budget_code[]': "(Please fill all *)",

                purpose_rationale: "(Please fill all *)",
                bid_waiver_sole: "(Please fill all *)",
                'vendor_name[]': "(Please fill all *)",
                'vendor_description[]': "(Please fill all *)",
                justification_for_request: "(Please fill all *)",
                comment_by_procurement: "(Please fill all *)",



            },

            errorPlacement: function(error, element) {
                
                if (element.attr("name") == "account_number" || element.attr("name") == "account_name" ||
                    element.attr("name") == "bank_name" ||
                    element.attr("name") == "swift_code" ||
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
                } else if (element.attr("name") == "currency" || element.attr("name") == "subject" || element.attr("name") == "expDate" || element
                    .attr("name") == "type[]" ||
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
