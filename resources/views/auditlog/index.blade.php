@extends('layouts.master')
@section('style')
    <style>
        .subject {
            display: inline-block;
            width: 200px;
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
    @include('siderbar.auditlog')
@endsection
@section('breadcrumb')
    @include('breadcrumb.auditlog')
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="form-inline">
            <input type="date" id="start_date" name="start_date" class="form-control mb-2 mr-sm-2">
            <span style="padding-right: 8px;">TO</span>
            <input type="date" id="end_date" name="end_date" class="form-control mb-2 mr-sm-2">
            <input type="text" id="req_num" name="req_num" class="form-control mb-2 mr-sm-2" placeholder="">
            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div id="table-data" class="card" style="display:none;">
            <div class="card-header">
                <h5>View all your requests</h5>
            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="auditlogTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Req.ID</th>
                                <th>Subject</th>
                                <th>Req.By</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Req.Type</th>
                                <th>Req.Status</th>
                                <th>Req.Date</th>

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
    $(document).ready(function() {

        // DataTable
        var table = $('#auditlogTable').DataTable({
            "pageLength": 10,
            order: [[ 8, 'desc' ]],
            processing: true,
            serverSide: true,
            ordering:  true,
            searching: true,
            ajax: {
                url:"{{ route('get-auditlog-listing-data') }}",
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
                    data: 'req_position',
                    name: 'req_position'
                },
                {
                    data: 'formname',
                    name: 'formname'
                },
                {
                    data: 'record_status_description',
                    name: 'record_status_description'
                },
                {
                    data: 'req_date',
                    name: 'req_date'
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
