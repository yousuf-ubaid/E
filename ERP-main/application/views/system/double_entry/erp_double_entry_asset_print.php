<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 80px"
                                 src="<?php echo mPDFImage. $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h4>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?></strong>
                            </h4>
                            <h5>Double Entry for <?php echo $extra['name']; ?></h5>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $extra['code']; ?> System Code</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['primary_Code']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $extra['code']; ?> Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php $convertFormat=convert_date_format(); echo format_date($extra['date'],$convertFormat) ; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
</div>
<div class="table-responsive"><br>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class='theadtr' colspan="6">GL Details</th>
            <th class='theadtr' colspan="2"> Amount (<?php echo $extra['currency']; ?>)</th>
        </tr>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%">GL Code</th>
            <th class='theadtr' style="min-width: 10%">Secondary Code</th>
            <th class='theadtr' style="min-width: 30%">GL Code Description</th>
            <th class='theadtr' style="min-width: 5%">Type</th>
            <th class='theadtr' style="min-width: 10%">Segment</th>
            <th class='theadtr' style="min-width: 15%">Debit</th>
            <th class='theadtr' style="min-width: 15%">Credit</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $dr_total = 0;
        $cr_total = 0;
//        var_dump($extra['master_data']);
//            exit;
        if (!empty($extra['master_data'])) {
            foreach ($extra['master_data'] as $key => $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $key + 1; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['gl_code']; ?></td>
                    <td style="text-align:center;"><?php echo $val['gl_secondary']; ?></td>
                    <td><?php echo $val['gl_desc']; ?></td>
                    <td style="text-align:center;"><?php echo $val['gl_type']; ?></td>
                    <td style="text-align:center;"><?php echo $val['segment']; ?></td>
                    <td style="text-align:right;"><?php echo $val['gl_dr']; ?></td>
                    <td style="text-align:right;"><?php echo $val['gl_cr']; ?></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr class="danger"><td colspan="8" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="6">Double Entry Total (<?php echo $extra['currency']; ?>)</td>
            <td class="text-right total"><?php echo $extra['dr_total']; ?></td>
            <td class="text-right total"><?php echo $extra['cr_total']; ?></td>
        </tr>
        </tfoot>
    </table>
</div>