<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$country = load_country_drop();
$gl_code_arr = supplier_group_gl_drop();
$currncy_arr = all_currency_master_drop();
$country_arr = array('' => 'Select Country');
$taxGroup_arr = customer_tax_groupMaster();
$customerCategory = party_group_category(1);
//$customerLinkingCompany = customer_company_link();
if (isset($country))
{
    foreach ($country as $row)
    {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('config_common_step_one'); ?><!--Step 1--> - <?php echo $this->lang->line('config_customer_header'); ?><!--Customer Header--></a>
    <!--<a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">Step 2 - Customer Link</a>-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="customermaster_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('config_customer_secondary_code'); ?><!--Customer Secondary Code--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="customercode" name="customercode">
            </div>
            <div class="form-group col-sm-4">
                <label for="customerName"><?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="customerName" name="customerName" required>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_category'); ?><!--Category--></label>
                <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="receivableAccount"><?php echo $this->lang->line('config_customer_receivable_account'); ?><!--Receivable Account--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('receivableAccount', $gl_code_arr, "", 'class="form-control select2" id="receivableAccount" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="customerCurrency"><?php echo $this->lang->line('config_customer_currency'); ?><!--Customer Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" id="customerCurrency" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('config_customer_country'); ?><!--Customer Country--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="customercountry" required'); ?>
            </div>
        </div>
        <div class="row">
            <!--<div class="form-group col-sm-4">
                <label for="">Tax Group</label>
                <?php /*echo form_dropdown('customertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="customertaxgroup"'); */ ?>
            </div>-->
            <div class="form-group col-sm-4">
                <label for="">VAT <?php echo $this->lang->line('config_identification_no'); ?><!--Identification No--></label>
                <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
            </div>
            <div class="form-group col-sm-4">
                <label for="customerTelephone"><?php echo $this->lang->line('common_telephone'); ?><!--Telephone--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="customerTelephone" name="customerTelephone">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="customerEmail"><?php echo $this->lang->line('common_email'); ?><!--Email--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="customerEmail" name="customerEmail">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="customerFax"><?php echo $this->lang->line('common_fax'); ?><!--Fax--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="customerFax" name="customerFax">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="customercustomerCreditPeriod"><?php echo $this->lang->line('config_credit_period'); ?><!--Credit Period--></label>
                <div class="input-group">
                    <div class="input-group-addon"><?php echo $this->lang->line('common_month'); ?><!--Month--></div>
                    <input type="text" class="form-control number" id="customerCreditPeriod"
                        name="customerCreditPeriod">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="customercustomerCreditLimit"><?php echo $this->lang->line('config_credit_limit'); ?><!--Credit Limit--></label>
                <!-- <div class="input-group"> -->
                <!-- <div class="input-group-addon"><span class="currency"></span></div> -->
                <input type="text" class="form-control number" id="customerCreditLimit" name="customerCreditLimit">
                <!-- </div> -->
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="customerUrl">URL</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="customerUrl" name="customerUrl">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="customerAddress1"><?php echo $this->lang->line('config_primary_address'); ?><!--Primary Address--></label>
                <textarea class="form-control" rows="2" id="customerAddress1" name="customerAddress1"></textarea>
            </div>
            <div class="form-group col-sm-4">
                <label for="customerAddress2"><?php echo $this->lang->line('config_secondary_address'); ?><!--Secondary Address--></label>
                <textarea class="form-control" rows="2" id="customerAddress2" name="customerAddress2"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" id="customer_btn" type="submit"><?php echo $this->lang->line('config_add_customer'); ?><!--Add Customer--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <!--<div class="row">
            <div class="col-sm-12 pull-right">
                <button type="button" class="btn btn-primary pull-right" onclick="openLinkModal()"><i
                        class="fa fa-plus"></i> Create Link
                </button>
            </div>
        </div>
        <hr>-->
        <div class="table-responsive">
            <table id="customer_link_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="min-width:3% ;">#</th>
                        <th style="min-width:8% ;"><?php echo $this->lang->line('config_customer_code'); ?><!--Customer Code--></th>
                        <th style="min-width:10%;"><?php echo $this->lang->line('config_customer_company'); ?><!--Customer Company--></th>
                        <th style="min-width:40%;"> <?php echo $this->lang->line('config_customer_details'); ?><!--Customer Details--></th>
                        <th style="min-width:30%;"><?php echo $this->lang->line('config_gl_descriprion'); ?><!--GL Description--></th>
                        <th style="min-width:1% ;"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="customerLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="customerlink_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_customer_link'); ?><!--Customer Link--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for=""><?php echo $this->lang->line('common_company'); ?><!--Company--></label>
                                <?php echo form_dropdown('companyID', $customerLinkingCompany, '', 'class="form-control select2" onchange="load_comapny_customers()"  id="companyID"'); ?>
                            </div>
                            <div class="form-group col-sm-6" id="loadComapnyCustomers">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('config_common_add_link'); ?><!--Add Link-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var groupCustomerAutoID;
    var groupCustomerDetailID;
    var Otable;
    $(document).ready(function() {
        $('#customer_btn').text('<?php echo $this->lang->line('config_add_customer'); ?>'); /*Add Customer*/
        $('.select2').select2();
        $('.headerclose').click(function() {
            fetchPage('system/GroupMaster/erp_customer_group_master', '', 'Customer Master');
        });
        groupCustomerAutoID = null;
        groupCustomerDetailID = null;
        number_validation();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            groupCustomerAutoID = p_id;
            load_customer_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }
        $('#customermaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                customercode: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('config_customer_code_is_required'); ?>.'
                        }
                    }
                },
                /*customer Code is required*/
                customerName: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('config_customer_name_is_required'); ?>.'
                        }
                    }
                },
                /*customer Name is required*/
                customercountry: {
                    validators: {
                        notEmpty: {
                            message: 'customer Country is required.'
                        }
                    }
                },
                receivableAccount: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('config_gl_receivable_account_is_required'); ?>.'
                        }
                    }
                },
                /*Receivabl Account is required*/
                customerCurrency: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('config_gl_receivable_customer_currency_is_required'); ?>.'
                        }
                    }
                } /*customer Currency  is required*/
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({
                'name': 'groupCustomerAutoID',
                'value': groupCustomerAutoID
            });
            data.push({
                'name': 'currency_code',
                'value': $('#customerCurrency option:selected').text()
            });
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CustomerGroup/save_customer'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('.btn-wizard').removeClass('disabled');
                        groupCustomerAutoID = data[2];
                        //Otable.draw();
                        fetchPage('system/GroupMaster/erp_customer_group_master', '', 'Customer Master');
                    }

                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#customerlink_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({
                'name': 'groupCustomerDetailID',
                'value': groupCustomerDetailID
            });
            data.push({
                'name': 'groupCustomerMasterID',
                'value': groupCustomerAutoID
            });
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CustomerGroup/save_customer_link'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        //fetchPage('system/GroupMaster/erp_customer_group_master', '', 'Customer Master');
                        Otable.draw();
                        $('#customerLinkModal').modal('hide');
                    }

                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
    });

    function load_customer_header() {
        if (groupCustomerAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'groupCustomerAutoID': groupCustomerAutoID
                },
                url: "<?php echo site_url('CustomerGroup/load_customer_header'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#customer_btn').text('<?php echo $this->lang->line('config_update_customer'); ?>'); /*Update Customer*/
                        groupCustomerAutoID = data['groupCustomerAutoID'];
                        $("#customercode").val(data['secondaryCode']);
                        $('#customerName').val(data['groupCustomerName']);
                        $('#customerFax').val(data['customerFax']);
                        $('#receivableAccount').val(data['receivableAutoID']).change();
                        //$("#assteGLCode").prop("disabled", true);
                        $('#customerCurrency').val(data['customerCurrencyID']).change();
                        $("#customerCurrency").prop("disabled", true);
                        $('#customercountry').val(data['customerCountry']).change();
                        $('#customerTelephone').val(data['customerTelephone']);
                        $('#customerEmail').val(data['customerEmail']);
                        $('#customerUrl').val(data['customerUrl']);
                        $('#customerCreditPeriod').val(data['customerCreditPeriod']);
                        $('#customerCreditLimit').val(data['customerCreditLimit']);
                        $('#customerAddress1').val(data['customerAddress1']);
                        $('#customerAddress2').val(data['customerAddress2']);
                        $('#partyCategoryID').val(data['partyCategoryID']).change();
                        //$('#customertaxgroup').val(data['taxGroupID']).change();
                        $('#vatIdNo').val(data['vatIdNo']);
                        load_customer_link_table();
                        //$('#companyID').val(data['companyID']).change();
                        //load_comapny_customers();
                        //groupCustomerDetailID = data['groupCustomerDetailID'];
                        //setTimeout(function(){$('#customerMasterID').val(data['customerMasterID']).change(); }, 500);

                    }
                    stopLoad();
                    refreshNotifications(true);
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
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
        currncy = $('#customerCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#customerCurrency').val();
        currency_validation_modal(CurrencyID, 'CUS', '', 'CUS');
    }

    function load_comapny_customers() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                companyID: $('#companyID').val(),
                All: 'true'
            },
            url: "<?php echo site_url('CustomerGroup/load_comapny_customers'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapnyCustomers').html(data);
                $('.select2').select2();
                $('#loadComapnyCustomers').removeClass('hidden');
            },
            error: function() {

            }
        });
    }

    function load_customer_link_table() {
        Otable = $('#customer_link_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('CustomerGroup/fetch_customer_link'); ?>",
            "aaSorting": [
                [1, 'desc']
            ],
            "fnInitComplete": function() {

            },
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [{
                    "mData": "groupCustomerDetailID"
                },
                {
                    "mData": "customerSystemCode"
                },
                {
                    "mData": "company_name"
                },
                {
                    "mData": "customer_detail"
                },
                {
                    "mData": "GLDescription"
                },
                {
                    "mData": "edit"
                },
                {
                    "mData": "customerName"
                },
                {
                    "mData": "customerAddress1"
                },
                {
                    "mData": "customerAddress2"
                },
                {
                    "mData": "company_name"
                }
                /*{"mData": "Amount"}*/
            ],
            "columnDefs": [{
                "targets": [5],
                "orderable": false
            }, {
                "visible": false,
                "searchable": true,
                "targets": [6, 7, 8, 9]
            }],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "groupCustomerMasterID",
                    "value": groupCustomerAutoID
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function delete_customer_link(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this_customer'); ?>",
                /*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'groupCustomerDetailID': id
                    },
                    url: "<?php echo site_url('CustomerGroup/delete_customer_link'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                        stopLoad();
                    },
                    error: function() {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    }
</script>