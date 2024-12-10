<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_salary_process_report'); // Salary Process Report
echo head_page($title, false);

?>
<style>
    .bgc{
        background-color: #e1f1e1;
    }

    #div_employee .btn-group, #div_employee .btn-group .multiselect2{
        width: 100% !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_search');?></legend>
        <?php echo form_open('login/loginSubmit', ' class="form-horizontal" id="frm_rpt_salary_process" role="form"'); ?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="inputCodforn" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?php echo $this->lang->line('common_month');?><!--Month--></label>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                <?php echo form_dropdown('payrollMonth', payrollMonth_dropDown(), '', 'class="form-control select2"
                        id="payrollMonth" required'); ?>
            </div>
            <div class="visible-sm visible-xs clearfix">&nbsp;</div>

            <label for="inputData" class="col-lg-2 col-md-2 col-sm-3 col-xs-6 control-label"><?php echo $this->lang->line('common_group_by');?><!--Group By-->:</label>
            <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6">
                <?php echo form_dropdown('groupBy', array(''=>'Select Group By', '1'=>'Segment', '2'=>'Department'), '', ' onchange="fetch_group_by(this.value)" class="form-control select2" id="groupBy" required'); ?>
            </div>
            <div class="visible-sm visible-xs clearfix">&nbsp;</div>

            <label for="segmentID" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label SegmentDiv">
                <?php echo $this->lang->line('common_segment');?><!--Segment-->
            </label>
            <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 f SegmentDiv" id="SegmentDiv">
                <?php echo form_dropdown('segmentID[]', fetch_segment(true, false), '', 'class="form-control" id="segmentID" multiple="multiple" required'); ?>
            </div>

            <label for="inputData" class="col-lg-1 col-md-1 col-sm-2 col-xs-6 control-label departmentDiv">
                <?php echo $this->lang->line('common_department');?><!--Department-->
            </label>
            <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 departmentDiv" id="departmentDiv">
                <?php echo form_dropdown('departmentID[]', fetch_employee_department(false), '', 'class="form-control" id="departmentID" multiple="multiple" required'); ?>
            </div>

            <label for="inputData" class="col-md-1 control-label"></label>
            <div class="col-lg-12">
                <button type="button" onclick="get_salary_process()" class="btn btn-xs btn-primary pull-right">
                    <?php echo $this->lang->line('common_submit');?><!--Submit-->
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<br>

<div id="div_salary_process"></div>




<?php echo footer_page('Right foot','Left foot',false); ?>

<script>
    $(document).ready(function (e) {
        $('.select2').select2();

        $('.departmentDiv').addClass('hide');
        $('.SegmentDiv').addClass('hide');

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/salary_process', '', '<?php echo $this->lang->line('hrms_reports_salary_process_report')?>');
        });

        $("#segmentID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            maxHeight: 400
        });

        $("#departmentID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            maxHeight: 400
        });
    });

    function fetch_group_by(grouping) {
        if(grouping == 1) {
            $('.departmentDiv').addClass('hide');
            $('.SegmentDiv').removeClass('hide');
        } else if(grouping == 2) {
            $('.departmentDiv').removeClass('hide');
            $('.SegmentDiv').addClass('hide');
        } else {
            $('.departmentDiv').addClass('hide');
            $('.SegmentDiv').addClass('hide');
        }
        // $('#segmentID').val('').change();
        // $('#departmentID').val('').change();
    }

    function get_salary_process() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/get_salary_process_report') ?>",
            data: $("#frm_rpt_salary_process").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_salary_process").html(data);
                $("#salary_process_report").tableHeadFixer({
                    head: true,
                    foot: true,
                    left: 5,
                    right: 0,
                    'z-index': 0
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
</script>
