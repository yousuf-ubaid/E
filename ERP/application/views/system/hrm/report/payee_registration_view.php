<?php
$companyData = $this->common_data['company_data'];
?>

<div class="col-md-12" style="margin-top: 2%">
    <div class="text-center"> <strong><?php echo $companyData['company_name']; ?></strong> </div>
    <div class="text-center"> <strong>Form No : PAYE / T-9A Schedule</strong></div>
</div>

<div class="">
    <div class="">PAYE Registration No. <?php echo $regNo; ?></div>
    <div class="">From : <?php echo $fromDate . ' - To ' . $toDate; ?></div>
</div>

<div style="margin: 2% 0% 2% 0%;height: 420px">
    <table class="<?php echo table_class(); ?>" id="payeeRegistrationTB">
        <thead>
            <tr>
                <th rowspan="2">Serial No.of paye  paysheet</th>
                <th rowspan="2">Name of the employee with initials</th>
                <th rowspan="2">Designation</th>
                <th colspan="2">Period of employment during the year of assessment</th>
                <th colspan="3">Gross remuneration as per paysheet</th>
                <th rowspan="2">Tax deducted as per PAYE paysheet and Tax deducted under section 117</th>
                <th rowspan="2">NIC No.</th>
                <th rowspan="2">Income Tax File No.</th>
            </tr>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Cash payment</th>
                <th>Non Cash</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $cashTot = 0;
        $payeTot = 0;
        foreach($payeeData as $key=>$row) {
            $cashTot += round($row['cashBenefit'],2);
            $payeTot += round($row['payee'],2);
        echo '<tr>
                <td align="right">'.($key+1).'</td>
                <td>'.$row['nameWithIn'].'</td>
                <td>'.$row['desgination'].'</td>
                <td>'.$fromDate.'</td>
                <td>'.$toDate.'</td>
                <td align="right">'.number_format($row['cashBenefit'],2).'</td>
                <td></td>
                <td align="right">'.number_format($row['cashBenefit'],2).'</td>
                <td align="right">'.number_format($row['payee'],2).'</td>
                <td>'.$row['NIC'].'</td>
                <td></td>
             </tr>';
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">&nbsp;</td>
                <td align="right"><?php echo number_format($cashTot,2) ?></td>
                <td></td>
                <td align="right"><?php echo number_format($cashTot,2) ?></td>
                <td align="right"><?php echo number_format($payeTot,2) ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $('#payeeRegistrationTB').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 0
    });
</script>

<?php
