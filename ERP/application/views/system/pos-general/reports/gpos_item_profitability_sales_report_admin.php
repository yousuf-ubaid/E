<?php echo head_page('<i class="fa fa-bar-chart"></i>  Item Wise Profitability Report', false);
$locations = get_gpos_location_with_status();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$main_category_arr = all_main_category_report_drop();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/gpos-reports.css'); ?>"/>

<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


?>
<div class="row">
    <form id="frm_itemizedSalesReport" method="post" class="form-inline" role="form">
        <input type="hidden" id="iws_outletID" name="outletID" value="0"/>
        <div class="form-group">
            <label class="col-md-4 control-label">Outlet</label>
            <div class="col-md-12">
                <select class=" filters" multiple required name="outletID_f[]" id="outletID_f" onchange="loadCashier()">
                    <?php
                    foreach ($locations as $loc) {
                        echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . ' - ' . $loc['isActive'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-5 control-label"><?php echo $this->lang->line('posr_cashier'); ?></label>
            <div class="col-md-12">
                <div id="cashier_option">
                    <?php echo form_dropdown('cashier[]', get_cashiers_gpos(), '', 'multiple required id="cashier3"  class="form-control input-sm"'); ?>
                </div>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-4 control-label"><?php echo $this->lang->line('common_from'); ?></label>
            <div class="col-md-12">
                <input type="text" required class="form-control input-sm startdateDatepic" name="filterFrom"
                    id="filterFrom2"
                    value="<?php echo date('d-m-Y 00:00:00') ?>">

                <?php echo $this->lang->line('common_to'); ?>&nbsp;&nbsp;<!--to-->

                <input type="text" required class="form-control input-sm startdateDatepic"
                    value="<?php echo date('d-m-Y 23:59:59') ?>" placeholder="To" name="filterTo" id="filterTo2">
            </div>
        </div>

        <div class="form-group col-sm-3">
            <label>Main Category </label><br>
            <?php echo form_dropdown('mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" multiple id="mainCategoryID" onchange="loadSub()"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label>Sub Category </label><br>
            <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                    onchange="loadSubSub()" multiple="multiple">
                <!--Select Category-->
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label>Sub Sub Category </label><br>
            <select name="subsubcategoryID" id="subsubcategoryID"
                    class="form-control searchbox" multiple="multiple">
                <!--Select Category-->
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label>Items </label><br>
            <select name="items[]" id="items" class="form-control items" multiple="multiple">
                <!--Select Category-->
            </select>
        </div>
       <!--  <div class="form-group col-sm-3">
            <label for=""><?php echo $this->lang->line('common_item'); ?> </label>
            <br>
            <?php // echo form_dropdown('items[]', fetch_item_dropdown(false,true), '', 'class="form-control select2 items" id="items"  multiple="" style="z-index: 0;"'); ?>
        </div> -->
        <div class="form-group">
            <label class="col-md-4 control-label">&nbsp;</label>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report-->
                </button>
            </div>
        </div>
    </form>
</div>
<hr>
<div id="pos_modalBody_posItemized_sales_report" class="reportContainer">
    <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
        Report
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $(document).ready(function (e) {
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


        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {}
        }).on('dp.change', function (ev) {

        });

        $("#frm_itemizedSalesReport").submit(function (e) {
            loadPaymentItemized_salesReport_ajax();
            return false;
        });

        $('.select2').select2();
       

        loadCashier();

        $("#items").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#items").multiselect2('selectAll', false);
        $("#items").multiselect2('updateButtonText');

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
        $("#subcategoryID").multiselect2('selectAll', false);
        $("#subcategoryID").multiselect2('updateButtonText');

        $("#subsubcategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#subcategoryID").change(function () {
            loadSubSub();
        });

        $("#subsubcategoryID").change(function () {
            loadItems();
        });
        loadItems();
    });




    $('.items-input').on('keyup', '.select2-search__field', function (e) {
        load_items_dropdown(e.target.value);
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

    function loadSub() {
        $("#items").empty();
        loadSubCategory();
        loadItems();
    }

    function loadSubSub() {
        $("#items").empty();
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
            data: {'mainCategoryID': mainCategoryID,type: 1},
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
                $("#subcategoryID").multiselect2('selectAll', false);
                $("#subcategoryID").multiselect2('updateButtonText');
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
                $("#subsubcategoryID").multiselect2('selectAll', false);
                $("#subsubcategoryID").multiselect2('updateButtonText');
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
                    $('#items').empty();
                    var mySelect = $('#items');
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            var itemSecondaryCodePolicy=<?php echo is_show_secondary_code_enabled(); ?>;
                            if(itemSecondaryCodePolicy){
                                var itemCode=text['seconeryItemCode'];
                            }else{
                                var itemCode=text['itemSystemCode'];
                            }
                            mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemSystemCode']+ ' | '+text['seconeryItemCode']+ ' | ' + text['itemDescription']));
                        });
                    }
                }
                $('#items').multiselect2('rebuild');
                $("#items").multiselect2('selectAll', false);
                $("#items").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
</script>