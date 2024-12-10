<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_investment_details');
echo head_page($title, false);

$investID = trim($this->input->post('page_id'));
$currency_arr = all_currency_new_drop();
$invType_arr = investmentType_drop();
$bank_acc_arr = company_bank_account_drop();
$date_format_policy = date_format_policy();
$current_date = current_format_date();

?>

<style>
    .header-div{
        background-color: #afc6dc;
        padding: 1%;
    }

    .details-td{ font-weight: bold; }

    legend{ font-size: 16px !important; }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="masterContainer">
    <div class="row well">
        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                    <td class="bgWhite details-td" id="documentCode" width="200px"></td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('fn_man_investment_types');?></td>
                    <td class="bgWhite details-td" id="inv_type" width="200px"></td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_narration');?></td>
                    <td class="bgWhite details-td" id="inv_narration1" width="200px">
                        <a href="#" data-type="text" data-placement="bottom" id="narration_xEditable"
                           data-title="<?php echo $this->lang->line('fn_man_edit_amount');?>"
                           data-pk="" data-value=" ">
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('fn_man_investment_company');?></td>
                    <td class="bgWhite details-td" id="invCompany" width="200px"></td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('fn_man_new_investment_date');?></td>
                    <td class="bgWhite details-td" id="inv_date1" width="200px">
                        <a href="#" data-type="combodate" data-placement="bottom" id="inv_date_xEditable"
                           data-title="<?php echo $this->lang->line('fn_man_edit_inv_date');?>" data-pk="1"  data-value="" >
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('fn_man_amount');?></td>
                    <td class="bgWhite details-td" id="inv_amount" width="200px" style="text-align: right">
                        <span class="inv_currency"></span>
                        <a href="#" data-type="text" data-placement="left" id="amount_xEditable"
                           data-title="<?php echo $this->lang->line('fn_man_edit_amount');?>"
                           data-pk="" data-value=" ">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_balance');?></td>
                    <td class="bgWhite details-td" width="200px" style="text-align: right">
                        <span class="inv_currency"></span>
                        <span id="balAmount"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>


<div class="row" style="margin-top: 15px">
    <div class="col-md-8">
        <fieldset class="scheduler-border" style="margin-top: 10px">
            <legend class="scheduler-border"><?php echo $this->lang->line('fn_man_disburse_details'); ?></legend>
            <div class="row" style="margin-bottom: 4px;">&nbsp;</div>

            <div class="row">
                <div class="table-responsive" id="disburse_data">

                </div>
            </div>
        </fieldset>
    </div>
<div id="test"></div>
    <div class="col-md-4" id="add-edit-disburse-div">
        <fieldset class="scheduler-border" style="margin-top: 10px">
            <legend class="scheduler-border" id="disburse-form-title"><?php echo $this->lang->line('fn_man_new_disburse');?></legend>
            <div class="row" style="margin-bottom: 4px;">&nbsp;</div>

            <?php echo form_open('', 'role="form" id="invest_disburse_form" class="form-horizontal" autocomplete="off"'); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="invDate"><?php echo $this->lang->line('common_date');?></label>
                        <div class="col-sm-8">
                            <div class="input-group picDate">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="disburseDate" id="disburseDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group ">
                        <label class="col-sm-4 control-label" for="amount"><?php echo $this->lang->line('fn_man_amount');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="amount" id="amount" class="form-control number" value="" >
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group ">
                        <label class="col-sm-4 control-label" for="bankGL"><?php echo $this->lang->line('common_bank');?></label>
                        <div class="col-sm-8">
                            <?php echo form_dropdown('bankGL', $bank_acc_arr, '', 'class="form-control select2" id="bankGL"'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group ">
                        <label class="col-sm-4 control-label" for="bankGL"><?php echo $this->lang->line('common_narration');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="narration" id="narration" class="form-control" value="" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding-right: 0px;">
                <input type="hidden" name="invMasterID" id="invMasterID" value="<?php echo $investID; ?>">
                <input type="hidden" name="disburseID" id="disburseID" value="0">
                <button class="btn btn-primary btn-xs" type="button" id="invest-disburse-frm-btn" onclick="add_disburse()">
                    <?php echo $this->lang->line('common_save');?>
                </button>
                <button class="btn btn-primary btn-xs" type="button" id="invest-disburse-confirm-frm-btn" onclick="disburse_confirm()">
                    <?php echo $this->lang->line('common_save_and_confirm');?>
                </button>
                <button class="btn btn-default btn-xs" type="button" id="invest-disburse-frm-cancel-btn" onclick="new_disburse()" style="display: none">
                    <?php echo $this->lang->line('common_cancel');?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </fieldset>
    </div>
</div>


<div class="row" style="margin-top: 15px">
    <div class="col-md-12">
        <fieldset class="scheduler-border" style="margin-top: 10px">
            <legend class="scheduler-border" id="disburse-form-title"><?php echo $this->lang->line('common_attachments');?></legend>
            <div class="row" style="margin-bottom: 4px;">&nbsp;</div>

            <div id="attach_details"></div>
        </fieldset>
    </div>
</div>



<script>
    var investID = <?php echo json_encode($investID); ?>;
    var new_disburseText = '<?php echo $this->lang->line('fn_man_new_disburse'); ?>';
    var edit_disburseText = '<?php echo $this->lang->line('fn_man_edit_disburse'); ?>';

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.picDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    }).on('dp.change', function (ev) {

    });

    $('#bankGL').select2();

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/fund-management/investment', investID, 'Investment');
        });

        load_investments_details();
        get_attachments_details();
    });

    function new_disburse(){
        $('#invest_disburse_form')[0].reset();
        $('#bankGL').val('').change();
        $('#invest-disburse-frm-btn').attr('onclick', 'add_disburse()');
        $('#invest-disburse-confirm-frm-btn').attr('onclick', 'disburse_confirm()');
        $('#disburse-form-title').text(new_disburseText);

        $('#invest-disburse-frm-cancel-btn').hide();
        $('#add-edit-disburse-div').hide().fadeIn(1500);
    }

    function add_disburse(isConfirm=0){
        var postData = $('#invest_disburse_form').serializeArray();
        postData.push({name:'isConfirm', value:isConfirm});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/add_disburse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    new_disburse();
                    investments_disbursed_view();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function disburse_confirm(isSave=0) {
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            if(isSave == 0){
                add_disburse(1);
            }else{
                update_disburse(1);
            }
        });
    }

    function load_investments_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'investID': investID},
            url: "<?php echo site_url('Fund_management/investment_master_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#documentCode').text(data['documentCode']);
                $('#invCompany').text(data['company_name']);
                $('#inv_type').text(data['invDes']);
                $('#inv_narration').text(data['narration']);
                $('.inv_currency').text(data['CurrencyCode'] +' : ');
                $('#narration_xEditable').editable('setValue', data['narration'] );
                $('#amount_xEditable').editable('setValue', data['invAmountStr'] );
                $('#inv_date_xEditable').editable('setValue', data['invDate'],true);
                $('#inv_date_xEditable').attr('data-pk', data['inv_date']);
                $('#balAmount').text(data['balAmount']);

                setTimeout(function () {
                    investments_disbursed_view();
                }, 300);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function investments_disbursed_view(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'investID': investID},
            url: "<?php echo site_url('Fund_management/investment_disburse_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#disburse_data').html(data);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_attachments_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'documentSystemCode': investID, 'systemDocumentID': 'FMIT'},
            url: "<?php echo site_url('Fund_management/get_attachment_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attach_details').html(data);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function edit_disburse(disID, amount, disDate, glCode, narration){
        $('#invest_disburse_form')[0].reset();
        $('#invest-disburse-frm-btn').attr('onclick', 'update_disburse()');
        $('#invest-disburse-confirm-frm-btn').attr('onclick', 'disburse_confirm(1)');
        $('#disburse-form-title').text(edit_disburseText);

        $('#add-edit-disburse-div').hide().fadeIn('slow');
        $('#invest-disburse-frm-cancel-btn').show();

        $('#disburseDate').val(disDate);
        $('#amount').val(amount);
        $('#disburseID').val(disID);
        $('#bankGL').val(glCode).change();
        $('#narration').val(narration);
    }

    function update_disburse(isConfirm=0){
        var postData = $('#invest_disburse_form').serializeArray();
        postData.push({name:'isConfirm', value:isConfirm});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/update_disburse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#test').html(data)
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    new_disburse();
                    investments_disbursed_view();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    $('#amount_xEditable').editable({
        url: '<?php echo site_url('Fund_management/edit_investment_amount?masterID='.$investID) ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){
                    var amount_xEditable = $('#amount_xEditable');
                    setTimeout(function (){
                        amount_xEditable.attr('data-pk', amount_xEditable.html());
                        $('#amount_xEditable').editable('setValue', data['amount'] );
                        $('#balAmount').text(data['amount']);
                    },400);

                }else{
                    var oldVal = $('#amount_xEditable').data('pk');
                    setTimeout(function (){
                        $('#amount_xEditable').editable('setValue', oldVal );
                        $('#balAmount').text(oldVal);
                    },300);
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    $('#narration_xEditable').editable({
        url: '<?php echo site_url('Fund_management/edit_investment_narration?masterID='.$investID) ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    $('#inv_date_xEditable').editable({
        format: 'YYYY-MM-DD',
        viewformat: 'DD-MM-YYYY',
        template: 'D / MM / YYYY',
        combodate: {
            minYear: <?php echo format_date_getYear() - 10 ?>,
            maxYear: <?php echo format_date_getYear() + 10 ?>,
            minuteStep: 1
        },
        url: '<?php echo site_url('Fund_management/edit_investment_date?masterID='.$investID) ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){

                }else{
                    var oldVal = $('#inv_date_xEditable').data('pk');
                    setTimeout(function (){
                        $('#inv_date_xEditable').editable('setValue', oldVal, true);
                    },300);
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    $(document).on('keypress', '.number',function (event) {
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
</script>


<?php
