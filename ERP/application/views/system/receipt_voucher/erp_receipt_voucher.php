<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$bank_arr = fetch_payment_bank();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$customer_arr = all_customer_drop();
$projectExist = project_is_exist();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$country        = load_country_drop();
$gl_code_arr    = supplier_gl_drop();
$currncy_arr    = all_currency_new_drop();
$country_arr    = array('' => 'Select Country');
$taxGroup_arr    = customer_tax_groupMaster();
$customerCategory    = party_category(1);
$company_currency_id =  $this->common_data['company_data']['company_default_currencyID'];
$company_currency =  $this->common_data['company_data']['company_default_currency'];

if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$itemBatchPolicy = getPolicyValues('IB', 'All');
$createmasterrecords = getPolicyValues('CMR','All');
$customer_arr_masterlevel = array('' => 'Select Customer');
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_one');?><!--Step 1--> - <?php echo $this->lang->line('accounts_receivable_tr_receipt_voucher_header');?><!--Receipt Voucher Header--></a>
        <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_details(3);" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_two');?><!--Step 2--> - <?php echo $this->lang->line('accounts_receivable_tr_receipt_voucher_detail');?><!--Receipt Voucher Detail--></a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_three');?><!--Step 3--> - <?php echo $this->lang->line('accounts_receivable_tr_receipt_receipt_voucher_confirmation');?><!-- Receipt Voucher Confirmation--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="receiptvoucher_form"'); ?>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label><?php echo $this->lang->line('accounts_receivable_common_voucher_type');?><!--Voucher Type--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('vouchertype',
                        array('' => $this->lang->line('common_select_type'),
                            /*'Direct' => $this->lang->line('accounts_receivable_common_direct_expense_payment')*/
                            /*'Direct Expense Payment'*//*, 'Invoices' =>$this->lang->line('accounts_receivable_common_customer_receipt_voucher') */
                            /*'Customer Receipt Voucher'*//*,*/
                            'DirectItem' => 'Direct Item Receipt'/*'Direct Item Payment'*/,
                            'DirectIncome' => 'Direct Income Receipt'/*'Direct Income Payment'*/,
                            //'InvoicesAdvance' =>'Customer Advance Receipt' /*'Customer Advance'*/,
                            'CustomerInvoices' =>'Customer Invoice Receipt' /*'Customer Invoice Payment'*/,
                            'InvoicesItem' =>'Customer Item Receipt' /*'Customer Item Payment'*/,
                            'InvoicesIncome' =>'Customer Income Receipt' /*'Customer Income Payment'*/),
                        'Direct', 'class="form-control select2" onchange="rv_type(this.value)" id="vouchertype" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="segment"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date');?><!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="RVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="RVdate"
                               class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                    <input type="text" name="referenceno" id="referenceno" class="form-control">
                </div>
                <div class="form-group col-sm-4" id="not_direct" style="display: none;">
                    <label for="customerID"><span id="party_text"><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--> </span> <?php required_mark(); ?>
                    </label>

                    <?php if($createmasterrecords==1){?>
                        <div class="input-group">
                            <div id="div_customer_drop">
                                <?php echo form_dropdown('customerID', $customer_arr_masterlevel, '', 'class="form-control select2" id="customerID"  onchange="Load_customer_currency(this.value)"'); ?>
                            </div>
                            <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Customer" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                        </div>
                    <?php } else { ?>
                        <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value)"'); ?>
                    <?php }?>


                </div>
                <div class="form-group col-sm-4" id="direct">
                    <label for="customer_name"><span id="party_text"><?php echo $this->lang->line('common_customer');?><!--Customer--> </span> <?php required_mark(); ?></label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control">
                </div>
                <div class="form-group col-sm-4">
                    <label for="transactionCurrencyID"><?php echo $this->lang->line('accounts_receivable_tr_receipt_currency');?><!--Receipt Currency--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'RV\')" id="transactionCurrencyID" required'); ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="RVbankCode"><?php echo $this->lang->line('accounts_receivable_common_bank_or_cash');?><!--Bank or Cash--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="RVbankCode" onchange="set_payment_method()" required'); ?>
                </div>
                <?php
                if($financeyearperiodYN==1){
                ?>
                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                    <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                </div>
                <?php } ?>
            </div>
            <div class="row">
                 <div class="form-group col-sm-4 paymentType">
                <label>Mode of Payment <?php required_mark(); ?></label>
                <?php echo form_dropdown('paymentMode', array('' => 'Select Payment', '1' => ' Cheque', '2' => 'Bank Transfer'), '', 'class="form-control " id="paymentMode" onchange="show_payment_method(this.value)"'); ?>
                 </div>
                <div class="form-group col-sm-4 banktrans hide">
                    <label>Customer Bank</label>
                    <select class="form-control" id="customerBankMasterID" name="customerBankMasterID">

                    </select>
                </div>

                <div class="form-group col-sm-4 paymentmoad">
                    <label><?php echo $this->lang->line('accounts_receivable_common_cheque_number');?><!--Cheque Number--><?php required_mark(); ?></label>
                    <input type="text" name="RVchequeNo" id="RVchequeNo" class="form-control">
                </div>
                <div class="form-group col-sm-4 paymentmoad">
                    <label><?php echo $this->lang->line('accounts_receivable_common_cheque_date');?><!--Cheque Date--> <?php required_mark(); ?></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="RVchequeDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="RVchequeDate"
                               class="form-control" >
                    </div>
                </div>
            </div>

         


            <div class="row">
                <div class="form-group col-sm-4">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label><?php echo $this->lang->line('accounts_receivable_common_memo');?><!--Memo--> </label>
                            <textarea class="form-control" rows="3" name="RVNarration" id="RVNarration"></textarea>
                        </div>

                        <div class="form-group col-sm-12 hide" style="margin-top:35px;" id="exchange_alter_prompt">
                            <p class="text-bold">Alter the bank currency exchange rates ? </p>
                            <div class="text-left m-t-xs">
                                <button class="btn btn-primary" type="button" onclick="exchange_alter_prompt_yes()">
                                    <?php echo $this->lang->line('common_yes');?>
                                </button>
                            </div>
                        </div>

                        <div class="form-group col-sm-12 hide" style="margin-top:35px; background-color:#f9e7ed;padding:20px" id="exchange_alter_area">
                            <label><u><?php echo 'Currency exchange rates';?><!--Memo--> </u></label>
                            <div class="row">
                                <p class="h5 col-sm-12"> Transaction Currency - <span id="transCurrencyID"> <?php echo $company_currency ?> </span> </p>
                                <p class="h5 col-sm-12"> Bank Currency - <span id="bankCurrencyID"> <?php echo $company_currency ?> </span> </p>
                                <p class="h5 col-sm-12"> Default Exchange rate : <span id="systemExchangeRate">  </span> </p>
                                <input type="hidden" id="default_exchange_rate" name="default_exchange_rate" val="" />
                                <input type="hidden" id="edit_exchange" name="edit_exchange" val="" />
                            </div>
                            <div class="row">
                                <!-- <div class="col-sm-6">
                                    <label for="receipt_currency_exchange_rate"><span id="party_text"><?php echo 'Receipt Currency Exchange Rate'?> </span> <?php required_mark(); ?></label>
                                    <input type="text" name="receipt_currency_exchange_rate" id="receipt_currency_exchange_rate" class="form-control">
                                </div> -->
                                <div class="col-sm-6">
                                    <label for="bank_currency_exchange_rate"><span id="party_text"><?php echo 'Bank Currency Exchange Rate';?><!--Customer--> </span> <?php required_mark(); ?></label>
                                    <input type="text" name="bank_currency_exchange_rate" id="bank_currency_exchange_rate" class="form-control text-right" onkeyup="revalidate_form()">
                                </div>
                            </div>

                            <p class="text-danger" style="margin-top:35px;">** Exchange rate changes only valid for this receipt only</p>
                        </div>
                    </div> 
                    
                </div>

                <div class="form-group col-sm-8 hide" id="employeerdirect">
                    <label>Bank Transfer Details </label>
                    <textarea class="form-control" rows="3" name="bankTransferDetails" id="bankTransferDetails"></textarea>
                </div>
            </div>


            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-primary" type="submit" id="form_submit_btn"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
            </div>
            </form>
        </div>
        <div id="step2" class="tab-pane">

        </div>
        <div id="step3" class="tab-pane">
            <hr>
            <div id="conform_body"></div>
            <hr>
            <div id="conform_body_attachement">
                <h4 class="modal-title" id="receiptVoucher_attachment_label">Modal title</h4>
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
                        <tbody id="receiptVoucher_attachment" class="no-padding">
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


    <div aria-hidden="true" role="dialog" id="all_item_edit_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog" style="width: 95%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Receipt Voucher</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="edit_all_item_detail_form" class="form-horizontal">
                        <table class="table table-bordered table-condensed no-color"
                               id="receipt_voucher_detail_all_edit_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Ware House--> <?php required_mark(); ?></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                    <th>Batch Number</th>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th>Current Stock</th>
                                <th>Park Qty</th>

                                <th>
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency"> (<?php echo $master['transactionCurrency'] ?? ''; ?>
                                        )</span> <?php required_mark(); ?></th>
                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    <span
                                        class="currency"> (<?php echo $master['transactionCurrency'] ?? ''; ?>)</span>
                                </th>
                                <th style="display: none;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th style="display: none;">
                                    <?php echo $this->lang->line('accounts_receivable_common_remarks'); ?><!--Remarks--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more_edit_receipt_voucher()">
                                        <i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="edit_item_table_body">

                            </tbody>
                            <tfoot id="edit_item_table_tfoot">

                            </tfoot>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="update_receipt_voucher_edit_all_Item()"><?php echo $this->lang->line('common_update_changes'); ?>
                    </button><!--Update changes-->
                </div>

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

    <div class="modal fade" id="access_denied_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">You cannot use this Item. This item has been pulled for following documents.</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document ID</th>
                        <th>Document Code</th>
                        <th>Reference No</th>
                        <th>Document Date</th>
                        <th>WareHouse</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_item_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
    <script type="text/javascript">
        var receiptVoucherAutoId;
        var RVType;
        var customerID;
        var bankTransferDetails;
        var currencyID;
        $(document).ready(function () {
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop()
            <?php }?>
            $('.headerclose').click(function () {

                fetchPage('system/receipt_voucher/receipt_voucher_management', receiptVoucherAutoId, 'Receipt Voucher');
            });
            $('.select2').select2();
            receiptVoucherAutoId = null;
            RVType = null;
            customerID = null;
            bankTransferDetails = null;
            currencyID = null;

            $(".paymentmoad").hide();
            $(".paymentType").hide();

            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#receiptvoucher_form').bootstrapValidator('revalidateField', 'RVdate');
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
            });

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                receiptVoucherAutoId = p_id;
                load_receipt_voucher_header();
            } else {
                $('.btn-wizard').addClass('disabled');
                CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
                currency_validation_modal(CurrencyID, 'RV', '', '');

                $('.btn-wizard').addClass('disabled');
                currency_validation(<?php echo json_encode(trim($this->common_data['company_data']['company_default_currencyID'])); ?>, 'PV');
                //$("#bankTransferDetails").wysihtml5();
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

            FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
            DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
            DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
            periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
            fetch_finance_year_period(FinanceYearID, periodID);

            $('#receiptvoucher_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    vouchertype: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_voucher_type_is_required');?>.'}}},/*Voucher Type is required*/
                    segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                    RVdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_receipt_voucher_date_is_required');?>.'}}},/*Receipt Voucher Date is required*/
                    //referenceno: {validators: {notEmpty: {message: 'Reference # is required.'}}},
                    supplier: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_supplier_is_required');?>.'}}},/*Supplier is required*/
                    transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_transaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                    //RVNarration: {validators: {notEmpty: {message: 'Narration is required.'}}},
                    //paymentMode: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('accounts_receivable_tr_mode_of_payment_is_required');?>//.'}}},/*Mode of Payment is required*/
                    RVbankCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_bank_or_cash_is_required');?>.'}}}/*Bank or Cash is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();

                //Check exchange rate exceeds

                var default_exchange_rate = $('#default_exchange_rate').val();
                var bank_currency_exchange_rate = $('#bank_currency_exchange_rate').val();

                var diviation_range = (default_exchange_rate*5)/100;
                var diviation = Math.abs(default_exchange_rate - bank_currency_exchange_rate);
                
                if(diviation > diviation_range){
                    swal("Cancelled", "Entered exchange rate greater than accepted deviation range", "error");
                    return false;
                }

                //End exchange rate check

                $("#customerID").prop("disabled", false);
                $("#transactionCurrencyID").prop("disabled", false);
                $("#vouchertype").prop("disabled", false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                
                var data = $form.serializeArray();
                data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
                data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
                data.push({'name': 'SupplierDetails', 'value': $('#supplier option:selected').text()});
                data.push({'name': 'bank', 'value': $('#RVbankCode option:selected').text()});
                data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
                data.push({'name': 'bank_currency_exchange_rate', 'value': bank_currency_exchange_rate});
                
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Receipt_voucher/save_receiptvoucher_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data_arr) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data_arr['status']) {
                            receiptVoucherAutoId = data_arr['last_id'];
                            RVType = $('#vouchertype').val();
                            rv_type(RVType);
                            customerID = $('#customerID').val();
                            bankTransferDetails =  $("#bankTransferDetails").val();
                            currencyID = $('#transactionCurrency').val();
                            $('.btn-wizard').removeClass('disabled');
                            $("#a_link").attr("href", "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + receiptVoucherAutoId);
                            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + receiptVoucherAutoId + '/RV');
                            $("#customerID").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            $("#vouchertype").prop("disabled", true);
                            /* $("#vouchertype").change();*/
                            fetch_detail(RVType, customerID, currencyID, 3);
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

        // function fetch_supplier_currency(supplierAutoID, select_value) {
        //     $.ajax({
        //         async: true,
        //         type: 'post',
        //         dataType: 'json',
        //         data: {'supplierAutoID': supplierAutoID},
        //         url: "<?php //echo site_url('Procurement/fetch_supplier_currency'); ?>",
        //         success: function (data) {
        //             if (data.supplierCurrency) {
        //                 $("#transactionCurrency").val(data.supplierCurrency);
        //             }
        //             ;
        //         }
        //     });
        // }

        function rv_type(type) {
            if (type == 'Direct' || type == 'DirectItem' || type == 'DirectIncome') {
                $('.startmark').removeClass('hidden');
                $('#direct').show();
                $('#not_direct').hide();
            } else {
                $('.startmark').addClass('hidden');
                $('#not_direct').show();
                $('#direct').hide();
            }
        }

        function confirmation() {
            if (receiptVoucherAutoId) {
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
                            data: {'receiptVoucherAutoId': receiptVoucherAutoId},
                            url: "<?php echo site_url('Receipt_voucher/receipt_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if(data['error']==1){
                                    myAlert('e',data['message']);
                                    if( data['message']=='Some Item quantities are not sufficient to confirm this transaction'){
                                        confirm_all_item_detail_modal(data['itemAutoID']);
                                    }
                                }else if(data['error']==2){
                                    myAlert('w',data['message']);
                                }else if (data['error'] == 4){ 
                                
                                $('#wac_minus_calculation_validation_body').empty();
                                x = 1;
                                if (jQuery.isEmptyObject(data['message'])) {
                                    $('#wac_minus_calculation_validation_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                               } else {
                                   $.each(data['message'], function (key, value) {
                          
                                    $('#wac_minus_calculation_validation_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>'+value['itemName']+'</td><td>' + value['Amount'] + '</td></tr>');
                                   x++;
                         
           
                               });
                               }
                                   $('#wac_minus_calculation_validation').modal('show');
                               }
                                else {
                                    myAlert('s',data['message']);
                                    fetchPage('system/receipt_voucher/receipt_voucher_management', receiptVoucherAutoId, 'Receipt Voucher');
                                }
                                /*if (data['status']) {
                                    refreshNotifications(true);

                                } else {
                                    myAlert('w', data['data'], 1000);
                                }*/
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
            ;
        }

        function currency_validation(CurrencyID, documentID) {
            if (CurrencyID) {
                partyAutoID = $('#customerID').val();
                currency_validation_modal(CurrencyID, documentID, partyAutoID, 'CUS');
                
                //check exchange rates
                check_for_exchange_rates('transaction_currency');
            
            }
        }

        function fetch_details(tab) {
            $('.nav-tabs a[href="#tab_1"]').tab('show');
            if (tab == 1) {
                $('.nav-tabs a[href="#tab_3"]').tab('show');
            }
            if (tab == 2) {
                $('.nav-tabs a[href="#tab_2"]').tab('show');
            }
            if (tab == 3) {
                $('.nav-tabs a[href="#tab_1"]').tab('show');
            }
            if (tab == 4) {
                $('.nav-tabs a[href="#tab_4"]').tab('show')
            }
            if (tab == 5) {
                $('.nav-tabs a[href="#tab_5"]').tab('show')
            }
            //fetch_rv_details(RVType, customerID, currencyID, tab);
            fetch_detail(RVType, customerID, currencyID, tab);
        }

        function load_conformation() {
            if (receiptVoucherAutoId) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'html': true},
                    url: "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#conform_body').html(data);
                        $("#a_link").attr("href", "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + receiptVoucherAutoId);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + receiptVoucherAutoId + '/RV');
                        attachment_modal_receiptVoucher(receiptVoucherAutoId, "<?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher');?>", "RV");/*Receipt Voucher*/
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

        function fetch_detail(type, customerID, currencyID, tab) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'receiptVoucherAutoId': receiptVoucherAutoId,
                    'RVType': type,
                    'customerID': customerID,
                    'currencyID': currencyID,
                    tab: tab
                },
                url: "<?php echo site_url('Receipt_voucher/fetch_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#step2').html(data);
                    //check_detail_dataExist(receiptVoucherAutoId);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function check_detail_dataExist(receiptVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'receiptVoucherAutoId': receiptVoucherAutoId},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (jQuery.isEmptyObject(data)) {
                        $("#customerID").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $("#vouchertype").prop("disabled", false)
                    } else {
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#vouchertype").prop("disabled", true)
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function fetch_finance_year_period(companyFinanceYearID, select_value) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'RV'},
                url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
                success: function (data) {
                    $('#financeyear_period').empty();
                    var mySelect = $('#financeyear_period');
                    mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
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

        function Receipt_voucher_detail_model() {
            $("#Receipt_voucher_model").modal({backdrop: "static"});
        }

        function load_receipt_voucher_header() {
            if (receiptVoucherAutoId) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'receiptVoucherAutoId': receiptVoucherAutoId},
                    url: "<?php echo site_url('Receipt_voucher/load_receipt_voucher_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $("#a_link").attr("href", "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + receiptVoucherAutoId);
                            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + receiptVoucherAutoId + '/RV');
                            RVType = data['RVType'];
                            rv_type(RVType);
                            customerID = data['customerID'];
                            bankTransferDetails = data['bankTransferDetails'];
                            currencyID = data['transactionCurrencyID'];
                            $('#financeyear').val(data['companyFinanceYearID']);
                            fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                            $('#RVdate').val(data['RVdate']);
                            $('#paymentbank').val(data['BRVbank']);
                            $('#account').val(data['RVAccount']);
                            $('#RVNarration').val(data['RVNarration']);
                            $('#vouchertype').val(data['documenttype']).change();
                            $('#referenceno').val(data['referanceNo']);
                            $('#RVbankCode').val(data['RVbankCode']).change();
                            $('#customerID').val(data['customerID']).change();
                            $('#customer_name').val(data['customerName']);
                            $('#transactionCurrencyID').val(data['transactionCurrencyID']);
                            $("#transactionCurrencyID").addClass('selected');
                            $('#edit_exchange').val(1);
                            $('#bank_currency_exchange_rate').val(data['bankCurrencyExchangeRate']);
                            $('#exchange_alter_area').removeClass('hide');
                           

                            if (data['modeOfPayment'] == 1) {
                                $(".paymentmoad").hide();
                            } 
                            if (data['modeOfPayment'] == 2) {
                                $('#paymentMode').val(data['modeOfPayment']);
                                $('#paymentMode').val(data['paymentType']);
                                $('#RVchequeNo').val(data['RVchequeNo']);
                                $('#RVchequeDate').val(data['RVchequeDate']);

                                $(".paymentType").show();
                                if(data[''] == 1) {
                                    $(".banktrans").addClass('hide');
                                    $("#employeerdirect").addClass('hide');
                                } else {
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
                                    $("#bankTransferDetails").val( data['bankTransferDetails']);
                                    $("#employeerdirect").removeClass('hide');
                                    $(".banktrans").addClass('show');
                                }
                                // $(".paymentmoad").show();
                            }
                            show_payment_method();
                            fetch_detail(data['RVType'], data['customerID'], data['transactionCurrency'], 3);
                            $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
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
            if (receiptVoucherAutoId) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_save_this_file');?>",/*You want to save this file!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        fetchPage('system/receipt_voucher/receipt_voucher_management', receiptVoucherAutoId, 'Receipt Voucher');
                    });
            }
        }

        function attachment_modal_receiptVoucher(documentSystemCode, document_name, documentID) {
            if (documentSystemCode) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                    dataType: 'json',
                    data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                    success: function (data) {
                        $('#receiptVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                        $('#receiptVoucher_attachment').empty();
                        $('#receiptVoucher_attachment').append('' +data+ '');

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#ajax_nav_container').html(xhr.responseText);
                    }
                });
            }
        }

        function delete_receiptVoucher_attachment(receiptVoucherAutoId, DocumentSystemCode, myFileName) {
            if (receiptVoucherAutoId) {
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
                            data: {'attachmentID': receiptVoucherAutoId, 'myFileName': myFileName},
                            url: "<?php echo site_url('Receipt_voucher/delete_receipt_voucher_attachement'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if (data == true) {
                                    myAlert('s', 'Deleted Successfully');
                                    attachment_modal_receiptVoucher(DocumentSystemCode, "Receipt Voucher", "RV");
                                } else {
                                    myAlert('e', 'Deletion Failed');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function set_payment_method() {
            val = $('#RVbankCode option:selected').text();
            res = val.split(" | ")
            if (res[5] == 'Cash') {
                $(".paymentType").hide();
                $(".paymentmoad").hide();
                $('#employeerdirect').addClass('hide');
            } else {
                $(".paymentType").show();
                show_payment_method();
                // $(".paymentmoad").show();
            }

            //check exchange rates
            check_for_exchange_rates('bank_change');
        }

        function show_payment_method(){
            if ($("#paymentMode").val() == 1) {
                $(".paymentmoad").show();
                $('.banktrans').addClass('hide');
                $('#employeerdirect').addClass('hide');
            }else if($("#paymentMode").val() == 2) {
                $('#RVchequeNo').val('');
                $('#RVchequeDate').val('');
                $('#customerBankMasterID').addClass('hide');
                $('#employeerdirect').removeClass('hide');
                $(".paymentmoad").hide();

                var invoiceNote='<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
                if (p_id) {
                    if(bankTransferDetails){
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
                        $("#bankTransferDetails").val(bankTransferDetails);
                    } else {
                        $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
                    }

                }else{
                    $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
                }
            } else{
                $('#RVchequeNo').val('');
                $('#RVchequeDate').val('');
                $('#employeerdirect').addClass('hide');
                $('.banktrans').addClass('hide');
                $(".paymentmoad").hide();
            }
        }

        function confirm_all_item_detail_modal(itemAutoIdArr) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'receiptVoucherAutoId': receiptVoucherAutoId},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_details_all'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var total = 0;
                    var descm = 2;
                    $('#edit_item_table_body').empty();
                    var x = 2;
                    if (jQuery.isEmptyObject(data)) {
                        $('#edit_item_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                        <!--No Records Found-->
                    } else {
                        $.each(data['detail'], function (key, value) {
                            var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown" onchange="convertPrice_RV(this)" required')) ?>';
                            var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                            var project = '';
                            <?php if ($projectExist == 1) { ?>
                            project = ' <td> <div class="div_projectID_item"> <select name="projectID" class="form-control select2"> <option value="">Select Project</option> </select> </div> </td>';
                            <?php
                            } ?>
                            var string = '<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control search f_search" name="search[]" id="f_search_' + x + '" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - ' + value['seconeryItemCode'] + '" placeholder="Item ID,Item Description...">  <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '"  name="itemAutoID[]"> <input type="hidden" class="form-control receiptVoucherDetailAutoID" value="' + value['receiptVoucherDetailAutoID'] + '"  name="receiptVoucherDetailAutoID[]"> </td> <td>' + wareHouseAutoID + '</td> ' + project + ' <td><input class="hidden conversionRate" id="conversionRate" value="' + value['conversionRateUOM'] + '" name="conversionRate">' + UOM + '</td> <td> <div class="input-group"> <input type="text" name="currentstock[]" class="form-control currentstock" required disabled></div></td> <td> <input type="text" onchange="change_amount(this,1)" onkeyup="checkCurrentStock(this)" name="quantityRequested[]" placeholder="0.00" class="form-control number quantityRequested" onfocus="this.select();" value="' + value['requestedQty'] + '" required> </td><td> <input type="text" onchange="change_amount(this,1)" name="estimatedAmount[]" onkeypress="return validateFloatKeyPress(this,event)" value="' + value['unittransactionAmount'] + '" onfocus="this.select();" placeholder="0.00" class="form-control number estimatedAmount"> </td><td> <input type="text" onchange="change_amount(this,2)" name="netAmount[]" placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number netAmount input-mini" value="' + value['transactionAmount'] + '"> </td><td class="remove-td"><a onclick="delete_receipt_voucherDetailsEdit(' + value['receiptVoucherDetailAutoID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            $('#edit_item_table_body').append(string);
                            //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                            $('#ware_' + key).val(value['wareHouseAutoID']).change();
                            fetch_related_uom_id(value['defaultUOMID'], value['unitOfMeasureID'], $('#uom_' + key));
                            initializeitemTypeahead(x);
                            x++;
                        });
                        $('.select2').select2();
                        search_id = x - 1;
                        $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                    }
                    $.each(itemAutoIdArr, function (key, valu) {
                        var concatval=valu['itemAutoID'] +'|'+valu['wareHouseAutoID'];
                        $('.itemAutoID').each(function () {
                            var thisconcat=this.value+'|'+$(this).closest('tr').find('.wareHouseAutoID').val();
                            if(concatval == thisconcat){
                                $(this).closest('tr').css("background-color",'#ffb2b2');
                            }
                        });
                    });
                    stopLoad();
                    <!--Total-->

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
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
                            fetch_customerdrop(data['last_id']);
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


        function fetch_customerdrop(id) {
            var customer_id;
            if(id)
            {
                customer_id = id
            }else
            {
                customer_id = '';
            }
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {customer:customer_id},
                url: "<?php echo site_url('Invoices/fetch_customer_Dropdown_all'); ?>",
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

        function check_item_not_approved_in_document(itemAutoID,id,documentcode) {
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            // data: {itemAutoID: itemAutoID,'documentcode':'RV','receiptVoucherAutoId':receiptVoucherAutoId},
            data: {itemAutoID: itemAutoID,'documentcode':documentcode,'receiptVoucherAutoId':receiptVoucherAutoId,'receiptVoucherDetailAutoID':receiptVoucherDetailAutoID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_item_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_item_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_item_body').append('<tr><td>' + x + '</td><td>' + value['documentID'] + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td><td>' + value['referenceNo'] + '</td><td>' + value['documentDate'] + '</td><td>' + value['warehouse'] + '</td><td>' + value['Uom'] + '</td><td>' + value['stock'] + '</td></tr>');
                        x++;
                    });

                 /*    $('#f_search_' + id).val('');
                    $('#f_search_' + id).closest('tr').find('.f_search').val('');
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val('');
                    $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                    $('#f_search_' + id).closest('tr').find('.currentstock').val('');
                    $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                    $('#f_search_' + id).closest('tr').find('.estimatedAmount').val('');
                    $('#f_search_' + id).closest('tr').find('.netAmount').val(''); */
                    $('#access_denied_item').modal('show');
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_item_not_approved_in_document_edit(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {itemAutoID: itemAutoID,'documentcode':'RV','receiptVoucherAutoId':receiptVoucherAutoId},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_item_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_item_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_item_body').append('<tr><td>' + x + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td></tr>');
                        x++;
                    });


                    $('#itemAutoID_edit').val('');
                    $('#edit_wareHouseAutoID').val('');
                    $('#conversionRateRVEdit').val('');
                    $('#currentstock_edit').val('');
                    $('#edit_quantityRequested').val('');
                    $('#edit_estimatedAmount').val('');
                    $('#editNetAmount').val('');
                    $('#search').val('');
                    $('#access_denied_item').modal('show');
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    ///////////////////////////////////////////////////////////////
    /* 
        @description : when receipt using different cureencies different bank can have different exchange rates for them
        user will manually set those exchange rates
    */

    var exchange_called = 0;

    async function check_for_exchange_rates(type){

        var bank_gl = $('#RVbankCode option:selected').val();
        var transaction_currency = $('#transactionCurrencyID option:selected').val();
        var transaction_currency_text = $('#transactionCurrencyID option:selected').text().split('|');
        var bank_details;
        var edit_exchange = $('#edit_exchange').val();

        if(receiptVoucherAutoId){
            exchange_called++;
        }

        if(bank_gl){
            bank_details = await fetch_bank_details(bank_gl,transaction_currency);
          
        }
        
        if(typeof(bank_details)  === "undefined"){
            return false;
        }

        //Different currencies
        // != transaction_currency
        if(bank_details.bankCurrencyID){
            //prompt currency edit box

            if(bank_details.bankCurrencyID == transaction_currency){
                $('#bank_currency_exchange_rate').prop('disabled','true');
            }else{
                $('#bank_currency_exchange_rate').prop('disabled',null);
            }

            $('#exchange_alter_prompt').removeClass('hide');
           // $('#exchange_alter_area').removeClass('hide');

            if(bank_details){
                $('#bankCurrencyID').empty().html(bank_details.bankCurrencyCode);
                $('#transCurrencyID').empty().html(transaction_currency_text[0]);
                $('#systemExchangeRate').empty().html(bank_details.conversions.conversion);

                if(!receiptVoucherAutoId || exchange_called > 2){
                    $('#bank_currency_exchange_rate').val(bank_details.conversions.conversion);
                }
                
                $('#default_exchange_rate').val(bank_details.conversions.conversion);
            }

        }else{
            
           //  $('#exchange_alter_prompt').addClass('hide');
          //   $('#exchange_alter_area').addClass('hide');

        }

    }

    /* 
        @description : fetch bank currencies details
    */

    function fetch_bank_details(bank_gl,transaction_currency){

        var bank_gl_details;

        $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {'bank_gl': bank_gl,'transaction_currency': transaction_currency},
                url: "<?php echo site_url('Receipt_voucher/load_bank_detail_exchange_rates'); ?>",
                beforeSend: function () {
                   
                },
                success: function (data) {
                    bank_gl_details = data;
                   
                }
        });

        return bank_gl_details;

    }

    function exchange_alter_prompt_yes(){
        $('#exchange_alter_area').removeClass('hide');
    }

    function revalidate_form(){
        $('#form_submit_btn').attr('disabled', null);
    }

    </script>



<div id="modal"></div>
<?php


$data['documentID'] = 'RV';
/*
$data['invoiceAutoID'] = $invoiceAutoID;
$data['master'] = $this->input->post('page_id');
$data['invoiceType'] = $invoiceType;*/

$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);

?>