<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_detail_report');
echo head_page($title, false);
$warehouse=all_delivery_location_drop(false);
$main_category_arr = all_main_category_report_drop();
$customer_category_arr=all_customer_category_report_drop();
$customer = all_customer_drop(false);
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
<div class="box-tools pull-right hide">
    <button id="" onclick="openColumnSelection()" class="btn btn-box-tool " ><i class="fa fa-plus"></i></button>
</div>
<div class="box-body" style="display: block;width: 100%">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#inventory_report" data-toggle="tab" aria-expanded="false">Inventory</a></li>
            <li class=""><a href="#service_report" data-toggle="tab" aria-expanded="false">Services</a></li>
        </ul>
    </div>
</div>
<div class="tab-content">
    <div class="tab-pane active" id="inventory_report"><!--Inventory Report-->
        <?php echo form_open('login/loginSubmit', ' name="frm_itemizedprofitbleReport" id="frm_itemizedprofitbleReport" class="form-horizontal" role="form"'); ?>
        <input type="hidden" id="itemAutoID" name="itemAutoID">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group col-sm-3">
                    <label>Main Category </label><br>
                    <?php echo form_dropdown('mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" multiple id="mainCategoryID" onchange="loadSub()"'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label>Sub Category </label><br>
                    <select name="subcategoryID" id="subcategoryID" class="form-control searchbox" onchange="loadSubSub()" multiple="multiple">
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Sub Sub Category </label><br>
                    <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox" multiple="multiple"></select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Items </label><br>
                    <select name="items[]" id="items" class="form-control items" multiple="multiple"></select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group col-sm-3">
                    <label><?php echo $this->lang->line('common_warehouse'); ?></label>
                    <br>
                    <?php echo form_dropdown('wareHouseAutoID[]', $warehouse, 'Each', 'class="form-control" multiple id="wareHouseAutoID" '); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label for="">Extra Columns</label><br>
                    <?php echo form_dropdown('columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="load_profitable_report()" id="columSelectionDrop" multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label><?php echo $this->lang->line('common_customer_category'); ?></label><br>
                    <?php echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID"  multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                    <div id="div_load_customers">
                        <select name="customerID[]" class="form-control" id="filter_customerID" multiple="">
                            <?php
                            unset($customer[""]);
                            if (!empty($customer)) {
                                foreach ($customer as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group col-sm-2">
                    <label class="col-md-4 control-label"><?php echo $this->lang->line('common_from'); ?></label>
                    <div class="col-md-12">
                        <input type="text" required class="form-control input-sm startdateDatepic" name="filterFrom" id="filterFrom2" value="<?php echo date('1-1-Y 00:00:00') ?>">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label class="col-md-4 control-label"><?php echo $this->lang->line('common_to'); ?></label>
                    <div class="col-md-12">
                        <input type="text" required class="form-control input-sm startdateDatepic" value="<?php echo date('d-m-Y 23:59:59') ?>" placeholder="To" name="filterTo" id="filterTo2">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label><?php echo $this->lang->line('common_currency'); ?></label>
                    <select name="currency" class="form-control" id="currency">
                        <option value="Local">Local Currency</option>
                        <option value="Reporting">Reporting Currency</option>
                    </select>
                </div>
                <!-- <div class="form-group col-sm-1"></div>-->
                <!-- <div class="form-group col-sm-2 hide" id="columSelectionDiv">
                <label for="">Extra Columns</label>
                <?php echo form_dropdown('columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="load_profitable_report()" id="columSelectionDrop" multiple="multiple"'); ?>
            </div> -->
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12"  style="margin-top: 25px;" >
                <a href="#" type="button" class="btn btn-excel pull-right" style="margin-left: 2px" onclick="excel_export_itemProfit()">
                    <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?> <!--Excel-->
                </a>
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="load_profitable_report()">
                    <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                </button>
            </div>
        </div>
        </form>
        <hr>
        <div class="table-responsive" id="item_wise_profitable_table" style="height: 500px;">

        </div>
    </div>
    <div class="tab-pane active" id="service_report"><!--Service Report-->
        <?php echo form_open('login/loginSubmit', ' name="serviceReport" id="serviceReport" class="form-horizontal" role="form"'); ?>
        <input type="hidden" id="s_itemAutoID" name="itemAutoID">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group col-sm-3">
                    <label>Main Category </label><br>
                    <?php echo form_dropdown('s_mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" multiple id="s_mainCategoryID" onchange="s_loadSub()"'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label>Sub Category </label><br>
                    <select name="s_subcategoryID" id="s_subcategoryID" class="form-control searchbox" onchange="s_loadSubSub()" multiple="multiple">
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Sub Sub Category </label><br>
                    <select name="s_subsubcategoryID" id="s_subsubcategoryID" class="form-control searchbox" multiple="multiple"></select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Items </label><br>
                    <select name="s_items[]" id="s_items" class="form-control items" multiple="multiple"></select>
                </div>
            </div>
            <div class="col-sm-12">
                <!--<div class="form-group col-sm-3">
                    <label><?php /*echo $this->lang->line('common_warehouse'); */?></label>
                    <br>
                    <?php /*echo form_dropdown('s_wareHouseAutoID[]', $warehouse, 'Each', 'class="form-control" multiple id="s_wareHouseAutoID" '); */?>
                </div>-->

                <div class="form-group col-sm-3">
                    <label for="">Extra Columns</label><br>
                    <?php echo form_dropdown('s_columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="load_service_report()" id="s_columSelectionDrop" multiple="multiple"'); ?>
                </div>

                <div class="form-group col-sm-3">
                    <label><?php echo $this->lang->line('common_customer_category'); ?></label><br>
                    <?php echo form_dropdown('s_customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="s_customerCategoryID"  multiple="multiple"'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                    <div id="s_div_load_customers">
                        <select name="s_customerID[]" class="form-control" id="s_filter_customerID" multiple="">
                            <?php
                            unset($customer[""]);
                            if (!empty($customer)) {
                                foreach ($customer as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group col-sm-2">
                    <label class="col-md-4 control-label"><?php echo $this->lang->line('common_from'); ?></label>
                    <div class="col-md-12">
                        <input type="text" required class="form-control input-sm startdateDatepic" name="s_filterFrom" id="s_filterFrom2" value="<?php echo date('1-1-Y 00:00:00') ?>">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label class="col-md-4 control-label"><?php echo $this->lang->line('common_to'); ?></label>
                    <div class="col-md-12">
                        <input type="text" required class="form-control input-sm startdateDatepic" value="<?php echo date('d-m-Y 23:59:59') ?>" placeholder="To" name="s_filterTo" id="s_filterTo2">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label><?php echo $this->lang->line('common_currency'); ?></label>
                    <select name="s_currency" class="form-control" id="s_currency">
                        <option value="Local">Local Currency</option>
                        <option value="Reporting">Reporting Currency</option>
                    </select>
                </div>
                <!-- <div class="form-group col-sm-1"></div>-->
                <!-- <div class="form-group col-sm-2 hide" id="columSelectionDiv">
                <label for="">Extra Columns</label>
                <?php echo form_dropdown('s_columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="load_service_report()" id="s_columSelectionDrop" multiple="multiple"'); ?>
            </div> -->
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12"  style="margin-top: 25px;" >
                <a href="#" type="button" class="btn btn-excel pull-right" style="margin-left: 2px" onclick="excel_export_service()">
                    <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?> <!--Excel-->
                </a>
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="load_service_report()">
                    <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                </button>
            </div>
        </div>
        </form>
        <hr>
        <div class="table-responsive" id="service_table" style="height: 500px;">

        </div>
    </div>

</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="item_wise_profitable_DD_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " style="width: 100%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Sales Detail Report Drill Down</h4>
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
                                <tbody id="profitability_DD"></tbody>
                                <tfoot id="profitability_DD_foot"></tfoot>
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
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/sales/sales_details_report', 'Test', '<?php echo $this->lang->line('sales_detail_report');?>');
        });
        load_categorybase_customer();
        s_load_categorybase_customer();

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {}
        }).on('dp.change', function (ev) {

        });

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
        loadItems();

        $("#customerCategoryID").change(function () {
            load_categorybase_customer();
        });
        $("#customerCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customerCategoryID").multiselect2('selectAll', false);
        $("#customerCategoryID").multiselect2('updateButtonText');

        $('#columSelectionDiv').addClass('hide');
        $('#columSelection').val();
        $("#columSelectionDrop").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#s_items").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#s_items").multiselect2('selectAll', false);
        $("#s_items").multiselect2('updateButtonText');

        $("#s_mainCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#s_subcategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#s_subcategoryID").multiselect2('selectAll', false);
        $("#s_subcategoryID").multiselect2('updateButtonText');

        $("#s_subsubcategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#s_subcategoryID").change(function () {
            s_loadSubSub();
        });

        $("#s_subsubcategoryID").change(function () {
            s_loadItems();
        });
        s_loadItems();

        $("#s_customerCategoryID").change(function () {
            s_load_categorybase_customer();
        });
        $("#s_customerCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#s_customerCategoryID").multiselect2('selectAll', false);
        $("#s_customerCategoryID").multiselect2('updateButtonText');

        $('#s_columSelectionDiv').addClass('hide');
        $('#s_columSelection').val();
        $("#s_columSelectionDrop").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $(".tdCol").hover(function (eventObject) {
            $(".applytoAll").hide();
            $(this).closest('td').find('span').show()
        });

        load_profitable_report();
        load_service_report();

    });

    function load_profitable_report() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('ItemMaster/load_sales_details_report_in_sales_and_marketing'); ?>",
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
    function load_service_report() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('ItemMaster/load_service_details_report_in_sales_and_marketing'); ?>",
            data: $("#serviceReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#service_table").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i> Loading... </div>');

            },
            success: function (data) {
                $("#service_table").html(data);
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
        form.action = '<?php echo site_url('ItemMaster/export_excel_sales_detail_report'); ?>';
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

    function load_categorybase_customer() {
        var customerCategoryID = $('#customerCategoryID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerCategoryID: customerCategoryID,type: 1,tab:1},
            url: "<?php echo site_url('Report/fetch_customerDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_customers').html(data);
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
                //fetch_farm();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    /* For Service tab */
    $("#s_columSelectionDrop").change(function () {
        if ((this.value)) {
            load_service_report(this.value);
            return false;
        }
    });

    function s_loadSub() {
        $("#s_items").empty();
        s_loadSubCategory();
        s_loadItems();
    }

    function s_loadSubSub() {
        $("#s_items").empty();
        s_loadSubSubCategory();
        s_loadItems();
    }

    function s_loadSubCategory() {
        $('#s_subcategoryID option').remove();
        var mainCategoryID = $('#s_mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/load_subcat"); ?>',
            dataType: 'json',
            data: {'mainCategoryID': mainCategoryID,type: 1},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#s_subcategoryID').empty();
                    var mySelect = $('#s_subcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#s_subcategoryID').multiselect2('rebuild');
                $("#s_subcategoryID").multiselect2('selectAll', false);
                $("#s_subcategoryID").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function s_loadSubSubCategory() {
        $('#s_subsubcategoryID option').remove();
        var subCategoryID = $('#s_subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subCategoryID': subCategoryID, type: 1},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#s_subsubcategoryID').empty();
                    var mySelect = $('#s_subsubcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#s_subsubcategoryID').multiselect2('rebuild');
                $("#s_subsubcategoryID").multiselect2('selectAll', false);
                $("#s_subsubcategoryID").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function s_load_categorybase_customer() {
        var customerCategoryID = $('#s_customerCategoryID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerCategoryID: customerCategoryID,type: 1,tab:2},
            url: "<?php echo site_url('Report/fetch_customerDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#s_div_load_customers').html(data);
                $('#s_filter_customerID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#s_filter_customerID").multiselect2('selectAll', false);
                $("#s_filter_customerID").multiselect2('updateButtonText');
                //fetch_farm();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function s_loadItems() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/loadItems"); ?>',
            dataType: 'json',
            data: {
                subSubCategoryID: $('#s_subsubcategoryID').val(),
                mainCategoryID: $('#s_mainCategoryID').val(),
                subCategoryID: $('#s_subcategoryID').val(),
                type: 1,
                servicetype:1
            },
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#s_items').empty();
                    var mySelect = $('#s_items');
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
                $('#s_items').multiselect2('rebuild');
                $("#s_items").multiselect2('selectAll', false);
                $("#s_items").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


</script>