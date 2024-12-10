<?php
echo head_page('Donor Commitment Status', false);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
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
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_sales_order" id="frm_rpt_sales_order" class="form-horizontal" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-4" style="margin-bottom: 0px">
                    <label class="col-md-3 control-label text-left"
                           for="employeeID">As of</label>

                    <div class="form-group col-md-8">
                        <div class='input-group date filterDate' id="">
                            <input type="text" name="from" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo current_format_date() ?>" id="as_date"
                                   class="form-control " required>
                                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-4" style="margin-bottom: 0px">
                    <label class="col-md-4 control-label text-left"
                           for="employeeID">Donors</label>
                    <div class="form-group col-md-8">
                        <?php echo form_dropdown('contactID[]', fetch_ngo_donors_drop(false), '', 'multiple  class="form-control" id="contactID" required'); ?>
                    </div>
                </div>
                <div class="form-group col-sm-3" style="margin-bottom: 0px">
                    <label class="col-md-4 control-label text-left"
                           for="employeeID">Type</label>
                    <div class="form-group col-md-8">
                        <?php echo form_dropdown('reportType', array("" => "Select",1 => "Summary",2 => "Detail"), "1", 'class="form-control" id="reportType" disabled');  ?>
                    </div>
                </div>
                <div class="form-group col-sm-1" style="margin-bottom: 0px;">
                    <button type="button" class="btn btn-primary pull-left" onclick="generateReport()" name="filtersubmit" id="filtersubmit"><i class="fa fa-plus"></i> Generate
                    </button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_sales_order">
</div>
<div class="modal fade" id="drilldownModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="sales_order_drilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.filterDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    });

    $('#contactID').multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#contactID").multiselect2('selectAll', false);
    $("#contactID").multiselect2('updateButtonText');
    $('.headerclose').click(function () {
        fetchPage('system/operationNgo/donor_commitment_status','','Donor Commitment Status')
    });
    $(document).ready(function (e) {
        generateReport();
        //$('.select2').select2();

    });

    function generateReport() {
        $("#reportType").prop("disabled", false);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('OperationNgo/get_donor_commitment_status_report') ?>",
            data: $("#frm_rpt_sales_order").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_sales_order").html(data);
                $("#reportType").prop("disabled", true);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        $("#reportType").prop("disabled", false);
        var form = document.getElementById('frm_rpt_sales_order');
        form.target = '_blank';
        form.action = '<?php echo site_url('OperationNgo/get_donor_commitment_status_report_pdf'); ?>';
        form.submit();
        $("#reportType").prop("disabled", true);
    }

    function drilldownCommitmentReport(donorID, currencyID, type,title) {
        var form = $("#frm_rpt_sales_order").serializeArray();
        form.push({name: 'donorID', value: donorID});
        form.push({name: 'currencyID', value: currencyID});
        form.push({name: 'type', value: type});
        form.push({name: 'from', value: $('#as_date').val()});
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('OperationNgo/get_donor_commitment_drilldown_report') ?>",
            data: form,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#drilldownModal').modal('show');
                $('.drilldown-title').html(title);
                $("#sales_order_drilldown").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

</script>
