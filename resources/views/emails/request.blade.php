@extends('beautymail::templates.ark')

@section('content')

    
    @include('beautymail::templates.ark.contentStart')
        <h2 style="color: blue;font-weight: bold;">{{$subject}}</h2><br>
        <span>{{$content}}</span><br>
        <table style="width: 100%" cellpadding="5px" cellpadding="5px">
            
            <tr>
                <td>Request ID</td>
                <td>{{$record_id}}</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>{{$requester}}</td>
            </tr>
            <tr>
                <td>Division</td>
                <td>{{$division}}</td>
            </tr>
            <tr>
                <td>Department</td>
                <td>{{$branch}}</td>
            </tr>
           
            <tr>
                <td>Remark</td>
                <td>{{$remark}}</td>
            </tr>
        </table>
        <tr>
            <td width="100%" height="25"></td>
        </tr>
        <tr>
            <td>
                <a href="{{$link}}">View</a>                
            </td>
        </tr>
    @include('beautymail::templates.ark.contentEnd')

@stop