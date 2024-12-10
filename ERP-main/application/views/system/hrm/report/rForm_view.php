<?php
$companyData = $this->common_data['company_data'];
$employerCode = $payrollData['employerNumber'];
//echo '<pre>';print_r($etfData); echo '</pre>';

$masterDetCont = ($responseType == 'print')? 'style="width:300px; float:right" class="col-sm-5"' : 'class="col-sm-5 pull-right"' ;
?>

    <table style="width: 100%">
        <tbody>
        <tr>
            <td valign="top">
                <table class="<?php echo table_class(); ?>">
                    <tbody>
                    <tr>
                        <td><?php echo $companyData['company_name']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $companyData['company_address1'] . ' ' . $companyData['company_address2']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $companyData['company_city']; ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 130px">
                &nbsp;
            </td>
            <td>
                <table class="<?php echo table_class(); ?>">
                    <tbody>
                    <tr>
                        <td style="width:150px;text-align:right; vertical-align: top" colspan="2"><span style="font-size: 50px;font-weight: bold">R4 Form</span></td>
                    </tr>
                    <tr>
                        <td>Page No.</td>
                        <td style="text-align:right">1</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <div style="height: 1%">&nbsp;</div>

    <div class="row">
        <div <?php echo $masterDetCont; ?>>
            <table class="<?php echo table_class(); ?>">
                <tbody>
                <tr>
                    <td>ETF Registration No</td>
                    <td style="text-align:right; font-weight:bold"><?php echo $employerCode; ?></td>
                </tr>
                <tr>
                    <td>Contribution for the Month of</td>
                    <td style="text-align:right; font-weight:bold"><?php echo $payrollData['contPeriod']; ?></td>
                </tr>
                <tr>
                    <td>Total Contributions</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format(abs($totalContribution), 2); ?></td>
                </tr>
                <tr>
                    <td>Total Remittance</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format(abs($totalContribution), 2); ?></td>
                </tr>
                <tr>
                    <td>No.of Employees</td>
                    <td style="text-align:right"><?php echo count($etfData); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            Bank Name and Branch Name :..........................................................
        </div>
    </div>

    <div class="fixHeader_Div" style="margin-top: 2%; height: 500px">
        <table class="<?php echo table_class(); ?>" id="cFormData" style="margin-bottom: -1px; margin-top: -1px;">
            <thead>
            <tr>
                <th>Employee Name</th>
                <th>NIC Number</th>
                <th>ETF No</th>
                <th>Contribution</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $totalETF = 0;
            $totalEmployer = 0;
            $totalEmployee = 0;
            $totalEarnings = 0;
            foreach ($etfData as $key => $row) {

                $totalETF += round($row['etfContribution'], 2);

                echo '<tr>
                          <td>' . ucwords(strtolower($row['lastName'])) . ' ' . $row['initials'] . '</td>
                          <td>' . $row['nic'] . '</td>
                          <td>' . $employerCode . ' / ' . $row['memNumber'] . '</td>
                          <td style="text-align:right">' . number_format($row['etfContribution'], 2) . '</td>
                      </tr>';
            }
            ?>
            </tbody>
            <tfoot><!--Footer back-ground color defined in r_form page-->
            <?php
                echo '<tr>
                          <td colspan="3">Total</td>
                          <td style="text-align:right">' . number_format($totalETF, 2) . '</td>
                      </tr>';
            ?>
            </tfoot>
        </table>
    </div>

<?php
    if($responseType == 'print'){
?>
    <div style="font-weight: bold; font-size: 10px">I certify that the information given above is correct</div>
    <div style="margin-top: 50px; font-weight: bold; font-size: 10px">
        ------------------------------- <br/>
        Signature of employer
    </div>
<?php
    }
?>

    <script>
        $('#cFormData').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 0
        });
    </script>
<?php
