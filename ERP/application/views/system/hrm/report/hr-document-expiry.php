<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_document_expiry_report');
echo head_page($title, false);

$date_format_policy = date_format_policy();

$current_date = current_format_date();
$yearEnd = convert_date_format( date('Y-12-31') );
$segment_arr = fetch_segment(true,false);
$employee_list = employee_list_by_segment(1, 0);

$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

$rpt_type = [
        'E' => $this->lang->line('common_employee'),
        'D' => $this->lang->line('common_dependents'),
];
?>

<style>
    .select-container .btn-group{ width: 150px !important; } /*Segment, Employee drop down style*/

    fieldset{
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 0px;
        margin:auto;
        padding-bottom: 10px;
    }
    legend{
        width:auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 14px;
        font-weight: 500;
    }
</style>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><? echo $this->lang->line('common_filter')?></legend>
    <form role="form" id="frm_rpt" class="form-horizontal" autocomplete="off">
        <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
        <div class="row">
            <div class="col-sm-12">
                <label class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label" for="rpt_type"><?=$this->lang->line('common_filter')?></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 select-container">
                    <?php echo form_dropdown('rpt_type', $rpt_type, '', 'class="form-control" id="rpt_type"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label" for="segmentID"><? echo $this->lang->line('common_segment')?></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 select-container" id="segment-container">
                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple" onchange="loadEmployees()"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-1 col-sm-1 col-xs-6 control-label">
                    <?php echo $this->lang->line('hrms_reports_employee');?><!--Employee-->
                </label>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 select-container" id="div_employee">
                    <?php echo form_dropdown('empID[]', $employee_list, '', ' class="form-control" id="empID" multiple="multiple" onchange="dateFilterProp()"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="fromDate" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_from_date');?></label>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <div class="form-group">
                        <div class="input-group filterDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="fromDate" value="<?php echo $current_date; ?>"
                                   id="fromDate" class="form-control date-input" onchange=""
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                        </div>
                    </div>
                </div>
                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="toDate" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_to_date');?></label>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <div class="form-group">
                        <div class="input-group filterDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="toDate" value="<?php echo $yearEnd; ?>"
                                   id="toDate" class="form-control date-input" onchange=""
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" >
                        </div>
                    </div>
                </div>

                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox" name="expired" id="expired" value="1" checked="">
                        </span>
                        <input type="text" class="form-control" disabled="" value="<?= $this->lang->line('common_expired');?>">
                    </div>
                </div>

                <div class="visible-sm visible-xs clearfix col-sm-12 col-xs-12">&nbsp;</div>

                <label for="inputData" class="col-sm-1 control-label"></label>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="form-btn-group">
                    <!--<button type="button" onclick="get_report(2)" class="btn btn-success btn-xs pull-right">
                        <i class="fa fa-file-excel-o"></i> To Excel
                    </button>-->
                    <button type="button" class="btn btn-danger btn-xs pull-right" style="margin-right: 10px" onclick="get_report(1)">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px" onclick="get_report(0)" >Generate</button>
                </div>
            </div>
        </div>
    </form>
</fieldset>

<div id="ajax-response"></div>

<script type="text/javascript">

    $('#category').select2();
    let date_input = $('.date-input');
    let ajax_repDiv = $("#ajax-response");

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/report/hr-document-expiry','','HRMS');
        });

        setTimeout(function(){
            get_report();
        }, 300);
    });

    let empDrop = $('#empID');
    let segmentDrop = $('#segmentID');

    Inputmask().mask(document.querySelectorAll("input"));
    let date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

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

    function loadEmployees(){
        let segmentID  = segmentDrop.val();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_list_by_segment') ?>",
            data: {segmentID:segmentID, 'status':0, 'isContractRpt':1},
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
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });

                    empDrop.attr('onchange', 'dateFilterProp()');

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

    function get_report(returnType){
        let postData = $('#frm_rpt').serializeArray();
        postData.push({'name':'returnType', 'value':returnType});

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/get_hr_document_expiry_details/Y') ?>",
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
                    if(returnType == 1){
                        print_btn();
                    }
                    else if(returnType == 2){
                        download_in_excel();
                    }
                    else{
                        ajax_repDiv.html(data['view']);
                    }
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
        let form = document.getElementById('frm_rpt');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('Employee/get_hr_document_expiry_details/N'); ?>/Print/Document-Expiry-Report';
        form.submit();
    }

    function download_in_excel() {
        let form = document.getElementById('frm_rpt');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('Employee/get_hr_document_expiry_details_excel'); ?>/Document-Expiry-Report';
        form.submit();
    }

    function dateFilterProp(){
        let empList = empDrop.val();
        date_input.prop('disabled',  (empList !== null));
    }
</script>
