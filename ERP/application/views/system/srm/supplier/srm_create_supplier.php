<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('srm_helper');
$country_arr = load_all_countries();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

<?php echo form_open('', 'role="form" id="suppliermaster_form"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('srm_general_details');?></h2><!--GENERAL DETAILS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('srm_secondary_code');?></label><!--Secondary Code-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="suppliercode"
                                                                                     id="suppliercode"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('srm_secondary_code');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Secondary Code-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('srm_company_name');?></label><!--Company Name / Name-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="supplierName"
                                                                                     id="supplierName"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('srm_company_name');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Company Name / Name-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_currency');?></label><!--Currency-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field">
                          <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
                                   <span
                                       class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_Country');?></label><!--Country-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('srm_contact_information');?></h2><!--CONTACT INFORMATION-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('srm_telephone_no');?></label><!--Telephone No-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control" id="supplierTelephone"
                       name="supplierTelephone" placeholder="<?php echo $this->lang->line('srm_telephone_no');?>"><!--Telephone No-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_email');?></label><!--Email-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control" id="supplierEmail"
                       name="supplierEmail" placeholder="<?php echo $this->lang->line('common_email');?>"><!--Email-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_fax');?> </label><!--Fax-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control" id="supplierFax"
                       name="supplierFax" placeholder="<?php echo $this->lang->line('common_fax');?>"><!--Fax-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">URL</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control" id="supplierUrl"
                       name="supplierUrl" placeholder="URL">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address');?></label><!--Address-->
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" rows="2" id="supplierAddress1"
                          name="supplierAddress1"></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('srm_is_active');?></label><!--IS Active-->
            </div>
            <div class="form-group col-sm-1">
                <div class="col-sm-1">
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns"><input id="isActive" type="checkbox"
                                                                      data-caption="" class="columnSelected"
                                                                      name="isActive" value="1"><label
                                for="checkbox">&nbsp;</label></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-12 text-right">
                <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save');?></button><!--Save-->
            </div>
        </div>
    </div>
</div>
</form>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var supplierAutoID;
    $(document).ready(function () {
        $('#supplier_btn').text('Add Supplier');

        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_suppliermaster', '', 'Supplier Master');
        });
        $('.select2').select2();
        supplierAutoID = null;
        number_validation();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            supplierAutoID = p_id;
            load_supplier_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#suppliermaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                suppliercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_supplier_code_is_required');?>.'}}},/*Supplier Code is required*/
                supplierName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_supplier_name_is_required');?>.'}}},/*Supplier Name is required*/
                suppliercountry: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_supplier_country_is_required');?>.'}}},/*Supplier Country is required*/
                liabilityAccount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_liability_account_is_required');?>.'}}},/*Liability Account is required*/
                supplierCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_supplier_currency_is_required');?>.'}}}
            },/*Supplier Currency  is required*/
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'supplierAutoID', 'value': supplierAutoID});
            data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('srm_master/save_supplier'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['status'] == 1) {
                        myAlert('e', data['message']);
                    } else {
                        myAlert('s', data['message']);
                        fetchPage('system/srm/srm_suppliermaster', '', 'Customer Master');

                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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

    function addBank() {
        $('#supplierBankMasterID').val('');
        $('#bank_modal').modal('show');
    }

    function load_supplier_header() {
        if (supplierAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierAutoID': supplierAutoID},
                url: "<?php echo site_url('srm_master/load_supplier_header'); ?>",
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
                        $('#liabilityAccount').val(data['liabilityAutoID']).change();
                        //$("#assteGLCode").prop("disabled", true);
                        $('#supplierCurrency').val(data['supplierCurrencyID']).change();
                        //$("#supplierCurrency").prop("disabled", true);
                        $('#suppliercountry').val(data['supplierCountry']).change();
                        $('#suppliertaxgroup').val(data['taxGroupID']).change();
                        $('#supplierEmail').val(data['supplierEmail']);
                        $('#supplierUrl').val(data['supplierUrl']);
                        $('#supplierCreditPeriod').val(data['supplierCreditPeriod']);
                        $('#supplierCreditLimit').val(data['supplierCreditLimit']);
                        $('#supplierAddress1').val(data['supplierAddress1']);
                        $('#supplierAddress2').val(data['supplierAddress2']);
                        $('#partyCategoryID').val(data['partyCategoryID']).change();
                        if (data['isActive'] == 1) {
                            $('#isActive').iCheck('check');
                        }else {
                            $('#isActive').iCheck('uncheck');
                        }
                        //set_currency(data['supplierCurrency']);
                        // $('[href=#step2]').tab('show');
                        // $('a[data-toggle="tab"]').removeClass('btn-primary');
                        // $('a[data-toggle="tab"]').addClass('btn-default');
                        // $('[href=#step2]').removeClass('btn-default');
                        // $('[href=#step2]').addClass('btn-primary');
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

    function set_currency(val) {
        $('.currency').html(val);
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