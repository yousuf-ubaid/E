<?php echo head_page($_POST['page_name'], false);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
//$customer_arr = all_customer_drop(false);
$customer_arr = all_customer_drop(false,1);
$main_category_arr = all_main_category_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currency'];
$currncy_arr    = all_currency_new_drop(true);
$customerCategory    = party_category(1, false);
?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">


    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 1 - <?php echo  $this->lang->line('sales_maraketing_masters_sales_prices');?><!--Step 1 - Sales Prices--></a>
        <a class="btn btn-default btn-wizard" href="#step2" onclick="customerSalesList_table_new();" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 2 - <?php echo  $this->lang->line('sales_maraketing_masters_sales_prices_details');?><!--Step 2 - Sales Prices Details--></a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="load_confirmation();" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 3 - <?php echo  $this->lang->line('sales_maraketing_masters_sales_price_confirmation');?><!--Step 3 - Sales Price Confirmation--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="customerPriceSetup_form_new"'); ?>
            <input class="hidden" id="cpsAutoID" name="cpsAutoID">
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title" style="margin-top: 20px ">
                        <h2><?php echo $this->lang->line('common_document_header') ?><!--DOCUMENT HEADER--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_document_date') ?><!--Document Date--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="documentDate" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_currency') ?><!--Currency--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="currency" id="currency" class="form-control" value="<?php echo $defaultCurrencyID; ?>" readonly>
                                <span class="input-req-inner"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_customer_category') ?><!--Customer Category--></label>
                        </div>
                        <div class="form-group col-sm-4">
                 <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" multiple="multiple"'); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_customer') ?><!--Customer--></label>
                        </div>
                        <div class="form-group col-sm-4" id="div_load_customers">
                 <span class="input-req" title="Required Field">
                          <?php echo form_dropdown('customerAutoID[]', $customer_arr, '', 'class="form-control" id="customerAutoID" multiple="multiple"'); ?>
                     <span class="input-req-inner"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_applicable_date_from') ?><!--Applicable Date From--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="applicableDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="applicableDateFrom" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_applicable_date_to') ?><!--Applicable Date To--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="applicableDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="applicableDateTo" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_narration') ?><!--Narration--></label>
                        </div>
                        <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                <textarea type="text" name="narration" id="narration" class="form-control"
                          placeholder="Comments"></textarea>
                <span class="input-req-inner"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('common_item') ?><!--Items--></label>
                        </div>
                    </div>

                        <div class="col-md-12">
                            <div class="col-sm-2">
                                <label class="title" style="text-align: left"><?php echo $this->lang->line('transaction_main_category') ?><!--Main Category-->  </label>
                                <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"'); ?>
                            </div>
                            <div class="col-sm-1"> &nbsp; </div>
                            <div class="col-sm-2">
                                <label class="title" style="text-align: left"><?php echo $this->lang->line('transaction_sub_category') ?><!--Sub Category--> </label>
                                <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                        onchange="loadSubSub()" multiple="multiple">
                                </select>
                            </div>
                            <div class="col-sm-1"> &nbsp; </div>
                            <div class="col-sm-2">
                                <label class="title" style="text-align: left"><?php echo $this->lang->line('erp_item_master_sub_sub_category') ?><!--Sub Sub Category--> </label>
                                <select name="subsubcategoryID" id="subsubcategoryID"
                                        class="form-control searchbox" multiple="multiple">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                                <div class="col-sm-5">
                                    <select name="itemFrom[]" id="search" class="form-control" size="8" multiple="multiple">
                                        <?php
                                        $items = "";
                                        $items = fetch_item_data_by_company();
                                        if (!empty($items)) {
                                            foreach ($items as $val) {
                                                echo '<option value="' . $val["itemAutoID"] . '">' . $val["itemSystemCode"] . ' | ' . $val["itemDescription"] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-sm-2">
                                    <!--<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>-->
                                    <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                                    ><i class="fa fa-forward"></i></button>
                                    <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                            class="fa fa-chevron-right"></i></button>
                                    <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                            class="fa fa-chevron-left"></i></button>
                                    <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                            class="fa fa-backward"></i></button>
                                    <!--<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>-->
                                </div>

                                <div class="col-sm-5">
                                    <select name="itemTo[]" id="search_to" class="form-control" size="8"
                                            multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>

                    <div class="row">
                        <div class="text-right m-t-xs">
                            <div class="form-group col-sm-12" style="margin-top: 10px;">
                                <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save') ?><!--Save--></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

        </div>

        <div id="step2" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('sales_maraketing_masters_customer_price_details') ?><!--CUSTOMER PRICE DETAILS--></h2>
                    </header>

                    <div class="row" style="margin: 5px;">
                        <div class="" id="customer_price_details"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="step3" class="tab-pane">
            <div class="row" style="margin: 5px;">
                <div id="confirm_body"></div>
            </div>
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous') ?><!--Previous--></button>
                <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft') ?><!--Save as Draft--></button>
                <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm') ?><!--Confirm--></button>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        var cpsAutoID = '';
        var customerAutoID = '';

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/customer/customer_price_setup_new', 'Customer Price Setup','CUS');
            });
            $('.select2').select2();
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                cpsAutoID = p_id;
                loadCustomer(cpsAutoID);
                load_customerSalesPrice_header();
                load_confirmation();
                $('.btn-wizard').removeClass('disabled');
            } else {
                $('.addTableView').addClass('hide');
                $('.btn-wizard').addClass('disabled');
                $('.addTableView').addClass('hide');
            }
            $('#customerAutoID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                buttonWidth: 345,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#customerAutoID").multiselect2('selectAll', false);
            $("#customerAutoID").multiselect2('updateButtonText');

            $('#category').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                buttonWidth: 345,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#category").multiselect2('selectAll', false);
            $("#category").multiselect2('updateButtonText');
            $("#category").change(function () {
                loadCustomer(cpsAutoID);
            });


            $("#mainCategoryID").multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $("#subcategoryID").multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $("#subsubcategoryID").multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $("#subsubcategoryID").change(function () {
                loadItems();
            });

            $('#search').multiselect({
                search: {
                    left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
                    right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
                },
                afterMoveToLeft: function ($left, $right, $options) {
                    $("#search_to option").prop("selected", "selected");
                }
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

            
            

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
            });

            $('.next').click(function () {
                var nextId = $(this).parents('.tab-pane').next().attr("id");
                $('[href=#' + nextId + ']').tab('show');
            });

            $('.prev').click(function () {
                var prevId = $(this).parents('.tab-pane').prev().attr("id");
                $('[href=#' + prevId + ']').tab('show');
            });

            $('#customerPriceSetup_form_new').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                    narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_narration_is_required');?>.'}}},/*Narration is required*/
                    currency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $('#customerAutoID').attr('disabled',false);
                $('#category').attr('disabled',false);
                $('#documentDate').attr('disabled',false);
                $("#mainCategoryID").multiselect2("disable");
                $("#subcategoryID").multiselect2("disable");
                $("#subsubcategoryID").multiselect2("disable");
                $("#search_to").prop('disabled', false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Customer/save_customerPriceSetup_header_new'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        // customerSalesList_table();
                        if (data[0] == 's') {
                            $('#cpsAutoID').val(data[2]);
                            cpsAutoID = data[2];
                            customerSalesList_table_new();
                            $('.btn-wizard').removeClass('disabled');
                            $('#category').attr('disabled',true);
                            $('#customerAutoID').attr('disabled',true);
                            $('#documentDate').attr('disabled',true);
                            $("#mainCategoryID").multiselect2("disable");
                            $("#subcategoryID").multiselect2("disable");
                            $("#subsubcategoryID").multiselect2("disable");
                            $("#search_to").prop('disabled', true);

                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                        } else {
                            $('.btn-primary').prop('disabled', false);
                            $('.btn-wizard').removeClass('disabled');
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

        function loadSub() {
            $("#search_to").empty();
            loadSubCategory();
            loadSubSubCategory();
            loadItems();
        }
        function loadSubSub() {
            $("#search_to").empty();
            loadSubSubCategory();
            loadItems();
        }

        function loadSubCategory() {
            $('#subcategoryID option').remove();
            var mainCategoryID = $('#mainCategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Report/load_subcat"); ?>',
                dataType: 'json',
                data: {'mainCategoryID': mainCategoryID, type: 1},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subcategoryID').empty();
                        var mySelect = $('#subcategoryID');
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                        });
                    }
                    $('#subcategoryID').multiselect2('rebuild');
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function loadSubSubCategory() {
            $('#subsubcategoryID option').remove();
            var subCategoryID = $('#subcategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Report/load_subsubcat"); ?>',
                dataType: 'json',
                data: {'subCategoryID': subCategoryID, type: 1},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subsubcategoryID').empty();
                        var mySelect = $('#subsubcategoryID');
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                        });
                    }
                    $('#subsubcategoryID').multiselect2('rebuild');
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function loadItems() {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Report/loadItems"); ?>',
                dataType: 'json',
                data: {
                    subSubCategoryID: $('#subsubcategoryID').val(),
                    mainCategoryID: $('#mainCategoryID').val(),
                    subCategoryID: $('#subcategoryID').val(),
                    type: 1
                },
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#search').empty();
                        var mySelect = $('#search');
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemSystemCode'] + ' | ' + text['itemDescription']));
                        });
                    } else {
                        $('#search').empty();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }
        function loadCustomer(cpsAutoID) {
            var page = '';
            
            if(cpsAutoID)
            {
                page = cpsAutoID;
            }
            var categoryID = $('#category').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {categoryID: categoryID,DocID:page},
                url: "<?php echo site_url('Customer/load_customers'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_customers').empty();
                    $('#div_load_customers').html(data);
                    $('#customerAutoID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        buttonWidth: 345,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#customerAutoID").multiselect2('selectAll', false);
                    $("#customerAutoID").multiselect2('updateButtonText');
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function load_customerSalesPrice_header() {
            if (cpsAutoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'cpsAutoID': cpsAutoID},
                    url: "<?php echo site_url('Customer/load_customerSalesPrice_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            cpsAutoID = data['header']['cpsAutoID'];
                            $('#cpsAutoID').val(cpsAutoID);
                            $('#documentDate').val(data['header']['documentDate']);
                            $('#narration').val(data['header']['narration']);
                            $('#applicableDateFrom').val(data['applicableDateFrom']);
                            $('#applicableDateTo').val(data['applicableDateTo']);

                            setTimeout(function () {
                                $('#customerAutoID').multiselect2("deselectAll", false).multiselect2("refresh");
                                $('#customerAutoID').multiselect2('select',data['customer']);
                                $("#customerAutoID").multiselect2("disable");
                                $("#category").multiselect2("disable");
                            }, 500);

                            setTimeout(function () {
                                $('#search_to').empty();
                                var mySelect = $('#search_to');
                                $.each(data['item'], function (val, text) {
                                    mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemSystemCode'] + ' | ' + text['itemDescription']));
                                    $("#search option[value=\""+ text['itemAutoID'] +"\"]").remove();
                                    $('#search').multiselect('refresh');
                                });
                            }, 500);

                            $("#documentDate").prop('disabled', true);
                            $("#mainCategoryID").multiselect2("disable");
                            $("#subcategoryID").multiselect2("disable");
                            $("#subsubcategoryID").multiselect2("disable");
                            $("#search_rightAll").prop('disabled', true);
                            $("#search_rightSelected").prop('disabled', true);
                            $("#search_leftSelected").prop('disabled', true);
                            $("#search_leftAll").prop('disabled', true);
                            $("#search_to").prop('disabled', true);
                            $("#search").prop('disabled', true);
                            customerSalesList_table_new();

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

        function customerSalesList_table_new(){
            if (cpsAutoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'cpsAutoID': cpsAutoID},
                    url: "<?php echo site_url('Customer/load_CustomerPrice_detailsView'); ?>",
                    beforeSend: function () {
                        $("#customer_price_details").html("<div class='text-center'><i class='fa fa-refresh fa-spin fa-2'></i> Loading</div>");
                        // startLoad();
                    },
                    success: function (data) {
                        // stopLoad();
                        $("#customer_price_details").html("");
                        $('.addTableView').removeClass('hide');
                        $('#customer_price_details').html(data);
                        refreshNotifications(true);
                    }, error: function () {
                        // stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        refreshNotifications(true);
                    }
                });
            }
        }

        function load_confirmation() {
            if (cpsAutoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'cpsAutoID': cpsAutoID, 'html': true},
                    url: "<?php echo site_url('Customer/load_Customer_PriceConfirmation_new'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#confirm_body').html(data);
                        refreshNotifications(true);
                    }, error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        refreshNotifications(true);
                    }
                });
            }
        }

        function save_draft() {
            if (cpsAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document!*/
                        type: "warning",/*warning*/
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>"/*Save as Draft*/
                    },
                    function () {
                        fetchPage('system/customer/customer_price_setup_new', 'Customer Price Setup','CUS');
                    });
            }
        }

        function confirmation() {
            if (cpsAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
                        type: "warning",/*warning*/
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>"/*Confirm*/
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'cpsAutoID': cpsAutoID},
                            url: "<?php echo site_url('Customer/customer_SalesPrice_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                myAlert(data[0], data[1]);
                                stopLoad();
                                if (data[0] == 's') {
                                    fetchPage('system/customer/customer_price_setup_new', 'Customer Price Setup','CUS');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                                stopLoad();
                            }
                        });
                    });
            }
        }
    </script>
