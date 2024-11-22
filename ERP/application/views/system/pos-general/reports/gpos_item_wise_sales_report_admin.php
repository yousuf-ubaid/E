<?php
$locations = get_gpos_location_with_status();

$type = 1;
$main_category_arr = all_main_category_report_drop_pos();
$main_category_group_arr = all_main_category_group_report_drop_pos();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<div class="box">
    <div class="box-header with-border" id="box-header-with-border">
        <h3 class="box-title" id="box-header-title"><i class="fa fa-bar-chart"></i> Item Wise Sales Report</h3>
        <div class="box-tools pull-right">
            <button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button id="" class="btn btn-box-tool headerclose navdisabl"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">

        <form id="frm_itemizedSalesReport" method="post" class="form-inline" role="form">
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
            <input type="hidden" id="iws_outletID" name="outletID" value="0"/>

            <div class="row">
                <div class="col-md-12">
                    <div class="col-sm-4">
                        <label> Main Category </label>
                        <!--Main Category-->
                        <?php if ($type == 1) {
                            echo form_dropdown('mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                        } else {
                            echo form_dropdown('mainCategoryID[]', $main_category_group_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                        }
                        ?>
                    </div>
                    <div class="col-sm-4">
                        <label>Sub Category </label>
                        <!--Sub Category-->
                        <select name="subcategoryID[]" id="subcategoryID" class="form-control searchbox"
                                onchange="loadSubSub()" multiple="multiple">
                            <!--Select Category-->
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Sub Sub Category </label>
                        <!--Sub Category-->
                        <select name="subsubcategoryID[]" id="subsubcategoryID"
                                class="form-control searchbox" multiple="multiple">
                            <!--Select Category-->
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-sm-3">
                    <label>From</label>
                    <div>
                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                               style="width: 200px;">
                    </div>
                </div>

                <div class="col-sm-3">
                    <label>to</label>
                    <div>
                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               value="<?php echo date('d/m/Y') ?>"
                               style="width: 200px;" placeholder="To" name="filterTo" id="filterTo2">
                    </div>
                </div>

                <div class="col-sm-3">
                    <label>Outlet</label>
                    <div>
                        <select class=" filters" multiple required name="outletID_f[]" id="outletID_f"
                                onchange="loadCashier()"> 
                            <?php
                            foreach ($locations as $loc) {
                                //echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '</option>';
                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . ' - ' . $loc['isActive'] . '</option>';
                                
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <label><?php echo $this->lang->line('posr_cashier'); ?></label>
                    <div>
                        <div id="cashier_option">
                            <?php echo form_dropdown('cashier[]', get_cashiers_gpos(), '', 'multiple required id="cashier3"  class="form-control input-sm"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">

                <div class="col-sm-4">
                    <label for=""><?php echo $this->lang->line('common_item'); ?><!--Item--> </label>
                    <br>
                    <?php echo form_dropdown('items[]', fetch_item_dropdown(false, true), '', 'class="form-control items" id="items" multiple="" style="z-index: 0;"'); ?>
                </div>

                <div class="col-sm-4 hidden" id="documentcode">
                    <label for="">Invoice Code</label>
                    <br>
                    <?php echo form_dropdown('InvoiceCode[]', fetch_invoice_dropdown_new(false, true), '', 'class="form-control select2 invoicecd" id="invoicecd"  multiple="" style="z-index: 0;"'); ?>
                </div>

                <div class="col-sm-4 ">
                    <label for="" style="color: white">button</label>
                    <br>
                    <button type="button" onclick="loadPaymentItemized_salesReport()" class="btn btn-primary btn-sm">
                        Generate Report
                    </button>
                </div>


            </div>


        </form>
        <hr>


        <div class="box-body" style="display: block;width: 100%">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_public_1" data-toggle="tab" aria-expanded="false"
                                          onclick="showInvoicefilter(1)">Summary</a></li>
                    <li class=""><a href="#tab_public_2" data-toggle="tab" aria-expanded="true"
                                    onclick="showInvoicefilter(2)">Detail</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_public_1">
                        <div id="pos_modalBody_posItemized_sales_report">
                            <div class="text-center"
                                 style="color:#9c9c9c; font-size:20px; margin-top:75px; min-height: 200px;    border: 2px solid gray !important; padding: 10px !important; ">
                                Click on the Generate
                                Report
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane " id="tab_public_2">
                        <div id="pos_modalBody_posItemized_sales_report_detail">
                            <div class="text-center"
                                 style="color:#9c9c9c; font-size:20px; margin-top:75px; min-height: 200px;    border: 2px solid gray !important; padding: 10px !important; ">
                                Click on the Generate
                                Report
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {

        $('.multiselect2-container').css({"height":"150px"});
        $("#cashier3").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });


        $("#outletID_f").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#outletID_f").multiselect2('selectAll', false);
        $("#outletID_f").multiselect2('updateButtonText');

        $("#cashier3").multiselect2('selectAll', true);
        $("#cashier3").multiselect2('updateButtonText');


        /*$("#filterFrom2,#filterTo2").datepicker({
         format: 'dd/mm/yyyy'
         });*/

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
        }).on('dp.change', function (ev) {
            // $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
            //$(this).datetimepicker('hide');
        });

        $("#frm_itemizedSalesReport").submit(function (e) {
            loadPaymentItemized_salesReport_ajax();
            return false;
        })
        $('.select2').select2();

        $("#items").select2({
            tags: true,
            containerCssClass : "items-input"
        });

        $("#invoicecd").select2({
            tags: true,
            containerCssClass : "invoicecd-input"
        });

      //  load_items_dropdown();

        loadCashier();
    });


    $('.items-input').on('keyup', '.select2-search__field', function (e) {
        load_items_dropdown(e.target.value);
    });

    $('.invoicecd-input').on('keyup', '.select2-search__field', function (e) {
        load_invoicecode_dropdown(e.target.value);
    });

    var currentRequest = null;
    function load_items_dropdown(skey) {
        let selected = $("#items").val();
        currentRequest = $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Pos/load_items_dropdown'); ?>",
            data: {skey:skey,selected:selected},
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                var Name = "";
                var ID = "";
                data.items.forEach(function (item, index) {
                     Name = item.seconeryItemCode + ' | ' + item.itemName;
                     ID = item.itemAutoID;
                     $("#items").append("<option value='"+ID+"'>"+Name+"</option>");
                        [].slice.call(items.options)
                        .map(function(a){
                            if(this[a.innerText]){
                                items.removeChild(a);
                            } else {
                                this[a.innerText]=1;
                            }
                            },{});
                });
            }
        });
    }

    var invoicecode_dropdown_request = null;
    function load_invoicecode_dropdown(skey) {
        let selected = $("#invoicecd").val();
        invoicecode_dropdown_request = $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Pos/load_invoicecode_dropdown'); ?>",
            data: {skey:skey,selected:selected},
            beforeSend: function () {
                if (invoicecode_dropdown_request != null) {
                    invoicecode_dropdown_request.abort();
                }
            },
            success: function (data) {
                var Name = "";
                var ID = "";
                var options = [];
                data.invoicecd.forEach(function (invoicecodes, index) {
                    Name = invoicecodes.invoiceCode;
                    ID = invoicecodes.invoiceID;
                    var item = {id:ID, text:Name};
                    options.push(item);
                    $("#invoicecd").append("<option value='"+ID+"'>"+Name+"</option>");
                    [].slice.call(invoicecd.options)
                        .map(function(a){
                            if(this[a.innerText]){
                                invoicecd.removeChild(a);
                            } else {
                                this[a.innerText]=1;
                            }
                        },{});
                });

            }
        });
    }

    function loadPaymentItemized_salesReport() {
        load_summary();
        load_detail();
    }

    function loadPaymentItemized_salesReport_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_general_report/load_item_wise_sales_report_admin'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i> <?php echo $this->lang->line('posr_loading_print_view');?> </div>');

            },
            success: function (data) {
                $("#pos_modalBody_posItemized_sales_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $(document).ready(function (e) {
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });
    });

    function loadCashier() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_general_report/get_gpos_outlet_cashier'); ?>",
            data: {warehouseAutoID: $('#outletID_f').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
                    $('#cashier_option').html(data);
                    $("#cashier2").multiselect2({
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    $("#cashier2").multiselect2('selectAll', false);
                    $("#cashier2").multiselect2('updateButtonText');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>
<script>
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

    function loadSub() {
        $("#search_to").empty();
        loadSubCategory();
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
            data: {'mainCategoryID': mainCategoryID, type:<?php echo $type; ?>},
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
                /* $("#subcategoryID").multiselect2('selectAll', false);
                 $("#subcategoryID").multiselect2('updateButtonText');*/
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
            data: {'subCategoryID': subCategoryID, type:<?php echo $type; ?>},
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
                /*$("#subsubcategoryID").multiselect2('selectAll', false);
                 $("#subsubcategoryID").multiselect2('updateButtonText');*/
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
                type:<?php echo $type; ?>
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

    function load_summary() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/loadGeneralItemizedSalesReport'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                startLoadPos();
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  Loading Print view</div>');
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posItemized_sales_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_detail() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/loadGeneralItemizedSalesReportDetail'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                startLoadPos();
                $("#pos_modalBody_posItemized_sales_report_detail").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  Loading Print view</div>');
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posItemized_sales_report_detail").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function showInvoicefilter(val) {
        if (val == 2) {
            $('#documentcode').removeClass('hidden');
        } else {
            $('#documentcode').addClass('hidden');
        }
    }
</script>