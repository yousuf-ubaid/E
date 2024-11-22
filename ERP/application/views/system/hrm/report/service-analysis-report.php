<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_service_analysis_report');
echo head_page($title, false);

$date_format_policy = date_format_policy();

$current_date = current_format_date();
$yearFirst = convert_date_format( date('Y-01-01') );
$segment_arr = fetch_segment(true,false);
$employee_list = employee_list_by_segment(1);
$categoryDrop = [
    'all' => 'All', 'join' => 'Join', 'discharged' => 'Discharged'
];

$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
?>

<style>
    .select-container .btn-group{ width: 150px !important; } /*Segment, Employee drop down style*/
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">


<fieldset class="scheduler-border">
    <legend class="scheduler-border"><?php echo $this->lang->line('common_filter');?><!--Filter--></legend>
    <form role="form" id="frm_rpt" class="form-horizontal" autocomplete="off">
        <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
        <div class="row">
            <div class="col-sm-12">
                <label for="category" class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_category');?></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3">
                    <?php echo form_dropdown('category', $categoryDrop, '', 'class="form-control" id="category"  onchange="loadEmployees()"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="fromDate" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_from_date');?></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3">
                    <div class="form-group">
                        <div class="input-group filterDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="fromDate" value="<?php echo $yearFirst; ?>"
                                   id="fromDate" class="form-control date-input" onchange=""
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                        </div>
                    </div>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="toDate" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_to_date');?></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3">
                    <div class="form-group">
                        <div class="input-group filterDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="toDate" value="<?php echo $current_date; ?>"
                                   id="toDate" class="form-control date-input" onchange=""
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                        </div>
                    </div>
                </div>
                <div class="clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label" for="segmentID"><?php echo $this->lang->line('common_segment');?><!--Segment--></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 select-container" id="segment-container">
                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple" onchange="loadEmployees()"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label">
                    <?php echo $this->lang->line('hrms_reports_employee');?><!--Employee-->
                </label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 select-container" id="div_employee">
                    <?php echo form_dropdown('empID[]', $employee_list, '', ' class="form-control" id="empID" multiple="multiple"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="inputData" class="col-sm-1 control-label"></label>
                <div class="col-sm-3 col-xs-12" id="form-btn-group">
                    <button type="button" class="btn btn-primary btn-xs" onclick="get_report()">
                        <?php echo $this->lang->line('hrms_reports_employee');?></button>
                    <button type="button" class="btn btn-danger btn-xs " style="" onclick="print_btn()">
                        <i class="fa fa-print"></i>  <?php echo $this->lang->line('common_print');?>
                    </button>
                    <input type="hidden"  id="eventType" value="">
                </div>
            </div>
        </div>
    </form>
</fieldset>

<div id="ajax-response"></div>


<script type="text/javascript">

    $('#category').select2();
    var date_input = $('.date-input');
    var ajax_repDiv = $("#ajax-response");

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/report/service-analysis-report','','HRMS');
        });

        date_input.prop('disabled', true);
    });

    var empDrop = $('#empID');
    var segmentDrop = $('#segmentID');

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.filterDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    segmentDrop.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
    segmentDrop.multiselect2('selectAll', false);
    segmentDrop.multiselect2('updateButtonText');

    empDrop.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        enableFiltering: true,
        maxHeight: 200,
        numberDisplayed: 2,
        buttonWidth: '180px'
    });
    empDrop.multiselect2('selectAll', false);
    empDrop.multiselect2('updateButtonText');

    function loadEmployees(){
        var segmentID  = segmentDrop.val();
        var catVal = $('#category').val();
        var status = '';

        switch (catVal){
            case 'all': date_input.prop('disabled', true);  break;
            case 'join': date_input.prop('disabled', false); break;
            case 'discharged': date_input.prop('disabled', false); status = 1; break;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_list_by_segment') ?>",
            data: {segmentID:segmentID, 'status':status},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){

                    $("#div_employee").html( data[1] );

                    empDrop = $('#empID');

                    empDrop.multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });
                    empDrop.multiselect2('selectAll', false);
                    empDrop.multiselect2('updateButtonText');

                    ajax_repDiv.html('');
                }
                else{
                    empDrop.empty();
                    empDrop.multiselect('refresh');
                    empDrop.multiselect2({
                        includeSelectAllOption: true,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    empDrop.multiselect2('updateButtonText');

                    ajax_repDiv.html(data[1]);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                ajax_repDiv.html('');
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_report(){
        var postData = $('#frm_rpt').serializeArray();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_service_analysis_report') ?>",
            data: postData,
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                ajax_repDiv.html('');

                if(data[0] == 's'){
                    ajax_repDiv.html(data[1]);
                }
                else{
                    myAlert(data[0], data[1]);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                ajax_repDiv.html('');
                myAlert('e', '' + errorThrown);
            }
        });
    }

    function print_btn() {
        var form= document.getElementById('frm_rpt');
        form.target='_blank';
        form.method='post';
        form.action='<?php echo site_url('Employee/employee_service_analysis_report'); ?>/Print/Service-Analysis-Report';
        form.submit();
    }
</script>
<?php
