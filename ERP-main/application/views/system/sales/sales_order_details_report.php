<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_sales_order_details');
echo head_page($title, false);
$customer="";
$type = $this->session->userdata("companyType");
if($this->session->userdata("companyType") == 1){
    $customer_category_arr=all_customer_category_report_drop();
    $customer = all_customer_drop(false,1);
}else{
    $customer = all_group_customer_drop(false);
}
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_sales_order_details" id="frm_rpt_sales_order_details" class="form-group" role="form"'); ?>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_document_types'); ?></label>
                <select name="documentTypes" class="form-control select2" id="documentTypes" onchange="get_sales_order_details_report()">
                    <option value="All">Select All</option>
                    <option value="QUT">Quotation</option>
                    <option value="CNT">Contract</option>
                    <option value="SO">Sales Order</option>
                </select>
            </div>
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
                <div class="input-group datepicto">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                </div>
            </div>
            <?php if ($type == 1) { ?>
                <div class="form-group col-sm-2">
                    <label><?php echo $this->lang->line('common_customer_category'); ?></label>
                    <?php echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID"  multiple="multiple"'); ?>
                </div>
          
                <div class="form-group col-sm-2 ">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
                </div>
            <?php }?>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                <?php // echo form_dropdown('customerID[]', $customer, '', 'multiple  class="form-control" id="customerID" required'); ?>
                <div id="div_load_customers">
                    <select name="customerID[]" class="form-control" id="filter_customerID" multiple="">
                        <?php
                        unset($customer[""]);
                        if (!empty($customer)) {
                            foreach ($customer as $key => $val) {
                                echo '<option value="' . $key . '">' . $val . '</option>';
                            }
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="segment">&nbsp;</label>
                <div class="input-group" id="">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="viewZeroBalace" id="viewZeroBalace" value="1" checked>
                                </span>
                    <input type="text" class="form-control" disabled="" value="Hide Zero Balance Qty">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_document_code'); ?></label>
                <br>
                    <?php echo form_dropdown('documentCodes[]', get_all_documentcodes(), '', 'class="form-control select2" id="documentCodes"  multiple="" style="z-index: 0;"'); ?>
            </div>
            <div class="form-group col-sm-2">
                    <label for="segment">
                      Status
                    </label>
                    <?php
                    $type = $this->session->userdata("companyType");

                    echo form_dropdown('statusID[]', array('1'=>'Fully Received','2'=>'Not Received','3'=>'Partially Received'), '', 'multiple  class="form-control" id="statusID" required'); ?>
                </div>
            <!-- <div class="form-group col-sm-2">
                <label for=""><?php //echo $this->lang->line('common_status'); ?></label>
                <select name="status" class="form-control select2" id="status" onchange="get_sales_order_details_report()">
                    <option value="All">Select All</option>
                    <option value="fullyReceived">Fully Received</option>
                    <option value="partiallyReceived">Partially Received</option>
                    <option value="notReceived">Not Received</option>
                </select>
            </div> -->
            <div class="form-group col-sm-8">
                <label for=""></label>
                <button style="margin-top: 28px" type="button" onclick="get_sales_order_details_report()"
                        class="btn btn-primary btn-xs pull-right">
                    <?php echo $this->lang->line('common_search'); ?><!--Search--></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_sales_order_details">
</div>



<script>
    var type;
    var url;
    var urlPdf;
    var urlDrill;
    $(document).ready(function (e) {
        $('.headerclose').click(function () {
            fetchPage('system/sales/sales_order_details_report', '', '<?php echo $this->lang->line('sales_markating_sales_order_details');?>')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.select2').select2();
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        load_categorybase_customer();
        $("#customerCategoryID").change(function () {
            load_categorybase_customer();
        });
        $("#status_filter").change(function () {
            load_categorybase_customer();
        });
        $("#customerCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            //selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customerCategoryID").multiselect2('selectAll', false);
        $("#customerCategoryID").multiselect2('updateButtonText');

        $('#filter_customerID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_customerID").multiselect2('selectAll', false);
        $("#filter_customerID").multiselect2('updateButtonText');

        $('#statusID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#statusID").multiselect2('selectAll', false);
        $("#statusID").multiselect2('updateButtonText');

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];
        if(type == 1){
            url = '<?php echo site_url('sales/get_sales_order_details_report'); ?>';
            urlPdf = '<?php echo site_url('sales/get_sales_order_details_report_pdf'); ?>';
            //urlDrill = '<?php //echo site_url('sales/get_sales_order_details_drilldown_report'); ?>//';
        }/*else{
            url = '<?php echo site_url('sales/get_group_sales_order_details_report'); ?>';
            urlPdf = '<?php echo site_url('sales/get_group_sales_order_details_report_pdf'); ?>';
            urlDrill = '<?php echo site_url('sales/get_group_sales_order_details_drilldown_report'); ?>';
        }*/
        get_sales_order_details_report();
    });

    function get_sales_order_details_report() {
        var data =  $("#frm_rpt_sales_order_details").serializeArray();
        var viewZeroBalace = ($('#viewZeroBalace').prop('checked'))? '1' : '0';
        // data.push({'name':'viewZeroBalace', 'value':viewZeroBalace});
        // alert(viewZeroBalace)
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_sales_order_details").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_categorybase_customer() {
        var customerCategoryID = $('#customerCategoryID').val();
        var status_filter = $('#status_filter').val();
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerCategoryID: customerCategoryID,type:<?php echo $type;?>,activeStatus:status_filter},
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
    
    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_sales_order_details');
        form.target = '_blank';
        form.action = urlPdf;
        form.submit();
    }
</script>


