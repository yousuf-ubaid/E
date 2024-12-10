<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);

$country = load_country_drop();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
$company=group_company_drop_without_current();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');
if($ApprovalforSupplierMaster==NULL){
    $ApprovalforSupplierMaster=0;
}
$location_arr = fetch_all_location();
$customer_arr = all_customer_drop(true,1);


?>
<style>
#interCompanyGroup {
    display: none; /* Hide initially */
}
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_one');?><!--Step 1--> - <?php echo $this->lang->line('accounts_payable_sm_supplier_detail');?><!--Supplier Detail--></a></span>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_two');?><!--Step 2--> -  <?php echo $this->lang->line('accounts_payable_sm_bank_detail');?><!--Bank Detail--></a></span>
    </div>
    
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="suppliermaster_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('accounts_payable_sm_secondary_code');?><!--Secondary Code--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="suppliercode" name="suppliercode">
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_company_name');?><!--Company Name / Name--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="supplierName" name="supplierName" required>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_name_on_cheque');?><!--Name On Cheque--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_category');?><!--Category--></label>
                <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="liabilityAccount"><?php echo $this->lang->line('accounts_payable_sm_liability_account');?><!--Liability Account--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_Country');?><!--Country--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('accounts_payable_sm_tax_group');?><!--Tax Group--></label>
                <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_Location');?><!--Location--></label>
                <?php echo form_dropdown('supplierLocationID', $location_arr, '', 'class="form-control select2"  id="supplierLocationID"'); ?>
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
                <label for="supplierFax"><?php echo $this->lang->line('accounts_payable_sm_fax');?><!--FAX--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierFax" name="supplierFax">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('accounts_payable_sm_credit_period');?><!--Credit Period--></label>
                <div class="input-group">
                    <div class="input-group-addon"><?php echo $this->lang->line('common_month');?><!--Month--></div>
                    <input type="text" class="form-control number" id="supplierCreditPeriod"
                           name="supplierCreditPeriod">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('accounts_payable_sm_credit_limit');?><!--Credit Limit--></label>
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
                <label for="vatEligible"><?php echo $this->lang->line('accounts_payable_vat_eligible');?><!--VAT Eligible--></label>
                <?php echo form_dropdown('vatEligible', array('1'=> $this->lang->line('common_no'), '2'=> $this->lang->line('common_yes')), 1, 'class="form-control select2" id="vatEligible" required'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="">VAT Identification No</label>
                <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
            </div>

            <div class="form-group col-sm-4">
                <label for="vatPercentage">VAT <?php echo $this->lang->line('common_percentage');?><!--VAT Percentage--></label>
                <input type="text" class="form-control" id="vatPercentage" name="vatPercentage">
            </div>

        </div>
        <div class="row">

            <div class="form-group col-sm-4">
                <label for="supplierAddress1"><?php echo $this->lang->line('accounts_payable_sm_address_one');?><!--Address 1--></label>
                <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1"></textarea>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierAddress2"><?php echo $this->lang->line('accounts_payable_sm_address_two');?><!--Address 2--></label>
                <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2"></textarea>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for=""><?php echo $this->lang->line('accounts_payable_sm_is_active');?><!--isActive--></label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isActive" type="checkbox"
                                   data-caption="" class="columnSelected" name="isActive" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="form-group col-sm-4">
                <label for="paymentTerms">Payment Terms</label>
                <textarea class="form-control" rows="2" id="paymentTerms" name="paymentTerms"></textarea>
            </div>

            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_link_customer');?><!--Location--></label>
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2"  id="customerID"'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for=""><?php echo $this->lang->line('common_is_inter_company'); ?> </label>
                    <div class="skin skin-square">
                        <div class="skin-section">
                            <input type="checkbox" class="icheckbox_square_relative-blue iradio_square_relative-blue" data-caption="" onclick="check()" id="interCompanyCheck">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-4" id="interCompanyGroup">
                <label for="customerAddress1"><?php echo $this->lang->line('common_inter_company'); ?> </label>
                <?php echo form_dropdown('inter_company', array_column($company, 'cName', 'company_id'), '', 'class="form-control select2" id="inter_company"'); ?>

            </div>
        </div>
        <hr>
        <div class="" >
            <button id="supplier_btn" class="btn btn-primary pull-right" style="margin-right: 5px;" type="submit"><?php echo $this->lang->line('accounts_payable_sm_add_save');?><!--Add Save--></button>
        </div>
        </form>
        <?php if($ApprovalforSupplierMaster==1) {  ?>
            <div id="confirmbtn">
                <button class="btn btn-success btn-wizard pull-right" style="margin-right: 5px;" onclick="confirmation()">
                    <?php echo $this->lang->line('common_confirm');?></button>
            </div>
            <div id="approvebtn"></div>
            <div id="rejectbtn"></div>
            <!-- <div><button id="rejectbtn" class="btn btn-warning btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick="open_reject_model()">Reject</button></div>SMSD -->
        <?php  } ?>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-12 text-right" style="margin-bottom: 10px">
                <button type="button" class="btn btn-primary pull-right" onclick="addBank()"><i class="fa fa-plus"></i>
                    <?php echo $this->lang->line('accounts_payable_sm_create_bank');?><!--Create Bank-->
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="<?php echo table_class(); ?>" id="supplierbank_table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_bank');?><!--Bank--></th>
                        <th><?php echo $this->lang->line('common_address');?><!--Address--></th>
                        <th><?php echo $this->lang->line('accounts_payable_sm_account_name');?><!--Account Name--></th>
                        <th><?php echo $this->lang->line('accounts_payable_sm_account_number');?><!--Account Number--></th>
                        <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                        <th><?php echo $this->lang->line('accounts_payable_sm_swift');?><!--Swift--></th>
                        <th><?php echo $this->lang->line('accounts_payable_sm_iban_code');?><!--IBAN Code--></th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bank_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_payable_sm_bank_detail');?><!--Bank Detail--></h4>
            </div>
            <form class="form-horizontal" id="formbank">
                <div class="modal-body">


                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textinput"><?php echo $this->lang->line('accounts_payable_sm_bank_name');?><?php required_mark()?><!--Bank Name--></label>
                        <div class="col-md-6">
                            <input id="supplierBankMasterID" name="supplierBankMasterID" type="hidden"
                                   class="form-control">
                            <input id="bankName" name="bankName" type="text" class="form-control">

                        </div>
                    </div>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('common_currency');?><?php required_mark()?><!--Currency--></label>
                        <div class="col-md-6">
                            <?php $currency_arr = all_currency_new_drop();
                            echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID" required'); ?>
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textinput"><?php echo $this->lang->line('accounts_payable_sm_account_name');?><?php required_mark()?><!--Account Name--></label>
                        <div class="col-md-6">
                            <input id="accountName" name="accountName" type="text" class="form-control">

                        </div>
                    </div>


                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textinput"><?php echo $this->lang->line('accounts_payable_sm_account_number');?><?php required_mark()?><!--Account Number--></label>
                        <div class="col-md-6">
                            <input id="accountNumber" name="accountNumber" type="text" class="form-control">

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textinput"><?php echo $this->lang->line('accounts_payable_sm_swift_code');?><?php required_mark()?><!--SWIFT Code--></label>
                        <div class="col-md-6">
                            <input id="swiftCode" name="swiftCode" type="text" class="form-control">

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textinput"><?php echo $this->lang->line('accounts_payable_sm_iban_code');?><?php required_mark()?><!--IBAN Code--></label>
                        <div class="col-md-6">
                            <input id="ibanCode" name="ibanCode" type="text" class="form-control">

                        </div>
                    </div>

                    <!-- Textarea -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textarea"><?php echo $this->lang->line('common_address');?><?php required_mark()?><!--Address--></label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="address" name="address"></textarea>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save');?><!--Save--></button>

                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reject_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close fclose" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Reject Supplier Master Approval</h4>
                </div>
                <form role="form" id="reject_model_form" class="form-horizontal">
                    <div class="modal-body" id="modelBodyId">
                        <input type="hidden" id="supplierAutoID" name="supplierAutoID" class="form-control">
                        <div class="row" style="margin-left:10px; margin-right:10px;">
                            <div class="form-group col-sm-2">
                                <h5 class="form-control">Comment</h5> 
                            </div>
                            <div class="col-sm-1">
                                <h5>:</h5>
                            </div>
                            <div class="form-group col-sm-8">
                                <textarea id="comment" name="comment" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer ">
                    <button data-dismiss="modal" class="btn btn-default fclose" type="button"><?php echo $this->lang->line('common_Close'); ?></button>
                    <button class="btn btn-warning" type="button" onclick="reject_suppliermaster()">Submit</button>
                </div>
                
            </div>
        </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var supplierAutoID;
    var ApprovalforSupplierMaster = <?php echo $ApprovalforSupplierMaster ?>;

    function check() {
        var interCompanyCheck = document.getElementById('interCompanyCheck');
        var interCompanyGroup = document.getElementById('interCompanyGroup');

        if (interCompanyCheck && interCompanyGroup) {
            if (interCompanyCheck.checked) {
                interCompanyGroup.style.display = 'block';
            } else {
                interCompanyGroup.style.display = 'none';
                $('#inter_company').val('');
                interCompanyDropdown.removeAttribute('required');
            }
        }
    }

    $(document).ready(function () {
        $('#supplier_btn').text('Add Supplier');
        $('.headerclose').click(function () {
            fetchPage('system/supplier/erp_supplier_master', '', 'Supplier Master');
        });
        $('#inter_company').val('');
        $('.select2').select2();
        supplierAutoID = null;
        number_validation();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            supplierAutoID = p_id;
            if(ApprovalforSupplierMaster){
                load_aprovebtn(supplierAutoID);
            }
            laad_supplier_header();

            supplierbank();

            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('#formbank').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                suppliercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_supplier_code_is_required');?>.'}}},/*Supplier Code is required*/
                bankName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_bank_name_is_required');?>.'}}},/*Bank Name is required*/
                currencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                accountName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_account_name_is_required');?>.'}}},/*Account Name is required*/
                accountNumber: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_account_number_is_required');?>.'}}},/*Account Number is required*/
                swiftCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_swift_code_is_required');?>.'}}},/*Swift Code is required*/
                ibanCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_iban_code_is_required');?>.'}}},/*IBAN Code is required*/
                address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_bank_address_is_required');?>.'}}},/*Bank Address is required*/
                supplierAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_master_id_is_required');?>.'}}}/*MasterID is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'supplierAutoID', 'value': supplierAutoID});
            data.push({'name': 'currency_code', 'value': $('#currencyID option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Supplier/save_supplierbank'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#bank_modal').modal('hide');
                    $("#formbank")[0].reset();
                    $('#currencyID').val('').change();
                    $('#formbank').bootstrapValidator('resetForm', true);
                    supplierbank();
                    refreshNotifications(true);

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        $('#suppliermaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                suppliercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_supplier_code_is_required');?>.'}}},/*Supplier Code is required*/
                supplierName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_supplier_name_is_required');?>.'}}},/*Supplier Name is required*/
                suppliercountry: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_supplier_country_is_required');?>.'}}},/*Supplier Country is required*/
                nameOnCheque: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_name_on_cheque_required');?>.'}}},/*Name On Cheque is required*/
                liabilityAccount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_sm_liability_account_is_required');?>.'}}},/*Liability Account is required*/
                supplierCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_supplier_currency_is_required');?>.'}}}/*Supplier Currency is required*/
            },
        }).on('success.form.bv', function (e) {
            $('#liabilityAccount').prop("disabled", false);
            $('#supplierCurrency').prop("disabled", false);
            $('#suppliercountry').prop("disabled", false);
            $('#partyCategoryID').prop("disabled", false);
            $('#suppliertaxgroup').prop("disabled", false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'supplierAutoID', 'value': supplierAutoID});
            data.push({'name': 'suppliercode', 'value': $('#suppliercode').val()});
            data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Supplier/save_suppliermaster'); ?>",
                beforeSend: function () {
                    startLoad();
                    //$('#workFlowTemplateID').prop('disabled', true);
                    $('#liabilityAccount').prop("disabled", true);
                    $('#supplierCurrency').prop("disabled", true);
                    $('#suppliercountry').prop("disabled", true);
                    $('#partyCategoryID').prop("disabled", true);
                    $('#suppliertaxgroup').prop("disabled", true);
                },
                success: function (data) {
                    //HoldOn.close();
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0]=='s'){
                        fetchPage('system/supplier/erp_supplier_master', 'Test', 'Supplier Master');
                    }      
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
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
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });

    function addBank() {
        $('#supplierBankMasterID').val('');
        $('#bank_modal').modal('show');
    }

    function laad_supplier_header() {
        if (supplierAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierAutoID': supplierAutoID},
                url: "<?php echo site_url('Supplier/load_supplier_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#supplier_btn').text('Update Supplier');
                        supplierAutoID = data['supplierAutoID'];
                        $("#supplierTelephone").val(data['supplierTelephone']);
                        $("#suppliercode").val(data['secondaryCode']);
                        $('#supplierName').val(data['supplierName']);
                        $('#supplierFax').val(data['supplierFax']);
                        $('#customerID').val(data['customerID']);
                        $('#liabilityAccount').val(data['liabilityAutoID']).change();
                        //$("#assteGLCode").prop("disabled", true);
                        $('#supplierCurrency').val(data['supplierCurrencyID']).change();
                        $("#supplierCurrency").prop("disabled", true);
                        $('#suppliercountry').val(data['supplierCountry']).change();
                        $('#supplierLocationID').val(data['supplierLocationID']).change();
                        $('#suppliertaxgroup').val(data['taxGroupID']).change();
                        $('#vatIdNo').val(data['vatIdNo']);
                        $('#supplierTelephone').val(data['supplierTelephone']);
                        $('#supplierEmail').val(data['supplierEmail']);
                        $('#supplierUrl').val(data['supplierUrl']);
                        $('#supplierCreditPeriod').val(data['supplierCreditPeriod']);
                        $('#supplierCreditLimit').val(data['supplierCreditLimit']);
                        $('#vatEligible').val(data['vatEligible']).change();
                        $('#vatNumber').val(data['vatNumber']);
                        $('#vatPercentage').val(data['vatPercentage']);
                        $('#supplierAddress1').val(data['supplierAddress1']);
                        $('#supplierAddress2').val(data['supplierAddress2']);
                        $('#partyCategoryID').val(data['partyCategoryID']).change();
                        $('#nameOnCheque').val(data['nameOnCheque']);
                        $('#paymentTerms').val(data['paymentTerms']);
                        if(data['isSystemGenerated'] == 1){
                            $('#suppliercode').prop('disabled',true);
                        }

                        if (data['isActive'] == 1) {
                            $('#checkbox_isActive').iCheck('check');
                        } else {
                            $('#checkbox_isActive').iCheck('uncheck');
                        }

                        if (data['interCompanyYN'] == 1) {
                            $('#interCompanyCheck').iCheck('check');
                            $('#inter_company').val(data['interCompanyID']).change();
                            var interCompanyGroup = document.getElementById('interCompanyGroup');
                            interCompanyGroup.style.display = 'block';
                        } else {
                            $('#interCompanyCheck').iCheck('uncheck');
                            $('#inter_company').val('');
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
        }

        function set_currency(val) {
            $('.currency').html(val);
        }
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

    function editBankDetails(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierBankMasterID': id},
            url: "<?php echo site_url('Supplier/edit_Bank_Details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#supplierBankMasterID').val(id);
                    $('#bankName').val(data['bankName']);
                    $('#currencyID').val(data['currencyID']).change();
                    $('#accountName').val(data['accountName']);
                    $('#accountNumber').val(data['accountNumber']);
                    $('#ibanCode').val(data['IbanCode']);
                    $('#swiftCode').val(data['swiftCode']);
                    $('#address').val(data['bankAddress']);
                    $('#bank_modal').modal('show');
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




    function confirmation() {
        if (supplierAutoID && ApprovalforSupplierMaster) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dd6b55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'supplierAutoID': supplierAutoID},
                        url: "<?php echo site_url('Supplier/sup_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if(data[0]=='s'){
                                load_aprovebtn(supplierAutoID);
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

    function load_aprovebtn(supplierAutoID){
        if(supplierAutoID && ApprovalforSupplierMaster){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierAutoID': supplierAutoID},
                url: "<?php echo site_url('Supplier/check_supplier_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        //$('#approvebtn').empty();
                        if(data[2]=='2'){
                            $('#approvebtn').html('<button id="approve_btn" class="btn btn-success btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick="approve_suppliermaster(supplierAutoID)">Approve</button>');
                            $('#approve_btn').prop("disabled", true).val();
                        }else{
                            $('#approvebtn').html('<button id="approve_btn" class="btn btn-success btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick="approve_suppliermaster(supplierAutoID)">Approve</button>');
                            $('#rejectbtn').html('<button id="reject_btn" class="btn btn-warning btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick="open_reject_model(supplierAutoID)">Reject</button>');
                        }

                        $('#confirmbtn').empty();
                        $('#suppliercode').prop("readonly", true).val();
                        $('#supplierName').prop("readonly", true).val();
                        $('#nameOnCheque').prop("readonly", true).val();

                        $('#partyCategoryID').prop("disabled", true).val();
                        $('#liabilityAccount').prop("disabled", true);
                        $('#supplierCurrency').prop("disabled", true);
                        $('#suppliercountry').prop("disabled", true);
                        $('#suppliertaxgroup').prop("disabled", true).val();
                        $('#vatIdNo').prop("readonly", true).val();
                        $('#supplierTelephone').prop("readonly", true).val();
                        $('#supplierEmail').prop("readonly", true).val();
                        $('#supplierFax').prop("readonly", true).val();
                        $('#supplierCreditPeriod').prop("readonly", true).val();
                        $('#supplierCreditLimit').prop("readonly", true).val();
                        $('#supplierUrl').prop("readonly", true).val();
                        $('#supplierAddress1').prop("readonly", true).val();
                        $('#supplierAddress2').prop("readonly", true).val();

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

    function approve_suppliermaster(supplierAutoID) {

        if(ApprovalforSupplierMaster && supplierAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierAutoID': supplierAutoID},
                url: "<?php echo site_url('Supplier/approve_suppliermaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if(data[2]=='2'){
                        $('#approve_btn').prop("disabled", true).val();
                    }
                    if(data[0]=='s'){

                    }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }

    }
/**SMSD : create function */
    function reject_suppliermaster(){
        var data = $('#reject_model_form').serializeArray();
        if(supplierAutoID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Supplier/reject_suppliermaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);

                    $('#rejectbtn').prop("disabled", true).val();
                    $('#approve_btn').prop("disabled", true).val();
                    $('#supplierAutoID').val('');
                    $('#comment').val('');
                    $('#reject_modal').modal('hide');

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function open_reject_model(){
        $('#supplierAutoID').val(supplierAutoID);
        $('#reject_modal').modal({ backdrop: "static" });
    }
</script>