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
.select2-close-mask{
    z-index: 2099;
}
.select2-dropdown{
    z-index: 3051;
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
                        <h4>Payment Method</h4>
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
                            Payment Method
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('payment-methods') }}">New</a>
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
            <button type="button" class="download-template btn btn-success" style="float: right;" data-toggle="modal" data-target="#new-tax-code-modal">
                <i class="fa fa-plus"></i> New
            </button>
        </div>
         <div class="dt-responsive table-responsive">
            <table id="tblPaymentMethod" class="table table-hover">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Name</th>
                     <th>Description</th>
                     <th>Created at</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <!-- Zero config.table end -->
   <div class="modal fade" id="new-tax-code-modal" style="overflow:hidden;" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h4 class="modal-title">CREATE NEW</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('payment-methods/create')}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="groupIds" id="groupIds" value="{{ $groupIds }}">
                    <div class="modal-body">
                        <div class="row">
                           
                            
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control " name="name" id="name" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Description</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control numbers" name="description" id="description" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Group Id </span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="select2-container form-control" multiple="multiple" id="group_ids" name="group_ids[]">
                                            @foreach ($groupIds as $value)
                                                <option value="{{ $value->group_id }}">{{ $value->group_id }}</option>
                                            @endforeach
                                        </select>
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
                <form method="post" action="{{route('payment-methods/update')}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="updated_id" id="updated_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span style="color: red;">*</span>
                                            <span>Name</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control " name="updated_name" id="updated_name" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Description</span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="updated_description" id="updated_description" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-inputs">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <div>
                                            <span>Group Id </span>
                                            <span style="float: right;">:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-7" id="select2GroupIds">
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

@endsection
@section('script')
    <script>
        document.getElementById("fileUpload").onchange = function() {
            document.getElementById("formUploadTemplate").submit();
        };
    </script>
	<script>
        $(document).on('click', '.edit_item', function () {

            var id       = $(this).data('id');
            var name = $(this).data('name');
            var description = $(this).data('description');
            var groupIds = $(this).data('group_ids');
        
            $('#updated_id').val(id);
            $('#updated_name').val(name);
            $('#updated_description').val(description);

            /** select2 when update */
            var groupStrs = $(this).data('group_id_strs');
            var choosedGroupIds = groupStrs.split(",");

            var groupIds = @json($groupIds);
            var select2GroupIdsHtml = '<select class="update-select2-container form-control" multiple="multiple" id="updated_group_ids" name="updated_group_ids[]">';
            for (var i = 0; i < groupIds.length; i++) {
                for(var j=0; j <choosedGroupIds.length; j++){
                    if(groupIds[i]['group_id'] == choosedGroupIds[j]){
                        select2GroupIdsHtml += `<option value="${groupIds[i]['group_id']}" selected >${groupIds[i]['group_id']}</option>`;
                    }else{
                        select2GroupIdsHtml += `<option value="${groupIds[i]['group_id']}">${groupIds[i]['group_id']}</option>`;
                    }
                }
                
            }
            select2GroupIdsHtml += '</select>'
            $('#select2GroupIds').html(select2GroupIdsHtml);

            $('.update-select2-container').select2({
                dropdownParent: $('#update_group_Modal')
            });

       })
    </script>
    <script type="text/javascript">
		$(document).ready(function() {
            $('.select2-container').select2({
                dropdownParent: $('#new-tax-code-modal')
            });
            $('.update-select2-container').select2({
                dropdownParent: $('#update_group_Modal')
            });
			// DataTable
			$('#tblPaymentMethod').DataTable({
				"pageLength": 10,
				order: [[ 3, 'desc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('payment-methods/listing') }}",
				columns: [{
						data: 'no',
						name: 'no'
					},{
						data: 'name',
						name: 'name'
					},{
						data: 'description',
						name: 'description'
					},{
						data: 'created_at',
						name: 'created_at'
					}
				]
			});
	
		});
	</script>

@endsection
