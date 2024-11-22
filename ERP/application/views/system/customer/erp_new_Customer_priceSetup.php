<?php echo head_page($_POST['page_name'], false);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customer_arr = all_customer_drop();
$main_category_arr = all_main_category_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currency'];

?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">


    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 1 - <?php echo  $this->lang->line('sales_maraketing_masters_sales_prices');?><!--Step 1 - Sales Prices--></a>
        <a class="btn btn-default btn-wizard" href="#step2" onclick="load_confirmation();" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 2 - <?php echo  $this->lang->line('sales_maraketing_masters_sales_price_confirmation');?><!--Step 2 - Sales Price Confirmation--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">

            <?php echo form_open('', 'role="form" id="customerPriceSetup_form"'); ?>
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
                                <input type="text" name="documentDate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="documentDate" class="form-control">
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
                <input type="text" name="currency" id="currency" class="form-control"
                          value="<?php echo $defaultCurrencyID; ?>" readonly>
                <span class="input-req-inner"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_customer') ?><!--Customer--></label>
                        </div>
                        <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                 <?php echo form_dropdown('customerAutoID', $customer_arr, '', 'class="form-control select2" id="customerAutoID" required'); ?>
                     <span class="input-req-inner"></span>
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
            <br>
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('sales_maraketing_masters_customer_price_details') ?><!--CUSTOMER PRICE DETAILS--></h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="customerprice_modal()">
                                <i class="fa fa-plus"></i><?php echo $this->lang->line('sales_maraketing_masters_add_sales_price') ?><!--Add Sales Price-->
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="customerSalesList_table" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 3%">#</th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('transaction_common_item_code') ?><!--Item Code--></th>
                                        <th style="min-width: 30%"><?php echo $this->lang->line('transaction_common_item_description') ?><!--Item Description--></th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('common_date_from') ?><!--Date From--></th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('common_date_to') ?><!--Date To--></th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_default_price') ?><!--Default Price--> (<?php echo $defaultCurrencyID ?>)</th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_sales_price') ?><!--Sales Price--> (<?php echo $defaultCurrencyID ?>)</th>
                                        <th style="width: 5%"><?php echo $this->lang->line('common_action') ?><!--Action--> <a onclick="delete_Customer_itemprice_all()"><span title="Delete All" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></th>
                                   <!--     <th style="min-width: 10%">Allow Modification</th> -->
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div id="step2" class="tab-pane">
            <div id="confirm_body"></div>
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous') ?><!--Previous--></button>
                <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft') ?><!--Save as Draft--></button>
                <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm') ?><!--Confirm--></button>
            </div>
        </div>
    </div>

    <!-- model fade Adding Customer wise item price -->
    <div class="modal fade" id="SalesPrice_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
        <div class="modal-dialog" role="document" style="width: 80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="SalesPriceTitle"><?php echo $this->lang->line('sales_maraketing_masters_add_new_sales_price') ?><!--Add New Sales Price--></h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('', 'role="form" id="AddCustomerSalesPrice_form"'); ?>
                    <input class="hidden" name="load_customerAutoID" id="load_customerAutoID">
                    <div class="row">
                        <div class="col-md-12">
                            <div class=" col-sm-3">
                                <label for=""><?php echo $this->lang->line('transaction_main_category') ?><!--Main Category-->:</label>
                                <?php echo form_dropdown('mainCategoryID', $main_category_arr, '', 'class="form-control select2" id="mainCategoryID"  onchange="load_sub_cat()"'); ?>
                            </div>

                            <div class=" col-sm-3">
                                <label for=""><?php echo $this->lang->line('transaction_sub_category') ?><!--Sub Category-->:</label>
                                <select name="subcategoryID" id="subcategoryID" class="form-control searchbox select2" onchange="load_sub_sub_cat()">
                                    <option value="">Select Category</option>
                                </select>
                            </span>
                            </div>
                            <div class="form-group col-sm-3">
                                <label class=""><?php echo $this->lang->line('erp_item_master_sub_sub_category') ?><!--Sub sub Category--> </label>
                                <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox select2">
                                    <option value="">Select Category</option>
                                </select>
                                </span>
                            </div>
                            <div class="form-group col-sm-2 pull-right">
                                <label for=""></label>
                                <button style="margin-top: 25px" type="button" onclick="fetch_Items()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_generate') ?><!--Generate--></button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div id="ItemPriceTable"></div>
                        </div>
                    </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="Customer_salesPrice_save()"><?php echo $this->lang->line('common_save') ?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_close') ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        var cpsAutoID = '';
        var customerAutoID = '';

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/customer/customer_price_setup', 'Customer Price Setup','CUS');
            });
            $('.select2').select2();

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
            });

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                cpsAutoID = p_id;
                load_customerSalesPrice_header();
                load_confirmation();
                $('.btn-wizard').removeClass('disabled');
            } else {
                $('.addTableView').addClass('hide');
                $('.btn-wizard').addClass('disabled');
                $('.addTableView').addClass('hide');
            }

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

            $('#customerPriceSetup_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                    //customerAutoID: {validators: {notEmpty: {message: 'Customer is required.'}}},
                    narration: {validators: {notEmpty: {message: 'Narration is required.'}}},
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $('#customerAutoID').attr('disabled',false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Customer/save_customerPriceSetup_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        customerSalesList_table();
                        if (data[0] == 's') {
                            $('#cpsAutoID').val(data[2]);
                            cpsAutoID = data[2];
                            $('.addTableView').removeClass('hide');
                            $('.btn-wizard').removeClass('disabled');
                        } else {
                            $('.btn-primary').prop('disabled', false);
                            $('.btn-wizard').removeClass('disabled');
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });

        });

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
                            cpsAutoID = data['cpsAutoID'];
                            $('#cpsAutoID').val(cpsAutoID);
                            $('#documentDate').val(data['documentDate']);
                            $('#narration').val(data['narration']);
                            setTimeout(function () {
                                $('#customerAutoID').val(data['customerAutoID']).change();
                            }, 500);
                            customerSalesList_table();

                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }

        function customerSalesList_table(){
            Otable = $('#customerSalesList_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Customer/fetch_customer_SalesItemList'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                        $('#customerAutoID').attr('disabled',true);
                    }
                },

                "aoColumns": [
                    {"mData": "cpsAutoID"},
                    {"mData": "itemSystemCode"},
                    {"mData": "itemDescription"},
                    {"mData": "applicableDateFrom"},
                    {"mData": "applicableDateTo"},
                    {"mData": "SellingPrice"},
                    {"mData": "salesPrice"},
                    {"mData": "action"}
                  //  {"mData": "isModificationAllowed"},
                ],
                "columnDefs": [],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "cpsAutoID", "value": $("#cpsAutoID").val()});
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

        function customerprice_modal(){
            if (cpsAutoID) {
                var customerAutoID = $('#customerAutoID').val();

                $('#load_customerAutoID').val(customerAutoID);
                $('#mainCategoryID').val('').change();
                $('#subSubCategoryID').val('').change();
                $('#subcategoryID').val('').change();
                $('#ItemPriceTable').addClass('hidden');
                $("#SalesPrice_model").modal({backdrop: "static"});
            }
        }


        function load_sub_cat(select_val) {
            $('#subcategoryID').val("");
            $('#subcategoryID option').remove();
            $('#subSubCategoryID').val("");
            $('#subSubCategoryID option').remove();
            var subid = $('#mainCategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
                dataType: 'json',
                data: {'subid': subid},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subcategoryID').empty();
                        var mySelect = $('#subcategoryID');
                        mySelect.append($('<option></option>').val('').html('Select Option'));
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                        });
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function load_sub_sub_cat() {
            $('#subSubCategoryID option').remove();
            $('#subSubCategoryID').val("");
            var subsubid = $('#subcategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
                dataType: 'json',
                data: {'subsubid': subsubid},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subSubCategoryID').empty();
                        var mySelect = $('#subSubCategoryID');
                        mySelect.append($('<option></option>').val('').html('Select Option'));
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                        });
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function fetch_Items() {
            var customerAutoID = $('#customerAutoID').val();
            var mainCategoryID = $('#mainCategoryID').val();
            var subcategoryID = $('#subcategoryID').val();
            var subSubCategoryID = $('#subSubCategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Customer/Fetch_ItemDetail"); ?>',
                dataType: 'html',
                data: {'customerAutoID': customerAutoID, 'mainCategoryID': mainCategoryID,'subcategoryID': subcategoryID, 'subSubCategoryID': subSubCategoryID, 'cpsAutoID' : cpsAutoID},
                async: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#ItemPriceTable').html(data);
                    $('#ItemPriceTable').removeClass('hidden');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ItemPriceTable').html(xhr.responseText);

                }
            });
        }

        function Customer_salesPrice_save() {
            var data = $('#AddCustomerSalesPrice_form').serializeArray();
            var customerAutoID = $('#load_customerAutoID').val();
        //    var cpsAutoID = $('#cpsAutoID').val();

            data.push({'name': 'customerAutoID', 'value': customerAutoID});
            data.push({'name': 'cpsAutoID', 'value': cpsAutoID});

            if (cpsAutoID) {
                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Customer/Save_Customer_ItemPrice'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_Items();
                                customerSalesList_table();
                            }
                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                    });
            } else {
                swal({
                    title: "Good job!",
                    text: "You clicked the button!",
                    type: "success"
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
                    url: "<?php echo site_url('Customer/load_Customer_PriceConfirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#confirm_body').html(data);
                        refreshNotifications(true);
                    }, error: function () {
                        stopLoad();
                        alert('An Error Occurred! Please Try Again.');
                        refreshNotifications(true);
                    }
                });
            }
        }


        function save_draft() {
            if (cpsAutoID) {
                swal({
                        title: "Are you sure?",
                        text: "You want to save this document!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Save as Draft"
                    },
                    function () {
                        fetchPage('system/customer/customer_price_setup', 'Customer Price Setup','CUS');
                    });
            }
        }

        function confirmation() {
            if (cpsAutoID) {
                swal({
                        title: "Are you sure?",
                        text: "You want confirm this document!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Confirm"
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
                                    fetchPage('system/customer/customer_price_setup', 'Customer Price Setup','CUS');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                                stopLoad();
                            }
                        });
                    });
            }
        }

        function delete_Customer_itemprice(customerPriceID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'customerPriceID': customerPriceID},
                        url: "<?php echo site_url('Customer/delete_Customer_itemprice'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            customerSalesList_table();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function delete_Customer_itemprice_all() {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'cpsAutoID': cpsAutoID},
                        url: "<?php echo site_url('Customer/delete_Customer_itemprice_all'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            customerSalesList_table();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    </script>
