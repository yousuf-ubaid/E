<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
//$this->lang->load('sales_marketing_reports', $primaryLanguage);
//$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
$financeyear_arr = all_financeyear_drop(true);

$customer_category_arr=all_customer_category_report_drop();
$type = $this->session->userdata("companyType");


$segment_arr = "";

if($type == 1){ 
    $customer = all_customer_drop(false,1);
    $segment_arr = fetch_segment(true,false);
}else { 
    $customer = all_group_customer_drop();
    $segment_arr = fetch_group_segment(true,false);
}

 
$title = $this->lang->line('accounts_receivable_collection_details');
echo head_page($title, false);
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
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filter'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_customer_collection_details" id="frm_rpt_customer_collection_details" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                    <select name="currency" class="form-control " id="currency" onchange="get_collection_detail_report()" tabindex="-1" aria-hidden="true" data-bv-field="currency">
                       <option value="1">Transaction Currency</option>
                        <option value="2">Local Currency</option>
                        <option value="3">Reporting Currency</option>
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="datefrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="" id="datefrom" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                    <div class="input-group datepicto">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="dateto"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="" id="dateto" class="form-control">
                    </div>
                </div>

                <?php if ($type == 1) {?>
                <div class="form-group col-sm-2">
                    <label><?php echo $this->lang->line('common_customer_category'); ?><!--Customer Category-->  </label>
                    <?php  
                    echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID"  multiple="multiple"');
              
                    ?>
                </div>
                <div class="form-group col-sm-2">
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
                            }

                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-2">
                    <label for="segment"><?php echo $this->lang->line('common_segment'); ?><!--Segment--></label>
                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'multiple class="form-control select2" id="segment" required'); ?>
                </div>

                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <button style="margin-top: 5px" type="button" onclick="get_collection_detail_report()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                </div>


            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_customer_invoice">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    var url;
    var urlPdf;
    $(document).ready(function (e) {

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

    $("#customerCategoryID").change(function () {
        // if ((this.value)) {
        //  load_categorybase_customer(this.value);
        //    return false;
        //  }
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

    $('#segment').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    
    $("#segment").multiselect2('selectAll', false);
    $("#segment").multiselect2('updateButtonText');
        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('Report/get_collection_detail_report'); ?>';
            urlPdf = '<?php echo site_url('Report/get_collection_detail_report_pdf'); ?>';
            urlExcel = '<?php echo site_url('Report/get_collection_detail_report_excel'); ?>';
        } else {
            url = '<?php echo site_url('Report/get_collection_detail_report_group'); ?>';
            urlPdf = '<?php echo site_url('Report/get_collection_detail_report_group_pdf'); ?>';
            urlExcel = '<?php echo site_url('Report/get_collection_detail_report_excel'); ?>';
        }
        get_collection_detail_report();

        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

 


    });



    
    $('.headerclose').click(function () {
        fetchPage('system/accounts_receivable/report/erp_collection_details_report', '', 'Collection Details')
    });
  
    function get_collection_detail_report() {
         
        var data = $("#frm_rpt_customer_collection_details").serialize();
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
                $("#div_customer_invoice").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
    $(document).ready(function (e) {
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

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
        get_collection_detail_report();
    });
    
    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_customer_collection_details');
        form.target = '_blank';
        form.action = urlPdf;
        form.submit();
    }

    function generateExcel() {
        var form = document.getElementById('frm_rpt_customer_collection_details');
        form.target = '_blank';
        form.action = urlExcel;
        form.submit();
    }


    function load_categorybase_customer() {
        
        var customerCategoryID = $('#customerCategoryID').val();
        var status_filter = $('#status_filter').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerCategoryID: customerCategoryID,type:type,activeStatus:status_filter},
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
</script>
