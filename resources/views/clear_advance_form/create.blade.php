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
                        Clear Advance Request
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('form/advances') }}">New</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/clear-advances/save-draft') }}" name="frmCreateNew" id="frmCreateNew"
            enctype="multipart/form-data">
            @csrf
            <div class="myheader" id="myHeader">
                <button type="submit" name="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;" disabled><i
                        class="fa fa-save" aria-hidden="true" style="color: green"></i> Save</button>

                <button type="button" onclick="go_home();" name="submit" value="reject"
                    style="padding: 5px 10px 5px 10px;cursor: pointer;">
                    <i class="fa fa-undo" aria-hidden="true" style="color: red"></i> Cancel</button>
            </div>
            <!-- Page-body start -->
            <div class="page-body">
            <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Clear Advance form  </h4>
                        <i class="text-danger">(After select Advance Form Ref. please click on button search <i class="fa fa-search"></i> before continue)</i>
                        <span id="validate_advance_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Advance Form Ref.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-8" >
                                        <select class="js-example-basic-multiple col-sm-12" multiple="multiple"
                                            name="procurementReferences[]" id="procurementReferences" >
                                            @foreach ($advanceReferences as $value)
                                                <option value="{{ $value->req_recid }}">{{ $value->req_recid }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="btn btn-info" id="fetch_advance_form_btn"><i class="fa fa-search"></i></button>
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
                                        <select class="form-control " id="currency" name="currency">
                                            <option value="" selected="" disabled="" >Select One</option>
                                            <option value="USD">USD</option>
                                            <option value="KHR">KHR</option>
                                        </select>
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
                    <div class="card-block add-remove-tbl">
                        <h4 class="sub-title">Form details </h4>
                        <span id="validate_advance_detail_pannel"></span>
                        <div class="card-block">

                            <div class="col-sm-12"
                                    style="text-align: right;display:flex;align-items: center;justify-content: end;">
                                    <a class="download-template" href="{{ route('form/advances/download-template-excel') }}"><i
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

                            <div class="table-responsive dt-responsive">
                                <table class="table table-striped table-bordered " style="font-size: 12.5px"
                                    id="tableAdvanceForm">
                                    <thead>
                                        <tr class="table-info">
                                            <th>Inv.No</th>
                                            <th><span style="color: red;">*</span> Description</th>
                                            <th><span style="color: red;">*</span> Br./Dep Code</th>
                                            <th><span style="color: red;">*</span> Budget Code</th>
                                            <th>Alternative Budget Code</th>
                                            <th> Unit</th>
                                            <th><span style="color: red;">*</span> QTY</th>
                                            <th> VAT</th>
                                            <th><span style="color: red;">*</span> Unit price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 5px;">
                                                <textarea cols="30" rows="1" style="margin-bottom: 0px;"
                                                        class="tabledit-input form-control input-sm resizetext" type="text"
                                                        id="invoice" name="invoices[]"></textarea>
                                            </td>
                                            <td style="padding: 5px;">
                                                <textarea cols="30" rows="1" style="margin-bottom: 0px;"
                                                        class="tabledit-input form-control input-sm resizetext" type="text"
                                                        id="description" name="descriptions[]"></textarea>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="department_codes"
                                                    name="department_codes[]" style="margin-bottom: 0px;">
                                                    <option value="" disabled="" selected="">Select</option>
                                                    @foreach ($dep_code as $value)
                                                        <option value="{{ $value->branch_code }}">
                                                            {{ $value->branch_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="budget_codes" style="margin-bottom: 0px;"
                                                    name="budget_codes[]">
                                                    <option value="" disabled="" selected="">Select</option>
                                                    @foreach ($budget_code as $value)
                                                        <option value="{{ $value->budget_code }}">
                                                            {{ $value->budget_code }} {{ $value->budget_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select readonly="" class="tabledit-input form-control input-sm" style="margin-bottom: 0px;"
                                                    id="alternative_budget_codes" name="alternative_budget_codes[]">
                                                    <option value="0" selected="">Select</option>
                                                    <!-- @foreach ($alternative_budget_codes as $value)
                                                        <option value="{{ $value->budget_code }}">
                                                            {{ $value->budget_code }}
                                                        </option>
                                                    @endforeach -->
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <textarea cols="30" rows="1"
                                                    class="tabledit-input form-control input-sm resizetext" id="unit"
                                                    name="units[]" style="min-width: 100px; margin-bottom: 0px;"></textarea>
                                            </td>
                                            <td style="padding: 5px;">
                                                <input type="text" style="min-width: 100px; margin-bottom: 0px;"
                                                    class="abledit-input form-control input-sm resizetext numbers" id="qty"
                                                    name="qtys[]" onpaste="return false;" >
                                            </td>
                                            <td style="padding: 5px;">
                                                <input type="text" style="min-width: 100px; margin-bottom: 0px;"
                                                    class="abledit-input form-control input-sm resizetext numbers" id="vat_item"
                                                    name="vat_item[]" onpaste="return false;" >
                                            </td>
                                            <td style="padding: 5px;">
                                                <div class="input-group" style="margin-bottom: 0px;">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>

                                                    <input type="number" class="form-control resizetext" placeholder="0"
                                                        required min="0" step="0,01" id="unit_prices" name="unit_prices[]"
                                                        onpaste="return false;" style="min-width: 100px;">
                                                </div>
                                            </td>
                                            
                                            <td >
                                                <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                            <tr>
                                                <td colspan="8" style="text-align: right;">
                                                    DISCOUNT
                                                </td>
                                                <td style="padding: 5px;">
                                                    <div class="input-group" style="margin-bottom: 0px;">
                                                        <span class="input-group-addon usd">$</span>
                                                        <span class="input-group-addon khr"
                                                            style="font-size: 20px;display: none;">៛</span>
                                                        <input style="min-width: 200px;" type="number" class="form-control resizetext" value="0" min="0"
                                                            step="0,01" id="discount" name="discount" onpaste="return false;">
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="8" style="text-align: right;">
                                                    VAT
                                                </td>
                                                <td style="padding: 5px;">
                                                    <div class="input-group" style="margin-bottom: 0px;">
                                                        <span class="input-group-addon usd">$</span>
                                                        <span class="input-group-addon khr"
                                                            style="font-size: 20px;display: none;">៛</span>
                                                        <input style="min-width: 200px;" type="number" class="form-control resizetext" value="0" min="0"
                                                            step="0,01" id="vat" name="vat" onpaste="return false;">
                                                    </div>

                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="8" style="text-align: right;">
                                                    WHT
                                                </td>
                                                <td style="padding: 5px;">
                                                    <div class="input-group" style="margin-bottom: 0px;">
                                                        <span class="input-group-addon usd">$</span>
                                                        <span class="input-group-addon khr"
                                                            style="font-size: 20px;display: none;">៛</span>
                                                        <input style="min-width: 200px;" type="number" class="form-control resizetext" value="0" required
                                                            min="0" step="0,01" id="wht" name="wht" onpaste="return false;">
                                                    </div>

                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>

                                </table>
                            </div>
                            <div class="row form-group" style="padding-top: 15px;">
                                <div class="col-sm-12 mobile-inputs">
                                    <label for="">Additional Remarks (if any) :</label>
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
                        '<select class="tabledit-input form-control input-sm selectbox_depcode" id="department_codes"  name="department_codes[]">';
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
                        '<select class="tabledit-input form-control input-sm" id="budget_codes" name="budget_codes[]">';
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
                        '<select  readonly="" class="tabledit-input form-control input-sm" id="alternative_budget_codes" name="alternative_budget_codes[]">';
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
                              <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="invoices" name="invoices[]">${jsondata[i]['Inv.No']}</textarea>
                              </label>
                           </td>`;
                    row$ += ` <td>
                              <label>
                                <textarea cols="30" rows="1"
                                                        class="tabledit-input form-control input-sm resizetext" type="text"
                                                        id="descriptions" name="descriptions[]">${jsondata[i]['Description']}</textarea>
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
                                                    class="tabledit-input form-control input-sm resizetext" id="units"
                                                    name="units[]">${jsondata[i]['Unit']}</textarea>
                           </td>`;
                    row$ += `<td>
                            <input type="text" value="${jsondata[i]['QTY']}"
                                                    class="abledit-input form-control input-sm resizetext numbers" id="qtys"
                                                    name="qtys[]" onpaste="return false;">
                           </td>`;
                    row$ += `<td>
                            <div class="input-group">
                                                    <span class="input-group-addon usd">$</span>
                                                    <span class="input-group-addon khr"
                                                        style="font-size: 20px;display: none;">៛</span>

                                                    <input type="number" class="form-control resizetext" placeholder="0"
                                                        required min="0" step="0,01" id="unit_prices" name="unit_prices[]"
                                                        onpaste="return false;" value="${jsondata[i]['Unit Price']}">
                                                </div>
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
        $('#fetch_advance_form_btn').on('click', function() {
            var references = $('#procurementReferences').val();
            if (references == '') {
                alert('No record select');
            } else {
                var url_redirect = "{{ url('/') }}" + '/form/clear-advances/save-advance-references/' +
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
                'procurementReferences[]': {required: true,},
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
                'procurementReferences[]':"Please input mandatory field",
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
                    $('#validate_advance_pannel').empty();
                    error.appendTo('#validate_advance_pannel');
                }

                //=== category ===
                if( element.attr("name") == "subject"){
                    $('#validate_advance_pannel').empty();
                    error.appendTo('#validate_advance_pannel');
                }
                //=== validate advance pannel ===
                if(
                    element.attr("name") == "department" || 
                    element.attr("name") == "due_date" || 
                    element.attr("name") == "request_date" ||
                    element.attr("name") == "procurementReferences[]" ||
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
