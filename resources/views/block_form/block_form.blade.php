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
@include('siderbar.blockform')
@endsection
@section('breadcrumb')
@include('breadcrumb.blockform')
@endsection
@section('content')
<div class="col-sm-12">
    <!-- Zero config.table start -->
    <div class="card">
        <div class="card-header">
            <h5>Block Date Listing</h5>
            <button type="button" hidden class="btn btn-info" style="float: right;" data-toggle="modal" data-target="#newgroup-Modal"><i class="fa fa-plus"></i> Upload</button>
            <button type="button" class="btn btn-success" style="float: right;" data-toggle="modal" data-target="#newgroup-Modal"><i class="fa fa-plus"></i> New</button>

        </div>
        <div class="card-block">
            <div class="dt-responsive table-responsive">
                <table id="budgetCodeTable" class="table table-striped table-bordered nowrap">
                    <thead>
                        <tr>

                            <th>No</th>
                            <th>Block Day</th>
                            <th>Create Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block_date as $key=>$item)
                        <tr>
                            <td>{{++$key}}</td>
                            <td>{{$item->block_day}}</td>
                            <td>{{$item->block_date}}</td>
                            <td>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Edit" class="view_budget_code" data-block_id="{{$item->id}}" data-block_day="{{$item->block_day}}">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>
                            </td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Zero config.table end -->
</div>
<!-- modal add -->
<div class="modal fade" id="newgroup-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-gear"></i> Add Block Day Management</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('block_form/store') }}" enctype="multipart/form-data" id="form_add_edit">
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs  tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home1" role="tab" aria-expanded="true">ADD NEW</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content tabs card-block">
                            <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                            <div>
                                <label for=""><b> Will Block all new request!</b></label>
                            </div>
                                <div class="table-responsive">
                                    <table style="width:100%" cellpadding="7px">
                                        <tr>
                                            <td colspan="2" style="text-align: right">
                                                <span id="spn_require" style="color: red"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;">
                                                <span style="color: red">*</span> Block Day
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="block_date" id="budgetcode1">
                                            </td>
                                        </tr>

                                    </table>
                                </div>
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

<!-- modal edit -->
<div class="modal fade" id="edit-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-group"></i> <span id="groupidshow">Modify Block Date</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('block_form/update') }}" id="form_add_edit">
                @csrf
                <div class="modal-body">
                    <div class="table-responsive">
                        <input type="hidden" id="block_day_value" name="block_day_value">
                        <table style="width:100%" cellpadding="7px">
                            <tr>
                                <td colspan="2" style="text-align: right">
                                    <span id="spn_require" style="color: red"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">
                                    <span style="color: red">*</span> ID
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="block_date" id="block_id" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">
                                    <span style="color: red">*</span> Block Day
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="block_date" id="block_day">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" value="edit" class="btn btn-success waves-effect " id="btn_update"><i class="fa fa-save"></i> Update</button>
                    <button type="submit" name="submit" value="delete" class="btn btn-danger waves-effect " id="btn_remove" onclick="return confirm('Are you sure?')"><i class="fa fa-save"></i> Remove</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
<!-- script -->
@section('script')
<script>
    $(document).on('click', '.view_budget_code', function() {

        $('#edit-Modal').modal('show');
        var id = $(this).data('block_id');
        var days = $(this).data('block_day');


        $('#block_id').val(id);
        $('#block_day').val(days);
        $('#block_day_value').val(id);



    })
</script>

<script>
    $("#form_add_edit").validate({
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        ignore: ":hidden",

        rules: {
            block_date: {
                required: true,
                max: 31,
                min: 1
            },
        },
        errorPlacement: function(error, element) {

            if (element.attr("name") == "block_date") {
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

@endsection