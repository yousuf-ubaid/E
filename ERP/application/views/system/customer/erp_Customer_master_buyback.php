<!-- ===================================================================
This Was Created for assigning coutomer area setup needed for buyback

Created On : 29/03/2019
======================================================================== -->

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_add_new_customer');
echo head_page($title, false);

$this->load->helper('buyback_helper');
$location_arr = load_all_locations();
$country        = load_country_drop();
$gl_code_arr    = supplier_gl_drop();
$rebate_gl_code_arr    = fetch_all_gl_codes();
$currncy_arr    = all_currency_new_drop();
$country_arr    = array('' => 'Select Country');
$taxGroup_arr    = customer_tax_groupMaster();
$customerCategory    = party_category(1);
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
?>
<style>

</style>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('','role="form" id="customermaster_form"'); ?>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('sales_maraketing_masters_customer_secondary_code');?> <?php  required_mark(); ?></label><!--Customer Secondary Code-->
                    <input type="text" class="form-control" id="customercode" name="customercode">
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName"><?php echo $this->lang->line('sales_maraketing_masters_customer_name');?> <?php  required_mark(); ?></label><!--Customer Name-->
                    <input type="text" class="form-control" id="customerName" name="customerName" required>
                </div>
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('common_category');?> </label><!--Category-->
                    <?php  echo form_dropdown('partyCategoryID', $customerCategory, '','class="form-control select2"  id="partyCategoryID"'); ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="receivableAccount"><?php echo $this->lang->line('sales_maraketing_masters_receivable_account');?> <?php  required_mark(); ?></label><!--Receivable Account-->
                    <?php  echo form_dropdown('receivableAccount', $gl_code_arr,$this->common_data['controlaccounts']['ARA'],'class="form-control select2" id="receivableAccount" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerCurrency"><?php echo $this->lang->line('sales_maraketing_masters_customer_currency');?> <?php  required_mark(); ?></label><!--Customer Currency-->
                    <?php  echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'] ,'class="form-control select2" onchange="changecreditlimitcurr()" id="customerCurrency" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('sales_maraketing_masters_customer_country');?> <?php  required_mark(); ?></label><!--Customer Country-->
                    <?php  echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['company_country'] ,'class="form-control select2"  id="customercountry" required'); ?>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="customerArea"><?php echo $this->lang->line('common_area');?></label><!--Area-->
                    <div class="form-group">
                        <span class="input-req" title="Required Field" style="z-index: 12;">
                           <!-- <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="add-location" style="height: 27px; padding: 2px 10px;">
                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                    </button>
                                </span>-->
                                <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID"'); ?>
                           <!-- </div>-->
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerSubArea"><?php echo $this->lang->line('common_sub_area');?></label><!--Sub Area-->
                    <div class="form-group">
                        <span class="input-req" title="Required Field" style="z-index: 12;">
                            <!--<div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="add-sub-location" style="height: 27px; padding: 2px 10px;">
                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                    </button>
                                </span>-->
                                <?php echo form_dropdown('subLocationID', array("" => "Select Sub Area"), "", 'class="form-control select2" id="subLocationID"'); ?>
                           <!-- </div>-->
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('sales_maraketing_masters_tax_group');?> </label><!--Tax Group-->
                    <?php  echo form_dropdown('customertaxgroup', $taxGroup_arr, '','class="form-control select2"  id="customertaxgroup"'); ?>
                </div>
                
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('sales_maraketing_masters_id_card_no');?><!--ID card number--> </label>
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
                    <label for="customercustomerCreditPeriod"><?php echo $this->lang->line('sales_maraketing_masters_credit_period');?></label><!--Credit Period-->
                    <div class="input-group">
                        <div class="input-group-addon"><?php echo $this->lang->line('common_month');?> </div><!--Month-->
                        <input type="text" class="form-control number" id="customerCreditPeriod" name="customerCreditPeriod">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customercustomerCreditLimit"><?php echo $this->lang->line('sales_maraketing_masters_credit_limit');?></label><!--Credit Limit-->
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
                    <label for="customerTolerancePercentage"><?php echo $this->lang->line('sales_maraketing_masters_tolerance_percentage');?><!--Tolerance Percentage--></label>
                    <div class="input-group">
                        <input type="text" class="form-control number" id="customerTolerancePercentage" name="customerTolerancePercentage">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label><?php echo $this->lang->line('sales_maraketing_masters_tolerance_amount');?><!--Tolerance Amount--></label>
                    <div class="input-group">
                        <div class="input-group-addon"><span class="currency">LKR</span></div>
                        <input type="text" class="form-control number" id="customerToleranceAmount" name="customerToleranceAmount" readonly="readonly">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="vatEligible"><?php echo $this->lang->line('sales_maraketing_masters_vat_eligible');?><!--VAT Eligible--></label>
                    <?php echo form_dropdown('vatEligible', array('1'=> $this->lang->line('common_no'), '2'=> $this->lang->line('common_yes')), 1, 'class="form-control select2" id="vatEligible" required'); ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('sales_maraketing_masters_vat_identification_no');?><!--VAT Identification No--></label>
                    <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerAddress1"><?php echo $this->lang->line('sales_maraketing_masters_primary_address');?> </label><!--Primary Address-->
                    <textarea class="form-control" rows="2" id="customerAddress1" name="customerAddress1"></textarea>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerAddress2"><?php echo $this->lang->line('sales_maraketing_masters_secondary_address');?> </label><!--Secondary Address-->
                    <textarea class="form-control" rows="2" id="customerAddress2" name="customerAddress2"></textarea>
                </div>
               
            </div>
            <div class="row">
                <?php $rebate = getPolicyValues('CRP', 'All');
                if($rebate == 1) { ?>
                    <div class="form-group col-sm-4">
                        <label for="customerAddress2"><?php echo $this->lang->line('sales_maraketing_masters_rebate_GL_code'); ?> </label>
                        <!--Rebate GL Code-->
                        <?php echo form_dropdown('rebateGL', $rebate_gl_code_arr, '', 'class="form-control select2"  id="rebateGL"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="customerAddress1"><?php echo $this->lang->line('sales_maraketing_masters_rebate_percentage'); ?> </label>
                        <!--Rebate Percentage-->
                        <input class="form-control number" id="rebatePercentage" name="rebatePercentage">
                    </div>
                    <?php
                } ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for=""><?php echo $this->lang->line('sales_maraketing_masters_is_active');?> </label><!--isActive-->

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
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-primary-new size-lg" id="customer_btn" type="submit"><?php echo $this->lang->line('sales_maraketing_masters_add_customer');?> </button><!--Add Customer-->
            </div>
            </form>
        </div>
    </div>

<div class="modal fade" id="location-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Area</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Area</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add_locationID" name="add_locationID">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-location">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sub-location-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Sub Area</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Area</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="selectedArea" name="selectedArea" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Sub Area</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add_subLocationID" name="subLocationID">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-sub-location">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        var  subLocationID = '';
        var customerAutoID;

        var $creditLimitAmount = $('#customerCreditLimit');
        var $creditTolerancePercentage = $('#customerTolerancePercentage');
        var $creditTolleranceAmount = $('#customerToleranceAmount');



        $creditLimitAmount.on("keydown keyup", function(){
            calculateToleranceAmount();
        });
        $creditTolerancePercentage.on("keydown keyup", function(){
            calculateToleranceAmount();
        });

        $creditTolleranceAmount.on("keydown keyup", function() {
            calculateToleranceAmount();
        });
        function calculateToleranceAmount(){
            var climitAmount = $creditLimitAmount.val();
            var vTolerancePercentage = $creditTolerancePercentage.val();
            var toleranceTotal = parseInt((climitAmount * vTolerancePercentage) / 100);

            $creditTolleranceAmount.val(toleranceTotal);
        }

        $( document ).ready(function() {
            $('#customer_btn').text('<?php echo $this->lang->line('sales_maraketing_masters_add_customer');?>');/*Add Customer*/
            $('.select2').select2();
            $('.headerclose').click(function(){
                fetchPage('system/customer/erp_customerMas_perItem_amount','','Customer Master');
            });
            customerAutoID         = null;
            number_validation();
            p_id         = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                customerAutoID =p_id;
                load_customer_header();
                $('.btn-wizard').removeClass('disabled');
            }else{
                $('.btn-wizard').addClass('disabled');
            }
            $('#customermaster_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    customercode        : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_customer_code_required');?>.'}}},/*customer Code is required*/
                    customerName        : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_customer_name_required');?>.'}}},/*customer Name is required*/
                    customercountry     : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_customer_country_required');?>.'}}},/*customer Country is required*/
                    customerAddress1    : {validators: {notEmpty: {message: 'Address 1 is required.'}}},
                    customerAddress2    : {validators: {notEmpty: {message: 'Address 2 is required.'}}},
                    receivableAccount   : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_customer_receivabl_account_is_required');?>.'}}},/*Receivabl Account is required*/
                    customerCurrency    : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_customer_currency_is_required');?>.'}}},/*customer Currency  is required*/
                    customerName        : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_customer_name_required');?>.'}}}/*customer Name is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name' : 'customerAutoID', 'value' : customerAutoID });
                data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});
                $.ajax(
                    {
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Customer/save_customer_buyback'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            HoldOn.close();
                            refreshNotifications(true);
                            if (data['status'] == true) {
                                fetchPage('system/customer/erp_customerMas_perItem_amount', 'Test', 'customer Master');
                            }
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            HoldOn.close();
                            refreshNotifications(true);
                        }
                    });
            });

            $('#extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-blue',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });

            $("#locationID").change(function () {
                get_buyback_subArea($(this).val())
            });

            $('#add-location').click(function () {
                $('#add_locationID').val('');
                $('#location-modal').modal({backdrop: 'static'});
            });

            $('#add-sub-location').click(function () {
                $('#selectedArea').val('');
                var selectedArea = $('#locationID option:selected').text();
                $('#selectedArea').val(selectedArea);
                $('#add_subLocationID').val('');
                $('#sub-location-modal').modal({backdrop: 'static'});
            });
        });

        function load_customer_header(){
            if (customerAutoID) {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'customerAutoID':customerAutoID},
                    url :"<?php echo site_url('Customer/load_customer_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        if(!jQuery.isEmptyObject(data)){

                            $('#customer_btn').text('<?php echo $this->lang->line('sales_maraketing_masters_customer_update');?>');/*Update Customer*/
                            customerAutoID = data['customerAutoID'];
                            $("#customerTelephone").val(data['customerTelephone']);
                            $("#customercode").val(data['secondaryCode']);
                            $('#customerName').val(data['customerName']);
                            $('#customerFax').val(data['customerFax']);
                            $('#receivableAccount').val(data['receivableAutoID']).change();
                            $('#rebateGL').val(data['rebateGLAutoID']).change();
                            $('#rebatePercentage').val(data['rebatePercentage']);
                            //$("#assteGLCode").prop("disabled", true);
                            $('#customerCurrency').val(data['customerCurrencyID']).change();
                            $("#customerCurrency").prop("disabled", true);
                            $('#customercountry').val(data['customerCountry']).change();
                            $('#customerTelephone').val(data['customerTelephone']);
                            $('#customerEmail').val(data['customerEmail']);
                            $('#customerUrl').val(data['customerUrl']);
                            $('#customerCreditPeriod').val(data['customerCreditPeriod']);
                            $('#customerCreditLimit').val(data['customerCreditLimit']);
                           //$('#customerToleranceAmount').val(data['customerToleranceAmount']);
                            $('#customerTolerancePercentage').val(data['creditTolerancePercentage']);
                            $('#customerToleranceAmount').val(parseInt((data['customerCreditLimit'] * data['creditTolerancePercentage']) / 100));
                            $('#customerAddress1').val(data['customerAddress1']);
                            $('#customerAddress2').val(data['customerAddress2']);
                            $('#partyCategoryID').val(data['partyCategoryID']).change();
                            $('#customertaxgroup').val(data['taxGroupID']).change();
                            $('#vatEligible').val(data['vatEligible']).change();
                            $('#vatIdNo').val(data['vatIdNo']);
                            $('#IdCardNumber').val(data['IdCardNumber']);
                            $('#locationID').val(data['locationID']).change();
                            subLocationID = data['subLocationID'];

                            if (data['isActive'] == 1) {
                                $('#checkbox_isActive').iCheck('check');
                            } else {
                                $('#checkbox_isActive').iCheck('uncheck');
                            }
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

            function set_currency(val){
                $('.currency').html(val);
            }
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

        function get_buyback_subArea(locationID) {
           if(locationID){
               $.ajax({
                   async: true,
                   type: 'post',
                   dataType: 'json',
                   data: {locationID: locationID},
                   url: "<?php echo site_url('Buyback/fetch_buyback_subArea'); ?>",
                   beforeSend: function () {
                       startLoad();
                   },
                   success: function (data) {
                       stopLoad();
                       $('#subLocationID').empty();
                       var mySelect = $('#subLocationID');
                       mySelect.append($('<option></option>').val("").html("Select"));
                       if (!$.isEmptyObject(data)) {
                           $.each(data, function (k, text) {
                               mySelect.append($('<option></option>').val(text['locationID']).html(text['description']));
                           });
                       }
                       if(subLocationID){
                           mySelect.val(subLocationID).change();
                       }

                   },
                   error: function (jqXHR, textStatus, errorThrown) {
                       myAlert('e', '<br>Message: ' + errorThrown);
                   }
               });
           } else {
               $('#subLocationID').empty();
           }
        }

        $('#save-btn-location').click(function (e) {
            e.preventDefault();
            var location = $.trim($('#add_locationID').val());
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'location': location},
                url: '<?php echo site_url("Buyback/new_location"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    var location_drop = $('#locationID');
                    if (data[0] == 's') {
                        location_drop.append('<option value="' + data[2] + '">' + location + '</option>');
                        location_drop.val(data[2]);
                        $('#location-modal').modal('hide');

                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#save-btn-sub-location').click(function (e) {
            e.preventDefault();
            var location = $.trim($('#locationID').val());
            var subLocation = $.trim($('#add_subLocationID').val());
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'location': location, 'subLocation' :subLocation},
                url: '<?php echo site_url("Buyback/save_buyback_sub_location"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    var location_drop = $('#subLocationID');
                    if (data[0] == 's') {
                        location_drop.append('<option value="' + data[2] + '">' + subLocation + '</option>');
                        location_drop.val(data[2]);
                        $('#sub-location-modal').modal('hide');
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    </script>



<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 3/29/2019
 * Time: 9:56 AM
 */