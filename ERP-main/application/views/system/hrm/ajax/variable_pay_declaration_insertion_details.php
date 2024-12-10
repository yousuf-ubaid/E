<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('hrms_loan_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$masterID = $masterData['vpMasterID'];

$confirmed = $masterData['confirmedYN'];

if($isGroupAccess == 1) {
    if ($totalEntries != count($detailRecords)) {
        if ($confirmed != 1) {
            $confirmed = 1;
            echo '<script type="text/javascript"> msg_popup("confirm-btn"); </script>';
        }
    }
}
$docDate = $masterData['documentDate'];
$docDateStr = convert_date_format($docDate);

$dPlace = $masterData['trCurrencyDPlaces'];
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
    <div class="row" style="margin-right: 0px; margin-left: 0px;">
        <div class="col-md-12 well">
            <div class="col-md-4">
                <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                        <td class="bgWhite details-td" id="documentCode" width="200px"><strong><?php echo $masterData['documentCode'] ?></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('hrms_payroll_initial_declaration');?></td>
                        <td class="bgWhite details-td" id="inv_type" width="200px"><strong><?php echo $isInitialDeclaration ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('hrms_payroll_document_date');?></td>
                        <td class="bgWhite details-td" id="documentCode" width="200px"><strong><?php echo $docDateStr ?></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('common_description');?></td>
                        <td class="bgWhite details-td" id="documentCode" width="200px"><strong><?php echo $masterData['description'] ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                        <td class="bgWhite details-td" id="inv_type" width="200px">
                            <strong><?php echo $masterData['trCurr'] ?></strong>
                            <input type="hidden" id="docCurrency" value="<?php echo $masterData['trCurrencyID'];?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="margin: 10px 0px 10px;">
            <h4>
                <?php
                if($confirmed != 1){
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
                        <th style="width: 30px"> # </th>
                        <th style=""> <?php echo $this->lang->line('common_type')?> <!--Type--> </th>
                    <th style=""> <?php echo $this->lang->line('common_category')?> <!--Category--> </th>
                        <th style="width: 110px"> <?php echo $this->lang->line('hrms_loan_currentamount')?> </th>
                        <th style="width: 100px"> <?php echo $this->lang->line('hrms_loan_new_amount')?><!--New Amount--> </th>
                        <th style="width: 105px"><?php echo $this->lang->line('common_effective_date')?><!-- Effective Date--> </th>
                        <th style="width: 100px"> <?php echo $this->lang->line('common_narration')?> </th>
                        <th style="width: 40px">
                            <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_all_item()"></span>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $i = 1; $m = 0;
                    if(!empty($detailRecords)){
                        $detailRecords = array_group_by($detailRecords, 'empID');

                        foreach ($detailRecords as $empID=>$row){
                            $newAmountTot = 0;
                            $firstRow = $row[0];
                            $empID = $firstRow['empID']; $empName =  $firstRow['ECode'].' | '.$firstRow['Ename2'];

                            echo '<tr>
                                    <td class="right-align"><b>'.$i.'</b></td><td colspan="6"><b>'.$empName.'</b></td>
                                    <td style="text-align: center">
                                        <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_employee('.$empID.')"></span>
                                    </td>
                                  </tr>';


                            foreach ($row as $key=>$det){
                                $detID = $det['detailID']; $catID = $det['salaryCategoryID'];
                                $amount = round($det['amount'], $dPlace);
                                $currentAmount = round($det['currentAmount'], $dPlace);
                                $effectiveDate = $det['effectiveDate']; $effectiveDate = convert_date_format($effectiveDate);
                                $newAmount = (!empty($amount) && $amount != 0)? $amount: 0;
                                $newAmountTot += $newAmount; $newAmountTxt = $newAmount;

                                $type = ($det['salaryCategoryType'] == 'A')? 'Addition': 'Deduction';
                                $m++;

                                echo '<tr>
                                        <td >&nbsp;</td>                                       
                                        <td >'.$type.'</td>                                       
                                        <td >'.$det['description'].'</td>
                                        <td class="right-align">'.number_format($currentAmount, $dPlace, '.', '').'</td>                                                                                                                                               
                                        <td class="right-align">
                                            <input type="text" name="" class="right-align amountTxt new_amn_'.$empID.'" value="'.$newAmountTxt.'" 
                                                id="new_amn_'.$detID.'" onchange="inline_update(\'amn\', \''.$detID.'\', \''.$empID.'\', this)" /> 
                                        </td>                                        
                                        <td class="">
                                            <div class="input-group date-pic">
                                                <div class="input-group-addon group-add-on-custom"><i class="fa fa-calendar" style="font-size: 11px; padding: 1px 5px;"></i></div>
                                                <input type="text" class="form-control dateTxt" id="eff_'.$detID.'" value="'.$effectiveDate.'" 
                                                    data-inputmask="\'alias\': \''.$date_format_policy.'\'" onchange="inline_update(\'eff\', \''.$detID.'\', \''.$empID.'\', this)" />
                                            </div>
                                        </td>
                                        <td class="">
                                            <input type="text" name="" class="narration_'.$empID.'" value="'.$det['narration'].'" 
                                                id="narration_'.$detID.'" onchange="inline_update(\'nar\', \''.$detID.'\', \''.$empID.'\', this)" /> 
                                        </td>
                                        <td style="text-align: center">
                                            <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_item('.$detID.')"></span>
                                        </td>
                                      </tr>';
                            }


                            /*echo '<tr>
                                    <td colspan="3" class="total-sd">&nbsp;</td>                                       
                                    <td class="total-sd">Total</td>
                                    <td class="right-align total-sd" id="new-tot-'.$empID.'">'.number_format($newAmountTot, $dPlace).'</td>                                   
                                    <td class="total-sd"></td>                           
                                    <td class="total-sd"></td> 
                                    <td class="total-sd"></td> 
                                  </tr>';*/

                            $i++;
                        }
                    }
                    else{
                        $no_record_found = $this->lang->line('common_no_records_found');
                        echo '<tr><td colspan="8" align="center">'.$no_record_found.'</td></tr>';
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
            if (!empty($detailRecords)) {
                $confirmed = $masterData['confirmedYN'];
                if ($confirmed != 1) { ?>
                    <div id="sdd_footer" style="margin: 16px 0px 1px 0px;" class="pull-right">
                        <button class="btn btn-success submitWizard confirm-btn" onclick="confirm_variablePay()">
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
    var documentDate = '<?php echo convert_date_format($masterData['documentDate']); ?>';
    var isInitialDeclaration = '<?php echo $masterData['isInitialDeclaration']; ?>';
    var docDate = '<?php echo $docDateStr; ?>';
    var dPlace = '<?php echo $dPlace; ?>';
    var maxLine = '<?php echo $m; ?>';
    var disableDate = '<?php echo $disableDate; ?>';


    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.amountTxt').numeric({decimalPlaces: dPlace});

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
                        data: {'masterID':VD_masterID, 'detList':detList, 'percent': per},
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
                                load_variable_pay_declaration_master(VD_masterID);
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

    function inline_update(ty, detID, empID, obj){

        var update_obj = $(obj);
        var updateVal = update_obj.val();
        var column = '';

        if(ty == 'amn'){  //Amount
            updateVal = getNumberAndValidate(updateVal, dPlace);
            update_obj.val(updateVal);
            column = 'amount';
        }
        else if(ty == 'nar'){  //Narration
            column = 'narration';
        }
        else if(ty == 'eff'){  //Effective date
            column = 'effectiveDate';
        }

        $('#balance_amn_'+detID).html('<i class="fa fa-refresh fa-spin" style="font-size:12px"></i>');
        inline_update_ajax(empID, detID, column, updateVal)

    }

    function inline_update_ajax(empID, detID, column, updateVal){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/variable_pay_declaration_inline_update') ?>",
            data: {'masterID':VD_masterID, 'detID':detID, 'empID':empID, 'column':column, 'updateVal':updateVal},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                if( data[0] == 'e' ){
                    bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');

                    if(column == 'effectiveDate'){
                        $('#eff_'+detID).val(data['oldVal']);
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+errorThrown+'</div>');
            }
        });
    }

    function confirm_variablePay() {
        bootbox.confirm("Are you sure want to confirm this variable pay declaration?", function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Employee/confirm_variablePay'); ?>",
                    data: {'masterID': VD_masterID},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/hrm/variable_pay_declaration_master', '', 'HRMS');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            }
        });

    }

    if(isInitialDeclaration == 1){
        $('#effDate_reqMark').hide();
    }
</script>

<?php
