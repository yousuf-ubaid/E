<div class="col-md-12">
    <div class="text-center">
        <strong>EMPLOYEES TRUST FUND - RETURN FOR THE PERIOD <?php echo strtoupper($period); ?></strong>
    </div>
    <?php
    if($responseType == 'view') {
        echo '<div class="pull-right">TOTAL NO OF EMPLOYEES : '.count($etfData).'</div>';
    } else{
        echo '<div style="width:100%; text-align:right">TOTAL NO OF EMPLOYEES : '.count($etfData).'</div>';
    }
    ?>
</div>


<div class="col-md-12">
    <div class="fixHeader_Div" style="margin: 2% 0% 2% 0%;height: 320px">
        <table class="<?php echo table_class(); ?>" id="etfReturnTB" style="margin-bottom: -1px; margin-top: -1px;">
            <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">MEMBER'S NAME</th>
                <th rowspan="2">NIC No</th>
                <th rowspan="2" style="">MEM'S No</th>
                <th>TOTAL</th>
                <?php
                foreach($payrollMastersData as $row){
                    echo '<th colspan="2">'.$row['payrollMonth'].'</th>';
                }
                ?>
            </tr>
            <tr>
                <th> CONTRIB.</th>
                <?php
                foreach($payrollMastersData as $keyMaster=>$rowData){

                    $payrollMastersData[$keyMaster]['totalEarnings_'.$rowData['payYearMonth']] = 0;
                    $payrollMastersData[$keyMaster]['etfContribution_'.$rowData['payYearMonth']] = 0;

                    echo '<th>Earnings</th>';
                    echo '<th>Contrib.</th>';
                }
                ?>

            </tr>
            </thead>
            <tbody>
            <?php

            $totalContribution = 0;

            foreach ($etfData as $key => $row) {
                $totalContribution += round($row['totalContribution'], 2);
                echo '<tr>
                          <td>' . ($key+1) . '</td>
                          <td>' . $row['initials'] . ' ' . $row['lastName'] . '</td>
                          <td>' . $row['nic'] . '</td>
                          <td>' . $employerNo . '&nbsp;/&nbsp;' . $row['memNumber'] . '</td>
                          <td style="text-align:right">' . number_format($row['totalContribution'], 2) . '</td>';

                foreach($payrollMastersData as $keyAmount=>$rowMaster){
                    $payYearMonth = $rowMaster['payYearMonth'];
                    $totalEarnings_columnName = 'totalEarnings_'.$payYearMonth;
                    $etfContribution_columnName = 'etfContribution_'.$payYearMonth;


                    $payrollMastersData[$keyAmount][$totalEarnings_columnName] += round($row[$totalEarnings_columnName], 2);
                    $payrollMastersData[$keyAmount][$etfContribution_columnName] += round($row[$etfContribution_columnName], 2);

                    echo '<td style="text-align:right">' . number_format($row[$totalEarnings_columnName], 2) . '</td>';
                    echo '<td style="text-align:right">' . number_format($row[$etfContribution_columnName], 2) . '</td>';
                }

                echo  '</tr>';
            }
            ?>
            </tbody>
            <tfoot> <!--Footer back-ground color defined in etf_return page-->
            <tr>
                <td colspan="4" class="total">Total</td>

            <?php
            $isFromPrint = 'N';

            echo '<td class="total t-foot" align="right">'.number_format($totalContribution, 2).'</td>';

            array_walk($payrollMastersData, function(&$value, $i) use ($isFromPrint){
                $payYearMonth = $value['payYearMonth'];
                $totalEarnings_columnName = 'totalEarnings_'.$payYearMonth;
                $etfContribution_columnName = 'etfContribution_'.$payYearMonth;

                echo '<td class="total t-foot" align="right">'.format_number($value[$totalEarnings_columnName], 2).'</td>';
                echo '<td class="total t-foot" align="right">'.format_number($value[$etfContribution_columnName], 2).'</td>';

            });
            ?>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$companyData = $this->common_data['company_data'];
if($responseType == 'view') {
?>
    <div class="col-md-12">
        <div class="row" style="width: 100%">
            <div class="col-md-12">
                Employer's Registration No - <?php echo $employerNo; ?>
            </div>
            <div class="col-md-12">
                <!--Reason of using (&nbsp;) for gaps to resolve alignment issue in export excel-->
                Name of Employer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -
                <?php echo $companyData['company_name']; ?>
            </div>
            <div class="col-md-12">
                Address of Employer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -
                <?php echo $companyData['company_address1'] . ' ' . $companyData['company_address2'] . ', ' . $companyData['company_city']; ?>
            </div>
        </div>
    </div>



<?php
}
else{
?>
<table>
    <tbody>
    <tr>
        <td>Employer's Registration No</td>
        <td>: <?php echo $employerNo; ?></td>
    </tr>
    <tr>
        <td>Name of Employer</td>
        <td> : <?php echo $companyData['company_name']; ?> </td>
    </tr>
    <tr>
        <td>Address of Employer</td>
        <td>: <?php echo $companyData['company_address1'] . ' ' . $companyData['company_address2'] . ', ' . $companyData['company_city']; ?></td>
    </tr>
    </tbody>
</table>
<?php
}
?>

<!--<div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">Employer's Registration No</div>
                <div class="col-md-9"><?php echo $employerNo; ?></div>
            </div>
            <div class="row">
                <div class="col-md-3">Name of Employer</div>
                <div class="col-md-9">
                    <?php
$companyData = $this->common_data['company_data'];
echo $companyData['company_name'];
?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">Address of Employer</div>
                <div
                    class="col-md-9"><?php echo $companyData['company_address1'] . ' ' . $companyData['company_address2'] . ', ' . $companyData['company_city']; ?></div>
            </div>
        </div>
    </div>-->

<script>
    $('#etfReturnTB').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 0
    });
</script>

<?php
