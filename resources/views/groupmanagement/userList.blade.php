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
    @include('siderbar.listuser')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>User Listing</h4>
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
                            USER
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="form-inline">
            <input type="text" id="req_num" name="req_num" class="form-control mb-2 mr-sm-2" placeholder="user name">
            <button type="button" id="btn_search" class="btn btn-sm btn-primary mb-2"
                style="height: 36px; font-size: 14px;">Search</button>
        </div>
        <div id="table-data" class="card" style="">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="accountingVoucherTable" class="table table-striped table-bordered nowrap" >
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>FULL NAME</th>
                                <th>EMAIL</th>
                                <th>GROUP ID</th>
                                <th>GROUP NAME</th>
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
            var table = $('#accountingVoucherTable').DataTable({
				"pageLength": 10,
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: false,
                ajax: {
                url:"{{ route('listing/users-data') }}",
                data: function(data){
                    // Read values
                    var fullName = $('#req_num').val();
                    // Append to data
                    data.fullName = fullName;
                }
                },
                columnDefs: [
                    { orderable: false, targets: [0,1,2,3,4,5] },
                ],
				columns: [
                    {data: 'number',name: 'number'},
                    {data: 'fullname',name: 'fullname'},
                    {data: 'email',name: 'email'},
                    {data: 'groupId',name: 'groupId'},
                    {data: 'groupName',name: 'groupName'},
                    {data: 'status',name: 'status'},
				]
			});
            $('#btn_search').on('click', function() {
                table.draw();
                $("#table-data").css({"display":"block"});
            });
    </script>

@endsection
