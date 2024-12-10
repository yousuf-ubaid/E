<?php  $payroll_project = getPolicyValues('PAYPR', 'All');

    if($payroll_project == 1){
        $colspan = 8;
    }else{
        $colspan = 7;
    }
       
    

?>

<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 80px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name'].' ('.current_companyCode().').'; ?></strong></h4>
                            <h5>Double Entry for Payroll</h5>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo 'SP'; ?> System Code</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $masterData['documentCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo 'SP'; ?> Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $masterData['payrollLastDate']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table><hr>
</div>

<div class="table-responsive"><br>
    <table class="table table-bordered table-striped" style="border: 1px solid #c0c0c0">
        <thead>
        <tr>
            <th class="theadtr" colspan="6">GL Details</th>
            <th class="theadtr" colspan="2"> Amount </th>
        </tr>
        <tr>
            <th class="theadtr" style="min-width: 5%">#</th>
            <th class="theadtr" style="min-width: 10%">GL Code</th>
            <th class="theadtr" style="min-width: 10%">Secondary Code</th>
            <th class="theadtr" style="min-width: 30%">GL Code Description</th>
            <th class="theadtr" style="min-width: 5%">Type</th>
            <th class="theadtr" style="min-width: 10%">Segment</th>

            <?php
            if($payroll_project == 1){
            ?>
            <th class="theadtr" style="min-width: 10%">Project</th>
            <?php } ?>
            <th class="theadtr" style="min-width: 10%">Currency</th>
            <th class="theadtr" style="min-width: 15%">Debit </th>
            <th class="theadtr" style="min-width: 15%">Credit </th>
        </tr>
        </thead>
        <tbody>
        <?php
        $dr_total=0; $cr_total =0; $curr = null;
        if (!empty($accReviewData_arr)) {
            foreach ($accReviewData_arr as $key=>$row) {
                if( $row['amount_type'] == 'dr' ){
                    $dr_total += abs($row['amount']);
                    //$dr_total += ($row['amount']);
                }
                else{
                    //$cr_total += $row['amount'] * (-1);
                    $cr_total += abs($row['amount']);
                }
                $currency = $row['transactionCurrency'];
        ?>
            <tr>
                <td style="text-align:right;"><?php echo ($key+1); ?>.&nbsp;</td>
                <td style="text-align:left;"><?php echo $row['systemGLCode']; ?></td>
                <td style="text-align:left;"><?php echo $row['GLCode']; ?></td>
                <td><?php echo $row['GLDescription'];?></td>
                <td style="text-align:center;"><?php echo $row['GLType']; ?></td>
                <td style="text-align:center;"><?php echo $row['segmentCode']; ?></td>
                <?php

                if($payroll_project == 1){
                ?>
                    <td style="text-align:center;"><?php echo ($row['contractCode']) ? $row['contractCode'].'<br>( '.$row['contractRef'].' )' : ''; ?></td>
                <?php } ?>

                <td style="text-align:center;"><?php echo $currency; ?></td>
                <td style="text-align:right;">
                    <?php echo ($row['amount_type'] =='dr' )? format_number( abs($row['amount']), $row['dPlace']) : format_number(0, $row['dPlace']); ?>
                </td>
                <td style="text-align:right;">
                    <?php echo ($row['amount_type'] =='cr' )? format_number( abs($row['amount']), $row['dPlace']) : format_number(0, $row['dPlace']); ?>
                </td>
            </tr>
        <?php
            }
        }else{
            echo '<tr class="danger"><td colspan="'.$colspan.'" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="<?php echo $colspan ?>">Double Entry Total (<?php echo $currency; ?>) </td>
            <td class="text-right total"><?php echo format_number($dr_total,2); ?></td>
            <td class="text-right total"><?php echo format_number($cr_total,2); ?></td>
        </tr>
        </tfoot>
    </table>
</div>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-12-15
 * Time: 5:10 PM
 */