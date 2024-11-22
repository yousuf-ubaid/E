<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title =$this->lang->line('common_employee_details');

echo head_page($title, false);

$columns = fetch_employee_details_columns();
$employee_list = employee_list_by_segment(1, 0);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>

<style type="text/css">
    .bgc{
        background-color: #e1f1e1;
    }
    #div_employee .btn-group, #div_employee .btn-group .multiselect2{
        width: 100% !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">

<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_search');?></legend>
        <form class="form-horizontal" id="frm_rpt" role="form" method="POST">
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="inputCodforn" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_column');?><!--Column--></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <?php echo form_dropdown('columns[]', $columns, '', ' class="form-control" id="columns" multiple="multiple"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_segment');?><!--Segment--></label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <?php echo form_dropdown('segmentID[]', fetch_segment(true, false), '', ' onchange="loadEmployees()" class="form-control" id="segmentID"  multiple="multiple"'); ?>
                </div>
                <div class="visible-sm visible-xs clearfix">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label">
                    <?php echo $this->lang->line('common_status');?><!---->
                </label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" id="">
                    <select name="isDischarged" id="isDischarged" class="form-control select2" onchange="loadEmployees()">
                        <option value="">All</option>
                        <option value="1">Discharged</option>
                        <option value="0" selected="selected">Active</option>
                    </select>
                </div>

                <div class="visible-sm visible-xs clearfix">&nbsp;</div>

                <label for="inputData" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label">
                    <?php echo $this->lang->line('hrms_reports_employee');?><!--Employee-->
                </label>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" id="div_employee">
                    <?php echo form_dropdown('empID[]', $employee_list, '', ' class="form-control" id="empID" multiple="multiple"'); ?>
                </div>

                <label for="inputData" class="col-md-1 control-label"></label>
                <div class="col-lg-12">
                    <button type="button" onclick="get_report()" class="btn btn-xs btn-primary pull-right">
                        <?php echo $this->lang->line('common_submit');?><!--Submit-->
                    </button>
                </div>
            </div>
        </form>
    </fieldset>
</div>

<div id="response-div"></div>

<script type="text/javascript">
    var empDrop = $('#empID');
    var columnsDrop = $('#columns');
    $('.select2').select2();


    columnsDrop.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        enableFiltering: true,
        maxHeight: 200,
        numberDisplayed: 1,
        buttonWidth: '150px'
    });

    columnsDrop.multiselect2('selectAll', false);
    columnsDrop.multiselect2('updateButtonText');

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

    $('#segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#segmentID').multiselect2('selectAll', false);
    $('#segmentID').multiselect2('updateButtonText');

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/employee_details_report', 'Test', 'HRMS');
        });
    });

    function loadEmployees(){
        var segmentID  = $('#segmentID').val();
        var status  = $('#isDischarged').val();

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
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });
                    empDrop.multiselect2('selectAll', false);
                    empDrop.multiselect2('updateButtonText');

                    $("#div_paySlips").html('');
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

                    $("#div_paySlips").html(data[1]);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_report(){
        var postData = $('#frm_rpt').serializeArray();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_details_report') ?>",
            data: postData,
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $("#response-div").html(data[1]);
                }
                else{
                    myAlert(data[0], data[1]);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function print_btn() {
        var form= document.getElementById('frm_rpt');
        form.target='_blank';
        form.method='post';
        form.action='<?php echo site_url('Employee/employee_details_report/'); ?>Print/Employee-details';
        form.submit();
    }

    function excel_btn(){
        var form= document.getElementById('frm_rpt');
        form.target='_blank';
        form.method='post';
        form.action='<?php echo site_url('Employee/employee_details_report/'); ?>Excel/Employee-details';
        form.submit();
    }
</script>
<?php
