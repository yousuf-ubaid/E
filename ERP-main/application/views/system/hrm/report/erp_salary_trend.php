<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_salary_trend_report');
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
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_salary_trend" id="frm_rpt_salary_trend" class="form-horizontal" role="form"'); ?>
            <div class="col-md-12">
                <label for="inputData" class="col-md-1 control-label">
                    <?php echo $this->lang->line('common_year'); ?><!--Year-->:</label>
                <div class="col-md-2">
                    <?php echo form_dropdown('year[]', payrollYear_dropDown(), '', 'multiple  class="form-control select2" id="year" required'); ?>
                </div>
                <button style="margin-top: 5px" type="button" onclick="get_salary_trend()"
                        class="btn btn-primary btn-xs">
                    <?php echo $this->lang->line('common_search'); ?><!--Search--></button>

            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_salary_trend">
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $('#segmentID').multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#year").multiselect2('selectAll', false);
    $("#year").multiselect2('updateButtonText');
    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/erp_salary_trend', '', 'Localization')
    });
    $(document).ready(function (e) {
        get_salary_trend();
    });

    function get_salary_trend() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/get_salary_trend_report') ?>",
            data: $("#frm_rpt_salary_trend").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_salary_trend").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
</script>
