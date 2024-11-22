<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//echo fetch_account_review(false,true,$approval);
$country = load_country_drop();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
$ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');
if($ApprovalforSupplierMaster==NULL){
    $ApprovalforSupplierMaster=0;
}
?>
<div class="tab-content">
    <div id="step1" class="table-responsive">
        <?php echo form_open('', 'role="form" id="suppliermaster_form"'); ?>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('accounts_payable_sm_secondary_code');?><!--Secondary Code--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="suppliercode" name="suppliercode" readonly>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_company_name');?><!--Company Name / Name--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="supplierName" name="supplierName" required readonly>
            </div>
            <div  style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_name_on_cheque');?><!--Name On Cheque--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque" readonly>
            </div>
        </div>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_category');?><!--Category--></label>
                <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID" readonly'); ?>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="liabilityAccount"><?php echo $this->lang->line('accounts_payable_sm_liability_account');?><!--Liability Account--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required readonly'); ?>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required readonly'); ?>
            </div>
        </div>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_Country');?><!--Country--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required readonly'); ?>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('accounts_payable_sm_tax_group');?><!--Tax Group--></label>
                <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup" readonly'); ?>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="">VAT Identification No</label>
                <input type="text" class="form-control" id="vatIdNo" name="vatIdNo" readonly>
            </div>
        </div>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierTelephone"><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone" readonly>
                </div>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierEmail" name="supplierEmail" readonly>
                </div>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierFax"><?php echo $this->lang->line('accounts_payable_sm_fax');?><!--FAX--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierFax" name="supplierFax" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('accounts_payable_sm_credit_period');?><!--Credit Period--></label>
                <div class="input-group">
                    <div class="input-group-addon"><?php echo $this->lang->line('common_month');?><!--Month--></div>
                    <input type="text" class="form-control number" id="supplierCreditPeriod"
                           name="supplierCreditPeriod" readonly>
                </div>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('accounts_payable_sm_credit_limit');?><!--Credit Limit--></label>
                <div class="input-group">
                    <div class="input-group-addon"><span class="currency">LKR</span></div>
                    <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit" readonly>
                </div>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierUrl">URL</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierUrl" name="supplierUrl" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierAddress1"><?php echo $this->lang->line('accounts_payable_sm_address_one');?><!--Address 1--></label>
                <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1" readonly></textarea>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="supplierAddress2"><?php echo $this->lang->line('accounts_payable_sm_address_two');?><!--Address 2--></label>
                <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2" readonly></textarea>
            </div>
            <div  class="col-md-4">
                <div class="form-group">
                    <label for=""><?php echo $this->lang->line('accounts_payable_sm_is_active');?><!--isActive--></label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isActive" type="checkbox"
                                   data-caption="" class="columnSelected" name="isActive" value="1" checked readonly>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        </form>

    </div>
</div>
<script type="text/javascript">
    var supplierAutoID;
    $(document).ready(function () {
        supplierAutoID = null;

       // p_id = <?php //echo json_encode(trim($extra['supplierAutoID'] ?? '')); ?>;
        p_id =<?php echo $this->input->post('supplierAutoID'); ?>;
        if (p_id) {
            supplierAutoID = p_id;
            load_supplier_header();
        }

    });
    function load_supplier_header() {
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

                        supplierAutoID = data['supplierAutoID'];
                        $("#supplierTelephone").val(data['supplierTelephone']);
                        $("#suppliercode").val(data['secondaryCode']);
                        $('#supplierName').val(data['supplierName']);
                        $('#supplierFax').val(data['supplierFax']);
                        $('#liabilityAccount').val(data['liabilityAutoID']).change();
                        //$("#assteGLCode").prop("disabled", true);
                        $('#supplierCurrency').val(data['supplierCurrencyID']).change();
                        $("#supplierCurrency").prop("disabled", true);
                        $('#suppliercountry').val(data['supplierCountry']).change();
                        $('#suppliertaxgroup').val(data['taxGroupID']).change();
                        $('#vatIdNo').val(data['vatIdNo']);
                        $('#supplierTelephone').val(data['supplierTelephone']);
                        $('#supplierEmail').val(data['supplierEmail']);
                        $('#supplierUrl').val(data['supplierUrl']);
                        $('#supplierCreditPeriod').val(data['supplierCreditPeriod']);
                        $('#supplierCreditLimit').val(data['supplierCreditLimit']);
                        $('#supplierAddress1').val(data['supplierAddress1']);
                        $('#supplierAddress2').val(data['supplierAddress2']);
                        $('#partyCategoryID').val(data['partyCategoryID']).change();
                        $('#nameOnCheque').val(data['nameOnCheque']);
                        if (data['isActive'] == 1) {
                            $('#checkbox_isActive').iCheck('check');
                        } else {
                            $('#checkbox_isActive').iCheck('uncheck');
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
    }
</script>


