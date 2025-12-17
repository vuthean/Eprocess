@extends('layouts.master')
@section('menu')
    @include('siderbar.dashboard')
@endsection
@section('breadcrumb')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h4>User Tracking</h4>
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
                      
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="userTrackTable" class="table table-striped table-hover table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Method</th>
                                <th>Module</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- <tbody>
                            @foreach ($logs as $key => $log)
                                @if ($log->activity_form == 'upload_budget_code')
                                    <tr onclick="linkBudgetCodeTracking('{{ route('budget-code-tracking', ['track_id' =>  Crypt::encrypt($log->id)]) }}')" style="cursor: pointer;">
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $log->doer_name }}</td>
                                        <td>{{ $log->doer_email }}</td>
                                        <td>{{ $log->activity_code }}</td>
                                        <td>{{ $log->activity_form }}</td>
                                        <td>{{ $log->activity_datetime }}</td>
                                    </tr>
                                @else
                                    <tr data-toggle="modal" data-target="#large-Modal"
                                        data-old-value="{{ json_encode($log->old_value, true) }}"
                                        data-new-value="{{ json_encode($log->new_value, true) }}"
                                        data-module-name="{{ $log->activity_form }}" style="cursor: pointer;">
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $log->doer_name }}</td>
                                        <td>{{ $log->doer_email }}</td>
                                        <td>{{ $log->activity_code }}</td>
                                        <td>{{ $log->activity_form }}</td>
                                        <td>{{ $log->activity_datetime }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="large-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Data Comparison</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12" id="data-compare"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $("#large-Modal").on("show.bs.modal", function(e) {
                /** make sure the current state was clear */
                $("#data-compare").empty()
                // let oldValue = JSON.stringify($(e.relatedTarget).data('old-value'));
                // let newValue = JSON.stringify($(e.relatedTarget).data('new-value'));

                let oldValue = $(e.relatedTarget).data('old-value');
                let newValue = $(e.relatedTarget).data('new-value');
                let oldValues = oldValue.replace("{", "").replace("}", "").split(",");
                let newValues = newValue.replace("{", "").replace("}", "").split(",");
                let countData = oldValues.length > 0 ? oldValues.length : newValues.length;
                let html = '<table class="table table-hover table-bordered">';
                html += '<thead><tr><td>Old Data</td><td>New Data</td></tr><thead/><tbody>'
                for (var i = 0; i < countData; i++) {
                    if (oldValues[i] !== newValues[i]) {
                        html += '<tr style="background-color: lightgoldenrodyellow;"><td>' + oldValues[i] +
                            '</td><td>' + newValues[i] + '</td></tr>';
                    } else {
                        html += '<tr><td>' + oldValues[i] + '</td><td>' + newValues[i] + '</td></tr>';
                    }
                }
                html += '<tbody/></table>';
                $("#data-compare").append(html);
            });
        });
        function linkBudgetCodeTracking(link) {
            window.location.href = link;
        }
    </script>
     <script type="text/javascript">
		$(document).ready(function() {
	
			// DataTable
			$('#userTrackTable').DataTable({
				"pageLength": 10,
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('get-user-tracking-data') }}",
                columnDefs: [ {
                    targets: 6,
                    orderable: false
                } ],
				columns: [{
						data: 'no',
						name: 'no'
					},
					{
						data: 'doer_name',
						name: 'doer_name'
					},
					{
						data: 'doer_email',
						name: 'doer_email'
					},
					{
						data: 'activity_code',
						name: 'activity_code'
					},
					{
						data: 'activity_form',
						name: 'activity_form'
					},
                    {
						data: 'activity_datetime',
						name: 'activity_datetime'
					},
                    {
						data: 'action',
						name: 'action'
					}
				]
			});
	
		});
    </script>

@endsection
