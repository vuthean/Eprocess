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
@include('siderbar.dashboard')
@endsection
@section('breadcrumb')
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Budget Code Tracking</h4>
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
                    <li class="breadcrumb-item"><a href="{{ route('user_log.index') }}">User Tracking</a>
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
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="simpletable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Budget Code</th>
                                <th>Budget Items</th>              
                                <th>Total Budget</th>    
                            </tr>
                        </thead>
                        <tbody>   
                            @foreach($log->old_value as $key => $value)      
                                <tr
                                data-toggle="modal"
                                data-target="#large-Modal"
                                data-id="{{$value['budget_code']}}"
                                style="cursor: pointer;"
                                >
                                    <td>
                                        {{ $value['budget_code'] }}
                                    </td>
                                    <td>{{ $value['budget_item']  }}</td>
                                    <td>$@money($value['total'] )</td>   
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
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
                        <div class="col-lg-12"  id="data-compare"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function () {
        $("#large-Modal").on("show.bs.modal", function (e) {
            /** make sure the current state was clear */
            $("#data-compare").empty()
            let id = $(e.relatedTarget).data('id');
            let new_value = @json($log->new_value);
            let old_value = @json($log->old_value);
            let new_budgets = new_value.find(function (el) {
            return el.budget_code == id
            });
            let old_budgets = old_value.find(function (el) {
            return el.budget_code == id
            });
            let oldValue = JSON.stringify(old_budgets);
            let newValue = JSON.stringify(new_budgets);
            let oldValues = oldValue.replace("{","").replace("}","").split(",");
            let newValues = newValue.replace("{","").replace("}","").split(",");
            let countData = oldValues.length > 0 ? oldValues.length : newValues.length;
            let html = '<table class="table table-hover table-bordered">';
            html += '<thead><tr><td>Old Data</td><td>New Data</td></tr><thead/><tbody>'
            for(var i =0; i<countData;i++){
                if(oldValues[i] !== newValues[i]){
                    html += '<tr style="background-color: lightgoldenrodyellow;"><td>' + oldValues[i] + '</td><td>' + newValues[i] + '</td></tr>';
                }else{
                    html += '<tr><td>' + oldValues[i] + '</td><td>' + newValues[i] + '</td></tr>';
                }
            }
            html += '<tbody/></table>';
            $("#data-compare").append(html);
        });
    });

</script>
@endsection
