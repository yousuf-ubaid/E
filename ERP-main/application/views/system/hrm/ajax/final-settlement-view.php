<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
$title = $this->lang->line('hrms_final_settlement_title');
echo head_page($title, false);
$masterID = $this->input->post('page_id');

$fn_data = final_settlement_data($masterID);
$masterData = $fn_data['masterData'];
$isConfirmed = $masterData['confirmedYN'];
$isApproved = $masterData['approvedYN'];
$paymentVoucherID = $masterData['paymentVoucherID'];
$payrollSal = $fn_data['payroll'];
$non_payrollSal = $fn_data['non_payroll'];
$docDate = convert_date_format($masterData['createdDateTime']);
$dateJoin = convert_date_format($masterData['dateOfJoin']);
$lastWorkingDay = convert_date_format($masterData['lastWorkingDay']);
$dPlaces = $masterData['trDPlace'];
$fn_items_drop = fetch_final_settlement_items();
$companyBanks = [];
$empBank = [];
$isBankTransferProcessed = 0;
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$sal_cat = salary_categories(['A', 'D'], 1);

$encash_policy = getPolicyValues('LEB', 'All'); //Leave encashment policy
$no_of_working_days = 22;
$readonly = '';
if($encash_policy == 1){
    $salaryProportionFormulaDays = getPolicyValues('SPF', 'All'); // Salary Proportion Formula
    $no_of_working_days = ($salaryProportionFormulaDays == 365)? 30.42: 30;
    $readonly = 'readonly';
}
?>
<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .row-centered {
        text-align:center;
    }

    .col-centered {
        display:inline-block;
        float:none;
        /* reset the text-align */
        text-align:left;
        /* inline-block space fix */
        margin-right:-4px;
        text-align: center;
    }

    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }

    .hide-div{ display: none; }

    .fs-actionBtn{ font-size: 9px !important; }

    .total-sd-single {
        border-top: 1px solid  #151313 !important;
        border-bottom: 1px solid  #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }

    #deduction-msg-container{
        color: red;
        margin-left: 5px;
        margin-bottom: 10px;
    }

    .chk-ind:hover, #chk-all:hover{ cursor: pointer; }

    .head-tot{
        font-weight: bold;
    }

    .wysihtml5-sandbox{
        height: 230px !important;
    }

    .bootBox-btn-margin{
        margin-right: 10px;
    }
</style>

<div class="row well" style="padding: 10px;">
    <div class="col-md-4">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                <td class="bgWhite details-td" id="documentCode" width="200px"><?php echo $masterData['documentCode']; ?></td>
            </tr>
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_employee');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $masterData['ECode'].' | '.$masterData['Ename2']; ?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-2">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_date');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $docDate; ?></td>
            </tr>
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo get_currency_code($masterData['trCurrencyID']); ?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('emp_date_joined');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $dateJoin; ?></td>
            </tr>
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('emp_lastworking_date');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $lastWorkingDay; ?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_narration');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $masterData['narration']; ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="font-weight: bold">
            <tr>
                <td style="padding: 2px 0px;" width="200px">
                    <?php if($isConfirmed != 1){ ?>
                    <button class="btn btn-success btn-xs pull-right" style="font-size: 11px; font-weight: bold;" onclick="doc_confirm()">
                        <?php echo $this->lang->line('common_confirm');?>
                    </button>
                    <?php } ?>

                    <?php if($isApproved == 1 && $paymentVoucherID == 0){
                        $companyBanks = company_bank_account_drop(1);
                        $empBank = employee_bank_drop($masterData['empID']);
                        ?>
                        <button class="btn btn-primary btn-xs pull-right" style="font-size: 11px; font-weight: bold;" onclick="open_bankTransferModal()">
                            <?php $isBankTransferProcessed = 1; echo $this->lang->line('common_bank_transfer'); ?>
                        </button>
                    <?php } ?>

                    <button class="btn btn-default btn-xs pull-right" style="font-size: 11px; font-weight: bold; margin-right: 10px;"
                            onclick="account_review(<?=$masterID?>, '<?=$masterData['documentCode']?>')">
                        <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp; <?php echo $this->lang->line('common_account_review');?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row" style="margin-top: -20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <div class="box collapsed-box" style="margin-top: 10px; border: 1px solid #ccc;; border-top: 3px solid #ccc;">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title" id="box-header-title"><?php echo $this->lang->line('emp_bank_payroll');?></h3>
                    <span id="totPayroll-span" class="head-tot"></span>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <table class="<?php echo table_class(); ?> add_declarationTB">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $totPayroll = 0;
                        if( !empty($payrollSal) ){
                            foreach($payrollSal as $rowAdd){
                                echo '<tr>
                                    <td>'.$rowAdd['salaryDescription'].'</td>                                  
                                    <td align="right">'.number_format( $rowAdd['amount'], $dPlaces ).'</td>
                                  </tr>';
                                $totPayroll += round( $rowAdd['amount'], $dPlaces);
                            }
                        }else{
                            echo '<tr><td align="center" colspan="2">'.$this->lang->line('common_no_records_found').'</td></tr>';
                        }
                        ?>
                        </tbody>

                        <?php if( !empty($payrollSal) ){ ?>
                            <tfoot><tr><td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                                <td align="right" class="total-sd"><?php echo number_format( $totPayroll, $dPlaces ) ?></td></tr></tfoot>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box collapsed-box" style="margin-top: 10px; border: 1px solid #ccc;; border-top: 3px solid #ccc;">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title" id="box-header-title"><?php echo $this->lang->line('emp_bank_non_payroll');?></h3>
                    <span id="totNonPayroll-span" class="head-tot"></span>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <table class="<?php echo table_class(); ?> add_declarationTB">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $totNonPayroll = 0;
                        if( !empty($non_payrollSal) ){
                            foreach($non_payrollSal as $rowAdd){
                                echo '<tr>
                                    <td>'.$rowAdd['salaryDescription'].'</td>                                  
                                    <td align="right">'.number_format( $rowAdd['amount'], $dPlaces ).'</td>
                                  </tr>';
                                $totNonPayroll += round( $rowAdd['amount'], $dPlaces);
                            }
                        }else{
                            echo '<tr><td align="center" colspan="2">'.$this->lang->line('common_no_records_found').'</td></tr>';
                        }
                        ?>
                        </tbody>

                        <?php if( !empty($non_payrollSal) ){ ?>
                            <tfoot><tr><td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                                <td align="right" class="total-sd"><?php echo number_format( $totNonPayroll, $dPlaces ) ?></td></tr></tfoot>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: -20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <fieldset>
                <legend><?php echo $this->lang->line('common_addition');?></legend>
                <?php if($isConfirmed != 1){ ?>
                <button class="btn btn-primary btn-xs pull-right" style="margin-bottom: 10px;" onclick="openAddData_modal('A')">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_final_add_addition');?>
                </button>
                <?php } ?>
                <table class="<?php echo table_class(); ?>" id="addition-tb"></table>
            </fieldset>
        </div>

        <div class="col-sm-6">
            <fieldset>
                <legend><?php echo $this->lang->line('common_deduction');?></legend>
                <?php if($isConfirmed != 1){ ?>
                <button class="btn btn-primary btn-xs pull-right" style="margin-bottom: 10px;" onclick="openAddData_modal('D')">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_final_add_deduction');?>
                </button>
                <?php } ?>
                <table class="<?php echo table_class(); ?>" id="deduction-tb"></table>
            </fieldset>
        </div>
    </div>
</div>

    <div class="row" style="margin-top: 10px;">
        <div class="total-sd" style="margin: 10px 30px; font-size: 14px !important;">
            <?php echo $this->lang->line('common_net_amount');?> <span class="pull-right" id="fnNetAmount"></span>
        </div>
    </div>

<!--Do not remove following drop downs. -->
<select id="add_drop" style="display: none">
    <option value=""></option>
    <?php foreach ($fn_items_drop['A'] as $item){
        echo '<option value="'.$item['typeID'].'">'.$item['description'].'</option>';
    } ?>
</select>
<select id="ded_drop" style="display: none">
    <option value=""></option>
    <?php foreach ($fn_items_drop['D'] as $item){
        echo '<option value="'.$item['typeID'].'">'.$item['description'].'</option>';
    } ?>
</select>
<br>
<br>
<div class="row" style="margin: 10px 15px;">
        <label  for="handOverDetails">Hand over details</label>
</div>
<div class="row" style="margin: 10px 35px;">
        <textarea class="col-sm-10"  name="handOverDetails" id="handOverDetails" rows="3"></textarea>
</div>
<div class="row" style="margin: 10px 15px;">
    <span class="col-sm-10">
        <button class="btn btn-primary pull-right" onclick="save_handOverDetails()" type=""><?php echo $this->lang->line('common_save');?><!--Save--></button>
    </span>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var masterID = '<?php echo $masterID; ?>';
    var dPlace = '<?php echo $dPlaces; ?>';
    var totPayroll = '<?php echo ' : '. number_format( $totPayroll, $dPlaces ); ?>';
    var totNonPayroll = '<?php echo ' : '. number_format( $totNonPayroll, $dPlaces ); ?>';
    var add_title = '<?php echo $this->lang->line('common_addition'); ?>';
    var ded_title = '<?php echo $this->lang->line('common_deduction'); ?>';
    var deduction_msg = '<?php echo '<b>'. $this->lang->line('common_note') .' : </b>'.$this->lang->line('hrms_final__deduction_amount_should_be_entered_with_a');?> ( - )';
    var paySubInputs = $('.pay-sub-inputs');
    var entry_type_obj = $('#entry_type');
    entry_type_obj.select2();
    $('.number').numeric();
    $('.payeeCheckBox').iCheck({
        checkboxClass: 'icheckbox_minimal-blue'
    });

    $('#calculate_based_on').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '205px',
        maxHeight: '30px'
    });

    $('.select-box').select2();

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.date_pic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/final-settlement', masterID,'HRMS');
        });

        $('#totPayroll-span').text(totPayroll);
        $('#totNonPayroll-span').text(totNonPayroll);
        load_FS_detail_view();

        if(parseInt('<?php echo $isBankTransferProcessed; ?>') == 1){
            $('#bankTransferDetails').wysihtml5({
                toolbar: {
                    "font-styles": false,
                    "emphasis": false,
                    "lists": false,
                    "html": false,
                    "link": false,
                    "image": false,
                    "color": false,
                    "blockquote": false
                }
            });
        }
    });

    function save_handOverDetails(){
        $postData = $('#handOverDetails').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url("Employee/save_handOverDetails"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        }); 
    }

    function openAddData_modal(type){
        var thisTitle = add_title;
        var itemDrop = $('#add_drop');
        var deduction_msg_container = $('#deduction-msg-container');
        deduction_msg_container.html('');

        if(type == 'D'){
            thisTitle = ded_title;
            itemDrop = $('#ded_drop');
            deduction_msg_container.html(deduction_msg);
        }

        $('#modal_title').text(thisTitle);

        entry_type_obj.select2('destroy').html( itemDrop.html() ).select2();

        $('.hide-div').hide();
        $('#finalSettlement_form')[0].reset();
        $('#modal-size').removeClass('modal-lg');
        $('#fn_item_add_modal').modal('show');
    }

    function save_finalSettlementItems(){
        var postData = $('#finalSettlement_form').serializeArray();
        postData.push({'name':'masterID', 'value':masterID});


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url("Employee/save_final_settlement_items"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#fn_item_add_modal').modal('hide');

                    if(data['type'] == 'A'){
                        $('#addition-tb').html(data['view']);
                    }else{
                        $('#deduction-tb').html(data['view']);
                    }

                    setNetAmount( data['netAmount'] );
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function display_form_items(){
        var entryTypeID = entry_type_obj.val();
        entryTypeID = parseInt(entryTypeID);
        $('.hide-div').hide();
        $('#fs-ajax-container').html('');
        $('#amount').val('');
        $('#modal-size').removeClass('modal-lg');

        var postData = null; var urlStr = null;
        switch (entryTypeID){
            case 1: /*Salary*/
                $('#modal-size').addClass('modal-lg');
                postData = {'masterID': masterID};
                urlStr = '<?php echo site_url("Employee/final_settlement_salary_process_view"); ?>';
                load_more_data(entryTypeID, postData, urlStr);
            break;

            case 2: /*Other Additions*/
                postData = {'masterID': masterID, 'type': 'A'};
                urlStr = '<?php echo site_url("Employee/final_settlement_other_add_deduction_form"); ?>';
                load_more_data(entryTypeID, postData, urlStr);
            break;

            case 4: /*Gratuity*/
            case 7: /*SSO*/
            case 13: /*PAYE*/
                /*Hide the amount input box*/
            break;

            case 6: /*Other Deductions*/
                postData = {'masterID': masterID, 'type': 'D'};
                urlStr = '<?php echo site_url("Employee/final_settlement_other_add_deduction_form"); ?>';
                load_more_data(entryTypeID, postData, urlStr);
            break;

            case 8: /*Loan Recovery*/
                postData = {'masterID': masterID};
                urlStr = '<?php echo site_url("Employee/final_settlement_loan_view"); ?>';
                load_more_data(entryTypeID, postData, urlStr);
            break;

            case 12:
                $('.ded-adjustment-container, #amount-container').show();
            break;

            case 14: /*Leave Payment*/
                $('.leave-pay-container').show();

                $('#calculate_based_on').multiselect2('deselectAll', false);
                $('#calculate_based_on').multiselect2('updateButtonText');
                $('#no_of_working_days').val(<?=$no_of_working_days?>);
                postData = {'masterID': masterID};
                urlStr = '<?php echo site_url("Employee/final_settlement_leave_types"); ?>';
                load_more_data(entryTypeID, postData, urlStr);
            break;

            case 15: /*Leave Salary*/
                // $('.leave-pay-container').show();
                // $('#calculate_based_on').multiselect2('deselectAll', false);
                // $('#calculate_based_on').multiselect2('updateButtonText');
                urlStr = '<?php echo site_url("Employee/final_settlement_salary_provision"); ?>';
                postData = {'masterID': masterID};
                $('#amount-container').show();
                load_more_data(entryTypeID, postData, urlStr);
            break;

            case 16:
                $('.open-leave-container').show();
                $('#no_of_working_days').val(<?=$no_of_working_days?>);
                postData = {'masterID': masterID};
                urlStr = '<?php echo site_url("Employee/final_settlement_leave_types"); ?>';
                load_more_data(entryTypeID, postData, urlStr);
            break;

            default: $('#amount-container').show();
        }
    }

    function load_more_data(entryTypeID, postData, urlStr){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: urlStr,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //$('#fs-ajax-container').html(data).show(); return false;
                if(entryTypeID == 15){
                    $('#amount-container #amount').val(parseFloat(data['amount']).toFixed(<?php echo $dPlaces ?>));
                    $('#amount-container #amount').prop('disabled',true);
                }

                if(data[0] == 's'){
                    if(entryTypeID == 1 || entryTypeID == 8){
                        $('#fs-ajax-container').html(data['view']).show();

                        if(entryTypeID == 1){
                            setTimeout(function(){
                                $('#salary-drill-down-tb').tableHeadFixer({
                                    head: true,
                                    foot: true,
                                    left: 0,
                                    right: 0,
                                    'z-index': 10
                                });
                            }, 300);
                        }
                    }
                    else if(entryTypeID == 14|| entryTypeID == 16){
                        $('#leave_drop_div').html(data['view']);

                        $('#leaveID').select2();
                    }else if(entryTypeID == 15){
                        $('#amount-container #amount').val(data['amount']);
                        $('#amount-container #amount').prop('disabled',true);
                    }

                    else {
                        $('#grouping-drop-down-container').html(data['view']);
                        $('#grouping-container, #amount-container').show();

                        $('#groupDropID').select2();
                    }
                }
                else{
                    myAlert('e', data[1]);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function load_FS_detail_view(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID},
            url: '<?php echo site_url("Employee/FS_detail_view"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $('#addition-tb').html(data['addView']);
                    $('#deduction-tb').html(data['dedView']);
                    setNetAmount( data['netAmount'] );
                }else{
                    myAlert('e', data[1]);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function delete_fs_item_confirmation(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                delete_fs_item(id);
            }
        );
    }

    function delete_fs_item(id, confirm=0){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID, 'id': id, 'isDeleteConfirmed': confirm},
            url: '<?php echo site_url("Employee/delete_fs_item"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    myAlert(data[0], data[1]);
                    if(data['type'] == 'A'){
                        $('#addition-tb').html(data['view']);
                    }else{
                        $('#deduction-tb').html(data['view']);
                    }

                    setNetAmount( data['netAmount'] );
                }
                else if(data[0] == 'w'){
                    salaryDeleteConfirmation(id);
                }
                else{
                    myAlert(data[0], data[1]);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function salaryDeleteConfirmation(id){
        var msg = 'Amounts calculated for PAYE/ Social insurance based on these salaries will remain same! ';
        msg += 'If you want to recalculate PAYE/ SSO delete those record and insert again.';
        msg += '<br/><strong>Are you sure you want to delete this entry?</strong> ';

        bootbox.confirm({
            title: 'Warning!',
            message: msg,
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'OK Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                delete_fs_item(id, 1);
            }
        });
    }

    function open_bankTransferModal(){
        $('#fnBankTransfer_form')[0].reset();
        $('#transfer_amount').val( $('#fnNetAmount').text() );
        $('#accountID, #empBankID').val('').change();
        paySubInputs.hide();
        $('#fn_bankTransfer_modal').modal('show');
    }

    function save_bankTransfer(){
        var postData = $('#fnBankTransfer_form').serializeArray();
        postData.push({'name':'masterID', 'value':masterID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url("Employee/final_settlement_paymentVoucher_generation"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#fn_bankTransfer_modal').modal('hide');
                    setTimeout(function(){
                        fetchPage('system/hrm/final-settlement', masterID,'HRMS');
                    }, 300);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function is_cashGL(){
        var isCash = $('#accountID :selected').attr('data-type');
        paySubInputs.hide();
        if(isCash == 0){
            $('#paymentType').val(0).change();
            $('#payment-type-container').show();
        }
    }

    function show_payment_method(){
        var paymentType = $('#paymentType').val();
        $('.sub-inputs').hide();

        if(paymentType == 1){ /*Cheque*/
            $('#cheque-inputs').show();
            $('.payeeCheckBox').iCheck('uncheck');

            var accountID = $('#accountID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'GLAutoID': accountID},
                url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#chequeNo").val((parseFloat(data['bankCheckNumber']) + 1));
                },
                error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        else if(paymentType == 2){  /*Bank transfer*/
            $('#bank-transfer-inputs').show();

            load_bakTransferDetails();

        }
    }

    function check_all_button_status_change(){
        if( $('#chk-all').is(':checked') ){
            $('.chk-ind').prop('checked', true);
        }else{
            $('.chk-ind').prop('checked', false);
        }

        compute_totSalary();
    }

    function individual_status_change(){
        $('#chk-all').prop('checked', false);
        if( $('.chk-ind:checked').length == $('.chk-ind').length ){
            $('#chk-all').prop('checked', true);
        }

        compute_totSalary();
    }

    function compute_totSalary(){
        var tot = 0;
        $('.chk-ind:checked').each(function(){
            var thisVal = $(this).attr('data-val');
            tot += parseFloat(thisVal);
        });
        tot = commaSeparateNumber(tot, dPlace);
        $('#selected-salary-tot').text( tot );
    }

    function more_fs_item(typeID, autoID){
        $('.more-det-tb, .leave-pay-formula').hide();
        $('#more-det-body').removeClass('modal-lg');

        if(typeID == 1 || typeID == 7){
            $('#more-det-body').addClass('modal-lg');
        }

        if(typeID == 14){
            $('.leave-pay-formula').show();
        }

        $('#more-detail-tb-'+typeID).show();
        var respDiv = $('#more-detail-body-'+typeID);
        $('#modal_moreDetailTitle').text('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID, 'autoID': autoID, 'typeID': typeID},
            url: '<?php echo site_url("Employee/FS_more_detail_view"); ?>',
            beforeSend: function () {
                respDiv.html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    respDiv.html(data['view']);
                    $('#modal_moreDetailTitle').html(data['title']);
                    $('#fn_item_moreDetail_modal').modal('show');
                }else{
                    myAlert('e', data[1]);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    
    function doc_confirm() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                doc_confirm_request(0);
            }
        );
    }

    function doc_confirm_request(validate){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID, 'validate': validate},
            url: "<?php echo site_url('Employee/final_settlement_document_confirm'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's' || data[0] == 'e'){
                    myAlert(data[0], data[1]);
                }

                if(data[0] == 's'){
                    setTimeout(function(){
                        fetchPage('system/hrm/final-settlement', masterID,'HRMS');
                    }, 300);
                }
                else if(data[0] == 'w'){
                    bootbox.confirm({
                        message: '<h4>Warning </h4>'+data[1],
                        size: 'medium',
                        closeButton: true,
                        buttons: {
                            confirm: {
                                label: 'Proceed',
                                className: 'btn-primary'
                            },
                            cancel: {
                                label: 'Cancel',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if(result){
                                doc_confirm_request(1);
                            }
                        }
                    });
                }
            }, error: function () {
                myAlert('e', 'Some thing went wrong please contact system support');
            }
        });
    }

    function setNetAmount(netAmount){
        $('#fnNetAmount').text(netAmount)
    }

    function load_bakTransferDetails(){
        var empBank_obj = $('#empBankID');
        var note = '';
        if( empBank_obj.val() != ''){
            empBank_obj = $('#empBankID :selected');
            var beneficiary = empBank_obj.attr('data-beneficiary');
            var bankName = empBank_obj.attr('data-bank');
            var accountNo = empBank_obj.attr('data-acc');
            var brnSwiftCode = empBank_obj.attr('data-swift');

            note = '<p><p>Beneficiary Name : '+beneficiary+'</p><p>Bank Name : '+bankName+'</p><p>Beneficiary Bank Address : </p><p>Bank Account : '+accountNo+'</p>';
            note += '<p>Beneficiary Swift Code : '+brnSwiftCode+'</p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
            $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(note);
        }else{
            note = '<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p>';
            note += '<p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
            $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(note);
        }
    }

    function account_review(docID, docCode){
        window.open("<?php echo site_url('Employee/final_settlement_account_review'); ?>/"+docID+"/"+docCode, "blank");
    }

    function max_month_days(){
        var obj = $('#no_of_working_days');

        if( parseInt(obj.val()) > 31 ){
            obj.val('');
            myAlert('w', 'Maximum days can not be greater than 30')
        }
    }
</script>

<div class="modal fade" id="fn_item_add_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" id="modal-size" style="" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal_title"><?php echo $title ?></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" id="finalSettlement_form" autocomplete="off">

                    <div class="row" style="" id="deduction-msg-container"> </div>
                    <div class="row" style="margin-left: 50px">
                        <div class="form-group ">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_type');?></label>
                            <div class="col-sm-5">
                                <select name="entry_type" id="entry_type" class="form-control select2" onchange="display_form_items()"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row hide-div" id="grouping-container" style="margin-left: 50px">
                        <div class="form-group">
                             <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_final_grouping_type');?></label>
                             <div class="col-sm-5" id="grouping-drop-down-container"></div>
                        </div>
                    </div>
                    <div class="row hide-div ded-adjustment-container" style="margin-left: 50px">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_adjustment_type');?></label>
                            <div class="col-sm-5">
                                <?php
                                echo form_dropdown('adjustment_type', drop_down_sso_and_payee(), '', 'class="form-control select-box" id="adjustmentType"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row hide-div open-leave-container" style="margin-left: 50px">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_open_leave');?></label>
                            <div class="col-sm-5">
                            <select name="openLeave" class="form-control" id="openLeave" >
                                   <option value="open" selected>Open Leave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row hide-div leave-pay-container" style="margin-left: 50px">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_annual_leave');?></label>
                            <div class="col-sm-5" id="leave_drop_div"></div>
                        </div>
                    </div>
                    <div class="row hide-div leave-pay-container open-leave-container" style="margin-left: 50px">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_basic_gross');?></label>
                            <div class="col-sm-5">
                                <select name="calculate_based_on[]" class="form-control" id="calculate_based_on" multiple="multiple">
                                    <?php
                                    foreach ($sal_cat as $cat_row){
                                        echo '<option value="'.$cat_row['salaryCategoryID'].'">'.$cat_row['salaryDescription'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row hide-div leave-pay-container open-leave-container" style="margin-left: 50px">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_no_of_working_days');?></label>
                            <div class="col-sm-5" style="padding-top: 10px;">
                                <input name="no_of_working_days" class="form-control number" id="no_of_working_days" onkeyup="max_month_days()"
                                    <?=$readonly?> value="<?=$no_of_working_days?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="row hide-div" id="amount-container" style="margin-left: 50px">
                        <div class="form-group">
                             <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_amount');?></label>
                             <div class="col-sm-5">
                                <input type="text" name="amount" id="amount" class="form-control number">
                             </div>
                        </div>
                    </div>
                    <div class="row hide-div" id="fs-ajax-container" style="padding: 15px;"></div>
                    <div class="row" style="margin-left: 50px">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_narration');?></label>
                            <div class="col-sm-5">
                                <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row hide-div leave-pay-container open-leave-container" style="margin-left: 50px; color: red;">
                        <?php echo $this->lang->line('common_leave_pay_formula');?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_finalSettlementItems()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fn_item_moreDetail_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" id="more-det-body" style="" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal_moreDetailTitle"></h4>
            </div>
            <div class="modal-body">
                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-1"> <!--Salary-->
                    <thead>
                    <tr>
                        <th>Period</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>No of Days</th>
                        <th>GL Description</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-1"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-7"> <!--SSO-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_period');?></th>
                        <th><?php echo $this->lang->line('common_description');?></th>
                        <th><?php echo $this->lang->line('common_employee_contribution');?></th>
                        <th><?php echo $this->lang->line('common_employer_contribution');?></th>
                        <th><?php echo $this->lang->line('common_expense_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_liability_gl_code');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-7"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-8"> <!--Loan-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_code');?></th>
                        <th><?php echo $this->lang->line('common_description');?></th>
                        <th><?php echo $this->lang->line('common_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_amount');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-8"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-12"> <!--Adjustment-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_employee_contribution');?></th>
                        <th><?php echo $this->lang->line('common_employer_contribution');?></th>
                        <th><?php echo $this->lang->line('common_expense_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_liability_gl_code');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-12"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-13"> <!--PAYE-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_period');?></th>
                        <th><?php echo $this->lang->line('common_liability_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_amount');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-13"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-14"> <!--Leave Payment-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_annual_leave');?></th>
                        <th><?php echo $this->lang->line('common_leave_balance');?></th>
                        <th><?php echo $this->lang->line('common_no_of_working_days');?></th>
                        <th><?php echo $this->lang->line('common_basic_gross');?></th>
                        <th><?php echo $this->lang->line('common_amount');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-14"></tbody>
                </table>
                <div class="leave-pay-formula" style="margin-left: 50px; margin-top: 15px; color: red;">
                    <?php echo $this->lang->line('common_leave_pay_formula');?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fn_bankTransfer_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_bank_transfer');?></h4>
            </div>
            <div class="modal-body">
                <form class="" role="form" id="fnBankTransfer_form" autocomplete="off">

                    <div class="row">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_employee_bank');?></label>
                            <select name="empBankID" id="empBankID" class="form-control select-box" onchange="load_bakTransferDetails()">
                                <option value="">Select Employee Bank</option>
                                <?php
                                foreach($empBank as $key=>$row){
                                    $des = trim($row['bankName'] ?? '').' | '.trim($row['branchName'] ?? '').' | '.trim($row['accountNo'] ?? '').' | '.trim($row['bankSwiftCode'] ?? '');
                                    $attr = 'data-beneficiary="'.$row['accountHolderName'].'" data-bank="'.$row['bankName'].'" data-acc="'.$row['accountNo'].'"  data-swift="'.$row['bankSwiftCode'].'"';
                                    echo '<option value="'.$row['id'].'" '.$attr.'> '.$des.'</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_transfer_date');?></label>
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="transDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="transDate" class="form-control date_picker" >
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_amount');?></label>
                            <input type="text" name="transfer_amount" id="transfer_amount" class="form-control number" disabled>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('accounts_payable_tr_pv_payment_bank_or_cash');?></label>
                            <select name="accountID" id="accountID" class="form-control select-box" onchange="is_cashGL()">
                                <option value="">Select Bank Account</option>
                                <?php
                                foreach($companyBanks as $key=>$row){
                                    $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                                    $des = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankBranch'] ?? '') . ' | ' . trim($row['bankSwiftCode'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . $type;
                                    echo '<option value="'.$row['GLAutoID'].'" data-type="'.$row['isCash'].'"> '.$des.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row pay-sub-inputs" id="payment-type-container">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_payment_type');?></label>
                            <?php
                            $paymentType_arr = [ ''=>$this->lang->line('common_select_type'), '1' => 'Cheque ', '2' =>'Bank Transfer' ];
                            echo form_dropdown('paymentType', $paymentType_arr, '', 'class="form-control select-box" id="paymentType" onchange="show_payment_method()"');
                            ?>
                        </div>
                    </div>

                    <fieldset class="pay-sub-inputs sub-inputs" id="cheque-inputs">
                        <legend><?php echo $this->lang->line('common_cheque_details');?> </legend>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_no');?> <?php required_mark(); ?></label>
                            <input type="text" name="chequeNo" id="chequeNo" class="form-control">
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_date');?> <?php required_mark(); ?></label>
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="chequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="chequeDate" class="form-control" >
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_payee_only');?></label>
                            <div class="skin skin-square">
                                <input type="checkbox" name="accountPayeeOnly" class="payeeCheckBox" >
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="pay-sub-inputs sub-inputs" id="bank-transfer-inputs">
                        <legend><?php echo $this->lang->line('common_bank_transfer_details');?> </legend>
                        <div class="form-group col-sm-12 col-xs-6">
                            <textarea class="form-control" rows="5" name="bankTransferDetails" id="bankTransferDetails"></textarea>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_bankTransfer()"><?php echo $this->lang->line('common_proceed');?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<?php
