<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = 'Invoice Overdue Report';
echo head_page($title, false);
$date_format_policy = date_format_policy();

$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$current_date = current_format_date();
$customer_arr = all_customer_drop(FALSE,1);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('', ' name="invoice_overdue_report_filter_frm" id="invoice_overdue_report_filter_frm" class="form-group" role="form"'); ?>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label for="datefrom"><?php echo $this->lang->line('common_date_from'); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="datefrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="dateto"><?php echo $this->lang->line('common_date_to'); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                </div>
            </div>
            <div class="col-sm-3">
                <label for="status_filter_customer"><?php echo $this->lang->line('common_status');?></label>
                <?php echo form_dropdown('status_filter_customer', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter_customer" onchange="load_statusbased_customer()" '); ?>
            </div>
            <div class="form-group col-sm-3">
                <label class="col-md-4 control-label text-left" for="customerAutoID"><?php echo $this->lang->line('common_customer'); ?></label><br>
                <?php //echo form_dropdown('customerAutoID[]', $customer_arr, '', 'class="form-control" id="customerAutoID" onchange="get_invoice_overdue_report()" multiple="multiple"');  ?>
                <div id="div_load_customers">
                    <select name="customerAutoID[]" class="form-control customerAutoID" id="customerAutoID" multiple="multiple" onchange="get_invoice_overdue_report()" >
                    <?
                        if (!empty($customer_arr)) {
                            foreach ($customer_arr as $key => $val) {
                                echo '<option value="' . $key . '">' . $val . '</option>';
                            }
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label class="col-md-4 control-label text-left" for="currency"><?php echo $this->lang->line('common_currency'); ?></label><br>
                <?php echo form_dropdown('currency', array('transactionAmount' => 'Transaction Currency', 'companyLocalAmount' => 'Local Currency', 'companyReportingAmount' => 'Reporting Currency'), '', 'class="form-control select2" id="currency" onchange="get_invoice_overdue_report()"');  ?>
            </div>
        </div>
        <div class="col-md-12">

            <div class="form-group col-sm-2">
                <label for=""></label>
                <button style="margin-top:28px " type="button" onclick="get_invoice_overdue_report()" class="btn btn-primary btn-xs">
                    Generate
                </button>
            </div>
        </div>
</div>
<?php echo form_close(); ?>
</fieldset>
</div>

<div id="Load_invoice_overdue_report"></div>


<div class="modal fade" id="invoice_overdue_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Invoice Overdue Report - Drill-down<span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="reportDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/accounts_receivable/report/erp_invoice_overdue_report', '', 'Invoice Overdue Report');
        });
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('#customerAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '220px',
            maxHeight: '30px'
        });
        $("#customerAutoID").multiselect2('selectAll', false);
        $("#customerAutoID").multiselect2('updateButtonText');

        $('.select2').select2();
        get_invoice_overdue_report();
    });


    function get_invoice_overdue_report()
    {
        var data = $("#invoice_overdue_report_filter_frm").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('Receivable/Load_invoice_overdue_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Load_invoice_overdue_report').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function invoice_overdue_drilldown(invoiceAutoID)
    {
        var data = $("#invoice_overdue_report_filter_frm").serializeArray();
        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('Receivable/Load_invoice_overdue_drilldown_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#reportDrilldown').html(data);
                $('#invoice_overdue_report_drilldown_modal').modal("show");
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('invoice_overdue_report_filter_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Receivable/Load_invoice_overdue_report_pdf'); ?>';
        form.submit();
    }

    function load_statusbased_customer() {
        var status_filter = $('#status_filter_customer').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {type:1,activeStatus:status_filter},
            url: "<?php echo site_url('Report/load_statusbased_customer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_customers').html(data);
               
                $('#customerAutoID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    maxHeight: '30px',
                    allSelectedText: 'All Selected'
                });
                $("#customerAutoID").multiselect2('selectAll', false);
                $("#customerAutoID").multiselect2('updateButtonText');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>