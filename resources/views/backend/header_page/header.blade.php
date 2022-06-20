<div class="box-header">
    <?php $i = (($customers->currentPage() - 1) * $customers->perPage()) + 1; ?>
    <div class="form-inline">
        <div class="form-group">
            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $customers->count()) . ' ( ' . trans('system.total') . ' ' . $customers->total() . ' )' !!}
            | <i>Chú giải: </i>&nbsp;&nbsp;
            <span class="text-info"><i class="fa fa-key"></i>{!! trans('users.changepwd') !!}</span>&nbsp;&nbsp;
            <span class="text-warning"><i class="glyphicon glyphicon-edit"></i>{!! trans('system.action.update') !!}</span>&nbsp;&nbsp;
            <span class="text-danger"><i class="glyphicon glyphicon-remove"></i>{!! trans('system.action.delete') !!}</span>
            <br/>
            <small class="label bg-green">{!! trans('customers.coin_available') !!}</small>&nbsp;&nbsp;
            <small class="label bg-red">{!! trans('customers.coin_holding') !!}</small>&nbsp;&nbsp;
            <small class="label bg-yellow">{!! trans('customers.coin_threshold') !!}</small>
        </div>
    </div>
</div>