<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
$title = $this->lang->line('accounts_payable_vendor_balance_summary');
$date_format_policy = date_format_policy();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment(true,false);
$from = convert_date_format(current_date());
$supplier_arr = all_supplier_drop(false,1);
$suppliergrp = all_group_supplier_drop(false);
$supplierCategory = party_category(2, false);
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
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_vendor_balance_summary" id="frm_rpt_vendor_balance_summary" class="form-group" role="form"'); ?>
        <input type="hidden" id="grouptyp" name="grouptyp">
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_currency'); ?></label>
                    <select name="currency" class="form-control " id="currency" onchange="get_vendor_balance_report()" tabindex="-1" aria-hidden="true" data-bv-field="currency">
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
                    <label for=""><?php echo $this->lang->line('common_supplier') . ' ' . $this->lang->line('common_category'); ?></label>
                        <?php echo form_dropdown('partyCategoryID[]', $supplierCategory, '', 'multiple  class="form-control select2" id="partyCategoryID"'); ?>
                </div>
                <div class="form-group col-sm-2 ntgrpfltr">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
                
                </div>
                <div class="form-group col-sm-2 ntgrpfltr">
                    <label for=""><?php echo $this->lang->line('common_supplier'); ?></label>
                        <?php // echo form_dropdown('supplierID[]', $supplier_arr, '', 'multiple  class="form-control select2" id="supplierID"'); ?>
                        <div id="div_load_supplier">
                        <select name="supplierID[]" class="form-control" id="supplierID" multiple="">
                            <?php
                                if (!empty($supplier_arr)) {
                                    foreach ($supplier_arr as $key => $val) {
                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-2 grpfltr">
                    <label for=""><?php echo $this->lang->line('common_supplier'); ?></label>
                    <?php echo form_dropdown('supplierIDgrp[]', $suppliergrp, '', 'multiple  class="form-control select2" id="supplierIDgrp"'); ?>
                </div>


                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <button style="margin-top: 5px" type="button" onclick="get_vendor_balance_report()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?></button>
                </div>


            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_vendor_invoice">
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
                <h4 class="modal-title" id="myModalLabel"> <?php echo $this->lang->line('accounts_payable_revenue_summary_drill_down'); ?></h4>
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

    $('#supplierID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#supplierID").multiselect2('selectAll', false);
    $("#supplierID").multiselect2('updateButtonText');

    $('#partyCategoryID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#partyCategoryID").multiselect2('selectAll', false);
    $("#partyCategoryID").multiselect2('updateButtonText');
    $("#partyCategoryID").change(function () {
        load_categorybase_supplier();

    });
    $("#status_filter").change(function () {
        load_categorybase_supplier();
    });

    $('#supplierIDgrp').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#supplierIDgrp").multiselect2('selectAll', false);
    $("#supplierIDgrp").multiselect2('updateButtonText');

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    $('.headerclose').click(function () {
        fetchPage('system/accounts_payable/report/erp_vendor_balance_report.php', '', '<?php echo $this->lang->line('accounts_payable_vendor_balance_summary'); ?>')
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

            url = '<?php echo site_url('Report/get_vendor_balance_report'); ?>';
            urlPdf = '<?php echo site_url('Report/get_vendor_balance_report_pdf'); ?>';
     
        }else{
            $('#grouptyp').val(type);
            $('.ntgrpfltr').hide();
            $('.grpfltr').show();
            url = '<?php echo site_url('Report/get_vendor_balance_report_group'); ?>';
            urlPdf =  '<?php echo site_url('Report/get_vendor_balance_report_pdf_group'); ?>';
        }

        get_vendor_balance_report();
    });

    function get_vendor_balance_report() {
        var data = $("#frm_rpt_vendor_balance_summary").serialize();
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
                $("#div_vendor_invoice").html(data);
                applyAlternateColor();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_vendor_balance_summary');
        form.target = '_blank';
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

    function load_categorybase_supplier() {
        var partyCategoryID = $('#partyCategoryID').val();
        var status_filter = $('#status_filter').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {partyCategoryID: partyCategoryID,type:1,status_filter:status_filter},
            url: "<?php echo site_url('Report/fetch_supplierDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_supplier').html(data);
                $('#supplierID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#supplierID").multiselect2('selectAll', false);
                $("#supplierID").multiselect2('updateButtonText');
                //fetch_farm();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function applyAlternateColor() {
        const rows = document.querySelectorAll("#tbl_report tbody tr");
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