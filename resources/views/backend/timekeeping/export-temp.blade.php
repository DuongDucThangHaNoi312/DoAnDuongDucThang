<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <table class="table table-striped table-bordered">
    
        <thead>
            <tr style="border: 1px solid white ;">
                <th  style="border: 1px solid white ;font-family:serif" colspan="33"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th  style="font-family:serif;border: 1px solid white ; font-size:16px; font-weight:bold" colspan="30">{!! $detail->company->name_es !!}</th>
                <th  style="font-family:serif;border: 1px solid white ;" colspan="13"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th  style="font-family:serif;border: 1px solid white  ; font-size:11px; font-weight:bold" colspan="30">Add : {!! $detail->company->address_es !!}</th>
                <th style="font-family:serif;border: 1px solid white ;" colspan="13"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th style="font-family:serif;border: 1px solid white ;" colspan="33"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th style="font-family:serif;border: 1px solid white ;" colspan="33"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th style="border: 1px solid white ;" colspan="7" ></th>
                <th  style="font-family:serif;border: 1px solid white ; ;text-transform: uppercase; font-size:16px; font-weight:bold" colspan="30">{!! trans('timekeeping.detail_title')!!} / SALARY WORKSHEET </th>
                <th style="border: 1px solid white ;" colspan="11"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th style="border: 1px solid white ;" colspan="7"></th>
                <th  style="font-family:serif;border: 1px solid white ; ;text-transform: uppercase; font-size:16px;"  colspan="30">{{ $detail->department->name }} / {{ $detail->department->name_es }} </th>
                <th style="border: 1px solid white ;" colspan="11"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th style="border: 1px solid white ;" colspan="14"></th>
                <th style="font-family:serif;border: 1px solid white ; ; font-size:16px; " colspan="30">{{ $detail->month }} , {{ $detail->year }} </th>
                <th style="border: 1px solid white ;" colspan="16"></th>
            </tr>
            <tr style="border: 1px solid white ;">
                <th colspan="33" style="border: 1px solid white ;"></th>
            </tr>
            <tr style="border: 1px solid black ">
                <th rowspan="2" style="font-family:serif;writing-mode: vertical-rl;border: 1px solid black ; vertical-align: center;text-align :center;" >STT</th>
                <th rowspan="2" style="font-family:serif;height: 60px;width:10px;vertical-align: center;text-align :center;border: 1px solid black ;width:22px">NAME</th>
                @if (count($getDays) > 0)
                @foreach ($getDays as $key => $item)
                <th align="center" colspan="1" style="font-family:serif;writing-mode: vertical-rl; width:3px;background: {{  $item == 'Sun' ? '#F8CBAD' : '' }} ;padding: 0 5px 10px ; height: 30px;text-align :center;border: 1px solid black ;">{{ $item }}</th>
                @endforeach
                @endif
            </tr>
            <tr style="border: 1px solid black">
                <?php $i = -1 ?>
                @if (count($getDates) > 0)
                @foreach ($getDates as $key => $item)
                <?php $i++;  ?>
                <th    align="center" style="font-family:serif;background: {{  $getDays[$i] == 'Sun' ? '#F8CBAD' : '' }}; padding: 0 5px 10p;height: 30px;text-align :center;border: 1px solid black ;">{{ $item }}</th>
                @endforeach
                @endif
            </tr>
    
        </thead>
        <tbody>
            @if (count($items) > 0)  
            
            @foreach ($items as $key => $item)
            <tr  style="border: 1px solid black">
                <td align="center" style="font-family:serif;text-align :center;border: 1px solid black ;color :#4dd7d7">{{ $item->staff->code }}</td>
                <td style="font-family:serif;border: 1px solid black">{{ $item->staff->fullname }}</td>
                <?php $i = -1 ?>
                @foreach ($item->detail as $k => $detail)
                <?php $i++;?>
                {{-- <td align="center" style="border: 1px solid black ;text-align :center "> --}}
                <td align="center" style="font-family:serif;border: 1px solid black ;text-align :center;background: {{  $getDays[$i] == 'Sun' ? '#F8CBAD' : ''}}">
                        {{ in_array($detail['status'], [10, 2, 3, 4, 5]) ? 'v/2' : ($detail['status'] == 1 ? 'v' : '') }}
                    </td>
                @endforeach
            </tr>
            @endforeach
            @endif
            <tr style="border: 1px solid white ";>
                <td colspan="33"></td>
            </tr>
            <tr style="border: 1px solid white ";>
                <td style="border: 1px solid white " colspan="20"></td>
                <td style="font-family:serif;border: 1px solid white;font-size:11px; font-weight:bold ; " colspan="12">Ngày...tháng...Năm....</td>
                <td colspan="3" style="border: 1px solid white ";></td>
            </tr>
    
            <tr style="border: 1px solid white ;">
                <td colspan="33"></td>
             </tr>
         
            <tr style="border: 1px solid white ;">
                <td style="border: 1px solid white ;" ></td>
                <td  colspan="2" style="font-family:serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">BRANCH MANAGER</td>
                <td style="border: 1px solid white ;" colspan="4"></td>
                <td colspan="5" style="font-family:serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">CHIEF ACC</td>
                <td colspan="5" style="border: 1px solid white ;"></td>
                <td colspan="5" style="font-family:serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">HRADMIN</td>
                <td colspan="5" style="border: 1px solid white ;"></td>
                <td colspan="5" style="font-family:serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">CHECK BY</td>
                <td colspan="4" style="border: 1px solid white ;"></td>
            </tr>
           
            <tr style="border: 1px solid white ;">
               <td colspan="33"></td>
            </tr>
        </tbody>           
    </table>
</body>
</html>
