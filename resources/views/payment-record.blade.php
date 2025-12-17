@extends('layouts.master')
@section('menu')
	@include('siderbar.tasklist')
@endsection
@section('breadcrumb')
<div class="page-header">
   <div class="row align-items-end">
      <div class="col-lg-8">
         <div class="page-header-title">
            <div class="d-inline">
               <h4>PAYMENT RECORDS</h4>               
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
               PAYMENT
               </li>
               <li class="breadcrumb-item"><a href="#!">RECORDS</a> </li>
            </ul>
         </div>
      </div>
   </div>
</div>
@endsection
@section('content')
	<div class="col-sm-12">
      <div class="col-sm-12" style="padding-left: 0px;">
         <div class="form-inline">  
               <input type="date" id="start_date" name="start_date" class="form-control mb-2 mr-sm-2" >
               <span style="padding-right: 8px;">TO</span>
               <input type="date" id="end_date" name="end_date" class="form-control mb-2 mr-sm-2" >
               <button id="btn_searh" class="btn btn-sm btn-primary mb-2" style="height: 36px; font-size: 14px;">Search</button>  
            </div>
      </div>
      <div class="card">
         <div class="card-block">
            <div class="dt-responsive table-responsive">
               <table id="paymentRecord"
                  class="table table-striped table-bordered nowrap">
                  <thead>
                     <tr>
                        <th>No</th>
                        <th>Request Number</th> 
                        <th>Requester</th>                                          
                        <th>From Department</th>
                        <th>CCY</th>
                        <th>Amount</th>
                        <th>FX Rate</th>
                        <th>Con. USD</th>
                        <th>Approval Date</th>  
                        <th>Request Date</th>
                        <th style="display: none;" >Created Date</th>
                     </tr>
                  </thead>
                  <tbody>
                        @foreach($payments as $key =>$payment)
                        <tr>
                           <td></td>
                           <td>
                              <a href="{{url($payment->description.'/'.Crypt::encrypt($payment->req_recid.'___'.'yes'))}}"​>
                                 {{$payment->req_recid}}
                              </a>
                           </td>	                
                           <td>{{$payment->requester_name}}</td>
                           <td>{{$payment->from_department}}</td>
                           <td>{{$payment->ccy}}</td>
                           <td>
                              @if($payment->ccy == 'USD')
                               {{$payment->grand_total_usd}} 
                              @else
                               {{$payment->grand_total_khr}} 
                              @endif
                           </td>
                           <td>{{$payment->exchange_rate}}</td>
                           <td>{{$payment->grand_total_usd}}</td>
                           <td>{{$payment->approved_at}}</td>
                           <td>{{$payment->req_date}}</td>
                           <td style="display: none;" >{{$payment->for_searching_date}}</td>
                        </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
@endsection
@section('script')
    <script>
        $(document).ready(function()
        {
            var table = $('#paymentRecord').DataTable({
               dom: 'Bfrtip',
               lengthMenu: [
                  [ 10, 25, 50, -1 ],
                  [ '10 rows', '25 rows', '50 rows', 'Show all' ]
               ],
               buttons: [
                  'pageLength',
                  'excelHtml5',
                  'pdfHtml5'
               ],
               "searching": true,
            }); 

            $('#btn_searh').on('click', function() {
                table.draw();
            });   

            table.on( 'order.dt search.dt', function () {
               table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                     cell.innerHTML = i+1;
                     table.cell(cell).invalidate('dom');
               } );
            } ).draw();
           
        });

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var dStart = new Date($('#start_date').val());
                var dEnd   = new Date($('#end_date').val());
                var dData  = new Date(data[10]);

                var search =parseFloat( dData.getTime()) || 0; // use data for the create date column 
                var min = parseFloat(dStart.getTime()) || search;
                var max = parseFloat(dEnd.getTime()) || search;  
 
                if ( ( isNaN( min ) && isNaN( max ) ) ||
                    ( isNaN( min ) && search <= max ) ||
                    ( min <= search   && isNaN( max ) ) ||
                    ( min <= search   && search <= max ) )
                {
                    return true;
                }
                return false;
            }
         );
   
    </script>
@endsection