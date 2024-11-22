<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$title = $this->lang->line('sales_markating_sales_person_performance');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$salesperso_arr = all_sales_person_drop(false);
$warehouse=all_delivery_location_drop_with_status(false);
$main_category_arr = all_main_category_report_drop();

?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>

<div class="box-body" style="display: block;width: 100%">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_public_1" data-toggle="tab" aria-expanded="false"
                onclick="">Summary</a></li>
            <li class=""><a href="#tab_public_2" data-toggle="tab" aria-expanded="true"
                onclick="">Detail</a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_public_1">
            <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' name="frm_sales_person_rpt" id="frm_sales_person_rpt" class="form-group" role="form"'); ?>
                        <div class="col-md-12">
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                                <select name="currency" class="form-control " id="currency" onchange="get_sales_person_performance_report()" tabindex="-1" aria-hidden="true" data-bv-field="currency">
                                <!--  <option value="1">Transaction Currency</option>-->
                                    <option value="2" selected>Local Currency</option>
                                    <option value="3" >Reporting Currency</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="datefrom"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                                <div class="input-group datepicto">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateto"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('sales_markating_sales_person'); ?><!--Sales Person--></label>
                                    <?php echo form_dropdown('salesperson[]', $salesperso_arr, '', 'multiple  class="form-control select2" id="salesperson" required'); ?>
                            </div>
                            <div class="form-group col-sm-1">
                                <label for=""></label>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button style="margin-top: 10%" type="button" onclick="get_sales_person_performance_report()"
                                                class="btn btn-primary btn-xs">
                                            <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                                    </div>
                                </div>

                            </div>


                        </div>
                    <?php echo form_close(); ?>
                </fieldset>
            </div>
            <hr style="margin: 0px;">
            <div id="div_customer_invoice">
            </div>
        </div>
        <div class="tab-pane " id="tab_public_2">
        <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' name="sales_person_detail_fm" id="sales_person_detail_fm" class="form-group" role="form"'); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-sm-3">
                                    <label>Main Category </label><br>
                                    <?php echo form_dropdown('detail_mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control" multiple id="detail_mainCategoryID" onchange="loadSub()"'); ?>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Sub Category </label><br>
                                    <select name="detail_subcategoryID" id="detail_subcategoryID" class="form-control searchbox" onchange="loadSubSub()" multiple="multiple">
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Sub Sub Category </label><br>
                                    <select name="detail_subsubcategoryID" id="detail_subsubcategoryID" class="form-control searchbox" multiple="multiple"></select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Items </label><br>
                                    <select name="detail_items[]" id="detail_items" class="form-control items" multiple="multiple"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-sm-3">
                                    <label><?php echo $this->lang->line('common_warehouse'); ?></label>
                                    <br>
                                    <?php echo form_dropdown('wareHouseAutoID[]', $warehouse, 'Each', 'class="form-control" multiple id="wareHouseAutoID" '); ?>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for=""><?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                                    <select name="detail_currency" class="form-control " id="detail_currency" onchange="" tabindex="-1" aria-hidden="true" data-bv-field="currency">
                                    <!--  <option value="1">Transaction Currency</option>-->
                                        <option value="2" selected>Local Currency</option>
                                        <option value="3" >Reporting Currency</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="detail_datefrom"
                                            data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                            value="<?php echo $start_date; ?>" id="detail_datefrom" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                                    <div class="input-group datepicto">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="detail_dateto"
                                            data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                            value="<?php echo $current_date; ?>" id="detail_dateto" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for=""><?php echo $this->lang->line('sales_markating_sales_person'); ?><!--Sales Person--></label>
                                        <?php echo form_dropdown('detail_salesperson[]', $salesperso_arr, '', 'multiple  class="form-control select2" id="detail_salesperson" required'); ?>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for=""><?php echo $this->lang->line('common_type'); ?><!--Type--></label>
                                    <?php echo form_dropdown('detail_type', array('1'=>'Contract/ Sales Order','2'=>'Invoice/ Deliver Order'), '2','class="form-control controlCls" id="detail_type" onclick="load_sales_person_performance_details_report();" required'); ?>
                                
                                </div>
                                <div class="form-group col-sm-1">
                                    <label for=""></label>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button style="margin-top: 10%" type="button" onclick="load_sales_person_performance_details_report();"
                                                    class="btn btn-primary btn-xs">
                                                <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-1">
                                    <label for=""></label>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="button" style="margin-top: 10%" class="btn btn-success btn-xs" onclick="generateReportExcel()" ><?php echo $this->lang->line('common_excel');?></button>
             
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </fieldset>
        </div>
        <div id="sales_person_detail">

        </div>
    </div>
</div>




<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="sales_person_com_dd" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_sales_person_performance_drill_down'); ?><!--Sales Person Performance Drill Down--></h4>
            </div>
            <div class="modal-body" id="salesperson_detail">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sales_person_det_dd" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_sales_person_performance_drill_down'); ?><!--Sales Person Performance Drill Down--></h4>
            </div>
            <div class="modal-body" id="item_wise_salesperson_detail">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var type;

    $(document).ready(function (e) {
        $('#salesperson,#detail_salesperson,#detail_items,#detail_subcategoryID,#detail_subsubcategoryID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#salesperson,#detail_salesperson,#detail_items,#detail_subcategoryID,#detail_subsubcategoryID").multiselect2('selectAll', false);
        $("#salesperson,#detail_salesperson,#detail_items,#detail_subcategoryID,#detail_subsubcategoryID").multiselect2('updateButtonText');

        $("#wareHouseAutoID,#detail_mainCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#wareHouseAutoID").multiselect2('selectAll', false);
        $("#wareHouseAutoID").multiselect2('updateButtonText');
        
        $("#detail_mainCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('.headerclose').click(function () {
            fetchPage('system/sales/erp_sales_person_report', '', 'Sales Person')
        });

        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        $("#detail_subcategoryID").change(function () {
            loadSubSub();
        });

        $("#detail_subcategoryID").change(function () {
            loadItems();
        });
        loadItems();



        get_sales_person_performance_report();
        load_sales_person_performance_details_report();

    });

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepicto').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });

    function get_sales_person_performance_report() {
        var data = $("#frm_sales_person_rpt").serialize();
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('Sales/get_sales_person_performance_report'); ?>',
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_customer_invoice").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_sales_person_rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Sales/get_sales_person_performance_report_pdf'); ?>';
        form.submit();
    }

    function opensalespersondd(salesPersonID,salespersontype){
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        var currency = $('#currency').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Sales/get_sales_preformance_dd'); ?>",
            data: {'salesPersonID': salesPersonID,'datefrom':datefrom,'dateto':dateto,'currency':currency,'salespersontype':salespersontype},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#salesperson_detail").html(data);
                $('#sales_person_com_dd').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
    function loadSub(){
        $("#detail_items").empty();
        loadSubCategory();
        loadItems();
    }

    function loadSubSub(){
        $("#detail_items").empty();
        loadSubSubCategory();
        loadItems()
    }

    function loadSubCategory(){
        $('#detail_subcategoryID option').remove();
        var mainCategoryID = $('#detail_mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/load_subcat"); ?>',
            dataType: 'json',
            data: {'mainCategoryID': mainCategoryID,type: 1},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#detail_subcategoryID').empty();
                    var mySelect = $('#detail_subcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#detail_subcategoryID').multiselect2('rebuild');
                $("#detail_subcategoryID").multiselect2('selectAll', false);
                $("#detail_subcategoryID").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function loadSubSubCategory(){
        $('#detail_subsubcategoryID option').remove();
        var subCategoryID = $('#detail_subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subCategoryID': subCategoryID, type: 1},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#detail_subsubcategoryID').empty();
                    var mySelect = $('#detail_subsubcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#detail_subsubcategoryID').multiselect2('rebuild');
                $("#detail_subsubcategoryID").multiselect2('selectAll', false);
                $("#detail_subsubcategoryID").multiselect2('updateButtonText');
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
                subSubCategoryID: $('#detail_subsubcategoryID').val(),
                mainCategoryID: $('#detail_mainCategoryID').val(),
                subCategoryID: $('#detail_subcategoryID').val(),
                type: 1
            },
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#detail_items').empty();
                    var mySelect = $('#detail_items');
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
                $('#detail_items').multiselect2('rebuild');
                $("#detail_items").multiselect2('selectAll', false);
                $("#detail_items").multiselect2('updateButtonText');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_sales_person_performance_details_report(){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Sales/load_sales_person_performance_details_report'); ?>",
            data: $("#sales_person_detail_fm").serialize(),
            cache: false,
            beforeSend: function () {
                $("#sales_person_detail").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i> Loading... </div>');

            },
            success: function (data) {
                $("#sales_person_detail").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function openSalespersonDetaildd(salesPersonID,item){

        var wareHouseAutoID = $('#wareHouseAutoID').val();
        var currency = $('#detail_currency').val();
        var datefrom = $('#detail_datefrom').val();
        var dateto = $('#detail_dateto').val();
        var type = $('#detail_type').val();
        
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Sales/get_sales_preformance_details_dd'); ?>",
            data: {'salesPersonID': salesPersonID,'datefrom':datefrom,'dateto':dateto,'currency':currency,'type':type,'item':item,'wareHouseAutoID':wareHouseAutoID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#item_wise_salesperson_detail").html(data);
                $('#sales_person_det_dd').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportExcel() {
        var msg = '';
        if( $('#detail_datefrom').val() == '' ){
            msg += 'Date From field is required<br/>';
        }

        if( $('#detail_dateto').val() == '' ){
            msg += 'Date To field is required';
        }
       if( $('#detail_type').val() == null ){
            msg += 'Type field is required';
        }

        if( $('#detail_salesperson').val() == null ){
            msg += 'Salesperson field is required';
        }
        if( $('#wareHouseAutoID').val() == null ){
            msg += 'Warehouse field is required';
        }
        if( $('#detail_items').val() == null ){
            msg += 'Warehouse field is required';
        }
        if(msg != ''){
            myAlert('e', msg);
            return false;
        }

        var form = document.getElementById('sales_person_detail_fm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Sales/load_sales_person_performance_details_report/excel') ?>';
        form.submit();
    }
</script>
