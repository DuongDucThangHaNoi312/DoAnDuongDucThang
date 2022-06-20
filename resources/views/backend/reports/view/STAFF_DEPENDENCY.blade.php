<table class="table table-bordered" style="width: 100%" id="data">
    <thead>
        <tr style="background-color: #92D050;">
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Công ty</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phòng ban</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Mã Nhân viên</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Nhân viên</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Người phụ thuộc</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Mã số thuế</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ thuộc</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ thuộc từ</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ thuộc đến</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Mối quan hệ</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Tuổi</b></td>
            <td align="center" valign="middle" width="15" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Ngày sinh</b></td>
        </tr>
    </thead>
    <?php $relationships = \App\Defines\Staff::getFamilyRelationshipsForOption(); ?>
    @foreach($data['data'] as $companyId => $comData)
        <?php
            /*if ($oldCom == null) {
                $oldCom = $companyId;
            }
            if ($currentCom == null) {
                $currentCom = $companyId;
            }*/
        ?>
        @foreach($comData as $departmentId => $deptData)
            <?php
                /*if ($oldDept == null) {
                    $oldDept = $departmentId;
                }
                if ($currentDept == null) {
                    $currentDept = $departmentId;
                }*/
            ?>
            @foreach($deptData as $staffId => $families)
                <?php
                    /*if ($oldStaff == null) {
                        $oldStaff = $staffId;
                    }
                    if ($currentStaff == null) {
                        $currentStaff = $staffId;
                    }*/
                ?>
                @foreach($families as $family)
                <tr>
                    <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" rowspan="">
                        {!! $data['companies'][$companyId] ?? "" !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" rowspan="">
                        {!! $data['departments'][$departmentId] ?? "" !!}
                    </td>
                    <?php $fullname = $data['staffs'][$staffId] ?? ""; $code = explode('-', $fullname); ?>
                    <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" rowspan="">
                        {!! $code[0] !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" rowspan="">
                        {!! substr($fullname, strlen($code[0])+1, strlen($fullname)-strlen($code[0])+2) !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        {!! $family['fullname'] !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        {!! $family['tax_code'] !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        {!! $family['dependent'] ? 'Có' : 'Không' !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        @if ($family['dependent_from'])
                            {!! date('m/Y', strtotime($family['dependent_from'])) !!}
                        @endif
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        @if ($family['dependent_to'])
                            {!! date('m/Y', strtotime($family['dependent_to'])) !!}
                        @endif
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        {!! $relationships[$family['relationship']] !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        {!! $family['age'] !!}
                    </td>
                    <td align="center" valign="middle" style="border: 1px solid #000000;">
                        {!! date('d/m/Y', strtotime($family['dob'])) !!}
                    </td>
                </tr>
                @endforeach
            @endforeach
        @endforeach
    @endforeach
</table>