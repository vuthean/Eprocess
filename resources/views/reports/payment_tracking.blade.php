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
        #paymentTrackingTable .sorting_asc:before,  #paymentTrackingTable  .sorting_desc:after, #paymentTrackingTable .sorting_asc:after,  #paymentTrackingTable  .sorting_desc:before{
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
                        <h4>REPORTS PAYMENT TRACKING</h4>
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
        {{-- <div class="col-sm-12" style="padding-left: 0px;">
            <form method="POST" action="{{ route('reports/filter-payment-tracking-request') }}">
                @csrf
                <div class="form-inline">
                    <input type="date" id="start_date" name="start_date" class="form-control mb-2 mr-sm-2">
                    <span style="padding-right: 8px;">TO</span>
                    <input type="date" id="end_date" name="end_date" class="form-control mb-2 mr-sm-2">
                    <button type="submit" id="btn_searh" class="btn btn-sm btn-primary mb-2"
                        style="height: 36px; font-size: 14px;">Search</button>
                </div>
            </form>
        </div> --}}
        <div class="form-inline">
            <input type="date" id="start_date" name="start_date" class="form-control mb-2 mr-sm-2">
            <span style="padding-right: 8px;">TO</span>
            <input type="date" id="end_date" name="end_date" class="form-control mb-2 mr-sm-2">
            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div class="card">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="paymentTrackingTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>PR_REF_NO</th>
                                <th>SUBJECT</th>
                                <th>REQ_DATE</th>
                                <th>DUE DATE</th>
                                <th>APPROVED_DATE</th>
                                <th>LINE_REVIEW_DATE</th>
                                <th>ACCOUNTING_REVIEW_DATE</th>
                                <th>ACCOUNTING_REVIEW_NAME</th>
                                <th>REQUESTER</th>
                                <th>REVIEWER(S)</th>
                                <th>APPROVER(S)</th>
                                <th>REQ_DEPARTMENT</th>
                                <th>CCY</th>
                                <th>AMOUNT</th>
                                <th>SUPPLIER_NAME</th>
                                <th>PAYMENT_METHOD</th>
                                <th>BUDGET_CODE</th>
                                <th>ALT_CODE</th>
                                <th>BUDGET_ITEMS</th>
                                <th>TOTAL_BUDGET</th>
                                <th>YTD_EXPENSE</th>
                                <th>TOTAL_BUDGET_REMAINING</th>
                                <th>PAID_DATE</th>
                                <th>PAID_BY</th>
                                <th>STATUS</th>
                             
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>
@endsection
@section('script')
       <script type="text/javascript">
        function hoverTooltip(value) {
            $(".mytooltip").find(".tooltip_body").html(value);
        }
		$(document).ready(function() {    
			// DataTable
			var table = $('#paymentTrackingTable').DataTable({
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
				searching: true,
                ajax: {
                url:"{{ route('get-payment-tracking-data') }}",
                data: function(data){
                    // Read values
                    var dStart = $('#start_date').val();
                    var dEnd   = $('#end_date').val();
                    // Append to data
                    data.dStart = dStart;
                    data.dEnd = dEnd;
                }
                },
                columnDefs: [
                    { orderable: false, targets: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25] },
                ],
				columns: [
                    {
                        data: 'number',
						name: 'number'
                    },
					{
						data: 'rp_ref_no',
						name: 'rp_ref_no'
					},
					{
						data: 'subject',
						name: 'subject'
					},
					{
						data: 'req_date',
						name: 'req_date'
					},
                    {
						data: 'due_date',
						name: 'due_date'
					},
					{
						data: 'approve_date',
						name: 'approve_date'
					},
                    {
						data: 'line_review_date',
						name: 'line_review_date'
					},
                    {
						data: 'accounting_review_date',
						name: 'accounting_review_date'
					},
                    {
						data: 'accounting_review_name',
						name: 'accounting_review_name'
					},
                    {
						data: 'requester',
						name: 'requester'
					},
					{
						data: 'reviewers',
						name: 'reviewers'
					},
					{
						data: 'approvers',
						name: 'approvers'
					},
                    {
						data: 'req_department',
						name: 'req_department'
					},
                    {
						data: 'ccy',
						name: 'ccy'
					},
					{
						data: 'amount',
						name: 'amount'
					},
					{
						data: 'supplier_name',
						name: 'supplier_name'
					},
                    {
						data: 'payment_method',
						name: 'payment_method'
					},
                    {
						data: 'budget_code',
						name: 'budget_code'
					},
                    {
						data: 'alt_code',
						name: 'alt_code'
					},
					{
						data: 'budget_items',
						name: 'budget_items'
					},
					{
						data: 'total_budget',
						name: 'total_budget'
					},
                    {
						data: 'ytd_expense',
						name: 'ytd_expense'
					},
                    {
						data: 'total_budget_remaining',
						name: 'total_budget_remaining'
					},
					{
						data: 'paid_date',
						name: 'paid_date'
					},
                    {
						data: 'paid_by',
						name: 'paid_by'
					},
                    {
						data: 'status',
						name: 'status'
					}
				]
			});
            $('#btn_search').on('click', function() {
                table.draw();
            });

		});
    </script>

@endsection
