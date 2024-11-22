
<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$masterID = $output['salarydeclarationMasterID'];
$declarationMasterData= $this->db->query("SELECT * FROM srp_erp_salarydeclarationmaster WHERE salarydeclarationMasterID={$masterID}")->row_array();

$str = '';
$isGroupAccess = getPolicyValues('PAC', 'All');
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
$confirmed = $declarationMasterData['confirmedYN'];
$detailTable = $this->db->query("SELECT declarationDetailID,declarationMasterID,employeeNo,decDet.salaryCategoryID,transactionAmount,
                                srp_erp_pay_salarycategories.salaryDescription as description,decDet.salaryCategoryType,effectiveDate,
                                payDate, transactionCurrencyDecimalPlaces AS trDPlace
                                FROM srp_erp_salarydeclarationdetails AS decDet
                                JOIN srp_erp_pay_salarycategories ON decDet.salaryCategoryID=srp_erp_pay_salarycategories.salaryCategoryID
                                {$str}
                                WHERE declarationMasterID ={$masterID} ORDER BY employeeNo")->result_array();

if($isGroupAccess == 1) {
    if ($totalEntries != count($detailTable)) {
        if ($confirmed != 1) {
            $confirmed = 1;
            echo '<script type="text/javascript"> msg_popup("confirm-btn"); </script>';
        }
    }
}
?>

<style>
    .declarationTable td:not(:first-child) {
        width: 100px !important;
    }

    .declarationTable th:not(:first-child) {
        width: 100px !important;
    }

    .declarationTable tbody td:not(:first-child):not(:last-child):hover {
        cursor: pointer !important;
        background-color: #DEDEDE;
    }

     .drill-table {
         text-align: center;
         background-color: #dedede;
         padding-top: 8px;
         padding-bottom: 8px;
         line-height: 1.42857143;
         font-size: 12px !important;
         font-weight: bold;
     }
</style>

<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                <tr>
                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_code_declaration');?><!--Declaration Code--></td>
                    <td class="bgWhite"><strong><?php echo $output['documentSystemCode'] ?></strong></td>
                    <td colspan="2"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                    <td class="bgWhite"><strong><?php echo $output['transactionCurrency'];?></strong></td>
                    <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $output['Description'] ?></strong></td>
                    <td style="width: 80px;"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></td>
                    <td class="bgWhite" colspan="2">
                        <strong><?php echo ($output['isPayrollCategory'] == 1)? 'Payroll' : 'Non payroll' ?></strong>
                        <input type="hidden" id="isPayrollCategory_hidden" value="<?php echo $output['isPayrollCategory']?>">
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br>

<h4>
    <?php echo $this->lang->line('hrms_payroll_salary_declaration_detail');?><!--  Salary Declaration Detail-->
    <?php
    if($confirmed != 1){
        echo '<button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_salaryDeclarationModal()">
                <i class="fa fa-plus"></i>'.$this->lang->line('common_add_detail').'
              </button>';
    }
    ?>

</h4>
<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_type');?><!--Type--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_category');?><!--Category--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_effective_date');?><!--Effective Date--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_pay_date');?><!--Pay Date--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_amount');?><!--Amount--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_balance_amount');?><!--Balance Amount--></td>
                    <?php if($confirmed != 1){?>
                    <td style="font-weight: 700; text-align: center"></td>
                    <?php } ?>
                </tr>
                <?php
                $newArray = array();
                $empTotal = 0;
                $totalbalance=0;
                $trDPlace = 2;
                $n = 0;
                if (!empty($detailTable)) {
                    foreach ($detailTable as $val) {
                        $newArray[$val['employeeNo']][] = $val;
                    }
                    $trDPlace = $val['trDPlace'];
                    foreach ($newArray as $key => $value) {
                        $n++;
                        $empTotal = 0;
                        $empname = 0;
                        $totalbalance=0;
                        foreach ($value as $val) {
                            if ($empname == 0) {
                                ?>
                                <tr>
                                    <td colspan="5"><strong>
                                            <?php

                                            $empid = fetch_employeeNo($val['employeeNo']);
                                            echo '[ '.$n.' ] '.$empid['ECode'] . '-' . $empid['Ename2'];
                                            $empname++;
                                            ?></strong></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td><?php if ($val['salaryCategoryType'] == 'A') {
                                        echo 'Addition';
                                    } else if ($val['salaryCategoryType'] == 'D') {
                                        echo 'Deduction';
                                    } ?></td>
                                <td><?php echo $val['description']; ?></td>
                                <td><?php  $convertFormat=convert_date_format(); echo format_date($val['effectiveDate'],$convertFormat) ; ?></td>
                                <td><?php  $convertFormat=convert_date_format(); echo format_date($val['payDate'],$convertFormat) ; ?></td>
                                <td style="text-align: right"><?php
                                    if ($val['salaryCategoryType'] == 'D') {
                                        $amt = '-' . $val['transactionAmount'];
                                        echo number_format($val['transactionAmount'], $trDPlace);
                                    } else {
                                        $amt = $val['transactionAmount'];
                                        echo number_format($val['transactionAmount'], $trDPlace);
                                    }
                                    ?></td>
                                <td style="text-align: right"><?php

                                    $keys = array_keys(array_column($balancePayment, 'declarationDetailID'), $val['declarationDetailID']);
                                    $new_array = array_map(function($k) use ($balancePayment){return $balancePayment[$k]; }, $keys);

                                    if(!empty($new_array)){
                                        $totalbalance += $new_array[0]['balanceAmount'];
                                        echo number_format($new_array[0]['balanceAmount'], $trDPlace);
                                    }


                                    ?></td>
                                <?php if($confirmed != 1){?>
                                <td style="text-align: right"><a
                                        onclick="delete_item(<?php echo $val['declarationDetailID']; ?>,<?php echo $val['declarationMasterID']; ?>);">
                                        <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php
                            $empTotal += $val['transactionAmount'];
                        }
                        ?>
                        <tr>
                            <td colspan="4" style="background-color: rgba(119, 119, 119, 0.33)">Total</td>
                            <td class="text-right total"><?php echo number_format($empTotal, $trDPlace); ?></td>
                            <td class="text-right total"><?php echo number_format($totalbalance, $trDPlace); ?></td>
                            <?php if($confirmed != 1){?>
                            <td colspan="2" style="background-color: rgba(119, 119, 119, 0.33"></td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?><!--No records Found--></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <?php
            $conmed =  $this->lang->line('common_confirmed');
            if (!empty($detailTable)) {
                $confirmed = $declarationMasterData['confirmedYN'];
                if ($confirmed != 1) { ?>
                    <div id="sdd_footer" style="margin: 16px 0px 1px 0px;" class="pull-right">
                        <button class="btn btn-success submitWizard confirm-btn" onclick="confirmSalaryDeclaration()">
                            <?php echo $this->lang->line('common_confirm');?><!--Confirm-->
                        </button>
                    </div>
                <?php }
                else {
                    if ($declarationMasterData['confirmedYN'] == 1 && $declarationMasterData['approvedYN'] == 1) {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i> '.$conmed.'<!--Confirmed--> &nbsp;&nbsp;&nbsp;&nbsp; &amp; &nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-check"></i> Approved </div>  ';
                    } else {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i> '.$conmed.'<!--Confirmed--></div>  ';
                    }
                }
            } ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    var isPayrollCategoryYN = '<?php echo $declarationMasterData['isPayrollCategory']; ?>';
    var documentDate = '<?php echo convert_date_format($declarationMasterData['documentDate']); ?>';
    var isInitialDeclaration = '<?php echo $declarationMasterData['isInitialDeclaration']; ?>';

    $('#MasterCurrency').val('<?php echo $output['transactionCurrencyID'];?>');

    //$('#salaryType').attr('onChange', 'getSubCategory(this, ' + isPayrollCategoryYN + ')');



    function open_salaryDeclarationModal() {
        getDrilldownTableData();
        $("#employee").prop("disabled", false).val(null).trigger("change");
        $('#declaration_save_detail_form').bootstrapValidator('resetForm', true);
        $("#amount").val('');
        $("#effectiveDate").val(documentDate).attr('data-value', documentDate);
        $("#payDate").val(documentDate);
        $('#declaration_save_detail_form').bootstrapValidator('resetField', 'effectiveDate');
        $("#declarationDetailModal").modal({backdrop: "static"});
    }

    function getEffectiveDate(){

        $("#salarySubCatID").val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
        $('#declaration_save_detail_form').bootstrapValidator('resetField', 'cat[]');

        $(".salaryType, #currentAmount, #newAmount, #amount, #empJoinDate").val('');
        var empJoinDate = $('#employee :selected').attr('data-value');
        if(isInitialDeclaration == 1){
            $("#effectiveDate").val( empJoinDate );
        }
        $('#empJoinDate').val( empJoinDate );
    }

</script>