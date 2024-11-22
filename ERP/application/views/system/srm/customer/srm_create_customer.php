<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('srm_helper');
$country_arr = load_all_countries();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$taxGroup_arr = customer_tax_groupMaster();
$customerCategory = party_category(1);
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

<?php echo form_open('', 'role="form" id="customermaster_form"'); ?>
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
                <label class="title"><?php echo $this->lang->line('srm_secondary_code');?></label> <!--Secondary Code-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="customercode"
                                                                                     id="customercode"
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
                               <span class="input-req" title="Required Field"><input type="text" name="customerName"
                                                                                     id="customerName"
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
                          <?php echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="customerCurrency" required'); ?>
                                   <span
                                       class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_Country');?></label> <!--Country-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="customercountry" required'); ?>
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
                <input type="text" class="form-control" id="customerTelephone"
                       name="customerTelephone" placeholder="<?php echo $this->lang->line('srm_telephone_no');?>"><!--Telephone No-->
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
                <input type="text" class="form-control" id="customerEmail"
                       name="customerEmail" placeholder="<?php echo $this->lang->line('common_email');?>"><!--Email-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_fax');?></label><!--Fax-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control" id="customerFax"
                       name="customerFax" placeholder="<?php echo $this->lang->line('common_fax');?>"><!--Fax-->
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
                <input type="text" class="form-control" id="customerUrl"
                       name="customerUrl" placeholder="URL">
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
                <textarea class="form-control" rows="2" id="customerAddress1"
                          name="customerAddress1"></textarea>
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
    var customerAutoID;
    $(document).ready(function () {
        $('#customer_btn').text('Add Customer');
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_customermaster', '', 'Customer Master');
        });
        customerAutoID = null;
        number_validation();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            customerAutoID = p_id;
            load_customer_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });


        $('#customermaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                customercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_customer_code_is_required');?>.'}}},/*customer Code is required*/
                customerName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_customer_name_is_required');?>.'}}},/*customer Name is required*/
                customercountry: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_customer_country_is_required');?>.'}}},/*customer Country is required*/
                receivableAccount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_receivable_account_is_required');?>.'}}},/*Receivabl Account is required*/
                customerCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('srm_customer_currency_is_required');?>.'}}},/*customer Currency  is required*/

            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'customerAutoID', 'value': customerAutoID});
            data.push({'name': 'currency_code', 'value': $('#customerCurrency option:selected').text()});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Srm_master/save_customer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['status'] == 'e') {
                            myAlert('e', data['message']);
                        } else {
                            //alert('look ok!');
                            myAlert('s', data['message']);
                            fetchPage('system/srm/srm_customermaster', '', 'Customer Master');

                        }

                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    }
                });
        });
    });


    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#customerCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#customerCurrency').val();
        currency_validation_modal(CurrencyID, 'CUS', '', 'CUS');
    }


    function load_customer_header() {
        if (customerAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'customerAutoID': customerAutoID},
                url: "<?php echo site_url('srm_master/load_customer_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#customer_btn').text('Update Customer');
                        customerAutoID = data['CustomerAutoID'];
                        $("#customerAutoID").val(customerAutoID);
                        $("#customerTelephone").val(data['customerTelephone']);
                        $("#customercode").val(data['secondaryCode']);
                        $('#customerName').val(data['CustomerName']);
                        $('#customerFax').val(data['customerFax']);
                        $('#receivableAccount').val(data['receivableAutoID']).change();
                        //$("#assteGLCode").prop("disabled", true);
                        $('#customerCurrency').val(data['customerCurrencyID']).change();
                        //$("#customerCurrency").prop("disabled", true);
                        $('#customercountry').val(data['customerCountry']).change();
                        $('#customerTelephone').val(data['customerTelephone']);
                        $('#customerEmail').val(data['customerEmail']);
                        $('#customerUrl').val(data['customerUrl']);
                        $('#customerCreditPeriod').val(data['customerCreditPeriod']);
                        $('#customerCreditLimit').val(data['customerCreditLimit']);
                        $('#customerAddress1').val(data['CustomerAddress1']);
                        $('#customerAddress2').val(data['customerAddress2']);
                        $('#partyCategoryID').val(data['partyCategoryID']).change();
                        $('#customertaxgroup').val(data['taxGroupID']).change();
//                        $('#CustomerSystemCode').val(data['customer']).change();
                        if (data['isActive'] == 1) {
                            $('#isActive').iCheck('check');
                        }else {
                            $('#isActive').iCheck('uncheck');
                        }


                        //set_currency(data['customerCurrency']);
                        // $('[href=#step2]').tab('show');
                        // $('a[data-toggle="tab"]').removeClass('btn-primary');
                        // $('a[data-toggle="tab"]').addClass('btn-default');
                        // $('[href=#step2]').removeClass('btn-default');
                        // $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    //refreshNotifications(true);
                }
            });
        }
    }

    function set_currency(val) {
        $('.currency').html(val);
    }
</script>