<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('report_helper');
$title = $this->lang->line('accounts_receivable_rs_cad_revenue_collection_summary');
$date_format_policy = date_format_policy();
$financeyear_arr = [];
$segment_arr = [];
$customer = '';
echo head_page($title, false);
$type = $this->session->userdata("companyType");
if ($type == 1) {
    $customer = all_customer_drop(false,1);
    $customer_category_arr = all_customer_category_report_drop();
    $segment_arr = fetch_segment(true,false);
    //$financeyear_arr = all_financeyear_drop(true);
    $financeyear_arr = all_financeyear_report_drop(true);

} else {
    $customer = all_group_customer_drop(false);
    $segment_arr = fetch_group_segment(true,false);
    $financeyear_arr = all_group_financeyear_report_drop(true);
}
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
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_customer_invoice" id="frm_rpt_customer_invoice" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                    <select name="currency" class="form-control " id="currency" onchange="get_collection_summery_report()" tabindex="-1" aria-hidden="true" data-bv-field="currency">
                        <!--<option value="1">Transaction Currency</option>-->
                        <option value="2">Local Currency</option>
                        <option value="3">Reporting Currency</option>
                    </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('common_financial_year'); ?><!--Financial Year--> </label>
                    <?php 
                    if($type==1){
                        echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required');
                    }else{
                        echo form_dropdown('financeyear', $financeyear_arr,'1', 'class="form-control" id="financeyear" required');                        
                    }
                     ?>
                </div>

                <?php if ($type == 1) { ?>
                <div class="form-group col-sm-2">
                    <label> <?php echo $this->lang->line('common_customer_category'); ?><!--Customer Category--> </label>
                    <?php
                    echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID"  multiple="multiple"');
                    ?>
                </div>
                <div class="form-group col-sm-2">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
                
                </div>
                <?php } ?>
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
                    <?php 
                    if($type==1){
                        echo form_dropdown('segment[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'multiple class="form-control select2" id="segment" required'); 
                    }else{
                        echo form_dropdown('segment[]', $segment_arr, '', 'multiple class="form-control select2" id="segment" required'); 
                    }
                    ?>
                </div>

                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <button style="margin-top: 5px" type="button" onclick="get_collection_summery_report()"
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

<div class="modal fade" id="returndrilldownModal" tabindex="2" role="dialog" aria-labelledby="myModalLabel" style="z-index: 10000;">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <table id="tbl_rpt_salesreturn" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Document Code</th>
                        <th>Document Date</th>
                        <th>Currency</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody id="salesreturn">

                    </tbody>
                    <tfoot id="salesreturnfooter" class="table-borded">

                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="sumarydrilldownModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Revenue Summary Drill Down</h4>
            </div>
            <div class="modal-body" id="sumarydd">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var url;
    var urlPdf;

    var typeArr = $('#parentCompanyID option:selected').val();
    typeArr  = typeArr.split('-');
    type = typeArr[1];
    if(type == 1){
        url = '<?php echo site_url('Report/get_collection_summery_report'); ?>';
        urlPdf = '<?php echo site_url('Report/get_collection_summery_report_pdf'); ?>';
    }else{
        url = '<?php echo site_url('Report/get_collection_summery_report_group'); ?>';
        urlPdf = '<?php echo site_url('Report/get_collection_summery_report_group_pdf'); ?>';
    }

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
    $('.headerclose').click(function () {
        fetchPage('system/accounts_receivable/report/erp_collection_summary_report', '', 'Collection Summary')
    });
    $(document).ready(function (e) {
        get_collection_summery_report();

        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    function get_collection_summery_report() {
        var data = $("#frm_rpt_customer_invoice").serialize();
        $.ajax({
            type: "POST",
            //url: "<?php //echo site_url('Report/get_collection_summery_report') ?>",
            url:url,
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
        var form = document.getElementById('frm_rpt_customer_invoice');
        form.target = '_blank';
        //form.action = '<?php //echo site_url('Report/get_collection_summery_report_pdf'); ?>';
        form.action = urlPdf;
        form.submit();
    }


    function opencollectionsummaryDD(date,currency,segment,customerid){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_collection_details_drilldown_report') ?>",
            data: {'date': date,'currency': currency,'customerID': customerid,'segment': segment},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#sumarydd").html(data);
                $('#sumarydrilldownModal').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }


    function opencollectionsummaryPriviousDD(datebegin,dateend,currency,segment,customerid){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_collection_previous_details_drilldown_report') ?>",
            data: {'datebegin': datebegin,'dateend': dateend,'currency': currency,'customerID': customerid,'segment': segment},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#sumarydd").html(data);
                $('#sumarydrilldownModal').modal('show');
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
            data: {customerCategoryID: customerCategoryID,type:1,activeStatus:status_filter},
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