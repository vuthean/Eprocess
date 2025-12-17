@extends('layouts.master')
@section('menu')
	@include('menu.dashboard')
@endsection
@section('breadcrumb')
	<div class="row page-header">
       	<div class="col-lg-6 align-self-center ">
            <h2>Dashboard</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>                
       	       	<li class="breadcrumb-item active">John Doe</li>     
            </ol>
        </div>
    </div>
@endsection

@section('content')
	<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-default">
                  Winner Last Event
                </div>
                <div class="card-body">
                    <table id="datatable2" class="table table-striped dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Age</th>
                                <th>Start date</th>
                                <th>Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tiger Nixon</td>
                                <td>System Architect</td>
                                <td>Edinburgh</td>
                                <td>61</td>
                                <td>2011/04/25</td>
                                <td>$320,800</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection