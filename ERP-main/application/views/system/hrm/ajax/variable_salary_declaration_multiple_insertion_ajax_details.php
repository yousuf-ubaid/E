<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$masterID = $output['salarydeclarationMasterID'];
$masterData= $this->db->query("SELECT * FROM srp_erp_variable_salarydeclarationmaster WHERE salarydeclarationMasterID={$masterID}")->row_array();

$str = '';
$isGroupAccess = getPolicyValues('PAC', 'All');
if($isGroupAccess == 1){
    $totalEntries = $this->db->query("SELECT COUNT(declarationDetailID) AS totalEntries
                                    FROM srp_erp_variable_salarydeclarationdetails 
                                    LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_salarydeclarationdetails.salaryCategoryID=srp_erp_pay_salarycategories.salaryCategoryID
                                    WHERE declarationMasterID ={$masterID} ORDER BY employeeNo")->row('totalEntries');

    $companyID = current_companyID();
    $currentEmp = current_userID();
    $str = "JOIN (
                SELECT groupID FROM srp_erp_payrollgroupincharge
                WHERE companyID={$companyID} AND empID={$currentEmp}
            ) AS accTb ON accTb.groupID = decDet.accessGroupID";
}
$confirmed = $masterData['confirmedYN'];

$detailTable = $this->db->query("SELECT declarationDetailID AS detID,declarationMasterID,employeeNo,decDet.salaryCategoryID,transactionAmount,
                                md.monthlyDeclaration as description,decDet.salaryCategoryType,effectiveDate,
                                payDate, transactionCurrencyDecimalPlaces AS trDPlace, ECode, Ename2, currentAmount, percentage
                                FROM srp_erp_variable_salarydeclarationdetails AS decDet
                                JOIN srp_employeesdetails ed ON ed.EIdNo = decDet.employeeNo  
                                JOIN srp_erp_pay_salarycategories ON decDet.salaryCategoryID=srp_erp_pay_salarycategories.salaryCategoryID
                                JOIN srp_erp_pay_monthlydeclarationstypes as md ON decDet.monthlyDeclarationID = md.monthlyDeclarationID
                                {$str}
                                WHERE declarationMasterID ={$masterID} ORDER BY employeeNo, srp_erp_pay_salarycategories.salaryDescription")->result_array();

if($isGroupAccess == 1) {
    if ($totalEntries != count($detailTable)) {
        if ($confirmed != 1) {
            $confirmed = 1;
            echo '<script type="text/javascript"> msg_popup("confirm-btn"); </script>';
        }
    }
}
$docDate = $output['documentDate'];
$docDateStr = convert_date_format($docDate);
//echo '<pre>'; print_r($balancePayment); echo '</pre>';

$dPlace = $masterData['transactionCurrencyDecimalPlaces'];
$disableDate = $isInitialDeclaration = $masterData['isInitialDeclaration'];
$isInitialDeclaration = ($masterData['isInitialDeclaration'] == 1)? 'Yes': 'No';
$date_format_policy = date_format_policy();

?>

<style>
    .drill-table tbody tr:hover {
        cursor: pointer !important;
        background-color: #e2e4d5;
        font-weight: bold;
    }

    .right-align{ text-align: right; }

    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }

    .percentage{ width: 55px; height: 22px; }

    .amountTxt{ width: 100px }

    .group-add-on-custom{ height: 22px !important; padding: 1px }

    .dateTxt{ font-size: 11px; height: 22px }
</style>

<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA; ">
                <tr>
                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_code_declaration');?><!--Declaration Code--></td>
                    <td class="bgWhite" ><strong><?php echo $output['documentSystemCode'] ?></strong></td>

                    <td style="width: 80px;"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></td>
                    <td class="bgWhite" colspan="2">
                        <strong><?php echo ($output['isPayrollCategory'] == 1)? 'Payroll' : 'Non payroll' ?></strong>
                        <input type="hidden" id="isPayrollCategory_hidden" value="<?php echo $output['isPayrollCategory']?>">
                    </td>

                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></td>
                    <td class="bgWhite" colspan="2"> <?php echo $docDateStr; ?> </td>

                    <?php
                    $des_cols = 2;
                    if($output['payrollGroup'] > 0) {
                        $des_cols = 4;
                        ?>
                        <td ><?php echo $this->lang->line('common_payroll_group');?></td>
                        <td class="bgWhite"><strong><?=$output['payrollGroupStr'];?></strong></td>
                    <?php } ?>
                </tr>

                <tr>
                    <td><?php echo $this->lang->line('hrms_payroll_initial_declaration');?><!--Initial Declaration--></td>
                    <td class="bgWhite"><strong><?=$isInitialDeclaration ?></strong></td>

                    <td ><?php echo $this->lang->line('common_currency');?></td>
                    <td class="bgWhite" colspan="2">
                        <strong><?php echo $output['transactionCurrency'];?></strong>
                        <input type="hidden" id="docCurrency" value="<?php echo $output['transactionCurrencyID'];?>">
                    </td>

                    <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                    <td class="bgWhite" colspan="<?=$des_cols?>"><strong><?php echo $output['Description'] ?></strong></td>
                </tr>
            </table>
        </div>

        <div class="col-md-12" style="margin: 10px 0px 10px;">
            <h4>
                <?php
                if($confirmed != 1){
                    echo '<button type="button" class="btn btn-success btn-sm pull-right" onclick="openDownloadTemplate_modal()">
                            <i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp; '.$this->lang->line('hrms_payroll_excel_download').' 
                          </button>';

                    echo '<button type="button" class="btn btn-success btn-sm pull-right" onclick="open_uploadModal()" style="margin-right: 10px;">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp; '.$this->lang->line('hrms_payroll_excel_upload').' 
                          </button>';

                    echo '<button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_bulkDetailsModal()" style="margin-right: 10px;">
                            <i class="fa fa-plus"></i>'.$this->lang->line('hrms_payroll_add_employee').'
                          </button>';
                }
                ?>
            </h4>
            <div class="" style="color: red;">
                <?php echo $this->lang->line('common_note');?><!--Note--> : <?php echo $this->lang->line('hrms_payroll_deduction_amount_should_be_entered_with_a');?> ( - )
            </div>
        </div>
        <br>

        <div class="col-md-12">
            <div class="table-responsive">
            <table class="<?php echo table_class() ?> drill-table" >
                <thead>
                <tr>
                    <th> # </th>
                    <th style=""> Type </th>
                    <th style=""> Category </th>
                    <th style="width: 105px"> Effective Date </th>
                    <!-- <th style="width: 105px"> Pay Date </th> -->
                    <th style="width: 90px"> Current Amount </th>
                    <th class="hide" style="width: 90px"> Increment % </th>
                    <th style="width: 100px"> New Amount </th>
                    <th class="hide" style="width: 100px"> Adjustment Amount </th>
                    <th class="hide" style="width: 80px"> Balance Amount </th>
                    <th>
                        <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_all_item()"></span>
                    </th>
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
                                <td style="text-align: center">
                                    <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_employee('.$empID.')"></span>
                                </td>
                              </tr>';


                        foreach ($row as $key=>$det){
                            $detID = $det['detID']; $percentage = $det['percentage']; $catID = $det['salaryCategoryID'];
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

                            //     <td class="">
                            //     <div class="input-group date-pic">
                            //         <div class="input-group-addon group-add-on-custom"><i class="fa fa-calendar" style="font-size: 11px; padding: 1px 5px;"></i></div>
                            //         <input type="text" class="form-control dateTxt" id="pay_'.$detID.'" value="'.$payDate.'" 
                            //             data-inputmask="\'alias\': \''.$date_format_policy.'\'" onchange="dateChange(\''.$empID.'\', '.$detID.', \'pay\', this)" />
                            //     </div>
                            // </td>

                            echo '<tr>
                                    <td >&nbsp;</td>                                       
                                    <td >'.$type.'</td>                                       
                                    <td >'.$det['description'].'</td>
                                    <td class="">
                                    <div class="input-group date-pic">
                                        <div class="input-group-addon group-add-on-custom"><i class="fa fa-calendar" style="font-size: 11px; padding: 1px 5px;"></i></div>
                                        <input type="text" class="form-control dateTxt" id="eff_'.$detID.'" value="'.$effectiveDate.'" 
                                            data-inputmask="\'alias\': \''.$date_format_policy.'\'" onchange="dateChange(\''.$empID.'\', '.$detID.', \'eff\', this)" />
                                    </div>
                                    </td>
                                  
                                    <td class="right-align">'.number_format($currentAmount, $dPlace, '.', ',').'</td>
                                    <td class="right-align hide">
                                        <div class="input-group">
                                            <div class="input-group-addon group-add-on-custom" onclick="apply_to_all('.$catID.','.$m.')">
                                                <i class="fa fa-arrow-circle-down" style="font-size: 11px; padding: 1px 5px;"></i>
                                            </div>
                                            <input type="text" name="" class="right-align percentage line-'.$catID.'-'.$m.'" value="'.$percentage.'" id="per_'.$detID.'" 
                                              data-id="'.$detID.'" onchange="inline_update(\''.$currentAmount.'\',\'per\', \''.$detID.'\', \''.$empID.'\')"  /> 
                                        </div>                                        
                                    </td>
                                    <td class="right-align">
                                        <input type="text" name="" class="right-align amountTxt new_amn_'.$empID.'" value="'.$newAmountTxt.'" 
                                            id="new_amn_'.$detID.'" onchange="inline_update(\''.$currentAmount.'\',\'amn\', \''.$detID.'\', \''.$empID.'\')" /> 
                                    </td>
                                    <td class="right-align hide inc_amn_'.$empID.'" id="inc_amn_'.$detID.'">'.number_format($amount, $dPlace, '.', '').'</td>
                                    <td class="right-align hide balance_amn_'.$empID.'" id="balance_amn_'.$detID.'">'.number_format($empBalance, $dPlace, '.', '').'</td>
                                    <td style="text-align: center">
                                        <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_item('.$detID.')"></span>
                                    </td>
                                  </tr>';
                        }


                        echo '<tr>
                                <td colspan="3" class="total-sd">&nbsp;</td>                                       
                                <td class="total-sd">Total</td>
                                <td class="right-align total-sd">'.number_format($currentAmountTot, $dPlace).'</td>
                                <td class="right-align total-sd" id="new-tot-'.$empID.'">'.number_format($newAmountTot, $dPlace).'</td>
                            
                                <td class="right-align hide total-sd" id="increment-tot-'.$empID.'">'.number_format($incAmountTot, $dPlace).'</td>                                        
                                <td class="right-align hide total-sd" id="balance-tot-'.$empID.'">'.number_format($empBalanceTot, $dPlace).'</td>                                        
                                <td></td>
                              </tr>';

                        $i++;
                    }
                }
                else{
                    $no_record_found = $this->lang->line('common_no_records_found');
                    echo '<tr><td colspan="11" align="center">'.$no_record_found.'</td></tr>';
                }
                ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <?php
            $confirmed_str =  $this->lang->line('common_confirmed');
            if (!empty($detailTable)) {
                $confirmed = $masterData['confirmedYN'];
                if ($confirmed != 1) { ?>
                    <div id="sdd_footer" style="margin: 16px 0px 1px 0px;" class="pull-right">
                        <button class="btn btn-success submitWizard confirm-btn" onclick="confirmSalaryDeclaration()">
                            <?php echo $this->lang->line('common_confirm');?><!--Confirm-->
                        </button>
                    </div>
                <?php }
                else {
                    if ($masterData['confirmedYN'] == 1 && $masterData['approvedYN'] == 1) {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i> '.$confirmed_str.'<!--Confirmed--> &nbsp;&nbsp;&nbsp;&nbsp; &amp; &nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-check"></i> Approved </div>  ';
                    } else {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i> '.$confirmed_str.'<!--Confirmed--></div>  ';
                    }
                }
            } ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    var isPayrollCategoryYN = '<?php echo $masterData['isPayrollCategory']; ?>';
    var documentDate = '<?php echo convert_date_format($masterData['documentDate']); ?>';
    var isInitialDeclaration = '<?php echo $masterData['isInitialDeclaration']; ?>';
    var docDate = '<?php echo $docDateStr; ?>';
    var dPlace = '<?php echo $dPlace; ?>';
    var maxLine = '<?php echo $m; ?>';
    var disableDate = '<?php echo $disableDate; ?>';

    payroll_group = '<?=$masterData['payrollGroup']; ?>'; /*Declared in hrm/ajax/salary_declaration_multiple_insertion_ajax_details */
    is_period_base_process = (payroll_group > 0); /*Declared in hrm/ajax/salary_declaration_multiple_insertion_ajax_details */


    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.amountTxt').numeric();

    $('.amountTxt, .percentage').bind("cut copy paste",function(e) {
        e.preventDefault();
    });

    function apply_to_all(catID, id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "You want to apply this salary category increment for below employees",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var per = $('.line-'+catID+'-'+id).val();
                per = getNumberAndValidate(per, 0);
                id++;

                var detList = [];
                while(id <= maxLine){
                    var thisLineID = $('.line-'+catID+'-'+id).attr('data-id');

                    if(thisLineID != undefined){
                        detList.push( thisLineID );
                    }
                    id++;
                }

                detList = detList.toString();

                if(detList !== ''){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('Employee/salaryDeclaration_apply_percentage') ?>",
                        data: {'masterID':SD_masterID, 'detList':detList, 'percent': per,'isVariable':1},
                        dataType: "json",
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if( data[0] == 'e' ){
                                bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');
                            }else {
                                load_SalaryDeclarationMaster(SD_masterID);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+errorThrown+'</div>');
                        }
                    });
                }
            }
        );
    }

    function inline_update(curAmount, ty, detID, empID){
        curAmount = getNumberAndValidate(curAmount, dPlace);

        var per_obj = $('#per_'+detID);
        var newAmount_obj = $('#new_amn_'+detID);
        var inc_amount = 0;

        if(ty == 'per'){ //percentage
            var per = per_obj.val();
            per = getNumberAndValidate(per, 2);
            inc_amount = curAmount * (per/100);
            inc_amount = getNumberAndValidate(inc_amount, dPlace);

            $('#inc_amn_'+detID).text(inc_amount);
            newAmount_obj.val( (curAmount + inc_amount) );
            per_obj.val(per);
        }
        else{ //Amount
            per_obj.val(0);
            var newAmount = newAmount_obj.val();
            newAmount = getNumberAndValidate(newAmount, dPlace);
            inc_amount = newAmount - curAmount ;
            inc_amount = getNumberAndValidate(inc_amount, dPlace);

            $('#inc_amn_'+detID).text(inc_amount);
            newAmount_obj.val(newAmount);

        }

        var newTotalAmount = 0;
        $('.new_amn_'+empID).each(function(){
            var val = $(this).val();
            newTotalAmount += getNumberAndValidate(val, dPlace);
        });
        newTotalAmount = commaSeparateNumber(newTotalAmount, dPlace);
        $('#new-tot-'+empID).text(newTotalAmount);


        var incrementTotalAmount = 0;
        $('.inc_amn_'+empID).each(function(){
            var val = $(this).text();
            incrementTotalAmount += getNumberAndValidate(val, dPlace);
        });
        incrementTotalAmount = commaSeparateNumber(incrementTotalAmount, dPlace);
        $('#increment-tot-'+empID).text(incrementTotalAmount);

        per = per_obj.val();

        $('#balance_amn_'+detID).html('<i class="fa fa-refresh fa-spin" style="font-size:12px"></i>');

        inline_update_ajax(empID, detID, inc_amount, per)
    }

    function inline_update_ajax(empID, detID, incrementAmount, percentage){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/salaryDeclaration_inline_update') ?>",
            data: {'empID':empID, 'masterID':SD_masterID, 'detID':detID, 'incrementAmount':incrementAmount, 'percentage':percentage,'isVariable':1},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                if( data[0] == 'e' ){
                    bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');
                }else {
                    $('#balance_amn_'+detID).text(data['balanceAmount']);
                    calculateBalanceTot(empID, detID);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+errorThrown+'</div>');
            }
        });
    }

    function dateChange(empID, detID, ty, obj){
        var selectedDate = $(obj).val();
        $('#balance_amn_'+detID).html('<i class="fa fa-refresh fa-spin" style="font-size:12px"></i>');

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/salaryDeclaration_inline_date_update') ?>",
            data: {'masterID':SD_masterID, 'detID':detID, 'dateOf':ty, 'selectedDate': selectedDate, 'isVariable':'1'},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                if( data[0] == 'e' ){

                    if(ty == 'pay'){
                        $('#pay_'+detID).val(data['oldVal']);
                    }else{
                        $('#eff_'+detID).val(data['oldVal']);
                    }

                    $('#balance_amn_'+detID).text(data['old_balanceVal']);

                    bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');
                }else {
                    $('#balance_amn_'+detID).text(data['balanceAmount']);
                    calculateBalanceTot(empID, detID);

                    myAlert('s', 'Successfully Updated');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#balance_amn_'+detID).html('');
                bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+errorThrown+'</div>');
            }
        });
    }

    function calculateBalanceTot(empID, detID){
        var total = 0;
        $('.balance_amn_'+empID).each(function(){
            var val = $(this).text();
            total += getNumberAndValidate(val, dPlace);
        });
        total = commaSeparateNumber(total, dPlace);
        $('#balance-tot-'+empID).text(total);

    }

    $(document).on('keypress', '.percentage',function (event) {
        var amount = $(this).val();
        if(amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
    });

    function openDownloadTemplate_modal(){
        $("#down_salaryID").multiselect2('selectAll', false);
        $("#down_salaryID").multiselect2('updateButtonText');
        segmentDrop.multiselect2('selectAll', false);
        segmentDrop.multiselect2('updateButtonText');
        loadEmployees();
        $('#downloadTemplate_modal').modal('show');
    }

    function open_uploadModal(){
        $('#excelUpload_Modal').modal('show');
        $('#employeeUpload_form')[0].reset();
        $('#up_remove_id').click();
        $('#upload-msg-div').hide();
    }

    function excel_upload(){
        var formData = new FormData($("#employeeUpload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/salary_declaration_excelUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
                $('#upload-msg-div').hide();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 's' || data[0] == 'e') {
                    myAlert(data[0], data[1]);
                }

                if (data[0] == 'm') {
                    $('#excelUploadMsg_Modal').modal('show');
                    $('#upload-msg-div').show().html(data[1]);

                }

                if (data[0] == 's') {
                    $('#excelUpload_Modal').modal('hide');

                    setTimeout(function(){
                        load_SalaryDeclarationMaster(SD_masterID);
                    }, 300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function downloadTemplate(){

        if($('#down_salaryID').val() == null){
            bootbox.alert('<div class="alert alert-danger" style="margin-top: 20px;">Please select at least one category to proceed.</div>');
            return false;
        }

        var form= document.getElementById('downloadTemplate_form');
        form.target='_blank';
        form.action='<?php echo site_url('Employee/salary_declaration_download_csv'); ?>';
        form.submit();
    }
</script>
<?php
