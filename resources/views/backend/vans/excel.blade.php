@for ($i = 0; $i < count($data); $i++)
    @if($data[$i][1] == '' && $data[$i][5] == '') @continue @endif
    @if($i == 0)
        <tr>
            @for($j = 0; $j < count($data[$i]), $j < 17; $j++)
                @if ($j == 0 || $j == 1)
                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                        {!! $data[$i][$j] !!}
                    </td>
                @elseif(($j-2)%2 == 0)
                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                        {!! $data[$i][$j] !!}
                    </td>
                @elseif(($j-2)%2 == 1)
                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                        {!! $data[$i][$j] !!}
                    </td>
                @endif
            @endfor
        </tr>
    @else
        <tr>
            @for($j = 0; $j < count($data[$i]), $j < 17; $j++)
                @if ($j == 0)
                    <td style="text-align: center; vertical-align: middle;">
                        {!! $data[$i][0] ?? '' !!}
                    </td>
                @elseif($j == 1)
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="text">{!! $data[$i][1] !!}</span>
                    </td>
                @elseif(($j-2)%2 == 0)
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="text">{!! $data[$i][$j] !!}</span>
                    </td>
                @elseif(($j-2)%2 == 1)
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="text">{!! $data[$i][$j] !!}</span>
                    </td>
                @endif
            @endfor
        </tr>
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
                // if($.trim(value) == '') return "{!! trans('system.data_not_empty') !!}";
                if ($.trim(value) != '') {
                    if (isNaN(value))  return 'Dữ liệu phải là một số nguyên';
                    if (value%1 != 0)  return 'Dữ liệu phải là một số nguyên';
                    if(value < -9999 || value > 9999) return 'Dữ liệu nằm trong khoảng từ -9,999 đến 9,999';
                }
            }
        });
    });
</script>