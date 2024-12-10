<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_grade_wise_salary_cost_report');
echo head_page($title, false);
$grade_arr = grade_drop(false);
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
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
        <?php echo form_open('', ' name="frm-rpt" id="frm-rpt" class="form-horizontal" role="form"'); ?>
        <div class="col-md-12">
            <label for="inputCodforn" class="col-md-1 control-label"><?php echo $this->lang->line('common_company');?><!--As Of--></label>
            <div class="col-md-3">
                <input type="text"  value="<?php echo current_companyName() ?>" class="form-control" readonly>
            </div>
            <label for="inputData" class="col-md-2 control-label"><?php echo $this->lang->line('hrms_reports_grade');?>:</label>
            <div class="col-md-2">
                <?php echo form_dropdown('gradeID[]', $grade_arr, '', 'multiple  class="form-control select2" id="gradeID" required'); ?>
            </div>

            <button style="margin-top: 5px" type="button" onclick="load_report()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_load');?></button>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>

<hr style="margin: 0px;">


<div id="div_ajax_response"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $('#gradeID').multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#gradeID").multiselect2('selectAll', false);
    $("#gradeID").multiselect2('updateButtonText');

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/grade-wise-salary-cost','','Grade-wise salary cost')
    });

    $(document).ready(function (e) {
        load_report();
    });

    function load_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_grade_wise_salary_cost_report') ?>",
            data: $("#frm-rpt").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_ajax_response").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
</script>


<?php
