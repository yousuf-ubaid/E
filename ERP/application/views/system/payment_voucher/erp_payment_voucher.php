<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$enbleAuthorizeSignature = getPolicyValues('SGB', 'All');
$pID = $this->input->post('page_id');
$supplier_arr = all_supplier_drop(true,1);
if($pID != '') {

    $Documentid = 'PV';
    $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pID,$Documentid);
    if($supplieridcurrentdoc && $supplieridcurrentdoc['isActive'] == 0)
    {
        $supplier_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

    }
}
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
$chequeRegister = getPolicyValues('CRE', 'All');
$purchaseOrderPolicy = getPolicyValues('PQP', 'All');

$voucherType = array('' => $this->lang->line('common_select_type')/*'Select Type'*/,
    'DirectItem1' => 'Direct Item Payment' /*'Direct Item Payment'*/,
    'DirectExpense1' => 'Direct Expense' /*'Direct Expense'*/,
    'SupplierAdvance' =>'Supplier Advance Payment',
    'SupplierInvoice' =>'Supplier Invoice Payment',
   );

if($purchaseOrderPolicy == 1) {
    $voucherType['PurchaseRequest'] = 'Purchase Request';
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_one');?><!--Step 1--> - <?php echo $this->lang->line('accounts_payable_tr_pv_header');?><!--Payment Voucher Header--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_details(4);" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_two');?><!--Step 2 --> - <?php echo $this->lang->line('accounts_payable_tr_pv_detail');?><!--Payment Voucher Detail--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_three');?><!--Step 3--> - <?php echo $this->lang->line('accounts_payable_tr_pv_confirmation');?><!--Payment Voucher Confirmation--></span>
            </a>
           
        </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="paymentvoucher_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><!--Voucher Type--><?php echo $this->lang->line('accounts_payable_tr_pv_voucher_type');?> <?php required_mark(); ?></label>
                <?php echo form_dropdown('vouchertype', $voucherType, 'Direct', 'class="form-control select2 vouchertype" onchange="voucher_type(this.value)" id="vouchertype" required'); ?>
            </div>

            <div class="form-group col-sm-4 pvtypeHideShow">
                <label>Payee Type<?php required_mark(); ?></label>
                <?php echo form_dropdown('pvtype',array(''=>'Select Payee Type'), ' ', 'class="form-control select2" id="pvtype" onchange="voucher_type(this.value)"' ); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('accounts_payable_tr_pv_payment_voucher_date');?><!--Payment Voucher Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="PVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="PVdate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                <input type="text" name="referenceno" id="referenceno" class="form-control">
            </div>
            <div class="form-group col-sm-4">
                <label for="partyName"><span id="party_text"><?php echo $this->lang->line('accounts_payable_tr_pv_payee_name');?><!--Payee Name--> </span> <?php required_mark(); ?></label>
                <span id="party_textbox">
                        <input type="text" name="partyName" id="partyName" class="form-control">
                        </span>

            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_currency');?><!--Payment Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'PV\')" id="transactionCurrencyID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="PVbankCode"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_bank_or_cash');?><!--Bank or Cash--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('PVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="PVbankCode" onchange="fetch_cheque_number(this.value)"'); ?>
            </div>

            <?php
            if($financeyearperiodYN==1){
                ?>
                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('accounts_payable_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('accounts_payable_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                    <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="form-group col-sm-4 paymentType hide">
                <label>Payment Type</label>
                <?php echo form_dropdown('paymentType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, '1' => 'Cheque ', '2' =>'Bank Transfer'), ' ', 'class="form-control select2" id="paymentType" onchange="show_payment_method(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4 banktrans hide">
                <label>Supplier Bank</label>
                <select class="form-control" id="supplierBankMasterID" name="supplierBankMasterID">

                </select>
            </div>
            <?php
            if($chequeRegister==1){
                ?>
                <div class="form-group col-sm-4 paymentmoad">
                    <label for="PVbankCode"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_no');?>  <?php required_mark(); ?></label>
                    <?php echo form_dropdown('chequeRegisterDetailID', array('' => 'Select Cheque no'), '', 'class="form-control" id="chequeRegisterDetailID"'); ?>
                </div>
            <?php
            }else{
                ?>
                <div class="form-group col-sm-4 paymentmoad">
                    <label><!--Cheque Number--><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_no');?> <?php required_mark(); ?></label>
                    <input type="text" name="PVchequeNo" id="PVchequeNo" class="form-control">
                </div>
            <?php
            }
            ?>
            <div class="form-group col-sm-4 paymentmoad">
                <label><!--Cheque Date--><?php echo $this->lang->line('accounts_payable_tr_pv_payment_cheaque_date');?> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="PVchequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="PVchequeDate"
                           class="form-control" >
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="form-group col-sm-4">
                <label><!--Memo--><?php echo $this->lang->line('accounts_payable_tr_pv_payment_memo');?> </label>
                <textarea class="form-control" rows="3" name="narration" id="narration" ></textarea>
            </div>
            <div class="form-group col-sm-1 paymentmoad" style="padding-right: 0px;">
                <label class="title">Payee Only
            </div>
            <div class="form-group col-sm-1 paymentmoad" style="padding-left: 0px;">
                <div class="col-sm-1">
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns"><input id="accountPayeeOnly" type="checkbox"
                                                                      data-caption="" class="columnSelected"
                                                                      name="accountPayeeOnly" value="1"><label
                                for="checkbox">&nbsp;</label></div>
                    </div>
                </div>
            </div>

            <?php if($enbleAuthorizeSignature ==1){ ?>
            <div class="form-group col-sm-4 hide" id="signature_field">
                <label for="signature">Signatures</label><br>
                <?php echo form_dropdown('signature[]', '', '', 'class="form-control select2" id="signature" multiple="multiple"'); ?>
            </div>
            <?php } ?>

            
        </div><!--SMSD-->
        <div class="row "><!--SMSD-->
            <div class="form-group col-sm-4 hide" id="employeerdirect"><!--SMSD-->
                <label>Bank Transfer Details </label>
                <textarea class="form-control" rows="3" name="bankTransferDetails" id="bankTransferDetails"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button id="submitbtn" class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
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
            <h4 class="modal-title" id="paymentVoucher_attachment_label"><?php echo $this->lang->line('accounts_payable_modal_title');?><!--Modal title--></h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><!--File Name--><?php echo $this->lang->line('common_file_name');?></th>
                        <th><!--Description--><?php echo $this->lang->line('common_description');?></th>
                        <th><!--Type--><?php echo $this->lang->line('common_type');?></th>
                        <th><!--Action--><?php echo $this->lang->line('common_action');?></th>
                    </tr>
                    </thead>
                    <tbody id="paymentVoucher_attachment" class="no-padding">
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
<?php
/** sub item master modal created by Shafry */
$this->load->view('system/grv/sub-views/inc-sub-item-master');
?>
<div class="modal fade" id="payment_voucher_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="payment_voucher_detail_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_voucher_modal');?><!--Payment Voucher Modal--></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header bg-yellow">
                                    <h4><?php echo $this->lang->line('accounts_payable_tr_pv_payment_voucher');?><!--Payment Voucher--></h4>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">

                                        <?php
                                        $norecfound=$this->lang->line('common_no_records_found');
                                        echo '<li><a>'.$norecfound.'<!--No Records found--></a></li>'; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_tr_pv_booking_invoice_code');?> </th><!--Booking Invoice Code-->
                                    <th style="min-width: 40%"><?php echo $this->lang->line('accounts_payable_tr_pv_invoice_no');?><!--Invoice No--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('common_invoice_date');?> </th><!--Invoice Date-->
                                    <th style="min-width: 5%"><?php echo $this->lang->line('accounts_payable_tr_pv_invoice_amount');?> <!--Invoice Amount--></th>
                                    <th style="min-width: 5%"><?php echo $this->lang->line('accounts_payable_tr_pv_balance_amount');?><!--Balance Amount--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_tr_pv_payment_amount');?><!--Payment Amount--></th>
                                </tr>
                                </thead>
                                <tbody id="table_body">
                                <tr class="danger">
                                    <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                                </tr>
                                </tbody>
                                <tfoot id="table_tfoot">

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-lg" id="prq_party_modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">Select Supplier</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Supplier</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('prqpartyID', all_supplier_drop(), '', 'class="form-control select2" id="prqpartyID" required 
                            onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="set_partyID_details()">Add Supplier</button>
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
                            <?php echo form_dropdown('supplier
                            group', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
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



<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var PayVoucherAutoId;
    var fromedit;
    var pvType;
    var partyID;
    var currencyID;
    var p_id;/**SMSD */
    var gl_id;/**SMSD */
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/payment_voucher/payment_voucher_management', PayVoucherAutoId, 'Payment Voucher');
        });
        PayVoucherAutoId = null;
        pvType = null;
        partyID = null;
        currencyID = null;

        $(".paymentmoad").hide();

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#paymentvoucher_form').bootstrapValidator('revalidateField', 'PVdate');
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            PayVoucherAutoId = p_id;
            fromedit = 2;
            load_payment_voucher_header();
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
        } else {
            fromedit=1
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

        $('#paymentvoucher_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                vouchertype: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_voucher_type_is_required');?>.'}}},/*Voucher Type is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                PVdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_date_is_required');?>.'}}},/*Payment Voucher Date is required*/
                //referenceno: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_reference_is_required');?>.'}}},/*Reference # is required*/
                supplier: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_supplier_is_required');?>.'}}},/*Supplier is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_tranasaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                paymentvouchercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_payment_voucher_code_is_required');?>.'}}},/*Payment Voucher Code is required*/
                //PVbankCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_bank_or_cash_is_required');?>.'}}},/*Bank or Cash is required*/
                //PVchequeDate: {validators: {notEmpty: {message: 'Bank or Cash is required.'}}},
               // narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_cheque_date_is_required');?>.'}}},/*Cheque Date is required*/
                paymentMode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_pv_mode_of_payment_required');?>.'}}}/*Mode of Payment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#vouchertype").prop("disabled", false);
            $("#pvtype").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#partyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'PayVoucherAutoId', 'value': PayVoucherAutoId});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'SupplierDetails', 'value': $('#supplier option:selected').text()});
            data.push({'name': 'bank', 'value': $('#PVbankCode option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_paymentvoucher_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data_arr) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data_arr['status']) {
                        PayVoucherAutoId = data_arr['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
                        pvType = $('#pvtype').val();
                        partyID = $('#supplier').val();
                        var result = $('#transactionCurrencyID option:selected').text().split('|');
                        currencyID = result[0];
                        $('.btn-wizard').removeClass('disabled');
                        fetch_details(4);
                        $("#vouchertype").prop("disabled", true);
                        $("#pvtype").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#partyID").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                    }// else {
                        $('#submitbtn').prop('disabled', false);
                    //}

                    //save_signature_authority_pv();/**SMSD */
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }).on('error.form.bv', function (e) {

            $('#submitbtn').prop('disabled', false);

        });

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });




    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function fetch_cheque_number(GLAutoID) {
        gl_id = GLAutoID;/**SMSD */
        $('#signature').empty();
        if (!jQuery.isEmptyObject(GLAutoID)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'GLAutoID': GLAutoID,'PvID':p_id},
                url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
                success: function (data) {
                    if (data['master']) {
                        if(data['master']['bankCheckNumber']!='NaN')
                        {

                            if(p_id){
                                $("#PVchequeNo").val((parseFloat(data['master']['bankCheckNumber'])));
                            }else{
                                $("#PVchequeNo").val((parseFloat(data['master']['bankCheckNumber']) + 1));
                            }
                        }else
                        {
                            $("#PVchequeNo").val(" ");
                        }

                        /*if($('#vouchertype').val()=='Supplier'){*/
                        if (data['master']['isCash'] == 1) {
                            $("#employeerdirect").addClass('hide');
                            $(".paymentmoad").hide();
                            $('.paymentType').addClass('hide');
                            $('.banktrans').addClass('hide');
                        } else {
                            $('.paymentType').removeClass('hide');
                            show_payment_method();
                            //$(".paymentmoad").show();
                        }
                    }
                    if (data['detail']) {
                        $('#chequeRegisterDetailID').empty();
                        var mySelect = $('#chequeRegisterDetailID');
                        mySelect.append($('<option></option>').val('').html('Select Cheque no'));
                        if (!jQuery.isEmptyObject(data['detail'])) {
                            $.each(data['detail'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['chequeRegisterDetailID']).html(text['chequeNo'] + ' - ' + text['description']));
                            });
                            ;
                        }
                    }
                }
            });
        }else{
            $('.paymentType').addClass('hide');
            $('.banktrans').addClass('hide');
        }

    }

/**SMSD */
    function save_signature_authority_pv(){
        if(gl_id){
            $signature = $('#signature').val();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'gl_autoID': gl_id,'signature_id': $signature},
                    url: "<?php echo site_url('Payment_voucher/save_signature_authority_pv'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            var mySelect = $('#signature');
                            mySelect.empty();
                        } 
                    }, 
                    error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }
    }

    function confirmation() {
        if (PayVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*you want to confirm this document!*/
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
                               }else{
                                myAlert('s', data['message']);
                                fetchPage('system/payment_voucher/payment_voucher_management', PayVoucherAutoId, 'Payment Voucher');
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function fetch_details(tab) {
        $('.nav-tabs a[href="#tab_1"]').tab('show');
        if (tab == 1) {
            $('.nav-tabs a[href="#tab_4"]').tab('show');
        }
        if (tab == 2) {
            $('.nav-tabs a[href="#tab_3"]').tab('show');
        }
        if (tab == 3) {
            $('.nav-tabs a[href="#tab_2"]').tab('show');
        }
        if (tab == 4) {
            $('.nav-tabs a[href="#tab_1"]').tab('show');
        }
        if (tab == 5) {
            $('.nav-tabs a[href="#tab_5"]').tab('show');
        }
        if (tab == 7) {
            $('.nav-tabs a[href="#tab_7"]').tab('show');
        }

        fetch_detail(pvType, partyID, currencyID, tab);
    }

    function load_conformation() {
        if (PayVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payVoucherAutoId': PayVoucherAutoId, 'html': true},
                url: "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
                    attachment_modal_paymentVoucher(PayVoucherAutoId, "<?php echo $this->lang->line('accounts_payable_tr_payment_voucher');?> ", "PV");/*Payment Voucher*/
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

    function fetch_detail(type, partyID, currencyID, tab) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'PayVoucherAutoId': PayVoucherAutoId,
                'pvType': type,
                'partyID': partyID,
                'currencyID': currencyID,
                'tab': tab
            },
            url: "<?php echo site_url('Payment_voucher/fetch_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                //check_detail_dataExist(PayVoucherAutoId);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_detail_dataExist(PayVoucherAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'PayVoucherAutoId': PayVoucherAutoId},
            url: "<?php echo site_url('Grv/fetch_detail_header_lock'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data)) {
                    $("#vouchertype").prop("disabled", true);
                    $("#pvtype").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    setTimeout(function () {
                        $("#partyID").prop("disabled", true);
                    }, 500);
                } else {
                    $("#vouchertype").prop("disabled", true);
                    $("#pvtype").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    setTimeout(function () {
                        $("#partyID").prop("disabled", true);
                    }, 500);
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
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'PV'},
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

    function payment_voucher_detail_model() {
        $("#payment_voucher_model").modal({backdrop: "static"});
    }

    function load_payment_voucher_header() {
        if (PayVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'PayVoucherAutoId': PayVoucherAutoId},
                url: "<?php echo site_url('Payment_voucher/load_payment_voucher_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
                        pvType = data['pvType'];
                        partyID = data['partyID'];
                        currencyID = data['transactionCurrency'];
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#paymentvouchercode").val(data['PVcode']);
                        $('#PVdate').val(data['PVdate']);
                        $('#paymentbank').val(data['BPVbank']);
                        $('#account').val(data['PVAccount']);
                        $('#narration').val(data['PVNarration']);
                        purchaseOrderPolicy = '<?php echo $purchaseOrderPolicy?>';
                        if(purchaseOrderPolicy!=1 && data['documenttype']=='PurchaseRequest')
                        {
                            $('#vouchertype').append('<option value="PurchaseRequest" >Purchase Request</option>');
                            $('#pvtype').val('PurchaseRequest').change();
                        }

                        if(data['documenttype']=='DirectItem' || data['documenttype']=='SupplierItem' || data['documenttype']=='EmployeeItem' )
                        {
                            $('.pvtypeHideShow').removeClass('hide');
                            $('#vouchertype').val('DirectItem1').change();
                            $('#pvtype').val(data['documenttype']).change();
                        }else if(data['documenttype'] == 'DirectExpense' || data['documenttype'] == 'SupplierExpense' || data['documenttype'] == 'EmployeeExpense')
                        {
                            $('.pvtypeHideShow').removeClass('hide');
                            $('#vouchertype').val('DirectExpense1').change();
                            $('#pvtype').val(data['documenttype']).change();
                        }else {
                            $('.pvtypeHideShow').addClass('hide');
                            $('#vouchertype').val(data['documenttype']).change();
                            $('#pvtype').val(data['documenttype']).change();
                        }



                        $('#referenceno').val(data['referenceNo']);
                        $('#PVbankCode').val(data['PVbankCode']).change();
                        $('#PVchequeDate').val(data['PVchequeDate']);
                        $('#paymentType').val(data['paymentType']);

                        if (data['accountPayeeOnly']== 1) {
                            $('#accountPayeeOnly').iCheck('check');
                        }
                        //$('#paymentMode').val(data['modeOfPayment']);
                        if (data['modeOfPayment'] == 0 && (data['pvType'] == 'Supplier' || data['pvType'] == 'SupplierAdvance' || data['pvType'] == 'SupplierInvoice' || data['pvType'] == 'SupplierItem' || data['pvType'] == 'SupplierExpense')) {
                            $('.paymentType').removeClass('hide');
                            //$(".paymentmoad").show();
                            if(data['paymentType']==1){
                                $(".paymentmoad").show();
                            }
                        }else if(data['modeOfPayment'] == 0 && (data['pvType'] == 'PurchaseRequest')){
                            $(".paymentmoad").show();
                        }else if(data['modeOfPayment'] == 0 && (data['pvType'] == 'Direct' || data['pvType'] == 'DirectItem' || data['pvType'] == 'DirectExpense' || data['pvType'] == 'Employee' || data['pvType'] == 'EmployeeExpense' || data['pvType'] == 'EmployeeItem')){
                                $(".paymentmoad").show();
                        }

                        if(data['paymentType']==1){
                            $(".banktrans").addClass('hide');
                            $("#employeerdirect").addClass('hide');

                            setTimeout(function(){
                                load_payment_voucher_Signatures();
                            }, 1000);
                            

                        }else if(data['paymentType']==2 && data['pvType'] == 'PurchaseRequest' && data['partyID']==0){
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
                        }else if(data['paymentType']==2 && data['pvType'] == 'PurchaseRequest' && data['partyID']>0) {


                        }else{
                            if (data['pvType'] == 'Direct' || data['pvType'] == 'DirectItem' || data['pvType'] == 'DirectExpense' || data['pvType'] == 'Employee' || data['pvType'] == 'EmployeeExpense' || data['pvType'] == 'EmployeeItem') {
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
                            }else{
                                $(".banktrans").addClass('show');
                                $("#employeerdirect").removeClass('show');
                                $("#employeerdirect").addClass('hide');
                            }
                        }

                        if (data['pvType'] == 'Direct' || data['pvType'] == 'DirectItem' || data['pvType'] == 'DirectExpense') {
                            voucher_type(data['pvType'], data['partyName']);
                        }else if(data['pvType'] == 'PurchaseRequest'){
                            voucher_type(data['pvType'], data['partyID']);
                        } else if (data['pvType'] == 'Employee' ||data['pvType'] == 'EmployeeExpense' ||data['pvType'] == 'EmployeeItem') {
                            voucher_type(data['pvType'], data['partyID']);
                        } else {
                            voucher_type(data['pvType'], data['partyID']);
                        }
                        fetch_detail(data['pvType'], data['partyID'], data['transactionCurrencyID']);
                        setTimeout(function () {
                            $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        }, 1000);
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#partyName').val(data['partyName']);

                        setTimeout(function(){
                            $('#partyID').val(partyID).change();
                            $('#PVchequeNo').val(data['PVchequeNo']);
                            <?php
                            if($chequeRegister==1){
                            ?>
                            setTimeout(function () {
                                $('#chequeRegisterDetailID').val(data['chequeRegisterDetailID']).change();
                            }, 1000);

                            <?php } ?>
                        }, 2000);
                        setTimeout(function(){
                            $('#supplierBankMasterID').val(data['supplierBankMasterID']);
                        }, 2500);

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

    function load_payment_voucher_Signatures() {
        
        if (PayVoucherAutoId) {
            
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'PayVoucherAutoId': PayVoucherAutoId},
                url: "<?php echo site_url('Payment_voucher/load_payment_voucher_Signatures'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {

                        var sig_arr = [];

                        $.each(data, function (key, value) {
                            sig_arr.push(value['signatureID']);
                        });

                        setTimeout(function(){
                           // $('#supplierBankMasterID').val(data['supplierBankMasterID']);
                           $('#signature').val(sig_arr).change();
                        }, 1000);
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
        if (PayVoucherAutoId) {
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
                    fetchPage('system/payment_voucher/payment_voucher_management', PayVoucherAutoId, 'Payment Voucher');
                });
        }
    }

    function attachment_modal_paymentVoucher(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#paymentVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachment');?>");/*Attachments*//**SMSD */
                    $('#paymentVoucher_attachment').empty();
                    $('#paymentVoucher_attachment').append('' +data+ '');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_paymentVoucher_attachment(PayVoucherAutoId, DocumentSystemCode, myFileName) {
        if (PayVoucherAutoId) {
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
                        data: {'attachmentID': PayVoucherAutoId, 'myFileName': myFileName},
                        url: "<?php echo site_url('Payable/delete_paymentVoucher_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/
                                attachment_modal_paymentVoucher(DocumentSystemCode, "Payment Voucher", "PV");
                            } else {
                                myAlert('e', '<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function show_payment_method(){
        if ($("#paymentType").val() == 1) {
            $(".paymentmoad").show();
            $('.banktrans').addClass('hide');
            $('#employeerdirect').addClass('hide');
            $("#signature_field").removeClass('hide');
            if(gl_id){/**SMSD start*/
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'gl_autoID': gl_id},
                    url: "<?php echo site_url('Payment_voucher/fetch_signature_authority_on_pv'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if (!jQuery.isEmptyObject(data)) {
                            
                            var mySelect = $('#signature');
                            mySelect.empty();
                           // mySelect.append($('<option></option>').val('').html('Select signature'));
                            $.each(data, function (key, value) {
                                mySelect.append($('<option></option>').val(key).html(value));
                            });
                            
                        }else{
                            //$("#signature_field").addClass('hide');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }/**SMSD end*/

        }else if ($("#paymentType").val() == 2 && $('#pvtype').val()=='PurchaseRequest' && (!jQuery.isEmptyObject($('#partyID').val()) && $('#partyID').val()>0)) {
            $(".paymentmoad").hide();
            $('#employeerdirect').addClass('hide');
            $('#supplierBankMasterID').removeClass('hide');
            $("#signature_field").addClass('hide');
            var supplierID= $("#partyID").val();
            if(supplierID){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'supplierID': supplierID},
                    url: "<?php echo site_url('Payment_voucher/get_supplier_banks'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            $('#supplierBankMasterID').empty();
                            $.each(data, function (key, value) {
                                $('#supplierBankMasterID').append('<option value="' + value['supplierBankMasterID'] + '">' + value['bankName'] + '</option>');
                            });
                            $('.banktrans').removeClass('hide');
                        }else{
                            $('#supplierBankMasterID').empty();
                            $('#supplierBankMasterID').append('<option value="">No Records Found</option>');
                            $('.banktrans').removeClass('hide');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }else{
                //myAlert('w','Select Supplier')
            }
        }else if ($("#paymentType").val() == 2 && $('#pvtype').val()=='PurchaseRequest' && (jQuery.isEmptyObject($('#partyID').val()) || $('#partyID').val()==0)) {
            $('#supplierBankMasterID').addClass('hide');
            $('#employeerdirect').removeClass('hide');
            $("#signature_field").addClass('hide');
            $(".paymentmoad").hide();
            var invoiceNote='<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
            if (p_id) {

            }else{
                $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
            }
        }else if($("#paymentType").val() == 2 && ($('#pvtype').val()=='Supplier' || $('#pvtype').val()=='SupplierAdvance' || $('#pvtype').val()=='SupplierInvoice' || $('#pvtype').val()=='SupplierItem' || $('#pvtype').val()=='SupplierExpense')) {
            $(".paymentmoad").hide();
            $('#employeerdirect').addClass('hide');
            $('#supplierBankMasterID').removeClass('hide');
            $("#signature_field").addClass('hide');
            var supplierID= $("#partyID").val();
            if(supplierID){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'supplierID': supplierID},
                    url: "<?php echo site_url('Payment_voucher/get_supplier_banks'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            $('#supplierBankMasterID').empty();
                            $.each(data, function (key, value) {
                                $('#supplierBankMasterID').append('<option value="' + value['supplierBankMasterID'] + '">' + value['bankName'] + '</option>');
                            });
                            $('.banktrans').removeClass('hide');
                        }else{
                            $('#supplierBankMasterID').empty();
                            $('#supplierBankMasterID').append('<option value="">No Records Found</option>');
                            $('.banktrans').removeClass('hide');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            }else{
                //myAlert('w','Select Supplier')
            }

        }else if($("#paymentType").val() == 2 && ($('#pvtype').val()=='Direct' || $('#pvtype').val()=='DirectItem' || $('#pvtype').val()=='DirectExpense' || $('#pvtype').val()=='Employee') || $('#pvtype').val()=='EmployeeExpense' || $('#pvtype').val()=='EmployeeItem') {
            $('#supplierBankMasterID').addClass('hide');
            $('#employeerdirect').removeClass('hide');
            $(".paymentmoad").hide();
            $("#signature_field").addClass('hide');

            if($('#pvtype').val()=='Employee'){
                update_bank_transferDet();
            }
            else{
                var invoiceNote='<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
                if (p_id) {

                }else{
                    $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
                }
            }
        } else{
            $('#employeerdirect').addClass('hide');
            $('.banktrans').addClass('hide');
            $(".paymentmoad").hide();
            $("#signature_field").addClass('hide');
        }
    }
    function clear_partyID_req_details() {
        $('#partyName').val('');
        $('#partyID').val('');
        $('#PVbankCode').val('').change();
        $('#prqpartyID').val('').change();
        show_payment_method()
    }
    function clear_partyID_req_id() {
        $('#partyID').val('');
        $('#PVbankCode').val('').change();
        $('#prqpartyID').val('').change();
    }

    function link_partyID_irq_modal() {
        $('#prqpartyID').val('').change();
        $('#prq_party_modal').modal('show');
    }

    function set_partyID_details(){
        var prqpartyID=$('#prqpartyID').val();
        $('#partyID').val(prqpartyID);
        $('#partyName').val($('#prqpartyID option:selected').text());
        $('#prq_party_modal').modal('hide');
        show_payment_method()
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
                if(data['status'] == true)
                {
                    $('#emp_model').modal('hide');
                    voucher_type('Supplier','',data['last_id']);
                    fetch_supplier_currency_by_id(data['last_id']);

                }else if(data['status'] == false)
                {
                    $('#emp_model').modal('show');

                }
                stopLoad();
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                $('#emp_model').modal('show');
                refreshNotifications(true);
            }
        });


    }
    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        //$('#paymentType').change();
        var PVbankCode=$('#PVbankCode').val();
        $('#PVbankCode').val(PVbankCode).change();
        show_payment_method();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierAutoID': supplierAutoID},
            url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
            success: function (data) {
                if (data.supplierCurrencyID) {
                    $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                    //currency_validation_modal(data.supplierCurrencyID,'PV',supplierAutoID,'SUP');
                }
                ;
            }
        });
    }

    function voucher_type(value, select_value,supID) {

        if((value!='DirectItem1') && (value!='DirectExpense1'))
        {
            $("#employeerdirect").addClass('hide');
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

            if (select_value == 'undefined') {
                select_value = '';
            }
            if (value == 'Direct' || value == 'DirectItem' || value == 'DirectExpense') {
                $('.startmark').removeClass('hidden');
                $('#party_text').text('Payee Name');
                $('#party_textbox').html('<input type="text" name="partyName" id="partyName" value="" class="form-control" >');
            }else if(value == 'PurchaseRequest'){
                $('#prqpartyID').val(select_value).change();
                if (select_value == 'undefined') {
                    select_value = '';
                }else{
                    set_partyID_details()
                }
                $('.startmark').removeClass('hidden');
                $('#party_text').text('Payee Name');
                $('#party_textbox').html(' <div class="input-group"> <input type="text" class="form-control" id="partyName" name="partyName" onkeyup="clear_partyID_req_id()" placeholder="Supplier Name" required> <input type="hidden" class="form-control" id="partyID" name="partyID">  <span class="input-group-btn"> <button class="btn btn-default" type="button" title="Clear" rel="tooltip" onclick="clear_partyID_req_details()" style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button> <button class="btn btn-default" type="button" title="Add Community Member" rel="tooltip" onclick="link_partyID_irq_modal()"style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button> </span> </div> <input type="hidden" name="contactID" id="contactID_edit">');
            }else {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'select_value': select_value, 'value': value,'masterid':p_id,'SupplierID':supID},
                    url: "<?php echo site_url('Payment_voucher/load_html'); ?>",
                    success: function (data) {
                        $('.startmark').removeClass('hidden');
                        if(value == 'EmployeeItem' || value == 'EmployeeExpense'){
                            $('#party_text').text('Employee');
                        } else if (value == 'SupplierAdvance' || value == 'SupplierInvoice' || value == 'SupplierItem' || value == 'SupplierExpense'){
                            $('#party_text').text('Supplier');
                        } else {
                            $('#party_text').text(value);
                        }
                        $('#party_textbox').html(data);
                        $('.select2').select2();
                    }
                });
            }
            if(value=='Direct' || value=='DirectItem' || value=='DirectExpense' || value=='Employee' || value=='EmployeeExpense' || value=='EmployeeItem'){
                $('.paymentType').addClass('hide');
                $('.banktrans').addClass('hide');
            }
            if((value=='Supplier' || value=='SupplierAdvance' || value=='SupplierInvoice' || value=='SupplierItem' || value=='SupplierExpense') && $('#PVbankCode').val()>0 && fromedit==1){
                $('#PVbankCode').val('').change();
            }
            if((value=='Direct' || value=='DirectItem' || value=='DirectExpense' || value=='Employee' || value=='EmployeeExpense' || value=='EmployeeItem' || value=='PurchaseRequest') && fromedit==1){
                $('#PVbankCode').val('').change();
            }
            if(fromedit==2){
                var PVbankCode=$('#PVbankCode').val();
                $('#PVbankCode').val(PVbankCode).change();
            }
        }else {
            $('.startmark').removeClass('hidden');
            $('#party_text').text('Payee Name');
            $('#party_textbox').html('<input type="text" name="partyName" id="partyName" value="" class="form-control" >');
        }

    }

    function update_bank_transferDet(){
        var empBank_obj = $('#partyID');
        var note = '';

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

        if( empBank_obj.val() != ''){
            empBank_obj = $('#partyID :selected');
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

    function check_cheque_used() {
        var chequeRegisterDetailID = $('#chequeRegisterDetailID').val();
        if (!jQuery.isEmptyObject(chequeRegisterDetailID)) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'PayVoucherAutoId': PayVoucherAutoId, 'chequeRegisterDetailID': chequeRegisterDetailID},
            url: "<?php echo site_url('Payment_voucher/check_cheque_used'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data == 1) {
                    myAlert('w', 'Cheque No has been used');
                    $('#chequeRegisterDetailID').val('').change();
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    }

    $( ".vouchertype" ).change(function() {
        $('.pvtypeHideShow').removeClass('hide');
        if(this.value == 'DirectItem1')
        {
            var voucherdocuments = {
                SupplierItem : 'Supplier',
                EmployeeItem : 'Employee',
                DirectItem : 'Other'

            };
            $('#pvtype').empty();

            var mySelect = $('#pvtype');
            $.each(voucherdocuments, function(val, text) {
                mySelect.append(
                    $('<option></option>').val(val).html(text)
                );
            });
            voucher_type($('#pvtype').val());

        }else if(this.value == 'DirectExpense1')
        {
            $('.pvtypeHideShow').removeClass('hide');
            var voucherdocuments = {
                SupplierExpense : 'Supplier',
                EmployeeExpense : 'Employee',
                DirectExpense : 'Other'
            };
            $('#pvtype').empty();
            var mySelect = $('#pvtype');
            $.each(voucherdocuments, function(val, text) {
                mySelect.append(
                    $('<option></option>').val(val).html(text)
                );
            });
            voucher_type($('#pvtype').val());
        }else{
            $('.pvtypeHideShow').addClass('hide');
            $('#pvtype').empty();
            var mySelect = $('#pvtype');
            mySelect.append(
                $('<option></option>').val(this.value)
            );

            voucher_type($('#pvtype').val());
        }

    });

</script>