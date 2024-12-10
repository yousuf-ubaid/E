<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$financeyear_arr = all_financeyear_drop(true);
//$gl_code_arr = company_PL_account_drop();
$gl_code_arr = dropdown_all_revenue_gl_JV();
$segment_arr = fetch_segment();
$projectExist = project_is_exist();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"> <?php echo $this->lang->line('finance_common_step_one');?><!--Step 1--> - <?php echo $this->lang->line('finance_tr_rj_recurring_journal_voucher_header');?><!--RJV Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_journal_entry_detail()" data-toggle="tab"> <?php echo $this->lang->line('finance_common_step_two');?><!--Step 2--> - <?php echo $this->lang->line('finance_tr_rj_recurring_journal_voucher_detail');?><!--RJV Detail--></a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation()" data-toggle="tab"><?php echo $this->lang->line('finance_common_step_three');?><!--Step 3--> - <?php echo $this->lang->line('finance_tr_rj_recurring_journal_voucher_confirmation');?><!--RJV Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="recurring_journal_entry_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""> <?php echo $this->lang->line('finance_common_reference_no');?><!--Reference No--></label>
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_start_date');?><!--Start Date --><?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="RJVStartDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RJVStartDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_end_date');?><!--End Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="RJVEndDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RJVEndDate"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'JE\')" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_narration');?><!--Narration--> <?php required_mark(); ?></label>
                <textarea class="form-control" rows="3" id="RJVNarration" name="RJVNarration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <!--<div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> Add Item Detail </h4></div>-->
            <div class="col-md-12 pull-right">
                <div class="">
                    <div class="col-md-12 no-padding" style="margin-bottom: 10px;">
                        <button type="button" onclick="jv_detail_modal()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> <?php echo $this->lang->line('common_update_add_new');?><!--Add New-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th colspan="6"> <?php echo $this->lang->line('common_gl_details');?><!--GL Details--></th>
                <th colspan="2"> <?php echo $this->lang->line('common_amount');?><!--Amount--> <span class="currency">&nbsp;</span></th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('finance_tr_jv_system_code');?><!--System Code--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('GL Code Description');?><!--GL Code Description--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_debit');?><!--Debit--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_credit');?><!--Credit--></th>
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="gl_table_body">
            <tr class="danger">
                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
            </tr>
            </tbody>
            <tfoot id="gl_table_tfoot">

            </tfoot>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
            <!-- <button class="btn btn-primary submitWizard" onclick="confirmation()">Confirmation</button> -->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--  <div class="row">
            <div class="col-md-12">
            <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php /*echo site_url('Double_entry/fetch_double_entry_journal_entry/'); */ ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries
                </a>
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank" href="<?php /*echo site_url('Procurement/load_purchase_order_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
            </span>
            </div>
        </div><hr>-->
        <div id="conform_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        </div>
    </div>
</div>
<!--Add New-->
<div aria-hidden="true" role="dialog" id="jv_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('finance_common_gl_detail');?><!--GL Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="jv_detail_form">
                    <table class="table table-bordered table-condensed no-color" id="jv_detail_add_table">
                        <thead>
                        <tr>
                            <th colspan="2"></th>
                            <th colspan="2"><?php echo $this->lang->line('common_amount');?><!--Amount--> <span class="currency">(LKR)</span></th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th style="width: 350px;"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project');?><!--Project--> </th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('finance_common_debit');?><!--Debit--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('finance_common_credit');?><!--Credit--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_narration');?><!--Narration--> <?php required_mark(); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('gl_code[]', $gl_code_arr, '', 'class="form-control select2" id="gl_code" required'); ?></td>
                            <td><?php echo form_dropdown('segment_gl[]', $segment_arr, '', 'class="form-control select2" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?></td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_income">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('common_select_project');?><!--Select Project--></option>
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="text" name="debitAmount[]" id="debitAmount" value="0"
                                       onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" onchange="makeZero('creditAmount',this, this.value)"
                                       class="form-control cus1-input number amount">

                            </td>
                            <td>
                                <input type="text" name="creditAmount[]" id="creditAmount" value="0"
                                       onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" onchange="makeZero('debitAmount',this, this.value)"
                                       class="form-control cus1-input number amount">
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" id="description"
                                          name="description[]"></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="saveJVDetails()"> <?php echo $this->lang->line('common_save_change');?><!--Save Changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<!--Edit New-->
<div aria-hidden="true" role="dialog" id="jv_detail_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('finance_common_gl_detail');?><!--GL Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="jv_detail_edit_form">
                    <input type="hidden" id="xRJVDetailAutoID" name="RJVDetailAutoID">
                    <table class="table table-bordered table-condensed no-color" id="jv_detail_edit_table">
                        <thead>
                        <tr>
                            <th colspan="2"></th>
                            <th colspan="2"><?php echo $this->lang->line('common_amount');?><!--Amount--> <span class="currency">(LKR)</span></th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th style="width: 350px;"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project');?><!-- Project --> </th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('finance_common_debit');?><!--Debit--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('finance_common_credit');?><!--Credit--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_narration');?><!--Narration--> <?php required_mark(); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('edit_gl_code', $gl_code_arr, '', 'class="form-control select2" id="edit_gl_code" required'); ?></td>
                            <td><?php echo form_dropdown('edit_segment_gl', $segment_arr, '', 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?></td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_income">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('common_select_project');?><!--Select Project--></option>
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="text" name="editdebitAmount" id="editdebitAmount" value="0"
                                       onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" onchange="makeZero_edit('editcreditAmount')"
                                       class="form-control cus1-input number amount">

                            </td>
                            <td>
                                <input type="text" name="editcreditAmount" id="editcreditAmount" value="0"
                                       onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" onchange="makeZero_edit('editdebitAmount')"
                                       class="form-control cus1-input number amount">
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" id="editdescription"
                                          name="editdescription"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="updateJVDetails()"><?php echo $this->lang->line('common_save_change');?><!--Save Changes-->
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var RJVMasterAutoId;
    var RJVDetailAutoID;
    var creditAmount;
    var debitAmount;
    var projectID_income;
    var currency_decimal;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/recurringJV/recurring_jv_management','','Recurring Journal Voucher');
        });
        $('.select2').select2();
        RJVMasterAutoId = null;
        RJVDetailAutoID = null;
        projectID_income = null;
        creditAmount = 0;
        debitAmount = 0;

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#recurring_journal_entry_form').bootstrapValidator('revalidateField', 'RJVStartDate');
            $('#recurring_journal_entry_form').bootstrapValidator('revalidateField', 'RJVEndDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            RJVMasterAutoId = p_id;
            load_journal_entry_header();
            $("#a_link").attr("href", "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + RJVMasterAutoId);
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            currency_validation(<?php echo json_encode(trim($this->common_data['company_data']['company_default_currencyID'])); ?>,'JE');

        }
        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID,periodID);
        number_validation();

        $('#jv_detail_form1').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
            excluded: [':disabled'],
            fields: {
                gl_code: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gl_code_is_required');?>.'}}},/*GL code is required*/
                amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('finance_common_amount_is_required');?>.'}}},/*Amount is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'RJVMasterAutoId', 'value': RJVMasterAutoId});
            /*    data.push({'name': 'RJVDetailAutoID', 'value': RJVDetailAutoID});*/
            data.push({'name': 'gl_code_des', 'value': $('#gl_code option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Journal_entry/save_gl_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {


                    refreshNotifications(true);
                    stopLoad();

                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        $('#jv_detail_form')[0].reset();
                        $("#segment_gl").select2("");
                        $("#gl_code").select2("");

                        $('#jv_detail_modal').modal('hide');

                        debitNoteDetailsID = null;
                        fetch_journal_entry_detail();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#recurring_journal_entry_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                RJVStartDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('finance_common_start_date_is_required');?>.'}}},/*Start Date is required*/
                RJVEndDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('finance_common_end_date_is_required');?>.'}}},/*End Date is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('finance_common_transaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                RJVNarration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('finance_common_narration_is_required');?>.'}}}/*Narration is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#transactionCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'RJVMasterAutoId', 'value': RJVMasterAutoId});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Recurring_je/save_recurring_journal_entry_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        RJVMasterAutoId = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + RJVMasterAutoId);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                    }
                    ;
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });
    });

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Finance Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_journal_entry_header() {
        if (RJVMasterAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'RJVMasterAutoId': RJVMasterAutoId},
                url: "<?php echo site_url('Recurring_je/load_journal_entry_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#RJVStartDate').val( data['RJVStartDate']);
                        $('#RJVEndDate').val( data['RJVEndDate']);
                        $('#RJVNarration').val(data['RJVNarration']);
                        $('#referenceNo').val(data['referenceNo']);
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        get_currency_decimal_places(data['transactionCurrencyID']);
                        fetch_journal_entry_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_journal_entry_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'RJVMasterAutoId': RJVMasterAutoId},
            url: "<?php echo site_url('Recurring_je/fetch_journal_entry_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.currency').html('(' + data['currency']['transactionCurrency'] + ')');
                $('#gl_table_body,#gl_table_tfoot').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#gl_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    $("#transactionCurrencyID").prop("disabled", false);
                } else {
                    $("#transactionCurrencyID").prop("disabled", true);
                    creditAmount = 0;
                    debitAmount = 0;
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    $.each(data['detail'], function (key, value) {
                        var segMentCode = value['segmentCode'];
                        if (value['segmentCode'] == null) {
                            segMentCode = '-';
                        }
                        $('#gl_table_body').append('<tr><td>' + x + '</td><td>' + value['systemGLCode'] + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + '</td><td>' + value['description'] + '</td><td class="text-center">' + segMentCode + '</td><td class="text-right">' + parseFloat(value['debitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['creditAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['RJVDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="delete_item(' + value['RJVDetailAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        x++;
                        creditAmount += (parseFloat(value['creditAmount']));
                        debitAmount += (parseFloat(value['debitAmount']));
                    });
                    $('#gl_table_tfoot').append('<tr><td colspan="6" class="text-right"> <?php echo $this->lang->line('common_total');?><!--Total--> (' + data['currency']['transactionCurrency'] + ' ) </td><td class="text-right total">' + parseFloat(debitAmount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right total">' + parseFloat(creditAmount).formatMoney(currency_decimal, '.', ',') + '</td></tr>');
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function jv_detail_modal() {
        if (RJVMasterAutoId) {
            $('#gl_code').val('').change();
            $('#segment_gl').val('').change();
            $('#jv_detail_form')[0].reset();
            $('#xRJVDetailAutoID').val('');
            $("#jv_detail_modal").modal({backdrop: "static"});
            $('#jv_detail_add_table tbody tr').not(':first').remove();
        }
    }

    function currency_validation(CurrencyID) {
        if (CurrencyID) {
            documentID = $('#contractType').val();
            partyAutoID = $('#customerID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'CUS');
            get_currency_decimal_places(CurrencyID);
        }
    }

    function load_conformation() {
        if (RJVMasterAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'RJVMasterAutoId': RJVMasterAutoId, 'html': true},
                url: "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + RJVMasterAutoId);
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    //refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if ((creditAmount.toFixed(2)) == (debitAmount.toFixed(2))) {
            if (RJVMasterAutoId) {
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
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'RJVMasterAutoId': RJVMasterAutoId},
                            url: "<?php echo site_url('Recurring_je/recurring_journal_entry_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                refreshNotifications(true);
                                stopLoad();
                                fetchPage('system/recurringJV/recurring_jv_management', 'Test', 'Recurring Journal Voucher');
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
            ;
        } else {
            sweetAlert("Oops...", "The transaction is not in balance. Please make sure the total amount in debit column equals the total amount in the credit column.", "error");
        }
    }

    function save_draft() {
        if (RJVMasterAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('finance_common_you_want_to_save_this_file');?>",/*You want to save this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/recurringJV/recurring_jv_management', 'Test', 'Recurring Journal Voucher');
                });
        }
    }

    function delete_item(id, value) {
        if (RJVMasterAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'RJVDetailAutoID': id},
                        url: "<?php echo site_url('Recurring_je/delete_Journal_entry_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            fetch_journal_entry_detail();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>",/*You want to edit this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",/*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'RJVDetailAutoID': id},
                    url: "<?php echo site_url('Recurring_je/load_jv_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#jv_detail_edit_form')[0].reset();
                        projectID_income = data['projectID'];
                        $('#xRJVDetailAutoID').val(data['RJVDetailAutoID']);
                        RJVDetailAutoID = data['RJVDetailAutoID'];
                        $('#edit_gl_code').val(data['GLAutoID']).change();
                        $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        if (data['gl_type'] == 'Cr') {
                            $('#editcreditAmount').val(data['creditAmount']);
                        } else {
                            $('#editdebitAmount').val(data['debitAmount']);
                        }
                        $('#editdescription').val(data['description']);
                        /**/
                        $("#jv_detail_edit_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });

    }

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#jv_detail_add_table tbody tr:first').clone();
        //appendData.find('input').val('')
        /*var expneceGL = $('#expenseGlAutoID_1 option').clone();
         var liabilityGL = $('#liabilityGlAutoID_1 option').clone();
         var ifSlab = $('#ifSlab_1 option').clone();*/
        appendData.find('input,select,textarea').val('')

        appendData.find('#expenseGlAutoID_1').attr('id', '')
        appendData.find('#liabilityGlAutoID_1').attr('id', '')
        appendData.find('#ifSlab_1').attr('id', '')

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#jv_detail_add_table').append(appendData);
        var lenght = $('#jv_detail_add_table tbody tr').length - 1;

        //$('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.expenseGlAutoID').html(expneceGL);
        //$('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.liabilityGlAutoID').html(liabilityGL);
        //$('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.ifSlab').html(ifSlab);
        $(".select2").select2();
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });


    function saveJVDetails() {
        var $form = $('#jv_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'RJVMasterAutoId', 'value': RJVMasterAutoId});

        $('select[name="gl_code[]"] option:selected').each(function () {
            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Recurring_je/save_gl_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#jv_detail_form')[0].reset();
                    $("#segment_gl").select2("");
                    $("#gl_code").select2("");

                    $('#jv_detail_modal').modal('hide');

                    debitNoteDetailsID = null;
                    fetch_journal_entry_detail();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function updateJVDetails() {
        var $form = $('#jv_detail_edit_form');
        var data = $form.serializeArray();
        data.push({'name': 'RJVMasterAutoId', 'value': RJVMasterAutoId});
        data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Recurring_je/update_gl_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                if (data['status']) {
                    $('#jv_detail_edit_form')[0].reset();
                    $("#edit_segment_gl").select2("");
                    $("#edit_gl_code").select2("");
                    $('#jv_detail_edit_modal').modal('hide');
                    debitNoteDetailsID = null;
                    fetch_journal_entry_detail();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function makeZero(fieldName, item, value) {
        if(value == ''){

        }else{
            $(item).closest('tr').find('input[name="' + fieldName + '[]"]').val('0')
        }

    }

    function makeZero_edit(fieldName) {
        $('input[name="' + fieldName + '"]').val('0')
    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if(number.length>1 && charCode == 46){
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if( caratPos > dotPos && dotPos>-(currency_decimal-1) && (number[1] && number[1].length > (currency_decimal-1))){
            return false;
        }
        return true;
    }

    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_segmentBase_projectID_income(segment) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment.value},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(segment).closest('tr').find('.div_projectID_income').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_segmentBase_projectID_incomeEdit(segment) {
        var type = 'income';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment.value,type:type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#edit_div_projectID_income').html(data);
                $('.select2').select2();
                if (projectID_income) {
                    $("#projectID_income").val(projectID_income).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function get_currency_decimal_places(CurrencyID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {CurrencyID: CurrencyID},
            url: "<?php echo site_url('Journal_entry/get_currency_decimal_places'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                currency_decimal=data['DecimalPlaces'];
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

</script>