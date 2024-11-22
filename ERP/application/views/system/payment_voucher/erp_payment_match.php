<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'],false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr        = all_currency_new_drop();
$segment_arr         = fetch_segment();
$pID = $this->input->post('page_id');
$supplier_arr = all_supplier_drop(true,1);
if($pID != '') {
    $grvAutoid = $pID;
    $Documentid = 'PVM';
    $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pID,$Documentid);
    if($supplieridcurrentdoc['isActive'] == 0)
    {
        $supplier_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

    }
}
$createmasterrecords = getPolicyValues('CMR','All');
$financeyear_arr = all_financeyear_drop(true);
$supplier_arr_master = array(''=>'Select Supplier');
$country = load_country_drop();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_one');?><!--Step 1--> - <?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching_header');?><!--Payment Matching Header--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_detail();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_two');?><!--Step 2--> - <?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching_deail');?><!--Payment Matching Detail--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_three');?><!--Step 3--> - <?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching_confirmation');?></span>
            </a>
           
        </div>

</div><hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('','role="form" id="payment_match_form"'); ?>
            <div class="row" >
                <div class="form-group col-sm-4">
                    <label ><?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching_date');?><!--Payment Matching Date--> <?php  required_mark(); ?></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="matchDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="matchDate" class="form-control" required>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label ><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                    <input type="text" name="refNo" id="refNo" class="form-control" >
                </div>
                <div class="form-group col-sm-4">

                    <label for="supplierID"><?php echo $this->lang->line('common_supplier');?><!--Supplier--> <?php  required_mark(); ?></label>
                    <?php if($createmasterrecords==1){?>
                    <div class="input-group">
                        <div id="div_supplier_drop">
                            <?php echo form_dropdown('supplierID', $supplier_arr_master, '', 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Supplier" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                    <?php }else {?>

                        <?php echo form_dropdown('supplierID', $supplier_arr, '','class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value)" required');
                    //onchange="fetch_supplier_currency_by_id(this.value)" ?>
                    <?php }?>

                </div>   
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="supplier_currency"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_currency');?><!--Payment Currency--> <?php  required_mark(); ?></label>
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currency'],'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'PVM\')" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label ><?php echo $this->lang->line('accounts_payable_tr_pv_payment_memo');?><!--Memo--> </label>
                    <textarea class="form-control" rows="3" name="Narration" id="Narration" ></textarea>
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
            <div class="col-md-9">
            </div>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-primary pull-right" onclick="payment_match_detail_model()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_trans_pm_new_match');?><!--New Match--> </button>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="payment_voucher_detail_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th colspan="3"> <?php echo $this->lang->line('accounts_payable_tr_pv_payment_voucher');?> <!--Payment Voucher--> </th>
                    <th colspan="2"><?php echo $this->lang->line('accounts_payable_tr_pv_invoice');?><!--Invoice--> </th>
                    <th colspan="3"><?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('accounts_payable_trans_pm_match');?><!--Match--> </th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                </tr>
                </thead>
                <tbody id="table_body">

                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="5"><?php echo $this->lang->line('common_total');?><!--Total--> <span class="currency"> ( LKR )</span></td>
                        <td class="text-right total" id="t_total">0.00</td>
                        <td class="text-right" colspan="1">&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank" href="<?php echo site_url('Payment_voucher/load_pv_match_conformation/'); ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div><hr>
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="paymentMatch_attachment_label">Modal title</h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="paymentMatch_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" ><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<div class="modal fade" id="payment_match_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form class="form-horizontal" id="payment_voucher_detail_form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching');?><!--Payment Matching--></h4>
            </div>
            <div class="modal-body">
                <table class="table ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('accounts_payable_pv_pv_code');?><!--PV Code--></th>
                            <th><?php echo $this->lang->line('accounts_payable_trans_pm_pv_date');?><!--PV Date--></th>
                            <th><?php echo $this->lang->line('accounts_payable_tr_pv_po_code');?><!--PO Code--></th>
                            <th><?php echo $this->lang->line('accounts_payable_trans_pm_full_advance');?><!--Full Advance--></th>
                            <th><?php echo $this->lang->line('accounts_payable_trans_pm_balance_advance');?><!--Balance Advance--></th>
                            <th><?php echo $this->lang->line('accounts_payable_trans_pm_match_invoice');?><!--Match Invoice--></th>
                            <th>Balance Amount</th>
                            <th><?php echo $this->lang->line('accounts_payable_trans_pm_to_pay');?><!--To Pay--></th>
                        </tr>
                    </thead>
                    <tbody id="match_ad_table">
                        <tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="save_match_items()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
            </div>
        </form>
    </div>
  </div>
</div>


<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Add New Supplier</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('','role="form" id="suppliermaster_form"'); ?>
                <input type="hidden" id="isActive" name="isActive" value="1">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Secondary Code<!--Secondary Code--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="suppliercode" name="suppliercode">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierName">Company Name / Name<!--Company Name / Name--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierName">Name On Cheque<!--Name On Cheque--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('common_category');?><!--Category--></label>
                            <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="liabilityAccount">Liability Account<!--Liability Account--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierCurrency">Currency<!--Currency--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('common_Country');?><!--Country--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Tax Group<!--Tax Group--></label>
                            <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">VAT Identification No</label>
                            <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="supplierTelephone"><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierEmail" name="supplierEmail">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierFax">FAX<!--FAX--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierFax" name="supplierFax">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="suppliersupplierCreditPeriod">Credit Period<!--Credit Period--></label>
                            <div class="input-group">
                                <div class="input-group-addon">Month<!--Month--></div>
                                <input type="text" class="form-control number" id="supplierCreditPeriod"
                                       name="supplierCreditPeriod">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="suppliersupplierCreditLimit">Credit Limit<!--Credit Limit--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency">LKR</span></div>
                                <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierUrl">URL</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierUrl" name="supplierUrl">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="supplierAddress1">Address 1<!--Address 1--></label>
                            <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1"></textarea>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierAddress2">Address 2<!--Address 2--></label>
                            <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2"></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="save_supplier_master()">Add Supplier </button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
var matchID;
var documentCurrency;
var currency_decimal;
$( document ).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/payment_voucher/payment_match_management',matchID,'Payment Match');
    });
    $('.select2').select2();
    matchID = null;
    documentCurrency = null;
    currency_decimal = 2;
    number_validation();

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        $('#payment_match_form').bootstrapValidator('revalidateField', 'matchDate');
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass('btn-primary');
    });

    p_id         = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    if (p_id) {
        matchID =p_id;
        <?php if($createmasterrecords==1){?>
        fetch_supplierdrop('',matchID)
        <?php }?>
        load_payment_match_header();
    }else{
        <?php if($createmasterrecords==1){?>
        fetch_supplierdrop();
        <?php }?>
        $('.btn-wizard').addClass('disabled');
    }
    
    $('#payment_match_form').bootstrapValidator({
        live            : 'enabled',
        message         : '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded        : [':disabled'],
        fields          : {
            matchDate               : {validators : {notEmpty:{message:'<?php echo $this->lang->line('accounts_payable_trans_pm_payment_match_date_is_required');?>.'}}},/*Payment Match Date is required*/
           // supplierID              : {validators : {notEmpty:{message:'<?php echo $this->lang->line('accounts_payable_supplier_is_required');?>.'}}},/*Supplier is required*/
            transactionCurrencyID   : {validators : {notEmpty:{message:'<?php echo $this->lang->line('accounts_payable_tr_pv_tranasaction_currency_is_required');?>.'}}}
        },
    }).on('success.form.bv', function(e) {
        $("#supplierID").prop("disabled", false);
        $("#transactionCurrencyID").prop("disabled", false);
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name' : 'matchID', 'value' : matchID });
        data.push({'name' : 'currency_code', 'value' : $('#transactionCurrencyID option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Payment_voucher/save_payment_match_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if (data['status']) {
                    matchID = data['last_id'];
                    documentCurrency = data['currency'];
                    currency_decimal = data['decimal'];
                    $('.currency').html('( '+documentCurrency+' )');
                    $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>/"+matchID);
                    $('.btn-wizard').removeClass('disabled');
                    $("#supplierID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    fetch_detail();
                    $('[href=#step2]').tab('show');
                 }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });
});

function currency_validation(CurrencyID,documentID){
    if (CurrencyID) {
        partyAutoID = $('#supplierID').val();
        currency_validation_modal(CurrencyID,documentID,partyAutoID,'SUP');
    }
}


function confirmation(){
    if (matchID) {
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'matchID':matchID},
                url  :"<?php echo site_url('Payment_voucher/payment_match_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0]=='s'){
                        fetchPage('system/payment_voucher/payment_match_management',matchID,'Payment Match');
                    }

                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
    };
}

function load_conformation(){
    if (matchID) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'html',
            data : {'matchID':matchID,'html':true},
            url :"<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                $('#conform_body').html(data);
                attachment_modal_paymentMatch(matchID, "<?php echo $this->lang->line('accounts_payable_trans_pm_payment_matching');?> ", "PVM");/*Payment Matching*/
                stopLoad();
                refreshNotifications(true);
            },error : function(){
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
}

function fetch_detail(){
    $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'matchID':matchID},
            url :"<?php echo site_url('Payment_voucher/fetch_match_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                $('#table_body').empty();
                $('#t_total').html(0);x=1;
                if(jQuery.isEmptyObject(data)){
                    $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    $("#supplierID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                }
                else{
                    $("#supplierID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                    tot_amount = 0; receivedQty=0;
                    //currency_decimal  = 2;
                    $.each(data, function (key, value) {
                      $('#table_body').append('<tr><td>'+x+'</td><td>'+value['pvCode']+'</td><td>'+value['PVdate']+'</td><td>'+value['bookingInvCode']+'</td><td>'+value['bookingDate']+'</td><td class="text-right" >'+parseFloat(value['transactionAmount']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',')+'</td><td class="text-right"><a onclick="delete_pv_match_detail('+value['matchDetailID']+')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
                      tot_amount +=parseFloat(value['transactionAmount']);
                      currency_decimal = value['transactionCurrencyDecimalPlaces'];
                      x++;
                    });
                    $('#t_total').html(parseFloat(tot_amount).formatMoney(currency_decimal, '.', ','));
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
}


function payment_match_detail_model(){
    $.ajax({
        async : true,
        type : 'post',
        dataType : 'json',
        data : {'matchID':matchID},
        url :"<?php echo site_url('Payment_voucher/fetch_pv_advance_detail'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success : function(data){
            stopLoad();
            $('#match_ad_table').empty();x=1;
            if(jQuery.isEmptyObject(data['payment'])){
                $('#match_ad_table').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
            }else{
                $.each(data['payment'], function (key, value) {
                    var ad_detail   = ' - ';
                    var paid_amount = 0;
                    if (value['purchaseOrderID']!=0) {
                       ad_detail = value['POCode'];//+' '+value['PODescription'];
                    }
                    if (value['paid']!='null') {
                        paid_amount = value['paid'];
                    }
                    var balamnt=parseFloat((value['transactionAmount']-paid_amount)).formatMoney(currency_decimal, '.', ',');
                    var balamntchk=parseFloat((value['transactionAmount']-paid_amount));
                    if(balamntchk>0){
                        $('#match_ad_table').append('<tr><td>'+x+'</td><td>'+value['PVcode']+'</td><td>'+value['PVdate']+'</td><td>'+ad_detail+'</td><td class="text-right">'+parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',')+'</td><td class="text-right">'+parseFloat((value['transactionAmount']-paid_amount)).formatMoney(currency_decimal, '.', ',')+' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty('+value['payVoucherDetailAutoID']+','+(value['transactionAmount']-paid_amount)+')" aria-hidden="true"></i></td><td class="text-right"><select class="inv_drop" onchange="showBalanceAmount('+value['payVoucherDetailAutoID']+')"  id="inv_'+value['payVoucherDetailAutoID']+'"><option value=""><?php echo $this->lang->line('accounts_payable_trans_pm_select_invoice');?><!--Select Invoice--></option></select></td><td class="text-right" id="balamount_'+value['payVoucherDetailAutoID']+'"></td><td class="text-right"><input type="text" name="amount[]" style="width: 100px" id="amount_'+value['payVoucherDetailAutoID']+'" onkeyup="select_check_box(this,'+value['payVoucherDetailAutoID']+','+(value['transactionAmount']-paid_amount)+');" onkeypress="return validateFloatKeyPress(this,event);"  class="number"></td><td class="text-right" style="display:none;"><input class="checkbox" id="check_'+value['payVoucherDetailAutoID']+'" type="checkbox" value="'+value['payVoucherDetailAutoID']+'"></td></tr>');
                    }
                    x++;
                });

                if (!jQuery.isEmptyObject(data['invoice'])) {
                    $('.inv_drop').empty();
                    var mySelect = $('.inv_drop');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data['invoice'], function (val, text) {
                        mySelect.append($('<option></option>').val(text['InvoiceAutoID']).html(text['bookingInvCode']));
                    });
                }
            }
            $("#payment_match_model").modal({backdrop: "static"});
            number_validation();
        },error : function(){
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            stopLoad();
            refreshNotifications(true);
        }
    });
}

function select_check_box(data,id,total){
    $( "#check_"+id ).prop( "checked", false);
    var balamount=$('#balamount_'+id).html();
    balamount = balamount.replace(/,/g, "");
    var inv = $('#inv_'+id).val();
    if(inv==''){
        myAlert('w', 'Match Invoice canot be empty');/**/
        $( "#amount_"+id ).val('');
        return false
    }
    if(data.value > 0){
        if(total >= data.value ){
            $( "#check_"+id ).prop( "checked", true);
            if(parseFloat(balamount)<data.value){
                $( "#check_"+id ).prop( "checked", false);
                $( "#amount_"+id ).val('');
                myAlert('w', 'Payment Matching Amount cannot be greater than Invoice Balance Amount');/**/
                return false
            }
        }else{
            $( "#check_"+id ).prop( "checked", false);
            $( "#amount_"+id ).val('');
            myAlert('w', 'Payment Matching Amount cannot be greater than Advance Balance Amount');
            return false
        }

        var amnt=0;
        $('#match_ad_table input:checked').each(function() {
            var docid=$(this).val();
            if($('#inv_'+docid).val()==$('#inv_'+id).val()){
                amnt+=parseFloat($("#amount_"+docid ).val());
            }
        });

        if(parseFloat(balamount)<amnt){
            $( "#check_"+id ).prop( "checked", false);
            $( "#amount_"+id ).val('');
            myAlert('w', 'Payment Matching Amount cannot be greater than Invoice Balance Amount');/**/
            return false
        }
    }
}

function save_match_items(){
    var selected    = [];
    var amount      = [];
    var invoice     = [];
    $('#match_ad_table input:checked').each(function() {
        selected.push($(this).val());
        amount.push($('#amount_'+$(this).val()).val());
        invoice.push($('#inv_'+$(this).val()).val());
    });
    if (!jQuery.isEmptyObject(selected)) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'payVoucherDetailAutoID':selected,'amounts':amount,'InvoiceAutoID':invoice,'matchID':matchID},
            url :"<?php echo site_url('Payment_voucher/save_match_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                myAlert(data['type'],data['messsage'], 1000);
                stopLoad();
                if (data['status']) {
                    $('#payment_match_model').modal('hide');
                    setTimeout(function(){ fetch_detail(); }, 300);
                }
            },error : function(){
                $('#payment_match_model').modal('hide');
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }
}

function load_payment_match_header(){
    if (matchID) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'matchID':matchID},
            url :"<?php echo site_url('Payment_voucher/load_payment_match_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                if(!jQuery.isEmptyObject(data)){
                    $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>/"+matchID);
                    $('#matchDate').val(data['matchDate']);
                    $('#refNo').val(data['refNo']);
                    $('#supplierID').val(data['supplierID']).change();
                    $('#Narration').val(data['Narration']);
                    $('#refNo').val(data['refNo']);
                    fetch_detail();
                    setTimeout(function () {
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                    }, 1000);
                    currency_decimal= data['transactionCurrencyDecimalPlaces'];
                    documentCurrency = data['transactionCurrency'];
                    $('.currency').html('( '+documentCurrency+' )');
                    $('[href=#step2]').tab('show');
                    $('a[data-toggle="tab"]').removeClass('btn-primary');
                    $('a[data-toggle="tab"]').addClass('btn-default');
                    $('[href=#step2]').removeClass('btn-default');
                    $('[href=#step2]').addClass('btn-primary');
                }
                stopLoad();
                refreshNotifications(true);
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
}

function save_draft(){
    if (matchID) {
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('accounts_payable_you_want_to_save_this_file');?>",/*You want to save this file!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            fetchPage('system/payment_voucher/payment_match_management',matchID,'Payment Match');
        });
    };
}

function delete_pv_match_detail(id) {
        if (matchID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_payable_trans_are_you_want_to_delete');?>",/*You want to delete this file!*/
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
                        data: {'matchDetailID': id},
                        url: "<?php echo site_url('Payment_voucher/delete_pv_match_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function(){ fetch_detail(); }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        };
    }


function attachment_modal_paymentMatch(documentSystemCode, document_name, documentID) {
    if (documentSystemCode) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
            dataType: 'json',
            data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
            success: function (data) {
                $('#paymentMatch_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?> ");<!--Attachments-->
                $('#paymentMatch_attachment').empty();
                $('#paymentMatch_attachment').append('' +data+ '');

            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ajax_nav_container').html(xhr.responseText);
            }
        });
    }
}

function delete_paymentMatch_attachment(attachmentID, DocumentSystemCode,myFileName) {
    if (attachmentID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file!*/
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
                    data: {'attachmentID': attachmentID,'myFileName': myFileName},
                    url: "<?php echo site_url('Attachment/delete_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s','Deleted Successfully');
                            attachment_modal_paymentMatch(DocumentSystemCode, "Payment Match", "PVM");
                        }else{
                            myAlert('e','Deletion Failed');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
}



    function showBalanceAmount(id){
        var invid=$('#inv_'+id).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'InvoiceAutoID': invid},
            url: "<?php echo site_url('Payment_voucher/showBalanceAmount_matching'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#balamount_'+id).html(data);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
function link_employee_model() {
    $('#suppliercode').val('');
    $('#supplierName').val('');
    $('#vatIdNo').val('');
    $('#supplierTelephone').val('');
    $('#nameOnCheque').val('');
    $('#supplierEmail').val('');
    $('#supplierFax').val('');
    $('#supplierCreditPeriod').val('');
    $('#supplierCreditLimit').val('');
    $('#supplierUrl').val('');
    $('#supplierAddress2').val('');
    $('#supplierAddress1').val('');
    $('#partyCategoryID').val(null).trigger('change');
    $('#liabilityAccount').val('<?php echo $this->common_data['controlaccounts']['APA']?>').change();
    $('#supplierCurrency').val('<?php echo $this->common_data['company_data']['company_default_currencyID']?>').change();
    $('#suppliercountry').val('<?php echo $this->common_data['company_data']['company_country']?>').change();
    $('#suppliertaxgroup').val(null).trigger('change');
    $('#emp_model').modal('show');

}
function changecreditlimitcurr() {
    var currncy;
    var split;
    currncy = $('#supplierCurrency option:selected').text();
    split = currncy.split("|");
    $('.currency').html(split[0]);
    CurrencyID = $('#supplierCurrency').val();
    currency_validation_modal(CurrencyID, 'SUP', '', 'SUP');
}
function save_supplier_master() {
    var data = $('#suppliermaster_form').serializeArray();
    data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
    $.ajax({
        async: false,
        type: 'post',
        dataType: 'json',
        data: data,
        url: "<?php echo site_url('Supplier/save_suppliermaster'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            myAlert(data[0], data[1]);
            if(data[0] == 's')
            {
                $('#emp_model').modal('hide');
                fetch_supplierdrop(data[2],' ');
                fetch_supplier_currency_by_id(data['last_id']);
            }else
            {
                $('#emp_model').modal('show');
            }
        },
        error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            $('#emp_model').modal('show');
            refreshNotifications(true);
        }
    });


}
function fetch_supplierdrop(id,purchaseOrderID) {
    var supplier_id;
    var page = '';

    if(id)
    {
        supplier_id = id
    }else
    {
        supplier_id = '';
    }
    if(purchaseOrderID)
    {
        page = purchaseOrderID
    }

    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {supplier:supplier_id,DocID:page,Documentid:'PVM'},
        url: "<?php echo site_url('Procurement/fetch_supplier_Dropdown_all_grv'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $('#div_supplier_drop').html(data);
            stopLoad();
            $('.select2').select2();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            myAlert('e', '<br>Message: ' + errorThrown);
        }
    });
}

function fetch_supplier_currency_by_id(supplierAutoID,select_value){
    $.ajax({
        async       : true,
        type        :'post',
        dataType    :'json',
        data        :{'supplierAutoID':supplierAutoID},
        url         :"<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
        success : function(data){
            if (data.supplierCurrencyID) {
                $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
            };
        }
    });
}

function validateFloatKeyPress(el, evt) {
    //alert(currency_decimal);
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    //just one dot
    if (number.length > 1 && charCode == 46) {
        return false;
    }
    //get the carat position
    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
        return false;
    }
    return true;
}

function setQty(id,amount){
    var ordQtyId = "#amount_"+id;
    $(ordQtyId).val(amount);
    var data = {value:amount};
    select_check_box(data,id,amount);
}
</script>