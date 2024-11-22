<?php
$isFromApproval = $this->input->post('isFromApproval');
$masterID = $extra['salarydeclarationMasterID'] ?? '';
$declarationMasterData= $this->db->query("SELECT * FROM srp_erp_salarydeclarationmaster WHERE salarydeclarationMasterID={$masterID}")->row_array();

$str = '';
$isGroupAccess = 0;
if($isFromApproval != 'Y'){
    $isGroupAccess = getPolicyValues('PAC', 'All');
}
if($isGroupAccess == 1){
    $totalEntries = $this->db->query("SELECT COUNT(declarationDetailID) AS totalEntries
                                    FROM srp_erp_salarydeclarationdetails LEFT JOIN srp_erp_pay_salarycategories
                                    ON srp_erp_salarydeclarationdetails.salaryCategoryID=srp_erp_pay_salarycategories.salaryCategoryID
                                    WHERE declarationMasterID ={$masterID} ORDER BY employeeNo")->row('totalEntries');

    $companyID = current_companyID();
    $currentEmp = current_userID();
    $str = "JOIN (
                SELECT groupID FROM srp_erp_payrollgroupincharge
                WHERE companyID={$companyID} AND empID={$currentEmp}
            ) AS accTb ON accTb.groupID = decDet.accessGroupID";
}
$detailTable = $this->db->query("SELECT declarationDetailID,declarationMasterID,employeeNo,decDet.salaryCategoryID,transactionAmount,
                                 srp_erp_pay_salarycategories.salaryDescription as description,decDet.salaryCategoryType,effectiveDate, payDate,
                                 transactionCurrencyDecimalPlaces AS trDPlace
                                 FROM srp_erp_salarydeclarationdetails AS decDet
                                 JOIN srp_erp_pay_salarycategories ON decDet.salaryCategoryID=srp_erp_pay_salarycategories.salaryCategoryID
                                 {$str}
                                 WHERE declarationMasterID = {$masterID} order by employeeNo")->result_array();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


if($isGroupAccess == 1) {
    if ($totalEntries != count($detailTable)) {
        echo '<script type="text/javascript"> msg_popup("saveBtn"); </script>';
    }
}

if(!empty($balancePayment)){
    $balancePayment = array_group_by($balancePayment, 'declarationDetailID');
}
?>
<div class="table-responsive">
    <table style="width: 100%; margin-bottom: 10px" border="0px">
        <tbody>
        <tr>
            <td style="width:40%; height: 80px">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 80px"
                                 src="<?php echo $imgPath.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2">
                            <h2>
                                <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td><h4 style="margin-bottom: 0px">Salary Declaration</td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed " style="background-color: #EAF2FA;" id="SD-header-print-tb">
                <tr>
                    <td style="width: 110px;"><?php echo $this->lang->line('common_code');?><!--Declaration Code--></td>
                    <td class="bgWhite"><strong><?php echo $extra['documentSystemCode'] ?></strong></td>
                    <td colspan="2"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                    <td class="bgWhite"><strong><?php echo $extra['transactionCurrency'] ?></strong></td>
                    <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $extra['Description'] ?></strong></td>
                    <td style="width: 80px;"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></td>
                    <td class="bgWhite" colspan="2">
                        <strong><?php echo ($extra['isPayrollCategory'] == 1)? 'Payroll' : 'Non payroll' ?></strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<hr style="margin: 10px 0px">
<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?> drill-table">
                <tr>
                    <th style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_category_type');?><!--Category Type--></th>
                    <th style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_category');?><!--Category--></th>
                    <th style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_effective_date');?><!--Effective Date--></th>
                    <th style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_pay_date');?><!--Pay Date--></th>
                    <th style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                    <th style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_balance_amount');?><!--Balance Amount--></th>
                </tr>
                <?php
                $newArray = array();
                $empTotal = 0;
                if (!empty($detailTable)) {
                    foreach ($detailTable as $val) {
                        $newArray[$val['employeeNo']][] = $val;
                    }
                    $trDPlace = $val['trDPlace'];
                    foreach ($newArray as $key => $value) {
                        $empTotal = 0;
                        $empname = 0;
                        $totalbalance=0;
                        foreach ($value as $val) {
                            if ($empname == 0) {
                                ?>
                                <tr>
                                    <td colspan="4"><strong>
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
                                <td><?php if ($val['salaryCategoryType'] == 'A') {
                                        echo $this->lang->line('common_addition')/*'Addition'*/;
                                    } else if ($val['salaryCategoryType'] == 'D') {
                                        echo $this->lang->line('common_deduction')/*'Deduction'*/;
                                    }  ?></td>
                                <td><?php echo $val['description']; ?></td>
                                <td><?php echo $val['effectiveDate']; ?></td>
                                <td><?php echo $val['payDate']; ?></td>
                                <td style="text-align: right"><?php
                                    if ($val['salaryCategoryType'] == 'D') {
                                        $amt = '-' . $val['transactionAmount'];
                                        echo  number_format($val['transactionAmount'], $trDPlace);
                                    } else {
                                        $amt = $val['transactionAmount'];
                                        echo number_format($val['transactionAmount'], $trDPlace);
                                    }
                                    ?></td>
                                <td style="text-align: right">
                                    <?php
                                    $empBalance = (array_key_exists($val['declarationDetailID'], $balancePayment))? $balancePayment[$val['declarationDetailID']][0]['balanceAmount']: 0;

                                    $totalbalance += $empBalance;
                                    echo number_format($empBalance, $trDPlace);
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $empTotal += $val['transactionAmount'];
                        }
                        ?>
                        <tr>
                            <td colspan="4" style="background-color: rgba(119, 119, 119, 0.33)"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                            <td class="text-right total"><?php echo number_format($empTotal, $trDPlace); ?></td>
                            <td class="text-right total"><?php echo number_format($totalbalance, $trDPlace); ?></td>
                        </tr>
                        <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center">No records Found</td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
