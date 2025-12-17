@extends('layouts.master')
@section('menu')
	@include('siderbar.branch')
@endsection
@section('breadcrumb')
	@include('breadcrumb.branch')
@endsection
@section('content')
	<div class="col-sm-12">
   <!-- Zero config.table start -->
   <div class="card">
      <div class="card-header">
         <h5>Department Code Listing</h5>

         	<button type="button" class="btn btn-success" style="float: right;" data-toggle="modal" data-target="#newgroup-Modal"><i class="fa fa-plus"></i> New</button>
            <button type="button" class="btn btn-success exportdep" style="float: right; margin-right: 1%;" id="exportdep"><i class="fa fa-download"></i> Export</button>
      </div>
      <div class="card-block">
         <div class="dt-responsive table-responsive">
            <table id="branchCodeTable"
               class="table table-striped table-bordered nowrap">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Department Code</th>
                     <th>Department Name</th>
                     <th>Created at</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <!-- Zero config.table end -->
</div>

<div class="modal fade" id="newgroup-Modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-gear"></i> Add New Department</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form method="post" action="{{route('branchcode/save')}}" enctype="multipart/form-data">
         @csrf
         <div class="modal-body">

                         <div class="col-lg-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs  tabs" role="tablist">
                   <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#home1" role="tab" aria-expanded="true">ADD NEW</a>
                   </li>
                   <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#profile1" role="tab" aria-expanded="false">UPLOAD FILE</a>
                   </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs card-block">
                   <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                     <div class="table-responsive">
                      <table class="table table-hover">
                          <tr>
                              <td style="text-align: right;">Department Code</td>
                              <td>
                                <input type="text" class="form-control" name="branchcode">
                              </td>
                          </tr>
                          <tr>
                              <td style="text-align: right;">Department Name</td>
                              <td>
                                <input type="text" class="form-control" name="branchname">
                              </td>
                          </tr>
                      </table>
                    </div>
                   </div>
                   <div class="tab-pane" id="profile1" role="tabpanel" aria-expanded="false">
                      <input class="form-control" type="file" name="fileupload">
                   </div>
                </div>
             </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success waves-effect "><i class="fa fa-save"></i> Save</button>
         </div>
         </form>
      </div>
   </div>
</div>
<div class="modal fade" id="editgroup-Modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-group"></i> <span id="groupidshow"></span></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form method="post" action="{{route('branchcode/save')}}">
         @csrf
         <input type="hidden" name="group_id" id="group_id_dis">
         <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td style="text-align: right;">Group Name</td>
                        <td>
                            <input  type="hidden" class="form-control" name="branchId" id="branchId_dis">
                        	<input type="text" class="form-control" name="branchcode" id="branchcode_dis">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Group Description</td>
                        <td>

                        	<input type="text" class="form-control" name="branchname" id="branchname_dis">
                        </td>
                    </tr>
                </table>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success waves-effect " id="btn_update"><i class="fa fa-save"></i> Update</button>
            <button type="submit" name="submit" value="delete" class="btn btn-danger waves-effect " id="btn_remove"><i class="fa fa-save"></i> Remove</button>
         </div>
         </form>
      </div>
   </div>
</div>
<form id="download_department" method="post" action="{{ url('export/department') }}">
    {{ csrf_field() }}
</form>
@endsection
@section('script')
	<script>
        $(document).on('click', '.group_id_click', function () {

          $('#groupidshow').text('');
          var branchId=$(this).data('branchid');
          var branchcode=$(this).data('branchcode');
          var branchname=$(this).data('branchname');
          // var group_des=$(this).data('groupdesc');
            $('#branchId_dis').val(branchId);
          $('#branchcode_dis').val(branchcode);
          $('#branchname_dis').val(branchname);
          // $('#group_description_dis').val(group_des);
          $('#groupidshow').append(branchname);

       })
       $(document).on('click','.exportdep',function(){
         $("#download_department").submit();
       })
   </script>
    <script type="text/javascript">
		$(document).ready(function() {
	
			// DataTable
			$('#branchCodeTable').DataTable({
				"pageLength": 10,
				order: [[ 3, 'desc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('get-branch-code-listing-data') }}",
				columns: [{
						data: 'no',
						name: 'no'
					},
					{
						data: 'branch_code',
						name: 'branch_code'
					},
					{
						data: 'branch_name',
						name: 'branch_name'
					},
					{
						data: 'created_at',
						name: 'created_at'
					}
				]
			});
	
		});
	</script>

@endsection
