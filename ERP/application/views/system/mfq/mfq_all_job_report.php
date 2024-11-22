<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->lang->line('manufacturing_jobs'), false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-m-01', strtotime($current_date));
$start_date = convert_date_format($startdate);
$segment = fetch_mfq_segment(true, false);
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
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_all_jobs" id="frm_rpt_all_jobs" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="datefrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                    <div class="input-group datepicto">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="dateto"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                    </div>
                </div>

                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('manufacturing_department'); ?><!--Search --></label>
                    <?php echo form_dropdown('filter_DepartmentID[]', $segment,'', 'class="form-control" id="filter_DepartmentID" multiple="multiple"'); ?>
                </div>
                
                <div class="form-group col-sm-2" style="margin-left: 25px;">
                    <label for=""><?php echo $this->lang->line('manufacturing_customer'); ?><!--Search --></label>
                    <?php echo form_dropdown('filter_customerID[]', all_mfq_customer_drop(false), '', 'class="form-control" multiple="multiple" id="filter_customerID"'); ?>
                </div>
                
                <div class="form-group col-sm-2" style="margin-left: 25px;">
                    <label for="">Job Status</label>
                    <?php echo form_dropdown('filter_subJobStatus', array(''=> 'Select Status', 1=>'Open', 2=>'Invoiced', 3=>'Delivered', 4=>'Overdue', 5=>'Closed'), '', 'class="form-control select2" id="filter_subJobStatus"'); ?>
                </div>

            </div>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_search'); ?><!--Search --></label>
                        <input type="text" id="search" name="search" class="form-control">
                </div>
                
                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <button style="margin-top: 30%" type="button" onclick="get_all_jobs_report()" class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?><!--Generate -->
                    </button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>

<hr style="margin: 0px;">
<div id="div_job_report"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    var type;
    var url;
    var urlPdf;
    var urlDrill1;
    var urlDrill2;
    $(document).ready(function (e) {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_all_job_report', '', '<?php echo $this->lang->line('manufacturing_jobs'); ?>')
        });
        $('.select2').select2();
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
        $('#filter_DepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
       
        $('#filter_customerID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });     

        url = '<?php echo site_url('MFQ_Report/get_all_job_report'); ?>';
        urlPdf = '<?php echo site_url('MFQ_Report/get_all_job_report_pdf'); ?>';
        get_all_jobs_report();
    });

    function get_all_jobs_report() {
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frm_rpt_all_jobs").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_job_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_all_jobs');
        form.target = '_blank';
        form.action = urlPdf;
        form.submit();
    }
</script>
