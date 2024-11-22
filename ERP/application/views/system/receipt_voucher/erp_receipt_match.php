<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
//$customer_arr = all_customer_drop();
$pID = $this->input->post('page_id');
$customer_arr = all_customer_drop(true,1);
if($pID != 'RVM') {
    $contractAutoID = $pID;
    $Documentid = '';
     $customeridcurrentdoc = all_customer_drop_isactive_inactive($pID,$Documentid);
    if(!empty($customeridcurrentdoc) && $customeridcurrentdoc['isActive'] == 0)
    {
        $customer_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
    }
}
$country        = load_country_drop();
$gl_code_arr    = supplier_gl_drop();
$currncy_arr    = all_currency_new_drop();
$country_arr    = array('' => 'Select Country');
$taxGroup_arr    = customer_tax_groupMaster();
$customerCategory    = party_category(1);
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$createmasterrecords = getPolicyValues('CMR','All');
$financeyear_arr = all_financeyear_drop(true);
$customer_arr_masterlevel = array('' => 'Select Customer');
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_one');?><!--Step 1--> - <?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching_header');?><!--Receipt Matching Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail();" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_two');?><!--Step 2 -->- <?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching_detail');?><!--Receipt Matching Detail--></a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_three');?><!--Step 3 -->- <?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching_confirmation');?><!--Receipt Matching Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="receipt_match_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching_date');?><!--Receipt Matching Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="matchDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="matchDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('accounts_receivable_common_reference_no');?><!--Reference No--> </label>
                <input type="text" name="refNo" id="refNo" class="form-control" >
            </div>

           <?php if($createmasterrecords==1){?>
                <div class="form-group col-sm-4">
                    <label for="customerName"><?php echo $this->lang->line('common_customer_name');?><?php  required_mark(); ?></label><!--Customer Name-->
                    <div class="input-group">
                        <div id="div_customer_drop">
                            <?php echo form_dropdown('customerID', $customer_arr_masterlevel, '', 'class="form-control select2" id="customerID"  onchange="Load_customer_currency(this.value)"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Customer" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            <?php } else { ?>
            <div class="form-group col-sm-4">
                <label for="customerID"><?php echo $this->lang->line('common_customer');?><!--Customer--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" required onchange="Load_customer_currency(this.value)"'); ?>
            </div>
            <?php }?>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="supplier_currency"><?php echo $this->lang->line('accounts_receivable_tr_receipt_currency');?><!--Receipt Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'RVM\')" required'); ?>
            </div>
            <div class="form-group col-sm-4 pull-left">
                <label><?php echo $this->lang->line('accounts_receivable_common_memo');?><!--Memo--> </label>
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
                <button type="button" class="btn btn-primary pull-right" onclick="Receipt_match_detail_model()"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_receivable_tr_rm_new_match');?><!--New Match-->
                </button>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="Receipt_voucher_detail_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th colspan="3"> <?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher');?><!--Receipt Voucher--></th>
                    <th colspan="2"><?php echo $this->lang->line('accounts_receivable_common_invoice');?><!--Invoice--></th>
                    <th colspan="3"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('accounts_receivable_tr_rm_match');?><!--Match--></th>
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
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank"
                   href="<?php echo site_url('Receipt_voucher/load_rv_match_conformation/'); ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div>
        <hr>
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="ReceiptMatch_attachment_label">Modal title</h4>
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
                    <tbody id="ReceiptMatch_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<div class="modal fade" id="receipt_match_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 70%">
        <div class="modal-content">
            <form class="form-horizontal" id="Receipt_voucher_detail_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching');?><!--Receipt Matching--></h4>
                </div>
                <div class="modal-body">
                    <table class="table ">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('accounts_receivable_common_rv_code');?><!--RV Code--></th>
                            <th><?php echo $this->lang->line('accounts_receivable_common_rv_date');?><!--RV Date--></th>
                            <!--<th>PO Code</th>-->
                            <th><?php echo $this->lang->line('accounts_receivable_common_full_advance');?><!--Full Advance--></th>
                            <th><?php echo $this->lang->line('accounts_receivable_common_balance_advance');?><!--Balance Advance--></th>
                            <th><?php echo $this->lang->line('accounts_receivable_tr_rm_match_invoice');?><!--Match Invoice--></th>
                            <th>Balance Amount</th>
                            <th><?php echo $this->lang->line('accounts_receivable_common_to_pay');?><!--To Pay--></th>
                        </tr>
                        </thead>
                        <tbody id="match_ad_table">
                        <tr class="danger">
                            <td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                        </tr>
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
                    id="exampleModalLabel">Add New Customer</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('','role="form" id="customermaster_form"'); ?>
                <input type="hidden" id="isActive" name="isActive" value="1">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Customer Secondary Code <?php  required_mark(); ?></label><!--Customer Secondary Code-->
                            <input type="text" class="form-control" id="customercode" name="customercode">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerName">Customer Name<?php  required_mark(); ?></label><!--Customer Name-->
                            <input type="text" class="form-control" id="customerName" name="customerName" required>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Category</label><!--Category-->
                            <?php  echo form_dropdown('partyCategoryID', $customerCategory, '','class="form-control select2"  id="partyCategoryID"'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="receivableAccount">Receivable Account <?php  required_mark(); ?></label><!--Receivable Account-->
                            <?php  echo form_dropdown('receivableAccount', $gl_code_arr,$this->common_data['controlaccounts']['ARA'],'class="form-control select2" id="receivableAccount" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerCurrency">Customer Currency<?php  required_mark(); ?></label><!--Customer Currency-->
                            <?php  echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'] ,'class="form-control select2" onchange="changecreditlimitcurr()" id="customerCurrency" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Customer Country<?php  required_mark(); ?></label><!--Customer Country-->
                            <?php  echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['company_country'] ,'class="form-control select2"  id="customercountry" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Tax Group </label><!--Tax Group-->
                            <?php  echo form_dropdown('customertaxgroup', $taxGroup_arr, '','class="form-control select2"  id="customertaxgroup"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">VAT Identification No</label>
                            <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">ID card number </label>
                            <input type="text" class="form-control" id="IdCardNumber" name="IdCardNumber">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="customerTelephone"><?php echo $this->lang->line('common_telephone');?></label><!--Telephone-->
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerTelephone" name="customerTelephone">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerEmail"><?php echo $this->lang->line('common_email');?></label><!--Email-->
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerEmail" name="customerEmail">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerFax"><?php echo $this->lang->line('common_fax');?></label><!--Fax-->
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerFax" name="customerFax" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="customercustomerCreditPeriod">Credit Period</label><!--Credit Period-->
                            <div class="input-group">
                                <div class="input-group-addon"><?php echo $this->lang->line('common_month');?> </div><!--Month-->
                                <input type="text" class="form-control number" id="customerCreditPeriod" name="customerCreditPeriod">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customercustomerCreditLimit">Credit Limit</label><!--Credit Limit-->
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency">LKR</span></div>
                                <input type="text" class="form-control number" id="customerCreditLimit" name="customerCreditLimit">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerUrl">URL</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerUrl" name="customerUrl">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="customerAddress1">Primary Address</label><!--Primary Address-->
                            <textarea class="form-control" rows="2" id="customerAddress1" name="customerAddress1"></textarea>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerAddress2">Secondary Address</label><!--Secondary Address-->
                            <textarea class="form-control" rows="2" id="customerAddress2" name="customerAddress2"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="save_customer_master()">Add Customer </button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var matchID;
    var currencyID;
    var currency_decimal;
    var documentCurrency;
    $(document).ready(function () {
        
        $('.headerclose').click(function () {
            fetchPage('system/receipt_voucher/receipt_match_management', matchID, 'Receipt Matching');
        });
        $('.select2').select2();
        matchID = null;
        currencyID = null;
        currency_decimal = 2;
        documentCurrency = null;
        number_validation();

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#receipt_match_form').bootstrapValidator('revalidateField', 'matchDate');
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            matchID = p_id;
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop('',matchID);
            <?php }?>
            load_receipt_match_header();
        } else {
            $('.btn-wizard').addClass('disabled');
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop();
            <?php }?>
            CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID, 'RVM', '', '');
        }

        $('#receipt_match_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                matchDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching_date_is_required');?>.'}}},/*Receipt Matching Date is required*/
                //customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_supplier_is_required');?>.'}}},/*Supplier is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_transaction_currency_is_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            $("#customerID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'matchID', 'value': matchID});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receipt_voucher/save_receipt_match_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        matchID = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>/" + matchID);
                        $('.btn-wizard').removeClass('disabled');
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        documentCurrency = data['currency'];
                        currency_decimal = data['decimal'];
                        $('.currency').html('( '+documentCurrency+' )');
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

    function confirmation() {
        if (matchID) {
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
                        data: {'matchID': matchID},
                        url: "<?php echo site_url('Receipt_voucher/Receipt_match_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if(data['error']==1)
                            {
                                myAlert('e',data['message']);
                            }
                            if(data['error']==2)
                            {
                                myAlert('w',data['message']);
                            }
                            else
                            {
                                myAlert('s',data['message']);
                                fetchPage('system/receipt_voucher/receipt_match_management',matchID,'Receipt Matching');

                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function load_conformation() {
        if (matchID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'matchID': matchID, 'html': true},
                url: "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    attachment_modal_ReceiptMatch(matchID, "<?php echo $this->lang->line('accounts_receivable_tr_rm_receipt_matching');?>", "RVM");/*Receipt Matching*/
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#customerID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'CUS');
        }
    }

    function fetch_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'matchID': matchID},
            url: "<?php echo site_url('Receipt_voucher/fetch_match_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#table_body').empty();
                $('#t_total').html(0);
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    currencyID = null;
                }
                else {
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                    tot_amount = 0;
                    receivedQty = 0;
                    $.each(data, function (key, value) {
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['RVcode'] + '</td><td>' + value['RVdate'] + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['invoiceDate'] + '</td><td class="text-right" >' + parseFloat(value['transactionAmount']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="delete_rv_match_detail(' + value['matchDetailID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
                        tot_amount += parseFloat(value['transactionAmount']);
                        currency_decimal = value['transactionCurrencyDecimalPlaces'];
                        x++;
                    });
                    $('#t_total').html(parseFloat(tot_amount).formatMoney(currency_decimal, '.', ','));
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function Receipt_match_detail_model() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'matchID': matchID},
            url: "<?php echo site_url('Receipt_voucher/fetch_rv_advance_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#match_ad_table').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['receipt'])) {
                    $('#match_ad_table').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {

                    $.each(data['receipt'], function (key, value) {
                        currency_decimal = value['decimalplaces'];

                        var ad_detail = ' - ';
                        var paid_amount = 0;
                        // if (value['purchaseOrderID']!=0) {
                        //    ad_detail = value['POCode'];//+' '+value['PODescription'];
                        // }
                        if (value['paid'] != 'null') {
                            paid_amount = value['paid'];
                        }
                        if((value['transactionAmount'] - paid_amount)>0){
                            $('#match_ad_table').append('<tr><td>' + x + '</td><td>' + value['RVcode'] + '</td><td>' + value['RVdate'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat((value['transactionAmount'] - paid_amount)).formatMoney(currency_decimal, '.', ',') + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + value['receiptVoucherDetailAutoID'] + ',' + (value['transactionAmount'] - paid_amount).toFixed(value['decimalplaces']) + ')" aria-hidden="true"></i></td><td class="text-right"><select class="inv_drop select2" onchange="showBalanceAmount('+value['receiptVoucherDetailAutoID']+')"  id="inv_' + value['receiptVoucherDetailAutoID'] + '"><option  value="">Select Invoice</option></select></td><td class="text-right" id="balamount_'+value['receiptVoucherDetailAutoID']+'"></td><td class="text-right"><input type="text" name="amount[]" style="width: 100px" id="amount_' + value['receiptVoucherDetailAutoID'] + '" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="select_check_box(this,' + value['receiptVoucherDetailAutoID'] + ',' + (value['transactionAmount'] - paid_amount).toFixed(value['decimalplaces']) + ')" class="number"></td><td class="text-right" style="display:none;"><input class="checkbox" id="check_' + value['receiptVoucherDetailAutoID'] + '" type="checkbox" value="' + value['receiptVoucherDetailAutoID'] + '"></td></tr>');
                        }

                        x++;
                    });

                    if (!jQuery.isEmptyObject(data['invoice'])) {
                        $('.inv_drop').empty();
                        var mySelect = $('.inv_drop');
                        mySelect.append($('<option></option>').val('').html('Select Option'));
                        $.each(data['invoice'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['invoiceAutoID']).html(text['invoiceCode']));
                        });
                    }
                    $('.select2').select2();
                }
                $("#receipt_match_model").modal({backdrop: "static"});
                number_validation();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function select_check_box(data, id, total) {
        $("#check_" + id).prop("checked", false)
        var balamount=$('#balamount_'+id).html();
        balamount = balamount.replace(/,/g, "");
        var inv = $('#inv_'+id).val();
        if(inv==''){
            myAlert('w', 'Match Invoice canot be empty');/**/
            $( "#amount_"+id ).val('');
            return false
        }
        if (data.value > 0) {
            if (total >= data.value) {
                $("#check_" + id).prop("checked", true);
                if(parseFloat(balamount)<data.value){
                    $( "#check_"+id ).prop( "checked", false);
                    $( "#amount_"+id ).val('');
                    myAlert('w', 'Payment Matching Amount cannot be greater than Invoice Balance Amount');/**/
                    return false
                }
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');
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

    function save_match_items() {
        var selected = [];
        var amount = [];
        var invoice = [];
        $('#match_ad_table input:checked').each(function () {
            selected.push($(this).val());
            amount.push($('#amount_' + $(this).val()).val());
            invoice.push($('#inv_' + $(this).val()).val());
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'receiptVoucherDetailAutoID': selected,
                    'amounts': amount,
                    'invoiceAutoID': invoice,
                    'matchID': matchID
                },
                url: "<?php echo site_url('Receipt_voucher/save_match_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['messsage'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $('#receipt_match_model').modal('hide');
                        setTimeout(function () {
                            fetch_detail();
                        }, 300);
                    }
                }, error: function () {
                    $('#receipt_match_model').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function load_receipt_match_header() {
        if (matchID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'matchID': matchID},
                url: "<?php echo site_url('Receipt_voucher/load_receipt_match_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        currencyID = data['transactionCurrencyID'];
                        $("#a_link").attr("href", "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>/" + matchID);
                        $('#matchDate').val(data['matchDate']);
                        $('#refNo').val(data['refNo']);
                        $('#customerID').val(data['customerID']).change();
                        $('#Narration').val(data['Narration']);
                        $('#refNo').val(data['refNo']);
                        setTimeout(function () {
                            $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        }, 1000);
                        currency_decimal= data['transactionCurrencyDecimalPlaces'];
                        documentCurrency = data['transactionCurrency'];
                        $('.currency').html('( '+documentCurrency+' )');
                        fetch_detail();
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

    function save_draft() {
        if (matchID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_save_this_file');?>",/*You want to save this file !*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/receipt_voucher/receipt_match_management', matchID, 'Receipt Matching');
                });
        }
        ;
    }

    function delete_rv_match_detail(id) {
        if (matchID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
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
                        url: "<?php echo site_url('Receipt_voucher/delete_rv_match_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_detail();
                            }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }


    function attachment_modal_ReceiptMatch(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#ReceiptMatch_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#ReceiptMatch_attachment').empty();
                    $('#ReceiptMatch_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_ReceiptMatch_attachment(attachmentID, DocumentSystemCode,myFileName) {
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
                                myAlert('s','<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/
                                attachment_modal_ReceiptMatch(DocumentSystemCode, "Receipt Matching", "RVM");
                            }else{
                                myAlert('e','<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
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
            url: "<?php echo site_url('Receipt_voucher/showBalanceAmount_matching'); ?>",
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
        $('#customercode').val('');
        $('#customerName').val('');
        $('#IdCardNumber').val('');
        $('#customerTelephone').val('');
        $('#customerEmail').val('');
        $('#customerFax').val('');
        $('#customerCreditPeriod').val('');
        $('#customerCreditLimit').val('');
        $('#customerUrl').val('');
        $('#customerAddress1').val('');
        $('#customerAddress2').val('');
        $('#partyCategoryID').val(null).trigger('change');
        $('#receivableAccount').val('<?php echo $this->common_data['controlaccounts']['ARA']?>').change();
        $('#customerCurrency').val('<?php echo $this->common_data['company_data']['company_default_currencyID']?>').change();
        $('#customercountry').val('<?php echo $this->common_data['company_data']['company_country']?>').change();
        $('#customertaxgroup').val(null).trigger('change');
        $('#vatIdNo').val(null).trigger('change');
        $('#emp_model').modal('show');

    }
    function save_customer_master() {
        var data = $("#customermaster_form").serializeArray();
        data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});
        //data.push({'name' : 'customerAutoID', 'value' : $('#customerID option:selected').val()});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Customer/save_customer'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if(data['status'] == true)
                    {
                        $('#emp_model').modal('hide');
                        fetch_customerdrop(data['last_id'],'');
                        Load_customer_currency(data['last_id']);

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
    function changecreditlimitcurr(){
        var currncy;
        var split;
        currncy=  $('#customerCurrency option:selected').text();
        split= currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#customerCurrency').val();
        currency_validation_modal(CurrencyID,'CUS','','CUS');
    }



    function Load_customer_currency(customerAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': customerAutoID},
            url: "<?php echo site_url('Payable/fetch_customer_currency_by_id'); ?>",
            beforeSend: function () {
                $(':input[type="submit"]').prop('disabled', true);
            },
            success: function (data) {
                $(':input[type="submit"]').prop('disabled', false);
                if (currencyID) {
                    $("#transactionCurrencyID").val(currencyID).change()
                } else {
                    if (data.customerCurrencyID) {
                        $("#transactionCurrencyID").val(data.customerCurrencyID).change();
                        //currency_validation_modal(data.customerCurrencyID, 'BSI', customerAutoID, 'SUP');
                    }
                }
            }
        });
    }

    function fetch_customerdrop(id,matchID) {
        var customer_id;
        var page = '';
        if(matchID)
        {
            page = matchID;
        }
        if(id)
        {
            customer_id = id;
        }else
        {
            customer_id = '';
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customer:customer_id,DocID:page,Documentid:'RVM'},
            url: "<?php echo site_url('Receipt_voucher/fetch_customer_Dropdown_all_receiprtvoucer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_customer_drop').html(data);
                stopLoad();
                $('.select2').select2();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
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