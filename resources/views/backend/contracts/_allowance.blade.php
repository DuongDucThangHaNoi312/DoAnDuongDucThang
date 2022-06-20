<div class="row">
        <div class="col-md-12">
            <table class="table table-bordered expense">
                <thead style="background: #3C8DBC;color: white;">
                <tr>
                    <th style="text-align: center; vertical-align: middle; width: 5%;">{!! trans('system.no.') !!}</th>
                    <th style="text-align: center; vertical-align: middle; width: 35%;">{!! trans('contracts.allowance_cat') !!}</th>
                    <th style="text-align: center; vertical-align: middle; width: 25%;">{!! trans('contracts.allowance_cost') !!}</th>
                    <th style="text-align: center; vertical-align: middle; width: 27%;">{!! trans('system.desc') !!}</th>
                    <th style="text-align: center; vertical-align: middle; width: 8%;">{!! trans('system.action.label') !!}</th>
                </tr>
                </thead>
                <tbody>
                <?php $allowance_cat = old('allowance_cat', $allowanceCat ?? []);?>
                @if (count($allowance_cat))
                    <?php
                    $desc = old('desc', $allowanceDesc ?? []);
                    $allowance_cost = old('allowance_cost', $allowanceCot);
                    ?>
                    @for ($i = 0; $i < count($allowance_cat); $i++)
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                {!! Form::select("allowance_cat[$i]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("allowance_cat[$i]", $allowance_cat[$i]), ['class' => 'form-control select2 ', ]) !!}
                            </td>

                            <td style="text-align: center; vertical-align: middle;">
                                {!! Form::text("allowance_cost[$i]", old("allowance_cost[$i]", $allowance_cost[$i]), ['class' => 'form-control currency allowance_cost']) !!}
                            </td>
                            <td style="vertical-align: middle;">
                                {!! Form::text("desc[$i]", old("desc[$i]", $desc[$i]), ['class' => 'form-control ']) !!}
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if ($i > 0)
                                    <a href="javascript:void(0);" class="btn btn-xs btn-default remove-expense">
                                        <i class="text-danger fa fa-minus"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0);" class="btn btn-xs btn-default add-expense">
                                        <i class="text-success fa fa-plus"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endfor
                @else
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">1</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {!! Form::select('allowance_cat[]', ['' => trans('system.dropdown_choice')] + $allowancesOption, old('allowance_cat[]'), ['class' => 'form-control select2 appendix_allowance_cat_new', ]) !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            {!! Form::text('allowance_cost[]', old('allowance_cost[]'), ['class' => 'form-control currency appendix_allowance_cost_new allowance_cost']) !!}
                        </td>
                        <td style="vertical-align: middle;">
                            {!! Form::text("desc[]", old("desc[]"), ['class' => 'form-control']) !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <a href="javascript:void(0);" class="btn btn-xs btn-default add-expense">
                                <i class="text-success fa fa-plus"></i>
                            </a>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>

            @if(Session::get('err_allowance'))
                <ul>
                    <li class="text-danger">{!! Session::get('err_allowance') !!}</li>
                </ul>
            @endif
        </div>
    </div>
