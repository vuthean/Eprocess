<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <title>Document</title>
    <style>
    
    </style>
</head>
<body>  

        <table> 
           
            <tr rowspan="2">
                <th><b>Budget Code</b></th>
                <th><b>Budget Items</b></th>
                <th><b>Budget Owner</b></th>
                <th><b>Total Budget</b></th>
                <th><b>Remaining  Procurement </b></th>
                <th><b>Remaining  Payment</b></th>
                
            </tr>
            @foreach($budgetCode as $key => $items)
                <tr>
                    <td>{{$items->budget_code}}</td>
                    <td>{{$items->budget_item}}</td>
                    <td>{{$items->fullname}}</td>
                    <td>{{$items->total}}</td>
                    <td>{{$items->remaining}}</td>
                    <td>{{$items->payment_remaining}}</td>
                </tr>
            @endforeach
        </table>
        
</body>
</html>