<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = 'Item Movement Analysis';
echo head_page($title, false);
$date_format_policy = date_format_policy();

$main_category_arr = all_main_category_report_drop();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$current_date = current_format_date();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<style>
    .sub-headers {
        font-size: 12px;
        font-weight: bold;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
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
        </div>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label>Currency </label>
                <select name="currency" class="form-control" id="currency">
                    <option value="Local">Local Currency</option>
                    <option value="Reporting">Reporting Currency</option>
                </select>
            </div>
            <div class="form-group col-sm-2">
                <label>Warehouse</label><br>
                <?php
                $location = "";
                $type = 1;
                if ($type == 1) {
                    $location = array_filter(all_delivery_location_drop(true));
                } else {
                    $location = array_filter(all_group_warehouse_drop(true));
                }

                unset($location['']);
                echo form_dropdown('location[]', $location, '', 'class="location" id="location" multiple="multiple"'); ?>
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
            <div class="form-group col-sm-3">
                <label>Items </label><br>
                <select name="item" id="item"
                        class="form-control items" onchange="get_item_movement_report()">
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

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });


        $("#location").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#location").multiselect2('selectAll', false);
        $("#location").multiselect2('updateButtonText');

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

        $('.select2').select2();
        loadItems();
        $("#item").select2();
        get_item_movement_report();

    });


    function get_item_movement_report()
    {
        var data = $("#itm_movement_report_filter_frm").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('Inventory/load_movement_analysis_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Load_item_movement_report').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadSub() {
        $("#item").empty();
        loadSubCategory();
        loadItems();
    }

    function loadSubSub() {
        $("#item").empty();
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
                    $('#item').empty();
                    var mySelect = $('#item');
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemSystemCode'] + ' | ' + text['itemDescription']));
                        });
                    }
                }
                // $('#item').multiselect2('rebuild');
                // $("#item").multiselect2('selectAll', false);
                // $("#item").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('itm_movement_report_filter_frm');
        form.target = '_blank';
        /*form.action = 'php echo site_url('template_paySheet/get_payScale_report_pdf'); ?>';*/
        form.action = '<?php echo site_url('Inventory/load_movement_analysis_report_pdf'); ?>';
        form.submit();
    }
</script>