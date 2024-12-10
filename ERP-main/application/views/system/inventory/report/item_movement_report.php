<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = 'Item Movement Report';
echo head_page($title, false);
$date_format_policy = date_format_policy();

$main_category_arr = all_main_category_report_drop();
$cdate = current_date(FALSE);
$startdate = date('Y-m-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$current_date = current_format_date();
//$location_arr = all_delivery_location_drop(false);
$location_arr = all_delivery_location_drop_with_status(false);

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <div class="box-tools pull-right">
            <button id="" onclick="openColumnSelection()" class="btn btn-box-tool " ><i class="fa fa-plus"></i></button>
        </div>
        <?php echo form_open('', ' name="itm_movement_report_filter_frm" id="itm_movement_report_filter_frm" class="form-group" role="form"'); ?>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_from'); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="datefrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_to'); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-5 " id=""></div>

            <div class="form-group col-sm-3 pull-right hide" id="columSelectionDiv">
                <label for="">Extra Columns</label>
                <?php echo form_dropdown('columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="get_item_movement_report()" id="columSelectionDrop" multiple="multiple"'); ?>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label>Warehouse </label><br>
                <?php echo form_dropdown('warehouseID[]', $location_arr, '', 'class="form-control" id="warehouseID" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label>Main Category </label><br>
                <?php echo form_dropdown('mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" multiple id="mainCategoryID" onchange="loadSub()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label>Sub Category </label><br>
                <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                        onchange="loadSubSub()" multiple="multiple">
                    <!--Select Category-->
                </select>
            </div>
            <div class="form-group col-sm-2">
                <label>Sub Sub Category </label><br>
                <select name="subsubcategoryID" id="subsubcategoryID"
                        class="form-control searchbox" multiple="multiple">
                    <!--Select Category-->
                </select>
            </div>
            <div class="col-sm-2">
                <label for="status_filter_item"><?php echo $this->lang->line('common_item_status');?></label>
                <?php echo form_dropdown('status_filter_item', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item" '); ?>
            </div>
            <div class="form-group col-sm-2">
                <label>Items </label><br>
                <select name="items[]" id="items"
                        class="form-control items" multiple="multiple" onchange="get_item_movement_report()">
                    <!--Select Category-->
                </select>
            </div>

            <div class="form-group col-sm-2">
                <label for=""></label>
                <button style="margin-top:28px " type="button" onclick="get_item_movement_report()" class="btn btn-primary btn-xs">
                    Generate
                </button>
            </div>
        </div>

</div>

<?php echo form_close(); ?>
</fieldset>
</div>


<div id="Load_item_movement_report" style=""></div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/inventory/report/item_movement_report', '', 'Item Movement Report');
        });
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('#columSelectionDiv').addClass('hide');
        $('#columSelection').val();

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

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

        $("#subsubcategoryID").change(function () {
            loadItems();
        });
        $("#status_filter_item").change(function () {
            loadItems();
        });
        $("#columSelectionDrop").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('.select2').select2();
        loadItems();

        $("#warehouseID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#warehouseID").multiselect2('selectAll', false);
        $("#warehouseID").multiselect2('updateButtonText');
        $("#columSelectionDrop").change(function () {
            if ((this.value)) {
                get_item_movement_report(this.value);
                return false;
            }
        });
    });

    function get_item_movement_report()
    {
        var data = $("#itm_movement_report_filter_frm").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('Inventory/load_item_movement_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Load_item_movement_report').html(data);
                applyAlternateColor();
                stopLoad();
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
                type: 1,
                activeStatus: $('#status_filter_item').val()

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

    function generateReportPdf() {
        var form = document.getElementById('itm_movement_report_filter_frm');
        form.target = '_blank';
        /*form.action = 'php echo site_url('template_paySheet/get_payScale_report_pdf'); ?>';*/
        form.action = '<?php echo site_url('Inventory/load_item_movement_report_pdf'); ?>';
        form.submit();
    }
    function openColumnSelection(){
        if ($('#columSelectionDiv').hasClass('hide')) {
            $('#columSelectionDiv').removeClass('hide');
        }else{
            $('#columSelectionDiv').addClass('hide');
        }
    }

    function applyAlternateColor() {
        const rows = document.querySelectorAll("#tbl_rpt_itemmovement tbody tr");
        let toggleClass = false;

        rows.forEach(function(row) {
            if (row.tagName.toLowerCase() === 'tr') {
                toggleClass = !toggleClass;
                row.style.backgroundColor = toggleClass ? "#efeffc" : "";
            } else {
                row.style.backgroundColor = "";
                toggleClass = false;
            }
        });
    }
</script>