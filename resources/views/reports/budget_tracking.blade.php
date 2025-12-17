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
        #budgetCodeTracking .sorting_asc:before,  #budgetCodeTracking  .sorting_desc:after, #budgetCodeTracking .sorting_asc:after,  #budgetCodeTracking  .sorting_desc:before{
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
                        <h4>REPORTS BUDGET CODE TRACKING</h4>
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
            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div class="card">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="budgetCodeTracking" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th style="width: 5px">No</th>
                                <th>BUDGET CODE</th>
                                <th>BUDGET ITEM</th>
                                <th>BUDGET OWNER</th>
                                <th>TOTAL BUDGET</th>
                                <th>YTD PAYMENT</th>
                                <th>REMAINING PAYMENT</th>
                                <th>YTD PROCUREMENT</th>
                                <th>REMAINING PROCUREMENT</th>
                                <th>YEAR</th>
                                <th>CREATED DATE</th>
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
			var table = $('#budgetCodeTracking').DataTable({
                
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
                        title:'Budget Report Tracking',
                    },
                    {
                        extend: 'csvHtml5',
                        title:'Budget Report Tracking',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title:'Budget Report Tracking',
                        customize: function (doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc.styles.tableHeader.fontSize = 10;
                            doc.defaultStyle.fontSize = 10;
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
                url:"{{ route('get-budget-tracking-data') }}",
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
                    { 
                        orderable: false, 
                        targets: [0,1,2,3,4,5,6,7,8,9,10] 
                    },
                ],
				columns: [
                    {data: 'no',name: 'no'},
                    {data: 'budget_code',name: 'budget_code'},
                    {data: 'budget_item',name: 'budget_item'},
                    {data: 'budget_owner',name: 'budget_owner'},
                    {data: 'total_budget',name: 'total_budget'},
                    {data: 'ytd_payment',name: 'ytd_payment'},
                    {data: 'remaining_payment',name: 'remaining_payment'},
                    {data: 'ytd_procurement',name: 'ytd_procurement'},
                    {data: 'remaining_procurement',name: 'remaining_procurement'},
                    {data: 'year',name: 'year'},
                    {data: 'created_date',name: 'created_date'},
				]
			});
            $('#btn_search').on('click', function() {
                table.draw();
            });

		});
    </script>

@endsection
