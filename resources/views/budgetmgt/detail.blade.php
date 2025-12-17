@extends('layouts.master')
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
          <button type="button" hidden class="btn btn-info" style="float: right;" data-toggle="modal" data-target="#newgroup-Modal"><i class="fa fa-plus"></i> Upload</button>          
         	<button type="button" class="btn btn-success" style="float: right;" data-toggle="modal" data-target="#newgroup-Modal"><i class="fa fa-plus"></i> New</button>
         
      </div>
      <div class="card-block">
         <div class="dt-responsive table-responsive">
            <table id="simpletable"
               class="table table-striped table-bordered nowrap">
               <thead>
                  <tr>                     
                     <th>No</th>
                     <th>Ref. Number</th>                                                         
                     <th>Budget Code</th>                     
                     <th>Alternative Budget</th>                     
                     <th>Amount Used</th>                                                               
                  </tr>
               </thead>
               <tbody>
               		@foreach($result as $key =>$value)
	                  <tr>
	                    <td>{{++$key}}</td>
	                     <td>
                            <a href="{{url($value->description.'/'.Crypt::encrypt($value->req_recid.'___'.'no'))}}"​>
                              {{$value->req_recid}}
                           </a>
                        </td>
                       <td>{{$value->budget_code}}</td>
                       <td>{{$value->alternative_budget_code}}</td>
                       <td>                           
                        $@money($value->budget_amount_use + $value->alternative_amount_use)                                                                                      
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

<div class="modal fade" id="newgroup-Modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-gear"></i> Budget Code Management</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form method="post" action="{{route('budgetcode/upload')}}" enctype="multipart/form-data">
         @csrf         
             <div class="modal-body">
             <div class="col-lg-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs  tabs" role="tablist">
                   <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#home1" role="tab" aria-expanded="true">ADD NEW</a>
                   </li>
                   <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#profile1" role="tab" aria-expanded="false">UPLOAD FILE</a>
                   </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs card-block">
                   <div class="tab-pane active" id="home1" role="tabpanel" aria-expanded="true">
                      <div class="table-responsive">
                         <table class="table table-hover">
                            <tr>
                               <td style="text-align: right;">Branch Code</td>
                               <td>
                                  <input type="text" class="form-control" name="branchcode">
                               </td>
                            </tr>
                            <tr>
                               <td style="text-align: right;">Branch Name</td>
                               <td>
                                  <input type="text" class="form-control" name="branchname">
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
<div class="modal fade" id="editgroup-Modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><i class="fa fa-group"></i> <span id="groupidshow"></span></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form method="post" action="{{route('branchcode/save')}}">
         @csrf         
         <input type="hidden" name="group_id" id="group_id_dis">
         <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td style="text-align: right;">Group Name</td>
                        <td>
                        	<input type="text" class="form-control" name="branchcode" id="branchcode_dis">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Group Description</td>
                        <td>
                        	<input type="text" class="form-control" name="branchname" id="branchname_dis">
                        </td>
                    </tr>                    
                </table>
            </div>            
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>            
            <button type="submit" class="btn btn-success waves-effect " id="btn_update"><i class="fa fa-save"></i> Update</button>            
            <button type="submit" name="submit" value="delete" class="btn btn-danger waves-effect " id="btn_remove"><i class="fa fa-save"></i> Remove</button>            
         </div>
         </form>
      </div>
   </div>
</div>
@endsection
@section('script')
	<script>
        $(document).on('click', '.group_id_click', function () {
         
          $('#groupidshow').text('');
          var branchcode=$(this).data('branchcode');
          var branchname=$(this).data('branchname');
          // var group_des=$(this).data('groupdesc');
          $('#branchcode_dis').val(branchcode);
          $('#branchname_dis').val(branchname);
          // $('#group_description_dis').val(group_des);
          $('#groupidshow').append(branchname);
                
       })
   </script>
@endsection