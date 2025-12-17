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
        #accountingVoucherTable .sorting_asc:before,  #accountingVoucherTable  .sorting_desc:after, #accountingVoucherTable .sorting_asc:after,  #accountingVoucherTable  .sorting_desc:before{
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
                        <h4>REPORTS CASH PAYMENT VOUCHER TRACKING</h4>
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
            <input type="text" id="req_num" name="req_num" class="form-control mb-2 mr-sm-2" placeholder="VOUCHER NO">
            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div id="table-data" class="card" style="display: none;">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="accountingVoucherTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>VOUCHER NO</th>
                                <th>REF. NO</th>
                                <th>REF. TYPE</th>
                                <th>REQ_DATE</th>
                                <th>REVIEW_DATE</th>
                                <th>APPROVED_DATE</th>
                                <th>REQUESTER</th>
                                <th>REVIEWER(S)</th>
                                <th>APPOVER(S)</th>
                                <th>CCY</th>
                                <th>AMOUNT</th>
                                <th>ACCOUNT NAME</th>
                                <th>PAYMENT METHOD</th>
                                <th>PAID DATE</th>
                                <th>PAID_BY</th>
                                <th>EXPORTED DATE</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">

        function hoverTooltip(value) {
            $(".mytooltip").find(".tooltip_body").html(value);
        }
		    $(document).ready(function() {
			// DataTable
            // $("#table-data").empty();

                var table = $('#accountingVoucherTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50,-1 ],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],

                buttons: [
                    'pageLength',
                    'copyHtml5',
                    {
                        extend: 'excelHtml5',
                        title:'Cash Payment Voucher Report Tracking',
                    },
                    {
                        extend: 'csvHtml5',
                        title:'Cash Payment Voucher Report Tracking',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title:'Cash Payment Voucher Report Tracking',
                        customize: function (doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc.styles.tableHeader.fontSize = 5;
                            doc.defaultStyle.fontSize = 5;
                            doc.styles['td:nth-child(1)'] = { width: '2px','max-width': '2px'}
                        }
                    },
                ],

				"pageLength": 10,
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
                ajax: {
                url:"{{ route('get-cash-payment-voucher-tracking-data') }}",
                data: function(data){
                    // Read values
                    var dStart = $('#start_date').val();
                    var dEnd   = $('#end_date').val();
                    var req_num = $('#req_num').val();
                    // Append to data
                    data.dStart = dStart;
                    data.dEnd = dEnd;
                    data.req_num = req_num;
                }
                },
                columnDefs: [
                    { orderable: false, targets: [0,1,2,3,4,5,6,7,8,9,10] },
                ],
				columns: [
                    {data: 'number',name: 'number'},
					{data: 'voucher_no',name: 'voucher_no'},
                    {data: 'ref_no',name: 'ref_no'},
                    {data: 'ref_type',name: 'ref_type'},
					{data: 'req_date',name: 'req_date'},
					{data: 'review_date',name: 'review_date'},
					{data: 'approve_date',name: 'approve_date'},
					{data: 'requester',name: 'requester'},
					{data: 'reviewer',name: 'reviewer'},
					{data: 'approver',name: 'approver'},
					{data: 'ccy',name: 'ccy'},
					{data: 'amount',name: 'amount'},
					{data: 'account_name',name: 'account_name'},
                    {data: 'payment_method',name: 'payment_method'},
                    {data: 'paid_date',name: 'paid_date'},
                    {data: 'paid_by',name: 'paid_by'},
                    {data: 'exported_date',name: 'exported_date'},
					{data: 'status',name: 'status'},
				]
			});
            $('#btn_search').on('click', function() {
                table.draw();
                $("#table-data").css({"display":"block"});
            });


		});
    </script>

@endsection
