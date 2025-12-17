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
                        <h4>Branch Codes</h4>
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
                            Branch Code
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('real-branches') }}">New</a>
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
            <a class="download-template" href="{{ route('real-branches/download-template') }}">
                <i class="fa fa-download"></i> Download Template
            </a>
            <div class="box-excel " style="margin-right:10px;">
                <form method="post" action="{{route('real-branches/import-excel-data')}}" enctype="multipart/form-data" id="formUploadTemplate" style="padding-top: 14px;">
                    @csrf
                    <input type="file" id="fileUpload" name="fileUpload" style="display: none;"
                        accept=".xlsx, .xls, .csv">
                    <label for="fileUpload"><i class="fa fa-upload"></i> <span>Choose a file</span></label>
                </form>
            </div>
            <button type="button" class="btn btn-success exportBranch download-template" style="float: right; margin-right: 1%;" id="exportBranch"><i class="fa fa-download"></i> Export</button>
            <button type="button" class="download-template btn btn-success" style="float: right;" data-toggle="modal" data-target="#new-product-code-modal">
                <i class="fa fa-plus"></i> New
            </button>
            
        </div>
         <div class="dt-responsive table-responsive">
            <table id="tblGernalTaxCode" class="table table-hover">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>CODE</th>
                     <th>NAME</th>
                     <th>Created at</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <!-- Zero config.table end -->
   <div class="modal fade" id="new-product-code-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h4 class="modal-title">CREATE NEW</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('real-branches/create')}}" enctype="multipart/form-data" id="formNewBranch">
                    @csrf
                    <div class="modal-body">
                        <span id="validate_pannel"></span>
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Branch Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control " name="code" id="code" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Branch Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="name" id="name" >
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

    <div class="modal fade" id="update_group_Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h4 class="modal-title">UDATE</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <span id="validate_create_new_item"></span>
                <form method="post" action="{{route('real-branches/update')}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="updated_id" id="updated_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Branch Code</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control " name="updated_code" id="updated_code" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Branch Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="updated_name" id="updated_name" >
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
<form id="download_branch" method="post" action="{{ url('export/branch') }}">
    {{ csrf_field() }}
</form>
@endsection
@section('script')
    <script>
        document.getElementById("fileUpload").onchange = function() {
            document.getElementById("formUploadTemplate").submit();
        };
        $(document).on('click','.exportBranch',function(){
         $("#download_branch").submit();
       })
    </script>
	<script>
        $(document).on('click', '.edit_item', function () {

        var id   = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');
        
        $('#updated_id').val(id);
        $('#updated_code').val(code);
        $('#updated_name').val(name);
       })
    </script>
    <script type="text/javascript">
		$(document).ready(function() {
			$('#tblGernalTaxCode').DataTable({
				"pageLength": 10,
				order: [[ 3, 'desc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('real-branches/listing') }}",
				columns: [{
						data: 'no',
						name: 'no'
					},{
						data: 'code',
						name: 'code'
					},{
						data: 'name',
						name: 'name'
					},{
						data: 'created_at',
						name: 'created_at'
					}
				]
			});
	
		});
	</script>
    <script>
        $("#formNewBranch").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: "[readonly]",

            rules: {
                code: {required: true,},
            },
            messages: {
                code: "Please input mandatory field",
            },

            errorPlacement: function(error, element) {
                if(element.attr("name") == "code" ){
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
@endsection
