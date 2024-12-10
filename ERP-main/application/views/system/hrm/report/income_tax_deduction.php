<style type="text/css">
    .config-text{
        height: 25px !important;
        font-size: 11px;
        padding: 2px 4px;
    }

    @media (min-width: 768px){
        .margin-align {
            margin-right: 5px;
        }
    }

    /*#segment-container .btn-group{ width: 150px !important; }*/ /*Segment drop down style*/

    /*@media (min-width:1025px) {
        .margin-align {
            margin-left: -70px;
        }
    }

    @media (min-width:1281px) {
        .margin-align {
            margin-left: -70px;
        }
    }*/
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_income_tax_deduction');
echo head_page($title, false);
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
$defaultPayeeValues = get_defaultPayeeSetup();
if(!empty($defaultPayeeValues)){
    $segment_arr = fetch_segment(true,false);
    $type_arr = [
            'M' => 'Monthly', 'Y' => 'Year'
    ];

    $date_format_policy = date_format_policy();
    $start_date = date('Y-04'); //convert_date_format( date('Y-04-01') ); '2017-04';
    $end_date = date('Y-03', strtotime("$start_date +1 year")) ; //convert_date_format( date('Y-03-31', strtotime("$start_date +1 year")) ); '2018-03';
    ?>

    <form role="form" id="reportCreate_form" class="form-horizontal1">
        <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Filter</legend>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-2 col-xs-6">
                        <label for="rpt_type" class="control-label">From</label>
                        <div class="input-group date_pic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" class="form-control filterDate" id="from_date" name="from_date"  value="<?=$start_date?>" required />
                        </div>
                    </div>

                    <div class="form-group col-sm-2 col-xs-6">
                        <label for="rpt_type" class="control-label">To</label>
                        <div class="input-group date_pic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" class="form-control filterDate" id="to_date" name="to_date"  value="<?=$end_date?>" required />
                        </div>
                    </div>

                    <div class="form-group col-sm-2 col-xs-6">
                        <label for="processDate" class="control-label">Process&nbsp;Date </label><br/>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="processDate" value="" id="processDate" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="segment[]" class="control-label">Segment</label><br/>
                        <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple"'); ?>
                    </div>

                    <div class="col-sm-12 col-xs-12" id="form-btn-group">
                        <button type="button" class="btn btn-primary btn-xs generateBtn pull-right" onclick="isGenerateORDownload('Print')">Print</button>
                        <button type="button" class="btn btn-primary btn-xs generateBtn pull-right" onclick="isGenerateORDownload('Generate')" style="margin-right: 1%">
                            Generate
                        </button>
                        <button type="button" class="btn btn-primary btn-xs generateBtn pull-right" onclick="isGenerateORDownload('View')" style="margin-right: 1%">View</button>

                        <!--<button type="button" class="btn btn-success btn-xs btn-xs generateBtn" onclick="isGenerateORDownload('Excel')">Excel</button>-->
                        <input type="hidden"  id="eventType" value="">
                    </div>
                    <label for="inputData" class="col-md-1 control-label"></label>
                </div>
            </div>
        </fieldset>
    </form>
    
    <div id="report-payee-return" class="col-sm-12" style="display: none; padding: 0px !important;"></div>
    <?php
}
else{
    echo '<div class="alert alert-warning">
        <strong>Warning!</strong></br>
        Payee configuration is not done.<br />
        Please complete the report configuration and try again.
    </div>';
}
?>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script>
    let reportCreateForm = $('#reportCreate_form');

    $('.filterDate').datepicker({
        format: 'yyyy-mm',
        viewMode: "months",
        minViewMode: "months"
    }).on('changeDate', function (ev) {
        reportCreateForm.bootstrapValidator('revalidateField', $(this).attr('id'));
        $(this).datepicker('hide');
    });

    $('#processDate').datepicker({
        format: 'yyyy-mm-dd',
    }).on('changeDate', function (ev) {
        reportCreateForm.bootstrapValidator('revalidateField', $(this).attr('id'));
        $(this).datepicker('hide');
    });

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/income_tax_deduction', '', 'Payee Registration');
    });

    $('#segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function (e) {
        $('.select2').select2();

        reportCreateForm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                payrollMonth: {validators: {notEmpty: {message: 'Payroll month is required.'}}},
                processDate: {validators: {notEmpty: {message: 'Process date month is required.'}}}
            }
        }).
        on('success.form.bv', function (e) {
            $('.generateBtn').prop('disabled', false);
            e.preventDefault();

            let eventType = $('#eventType').val();
            if(eventType == 'Print'){
                PrintData();
            } else{
                generateData(eventType);
            }

        });
    });

    function isGenerateORDownload(eventType){
        $('#eventType').val(eventType);
        reportCreateForm.submit();
    }

    function generateData(req_type){
        let postData = reportCreateForm.serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo site_url('Report/income_tax_deduction/') ?>'+req_type,
            data: postData,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#report-payee-return').html(data).css('display' , 'block');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function PrintData(){
        let form= document.getElementById('reportCreate_form');
        form.target='_blank';
        form.method='post';
        form.action='<?php echo site_url('Report/income_tax_deduction/print/Income-Tex-Deduction'); ?>';
        form.submit();
    }

</script>
<?php
