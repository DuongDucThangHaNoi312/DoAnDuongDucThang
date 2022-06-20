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
            <th colspan="10" style="font-family:Arial, Helvetica; font-size:11px; font-weight:bold">
                {!! trans('timekeeping.detail_title_log') !!} {{ $timekeeping->month }}/{{ $timekeeping->year }}</th>
        </tr>
        <tr>
            <th colspan="50" style="font-family:Arial, Helvetica; font-size:11px; font-weight:bold">
                {{ $timekeeping->department->name }} - {{ $timekeeping->company->name }}</th>
        </tr>
    </table>

    <table class="">
        <thead>
            <tr>
                <th width="35" style="color: white;background: #3c8dbc;font-weight-bold: 600; font-size: 12px;border: 1px solid black ; font-family:Arial, Helvetica"
                    align="center">Nội dung cũ
                </th>
                <th width="35" style="color: white;background: #3c8dbc;font-weight-bold: 600; font-size: 12px;border: 1px solid black ; font-family:Arial, Helvetica"
                    align="center">Nội dung
                    mới</th>
                <th width="40" style="color: white;background: #3c8dbc;font-weight-bold: 600; font-size: 12px;border: 1px solid black ; font-family:Arial, Helvetica"
                    align="center">Ghi chú
                </th>
                <th width="20" style="color: white;background: #3c8dbc;font-weight-bold: 600; font-size: 12px;border: 1px solid black ; font-family:Arial, Helvetica"
                    align="center">Ngày cập nhật</th>
                <th width="20" style="color: white;background: #3c8dbc;font-weight-bold: 600; font-size: 12px;border: 1px solid black ; font-family:Arial, Helvetica"
                    align="center">Người cập
                    nhật</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $item)
                <tr class="hover">
                    <td align="left" width="35"
                        style="background: #f9f9f9;border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                        {{ $item['content_old'] }}</td>
                    <td align="left" width="35"
                        style="background: #f9f9f9;border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                        {{ $item['content'] }}</td>
                    <td align="left" width="40"
                        style="background: #f9f9f9;border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                        {{ $item['note'] }}</td>
                    <td align="left" width="20"
                        style="background: #f9f9f9;border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                        {{ $item['action_at'] }}</td>
                    <td align="left" width="20"
                        style="background: #f9f9f9;border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                        {{ $item['user'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
