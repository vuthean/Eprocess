@extends('layouts.master')
@section('style')
    <style>
        .overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 99999999;
            background: rgba(255, 255, 255, 0.8) url("/examples/images/loader.gif") center no-repeat;
        }

        /* Turn off scrollbar when body element has the loading class */
        body.loading {
            overflow: hidden;
        }

        /* Make spinner image visible when body element has the loading class */
        body.loading .overlay {
            display: block;
        }

        .resizetext {
            /*background-color: #000;*/
            color: black;
            /*border-radius: 10px;*/
            /*border-style: none;*/

            transition: width ease 0.2s;
        }

        .resizetext:focus {
            width: 200px;
        }


        .myheader {
            margin: 0;
            padding: 8px 20px;
            background: white;
            color: #f1f1f1;
            z-index: 9999;
        }

        .sticky {
            position: fixed;
            top: 0;
            width: 100%;
        }

        .sticky+.content {
            padding-top: 102px;
        }

        .error {
            color: red;
            border-color: red;
        }

        .removeRowBtn {
            cursor: pointer;
        }

        .addRowBtn {
            cursor: pointer;
        }

        .no-js #loader {
            display: none;
        }

        .js #loader {
            display: block;
            position: absolute;
            left: 100px;
            top: 0;
        }

        .se-pre-con {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url(images/loader-64x/Preloader_2.gif) center no-repeat #fff;
        }

    </style>

@endsection
@section('menu')
    @include('siderbar.budgetcode')
@endsection
@section('breadcrumb')
    @include('breadcrumb.budgetcode')
@endsection
@section('content')
    <div class="col-sm-12">
        <!-- Zero config.table start -->
        <div class="card">
            <div class="card-header">
                <h5>Budget Code Listing</h5>
                <button type="button" hidden class="btn btn-info" style="float: right;" data-toggle="modal"
                    data-target="#newgroup-Modal"><i class="fa fa-plus"></i> Upload</button>
                <button type="button" class="btn btn-success" style="float: right;" data-toggle="modal"
                    data-target="#newgroup-Modal"><i class="fa fa-plus"></i> New</button>
                <button type="button" class="btn btn-success exportBudgetCode" style="float: right; margin-right: 1%;" id="exportBudgetCode"><i class="fa fa-download"></i> Export</button>

            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="budgetCodeTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>

                                <th>Budget Code</th>
                                <th>Budget Items</th>
                                <th>Budget Owner</th>
                                <th>Total Budget</th>
                                <th>Procurement</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- <tbody>
                            @foreach ($result as $key => $value)
                                <tr>

                                    <td>
                                        {{ $value->budget_code }}
                                    </td>
                                    <td>{{ $value->budget_item }}</td>
                                    <td>{{ $value->fullname }}</td>
                                    <td>$@money($value->total)</td>
                                    <td>$@money($value->remaining)</td>
                                    <td>$@money($value->payment_remaining)</td>
                                    <td>
                                        <a href="{{ url('budgetcode/detail/' . Crypt::encrypt($value->budget_code)) }})}}"
                                            data-toggle="tooltip" data-placement="top" title="View">
                                            <i class="fa fa-folder-open" aria-hidden="true"></i>
                                        </a>
                                        &nbsp;
                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Edit"
                                            class="view_budget_code" data-budget_code={{ $value->budget_code }}
                                            data-budget_item={{ $value->budget_item }}
                                            data-budget_name={{ $value->budget_owner }}
                                            data-budget_f_name={{ $value->firstname }}
                                            data-budget_l_name={{ $value->lastname }} data-total={{ $value->total }}
                                            data-procurement={{ $value->remaining }}
                                            data-payment={{ $value->payment_remaining }}>
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>

    <div class="modal fade" id="newgroup-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-gear"></i> Budget Code Management</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('budgetcode/upload') }}" enctype="multipart/form-data"
                    id="form_add_edit">
                    @csrf
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs  tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home1" role="tab"
                                        aria-expanded="true">ADD NEW</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#profile1" role="tab"
                                        aria-expanded="false">UPLOAD FILE</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content tabs card-block">
                                <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                                    <div class="table-responsive">
                                        <table style="width:100%" cellpadding="7px">
                                            <tr>
                                                <td colspan="2" style="text-align: right">
                                                    <span id="spn_require" style="color: red"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">
                                                    <span style="color: red">*</span> Budget Code
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="budgetcode"
                                                        id="budgetcode1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;"><span style="color: red">*</span> Budget
                                                    Items</td>
                                                <td>
                                                    <input type="text" class="form-control" name="budget_item"
                                                        id="budgetitem1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;"><span style="color: red">*</span> Budget
                                                    Owner</td>
                                                <td>
                                                    <select class="js-example-basic-single" name="owner1" id="owner1">
                                                        <option value="" selected="" disabled="">Select one</option>
                                                        @foreach ($alluser as $value)
                                                            <option value="{{ $value->email }}"
                                                                data-department="{{ $value->department }}"
                                                                data-position="{{ $value->position }}"
                                                                data-loginid="{{ $value->userid }}">
                                                                {{ $value->firstname }} {{ $value->lastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="owner_name" id="owner_name1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;"><span style="color: red">*</span> Total</td>
                                                <td>
                                                    <input type="text" class="form-control" name="total_b" id="total_b1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;"><span style="color: red">*</span> Procurement
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="total_pr" id="total_pr1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;"><span style="color: red">*</span> Payment
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="total_pay"
                                                        id="total_pay1">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="profile1" role="tabpanel" aria-expanded="false">
                                    <input class="form-control" type="file" name="fileupload">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success waves-effect "><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-group"></i> <span id="groupidshow">Modify Budget Code</span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('budgetcode/save') }}" id="form_add_edit">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id_dis">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table style="width:100%" cellpadding="7px">
                                <tr>
                                    <td style="text-align: right;">Budget Code</td>
                                    <td>
                                        <input type="text" class="form-control" name="budgetcode" id="budgetcode" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Budget Items</td>
                                    <td>
                                        <input type="text" class="form-control" name="budget_item" id="budgetitem">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Budget Owner</td>
                                    <td>
                                        <select class="js-example-basic-single" name="owner" id="owner">
                                            <option value="" selected="" disabled="">Select one</option>
                                            @foreach ($alluser as $value)
                                                <option value="{{ $value->email }}"
                                                    data-department="{{ $value->department }}"
                                                    data-position="{{ $value->position }}"
                                                    data-loginid="{{ $value->userid }}">
                                                    {{ $value->firstname }} {{ $value->lastname }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="owner_name" id="owner_name">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Total</td>
                                    <td>
                                        <input type="text" class="form-control" name="total_b" id="total_b">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Procurement</td>
                                    <td>
                                        <input type="text" class="form-control" name="total_pr" id="total_pr">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Payment</td>
                                    <td>
                                        <input type="text" class="form-control" name="total_pay" id="total_pay">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success waves-effect " id="btn_update"><i
                                class="fa fa-save"></i> Update</button>
                        <button type="submit" name="submit" value="delete" class="btn btn-danger waves-effect "
                            id="btn_remove"><i class="fa fa-save"></i> Remove</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <form id="download_budget" method="post" action="{{ url('export/budge-code') }}">
    {{ csrf_field() }}
</form>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $("#owner").select2({
                dropdownParent: $("#edit-Modal")
            });
        });
        $(document).on('click','.exportBudgetCode',function(){
         $("#download_budget").submit();
       })

    </script>
    <script>
        $(document).ready(function() {
            $("#owner1").select2({
                dropdownParent: $("#newgroup-Modal")
            });
        });

    </script>

    <script>
        $(document).on('click', '.view_budget_code', function() {

            $('#edit-Modal').modal('show');
            var budgetcode = $(this).data('budget_code');
            var budgetitem = $(this).data('budget_item');
            var budgetname = $(this).data('budget_name');
            var total = $(this).data('total');
            var procurement = $(this).data('procurement');
            var payment = $(this).data('payment');
            var owner_fname = $(this).data('budget_f_name');
            var owner_lname = $(this).data('budget_l_name');

            $('#budgetcode').val(budgetcode);
            $('#budgetitem').val(budgetitem);
            $('#owner').val(budgetname);
            $('#owner').trigger('change');
            $('#total_b').val(total);
            $('#total_pr').val(procurement);
            $('#total_pay').val(payment);
            $('#owner_name').val(owner_fname + ' ' + owner_lname);
        })

    </script>
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
    <script>
        $('#newgroup-Modal').on('hidden.bs.modal', function(e) {
            $(this)
                .find("input,textarea,select")
                .val('')
                .end()
                .find("input[type=checkbox], input[type=radio]")
                .prop("checked", "")
                .end();
        })

    </script>
    <script>
        $("#form_add_edit").validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: ":hidden",

            rules: {
                budgetcode: {
                    required: true,
                },
                budget_item: {
                    required: true,
                },
                owner: {
                    required: true,
                },
                total_b: {
                    required: true,
                },
                total_pr: {
                    required: true,
                },
                total_pay: {
                    required: true,
                }
            },

            messages: {
                budgetcode: "(Please fill all *",
                budget_item: "(Please fill all *",
                owner: "Please fill all *",
                total_b: "Please fill all *",
                total_pr: "Please fill all *",
                total_pay: "Please fill all *"
            },

            errorPlacement: function(error, element) {

                if (element.attr("name") == "budgetcode" || element.attr("name") == "budget_item" || element
                    .attr("name") == "owner" ||
                    element.attr("name") == "total_b" || element.attr("name") == "total_pr" || element.attr(
                        "name") == "total_pay") {
                    $('#spn_require').empty();
                    error.appendTo('#spn_require');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                var sagement_con = $('.total_sage').val();
                var product_con = $('.grand_total').val();
                $(".overlay").show();
                form.submit();
            }
        });

    </script>
     <script type="text/javascript">
		$(document).ready(function() {
	
			// DataTable
			$('#budgetCodeTable').DataTable({
				"pageLength": 10,
				order: [[ 0, 'asc' ]],
				processing: true,
				serverSide: true,
				ordering:  true,
				searching: true,
				ajax: "{{ route('get-budget-code-listing-data') }}",
                columnDefs: [ {
                    targets: 6,
                    orderable: false
                } ],
				columns: [{
						data: 'budget_code',
						name: 'budget_code'
					},
					{
						data: 'budget_item',
						name: 'budget_item'
					},
					{
						data: 'budget_owner',
						name: 'budget_owner'
					},
					{
						data: 'total',
						name: 'total'
					},
					{
						data: 'remaining',
						name: 'remaining'
					},
                    {
						data: 'payment_remaining',
						name: 'payment_remaining'
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
