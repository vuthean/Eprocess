@extends('layouts.master')
@section('style')
    <style>
        .subject {
            display: inline-block;
            width: 300px;
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
        }

        .tooltip_body {
            word-wrap: break-word;
            white-space: normal;
            font-size: 12px;
        }

     

    </style>
@endsection
@section('menu')
	@include('siderbar.tasklist')
@endsection
@section('breadcrumb')
	@include('breadcrumb.procurement_record')
@endsection
@section('content')
	<div class="col-sm-12">
   <!-- Zero config.table start -->
   <div class="card">
      <div class="card-header">
         <h5>Procurement Approve Request</h5>
      </div>
      <div class="card-block">
         <div class="dt-responsive table-responsive">
            <table id="procurementRequestTable"
               class="table table-striped table-bordered nowrap">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>REQ.NO</th>
                     <th>SUBJECT</th>
                     <th>REQ.NAME</th>                     
                     <th>REQ.DEPARTMENT</th>                    
                     <th>RECEIVE.DATE</th>
                     <th>PROCURED BY</th> 
                     <th>PAYMENT STATUS</th>
                  </tr>
               </thead>
               <tbody>
               		
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <!-- Zero config.table end -->
</div>
@endsection
@section('script')
	<script>
        $(document).on('click', '.group_id_click', function () {
         
          $('#groupidshow').text('');
          var branchcode=$(this).data('branchcode');
          var branchname=$(this).data('branchname');
          // var group_des=$(this).data('groupdesc');
          $('#branchcode_dis').val(branchcode);
          $('#branchname_dis').val(branchname);
          // $('#group_description_dis').val(group_des);
          $('#groupidshow').append(branchname);
                
       })
   </script>
    <script type="text/javascript">
		$(document).ready(function() {
	
			// DataTable
			$('#procurementRequestTable').DataTable({
				"pageLength": 10,
				order: [[ 1, 'desc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('get-procurement-request-listing-data') }}",
                columnDefs: [ {
                    targets: 0,
                    orderable: false
                } ],
				columns: [{
						data: 'no',
						name: 'no'
					},
					{
						data: 'req_recid',
						name: 'req_recid'
					},
					{
						data: 'subject',
						name: 'subject'
					},
					{
						data: 'req_name',
						name: 'req_name'
					},
					{
						data: 'req_branch',
						name: 'req_branch'
					},
                    {
						data: 'recieve_date',
						name: 'recieve_date'
					},
                    {
						data: 'procure_by',
						name: 'procure_by'
					},
                    {
						data: 'payment_status',
						name: 'payment_status'
					}
				]
			});
	
		});
    </script>
@endsection