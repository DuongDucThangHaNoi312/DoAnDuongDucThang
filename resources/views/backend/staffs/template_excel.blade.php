<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
    <table>
        <tr>
            <th style="text-align: center;">STT</th>
            <th style="text-align: center;" width="20">SKU</th>
            @foreach($categories as $code)
                <th style="text-align: center;" width="20">{!! $code !!}_quantity</th>
                <th width="50">{!! $code !!}_note</th>
            @endforeach
        </tr>
        <tr>
            <td colspan="{!! 2 + count($categories) * 3 !!}" height="180" style="color: #ff0000;">
                Ghi chú:<br/>
                <span style="color: #c2c2c2; font-weight: bold;">- !!! KHÔNG ĐƯỢC THAY ĐỔI HÀNG ĐẦU TIÊN ĐI !!!</span><br/>
                - Nếu chỉ nhập cho 01, 02, 03... kho, bỏ các cột của kho khác đi (bỏ theo cặp quantity và note)<br/>
                - Dữ liệu chuẩn theo định dạng sau:<br/>
                    + quantity (Số lượng): Yêu cầu, số nguyên từ 1-999, âm là xuất kho, dương hoặc không dấu là nhập kho<br/>
                    + note (Ghi chú): KHÔNG yêu cầu, dài không quá 255 kí tự<br/>
                - Bỏ trống ô số lượng, hệ thống coi như không thay đổi<br/>
                Ví dụ:<br/>
            </td>
        </tr>
        <tr>
            <th style="text-align: center;">1</th>
            <th style="text-align: center;">SKU123</th>
            <?php $i=1; ?>
            @foreach($categories as $code)
                <?php if ($i-- == 0) continue; ?>
                <th style="text-align: center;">-3</th>
                <th>Bán hàng đi</th>
            @endforeach
        </tr>
        <tr>
            <th style="text-align: center;">2</th>
            <th style="text-align: center;">SKU111</th>
            <?php $i=2; ?>
            @foreach($categories as $code)
                <?php if ($i-- == 0) continue; ?>
                <th style="text-align: center;">5</th>
                <th>Chuyển từ kho khác về</th>
            @endforeach
        </tr>
        <tr>
            <th style="text-align: center;">3</th>
            <th style="text-align: center;">SKU888</th>
            @foreach($categories as $code)
                <th style="text-align: center;">2</th>
                <th>Nhập mới</th>
            @endforeach
        </tr>
    </table>
</body>
</html>
