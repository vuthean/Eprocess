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
                        <h4>Cash Payment Voucher Requests</h4>
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
                            Cash Payment Voucher
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('form/cash-payment-vouchers') }}">New</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="col-sm-12">
        <form method="post" action="{{ route('form/cash-payment-vouchers/save-draft') }}" name="frmCreateNew" id="frmCreateNew"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="grand_total" name="currency" id="currency" value="USD">
            <div class="myheader" id="myHeader">
                <button type="submit" name="submit" style="padding: 5px 10px 5px 10px;cursor: pointer;"><i
                        class="fa fa-save" aria-hidden="true" style="color: green"></i> Save</button>

                <button type="button" onclick="go_home();" name="submit" value="cancel"
                    style="padding: 5px 10px 5px 10px;cursor: pointer;">
                    <i class="fa fa-undo" aria-hidden="true" style="color: red"></i> Cancel</button>
            </div>
            <!-- Page-body start -->
            <div class="page-body">
                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Cash Payment Voucher</h4>
                        <span id="validate_currency"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Reference No.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        <select class="js-example-basic-multiple col-sm-12"
                                            multiple="multiple"
                                            name="references[]" id="references" >
                                            @foreach ($references as $value)
                                                <option value="{{ $value->req_recid }}">{{ $value->req_recid }}</option>
                                            @endforeach
                                        </select>
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
                                        <input type="text" class="form-control" value="{{ $user->department}}" readonly="" name="department">
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
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Voucher No.</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="voucher_number" name="voucher_number">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Exchange rate</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="number" style="min-width: 80px; margin-bottom: 0px;" class="abledit-input form-control input-sm  numbers"
                                                id="exchange_rate" name="exchange_rate" onpaste="return false;" value="1" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block add-remove-tbl">
                        <h4 class="sub-title">Form details </h4>
                        <span id="validate_item_detail"></span>
                        <div id="item_detail_error" style="color: red;font-size: 15px; font-weight: bold;"></div>
                        <div class="card-block">
                            <div class="col-sm-12" style="text-align: right;display:flex;align-items: center;justify-content: end;">
                                    <a class="download-template" href="{{ route('form/cash-payment-vouchers/download-template-excel') }}"><i
                                        class="fa fa-download"></i> Download Template</a>
                                    <div class="box-excel" style="margin-right:10px;">
                                        <input type="file" id="upload-excel" style="display: none;"
                                            accept=".xlsx, .xls, .csv">
                                        <label for="upload-excel"><i class="fa fa-upload"></i> <span>Choose a
                                                file</span></label>
                                    </div>
                                    <button class="addRowBtn" type="button">
                                        <i class="fa fa-plus-square" style="font-size: 20px;color: #0ac282;"></i>
                                    </button>
                                </div>

                            <div class="table-responsive dt-responsive">
                                <table class="table table-striped table-bordered " style="font-size: 12.5px"
                                    id="tableBankPaymentVoucherForm">
                                    <thead>
                                        <tr class="table-info">
                                            <th><span style="color: red;">*</span> GL CODE</th>
                                            <th><span style="color: red;">*</span> ACCOUNT NAME</th>
                                            <th><span style="color: red;">*</span> BRANCH CODE</th>
                                            <th><span style="color: red;">*</span> DR/CR</th>
                                            <th><span style="color: red;">*</span> CURRENCY</th>
                                            <th><span style="color: red;">*</span> AMOUNT</th>
                                            <th><span style="color: red;">*</span> BUDGET CODE</th>
                                            <th>AL BUDGET CODE</th>
                                            <th>TAX CODE</th>
                                            <th>SUPP CODE</th>
                                            <th>DEPT CODE</th>
                                            <th>PRO CODE</th>
                                            <th>SEG CODE</th>
                                            <th>NARRATIVES</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="gl_codes" name="gl_codes[]" style="margin-bottom: 0px;">
                                                    <option value=""  selected="">Select</option>
                                                    @foreach ($generalLedgerCodes as $value)
                                                        <option value="{{ $value->account_number }}" name="{{ $value->account_name }}">{{ $value->account_number }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <input type="text" name="account_names[]" id="account_names" class="form-control" readonly="">
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="branch_codes" name="branch_codes[]" style="margin-bottom: 0px;">
                                                    <option value="" selected="">Select</option>
                                                    @foreach ($brancheCodes as $value)
                                                        <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select  class="tabledit-input form-control input-sm" id="dr_crs" name="dr_crs[]" style="margin-bottom: 0px;">
                                                    <option value="DEBIT" selected>DR</option>
                                                    <option value="CREDIT">CR</option>
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="currencies" name="currencies[]" style="margin-bottom: 0px;">
                                                    <option value="USD" selected>USD</option>
                                                    <option value="KHR">KHR</option>
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <input type="numbers" style="min-width: 80px; margin-bottom: 0px;" class="abledit-input form-control input-sm  numbers"
                                                id="amounts" name="amounts[]" onpaste="return false;" value="0" >
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="budget_codes" name="budget_codes[]" style="margin-bottom: 0px;">
                                                    <option value="" selected="">Select</option>
                                                    @foreach ($budgetCodes as $value)
                                                        <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="al_budget_codes" name="al_budget_codes[]" style="margin-bottom: 0px;">
                                                    <option value="" selected="">Select</option>
                                                    @foreach ($budgetCodes as $value)
                                                        <option value="{{ $value->budget_code }}">{{ $value->budget_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="tax_codes" name="tax_codes[]" style="margin-bottom: 0px;">
                                                    <option value=""  selected="">Select</option>
                                                    @foreach ($taxCodes as $value)
                                                        <option value="{{ $value->code }}">{{ $value->code }}@endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="supp_codes" name="supp_codes[]" style="margin-bottom: 0px;">
                                                    <option value="" selected="">Select</option>
                                                    @foreach ($supplierCodes as $value)
                                                        <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="dept_codes" name="dept_codes[]" style="margin-bottom: 0px;">
                                                    <option value=" " selected="">Select</option>
                                                    @foreach ($departmentCodes as $value)
                                                        <option value="{{ $value->branch_code }}">{{ $value->branch_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="pro_codes" name="pro_codes[]" style="margin-bottom: 0px;">
                                                    <option value=""  selected="">Select</option>
                                                    @foreach ($productCodes as $value)
                                                        <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <select class="tabledit-input form-control input-sm" id="seg_codes" name="seg_codes[]" style="margin-bottom: 0px;">
                                                    <option value="" selected="">Select</option>
                                                    @foreach ($segmentCodes as $value)
                                                        <option value="{{ $value->code }}">{{ $value->code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 5px;">
                                                <textarea cols="30" rows="1" style="margin-bottom: 0px;"
                                                class="tabledit-input form-control input-sm resizetext" type="text"
                                                id="naratives" name="naratives[]"></textarea>
                                            </td>
                                            <td >
                                                <i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <h4 class="sub-title">Account Information</h4>
                        <span id="validate_account_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Payment Method</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9" >
                                        <select class="tabledit-input form-control input-sm" id="payment_method" name="payment_method" style="margin-bottom: 0px;">
                                            <option value="" disabled="" selected="">Select</option>
                                            @foreach ($paymentMethods as $value)
                                                <option value="{{ $value->name }}">
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Beneficiary Bank</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="bank_name" id="bank_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Swift Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="swift_code" id="swift_code">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Address</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_currency" id="account_currency">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Cheque/Account Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_name" id="account_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Account Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_number" id="account_number">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Address</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="benificiary_name" id="benificiary_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span>Purpose</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="invoice_number" id="invoice_number">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row form-group" style="padding-top: 15px;">
                            <div class="col-sm-12 mobile-inputs">
                                <label for="">Note :</label>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <textarea name="note" class="form-control "></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i style="color: red;">*</i> File Upload</h5>
                        <span id="validate_file_upload_pannel"></span>
                    </div>
                    <div class="card-block">
                        <input type="file" name="fileupload[]" id="filer_input" multiple="multiple" required>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <form id="delete_frm" method="get">
        {{ csrf_field() }}
        <input type="hidden" name="param_req_recid" id="param_req_recid">
    </form>
@endsection
@section('script')
    <script src="{{ URL::to('js/PDF/xlsx.js') }}"></script>
    <script src="{{ URL::to('static/clone/patuta.min.js') }}"></script>
    <script>
        $('body').patuta();
    </script>
    <script>
        $('#references').on('change',function(){
            var selected = ($(this).val()).toString();
            var requests = selected.split(",");
            var references = requests[0].toString();
            var url_redirect = "{{ url('/') }}" + '/form/cash-payment-vouchers/get-rquest-info/' + "{{ Crypt::encrypt('" + references + "') }}" + '/' + references;
            $.ajax({
                url: url_redirect,
                type:"GET",
                success:function(response){
                    if(response) {
                        $('#bank_name').val(response['bank_name']);
                        $('#account_name').val(response['account_name']);
                        $('#account_number').val(response['account_number']);
                    }
                },
                error: function(error) {
                console.log(error);
                }
            });

        });

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
            $(document).ready(function() {
                $('#item_detail_error').hide();
            });
        </script>
    <!-- alert blink text -->
    <script>
        function blink_text() {
            $('#item_detail_error').fadeOut(700);
            $('#item_detail_error').fadeIn(700);
        }
        setInterval(blink_text, 1000);
        function go_home() {
            window.location.href = "{{ route('form/cash-payment-vouchers') }}";
        }
    </script>
    <script>
        $("#frmCreateNew").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                payment_method  : {required: true, },
                'gl_codes[]'    : {required: true, },
                'branch_codes[]': {required: true, },
                'dr_crs[]'      : {required: true, },
                'currencies[]'     : {required: true, },
                'amounts[]'     : {required: true, },
                'budget_codes[]': {required: true, },
                'fileupload[]': {required: true, },
            },

            messages: {
                payment_method: "Please input mandatory field",
                'gl_codes[]':"Please input mandatory field",
                'branch_codes[]':"Please input mandatory field",
                'dr_crs[]':"Please input mandatory field",
                'currencies[]':"Please input mandatory field",
                'amounts[]':"Please input mandatory field",
                'budget_codes[]':"Please input mandatory field",
                'fileupload[]':"Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                //=== validate advance pannel ===
                if(
                    element.attr("name") == "gl_codes[]" ||
                    element.attr("name") == "branch_codes[]" ||
                    element.attr("name") == "dr_crs[]" ||
                    element.attr("name") == "currencies[]" ||
                    element.attr("name") == "amounts[]" ||
                    element.attr("name") == "budget_codes[]"
                    ){
                    $('#validate_item_detail').empty();
                    error.appendTo('#validate_item_detail');
                }

                if(element.attr("name") == "payment_method"){
                    $('#validate_account_pannel').empty();
                    error.appendTo('#validate_account_pannel');
                }
                //===== file upload =====
                if(element.attr("name") == "fileupload[]"){
                    $('#validate_file_upload_pannel').empty();
                    error.appendTo('#validate_file_upload_pannel');
                }
            },
            submitHandler: function(form) {
                $('#item_detail_error').hide();
                $('#item_detail_error').text('');

                var isSuccuss  = true;

                var amounts    = $("input[name='amounts[]']").map(function(){return $(this).val();}).get();
                var currencies = $("select[name='currencies[]']").map(function(){return $(this).val();}).get();
                var dr_crs     = $("select[name='dr_crs[]']").map(function(){return $(this).val();}).get();

                // check if all item cross currency or not
                var uniqueCurrencies = currencies.filter((v, i, a) => a.indexOf(v) === i);
                if(uniqueCurrencies.length == 1){
                        // find debit amount
                        var totalDebitAmount = 0;
                        var totalCreditAmount = 0;
                    for(var i = 0; i < amounts.length; i++){
                        var currency = currencies[i];
                        var drcr = dr_crs[i];
                        var amount = parseFloat(amounts[i]);
                        var exchange_rate = parseFloat(exchangeRate);

                        if(drcr == 'DEBIT'){
                            var debitAmount = amount;
                            totalDebitAmount += debitAmount;
                        }

                        if(drcr == 'CREDIT'){
                            var creditAmount = amount;
                            totalCreditAmount += creditAmount;
                        }
                    }
                    if(parseFloat(totalCreditAmount).toFixed(2) != parseFloat(totalDebitAmount).toFixed(2)){
                        isSuccuss = false;
                        $('#item_detail_error').show();
                        $('#item_detail_error').append('<span><ul>'+
                        '<li>Sorry! Total CREDIT must be equalt to Total DEBIT amount.</li>'+
                        '<li>Total DEBIT Amount  ('+currencies[0]+') : '+parseFloat(totalDebitAmount).toFixed(2)+'</li>'+
                        '<li>Total CREDIT Amount ('+currencies[0]+') : '+parseFloat(totalCreditAmount).toFixed(2)+'</li>'+
                        '</ul></span>');
                        return;
                    }
                }else{
                    // it is used when item is cross currency
                    var totalCrossDebitAmount = 0;
                    var totalCrossCreditAmount = 0;
                    var exchangeRate = $("#exchange_rate").val();
                    for(var i = 0; i < amounts.length; i++){

                        var currency = currencies[i];
                        var drcr = dr_crs[i];
                        var amount = parseFloat(amounts[i]);
                        var exchange_rate = parseFloat(exchangeRate);

                        if(drcr == 'DEBIT'){
                            var debitAmount = amount;
                            if(currency == 'KHR'){
                                var debitAmount = amount / exchangeRate;
                                console.log('====debitAmount==');
                                console.log(debitAmount);
                            }
                            totalCrossDebitAmount += debitAmount;
                        }

                        if(drcr == 'CREDIT'){
                            var creditAmount = amount;
                            if(currency == 'KHR'){
                                creditAmount = amount / exchangeRate;
                            }
                            totalCrossCreditAmount += creditAmount;
                        }
                    }

                    var formatTotalCrossCreditAmount = parseFloat(totalCrossCreditAmount).toFixed(2);
                    var formatTotalCrossDebitAmount = parseFloat(totalCrossDebitAmount).toFixed(2);
                    if(formatTotalCrossCreditAmount != formatTotalCrossDebitAmount){
                        isSuccuss = false;
                        $('#item_detail_error').show();
                        $('#item_detail_error').append('<span><ul>'+
                        '<li>Sorry! Total CREDIT must be equalt to Total DEBIT amount.</li>'+
                        '<li>Total DEBIT Amount  (USD) : '+formatTotalCrossDebitAmount+'</li>'+
                        '<li>Total CREDIT Amount (USD) : '+formatTotalCrossCreditAmount+'</li>'+
                        '</ul></span>');
                        return;
                    }
                }

                // find total row in data table
                var totalRow = credits.length;

                // make sure user give full GL CODE for each row of table
                var glCodes = $("select[name='gl_codes[]']").map(function(){return $(this).val();}).get();
                if(totalRow != glCodes.length){
                    $('#item_detail_error').show();
                    $('#item_detail_error').text('Sorry! Make sure you select all GL CODE for all row.');
                    isSuccuss = false;
                    return;
                }

                // validation on brach codes
                var branchCodes = $("select[name='branch_codes[]']").map(function(){return $(this).val();}).get();
                if(totalRow != branchCodes.length){
                    $('#item_detail_error').show();
                    $('#item_detail_error').text('Sorry! Make sure you select all Branch Code.');
                    isSuccuss = false;
                    return;
                }

                // validation on brach codes
                var budgetCodes = $("select[name='budget_codes[]']").map(function(){return $(this).val();}).get();
                if(totalRow != budgetCodes.length){
                    $('#item_detail_error').show();
                    $('#item_detail_error').text('Sorry! Make sure you select all Budget Code.');
                    isSuccuss = false;
                    return;
                }

                if(isSuccuss == true){
                    $('#item_detail_error').hide();
                    $('#item_detail_error').text('');
                    $(".overlay").show();
                    form.submit();
                }
            }
        });
    </script>

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
                        BindTable(JSON.parse(json_object), "#tableBankPaymentVoucherForm");

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
            if (_string.match(/^[0-9]*\?[0-9]*$/) != null) {
                return true;
            }
            return false;
        }
        function BindTable(jsondata, tableid) {

            /*Function used to convert the JSON array to Html Table*/
            var row$ = "<tr>";
            for (var i = 0; i < jsondata.length; i++) {
                if (
                    jsondata[i].hasOwnProperty('GL_CODE') &&
                    jsondata[i].hasOwnProperty('BRANCH_CODE') &&
                    jsondata[i].hasOwnProperty('DR/CR') &&
                    jsondata[i].hasOwnProperty('CURRENCY') &&
                    jsondata[i].hasOwnProperty('AMOUNT') &&
                    jsondata[i].hasOwnProperty('BUDGET_CODE')){

                    // // MAKE SURE DEBIT AND CREDIT IS NUMBER NOT STRING
                    // if (checkIfStringHasOnlyDigits(jsondata[i]['AMOUNT']) == false){
                    //     alert('Make sure your CREDIT AND DEBIT AMOUNT IS NOT STRING');
                    //     location.reload();
                    //     return;
                    // }

                    /* =========== BIND GL CODE TO DROP DOWN ==============*/
                    var generalLedgerCodes = @json($generalLedgerCodes);
                    var acountName = '';
                    var selectBoxGLCode = '<select class="tabledit-input form-control input-sm " id="gl_codes"  name="gl_codes[]">';
                        selectBoxGLCode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < generalLedgerCodes.length; j++) {
                        if (generalLedgerCodes[j]['account_number'] == jsondata[i]['GL_CODE']) {
                            selectBoxGLCode +=`<option value="${generalLedgerCodes[j]['account_number']}" selected>${generalLedgerCodes[j]['account_number']}</option>`;
                            acountName = `<input type="text" value="${generalLedgerCodes[j]['account_name']}" class="abledit-input form-control input-sm resizetext numbers" id="account_names" name="account_names[]" onpaste="return false;" readonly="">`;
                        } else {
                            selectBoxGLCode +=`<option value="${generalLedgerCodes[j]['account_number']}">${generalLedgerCodes[j]['account_number']}</option>`;
                        }
                    }
                    selectBoxGLCode += '</select>'

                    /* =========== BIND DR/CR TO DROP DOWN ==============*/
                    var selectBoxDRCR = '<select class="tabledit-input form-control input-sm" id="dr_crs" name="dr_crs[]">';
                        if (jsondata[i]['DR/CR'] == 'DR') {
                            selectBoxDRCR +=`<option value="DEBIT" selected>DR</option>`;
                        } else {
                            selectBoxDRCR +=`<option value="DEBIT">DR</option>`;
                        }
                        if (jsondata[i]['DR/CR'] == 'CR') {
                            selectBoxDRCR +=`<option value="CREDIT" selected>CR</option>`;
                        } else {
                            selectBoxDRCR +=`<option value="CREDIT">CR</option>`;
                        }
                        selectBoxDRCR += '</select>'

                     /* =========== BIND CURRENCY TO DROP DOWN ==============*/
                     var selectBoxCurrency = '<select class="tabledit-input form-control input-sm" id="currencies" name="currencies[]">';
                        if (jsondata[i]['CURRENCY'] == 'USD') {
                            selectBoxCurrency +=`<option value="USD" selected>USD</option>`;
                        } else {
                            selectBoxCurrency +=`<option value="USD">USD</option>`;
                        }
                        if (jsondata[i]['CURRENCY'] == 'KHR') {
                            selectBoxCurrency +=`<option value="KHR" selected>KHR</option>`;
                        } else {
                            selectBoxCurrency +=`<option value="KHR">KHR</option>`;
                        }
                        selectBoxCurrency += '</select>'

                    /* =========== BIND BRANCH CODE TO DROP DOWN ==============*/
                    var branchCodes = @json($brancheCodes);
                    var selectboxBranchCode = '<select class="tabledit-input form-control input-sm" id="branch_codes" name="branch_codes[]">';
                        selectboxBranchCode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < branchCodes.length; j++) {
                        if (branchCodes[j]['code'] == jsondata[i]['BRANCH_CODE']) {
                            selectboxBranchCode +=`<option value="${branchCodes[j]['code']}" selected>${branchCodes[j]['code']}</option>`;
                        } else {
                            selectboxBranchCode +=`<option value="${branchCodes[j]['code']}">${branchCodes[j]['code']}</option>`;
                        }
                    }
                    selectboxBranchCode += '</select>'

                    /* =========== BIND BUDGET CODE TO DROP DOWN ==============*/
                    var budgetCodes = @json($budgetCodes);
                    var selectboxBudgetCode = '<select class="tabledit-input form-control input-sm" id="budget_codes" name="budget_codes[]">';
                        selectboxBudgetCode += '<option value="" selected="">Select</option>';
                    for (var k = 0; k < budgetCodes.length; k++) {
                        if (budgetCodes[k]['budget_code'] == jsondata[i]['BUDGET_CODE']) {
                            selectboxBudgetCode +=`<option value="${budgetCodes[k]['budget_code']}" selected>${budgetCodes[k]['budget_code']}</option>`;
                        } else {
                            selectboxBudgetCode +=`<option value="${budgetCodes[k]['budget_code']}">${budgetCodes[k]['budget_code']}</option>`;
                        }
                    }
                    selectboxBudgetCode += '</select>'

                     /* =========== BIND AL BUDGET CODE TO DROP DOWN ==============*/
                    var alBudgetCodes = @json($budgetCodes);
                    var selectboxAlBudgetCode = '<select class="tabledit-input form-control input-sm" id="al_budget_codes" name="al_budget_codes[]">';
                        selectboxAlBudgetCode += '<option value="" selected="">Select</option>';
                    for (var k = 0; k < alBudgetCodes.length; k++) {
                        if (alBudgetCodes[k]['budget_code'] == jsondata[i]['AL_BUDGET_CODE']) {
                            selectboxAlBudgetCode +=`<option value="${alBudgetCodes[k]['budget_code']}" selected>${alBudgetCodes[k]['budget_code']}</option>`;
                        } else {
                            selectboxAlBudgetCode +=`<option value="${alBudgetCodes[k]['budget_code']}">${alBudgetCodes[k]['budget_code']}</option>`;
                        }
                    }
                    selectboxAlBudgetCode += '</select>'

                    /* =========== BIND TAX CODE TO DROP DOWN ==============*/
                    var taxCodes = @json($taxCodes);
                    var selectboxTAXCode = '<select class="tabledit-input form-control input-sm" id="tax_codes" name="tax_codes[]">';
                        selectboxTAXCode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < taxCodes.length; j++) {
                        if (taxCodes[j]['code'] == jsondata[i]['TAX_CODE']) {
                            selectboxTAXCode +=`<option value="${taxCodes[j]['code']}" selected>${taxCodes[j]['code']}</option>`;
                        } else {
                            selectboxTAXCode +=`<option value="${taxCodes[j]['code']}">${taxCodes[j]['code']}</option>`;
                        }
                    }
                    selectboxTAXCode += '</select>'

                    /* =========== BIND SUPP CODE TO DROP DOWN ==============*/
                    var suppCodes = @json($supplierCodes);
                    var selectboxSUPPCode = '<select class="tabledit-input form-control input-sm" id="supp_codes" name="supp_codes[]">';
                        selectboxSUPPCode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < suppCodes.length; j++) {
                        if (suppCodes[j]['code'] == jsondata[i]['SUPP_CODE']) {
                            selectboxSUPPCode +=`<option value="${suppCodes[j]['code']}" selected>${suppCodes[j]['code']}</option>`;
                        } else {
                            selectboxSUPPCode +=`<option value="${suppCodes[j]['code']}">${suppCodes[j]['code']}</option>`;
                        }
                    }
                    selectboxSUPPCode += '</select>'

                    /* =========== BIND DEPARTMENT CODE TO DROP DOWN ==============*/
                    var departmentCodes = @json($departmentCodes);
                    var selectboxDEPTCode = '<select class="tabledit-input form-control input-sm" id="dept_codes" name="dept_codes[]">';
                        selectboxDEPTCode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < departmentCodes.length; j++) {
                        if (departmentCodes[j]['branch_code'] == jsondata[i]['DEPT_CODE']) {
                            selectboxDEPTCode +=`<option value="${departmentCodes[j]['branch_code']}" selected>${departmentCodes[j]['branch_code']}</option>`;
                        } else {
                            selectboxDEPTCode +=`<option value="${departmentCodes[j]['branch_code']}">${departmentCodes[j]['branch_code']}</option>`;
                        }
                    }
                    selectboxDEPTCode += '</select>'

                    /* =========== BIND PRODUCT CODE TO DROP DOWN ==============*/
                    var productCodes = @json($productCodes);
                    var selectboxPROCode = '<select class="tabledit-input form-control input-sm" id="pro_codes" name="pro_codes[]">';
                        selectboxPROCode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < productCodes.length; j++) {
                        if (productCodes[j]['code'] == jsondata[i]['PRO_CODE']) {
                            selectboxPROCode +=`<option value="${productCodes[j]['code']}" selected>${productCodes[j]['code']}</option>`;
                        } else {
                            selectboxPROCode +=`<option value="${productCodes[j]['code']}">${productCodes[j]['code']}</option>`;
                        }
                    }
                    selectboxPROCode += '</select>'

                    /* =========== BIND SEGMENT CODE TO DROP DOWN ==============*/
                    var segmentCode = @json($segmentCodes);
                    var selectboxSEGode = '<select class="tabledit-input form-control input-sm" id="seg_codes" name="seg_codes[]">';
                        selectboxSEGode += '<option value="" selected="">Select</option>';
                    for (var j = 0; j < segmentCode.length; j++) {
                        if (segmentCode[j]['code'] == jsondata[i]['SEG_CODE']) {
                            selectboxSEGode +=`<option value="${segmentCode[j]['code']}" selected>${segmentCode[j]['code']}</option>`;
                        } else {
                            selectboxSEGode +=`<option value="${segmentCode[j]['code']}">${segmentCode[j]['code']}</option>`;
                        }
                    }
                    selectboxSEGode += '</select>'

                    //=========== NARATIVE ========
                    var narative = `<textarea cols="30" rows="1" style="margin-bottom: 0px;" class="tabledit-input form-control input-sm resizetext" type="text" id="naratives" name="naratives[]">${jsondata[i]['NARATIVE']}</textarea>`;

                    //============ Amount =============
                    var amount = ` <input type="numbers" style="min-width: 80px; margin-bottom: 0px;" class="abledit-input form-control input-sm  numbers" id="amounts" name="amounts[]" onpaste="return false;" value="${jsondata[i]['AMOUNT']}" >`;



                    //============= BIND TO TABLE ====================
                    row$ += `<td style="padding: 5px;">${selectBoxGLCode}</td> `;
                    row$ += `<td style="padding: 5px;">${acountName}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxBranchCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectBoxDRCR}</td> `;
                    row$ += `<td style="padding: 5px;">${selectBoxCurrency}</td> `;
                    row$ += `<td style="padding: 5px;">${amount.replace(/\,/g,'')}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxBudgetCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxAlBudgetCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxTAXCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxSUPPCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxDEPTCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxPROCode}</td> `;
                    row$ += `<td style="padding: 5px;">${selectboxSEGode}</td> `;
                    row$ += `<td style="padding: 5px;">${narative}</td> `;
                    row$ += `<td style="padding: 5px;"><i class="fa fa-trash removeRowBtn" style="font-size: 20px;color: red"></i></td> `;
                    row$ += "</tr>"

                } else {
                    alert('Please contact admin!');
                    location.reload();
                    return false;
                }
            }
            $(`${tableid} tbody`).html(row$);
        }

        $('.addRowBtn').click(function() {
            $("#upload-excel").attr('disabled', 'disabled');
            $(".box-excel").css('color', 'gray');
        });

        //== trigger account name
        $('#tableBankPaymentVoucherForm').on('change', 'td:nth-child(1)', function() {
            var glCode = $(this).closest("tr").find("select").val();
            var row_index = $(this).parent().index('tr');

            var generalLedgerCodes = @json($generalLedgerCodes);
            var accountName = '';
            for (var j = 0; j < generalLedgerCodes.length; j++){
                if(generalLedgerCodes[j]['account_number'] == glCode){
                    accountName = generalLedgerCodes[j]['account_name'];
                    break;
                }
            }

            $("table tr:nth-child("+row_index+") td:nth-child(2)").html(`<textarea name="item_narative" id="item_narative" class="form-control " readonly>${accountName}</textarea>`);
        });

        // //==== trigger currency  ====
        // $('#tableBankPaymentVoucherForm').on('change', 'td:nth-child(5)', function() {
        //     var currency = $(this).closest("tr").find('td:eq(4) select').val();
        //     var amount   = $(this).closest("tr").find("td:eq(5) input[type='number']").val();
        //     var row_index = $(this).parent().index('tr');

        //     if(currency == 'USD'){
        //         var totalAmount = parseFloat(amount).toFixed(2);
        //         $("table tr:nth-child("+row_index+") td:nth-child(7)").html(`<input type="text" name="lcy_amounts[]" id="lcy_amounts" class="form-control" readonly="" value="$ ${totalAmount}">`);
        //     }else{
        //         var exchangeRate = $("#exchange_rate").val();
        //         var rate = parseFloat(exchangeRate).toFixed(2);
        //         var amount = parseFloat(amount).toFixed(2);
        //         var totalAmount = amount / rate;
        //         var grandTotal = parseFloat(totalAmount).toFixed(2);
        //         $("table tr:nth-child("+row_index+") td:nth-child(7)").html(`<input type="text" name="lcy_amounts[]" id="lcy_amounts" class="form-control" readonly="" value="$ ${grandTotal}">`);
        //     }
        // });


    //     //==== trigger amount    ====
    //     $('#tableBankPaymentVoucherForm').on('change', 'td:nth-child(6)', function() {
    //         var currency = $(this).closest("tr").find('td:eq(4) select').val();
    //         var amount   = $(this).closest("tr").find("td:eq(5) input[type='number']").val();
    //         var row_index = $(this).parent().index('tr');

    //         if(currency == 'USD'){
    //             var totalAmount = parseFloat(amount).toFixed(2);
    //             $("table tr:nth-child("+row_index+") td:nth-child(7)").html(`<input type="text" name="lcy_amounts[]" id="lcy_amounts" class="form-control" readonly="" value="$ ${totalAmount}">`);
    //         }else{
    //             var exchangeRate = $("#exchange_rate").val();
    //             var rate = parseFloat(exchangeRate).toFixed(2);
    //             var amount = parseFloat(amount).toFixed(2);
    //             var totalAmount = amount / rate;
    //             var grandTotal = parseFloat(totalAmount).toFixed(2);
    //             $("table tr:nth-child("+row_index+") td:nth-child(7)").html(`<input type="text" name="lcy_amounts[]" id="lcy_amounts" class="form-control" readonly="" value="$ ${grandTotal}">`);
    //         }
    //     });

    //    //====== Trigger when exchange rate changed ===

    //    $('#exchange_rate').bind('keyup change click', function() {
    //     var exchangeRate = $(this).val();
    //       alert(exchangeRate);
    //     });
    </script>
@endsection
