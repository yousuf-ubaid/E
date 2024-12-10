<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-m-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="commission_analysis_rpt" id="commission_analysis_rpt" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_type'); ?><!--type--></label>
                    <select name="commissionAnalysisType" class="form-control " id="commissionAnalysisType" onchange="get_commission_analysis_report()" tabindex="-1" aria-hidden="true" >
                        <option value="1" selected>Name Wise Commission Report</option>
                        <option value="2" >Designation Wise Commission Report</option>
                        <option value="3" >Name & Designation Wise Report</option>
                    </select>
                </div>
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
                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <div class="row">
                        <div class="col-sm-12">
                            <button style="margin-top: 10%" type="button" onclick="get_commission_analysis_report()"
                                    class="btn btn-primary btn-xs">
                                <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_commission_analysis">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var type;

    $(document).ready(function (e) {


        $('.headerclose').click(function () {
            fetchPage('system/sales/commission_analysis_report', '', 'Commission Analysis Report')
        });

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        get_commission_analysis_report();

    });

    function get_commission_analysis_report() {
        var data = $("#commission_analysis_rpt").serialize();
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('CommissionScheme/get_commission_analysis_report'); ?>',
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_commission_analysis").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    /*function generateReportPdf() {
        var form = document.getElementById('commission_analysis_rpt');
        form.target = '_blank';
        form.action = '<?php //echo site_url('Sales/get_sales_person_performance_report_pdf'); ?>';
        form.submit();
    }*/


</script>
