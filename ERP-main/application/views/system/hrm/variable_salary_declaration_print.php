<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan_lang', $primaryLanguage);


$isFromApproval = $this->input->post('isFromApproval');
$masterID = $extra['salarydeclarationMasterID'];
$masterData= $this->db->query("SELECT * FROM srp_erp_variable_salarydeclarationmaster WHERE salarydeclarationMasterID={$masterID}")->row_array();

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

$detailTable = $this->db->query("SELECT declarationDetailID AS detID,declarationMasterID,employeeNo,decDet.salaryCategoryID,transactionAmount,
                                md.monthlyDeclaration as description,decDet.salaryCategoryType,effectiveDate,
                                payDate, transactionCurrencyDecimalPlaces AS trDPlace, ECode, Ename2, currentAmount, percentage
                                FROM srp_erp_variable_salarydeclarationdetails AS decDet
                                JOIN srp_employeesdetails ed ON ed.EIdNo = decDet.employeeNo  
                                JOIN srp_erp_pay_salarycategories ON decDet.salaryCategoryID=srp_erp_pay_salarycategories.salaryCategoryID
                                JOIN srp_erp_pay_monthlydeclarationstypes as md ON decDet.monthlyDeclarationID = md.monthlyDeclarationID
                                {$str}
                                WHERE declarationMasterID ={$masterID} ORDER BY employeeNo, srp_erp_pay_salarycategories.salaryDescription")->result_array();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$dPlace = $masterData['transactionCurrencyDecimalPlaces'];
$date_format_policy = date_format_policy();
if($isGroupAccess == 1) {
    if ($totalEntries != count($detailTable)) {
        echo '<script type="text/javascript"> msg_popup("saveBtn"); </script>';
    }
}
$docDate = $masterData['documentDate'];
$docDateStr = convert_date_format($docDate);
$isInitialDeclaration = ($masterData['isInitialDeclaration'] == 1)? 'Yes': 'No';
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
                    <td><h4 style="margin-bottom: 0px"><?php echo $this->lang->line('hrms_loan_variable_salary_declaration')?> </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" id="SD-header-print-tb" style="background-color: #EAF2FA; ">
                <tr>
                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_code_declaration');?><!--Declaration Code--></td>
                    <td class="bgWhite"><strong><?php echo $masterData['documentSystemCode'] ?></strong></td>

                    <td style="width: 80px;"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></td>
                    <td class="bgWhite" colspan="2">
                        <strong><?php echo ($masterData['isPayrollCategory'] == 1)? 'Payroll' : 'Non payroll' ?></strong>
                        <input type="hidden" id="isPayrollCategory_hidden" value="<?php echo $masterData['isPayrollCategory']?>">
                    </td>

                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></td>
                    <td class="bgWhite" colspan="2"> <?php echo $docDateStr; ?> </td>
                </tr>

                <tr>
                    <td ><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                    <td class="bgWhite">
                        <strong><?php echo $masterData['transactionCurrency'];?></strong>
                        <input type="hidden" id="docCurrency" value="<?php echo $masterData['transactionCurrencyID'];?>">
                    </td>

                    <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $masterData['Description'] ?></strong></td>

                    <td><?php echo $this->lang->line('hrms_payroll_initial_declaration');?><!--Initial Declaration--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $isInitialDeclaration ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?> drill-table" >
                <thead>
                <tr>
                    <th> # </th>
                    <th style=""> <?php echo $this->lang->line('common_type')?> </th>
                    <th style=""> <?php echo $this->lang->line('common_category')?>  </th>
                    <th style="width: 105px"><?php echo $this->lang->line('common_effective_date')?> <!--Effective Date--> </th>
                    <th style="width: 105px"> <?php echo $this->lang->line('hrms_loan_pay_date')?>  <!-- Pay Date--> </th>
                    <th style="width: 90px"> <?php echo $this->lang->line('hrms_loan_currentamount')?> <!--Current Amount--> </th>
                    <th class="hide" style="width: 90px"> <!--Increment--><?php echo $this->lang->line('hrms_loan_increment')?> % </th>
                    <th style="width: 100px"><?php echo $this->lang->line('hrms_loan_new_amount')?> <!--New Amount--> </th>
                    <th class="hide" style="width: 100px"> <?php echo $this->lang->line('hrms_loan_increment_amount')?> <!--Increment Amount--> </th>
                    <th class="hide" style="width: 80px"> <?php  echo  $this->lang->line('hrms_loan_balance_amount');?> <!--Balance Amount--> </th>
                </tr>
                </thead>

                <tbody>
                <?php
                $i = 1; $m = 0;
                if(!empty($detailTable)){
                    $detailTable = array_group_by($detailTable, 'employeeNo');

                    foreach ($detailTable as $empID=>$row){
                        $currentAmountTot = 0; $newAmountTot = 0; $incAmountTot = 0; $empBalanceTot = 0;
                        $firstRow = $row[0];
                        $empID = $firstRow['employeeNo']; $empName =  $firstRow['ECode'].' | '.$firstRow['Ename2'];

                        echo '<tr>
                                <td>'.$i.'</td><td colspan="9">'.$empName.'</td>                                
                              </tr>';


                        foreach ($row as $key=>$det){
                            $detID = $det['detID']; $percentage = $det['percentage'];
                            $amount = round($det['transactionAmount'], $dPlace); $incAmountTot += $amount;
                            $currentAmount = round($det['currentAmount'], $dPlace); $currentAmountTot += $currentAmount;
                            $effectiveDate = $det['effectiveDate']; $effectiveDate = convert_date_format($effectiveDate);
                            $payDate = $det['payDate']; $payDate = convert_date_format($payDate);
                            $newAmount = (!empty($amount) && $amount != 0)? ($amount + $currentAmount): 0;
                            $newAmountTot += $newAmount; $newAmountTxt = $newAmount;
                            $empBalance = (array_key_exists($detID, $balancePayment))? $balancePayment[$detID][0]['balanceAmount']: 0;
                            $empBalanceTot += $empBalance;
                            $type = ($det['salaryCategoryType'] == 'A')? 'Addition': 'Deduction';
                            $m++;

                            echo '<tr>
                                    <td >&nbsp;</td>                                       
                                    <td >'.$type.'</td>                                       
                                    <td >'.$det['description'].'</td>
                                    <td style="text-align: center"> '.$effectiveDate.'</td>
                                    <td style="text-align: center"> '.$payDate.'</td>                                     
                                    <td class="right-align">'.number_format($currentAmount, $dPlace).'</td>                                    
                                    <td class="right-align hide">'.$percentage.'</td>
                                    <td class="right-align">'.number_format($newAmountTxt, $dPlace).'</td>
                                    <td class="right-align hide inc_amn_'.$empID.'" id="inc_amn_'.$detID.'">'.number_format($amount, $dPlace).'</td>
                                    <td class="right-align hide balance_amn_'.$empID.'" id="balance_amn_'.$detID.'">'.number_format($empBalance, $dPlace).'</td>                                    
                                  </tr>';
                        }


                        echo '<tr>
                                <td colspan="4" class="total-sd">&nbsp;</td>                                       
                                <td class="total-sd">'.$this->lang->line('common_total').'</td>
                                <td class="right-align total-sd">'.number_format($currentAmountTot, $dPlace).'</td>
                                <td class="right-align hide total-sd"></td>
                                <td class="right-align total-sd" id="new-tot-'.$empID.'">'.number_format($newAmountTot, $dPlace).'</td>
                                <td class="right-align hide total-sd" id="increment-tot-'.$empID.'">'.number_format($incAmountTot, $dPlace).'</td>                                        
                                <td class="right-align hide total-sd" id="balance-tot-'.$empID.'">'.number_format($empBalanceTot, $dPlace).'</td>                                                                       
                              </tr>';

                        $i++;
                    }
                }
                else{
                    $no_record_found = $this->lang->line('common_no_records_found');
                    echo '<tr><td colspan="10" align="center">'.$no_record_found.'</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
