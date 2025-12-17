@extends('layouts.master')
@section('menu')
	@include('siderbar.groupmgt')
@endsection
@section('breadcrumb')
	@include('breadcrumb.group')
@endsection
@section('content')
	<div class="col-sm-12">
   <!-- Zero config.table start -->
   <div class="card">
      <div class="card-header">
         <h5>Group Listing</h5>
         
         	<button type="button" class="btn btn-success" style="float: right;" data-toggle="modal" data-target="#newgroup-Modal"><i class="fa fa-plus"></i> New</button>
            <button type="button" class="btn btn-success exportGroup" style="float: right; margin-right: 1%;" id="exportGroup"><i class="fa fa-download"></i> Export</button>
      </div>
      <div class="card-block">
         <div class="dt-responsive table-responsive">
            <table id="groupTable"
               class="table table-striped table-bordered nowrap">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Group ID</th>
                     <th>Group Name</th>
                     <th>Description</th>
                     <th>Created at</th>                     
                  </tr>
               </thead>
               {{-- <tbody>
               		@foreach($result as $key =>$value)
	                  <tr>
	                     <td>{{++$key}}</td>
	                     <td>
	                     	<a href="#"​ class="group_id_click" data-toggle="modal" data-target="#editgroup-Modal"
	                     	data-groupid="{{$value->group_id}}"
	                     	data-groupname="{{$value->group_name}}"
	                     	data-groupdesc="{{$value->group_description}}">
	                     		{{$value->group_id}}
	                     	</a>
	                     </td>
	                     <td>
	                     	<a href="{{url('group/member/listing/'.Crypt::encrypt($value->group_id))}}">{{$value->group_name}}</a>
	                     </td>
	                     <td>{{$value->group_description}}</td>
	                     <td>{{$value->created_at}}</td>
	                  </tr>
	                @endforeach
               </tbody> --}}
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
            <h4 class="modal-title"><i class="fa fa-group"></i> Add new group</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form method="post" action="{{route('group/save')}}">
         @csrf         
         <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td style="text-align: right;">Group Name</td>
                        <td>
                        	<input type="text" class="form-control" name="group_name">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Group Description</td>
                        <td>
                        	<input type="text" class="form-control" name="group_description">
                        </td>
                    </tr>                    
                </table>
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
         <form method="post" action="{{route('group/save')}}">
         @csrf         
         <input type="hidden" name="group_id" id="group_id_dis">
         <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td style="text-align: right;">Group Name</td>
                        <td>
                        	<input type="text" class="form-control" name="group_name" id="group_name_dis">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Group Description</td>
                        <td>
                        	<input type="text" class="form-control" name="group_description" id="group_description_dis">
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
<form id="download_group" method="post" action="{{ url('export/group') }}">
    {{ csrf_field() }}
</form>
@endsection
@section('script')
	<script>
        $(document).on('click', '.group_id_click', function () {
         
          $('#groupidshow').text('');
          var group_id=$(this).data('groupid');
          var group_name=$(this).data('groupname');
          var group_des=$(this).data('groupdesc');
          $('#group_id_dis').val(group_id);
          $('#group_name_dis').val(group_name);
          $('#group_description_dis').val(group_des);
          $('#groupidshow').append(group_id);
                
       })
       $(document).on('click','.exportGroup',function(){
         $("#download_group").submit();
       })
   </script>
    <script type="text/javascript">
		$(document).ready(function() {
	
			// DataTable
			$('#groupTable').DataTable({
				"pageLength": 10,
				order: [[ 4, 'desc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('get-group-listing-data') }}",
				columns: [{
						data: 'no',
						name: 'no'
					},
					{
						data: 'group_id',
						name: 'group_id'
					},
					{
						data: 'group_name',
						name: 'group_name'
					},
					{
						data: 'group_description',
						name: 'group_description'
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