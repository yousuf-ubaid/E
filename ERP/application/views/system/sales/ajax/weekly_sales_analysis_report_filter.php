<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('buyback_helper');

$date_format_policy = date_format_policy();
if ($type == 1) {
    $from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
    $to = convert_date_format($this->common_data['company_data']['FYPeriodDateTo']);
} else {
    $from = convert_date_format($this->session->userdata("FYBeginingDate"));
    $to = convert_date_format($this->session->userdata("FYEndingDate"));
}
$main_category_arr = all_main_category_report_drop();
$main_category_group_arr = all_main_category_group_report_drop();

$financeyear_arr = "";
$companyFinanceYearID = "";
if($this->session->userdata("companyType") == 1){
    $customer = all_customer_drop(false);
    $financeyear_arr = all_financeyear_drop(true);
    $companyFinanceYearID = $this->common_data['company_data']['companyFinanceYearID'];
}else{
    $customer = all_group_customer_drop(false);
    $financeyear_arr = all_group_financeyear_report_drop(true);
}
?>
    <ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
        <li class="active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('transaction_display');?> <!-- Display--></a></li>
        <li>
    </ul>
    <input type="hidden" name="reportID" value="<?php echo $reportID ?>">
    <div class="tab-content">
        <div class="tab-pane active" id="display">
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?php echo $this->lang->line('transaction_date_range');?> <!--Date Range--></legend>
                        <div class="form-group col-sm-4" style="margin-bottom: 10px">
                            <div class="col-md-8">
                                <label><?php echo $this->lang->line('transaction_common_financial_year');?> <!-- Financial Year--> &nbsp;</label>
                                <?php echo form_dropdown('financeyear', $financeyear_arr, $companyFinanceYearID, 'class="form-control" id="financeyear" onchange="fetch_finance_year_period(this.value)" required'); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-4" style="margin-bottom: 10px">
                            <div class="col-md-8">
                                <label> <?php echo $this->lang->line('transaction_common_financial_period');?> <!-- Financial Period -->&nbsp;</label>
                                <?php echo form_dropdown('financeyear_period', array('' => 'Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_warehouse'); ?> <!--Warehouse--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <?php
                            $location = "";
                            if ($type == 1) {
                                // $location = array_filter(all_delivery_location_drop(true));
                                $location = array_filter(all_delivery_location_drop_with_status(true));

                            } else {
                                $location = array_filter(all_group_warehouse_drop(true));
                            }
                            unset($location['']);
                            echo form_dropdown('location[]', $location, '', 'class="location" id="location" multiple="multiple"'); ?>
                        </div>
                    </fieldset>

                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('inventory_data_view'); ?><!--Data View--></legend>
                        <!--View Data-->
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <?php
                                    echo form_dropdown('dataFilter', array('' => 'Select Data', '1' => 'Income', '2' => 'Avg. Price', '3' => 'Quantity'), '',  'class="form-control select2" id="dataFilter" onchange="cusCategoryFilter()"');
                                    ?>
                                </div>
                                <div class="col-sm-3 hidden" id="selectHeader">
                                    <select name="datafilter2" class="form-control select2" id="filter_datafilter2" onchange="selectFieldSets()">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>


                    <fieldset class="scheduler-border hidden" style="margin-top: 10px" id="ItemCategoryField">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_item'); ?><!--Item--></legend>
                        <!--Items-->
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('transaction_main_category'); ?><!-- Main Category--> &nbsp;</label>
                                    <?php if ($type == 1) {
                                        echo form_dropdown('mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                                    } else {
                                        echo form_dropdown('mainCategoryID[]', $main_category_group_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('transaction_sub_category'); ?><!--Sub Category--> &nbsp;</label><br>
                                    <select name="subcategoryID[]" id="subcategoryID" class="form-control searchbox" multiple="multiple" onchange="loadItemsSales()">

                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('common_item'); ?><!--Items -->&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><br>
                                    <!--Sub Category-->
                                    <div id="div_load_filter_itemAutoID">
                                        <select name="itemAutoID[]" id="filter_itemAutoID" class="form-control searchbox" multiple="multiple">
                                            <!--Select Category-->
                                        </select>
                                    </div>
                                </div>
                                <!--<div class="col-sm-3">
                                    <label>Items &nbsp;</label>
                                    <select name="itemAutoID[]" id="itemAutoID" class="form-control" multiple="multiple">
                                    </select>
                                </div>-->
                            </div>
                        </div>
                    </fieldset>



                    <fieldset class="scheduler-border hidden" style="margin-top: 10px" id="customerCategoryField">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_customer'); ?><!--Customer--> </legend>

                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('common_customer_category'); ?><!--Customer Category -->&nbsp;</label><br>
                                    <?php
                                    $customerCategory    = party_category(1, false);
                                    echo form_dropdown('cusCategory[]', $customerCategory, '', 'class="form-control" id="cusCategory" onchange="loadCustomerDrop()" multiple="multiple"');
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('common_customer'); ?><!--Customer--> &nbsp;</label>
                                    <!--Sub Category-->
                                    <div id="div_load_filter_customer">
                                        <select name="customerID[]" id="filter_customerID" class="form-control searchbox" multiple="multiple">
                                            <!--Select Category-->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="scheduler-border hidden" style="margin-top: 10px" id="AreaCategoryField">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_area'); ?><!--Area--></legend>
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <?php
                                    $customerAreaFilter    = load_all_locations(false);
                                    echo form_dropdown('area[]', $customerAreaFilter, '', 'class="form-control" id="area" multiple="multiple"');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="margin-top: 10px">
                    <button type="button" class="btn btn-primary pull-right"
                            onclick="generateReport('<?php echo $formName; ?>')" name="filtersubmit"
                            id="filtersubmit"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate'); ?>
                    </button><!--Generate-->
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            fetch_finance_year_period(<?php echo $companyFinanceYearID;?>);
        });
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.select2').select2();
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        $('.skin-square input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
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
        $("#mainCategoryID").multiselect2('selectAll', false);
        $("#mainCategoryID").multiselect2('updateButtonText');

        $("#cusCategory").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#cusCategory").multiselect2('selectAll', false);
        $("#cusCategory").multiselect2('updateButtonText');

        $("#filter_customerID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_customerID").multiselect2('selectAll', false);
        $("#filter_customerID").multiselect2('updateButtonText');

        $("#area").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#area").multiselect2('selectAll', false);
        $("#area").multiselect2('updateButtonText');

        $("#subcategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#subcategoryID").multiselect2('selectAll', false);
        $("#subcategoryID").multiselect2('updateButtonText');

        $("#filter_itemAutoID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_itemAutoID").multiselect2('selectAll', false);
        $("#filter_itemAutoID").multiselect2('updateButtonText');

        loadSubCategory();
        loadItemsSales();
        loadCustomerDrop();
        function loadSub() {
            loadSubCategory();
        }

        function loadSubCategory() {
            $('#subcategoryID option').remove();
            var mainCategoryID = $('#mainCategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Report/load_subcat"); ?>',
                dataType: 'json',
                data: {'mainCategoryID': mainCategoryID,type:<?php echo $type; ?>},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subcategoryID').empty();
                        var mySelect = $('#subcategoryID');
                        $.each(data, function (k, value) {
                            mySelect.append($('<option></option>').val(value['itemCategoryID']).html(value['description']));
                        });
                        loadItemsSales();
                    }
                    $('#subcategoryID').multiselect2('rebuild');
                    /* $("#subcategoryID").multiselect2('selectAll', false);
                     $("#subcategoryID").multiselect2('updateButtonText');*/
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function loadItemsSales() {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Sales/loadItems"); ?>',
                dataType: 'html',
                data: {
                    subSubCategoryID: $('#subsubcategoryID').val(),
                    mainCategoryID: $('#mainCategoryID').val(),
                    subCategoryID: $('#subcategoryID').val(),
                    type: <?php echo $type; ?>
                },
                async: false,
                success: function (data) {
                    $('#div_load_filter_itemAutoID').html(data);
                    $('#filter_itemAutoID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: 150,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#filter_itemAutoID").multiselect2('selectAll', false);
                    $("#filter_itemAutoID").multiselect2('updateButtonText');
                    //$('#province').val(province).change();
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }
        function loadCustomerDrop() {
            var cusCategory = $('#cusCategory').val();
            $.ajax({
                type: 'post',
                dataType: 'html',
                url: '<?php echo site_url("Sales/loadCustomer"); ?>',
                data: {'cusCategory': cusCategory},
                async: false,
                success: function (data) {
                    $('#div_load_filter_customer').html(data);
                    $('#filter_customerID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: 150,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#filter_customerID").multiselect2('selectAll', false);
                    $("#filter_customerID").multiselect2('updateButtonText');
                    //$('#province').val(province).change();
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function cusCategoryFilter() {
            var filterVal = $('#dataFilter').val();
            if(filterVal == 1){
                $('#selectHeader').removeClass('hidden');
                $('#filter_datafilter2').empty();
                var mySelect = $('#filter_datafilter2');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_select_group_by');?>.'));/*Select Group By*/
                mySelect.append($('<option></option>').val(1).html('<?php echo $this->lang->line('sales_markating_item_wise');?>.'));/*Item Wise*/
                mySelect.append($('<option></option>').val(2).html('<?php echo $this->lang->line('sales_markating_item_category_wise');?>.'));/*Item Category Wise*/
                mySelect.append($('<option></option>').val(3).html('<?php echo $this->lang->line('sales_markating_area_wise');?>.'));/*Area Wise*/
            } else if (filterVal == 2){
                $('#selectHeader').removeClass('hidden');
                $('#filter_datafilter2').empty();
                var mySelect = $('#filter_datafilter2');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_select_group_by');?>.'));/*Select Group By*/
                mySelect.append($('<option></option>').val(1).html('<?php echo $this->lang->line('sales_markating_item_wise');?>.'));/*Item Wise*/
                mySelect.append($('<option></option>').val(2).html('<?php echo $this->lang->line('sales_markating_item_category_wise');?>.'));/*Item Category Wise*/
            } else if (filterVal == 3){
                $('#selectHeader').removeClass('hidden');
                $('#filter_datafilter2').empty();
                var mySelect = $('#filter_datafilter2');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_select_group_by');?>.'));/*Select Group By*/
                mySelect.append($('<option></option>').val(1).html('<?php echo $this->lang->line('sales_markating_item_wise');?>.'));/*Item Wise*/
                mySelect.append($('<option></option>').val(2).html('<?php echo $this->lang->line('sales_markating_item_category_wise');?>.'));/*Item Category Wise*/
                mySelect.append($('<option></option>').val(3).html('<?php echo $this->lang->line('sales_markating_area_wise');?>.'));/*Area Wise*/
                mySelect.append($('<option></option>').val(4).html('<?php echo $this->lang->line('sales_markating_customer_wise');?>.'));/*Customer Wise*/
                mySelect.append($('<option></option>').val(5).html('<?php echo $this->lang->line('sales_markating_customer_category_wise');?>.'));/*Customer Category Wise*/
            }
        }
        function selectFieldSets() {
            var filterVal = $('#filter_datafilter2').val();
            if(filterVal == 1){
                $('#ItemCategoryField').removeClass('hidden');
                $('#AreaCategoryField').addClass('hidden');
                $('#customerCategoryField').addClass('hidden');
            } else if (filterVal == 2){
                $('#ItemCategoryField').removeClass('hidden');
                $('#AreaCategoryField').addClass('hidden');
                $('#customerCategoryField').addClass('hidden');
            } else if (filterVal == 3){
                $('#ItemCategoryField').addClass('hidden');
                $('#AreaCategoryField').removeClass('hidden');
                $('#customerCategoryField').addClass('hidden');
            } else if (filterVal == 4){
                $('#ItemCategoryField').addClass('hidden');
                $('#AreaCategoryField').addClass('hidden');
                $('#customerCategoryField').removeClass('hidden');
            } else if (filterVal == 5){
                $('#ItemCategoryField').addClass('hidden');
                $('#AreaCategoryField').addClass('hidden');
                $('#customerCategoryField').removeClass('hidden');
            }
        }

        function fetch_finance_year_period(companyFinanceYearID, select_value) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyFinanceYearID': companyFinanceYearID},
                url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
                success: function (data) {
                    $('#financeyear_period').empty();
                    var mySelect = $('#financeyear_period');
                    mySelect.append($('<option></option>').val('').html('Select Financial Period'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                        });
                        if (select_value) {
                            $("#financeyear_period").val(select_value);
                        }
                    }
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }

    </script>