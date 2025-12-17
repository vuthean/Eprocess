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
        #procurementTracking .sorting_asc:before,  #procurementTracking  .sorting_desc:after, #procurementTracking .sorting_asc:after,  #procurementTracking  .sorting_desc:before{
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
                        <h4>REPORTS PROCUREMENT TRACKING</h4>
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
            <input type="text" id="req_dep" name="req_dep" class="form-control mb-2 mr-sm-2" placeholder="REQ DEPARTMENT">
            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div class="card">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="procurementTracking" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>PR REF</th>
                                <th>SUBJECT</th>
                                <th>SOLE SOURCE</th>
                                <th>REQUESTED DATE</th>
                                <th>APPROVED DATE</th>
                                <th>REQUESTER</th>
                                <th>FIRST REVIEWER</th>
                                <th>SECORD REVIEWER</th>
                                <th>THIRD REVIEWER</th>
                                <th>FORTH REVIEWER</th>
                                <th>APPROVER</th>
                                <th>CO APPROVER</th>
                                <th>DESCRIPTION</th>
                                <th>BR/DEPT</th>
                                <th>BUDGET CODE</th>
                                <th>ALTERNATIVE BUDGET CODE</th>
                                <th>QUANTITY</th>
                                <th>UNIT</th>
                                <th>UNIT PRICE</th>
                                <th>VAT 10%</th>
                                <th>TOTAL COST</th>
                                <th>USD</th>
                                <th>PAYMENT</th>
                                <th>PROCURE BY</th>
                                <th>DATE OF RP</th>
                                <th>RP REF NO</th>
                                <th>DATE OF ADV</th>
                                <th>ADV REF NO</th>
                                <th>DATE OF ADC</th>
                                <th>ADC REF NO</th>
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
			var table = $('#procurementTracking').DataTable({
                
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
                        title:'Procurement Report Tracking',
                    },
                    {
                        extend: 'csvHtml5',
                        title:'Procurement Report Tracking',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title:'Procurement Report Tracking',
                        customize: function (doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc.styles.tableHeader.fontSize = 5;
                            doc.defaultStyle.fontSize = 5;
                            doc.styles['td:nth-child(0)'] = { 
                                width: '1px',
                                'max-width': '1px'
                            }
                        }
                    },
                ],
				"pageLength": 10,
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
                ajax: {
                url:"{{ route('get-procurement-tracking-data') }}",
                data: function(data){
                    // Read values
                    var dStart = $('#start_date').val();
                    var dEnd   = $('#end_date').val();
                    var req_dep = $('#req_dep').val();
                    // Append to data
                    data.dStart = dStart;
                    data.dEnd = dEnd;
                    data.req_dep = req_dep;
                }
                },
                columnDefs: [
                    { 
                        orderable: false, 
                        targets: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30] 
                    },
                ],
				columns: [
                    {data: 'no',name: 'no'},
                    {data: 'req_recid',name:'req_recid'},
                    {data: 'subject',name:'subject'},
                    {data: 'sole_source',name: 'sole_source'},
                    {data: 'req_date',name:'req_date'},
                    {data: 'approved_date',name:'approved_date'},
                    {data: 'requester',name:'requester'},
                    {data: 'reviewer',name:'reviewer'},
                    {data: 'second_review',name:'second_review'},
                    {data: 'third_review',name:'third_review'},
                    {data: 'fourth_review',name:'fourth_review'},
                    {data: 'approver',name:'approver'},
                    {data: 'co_approver',name:'co_approver'},
                    {data: 'description',name:'description'},
                    {data: 'br_dep_code',name:'br_dep_code'},
                    {data: 'budget_code',name:'budget_code'},
                    {data: 'alternativebudget_code',name:'alternativebudget_code'},
                    {data: 'quantity',name:'quantity'},
                    {data: 'unit',name:'unit'},
                    {data: 'unit_price',name:'unit_price'},
                    {data: 'vat',name:'vat'},
                    {data: 'total_usd',name:'total_usd'},
                    {data: 'currency',name:'currency'},
                    {data: 'paid',name:'paid'},
                    {data: 'procured_by',name:'procured_by'},
                    {data: 'payment_date',name:'payment_date'},
                    {data: 'payment_ref_no',name:'payment_ref_no'},
                    {data: 'advance_request',name:'advance_request'},
                    {data: 'date_of_adv',name:'date_of_adv'},
                    {data: 'clear_request',name:'clear_request'},
                    {data: 'date_of_adc',name:'date_of_adc'},

				]
			});
            $('#btn_search').on('click', function() {
                table.draw();
            });

		});
    </script>

@endsection
