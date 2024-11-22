<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_social_insurance_report');
echo head_page($title, false);

$currency_arr = all_currency_new_drop();

?>
<style>

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
            <label for="inputCodforn" class="col-md-1 control-label"><?php echo $this->lang->line('common_period');?></label>
            <div class="col-md-3">
                <?php echo form_dropdown('payroll_period', payrollMonth_dropDown(), '', 'class="form-control select2" id="payroll_period" required'); ?>
            </div>
            <label for="inputData" class="col-md-2 control-label"><?php echo $this->lang->line('common_currency');?></label>
            <div class="col-md-2">
                <?php echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID" required'); ?>
            </div>

            <button style="margin-top: 5px" type="button" onclick="load_report()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_load');?></button>
            <button style="margin-top: 5px" type="button" onclick="generateReportExcel()" class="btn btn-success btn-xs">Excel</button>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>

<hr style="margin: 0px;">


<div id="div_ajax_response" style="margin-top: 15px"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $('#payroll_period, #currencyID').select2();

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/social-insurance-report','','Social Insurance Report')
    });

    $(document).ready(function (e) {
        //load_report();
    });

    function load_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_social_insurance_report') ?>",
            data: $("#frm-rpt").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    $("#div_ajax_response").html(data['view']);
                }
                else{
                    myAlert(data[0], data[1])
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportExcel() {
        var msg = '';
        if( $('#payroll_period').val() == '' ){
            msg += 'Period field is required<br/>';
        }

        if( $('#currencyID').val() == '' ){
            msg += 'Currency field is required';
        }

        if(msg != ''){
            myAlert('e', msg);
            return false;
        }

        var form = document.getElementById('frm-rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_social_insurance_report/excel') ?>';
        form.submit();
    }
</script>

<?php
