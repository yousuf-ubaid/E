<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('treasury', $primaryLanguage);
$title = $this->lang->line('treasury_bank_reconciliation_report');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$bank_arr = company_bank_account_drop(0,1);
echo head_page($title, false);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_bank_reconciliation" id="frm_rpt_bank_reconciliation" class="form-group" role="form"'); ?>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="datefrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $start_date ?>" id="datefrom" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="bank">
                    <?php echo $this->lang->line('common_bank'); ?><!--Bank -->
                </label>
                <?php echo form_dropdown('GLAutoID', $bank_arr, '', 'class="form-control select2" id="GLAutoID" required'); ?>
            </div>
            <div class="form-group col-sm-1 pull-right">
                <label for=""></label>
                <button style="margin-top: 5px" type="button" onclick="get_bank_reconciliation_report()"
                        class="btn btn-primary btn-xs">
                    <?php echo $this->lang->line('common_generate'); ?><!-- Generate--></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_bank_rec">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        // get_bank_reconciliation_report();
    });

    $('.headerclose').click(function () {
        fetchPage('system/bank_rec/erp_bank_reconciliation_report', '', '<?php echo $this->lang->line('treasury_bank_reconciliation_report'); ?>')
    });

    function get_bank_reconciliation_report() {
        var data = $("#frm_rpt_bank_reconciliation").serialize();
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('Bank_rec/get_bank_reconciliation_report'); ?>',
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_bank_rec").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_bank_reconciliation');
        form.target = '_blank';
        form.action = '<?php echo site_url('Bank_rec/get_bank_reconciliation_report_pdf'); ?>';
        form.submit();
    }
</script>