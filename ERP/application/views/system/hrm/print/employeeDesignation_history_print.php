<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h4>Employee Designation History</h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Employee ID</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['ECode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Employee Name</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['Ename1']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Designation</th>
            <th style="min-width: 30%" class="text-left theadtr">Start Date</th>
            <th style="min-width: 5%" class='theadtr'>End Date</th>
            <th style="min-width: 5%" class='theadtr'>Is Primary</th>
            <th style="min-width: 10%" class='theadtr'>Is Active</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($moreDesignation)) {
            foreach ($moreDesignation as $val) { ?>
                <tr>
                    <td><?php echo $num; ?>.&nbsp;</td>
                    <td><?php echo $val['DesDescription']; ?></td>
                    <td><?php echo $val['startDate_format']; ?></td>
                    <td><?php echo $val['endDate_format']; ?></td>
                    <td style="text-align: center"><?php if ($val['isMajor'] == 1) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }; ?></td>
                    <td style="text-align: center"><?php if ($val['isActive'] == 1) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }; ?></td>

                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="5" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
    </table>
</div>




