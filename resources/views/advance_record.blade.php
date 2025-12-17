@extends('layouts.master')
@section('style')
    <style>
        #advanceRecordTable .sorting_asc:before,  #advanceRecordTable  .sorting_desc:after, #advanceRecordTable .sorting_asc:after,  #advanceRecordTable  .sorting_desc:before{
            opacity: 0;
        }
    </style>
@endsection
@section('menu')
    @include('siderbar.dashboard')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>Advance Records</h4>
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
                            REPORTS
                        </li>
                        <li class="breadcrumb-item"><a href="#!">New</a> </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="form-inline">
            <input type="date" id="start_date" name="start_date" class="form-control mb-2 mr-sm-2">
            <span style="padding-right: 8px;">TO</span>
            <input type="date" id="end_date" name="end_date" class="form-control mb-2 mr-sm-2">
            <input type="text" id="req_num" name="req_num" class="form-control mb-2 mr-sm-2" placeholder="">

            <span style="padding-right: 8px;">Filter Paid</span>
            <select class="form-control mb-2 mr-sm-2" id="filter_paid" name="filter_paid">
                <option value="filter">--all--</option>
                <option value="YES">Yes</option>
                <option value="NO">No</option>
                <option value="CANCEL">Cancel</option>
            </select>

            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div id="table-data" class="card" style="display:none;" >
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="advanceRecordTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Request Date</th>
                                <th>Approval Date</th>
                                <th>ADV/ADC ref No</th>
                                <th>Requester</th>
                                <th>From Department</th>
                                <th>Request Amount</th>
                                <th>Currency</th>
                                <th>Cleared</th>
                                <th>Payment By</th>
                                <th>Paid</th>
                                <th>Paid Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>
    <div class="modal fade" id="updatePaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h4 class="modal-title">Upadate Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('reports/advance-records/update-payment') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="req_recid" id="req_recid">
                    <input type="hidden" name="req_from" id="req_from">
                    <div class="modal-body">
                        <div class="row">
                           <div class="col-sm-12 mobile-inputs">
                              <div class="form-group row">
                                 <div class="col-sm-5">
                                       <div>
                                          <span>Payment by</span>
                                          <span style="float: right;">:</span>
                                       </div>
                                 </div>
                                 <div class="col-sm-7">
                                       <select name="payment_by_id" id="payment_by_id" class="form-control">
                                       @foreach($paymentUsers as $user)
                                          @if($user->id == $currentUser->id)
                                             <option value="{{ $user->id }}" selected="">{{ $user->fullname }}</option>
                                          @else
                                             <option value="{{ $user->id }}">{{ $user->fullname }}</option>
                                          @endif
                                       @endforeach
                                       </select>
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-12 mobile-inputs">
                              <div class="form-group row">
                                 <div class="col-sm-5">
                                       <div>
                                          <span>Paid</span>
                                          <span style="float: right;">:</span>
                                       </div>
                                 </div>
                                 <div class="col-sm-7">
                                       <select class="tabledit-input form-control input-sm" id="paid" name="paid">
                                            <option value="Paid_yes">Yes</option>
                                            <option value="Paid_no">No</option>
                                            <option value="Paid_cancel">Cancel</option>
                                       </select>
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-12 mobile-inputs">
                              <div class="form-group row">
                                 <div class="col-sm-5">
                                       <div>
                                          <span>Paid Date</span>
                                          <span style="float: right;">:</span>
                                       </div>
                                 </div>
                                 <div class="col-sm-7">
                                    <input type="date" class="form-control" id="paid_date" name="paid_date" value="{{ date('d/m/y') }}">
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-12 mobile-inputs">
                              <div class="form-group row">
                                 <div class="col-sm-12">
                                       <span>Comment</span>
                                 </div>
                                 <div class="col-sm-12">
                                       <textarea class="form-control" name="comment" id="comment"></textarea>
                                 </div>
                              </div>
                           </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success waves-effect" name="activity" value="save_item" ><i
                                class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-default waves-effect" value="update" data-dismiss="modal"><i
                                class="fa fa-close"></i> Cancel</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@section('script')
   <script type="text/javascript">

   $(document).on('click', '.edit_item', function() {
        var req_recid = $(this).data('req_recid');
        var req_from = $(this).data('req_from');
        $('#req_recid').val(req_recid);
        $('#req_from').val(req_from);
   });


		$(document).ready(function() {    
			// DataTable
			var table = $('#advanceRecordTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50,-1 ],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],

                buttons: [
                    'pageLength',
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                
				"pageLength": 10,
				processing: true,
				serverSide: true,
				ordering:  true,
                ajax: {
                url:"{{ route('reports/advance-records/render-pagination') }}",
                data: function(data){
                    // Read values
                    var paid = $('#filter_paid').val();
                    var dStart = $('#start_date').val();
                    var dEnd   = $('#end_date').val();
                    var req_num = $('#req_num').val();
                    // Append to data
                    data.dPaid = paid;
                    data.dStart = dStart;
                    data.dEnd = dEnd;
                    data.req_num = req_num;
                }
                },
                columnDefs: [
                    { orderable: false, targets: [0,1,2,3,4,5,6,7,8,9,10,11] },
                ],
				columns: 
            [
               {
                  data: 'number',
                  name: 'number'
               }, {
                  data: 'request_date',
                  name: 'request_date'
               }, {
                  data: 'approval_date',
                  name: 'approval_date'
               }, {
                  data: 'req_recid',
                  name: 'req_recid'
               }, {
                  data: 'requester',
                  name: 'requester'
               }, {
                  data: 'from_department',
                  name: 'from_department'
               }, {
                  data: 'request_amount',
                  name: 'request_amount'
               }, {
                  data: 'currency',
                  name: 'currency'
               }, {
                  data: 'cleared',
                  name: 'cleared'
               }, {
                  data: 'payment_by',
                  name: 'payment_by'
               }, {
                  data: 'paid',
                  name: 'paid'
               }, {
                  data: 'paid_date',
                  name: 'paid_date'
               }, {
                  data: 'action_button',
                  name: 'action_button'
               }
            ]
			});
         $('#btn_search').on('click', function() {
               table.draw();
               $("#table-data").css({"display":"block"});
         });

		});
    </script>

@endsection
