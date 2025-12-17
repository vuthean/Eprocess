<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Journal Voucher EXCEL FORM</title>
   
</head>
<body style="font-size: 10px;border: 0.1pt solid #ccc">
    <br>
    <table>
        <tr>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th colspan="2">REQUEST NO.: </th>
            <td colspan="2"> {{$bankPayment->req_recid}}</td>
        </tr>
        <tr>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th colspan="2">BATCH NO.: </th>
            <td colspan="2"> {{$bankPayment->batch_number}}</td>
        </tr>
        <tr>
            <th>OFFICE : </th>
            <th>HEAD OFFICE CENTER </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th colspan="2">EXCHANGE RATE: </th>
            <td colspan="2">{{$bankPayment->exchange_rate}}</td>
        </tr>
        <tr>
            <th> DATE : </th>
            <th>{{\Carbon\Carbon::parse($bankPayment->created_at)->format('Y-m-d')}} </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th colspan="2">PAYMENT REQUEST </th>
            <td colspan="2">
                <dl>
                    @foreach($merge_req as $data)
                        <dt>{{$data}}</dt>
                @endforeach
                </dl>
            </td>
        </tr>
        
    </table>
    <table>
        <tr>
                
            <th colspan="15" style="border: 1px solid black;background-color:#ebebe0;text-align: center;"><b> BANK JOURNAL VOUCHER</b></th>
        </tr>
    </table>
    <table>
        <tr>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>No</b></th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>GL Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>Account Name</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>B/R Code </b></th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Ccy</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>Debit</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>Credit</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>Budget Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"><b>AL Budget Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Tax Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Supp Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Dept Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Pro Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Seg Code</b> </th>
            <th style="border: 1px solid black;background-color:#ebebe0;"> <b>Narratives</b> </th>
        </tr>
        @foreach ($bankPaymentDetails as $key => $value)
            <tr>
                <td style="border: 1px solid black">{{{++$key}}}</td>
                <td style="border: 1px solid black">{{{$value->gl_code}}}</td>
                <td style="border: 1px solid black;text-align: left;">{{{$value->account_name}}}</td>
                <td style="border: 1px solid black">{{{$value->branch_code}}}</td>
                <td style="border: 1px solid black">{{{$value->currency}}}</td>
                <td style="border: 1px solid black">@if ($value->dr_cr == 'DEBIT') @money($value->amount)  @endif</td>
                <td style="border: 1px solid black">@if ($value->dr_cr == 'CREDIT') @money($value->amount )  @endif</td>
                <td style="border: 1px solid black">{{{$value->budget_code}}}</td>
                <td style="border: 1px solid black">{{{$value->al_budget_code}}}</td>
                <td style="border: 1px solid black">@if($value->tax_code) {{$value->tax_code}} @else N/A @endif</td>
                <td style="border: 1px solid black">@if($value->supp_code) {{$value->supp_code}} @else N/A @endif</td>
                <td style="border: 1px solid black">@if($value->department_code) {{$value->department_code}} @else N/A @endif</td>
                <td style="border: 1px solid black">@if($value->product_code) {{$value->product_code}} @else N/A @endif</td>
                <td style="border: 1px solid black">@if($value->segment_code) {{$value->segment_code}} @else N/A @endif</td>
                <td style="border: 1px solid black;text-align: left;">@if($value->naratives) {!! nl2br(e($value->naratives)) !!} @else N/A @endif</td>
            </tr>
        @endforeach
        <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;border: 1px solid black">
                        TOTAL
                    </td>
                    <td class="tabledit-view-mode" style="padding: 5px;border: 1px solid black">
                        @if ($defaultCurrency == 'USD')
                            $@money($totalDRCR->total_DR)
                        @else
                            ៛@money($totalDRCR->total_DR)
                        @endif
                    </td>
                    <td class="tabledit-view-mode" style="padding: 5px;border: 1px solid black">
                        @if ($defaultCurrency == 'USD')
                            $@money($totalDRCR->total_CR)
                        @else
                        ៛@money($totalDRCR->total_CR)
                        @endif
                    </td>
                    <td colspan="8" style="border: 1px solid black"></td>
                </tr>
            </tfoot>
    </table>
    <table>
        <tr>
            <th colspan="2">Note: </th>
            <td colspan="13"> {{ $bankPayment->note }}</td>
        </tr>
        @if($budgetcode_na === 'N')
            <tr>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="3">BUDGET CODE </th>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="4">TOTAL</th>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="4">YTD</th>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="4">REMAINING</th>
            </tr>
            @foreach ($totalAndYTD as $key => $value)
                @if($value->budget_code !== 'NA')
                    <tr>
                        <td style="border: 1px solid black" colspan="3"> {{ $value->budget_code }} </td>
                        <td style="border: 1px solid black" colspan="4"> @money($value->total) </td>
                        <td style="border: 1px solid black" colspan="4"> @money($value->total_YTD) </td>
                        <td style="border: 1px solid black" colspan="4"> @money($value->remaining) </td>
                    </tr>
                @endif
            @endforeach
        @endif
        @if($totalAndYTDAL->count() > 0 and $al_budgetcode_na === 'N')
            <tr>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="3">AL BUDGET </th>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="4">TOTAL</th>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="4">YTD</th>
                <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black" colspan="4">REMAINING</th>
            </tr>
            @foreach ($totalAndYTDAL as $key => $value)
                @if($value->budget_code !== 'NA')
                    <tr>
                        <td style="border: 1px solid black" colspan="3"> {{ $value->budget_code }} </td>
                        <td style="border: 1px solid black" colspan="4"> @money($value->total) </td>
                        <td style="border: 1px solid black" colspan="4"> @money($value->total_YTD) </td>
                        <td style="border: 1px solid black" colspan="4"> @money($value->remaining) </td>
                    </tr>
                @endif
            @endforeach
        @endif
    </table>
    <table>
    <tr>
        <th colspan="7" style="border-top: 1px solid black;border-left: 1px solid black">Payment method: </th>
        <td colspan="8" style="border-top: 1px solid black;border-right: 1px solid black">{{ $bankPayment->payment_method_code }}</td>
    </tr>
    <tr>
        <th style="border-left: 1px solid black" colspan="7" >Beneficiary Bank:</th>
        <td style="border-right: 1px solid black" colspan="8">{{ $bankPayment->bank_name }}</td>
    </tr>
    <tr>
        <th style="border-left: 1px solid black" colspan="7">Swift Code:</th>
        <td colspan="8" style="border-right: 1px solid black">{{ $bankPayment->swift_code }}</td>
    </tr>
    <tr>
        <th style="border-left: 1px solid black" colspan="7">Address:</th>
        <td style="border-right: 1px solid black" colspan="8">{{ $bankPayment->account_currency }}</td>
    </tr>
    <tr>
        <th style="border-left: 1px solid black" colspan="7">Cheque/Account Name:</th>
        <td style="border-right: 1px solid black" colspan="8">{{ $bankPayment->account_name }}</td>
    </tr>
    <tr>
        <th style="border-left: 1px solid black" colspan="7">Account Number:</th>
        <td style="border-right: 1px solid black" colspan="8">{{ $bankPayment->account_number }}</td>
    </tr>
    <tr>
        <th style="border-left: 1px solid black" colspan="7">Address:</th>
        <td style="border-right: 1px solid black" colspan="8">{{ $bankPayment->beneficiary_number }}</td>
    </tr>
     <tr>
        <th  colspan="7" style="border-bottom: 1px solid black;border-left: 1px solid black">Purpose:</th>
        <td  style="border-bottom: 1px solid black;border-right: 1px solid black" colspan="8">{{ $bankPayment->invoice_number }}</td>
    </tr>
    </table>
    <table>
        <tr>
            <th style="background-color: #dad0be; font-weight: bold;border: 1px solid ;text-align: center;border: 1px solid black" colspan="15">ACTIVITY LOG</th>
        </tr>
        <tr>
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="4">DATE</th>
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="4">NAME</th>
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="3">ACTIVITY</th>​
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="4">COMMENT</th>
        </tr>
        @foreach ($auditlogs as $key => $value)
            <tr>
                <td colspan="4" style="border: 1px solid black"> {{ $value->datetime }} </td>
                <td colspan="4" style="border: 1px solid black"> {{ $value->name }} </td>
                <td colspan="3" style="border: 1px solid black"> {{ $value->activity }} </td>
                <td colspan="4" style="border: 1px solid black"> {{ $value->comment }} </td>
            </tr>
        @endforeach
    </table>
    <table>
        <tr>
            <th style="background-color: #dad0be; font-weight: bold;border: 1px solid black;text-align: center;" colspan="15">REFERENCE DOCUMENT</th>
        </tr>
        <tr>
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="5">DATE</th>
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="5">FILE NAME</th>
            <th style="background-color: #FFF8DC; font-weight: bold;border: 1px solid black" colspan="5">UPLOAD BY</th>
        </tr>
        @foreach ($documents as $key => $value)
            <tr>
                <td colspan="5" style="border: 1px solid black"> {{ $value->activity_datetime }} </td>
                <td colspan="5" style="border: 1px solid black"> {{ $value->filename }} </td>
                <td colspan="5" style="border: 1px solid black"> {{ $value->doer_name }} </td>
            </tr>
        @endforeach
    </table>
</body>
</html>