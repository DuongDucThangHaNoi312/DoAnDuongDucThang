<?php $counter = 1; ?>
@for ($i = 1; $i < count($data); $i++)
    @if($i == 1)
        <tr>
            @for($j = 0; $j < count($data[$i]), $j < 24; $j++)
                @if ($j == 0 || $j == 1)
                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                        {!! $data[$i][$j] !!}
                    </td>
                @else
                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                        {!! $data[$i][$j] !!}
                    </td>
                @endif
            @endfor
        </tr>
    @else
        <?php
            $validFrom = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[$i][11]);
            $msg = $msg1 = "";
            if (!$validFrom->getTimestamp()) {
                $msg = "Hiệu lực từ tại dòng số {$i} không đúng định dạng dd/MM/yyyy";
            }
            if ($data[$i][12]) {
                $validTo = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[$i][12]);
                if (!$validFrom->getTimestamp()) {
                    $msg1 = "Hiệu lực từ tại dòng số {$i} không đúng định dạng dd/MM/yyyy";
                }
            }
        ?>
        <tr>
            @for($j = 0; $j < count($data[$i]), $j < 24; $j++)
                @if ($j == 0)
                    <td style="text-align: center; vertical-align: middle;">
                        {!! $counter++ !!}
                    </td>
                @elseif($j == 11)
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="text">{!! $validFrom ? $validFrom->format('d/m/Y') : "" !!}</span>
                    </td>
                @elseif($j == 12)
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="text">{!! $validTo ? $validTo->format('d/m/Y') : "" !!}</span>
                    </td>
                @elseif($j > 12)
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="integer">
                            @if ($data[$i][$j])
                                {!! preg_replace("/[^0-9]/", "", $data[$i][$j]) !!}
                            @endif
                        </span>
                    </td>
                @else
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="text">{!! $data[$i][$j] !!}</span>
                    </td>
                @endif
            @endfor
        </tr>
    @endif
    @if ($msg)
        <script>
            $(function(){
                toastr.error('{!! $msg !!}', '{!! trans('system.have_an_error') !!}');
            });
        </script>
    @endif
    @if ($msg1)
        <script>
            $(function(){
                toastr.error('{!! $msg1 !!}', '{!! trans('system.have_an_error') !!}');
            });
        </script>
    @endif
@endfor
<script>
    $(function(){
        $.fn.editable.defaults.mode = 'inline';
        $('.text').editable({emptytext: '-', unsavedclass: null, validate: function(value) {
                var value = $.trim(value);
                if (value.length > 255) return "Chuỗi không được vượt quá 255 kí tự";
            }
        });
        $('.text_require').editable({emptytext: '-', unsavedclass: null, validate: function(value) {
                var value = $.trim(value);
                if(value == '') return "{!! trans('system.data_not_empty') !!}";
                if (value.length > 100) return "Chuỗi không được vượt quá 100 kí tự";
            }
        });
        $('.integer').editable({emptytext: '-', unsavedclass: null, validate: function(value) {
                if ($.trim(value) != '') {
                    if (isNaN(value))  return 'Dữ liệu phải là một số nguyên';
                    if (value%1 != 0)  return 'Dữ liệu phải là một số nguyên';
                    if(value < 0 || value > 99999999) return 'Dữ liệu nằm trong khoảng từ 0 đến 99,999,999';
                }
            }
        });
    });
</script>