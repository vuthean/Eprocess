@extends('layouts.master')
@section('menu')
    @include('siderbar.groupmgt')
@endsection
@section('breadcrumb')
    @include('breadcrumb.member')
@endsection
@section('content')
    <div class="col-sm-12">
        <!-- Zero config.table start -->
        <div class="card">
            <div class="card-header">
                <h5>{{ $filter }}</h5>

                <button type="button" class="btn btn-success" style="float: right;" data-toggle="modal"
                    data-target="#newmember-Modal"><i class="fa fa-plus"></i> New</button>

            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="simpletable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Fullname</th>
                                <th>Department/Branch</th>
                                <th>Position</th>
                                <th>Role</th>
                                <th>Maximum Budget</th>
                                @if($filter == 'GROUP_CFO')
                                    <th>IS CFO</th>
                                @endif

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $key => $value)
                                @if($value->status == '1')
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>
                                            <a href="#" ​ class="name_click" data-toggle="modal" data-target="#editmember-Modal"
                                                data-groupid="{{ $value->group_id }}" data-email="{{ $value->email }}"
                                                data-fullname="{{ $value->firstname }} {{ $value->lastname }}"
                                                data-department="{{ $value->department }}"
                                                data-position="{{ $value->position }}" data-loginid="{{ $value->login_id }}"
                                                data-roleid="{{ $value->role_id }}" data-budget="{{ $value->budget }}"
                                                data-is_cfo ="{{ $value->is_cfo }}">
                                                {{ $value->firstname }} {{ $value->lastname }}
                                            </a>
                                        </td>
                                        <td>{{ $value->department }}</td>
                                        <td>{{ $value->position }}</td>
                                        <td>{{ $value->role_name }}</td>
                                        <td>{{ $value->budget }}</td>
                                        @if($filter == 'GROUP_CFO')
                                            <td>{{ $value->is_cfo=='1'?'YES':'NO' }}</td>
                                        @endif
                                    </tr>
                                @endif
                                
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>
    <div class="modal fade" id="newmember-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-group"></i> Add new Member</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('group/member/save') }}">
                    @csrf
                    <input type="hidden" class="form-control" name="group_id" id="group_id_new"
                        value="{{ $filter }}">
                    <input type="hidden" class="form-control" name="login_id" id="login_id">

                    <div class="modal-body">
                        <div class="table-responsive">


                            <table class="table table-hover">
                                @if ($filter == 'GROUP_ADMIN' or $filter == 'GROUP_PROCUREMENT' or $filter == 'GROUP_FINANCE' or $filter == 'GROUP_CFO' or $filter == 'GROUP_ACCOUNTING' or $filter == 'GROUP_CEO' or $filter == 'GROUP_MDOFFICE' or $filter == 'GROUP_MARKETING' or $filter == 'GROUP_SECONDLINE_EXCO' or $filter == 'GROUP_MEMBER_EXCO' or $filter == 'GROUP_ADMINISTRATION')
                                    <input type="hidden" name="role" value="4">
                                @else

                                    <tr>
                                        <td style="text-align: right;">Select Role</td>
                                        <td>
                                            <select class="form-control" name="role" id="slc_role">
                                                <option value="" disabled="" selected="">Select One</option>
                                                <option value="1">Maker</option>
                                                <option value="2">Reviewer</option>
                                                <option value="3">Approver</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr id="budget" style="display: none;">
                                        <td style="text-align: right;">Maximum Budget</td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3">$</span>
                                                <input type="text" class="form-control" placeholder="Right addon"
                                                    name="budget_value" id="budget_value">
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="text-align: right;">Select Name </td>
                                    <td>
                                        <select class="js-example-basic-single" name="email" id="slc_name">
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


                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Department/Branch</td>
                                    <td>
                                        <input type="text" class="form-control" name="department" id="department"
                                            readonly="">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Position</td>
                                    <td>
                                        <input type="text" class="form-control" name="poition" id="position" readonly="">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" value="add" class="btn btn-success waves-effect "><i
                                class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editmember-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-group"></i> <span id="name_user"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="post" action="{{ route('group/member/save') }}">
                    @csrf
                    <input type="hidden" class="form-control" name="group_id" id="group_id_dis"
                        value="{{ $filter }}">
                    <input type="hidden" class="form-control" name="login_id" id="login_id_dis">
                    <input type="hidden" class="form-control" name="email" id="email_dis">

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                @if ($filter == 'GROUP_ADMIN' or $filter == 'GROUP_PROCUREMENT' or $filter == 'GROUP_FINANCE' or $filter == 'GROUP_CFO' or $filter == 'GROUP_ACCOUNTING' or $filter == 'GROUP_CEO' or $filter == 'GROUP_MDOFFICE' or $filter == 'GROUP_MARKETING' or $filter == 'GROUP_SECONDLINE_EXCO' or $filter == 'GROUP_MEMBER_EXCO')
                                    <input type="hidden" name="role" value="4">
                                @else
                                    
                                    <tr id="budget_dis" style="display: none;">
                                        <td style="text-align: right;">Maximum Budget</td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3">$</span>
                                                <input type="text" class="form-control" placeholder="Right addon"
                                                    name="budget_value" id="budget_value_dis">
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="text-align: right;">Employee Name</td>
                                    <td>

                                        <input type="text" class="form-control" name="fullname" id="fullname_dis"
                                            readonly="">

                                 <tr>
                                        <td style="text-align: right;">Select Role</td>
                                        <td>
                                            <select class="form-control" name="role" id="slc_role_dis">
                                                <option value="" disabled="" selected="">Select One</option>
                                                <option value="1">Maker</option>
                                                <option value="2">Reviewer</option>
                                                <option value="3">Approver</option>
                                            </select>
                                        </td>
                                    </tr>   </td>
                                </tr>

                                <tr>
                                    <td style="text-align: right;">Department/Branch</td>
                                    <td>
                                        <input type="text" class="form-control" name="department" id="department_dis"
                                            readonly="">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Position</td>
                                    <td>
                                        <input type="text" class="form-control" name="poition" id="position_dis"
                                            readonly="">
                                    </td>
                                </tr>
                                @if($filter == 'GROUP_CFO')
                                <tr>
                                        <td style="text-align: right;">Is CFO</td>
                                        <td>
                                            <select class="form-control" name="is_cfo" id="is_cfo">
                                                <option value="" disabled="" selected="">Select One</option>
                                                <option value="1">CFO</option>
                                                <option value="0">Not CFO</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-danger waves-effect " value="delete"><i
                                class="fa fa-save"></i> Remove</button>
                        <button type="submit" class="btn btn-success waves-effect " name="submit" value="update"><i
                                class="fa fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   
@endsection
@section('script')

    <script>
        $(document).ready(function() {
            $("#slc_name").select2({
                dropdownParent: $("#newmember-Modal")
            });
        });

    </script>

    <script>
        $(document).on('click', '.name_click', function() {
            // $('.group_id_click').on('click',function(){
            // $('#groupidshow').text('')
            $('#name_user').empty()
            $('#budget_value_dis').val("")
            var group_id = $(this).data('groupid');
            var department = $(this).data('department');
            var position = $(this).data('position');
            var roleid = $(this).data('roleid');
            var budget = $(this).data('budget');
            var fullname = $(this).data('fullname');
            var email = $(this).data('email');
            var is_cfo = $(this).data('is_cfo');
            // alert(email)
            $('#is_cfo').val(is_cfo);
            $('#group_id_dis').val(group_id);
            $('#department_dis').val(department);
            $('#position_dis').val(position);
            $('#slc_role_dis').val(roleid);
            $('#fullname_dis').val(fullname);
            $('#name_user').append(fullname);
            $('#email_dis').val(email);
            if (roleid == '3') {
                $('#budget_dis').show();
                $('#budget_value_dis').val(budget)
            } else {
                // $('#budget_value').val("");
                $('#budget_dis').hide();
            }
        });
        $('#slc_role_dis').on('change', function() {
            $('#budget_value').val("");
            var condition = $(this).find(":selected").val();
            // alert(condition)
            if (condition == '3') {
                $('#budget_dis').show();
            } else {
                // $('#budget_value').val("");
                $('#budget_dis').hide();
            }
        });

        $('#slc_name').on('change', function() {
            var email = $(this).find(':selected').data('email');
            var department = $(this).find(':selected').data('department');
            var position = $(this).find(':selected').data('position');
            var loginid = $(this).find(':selected').data('loginid');
            // alert(email)
            var fullname = $(this).find(':selected').data('fullname');
            $('#email').val(email);
            $('#department').val(department);
            $('#position').val(position);
            $('#login_id').val(loginid);
            $('#fullname').val(fullname);
        })

        $('#slc_role').on('change', function() {
            $('#budget_value').val("");
            var condition = $(this).find(":selected").val();
            // alert(condition)
            if (condition == '3') {
                $('#budget').show();
            } else {
                // $('#budget_value').val("");
                $('#budget').hide();
            }
        });


        $('#newmember-Modal').on('hidden.bs.modal', function() {
            // $('#slc_role').
            $('#email').val("");
            $('#department').val("");
            $('#position').val("");
            $('#login_id').val("");
            $('#fullname').val("");
            $('#budget_value').val("");
            $('#budget').hide();
            // $('[name=options]').val( '' );
        });

    </script>
@endsection
