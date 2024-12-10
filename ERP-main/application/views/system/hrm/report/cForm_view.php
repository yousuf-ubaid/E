<?php
$companyData = $this->common_data['company_data'];
$employerCode = $payrollData['znCode'] . ' ' . $payrollData['employerNumber'];
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//echo '<pre>';print_r($epfData); echo '</pre>';

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
            <td style="width:130px;text-align:right; vertical-align: top" colspan="2"><span style="font-size: 50px;font-weight: bold">C</span></td>
            <td>
                <table class="<?php echo table_class(); ?>">
                    <tbody>
                    <tr>
                        <td colspan="2" style="/*width:230px; */vertical-align: middle">
                            <span style="font-size: 14px;font-weight: bold"><?php echo $this->lang->line('hrms_reports_epfact')?><!--Form EPF Act No. 15 of 1958--></span>
                        </td>
                    </tr>
                    <tr>
                        <td>No.of Employees</td>
                        <td style="text-align:right"><?php echo count($epfData); ?></td>
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
                    <td>EPF Registration No</td>
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
                    <td>Surcharges</td>
                    <td style="text-align:right; font-weight:bold"><?php ?></td>
                </tr>
                <tr>
                    <td>Total Remittance</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format(abs($totalContribution), 2); ?></td>
                </tr>
                <tr>
                    <td>Cheque No</td>
                    <td style="text-align:right; font-weight:bold"><?php ?></td>
                </tr>
                <tr>
                    <td>Bank Name and Branch Name</td>
                    <td style="text-align:right; font-weight:bold"><?php ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fixHeader_Div" style="margin-top: 2%; height: 500px">
        <table class="<?php echo table_class(); ?>" id="cFormData" style="margin-bottom: -1px; margin-top: -1px;">
            <thead>
            <tr>
                <th rowspan="2">Employee Name</th>
                <th rowspan="2">NIC Number</th>
                <th rowspan="2">EPF No</th>
                <th colspan="3">Contribution</th>
                <th rowspan="2">Total Earnings</th>
            </tr>
            <tr>
                <th>Total EPF</th>
                <th>EPF 12%</th>
                <th>EPF 8%</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $totalEPF = 0;
            $totalEmployer = 0;
            $totalEmployee = 0;
            $totalEarnings = 0;
            foreach ($epfData as $key => $row) {

                $totalEPF += round($row['toCount'], 2);
                $totalEmployer += round($row['employerCont'], 2);
                $totalEmployee += round($row['memberCont'], 2);
                $totalEarnings += round($row['totEarnings'], 2);

                echo '<tr>
                          <td>' . $row['lastName'] . ' ' . $row['initials'] . '</td>
                          <td>' . $row['nic'] . '</td>
                          <td>' . $employerCode . ' / ' . $row['memNumber'] . '</td>
                          <td style="text-align:right">' . number_format($row['toCount'], 2) . '</td>
                          <td style="text-align:right">' . number_format($row['employerCont'], 2) . '</td>
                          <td style="text-align:right">' . number_format($row['memberCont'], 2) . '</td>
                          <td style="text-align:right">' . number_format($row['totEarnings'], 2) . '</td>
                      </tr>';
            }
            ?>
            </tbody>
            <tfoot> <!--Footer back-ground color defined in c_form page-->
            <?php
                echo '<tr>
                          <td colspan="3">Total</td>
                          <td style="text-align:right">' . number_format($totalEPF, 2) . '</td>
                          <td style="text-align:right">' . number_format($totalEmployer, 2) . '</td>
                          <td style="text-align:right">' . number_format($totalEmployee, 2) . '</td>
                          <td style="text-align:right">' . number_format($totalEarnings, 2) . '</td>
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
