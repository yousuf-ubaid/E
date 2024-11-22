<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$title = $this->lang->line('sales_markating_sales_order_report');
$date_format_policy = date_format_policy();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment(true,false);
$from = convert_date_format(current_date());
$customer_category_arr=all_customer_category_report_drop();
$customer = all_customer_drop(false,1);
$customergrp = all_group_customer_drop(false);
echo head_page($this->lang->line('accounts_receivable_rs_cad_customer_balance_summary'), false);
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
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_customer_balance_summary" id="frm_rpt_customer_balance_summary" class="form-group" role="form"'); ?>
        <input type="hidden" id="grouptyp" name="grouptyp">
        <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_currency'); ?></label>
                    <select name="currency" class="form-control " id="currency" onchange="get_customer_balance_report()" tabindex="-1" aria-hidden="true" data-bv-field="currency">
                        <!--<option value="1">Transaction Currency</option>-->
                        <option value="1">Local/Reporting Currency</option>
                        <option value="2">Transaction Currency</option>
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_as_of_date'); ?></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="from"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $from; ?>" id="from" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2 ntgrpfltr">
                    <label><?php echo $this->lang->line('common_customer_category'); ?> <!--Customer Category--> </label>
                    <?php // if ($type == 1) {
                    echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID"  multiple="multiple"');
                    //}
                    ?>
                </div>
                <div class="form-group col-sm-2 ntgrpfltr">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
                </div>
                <div class="form-group col-sm-2 ntgrpfltr">
                    <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                    <?php // echo form_dropdown('customerID[]', $customer, '', 'multiple  class="form-control" id="customerID" required'); ?>
                    <div id="div_load_customers">
                        <select name="customerID[]" class="form-control" id="filter_customerID" multiple="">
                            <?php

                            if (!empty($customer)) {
                                foreach ($customer as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-2 grpfltr">
                    <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                    <?php // echo form_dropdown('customerID[]', $customer, '', 'multiple  class="form-control" id="customerID" required'); ?>
                    <div id="div_load_customers">
                        <select name="customerIDgrp[]" class="form-control" id="filter_customerIDgrp" multiple="">
                            <?php

                            if (!empty($customergrp)) {
                                foreach ($customergrp as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>
                </div>


                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <button style="margin-top: 5px" type="button" onclick="get_customer_balance_report()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?></button>
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
                        <th><?php echo $this->lang->line('common_document_code'); ?></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?></th>
                        <th><?php echo $this->lang->line('common_currency'); ?></th>
                        <th><?php echo $this->lang->line('common_amount'); ?></th>
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
                <h4 class="modal-title" id="myModalLabel"> <?php echo $this->lang->line('accounts_receivable_rs_cad_revenue_summary_drill_down'); ?></h4>
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

    $('#filter_customerIDgrp').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#filter_customerIDgrp").multiselect2('selectAll', false);
    $("#filter_customerIDgrp").multiselect2('updateButtonText');

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

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    $('.headerclose').click(function () {
        fetchPage('system/accounts_receivable/report/erp_customer_balance_report', '', '<?php echo $this->lang->line('accounts_receivable_rs_cad_customer_balance_summary'); ?>')
    });
    $(document).ready(function (e) {
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            $('#grouptyp').val(type);
            $('.grpfltr').hide();
            $('.ntgrpfltr').show();
        }else{
            $('#grouptyp').val(type);
            $('.ntgrpfltr').hide();
            $('.grpfltr').show();
        }



        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];
        if(type == 1){
            url = '<?php echo site_url('Report/get_customer_balance_report'); ?>';
            urlPdf = '<?php echo site_url('Report/get_customer_balance_report_pdf'); ?>';
        }else{
            url = '<?php echo site_url('Report/get_customer_balance_report_group'); ?>';
            urlPdf = '<?php echo site_url('Report/get_customer_balance_report_pdf_group'); ?>';
        }


        get_customer_balance_report();
    });

    function get_customer_balance_report() {
        var data = $("#frm_rpt_customer_balance_summary").serialize();
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
                applyAlternateColor();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_customer_balance_summary');
        form.target = '_blank';
        form.action =urlPdf;
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

    function applyAlternateColor() {
        const rows = document.querySelectorAll("#tbl_rpt_salesorder tbody tr");
        let toggleClass = false;

        rows.forEach(function(row) {
            if (row.classList.contains("hoverTr")) {
                toggleClass = !toggleClass;
                row.style.backgroundColor = toggleClass ? "#efeffc" : "";
            } else {
                row.style.backgroundColor = "";
                toggleClass = false;
            }
        });
    }
</script>
