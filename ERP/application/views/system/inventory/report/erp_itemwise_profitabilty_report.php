<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('inventory_item_profitability_report');
echo head_page($title, false);

$warehouse=all_delivery_location_drop_with_status(false);

$main_category_arr = all_main_category_report_drop();
?>
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .fc {
        height: 22px !important;
        width: 100% !important;
        display: inline !important;
        margin: 0px !important;
    }

    .arrowDown {
        vertical-align: sub;
        color: rgb(75, 138, 175);
        font-size: 13px;
    }

    .applytoAll {
        display: none;
        vertical-align: top;
    }
</style>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<!--<form id="frm_itemizedprofitbleReport">-->
<div class="box-tools pull-right">
    <button id="" onclick="openColumnSelection()" class="btn btn-box-tool " ><i class="fa fa-plus"></i></button>
</div>
<?php echo form_open('login/loginSubmit', ' name="frm_itemizedprofitbleReport" id="frm_itemizedprofitbleReport" class="form-horizontal" role="form"'); ?>
    <input type="hidden" id="itemAutoID" name="itemAutoID">

<div class="row">
    <div class="col-sm-12">
        <!-- <div class="form-group col-sm-3">
            <label for=""><?php // echo $this->lang->line('common_item'); ?></label>
            <br>
            <?php // echo form_dropdown('items[]', fetch_item_dropdown(false), '', 'class="form-control" id="items"  multiple style="z-index: 0;"'); ?>
        </div> -->
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
        <div class="col-sm-2">
            <label for="status_filter_item"><?php echo $this->lang->line('common_item_status');?></label>
            <?php echo form_dropdown('status_filter_item', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item" '); ?>
        </div>
    </div>
    <div class="col-sm-12">    
        <div class="form-group col-sm-3">
            <label for="items">Items </label><br>
            <select name="items[]" id="items" class="form-control items" multiple="multiple">
                <!--Select Category-->
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> </label>
            <br>
            <?php echo form_dropdown('wareHouseAutoID[]', $warehouse, 'Each', 'class="form-control" multiple id="wareHouseAutoID" '); ?>
        </div>
        <div class="form-group col-sm-2">
            <label class="col-md-4 control-label"><?php echo $this->lang->line('common_from'); ?></label>
            <div class="col-md-12">
                <input type="text" required class="form-control input-sm startdateDatepic" name="filterFrom"
                       id="filterFrom2"
                       value="<?php echo date('1-1-Y 00:00:00') ?>">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label class="col-md-4 control-label"><?php echo $this->lang->line('common_to'); ?></label>
            <div class="col-md-12">
                <input type="text" required class="form-control input-sm startdateDatepic"
                       value="<?php echo date('d-m-Y 23:59:59') ?>" placeholder="To" name="filterTo" id="filterTo2">
            </div>
        </div>

        <div class="form-group col-sm-2">
            <label><?php echo $this->lang->line('common_currency'); ?><!--Currency-->  </label>
            <select name="currency" class="form-control" id="currency">
                <option value="Local">Local Currency</option>
                <option value="Reporting">Reporting Currency</option>
            </select>
        </div>
       <!-- <div class="form-group col-sm-1"></div>-->
        <div class="form-group col-sm-2 hide" id="columSelectionDiv">
            <label for="">Extra Columns</label>
            <?php echo form_dropdown('columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="load_profitable_report()" id="columSelectionDrop" multiple="multiple"'); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12"  style="margin-top: 25px;" >
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="load_profitable_report()">
            <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
        </button>
    </div>
</div>
</form>
<hr>
<div class="table-responsive" id="item_wise_profitable_table">

</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="item_wise_profitable_DD_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " style="width: 100%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Item Profitability Report Drill Down</h4>
            </div>
            <div class="modal-body">
                <div class="row" >
                    <div class="col-sm-12" >
                        <div class="table-responsive" >
                            <table class="<?php echo table_class(); ?>"  >
                                <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Document Code</th>
                                    <th>Qty</th>
                                    <th>Total Sales Value </th>
                                    <th>Total Cost </th>
                                    <th>Profit</th>
                                </tr>
                                </thead>
                                <tbody id="profitability_DD">

                                </tbody>
                                <tfoot id="profitability_DD_foot">

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>



<?php

/*subItemConfigList_modal*/
$this->load->view('system/item/itemmastersub/item-master-list-view-modal');
?>
<script type="text/javascript">
    var Otable;
    var Otables;
    $(document).ready(function () {
        $('#columSelectionDiv').addClass('hide');
        $('#columSelection').val();

        $("#wareHouseAutoID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#wareHouseAutoID").multiselect2('selectAll', false);
        $("#wareHouseAutoID").multiselect2('updateButtonText');

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

        $("#status_filter_item").change(function () {
            loadItems();
        });
        loadItems();

        $("#customerAutoID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#customerAutoID").multiselect2('selectAll', false);
        $("#customerAutoID").multiselect2('updateButtonText');

        $("#columSelectionDrop").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('.headerclose').click(function () {
            fetchPage('system/inventory/report/erp_itemwise_profitabilty_report', 'Test', 'Item Profitibility Report');
        });
        load_profitable_report();

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {}
        }).on('dp.change', function (ev) {

        });

        $('.select2').select2();

        $(".tdCol").hover(function (eventObject) {
            $(".applytoAll").hide();
            $(this).closest('td').find('span').show()
        });

        $('#itemmultipletableserver').tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

    });

    function load_profitable_report() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('ItemMaster/load_item_wise_prfitability_report'); ?>",
            data: $("#frm_itemizedprofitbleReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#item_wise_profitable_table").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i> Loading... </div>');

            },
            success: function (data) {
                $("#item_wise_profitable_table").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_profitabilityDD(itemAutoID){
        $('#itemAutoID').val(itemAutoID);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('ItemMaster/load_item_wise_prfitability_report_DD'); ?>",
            data: $("#frm_itemizedprofitbleReport").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#profitability_DD').empty();
                    $('#profitability_DD_foot').empty();
                } else {
                    $('#profitability_DD').empty();
                    $('#profitability_DD_foot').empty();

                    var totalQty = 0;
                    var totalAmount = 0;
                    var totalWAC = 0;
                    var totalProfit = 0;
                    var decimal = 2;

                    $.each(data, function (key, value) {


                        if(value['documentID']=='SLR' || value['documentID']=='RET'){
                            var profit= (value['totSalesVal']- value['totalCost'])*-1;
                        }else{
                            var profit= Math.abs(value['totSalesVal'])- Math.abs(value['totalCost']);
                        }
                            var margin= 0;
                            if (value['totSalesVal'] != 0) {
                                margin= ( Math.abs(value['totSalesVal'])- Math.abs(value['totalCost']) /  Math.abs(value['totSalesVal'])) * 100;
                            }
                             decimal=value['decimalplace'];



                            if(value['documentID']=='SLR' || value['documentID']=='RET'){
                                var totSalesVal=(parseFloat(value['totSalesVal']*-1).toFixed(decimal));
                                var qty=parseFloat(value['qty']*-1);
                                var totalCost=parseFloat(value['totalCost']*-1).toFixed(decimal);

                                totalQty += value['qty']*-1;
                                totalAmount +=value['totSalesVal']*-1;
                                totalWAC += value['totalCost']*-1;
                                totalProfit += (value['totSalesVal']-value['totalCost'])*-1;
                            }else{
                                var totSalesVal=Math.abs(parseFloat(value['totSalesVal']).toFixed(decimal));
                                var qty=Math.abs(value['qty']);
                                var totalCost=Math.abs(parseFloat(value['totalCost']).toFixed(decimal));

                                totalQty += Math.abs(value['qty']);
                                totalAmount += Math.abs(value['totSalesVal']);
                                totalWAC += Math.abs(value['totalCost']);
                                totalProfit += Math.abs(value['totSalesVal'])-Math.abs(value['totalCost']);
                            }
                            $('#profitability_DD').append('<tr><td>' + x + '</td> <td><a href="#" class="" onclick="documentPageView_modal(\'' + value['documentID'] + '\' , ' + value["documentAutoID"] + ')">' + value['documentSystemCode'] + '</a></td> <td style="text-align: right;">' +  qty  + '</td> <td style="text-align: right;">' +  parseFloat(totSalesVal).toFixed(decimal)  + '</td> <td style="text-align: right;">' +  parseFloat(totalCost).toFixed(decimal)  + '</td> <td style="text-align: right;">' +  parseFloat(profit).toFixed(decimal) + '</td></tr>');
                            x++;

                    });
                    $('#profitability_DD_foot').append('<tr><td colspan="2" style="text-align: right;">Total</td> <td style="text-align: right;">' + totalQty + '</td> <td style="text-align: right;">' + parseFloat(totalAmount).toFixed(decimal) + '</td> <td style="text-align: right;">' + parseFloat(totalWAC).toFixed(decimal) + '</td> <td style="text-align: right;">' + parseFloat(totalProfit).toFixed(decimal) + '</td></tr>');
                    $('#item_wise_profitable_DD_model').modal('show');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function excel_export_itemProfit() {
        var form = document.getElementById('frm_itemizedprofitbleReport');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#frm_itemizedprofitbleReport').serializeArray();
        form.action = '<?php echo site_url('ItemMaster/export_excel_item_report'); ?>';
        form.submit();
    }
    function openColumnSelection(){
        if ($('#columSelectionDiv').hasClass('hide')) {
            $('#columSelectionDiv').removeClass('hide');
        }else{
            $('#columSelectionDiv').addClass('hide');
        }
    }
    $("#columSelectionDrop").change(function () {
        if ((this.value)) {
            load_profitable_report(this.value);
            return false;
        }
    });

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
</script>