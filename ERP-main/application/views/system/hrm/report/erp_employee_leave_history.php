<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('hrms_reports', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_leave_history');
$date_format_policy = date_format_policy();
$leave_types = leavemaster_dropdown();
unset($leave_types['']); //remove first option (select)
$year_first = convert_date_format(date('Y-01-01'));
$current_date = current_format_date();

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

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#leavehistory" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('hrms_reports_leave_history'); ?><!--Leave History--></a></li>
        <li class=""><a href="#onleave" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('hrms_reports_on_leave'); ?><!--On Leave--></a></li>

    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="leavehistory">
            <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' id="frm_rpt_leave_history" class="form-group" role="form" autocomplete="off"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="date_from" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $year_first ?>" id="date_from" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                                <div class="input-group datepicto">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="date_to" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date ?>" id="date_to" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('hrms_reports_employee_leave_type'); ?><!--Leave Type--></label>
                                <?php echo form_dropdown('leaveTypeID[]', $leave_types, '', 'class="form-control" id="leaveTypeID" required multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_segment'); ?><!--Segment--></label>
                                <?php echo form_dropdown('segmentID[]', fetch_segment(true,false), '', ' onchange="loadEmployees()" class="form-control" multiple="multiple" id="segmentID" required'); ?>
                            </div>
                            <div class="form-group col-sm-3" >
                                <label for=""><?php echo $this->lang->line('common_employee'); ?><!--Employee--></label>
                                <div id="div_employee">
                                    <select name="empID[]" id="empID" class="form-control" multiple="multiple"  required></select>
                                </div>
                            </div>

                            <div class="form-group col-sm-1">
                                <!--<label for=""></label>-->
                                <button style="margin-top: 25px" type="button" onclick="get_leave_history()" class="btn btn-primary btn-xs">
                                    <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </fieldset>
            </div>
            <hr style="margin: 0px;">
            <div id="div_leave_history">
            </div>
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="onleave">
            <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' id="frm_rpt_onleave" class="form-group" role="form" autocomplete="off"'); ?>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_as_of_date'); ?><!--As of Date--></label>
                                <div class="input-group datepicto">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="current_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date ?>" id="current_date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('hrms_reports_employee_leave_type'); ?><!--Leave Type--></label>
                                <?php echo form_dropdown('leaveTypeID_ol[]', $leave_types, '', 'class="form-control" id="leaveTypeID_ol" required multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for=""><?php echo $this->lang->line('common_segment'); ?><!--Segment--></label>
                                <?php echo form_dropdown('segmentID_ol[]', fetch_segment(true,false), '', ' onchange="loadEmployees_ol()" class="form-control" multiple="multiple" id="segmentID_ol" required'); ?>
                            </div>
                            <div class="form-group col-sm-3" >
                                <label for=""><?php echo $this->lang->line('common_employee'); ?><!--Employee--></label>
                                <div id="div_employee_ol">
                                    <select name="empID_ol[]" id="empID_ol" class="form-control" multiple="multiple"  required></select>
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                <!--<label for=""></label>-->
                                <button style="margin-top: 25px" type="button" onclick="get_onleave_history()" class="btn btn-primary btn-xs">
                                    <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </fieldset>
                </div>
                <hr style="margin: 0px;">
            <div id="div_onleave">
            </div>
        </div>
        <!-- /.tab-pane -->

    </div>
    <!-- /.tab-content -->
</div>




<div class="modal fade" id="returndrilldownModal" role="dialog" aria-labelledby="myModalLabel">
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
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
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

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {
        $('.select2').select2();

        $('#empID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            maxHeight: 200,
            numberDisplayed: 2,
            buttonWidth: '180px'
        });
        $("#empID").multiselect2('selectAll', false);
        $("#empID").multiselect2('updateButtonText');

        $('#segmentID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 2,
            buttonWidth: '150px'
        });
        $("#segmentID").multiselect2('selectAll', false);
        $("#segmentID").multiselect2('updateButtonText');

        $('#leaveTypeID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 2,
            buttonWidth: '150px'
        });
        $("#leaveTypeID").multiselect2('selectAll', false);
        $("#leaveTypeID").multiselect2('updateButtonText');

        $('#empID_ol').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            maxHeight: 200,
            numberDisplayed: 2,
            buttonWidth: '180px'
        });
        $("#empID_ol").multiselect2('selectAll', false);
        $("#empID_ol").multiselect2('updateButtonText');

        $('#segmentID_ol').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 2,
            buttonWidth: '150px'
        });
        $("#segmentID_ol").multiselect2('selectAll', false);
        $("#segmentID_ol").multiselect2('updateButtonText');

        $('#leaveTypeID_ol').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 2,
            buttonWidth: '150px'
        });
        $("#leaveTypeID_ol").multiselect2('selectAll', false);
        $("#leaveTypeID_ol").multiselect2('updateButtonText');

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/erp_employee_leave_history', '', 'Leave History')
        });
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
        loadEmployees();
        loadEmployees_ol();
        //get_leave_history();
    });

    function get_leave_history() {
        var data = $("#frm_rpt_leave_history").serialize();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/get_leave_history_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_leave_history").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_onleave_history() {
        var data = $("#frm_rpt_onleave").serialize();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/get_onleave_history_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_onleave").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_leave_history');
        form.target = '_blank';
        form.action = '<?php echo site_url('Template_paysheet/get_leave_history_report_pdf'); ?>';
        form.submit();
    }

    function generateReportPdf_ol() {
        var form = document.getElementById('frm_rpt_onleave');
        form.target = '_blank';
        form.action = '<?php echo site_url('Template_paysheet/get_onleave_history_report_pdf'); ?>';
        form.submit();
    }

    function openreturnDD(invoiceAutoID){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('sales/get_sales_order_return_drilldown_report') ?>",
            data: {'invoiceAutoID': invoiceAutoID},
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#salesreturn').empty();
                $('#salesreturnfooter').empty();
                if (jQuery.isEmptyObject(data)) {
                    $('#salesreturn').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                } else {
                    tot_amount = 0;
                    var currency;
                    var amount;
                    var decimalPlaces=2;
                    var total=0;
                    $.each(data, function (key, value) {
                        if($('#currency').val()==1){
                            currency=value['transactionCurrency'];
                            amount=value['totalValue']/value['transactionExchangeRate'];
                            decimalPlaces=value['transactionCurrencyDecimalPlaces'];
                        }else if($('#currency').val()==2){
                            currency=value['companyLocalCurrency'];
                            amount=value['totalValue']/value['companyLocalExchangeRate'];
                            decimalPlaces=value['companyLocalCurrencyDecimalPlaces'];
                        }else{
                            currency=value['companyReportingCurrency'];
                            amount=value['totalValue']/value['companyReportingExchangeRate'];
                            decimalPlaces=value['companyReportingCurrencyDecimalPlaces'];
                        }
                        total += amount;
                        $('#salesreturn').append('<tr><td><a href="#" class="" onclick="documentPageView_modal(\'SLR\' , ' + value["salesReturnAutoID"] + ')">' + value["salesReturnCode"] + '</a></td><td>' + value["returnDate"] + '</td><td >' + currency + '</td><td class="text-right">' + parseFloat(amount).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                    });
                    $('#salesreturnfooter').append('<tr><td colspan="3" >&nbsp;</td> <td class="text-right reporttotal" style="font-weight: bold;">' + parseFloat(total).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                }
                $('#returndrilldownModal').modal('show');
                $('.drilldown-title').html("Sales Return Drill Down");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }



    function openrecreditDD(invoiceAutoID){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('sales/get_sales_order_credit_drilldown_report') ?>",
            data: {'invoiceAutoID': invoiceAutoID},
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#salesreturn').empty();
                $('#salesreturnfooter').empty();
                if (jQuery.isEmptyObject(data)) {
                    $('#salesreturn').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                } else {
                    tot_amount = 0;
                    var currency;
                    var amount;
                    var decimalPlaces=2;
                    var total=0;
                    $.each(data, function (key, value) {
                        if($('#currency').val()==1){
                            currency=value['transactionCurrency'];
                            amount=value['transactionAmount'];
                            decimalPlaces=value['transactionCurrencyDecimalPlaces'];
                        }else if($('#currency').val()==2){
                            currency=value['companyLocalCurrency'];
                            amount=value['companyLocalAmount'];
                            decimalPlaces=value['companyLocalCurrencyDecimalPlaces'];
                        }else{
                            currency=value['companyReportingCurrency'];
                            amount=value['companyReportingAmount'];
                            decimalPlaces=value['companyReportingCurrencyDecimalPlaces'];
                        }
                        //alert(amount);
                        total += parseFloat(amount);
                        $('#salesreturn').append('<tr><td><a href="#" class="" onclick="documentPageView_modal(\'' + value["docID"] + '\' , ' + value["masterID"] + ')">' + value["documentCode"] + '</a></td><td>' + value["documentDate"] + '</td><td >' + currency + '</td><td class="text-right">' + parseFloat(amount).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                    });
                    $('#salesreturnfooter').append('<tr><td colspan="3" >&nbsp;</td> <td class="text-right reporttotal" style="font-weight: bold;">' + parseFloat(total).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                }
                $('#returndrilldownModal').modal('show');
                $('.drilldown-title').html("Receipt/Credit Note Drill Down");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadEmployees(){
        var segmentID  = $('#segmentID').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/dropdown_payslipemployees_his_report') ?>",
            data: {segmentID:segmentID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $('#empID').multiselect('refresh');
                    $("#div_employee").html( data[1] );
                    $('#empID').multiselect2({
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });
                    $("#empID").multiselect2('selectAll', false);
                    $("#empID").multiselect2('updateButtonText');

                }
                else{
                    $('#empID').multiselect('refresh');
                    $('#empID').multiselect2({
                        includeSelectAllOption: true,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#empID").multiselect2('updateButtonText');

                    $("#div_paySlips").html(data[1]);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });

    }
    function loadEmployees_ol(){
        var segmentID  = $('#segmentID_ol').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/dropdown_payslipemployees_his_report_ol') ?>",
            data: {segmentID:segmentID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $('#empID_ol').multiselect('refresh');
                    $("#div_employee_ol").html( data[1] );
                    $('#empID_ol').multiselect2({
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });
                    $("#empID_ol").multiselect2('selectAll', false);
                    $("#empID_ol").multiselect2('updateButtonText');

                }
                else{
                    $('#empID_ol').multiselect('refresh');
                    $('#empID_ol').multiselect2({
                        includeSelectAllOption: true,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#empID_ol").multiselect2('updateButtonText');

                    $("#div_paySlips").html(data[1]);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });

    }


</script>
