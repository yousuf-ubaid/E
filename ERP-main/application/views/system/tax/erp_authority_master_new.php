<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->load->helpers('expense_claim');
$this->lang->load('common', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);

echo head_page($_POST['page_name'], false);

$country = load_country_drop();
$gl_code_arr = authority_gl_drop_without_control_accounts();
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

<div class="tab-content">
    <?php echo form_open('', 'role="form" id="authoritymaster_form"'); ?>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for=""><?php echo $this->lang->line('tax_secondary_code');?><!--Secondary Code--> <?php required_mark(); ?></label>
            <input type="text" class="form-control" id="authoritySecondaryCode" name="authoritySecondaryCode">
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierName"><?php echo $this->lang->line('tax_authority_name');?><!--Authority Name--> <?php required_mark(); ?></label>
            <input type="text" class="form-control" id="AuthorityName" name="AuthorityName" required>
        </div>
        <div class="form-group col-sm-4">
            <label for="liabilityAccount"><?php echo $this->lang->line('tax_liability_account');?><!--Liability Account--> <?php required_mark(); ?></label>
            <?php echo form_dropdown('taxPayableGLAutoID', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="taxPayableGLAutoID" required'); ?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
            <?php echo form_dropdown('currencyID', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" id="currencyID" required'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for=""><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="telephone" name="telephone">
            </div>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="email" name="email">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierFax"><?php echo $this->lang->line('common_fax');?><!--FAX--></label>
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="fax" name="fax">
            </div>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierAddress1"><?php echo $this->lang->line('common_address');?><!--Address--> </label>
            <textarea class="form-control" rows="2" id="address" name="address"></textarea>
        </div>

    </div>
    <hr>
    <div class="text-right m-t-xs">
        <button class="btn btn-primary-new size-lg" id="supplier_btn" type="submit">
            <?php echo $this->lang->line('accounts_payable_sm_add_save'); ?><!--Add Save--></button>
    </div>
    </form>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var taxAuthourityMasterID;
    $(document).ready(function () {
        $('#supplier_btn').text('<?php echo $this->lang->line('tax_add_authority'); ?>');/*Add Authority*/
        $('.headerclose').click(function () {
            fetchPage('system/tax/tax_authority_management', '', 'Authority Master');
        });
        $('.select2').select2();
        taxAuthourityMasterID = null;
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            taxAuthourityMasterID = p_id;
            laad_authority_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }
        $('#authoritymaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                authoritySecondaryCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('tax_secondary_code');?>.'}}},/*Secondary Code*/
                AuthorityName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('tax_authority_name');?>.'}}},/*Authority Name*/
                taxPayableGLAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('tax_liability_account');?> '}}},/*Liability Account*/
                currencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency');?>.'}}}/*Currency*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'taxAuthourityMasterID', 'value': taxAuthourityMasterID});
            data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Authority/save_authoritymaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    fetchPage('system/tax/tax_authority_management', 'Test', 'Authority Master');
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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
    });


    function laad_authority_header() {
        if (taxAuthourityMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxAuthourityMasterID': taxAuthourityMasterID},
                url: "<?php echo site_url('Authority/load_authority_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#supplier_btn').text('Update Authority');
                        taxAuthourityMasterID = data['taxAuthourityMasterID'];
                        $("#telephone").val(data['telephone']);
                        $("#authoritySecondaryCode").val(data['authoritySecondaryCode']);
                        $('#AuthorityName').val(data['AuthorityName']);
                        $('#fax').val(data['fax']);
                        $('#taxPayableGLAutoID').val(data['taxPayableGLAutoID']).change();
                        //$("#assteGLCode").prop("disabled", true);
                        $('#currencyID').val(data['currencyID']).change();
                        $("#currencyID").prop("disabled", true);
                        $('#email').val(data['email']);
                        $('#address').val(data['address']);
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
</script>