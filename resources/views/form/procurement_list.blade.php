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
    @include('siderbar.dashboard')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>Procurement Requests</h4>
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
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('payment_request.list_auth') }}">Payment Request</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <!-- Zero config.table start -->
        <div class="card">
            <div class="card-header">
                <a type="button" style="float: right;" href="{{ route('form/procurement/new') }}"
                    class="dt-button  buttons-html5">CREATE NEW</a>
            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="procurementTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Branch</th>
                                <th>Position</th>
                                <th>Date</th>

                            </tr>
                        </thead>
                        {{-- <tbody>
                            @foreach ($result as $key => $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        <a href="{{ url($value->description . '/' . Crypt::encrypt($value->req_recid . '___' . 'no')) }}"
                                            ​>
                                            {{ $value->req_recid }}
                                        </a>
                                    </td>
                                    <td>
                                        <p style="height: 5px;"><a class="mytooltip tooltip-effect-9" href="javascript:void(0)" style="font-weight: 400;">
                                                <span class="subject"> {{ $value->subject }}</span>
                                                <span class="tooltip-content5" style="width: 500px;">
                                                    <span class="tooltip-text3">
                                                        <span class="tooltip-inner2" style="padding: 2px;">
                                                            <span class="tooltip_body">
                                                                {{ $value->subject }}</span>

                                                        </span>
                                                    </span>
                                                </span>
                                            </a></p>

                                    </td>
                                    <td>{{ $value->record_status_description }}</td>
                                    <td>{{ $value->req_branch }}</td>
                                    <td>{{ $value->req_position }}</td>
                                    <td>{{ \Carbon\Carbon::parse($value->req_date)->format('d-m-Y  g:i A') }}</td>

                                </tr>
                            @endforeach
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>

@endsection
@section('script')
    <script>
        $(document).on('click', '.group_id_click', function() {

            $('#groupidshow').text('');
            var branchcode = $(this).data('branchcode');
            var branchname = $(this).data('branchname');
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
			$('#procurementTable').DataTable({
				"pageLength": 10,
				order: [[ 6, 'asc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('get-procurement-listing-data') }}",
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
						data: 'record_status_description',
						name: 'record_status_description'
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
						data: 'req_date',
						name: 'req_date'
					}
				]
			});
	
		});
    </script>
@endsection
