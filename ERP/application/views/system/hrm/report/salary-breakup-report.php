<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_salary_breakup_report');
echo head_page($title, false);

$from_day = date('Y-01');
$to_day = date('Y-12');
?>
<style>
    .bgc{
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">

<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_search');?></legend>
        <?php echo form_open('#', ' class="form-horizontal" id="frm_rpt" role="form" autocomplete="off"'); ?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="inputCodforn" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?= $this->lang->line('common_from');?><!--Month--></label>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                <div class="input-group date_pic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="fromDate" value="<?=$from_day;?>" id="fromDate" class="form-control filterDate" style="width: 75px;">
                </div>
            </div>
            <div class="visible-sm visible-xs clearfix">&nbsp;</div>

            <label for="inputCodforn" class="col-lg-1 col-md-2 col-sm-2 col-xs-6 control-label"><?= $this->lang->line('common_to');?></label>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                <div class="input-group date_pic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="toDate" value="<?=$to_day;?>" id="toDate" class="form-control filterDate" style="width: 75px;">
                </div>
            </div>
            <div class="visible-sm visible-xs clearfix">&nbsp;</div>

            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                <button type="button" onclick="generateReportExcel()" class="btn btn-xs btn-primary pull-right">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>

                <button type="button" onclick="load_report()" class="btn btn-xs btn-primary pull-right" style="margin-right: 10px">
                    <i class="fa fa-eye"></i> <?php echo $this->lang->line('common_view');?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<br>

<div id="div-rpt"></div>


<?php echo footer_page('Right foot','Left foot',false); ?>

<script>
    $('.filterDate').datepicker({
        format: 'yyyy-mm',
        viewMode: "months",
        minViewMode: "months"
    }).on('changeDate', function (ev) {
        $(this).datepicker('hide');
    });

    $(document).ready(function (e) {
        $('.select2').select2();

        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/salary-breakup-report', '', 'Salary Breakup Report');
        });
    });

    function load_report() {
        let post_data = $("#frm_rpt").serializeArray();
        post_data.push({'name':'reqType', 'value': 'v'});
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Template_paysheet/salary_breakup_report') ?>",
            data: post_data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div-rpt").html(data);
                $("#pay-slip-report").tableHeadFixer({
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

    function generateReportExcel() {
        let form = document.getElementById('frm_rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Template_paysheet/salary_breakup_report'); ?>';
        form.submit();
    }

</script>