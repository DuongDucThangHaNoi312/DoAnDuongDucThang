<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="10" style="font-family:Arial, Helvetica; font-size:11px; font-weight:bold">{!! trans('timekeeping.detail_title') !!} {{ $detail->month }}/{{ $detail->year }}</th>
        </tr>
        <tr>
            <th colspan="50" style="font-family:Arial, Helvetica; font-size:11px; font-weight:bold">{{ $detail->department->name }} - {{ $detail->company->name }}</th>
        </tr>
    </table>
    
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{!! trans('system.no.') !!}</th>
                <th rowspan="2" align="center" width="10" style="border: 1px solid black ;line-height: 5;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{!! trans('timekeeping.code') !!} </th>
                <th rowspan="2" style="border: 1px solid black ;width: 20px;vertical-align: center;text-align :center; font-family:Arial, Helvetica; width: 24px">{!! trans('timekeeping.staff') !!}</th>
                @if (count($getDays) > 0)
                @foreach ($getDays as $key => $item)
                <th align="center" height="30" colspan="1" style="border: 1px solid black ;width: 4px  ;background: {{  $item == 'Sun' ? '#4dd7d7' : '' }};padding: 0 5px 10px; font-family:Arial, Helvetica">{{ $item }}</th>
                @endforeach
                @endif
    
                <th align="center" width="10" rowspan="2"  style="border: 1px solid black ;line-height: 5;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Số ngày <span><br></span>yêu cầu</th>
                <th style="border: 1px solid black ; font-family:Arial, Helvetica" align="center" width="10" colspan="2">Tổng số <br>ngày thường</th>
                <th style="border: 1px solid black ; font-family:Arial, Helvetica; vertical-align: center;text-align :center;" align="center" width="10" colspan="9">{!! trans('timekeeping.day_off_number') !!}</th>
                <th align="center" width="10" rowspan="2" style="border: 1px solid black ;line-height: 5;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Tổng công<span><br></span>thực tế</th>
            </tr>
            <tr>
                <?php $i = -1 ?>
                @if (count($getDates) > 0)
                @foreach ($getDates as $key => $item)
                <?php $i++;  ?>
                <th align="center" height="30" style="border: 1px solid black ;padding: 0 5px 10px;background: {{  $getDays[$i] == 'Sun' ? '#4dd7d7' : '' }}; font-family:Arial, Helvetica; text-align: center; vertical-align: center;" >{{ $item }}</th>
                @endforeach
                @endif
    
                <th style="border: 1px solid black ; font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" >{!! trans('timekeeping.hd') !!}</th>
                <th style="border: 1px solid black ;text-align: center; vertical-align: center;" align="center" >{!! trans('timekeeping.tv') !!}</th>
                
                <th style="border: 1px solid black ; font-family:Arial, Helvetica" align="center" width="8">70% <span><br></span>lương</th>
                <th style="border: 1px solid black ; font-family:Arial, Helvetica" align="center" width="8">Nghỉ <span><br></span> ốm</th>
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Không<span><br></span>lương</th>
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Nghỉ<span><br></span>cưới</th>
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Nghỉ <span><br></span>hiếu</th>   
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Nghỉ <span><br></span>phép</th>   
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Nghỉ <span><br></span>công tác</th>   
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Nghỉ <span><br></span>lễ</th>   
                <th style="border: 1px solid black ;font-family:Arial, Helvetica" align="center" width="8">Làm <span><br></span>tại nhà</th>   
            </tr>
    
        </thead>
        <tbody>
            @if (count($items) > 0)  
                @foreach ($items as $key => $item)
                <tr>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center">{{ $item->staff->code }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;vertical-align: center;">{{ $item->staff->fullname }}</td>
                    <?php $i = -1 ?>
                    @foreach ($item->detail as $k => $v)
                        <?php $i++; ?>
    
                        <td style="background: {{  $getDays[$i] == 'Sun' ? '#4dd7d7' : '' }}; padding: 0; border: 1px solid black; font-family:Arial, Helvetica; font-size:9px; text-align: center; vertical-align: center;">

                            @if ($item->timekeeping->department_id == $detail->department_id)
                                @if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                        @if (!in_array($holiday, ['H', 'T']))
                                            {{ $v['status'] == 1 ? $getShift[$v['shift']] : (in_array($v['status'], [2, 3, 4, 5, 10]) ? $getShift[$v['shift']] . '/2' : '') }}
                                        @endif
                                @elseif ($detail->department->type == \App\Define\Department::FUNCTIONAL_OFFICE)
                                    {{ $v['status'] == 1 ? 'v' : (in_array($v['status'], [2, 3, 4, 5, 10]) ? 'v/2' : '') }}
                                @else
                                    {{ $v['status'] == 1 ? 'v' : ($v['status'] == 10 ? 'v/2' : '') }}
                                @endif
                                {{ $v['day_off'] }}

                            @else    
                                @if ($item->timekeeping->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                        @if (!in_array($holiday, ['H', 'T']))
                                            {{ $v['status'] == 1 ? $getShift[$v['shift']] : (in_array($v['status'], [2, 3, 4, 5, 10]) ? $getShift[$v['shift']] . '/2' : '') }}
                                        @endif
                                @elseif ($item->timekeeping->department->type == \App\Define\Department::FUNCTIONAL_OFFICE)
                                    {{ $v['status'] == 1 ? 'v' : (in_array($v['status'], [2, 3, 4, 5, 10]) ? 'v/2' : '') }}
                                @else
                                    {{ $v['status'] == 1 ? 'v' : ($v['status'] == 10 ? 'v/2' : '') }}
                                @endif
                                {{ $v['day_off'] }}
                            @endif

                        </td>
                    @endforeach
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{!! $item->total_day_request !!}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica; text-align: center; vertical-align: center;" align="center" class="">{{  $item->total_hd }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica; text-align: center; vertical-align: center;" align="center" class="">{{ $item->total_tv }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica; text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_70_luong }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica; text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_om }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_khong_luong }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_cuoi }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_hieu }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_phep }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_cong_tac }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->nghi_le }}</td>
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center" class="">{{ $item->lam_tai_nha }}</td>
    
                    
                    <td style="border: 1px solid black ;font-family:Arial, Helvetica;text-align: center; vertical-align: center;" align="center">{{ $item->total }}</td>
                </tr>
                @endforeach
            @endif
            <tr style="border: 1px solid white ";>
                <td colspan="33"></td>
            </tr>
            <tr style="border: 1px solid white ";>
                <td style="border: 1px solid white " colspan="41"></td>
                <td style="font-family:Arial, Helvetica, sans-serif;border: 1px solid white;font-size:11px; font-weight:bold ; " colspan="12">Ngày...tháng...Năm....</td>
                <td colspan="3" style="border: 1px solid white ";></td>
            </tr>
    
            <tr style="border: 1px solid white ;">
                <td colspan="33"></td>
             </tr>
         
            <tr style="border: 1px solid white ;">
                <td style="border: 1px solid white ;" ></td>
                <td  colspan="2" style="font-family:Arial, Helvetica, sans-serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">BRANCH MANAGER</td>
                <td style="border: 1px solid white ;" colspan="8"></td>
                <td colspan="10" style="font-family:Arial, Helvetica, sans-serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">CHIEF ACC</td>
                <td colspan="10" style="border: 1px solid white ;"></td>
                <td colspan="10" style="font-family:Arial, Helvetica, sans-serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">HRADMIN</td>
                <td colspan="1" style="border: 1px solid white ;"></td>
                <td colspan="10" style="font-family:Arial, Helvetica, sans-serif;border: 1px solid white ;text-transform: uppercase; font-size:11px; font-weight:bold">CHECK BY</td>
                <td colspan="4" style="border: 1px solid white ;"></td>
            </tr>
           
            {{-- <tr style="border: 1px solid white ;">
               <td colspan="33"></td>
            </tr> --}}
        </tbody>           
    </table>
</body>
</html>
