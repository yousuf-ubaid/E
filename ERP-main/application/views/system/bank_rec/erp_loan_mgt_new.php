<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_tr_lm_new_loan');
echo head_page($title, false);

/*echo head_page('New Loan', false);*/
$financeyear_arr = all_financeyear_drop(true);
$date_format_policy = date_format_policy();
$financeyearperiodYN = getPolicyValues('FPC', 'All');

$current_date = format_date($this->common_data['current_date']);
$currency_arr = all_currency_new_drop();
$financeyear_arr = all_financeyear_drop();
$gl_code_arr = company_PL_account_drop();
$segment_arr = fetch_segment();
$gl_code_arr_new = dropdown_liability_gl();
$expense_gl =dropdown_expense_gl();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('treasury_common_step_one');?><!--Step 1--> - <?php echo $this->lang->line('treasury_tr_lm_loan_header');?><!--Loan Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="loanSettlementTable()" data-toggle="tab"><?php echo $this->lang->line('treasury_common_step_two');?><!--Step 2--> - <?php echo $this->lang->line('treasury_tr_lm_loan_detail');?><!--Loan Detail--></a>
    <!--<a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation()" data-toggle="tab">Step 3 - JV
        Confirmation</a>-->
        <a class="btn btn-default btn-wizard" href="#step3" data-toggle="tab">Step 3 -  GL Mapping</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="Journal_entry_form"'); ?>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border"><?php echo $this->lang->line('treasury_common_general');?><!--General--></legend>
            <div class="row">
                <div class="col-md-12">

                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('treasury_common_document_code');?><!--Document Code--></label>
                        <input type="text" class="form-control " id="documentCode" name="documentCode">
                    </div>


                    <div class="form-group col-sm-4">
                        <label for="JVType"> <?php echo $this->lang->line('common_bank');?><!--Bank--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('bankID', company_bank_account_drop(), '', 'class="form-control select2" id="bankID" required "'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('common_amount');?><!--Amount--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="amount" name="amount">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-sm-4">
                        <label for="JVType"><?php echo $this->lang->line('common_currency');?> <!--Currency--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('currency', all_currency_new_drop(), '', 'class="form-control select2" id="currency" required "'); ?>
                    </div>


                    <div class="form-group col-sm-4">
                        <label for=""><!--Date of Drawdown--><?php echo $this->lang->line('treasury_tr_lm_date_of_drawdown');?> <?php required_mark(); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="documentDate" value="<?php echo date('Y-m-d')?>"
                                   id="documentDate"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="JVType"> <?php echo $this->lang->line('common_status');?><!--Status--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('status', erp_bank_facilityStatus(), '', 'class="form-control select2" id="status" required "'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('common_narration');?><!--Narration--></label>
                        <input type="text" class="form-control " id="narration" name="narration">
                    </div>
                </div>

            </div>
        </fieldset>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border"><?php echo $this->lang->line('treasury_common_payment');?><!--Payment--></legend>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('treasury_tr_lm_no_of_installment');?><!--No of Installment--> <?php required_mark(); ?></label>
                        <div class="input-group">
                        <span class="input-group-addon">
                         <select class="" id="installmentID" onchange="getfacilityfatefrom()" name="installmentID">
                             <option value="1"><?php echo $this->lang->line('treasury_tr_lm_monthly');?><!--Monthly--></option>
                             <option  value="2"><?php echo $this->lang->line('treasury_tr_lm_quartely');?><!--Quaterly--></option>
                             </select>
                        </span>
                            <input type="text" id="noInstallment" name="noInstallment" class="form-control">
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('treasury_tr_lm_facility_date_from');?><!--Facility Date From--> <?php required_mark(); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input  type="text" name="facilityDateFrom" id="facilityDateFrom"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('treasury_tr_lm_facility_date_to');?><!--Facility Date To--> <?php required_mark(); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="facilityDateTo" id="facilityDateTo"
                                   class="form-control" required>
                        </div>
                    </div>
                </div>

            </div>
        </fieldset>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border"><?php echo $this->lang->line('treasury_common_interest');?><!--Interest--></legend>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-4">
                        <label for="JVType"> <?php echo $this->lang->line('treasury_tr_lm_type_of_rate');?><!--Type of Rate--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('ratetypeID', erp_bankfacilityrateType(), '', 'class="form-control select2" id="ratetypeID" required "'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="JVType"> <?php echo $this->lang->line('treasury_tr_lm_rate_of_interest');?><!--Rate of Interest--> <?php required_mark(); ?></label>
                        <div class="input-group">
                            <input type="number" step="any" id="rateOfInterest" name="rateOfInterest" class="form-control">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="JVType"> <?php echo $this->lang->line('treasury_tr_lm_interest_payment');?><!--Interest Payment--> <?php required_mark(); ?></label>
                        <select class="form-control" id="interestPayment" onchange="getfacilityfatefrom()" name="interestPayment">
                            <option value="1"><?php echo $this->lang->line('treasury_tr_lm_monthly');?><!--Monthly--></option>
                            <option  value="2"><?php echo $this->lang->line('treasury_tr_lm_quartely');?><!--Quaterly--></option>
                        </select>
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('treasury_tr_lm_initial_interest_payment');?><!--Initial Interest Payment--> <?php required_mark(); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="DateofInterestPayment" id="DateofInterestPayment"
                                   class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>



        </fieldset>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary"  type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs ">
          <!--      <li class="active"><a data-toggle="tab" href="#tab_3" aria-expanded="false">Utlization</a></li>-->
                <li class="active"><a data-toggle="tab" href="#tab_4" aria-expanded="false"><?php echo $this->lang->line('treasury_tr_lm_setltlement');?><!--Settlement--></a></li>

            </ul>
            <div class="tab-content">
                <div id="tab_4" class="tab-pane active ">
                    <div id="divSettlementTable">
                    <table id="loanSettlementTable" class="<?php echo table_class() ?>">
                        <thead style="">
                        <tr>
                            <th rowspan="2" style="width: 50px"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                            <!--   <th rowspan="2" style="">Reference No</th>-->



                            <th rowspan="2"  style="width: 100px"><?php echo $this->lang->line('treasury_bta_opening_balance');?><!--Opening Balance--></th>
                            <th rowspan="2" style="width: 100px"><?php echo $this->lang->line('treasury_tr_lm_principal_payment');?><!--Principal Repayment--></th>
                            <th rowspan="2"  style="width: 100px"><?php echo $this->lang->line('treasury_common_closing_balance');?><!--Closing Balance--></th>
                            <th rowspan="2" style="width: 100px"><?php echo $this->lang->line('common_days');?><!--Days--></th>
                            <th  colspan="3"  style="width: 100px;text-align: center"><?php echo $this->lang->line('treasury_common_interest');?><!--Interest--></th>
                            <th rowspan="2"><?php echo $this->lang->line('treasury_tr_lm_total_payment');?><!--Total Payment--></th>
                          <!--  <th rowspan="2" style="width: 10px">&nbsp;</th>-->


                        </tr>
                        <tr>

                            <th style="width:100px;"><?php echo $this->lang->line('treasury_tr_lm_fixed');?><!--Fixed--></th>
                            <th style="width:100px;"><?php echo $this->lang->line('treasury_tr_lm_variable');?><!--Variable--> (LIBOR)%</th>
                            <th><?php echo $this->lang->line('treasury_tr_lm_variable_amount');?><!--Variable Amount--></th>


                        </tr>
                        </thead>
                        </table>
                        </div>



                    </div>
                <div id="tab_3" class="tab-pane  ">
                    <div id="">
                    <table id="loanUtlizationTable" class="<?php echo table_class() ?>" ">
                    <thead style=" ">
                    <tr>
                        <th>CompanyID</th>

                        <th>Date</th>


                        <th><?php echo $this->lang->line('treasury_common_reference_no');?><!--Reference No--></th>

                        <th><?php echo $this->lang->line('treasury_tr_lm_system_document_ref');?><!--System Document Ref-->.</th>
                        <th><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
                        <th><?php echo $this->lang->line('treasury_tr_lm_principal_amount');?><!--Principle Amount--></th>
                        <!--  <th>Interest Amount</th>-->
                    <!--    <th style="width: 20px">&nbsp;</th>-->
                        
                    </tr>
                    </thead>
                    </table>
                </div>
                </div>
            </div>
                <!--        <div class="row">

                            <div class="col-md-12 pull-right">
                                <button type="button" onclick="jv_detail_modal()" class="btn btn-primary pull-right"><i
                                        class="fa fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>
                -->      <!--  <table class="<?php /*echo table_class(); */?>">
                            <thead>
                            <tr>
                                <th colspan="5">GL Details</th>
                                <th colspan="2"> Amount <span class="currency">&nbsp;</span></th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">GL Code</th>
                                <th style="min-width: 35%">GL Code Description</th>
                                <th style="min-width: 20%">Description</th>
                                <th style="min-width: 10%">Segment</th>
                                <th style="min-width: 15%">Debit</th>
                                <th style="min-width: 15%">Credit</th>
                                <th style="min-width: 5%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="gl_table_body">
                            <tr class="danger">
                                <td colspan="7" class="text-center"><b>No Records Found</b></td>
                            </tr>
                            </tbody>
                            <tfoot id="gl_table_tfoot">

                            </tfoot>
                        </table>-->
                    <!--    <hr>
                        <div class="text-right m-t-xs">
                            <button class="btn btn-default prev">Previous</button>

                        </div>--></div>

                        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" onClick="openLoanMgtGlMapping()"  type="submit">Next</button>
        </div>

    </div>
    <div id="step3" class="tab-pane">
        <?php echo form_open('', 'role="form" id="Journal_entry_form_gl"'); ?>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border"><?php echo $this->lang->line('treasury_common_general');?><!--General--></legend>
            <div class="row">
                <div class="col-md-12">

                    <!-- <div class="form-group col-sm-6">
                        <label for="">Principal Amount <?php required_mark(); ?></label>
                        <input type="text" class="form-control number" id="principalAmount" name="principalAmount">
                    </div> -->

                    <div class="form-group col-sm-6">
                        <label for="JVType">Principal Amount GL code <?php required_mark(); ?></label>
                        <?php echo form_dropdown('principalGlCode', $gl_code_arr_new, '', 'class="form-control select2" id="principalGlCode" required'); ?>
                    </div>
                    
                </div>
                <div class="col-md-12">
                    
                    <!-- <div class="form-group col-sm-6">
                        <label for="">Interest Amount <?php required_mark(); ?></label>
                        <input type="text" class="form-control number" id="interestAmount" name="interestAmount">
                    </div> -->

                    <div class="form-group col-sm-6">
                        <label for="JVType">Interest Amount GL code <?php required_mark(); ?></label>
                        <?php echo form_dropdown('interestGlCode', $expense_gl, '', 'class="form-control select2" id="interestGlCode" required'); ?>
                    </div>
                    
                </div>

                <div class="col-md-12">
                    <!-- <div class="form-group col-sm-6">
                        <label for="">Liability Amount <?php required_mark(); ?></label>
                        <input type="text" class="form-control number" id="liabilityAmount" name="liabilityAmount">
                    </div> -->
                    <div class="form-group col-sm-6">
                        <label for="JVType">LIBOR Amount GL code <?php required_mark(); ?></label>
                        <?php echo form_dropdown('libilityGlCode', $expense_gl, '', 'class="form-control select2" id="libilityGlCode" required'); ?>
                    </div>
                </div>

            </div>
        </fieldset>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="button" onClick="saveLoanMgtGlMapping()"><?php echo $this->lang->line('common_save_change');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="jv_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('treasury_tr_lm_gl_detail');?><!--GL Detail--></h5>
            </div>
            <form role="form" id="jv_detail_form" class="form-horizontal">
                <input type="hidden" id="xJVDetailAutoID" name="JVDetailAutoID">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('gl_code', $gl_code_arr, '', 'class="form-control select2" id="gl_code" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_segment');?><!--Segment--> </label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_gl" '); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_type');?><!--Type--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('gl_type', array('Cr' => 'Credit', 'Dr' => 'Debit'), 'Cr', 'class="form-control" id="gl_type" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_amount');?><!--Amount--> <?php required_mark(); ?></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency"> (LKR)</span></div>
                                <input type="text" name="amount" id="amount" value="00" class="form-control number">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <textarea class="form-control" rows="2" id="description" name="description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary" type=""><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('#interestPayment').val(1);
        $('#installmentID').val(1);
        getfacilityfatefrom();


        masterID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(masterID){
          $('[href=#step2]').tab('show');
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $('[href=#step2]').removeClass('btn-default');
            $('[href=#step2]').addClass('btn-primary');
            $('.btn-wizard').removeClass('disabled');
            load_journal_entry_header();
            loanSettlementTable();


        }
        else {
            $('.btn-wizard').addClass('disabled');
        }

        $('.headerclose').click(function () {

            fetchPage('system/bank_rec/erp_loan_management','','Loan Management')
        });
        $('.select2').select2();

        $('#documentDate').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            setinitlainterstpaymentDate();
            $('#Journal_entry_form').bootstrapValidator('revalidateField', 'documentDate');
            $(this).datepicker('hide');
        });
        $('#facilityDateFrom').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            getfacilityfatefrom();
            $('#Journal_entry_form').bootstrapValidator('revalidateField', 'facilityDateFrom');
            $(this).datepicker('hide');
        });
        $('#facilityDateTo').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#Journal_entry_form').bootstrapValidator('revalidateField', 'facilityDateTo');
            $(this).datepicker('hide');
        });
        $('#DateofInterestPayment').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#Journal_entry_form').bootstrapValidator('revalidateField', 'DateofInterestPayment');
            $(this).datepicker('hide');
        });

        number_validation();
        $('#jv_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
            excluded: [':disabled'],
            fields: {
                gl_code: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gl_code_is_required');?>.'}}},/*GL code is required*/
                amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_amount_is_required');?>.'}}},/*Amount is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'JVMasterAutoId', 'value': JVMasterAutoId});
            /*    data.push({'name': 'JVDetailAutoID', 'value': JVDetailAutoID});*/
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

    function loanUtlizationTable(){
        var Otable3 = $('#loanUtlizationTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/bank'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {

            },
            "aoColumns": [
                {"mData": "date"},
                {"mData": "principleAmount"},
                {"mData": "principalRepayment"},
                {"mData": "closingBalance"},
                {"mData": "installmentDueDays"},
                {"mData": "interestAmount"},
                {"mData": "variableLibor"},
                {"mData": "variableAmount"},
                {"mData": "variableTotal"}

            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({ "name": "masterID","value": masterID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    $('#Journal_entry_form').bootstrapValidator({

            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                bankID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_bank_is_required');?>.'}}},/*Bank is required*/
                currency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_narration_type_is_required');?>.'}}},/*Narration Type is required*/
                amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_amount_type_is_required');?>.'}}},/*Amount Type is required*/
                facilityDateFrom: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_facility_date_from_is_required');?>.'}}},/*Facility Date From is required*/
                noInstallment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_tr_lm_no_of_installment_is_required');?>.'}}},/*Number Of Installment is required*/
                facilityDateTo: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_tr_lm_facility_date_to_is_required');?>.'}}},/*Facility Date To is required*/
                ratetypeID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_tr_lm_facility_date_to_is_required');?>.'}}},/*Facility Date To is required*/
                rateOfInterest: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_tr_lm_rate_of_interest_is_required');?>.'}}},/*Rate Of Interest is required*/
                interestPayment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_tr_lm_rate_of_interest_payment_is_required');?>.'}}},/*Interest Payment is required*/
                DateofInterestPayment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_tr_lm_rate_of_date_of_interest_payment_is_required');?>.'}}},/*Date Of Interest Payment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
         data.push({'name': 'masterID', 'value': masterID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Bank_rec/save_loanManagementMaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        masterID=data['bankFacilityID'];

                        if(masterID){
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                            loanSettlementTable()

                        }
                    }

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


    function setinitlainterstpaymentDate() {
        $.ajax({
            type: "POST",
           /* url: "ajax/ajax-get-treasury-load-facility-interest-date.php",*/
            url: "<?php echo site_url('Bank_rec/setinitlainterstpaymentDate'); ?>",
            data: {documentDate: $("#documentDate").val(),installmentID:$('#interestPayment').val()},

            dataType: "json",
            cache: false,

            beforeSend: function () {
            },

            success: function (data) {
                $("#DateofInterestPayment").val(data['value']);

            }
        });
        return false;
    }

    function getfacilityfatefrom() {
        setinitlainterstpaymentDate();
        $.ajax({
            type: "POST",
        /*    url: "ajax/ajax-get-treasury-load-facility-date-from.php",*/
            url: "<?php echo site_url('Bank_rec/getfacilityfatefrom'); ?>",
            data: {datefrom: $("#facilityDateFrom").val(),installmentType:$('#installmentID').val(),noInstallment:$('#noInstallment').val()},
            dataType: "json",
            cache: false,

            beforeSend: function () {
            },

            success: function (data) {
                $("#facilityDateTo").val(data['value']);

            }
        });
        return false;
    }

    function saveLoanMgtGlMapping(){

        var data= $('#Journal_entry_form_gl').serializeArray();
        data.push({'name': 'masterID', 'value': masterID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Bank_rec/saveLoanMgtGlMapping'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data['status']==true) {
                    //fetchPage('system/bank_rec/erp_loan_management','','Bank Transfer');
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

    function openLoanMgtGlMapping(){

        $('[href=#step3]').tab('show');
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $('[href=#step3]').removeClass('btn-default');
        $('[href=#step3]').addClass('btn-primary');

    }



 function load_journal_entry_header() {
        if (masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'masterID': masterID},
                url: "<?php echo site_url('Bank_rec/bank_facilityLoanHeader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {


                       $("#amount").val(data['amount']);
                        $("#bankID").val(data['bankID']).change();
                        $("#currency").val(data['currencyID']).change();
                        $("#documentCode").val(data['documentCode']);$("#documentDate").val(data['documentDate']);
                        $("#facilityDateFrom").val(data['facilityDateFrom']);
                        $("#facilityDateTo").val(data['facilityDateTo']);
                        $("#installmentID").val(data['installmentID']);
                        $("#installmentType").val(data['installmentType']);
                        $("#DateofInterestPayment").val(data['interestPaymentDate']);
                        $("#interestPaymentID").val(data['interestPaymentID']);
                        $("#interestPaymentType").val(data['interestPaymentType']);
                        $("#narration").val(data['narration']);
                        $("#noInstallment").val(data['noInstallment']);
                        $("#rateOfInterest").val(data['rateOfInterest']);
                        $("#ratetypeID").val(data['ratetypeID']).change();
                        $("#status").val(data['status']).change();
                        $("#typeOfFacility").val(data['typeOfFacility']);

                      //  $("#principalAmount").val(data['principalAmount']);
                        $("#principalGlCode").val(data['principalGlCode']).change();

                     //   $("#interestAmount").val(data['interestAmount']);
                        $("#interestGlCode").val(data['interestGlCode']).change();

                      //  $("#liabilityAmount").val(data['liabilityAmount']);
                        $("#libilityGlCode").val(data['libilityGlCode']).change();

                       /* */
                        /*companyID*/
                       /* facilityCode*/
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


    function jv_detail_modal() {
        if (JVMasterAutoId) {
            $('#gl_code').val('').change();
            $('#segment_gl').val('').change();
            $('#jv_detail_form')[0].reset();
            $('#xJVDetailAutoID').val('');
            $('#jv_detail_form').bootstrapValidator('resetForm', true);
            $("#jv_detail_modal").modal({backdrop: "static"});
        }
    }



    function delete_item(id, value) {
        if (JVMasterAutoId) {
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
                        data: {'JVDetailAutoID': id},
                        url: "<?php echo site_url('Journal_entry/delete_Journal_entry_detail'); ?>",
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
        ;
    }

    function save_payment_voucher_LO_settlement(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to create payment voucher",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'bankFacilityDetailID':id},
                    url :"<?php echo site_url('Bank_rec/save_payment_voucher_LO_settlement'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){

                    if (data[0] == 's') {
                        Payment_voucher_confirm_lo(data[2]);
                        loanSettlementTable();
                    } else {
                        stopLoad();
                        //myAlert(data[0], data[1]);
                        refreshNotifications(true);
                    }

                    },error : function(){
                        
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
        });
    }

    function loanSettlementTable() {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Bank_rec/bankfacilityloansettlement'); ?>",
            data: {masterID: masterID},
            dataType: "html",
            cache: false,

            beforeSend: function () {
            },

            success: function (data) {
                $("#divSettlementTable").html(data);

            }
        });
        return false;
    }


    function Payment_voucher_confirm_lo(PayVoucherAutoId){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'PayVoucherAutoId': PayVoucherAutoId},
            url: "<?php echo site_url('Payment_voucher/payment_confirmation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                } else if(data['error']== 2)
                    {
                        myAlert('w',data['message']);
                    }
                    else {
                    //refreshNotifications(true);
                    myAlert('s', data['message']);
                    swal("Success", "Payment Voucher " + data['code'] + " Created Successfully " , "success");
                    //fetchPage('system/sales/commision_payment', PayVoucherAutoId, 'Commission Payment');
                }

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

</script>