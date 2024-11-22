<?php
$declarationdetailTable = $this->db->query("SELECT feDeclarationDetailID,feDeclarationMasterID,employeeNo,srp_erp_ot_fixedelementdeclarationdetails.fixedElementID,transactionAmount,effectiveDate,srp_erp_ot_fixedelements.fixedElementDescription
FROM srp_erp_ot_fixedelementdeclarationdetails LEFT JOIN srp_erp_ot_fixedelements ON srp_erp_ot_fixedelements.fixedElementID = srp_erp_ot_fixedelementdeclarationdetails.fixedElementID WHERE feDeclarationMasterID = " . $extra['fedeclarationMasterID'] . " ORDER BY employeeNo")->result_array();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


?>
<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                <tr>
                    <td style="width: 110px;">
                        <?php echo $this->lang->line('common_code'); ?><!--Declaration Code--></td>
                    <td class="bgWhite"><strong><?php echo $extra['documentSystemCode'] ?></strong></td>
                    <td colspan="2"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></td>
                    <td class="bgWhite"><strong><?php echo $extra['transactionCurrency'] ?></strong></td>
                    <td><?php echo $this->lang->line('common_description'); ?><!--Description--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $extra['Description'] ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<hr>
<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td style="font-weight: 700; text-align: center">
                        <?php echo $this->lang->line('hrms_payroll_category_type'); ?><!--Category Type--></td>
                    <td style="font-weight: 700; text-align: center">
                        <?php echo $this->lang->line('hrms_payroll_effective_date'); ?><!--Effective Date--></td>
                    <td style="font-weight: 700; text-align: center">
                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--></td>
                </tr>
                <?php
                $newArray = array();
                $empTotal = 0;
                if (!empty($declarationdetailTable)) {
                    foreach ($declarationdetailTable as $val) {
                        $newArray[$val['employeeNo']][] = $val;
                    }
                    foreach ($newArray as $key => $value) {
                        $empTotal = 0;
                        $empname = 0;
                        $totalbalance = 0;
                        foreach ($value as $val) {
                            if ($empname == 0) {
                                ?>
                                <tr>
                                    <td colspan="3"><strong>
                                            <?php
                                            $empid = fetch_employeeNo($val['employeeNo']);
                                            echo $empid['ECode'] . '-' . $empid['Ename2'];
                                            $empname++;
                                            ?></strong></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td><?php echo $val['fixedElementDescription']; ?></td>
                                <td><?php echo $val['effectiveDate']; ?></td>
                                <td style="text-align: right"><?php
                                    $amt = $val['transactionAmount'];
                                    echo number_format($val['transactionAmount'], 2);
                                    ?></td>
                            </tr>
                            <?php
                            $empTotal += $val['transactionAmount'];
                        }
                        ?>
                        <tr>
                            <td colspan="2" style="background-color: rgba(119, 119, 119, 0.33)">Total</td>
                            <td class="text-right total"><?php echo number_format($empTotal, 2); ?></td>
                        </tr>
                        <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center"><?php echo $this->lang->line('common_no_records_found'); ?><!--No records Found--></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
