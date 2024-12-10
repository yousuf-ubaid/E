<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 90px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h4>Employee Contract History</h4>
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

<div style="margin-top: 1%">
    <table class="<?php echo table_class(); ?>" id="contractHistoryTable">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo $this->lang->line('emp_contract_start_date'); ?></th>
            <th><?php echo $this->lang->line('emp_contract_end_date'); ?></th>
            <th><?php echo $this->lang->line('emp_contract_ref_no'); ?></th>
            <th>Is Current</th>
        </tr>
        </thead>
        <tbody>
        <?php

        if (!empty($history)) {
            foreach ($history as $key=>$val) { ?>
                <tr>
                    <td><?php echo ($key+1); ?>.&nbsp;</td>
                    <td><?php echo $val['contractStartDate']; ?></td>
                    <td><?php echo $val['contractEndDate']; ?></td>
                    <td><?php echo $val['contractRefNo']; ?></td>
                    <td><?php echo $val['isCurrent1']; ?></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr class="danger"><td colspan="5" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
    </table>
</div>
<?php

