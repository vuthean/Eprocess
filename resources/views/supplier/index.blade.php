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
	@include('siderbar.branch')
@endsection
@section('breadcrumb')
<div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>Supplier Codes</h4>
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
                        Supplier Code
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('tax-codes') }}">New</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
	<div class="col-sm-12">
   <!-- Zero config.table start -->
   <div class="card">
      <div class="card-block">
        <div class="col-sm-12" style="text-align: right;display:flex;align-items: center;justify-content: end;">
            <a class="download-template" href="{{ route('suppliers/download-template') }}">
                <i class="fa fa-download"></i> Download Template
            </a>
            <div class="box-excel " style="margin-right:10px;">
                <form method="post" action="{{route('suppliers/import-excel-data')}}" enctype="multipart/form-data" id="formUploadTemplate" style="padding-top: 14px;">
                    @csrf
                    <input type="file" id="fileUpload" name="fileUpload" style="display: none;"
                        accept=".xlsx, .xls, .csv">
                    <label for="fileUpload"><i class="fa fa-upload"></i> <span>Choose a file</span></label>
                </form>
            </div>
            <button type="button" class="download-template btn btn-success" style="float: right;" data-toggle="modal" data-target="#modal_new_supplier">
                <i class="fa fa-plus"></i> New
            </button>
            <button type="button" class="btn btn-success download-template exportSupplier" style="float: right; margin-right: 1%;" id="exportSupplier"><i class="fa fa-download"></i> Export</button>
        </div>
         <div class="dt-responsive table-responsive">
            <table id="tblSupplierCode" class="table table-hover">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Code</th>
                     <th>Name(Eng)</th>
                     <th>Name(Khmer)</th>
                     <th>Type</th>
                     <th>ACC Name</th>
                     <th>ACC Number</th>
                     <th>ACC Currency</th>
                     <th>Pay to bank</th>
                     <th>Created date</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>

   {{-- Create new supplier --}}
   <div class="modal fade" id="modal_new_supplier" tabindex="-1" role="dialog" >
        <div class="modal-dialog" role="document" style="margin-right: 38%;">
            <div class="modal-content" style="width: 800px;">
                <div class="modal-header">
                    <h4 class="modal-title">CREATE NEW</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('suppliers/create')}}" enctype="multipart/form-data" id="createNew">
                    @csrf
                    <div class="modal-body">
                        <span id="validate_pannel"></span>
                        <div class="row">
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control " name="code" id="code" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs"></div>

                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Personal Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>First Name(Eng)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="first_name_eng" id="first_name_eng" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Last Name(Eng)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="last_name_eng" id="last_name_eng" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>First name(KH)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="first_name_kh" id="first_name_kh" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Last name(KH)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="last_name_kh" id="last_name_kh" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Gender</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="form-control " id="gender" name="gender">
                                            <option value="" selected="" disabled="" >Select One</option>
                                            <option value="male" selected>Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Date of Birth</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Account Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Type</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="type" id="type" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>ACC Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="acct_name" id="acct_name" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>ACC Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="acct_number" id="acct_number" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>ACC Currency</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="acct_currency" id="acct_currency" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Pay to bank</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="pay_to_bank" id="pay_to_bank" value="N/A">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Identity Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Race</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="race" id="race" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Nationality</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="nationality" id="nationality" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>National ID card</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="id_card_number" id="id_card_number" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Passport Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="passport_number" id="passport_number" value="N/A">
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Contact Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div> 
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Phone Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="phone_number" id="phone_number" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Email</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="email" id="email" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div>
                                            <span>Address</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <textarea name="address" class="form-control ">N/A</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success waves-effect" name="submit" ><i
                                class="fa fa-save"></i> Create</button>
                        <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                class="fa fa-close"></i> Cancel</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- update supplier --}}
   <div class="modal fade" id="update_supplier_Modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog" role="document" style="margin-right: 38%;">
            <div class="modal-content" style="width: 800px;">
                <div class="modal-header">
                    <h4 class="modal-title">UPDATE</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('suppliers/update')}}" enctype="multipart/form-data" id="formUpdate">
                    @csrf
                    <input type="hidden" name="updated_id" id="updated_id">
                    <div class="modal-body">
                        <span id="validate_pannel_update"></span>
                        <div class="row">
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control " name="updated_code" id="updated_code" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs"></div>

                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Personal Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>First Name(Eng)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="updated_first_name_eng" id="updated_first_name_eng" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Last Name(Eng)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_last_name_eng" id="updated_last_name_eng" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>First name(KH)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_first_name_kh" id="updated_first_name_kh" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Last name(KH)</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_last_name_kh" id="updated_last_name_kh" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Gender</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="form-control " id="updated_gender" name="updated_gender">
                                            <option value="" selected="" disabled="" >Select One</option>
                                            <option value="male" selected>Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Date of Birth</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="date" class="form-control" id="updated_date_of_birth" name="updated_date_of_birth">
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Account Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Type</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_type" id="updated_type" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>ACC Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_acct_name" id="updated_acct_name" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>ACC Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_acct_number" id="updated_acct_number" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>ACC Currency</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_acct_currency" id="updated_acct_currency" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Pay to bank</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_pay_to_bank" id="updated_pay_to_bank" value="N/A">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Identity Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Race</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_race" id="updated_race" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Nationality</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_nationality" id="updated_nationality" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>National ID card</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_id_card_number" id="updated_id_card_number" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Passport Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_passport_number" id="updated_passport_number" value="N/A">
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12 mobile-inputs" style="padding-bottom: 25px;padding-top: 20px;">
                                <h5 style="padding: 2px;">Contact Information</h5>
                                <div class="col-sm-12" style="border-bottom: 1px solid #0000002b;"></div>
                            </div> 
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Phone Number</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_phone_number" id="updated_phone_number" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Email</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_email" id="updated_email" value="N/A">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div>
                                            <span>Address</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <textarea name="updated_address" class="form-control ">N/A</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success waves-effect" name="submit" value="update"><i
                            class="fa fa-save"></i> Update</button>
                        <button type="submit" class="btn btn-danger waves-effect" name="submit" value="delete"><i
                                class="fa fa-trash"></i> Delete</button>
                        <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                class="fa fa-close"></i> Cancel</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<form id="download_supplier" method="post" action="{{ url('export/supplier') }}">
    {{ csrf_field() }}
</form>
@endsection
@section('script')
    <script>
        document.getElementById("fileUpload").onchange = function() {
            document.getElementById("formUploadTemplate").submit();
        };
        $(document).on('click','.exportSupplier',function(){
         $("#download_supplier").submit();
       })
    </script>
    <script>
        $(document).on('click', '.edit_supplier', function () {
            var id             = $(this).data('id');
            var code           = $(this).data('code');
            var first_name_eng = $(this).data('first_name_eng');
            var last_name_eng  = $(this).data('last_name_eng');
            var first_name_kh  = $(this).data('first_name_kh');
            var last_name_kh   = $(this).data('last_name_kh');
            var full_name_eng  = $(this).data('full_name_eng');
            var full_name_kh   = $(this).data('full_name_kh');
            var gender         = $(this).data('gender');
            var date_of_birth  = $(this).data('date_of_birth');
            var race           = $(this).data('race');
            var nationality    = $(this).data('nationality');
            var id_card_number = $(this).data('id_card_number');
            var passport_number= $(this).data('passport_number');
            var phone_number   = $(this).data('phone_number');
            var email          = $(this).data('email');
            var address        = $(this).data('address');
            var type           = $(this).data('type');
            var acct_name      = $(this).data('acct_name');
            var acct_number    = $(this).data('acct_number');
            var acct_currency  = $(this).data('acct_currency');
            var pay_to_bank    = $(this).data('pay_to_bank');
            
            $('#updated_id').val(id);
            $('#updated_code').val(code);
            $('#updated_first_name_eng').val(first_name_eng);
            $('#updated_last_name_eng').val(last_name_eng);
            $('#updated_first_name_kh').val(first_name_kh);
            $('#updated_last_name_kh').val(last_name_kh);
            $('#updated_full_name_eng').val(full_name_eng);
            $('#updated_full_name_kh').val(full_name_kh);
            $('#updated_gender').val(gender);
            $('#updated_date_of_birth').val(date_of_birth);
            $('#updated_race').val(race);
            $('#updated_nationality').val(nationality);
            $('#updated_id_card_number').val(id_card_number);
            $('#updated_passport_number').val(passport_number);
            $('#updated_phone_number').val(phone_number);
            $('#updated_email').val(email);
            $('#updated_address').val(address);
            $('#updated_type').val(type);
            $('#updated_acct_name').val(acct_name);
            $('#updated_acct_number').val(acct_number);
            $('#updated_acct_currency').val(acct_currency);
            $('#updated_pay_to_bank').val(pay_to_bank);
    })
    </script>
    <script type="text/javascript">
		$(document).ready(function() {
			$('#tblSupplierCode').DataTable({
				"pageLength": 10,
				order: [[ 3, 'desc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('suppliers/listing') }}",
				columns: [{
						data: 'no',
						name: 'no'
					},{
						data: 'supplier_code',
						name: 'supplier_code'
					},{
						data: 'full_name_eng',
						name: 'full_name_eng'
					},{
						data: 'full_name_kh',
						name: 'full_name_kh'
					},{
						data: 'type',
						name: 'type'
					},{
						data: 'acct_name',
						name: 'acct_name'
					},{
						data: 'acct_number',
						name: 'acct_number'
					},{
						data: 'acct_currency',
						name: 'acct_currency'
					},{
						data: 'pay_to_bank',
						name: 'pay_to_bank'
					},{
						data: 'created_at',
						name: 'created_at'
					}
				]
			});
	
		});
	</script>
    <script>
        $("#createNew").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                code: {required: true,},
                first_name_eng: {required: true,},
                last_name_eng: {required: true,},
                first_name_kh: {required: true,},
                last_name_kh: {required: true,},
                date_of_birth: {required: true,},
            },
            messages: {
                code: "Please input mandatory field",
                first_name_eng:"Please input mandatory field",
                last_name_eng:"Please input mandatory field",
                first_name_kh:"Please input mandatory field",
                last_name_kh:"Please input mandatory field",
                date_of_birth:"Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                if(
                    element.attr("name") == "code" || 
                    element.attr("name") == "first_name_eng" ||
                    element.attr("name") == "last_name_eng" ||
                    element.attr("name") == "first_name_kh" ||
                    element.attr("name") == "date_of_birth" ||
                    element.attr("name") == "last_name_kh" 
                    ){
                    $('#validate_pannel').empty();
                    error.appendTo('#validate_pannel');
                }
            },
            submitHandler: function(form) {
                $(".overlay").show();
                form.submit();
            }

        });
    </script>
    <script>
        $("#formUpdate").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                code: {required: true,},
                first_name_eng: {required: true,},
                last_name_eng: {required: true,},
                first_name_kh: {required: true,},
                last_name_kh: {required: true,},
                date_of_birth: {required: true,},
            },
            messages: {
                code: "Please input mandatory field",
                first_name_eng:"Please input mandatory field",
                last_name_eng:"Please input mandatory field",
                first_name_kh:"Please input mandatory field",
                last_name_kh:"Please input mandatory field",
                date_of_birth:"Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                if(
                    element.attr("name") == "code" || 
                    element.attr("name") == "first_name_eng" ||
                    element.attr("name") == "last_name_eng" ||
                    element.attr("name") == "first_name_kh" ||
                    element.attr("name") == "date_of_birth" ||
                    element.attr("name") == "last_name_kh" 
                    ){
                    $('#validate_pannel_update').empty();
                    error.appendTo('#validate_pannel_update');
                }
            },
            submitHandler: function(form) {
                $(".overlay").show();
                form.submit();
            }

        });
    </script>
@endsection
