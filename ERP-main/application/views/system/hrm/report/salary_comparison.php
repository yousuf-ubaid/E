<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_salary_comparison');
echo head_page($title, false);

$payrollMonth = payrollMonth_dropDown();

?>
<style>
    tr.highlight td {
        background-color: #B0BED9 !important;
    }

    fieldset{
        border: 1px solid #ddd !important;
        padding: 0 0.5em 0.5em 1.0em !important;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
    }

    legend {
        margin-bottom: 0px;
        font-size: 12px;
        color: #6a6c6f;
        font-weight: bold;
        border-bottom: none;
        text-align: left !important;
        width: auto;
        padding: 0 5px;
    }

    #salaryComparisonDetTB tbody tr:hover td{
        background: #6ab3ca !important;
        cursor: pointer;
        color: #211f1f;
    }

    #salaryComparisonDetTB tbody tr:hover td{
        background: #6ab3ca !important;
        cursor: pointer;
        color: #211f1f;
    }

    .odd_column {
        background-color: #e0e2e6 !important;
    }
</style>


<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active">
            <a href="#summery-tab" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('common_summary'); ?><!--Summery--></a>
        </li>
        <li class="">
            <a href="#detail-tab" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('common_details'); ?><!--Detail--></a>
        </li>
    </ul>

    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="summery-tab">

            <?php
            $formula_arr = get_salaryComparison();

            if (!empty($formula_arr)) {

                echo form_open('', ' role="form" id="reportCreate_form" class="form-horizontal" '); ?>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">
                        <?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <div class="col-md-12">
                        <label for="currentMonth" class="col-md-2 control-label">
                            <?php echo $this->lang->line('hrms_reports_first_month2'); ?><!--First Month--></label>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo form_dropdown('firstMonth', $payrollMonth, '', 'class="form-control select2" id="currentMonth" required'); ?>
                            </div>
                        </div>
                        <label for="lastMonth" class="col-md-2 control-label">
                            <?php echo $this->lang->line('hrms_reports_scond_month2'); ?><!--Second Month--></label>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo form_dropdown('secondMonth', $payrollMonth, '', 'class="form-control select2" id="lastMonth" required'); ?>
                            </div>
                        </div>
                        <label for="inputData" class="col-md-1 control-label"></label>
                        <div class="col-md-3" id="form-btn-group">
                            <button type="button" class="btn btn-primary btn-xs generateBtn" onclick="isGenerateORPrint('Generate')">
                                <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                            </button>
                            <?php echo export_buttons('salaryComparisonTB', 'Salary Comparison', True, True); ?>
                            <!--<button type="button" class="btn btn-primary btn-xs generateBtn" onclick="isGenerateORPrint('Print')">Print</button>-->
                            <?php /*echo export_buttons('salaryComparisonReport', 'ETF Return', true, false, 'btn-xs '); */ ?>
                            <input type="hidden" id="eventType" value="">
                        </div>
                    </div>
                </fieldset>
                <?php echo form_close();

            }
            else {
                ?>
                <div class="alert alert-warning">
                    <strong><?php echo $this->lang->line('hrms_reports_warning'); ?><!--Warning-->!</strong>
                    </br>
                    <?php echo $this->lang->line('hrms_reports_payroll_employee_report_report_configuration_is_not_done'); ?><!--Report configuration is not done.-->
                </div>

                <?php
            }
            ?>

            <div id="salaryComparisonReport" class="cols-sm-12" style="display: none; margin-top: 2%"></div>
        </div>

        <div class="tab-pane" id="detail-tab">

            <?php echo form_open('', ' role="form" id="detail_form" class="form-horizontal" '); ?>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">
                        <?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <div class="col-md-12">
                        <label for="currentMonth" class="col-md-2 control-label">
                            <?php echo $this->lang->line('hrms_reports_first_month2'); ?><!--First Month--></label>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo form_dropdown('firstMonth', $payrollMonth, '', 'class="form-control select2" id="currentMonth2" required'); ?>
                            </div>
                        </div>
                        <label for="lastMonth" class="col-md-2 control-label">
                            <?php echo $this->lang->line('hrms_reports_scond_month2'); ?><!--Second Month--></label>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo form_dropdown('secondMonth', $payrollMonth, '', 'class="form-control select2" id="lastMonth2" required'); ?>
                            </div>
                        </div>
                        <label for="inputData" class="col-md-1 control-label"></label>
                        <div class="col-md-3" id="form-btn-group">
                            <button type="button" class="btn btn-primary btn-xs generateBtn" onclick="isGenerateORPrint('Generate', 2)">
                                <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                            </button>
                            <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="isGenerateORPrint('Print', 2)">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </button>
                            <a href="#" class="btn btn-excel btn-xs" id="btn-excel2" download="Salary Comparison Detail.xls" style="display: none"
                               onclick="let file = tableToExcel('salaryComparisonDetTB', 'Salary Comparison Detail'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a>
                            <input type="hidden" id="eventType2" value="">
                        </div>
                    </div>
                </fieldset>
            <?php echo form_close(); ?>

            <div id="salaryComparisonReportDet" class="cols-sm-12" style="display: none; margin-top: 2%"></div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <script>

        let reportCreateForm = $('#reportCreate_form');
        let detForm = $('#detail_form');

        $(document).ready(function (e) {
            /*align excel button*/
            let btnExcel = $('#btn-excel');
            let divContent = btnExcel.closest('div').html();
            btnExcel.closest('div').remove();
            $('#form-btn-group').append(divContent);

            Inputmask().mask(document.querySelectorAll("input"));

            $('.headerclose').click(function () {
                fetchPage('system/hrm/report/salary_comparison', '', 'Salary Comparison');
            });

            reportCreateForm.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    firstMonth: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_first_month_is_required');?>.'}}}, /*First month is required*/
                    secondMonth: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_second_month_is_required');?>.'}}}/*Second month is required*/
                },
            }).on('success.form.bv', function (e) {
                $('.generateBtn').prop('disabled', false);
                e.preventDefault();

                let eventType = $('#eventType').val();

                if (eventType == 'Print') {
                    PrintData();
                }
                else if (eventType == 'Excel') {
                    loadToExcel();
                }
                else {
                    generateData();
                }

            });


            detForm.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    firstMonth: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_first_month_is_required');?>.'}}}, /*First month is required*/
                    secondMonth: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_second_month_is_required');?>.'}}}/*Second month is required*/
                },
            }).on('success.form.bv', function (e) {
                $('.generateBtn').prop('disabled', false);
                e.preventDefault();

                let eventType = $('#eventType2').val();

                if (eventType == 'Print') {
                    PrintDataDet();
                }
                else {
                    generateDataDet();
                }

            });

        });

        function isGenerateORPrint(eventType, rpt=1) {
            if(rpt == 1){
                $('#eventType').val(eventType);
                reportCreateForm.submit();
            }
            else{
                $('#eventType2').val(eventType);
                detForm.submit();
            }

        }

        function generateReportPdf() {
            $('#eventType').val('Print');
            reportCreateForm.submit();
        }

        function generateData() {
            var postData = reportCreateForm.serializeArray();
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo site_url('Report/salaryComparison_reportGenerate/view/') ?>',
                data: postData,
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#salaryComparisonReport').html(data).css('display', 'block');

                    $('#salaryComparisonTB').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function PrintData() {
            let form = document.getElementById('reportCreate_form');
            form.target = '_blank';
            form.method = 'post';
            form.action = '<?php echo site_url('Report/salaryComparison_reportGenerate/print/Salary-Comparison'); ?>';
            form.submit();
        }

        function generateDataDet() {
            let postData = detForm.serializeArray();
            $('#btn-excel2').hide();

            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo site_url('Report/salaryComparisonDet_reportGenerate/view/') ?>',
                data: postData,
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#btn-excel2').show();
                    stopLoad();
                    $('#salaryComparisonReportDet').html(data).css('display', 'block');

                    $('#salaryComparisonDetTB').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function PrintDataDet() {
            let form = document.getElementById('detail_form');
            form.target = '_blank';
            form.method = 'post';
            form.action = '<?php echo site_url('Report/salaryComparisonDet_reportGenerate/print/Salary-Comparison'); ?>';
            form.submit();
        }

    </script>

<?php
