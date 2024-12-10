<style type="text/css">
    tfoot td{ background-color: #dedede; }

    #segment-container .btn-group{ width: 150px !important; } /*Segment drop down style*/

    @media (min-width:1025px) {
        .margin-align { margin-left: -80px; }

        #form-btn-group{ width: 200px; }
    }

    @media (min-width:1281px) {
        .margin-align { margin-left: -80px; }

        #form-btn-group{ width: 200px; }
    }
</style>


<?php
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_registration');
echo head_page('PAYE '. $title  , false);
//echo head_page('PAYE Registration', false);
$defaultPayeeValues = get_defaultPayeeSetup();
if(!empty($defaultPayeeValues)){
    $segment_arr = fetch_segment(true,false);
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">

<form role="form" id="reportCreate_form" class="form-horizontal">
    <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Filter</legend>
        <div class="col-sm-12">
            <label for="fromDate" class="col-sm-2 col-xs-3 control-label margin-align">From Date</label>
            <div class="col-sm-2 col-xs-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="fromDate" value=" " id="fromDate" class="form-control filterDate" readonly>
                    </div>
                </div>
            </div>
            <label for="toDate" class="col-sm-1 col-xs-3 control-label">To Date</label>
            <div class="col-sm-2 col-xs-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="toDate" value="" id="toDate" class="form-control filterDate" readonly>
                    </div>
                </div>
            </div>
            <label class="col-sm-2 col-xs-3 control-label margin-align" for="segmentID">Segment</label>
            <div class="col-sm-2 col-xs-3" id="segment-container">
                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple"'); ?>
                <div class="clearfix visible-xs visible-sm">&nbsp;</div>
            </div>
            <label for="inputData" class="col-sm-1 control-label"></label>
            <div class="col-sm-3 col-xs-12" id="form-btn-group">
                <button type="button" class="btn btn-primary btn-xs generateBtn" onclick="isGenerateORPrint('Generate')">Generate</button>
                <button type="button" class="btn btn-primary btn-xs generateBtn" onclick="isGenerateORPrint('Print')">Print</button>
                <?php echo export_buttons('report-paye-reg', 'PAYE Registration', true, false, 'btn-xs '); ?>
                <input type="hidden"  id="eventType" value="">
            </div>
        </div>
    </fieldset>
</form>


<div id="report-paye-reg" class="" style="display: none"></div>
<?php
}
else{
    echo '<div class="alert alert-warning">
        <strong>Warning!</strong></br>
        PAYE configuration is not done.<br />
        Please complete the report configuration and try again.
    </div>';
}
 ?>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script>

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/payee_registration', '', 'Payee Registration');
    });


    $('.filterDate').datepicker({
        format: 'yyyy-mm',
        viewMode: "months",
        minViewMode: "months"
    }).on('changeDate', function (ev) {
        reportCreateForm.bootstrapValidator('revalidateField', $(this).attr('id'));
        $(this).datepicker('hide');
    });

    $('#segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    var reportCreateForm = $('#reportCreate_form');

    $(document).ready(function (e) {
        /*align excel button*/
        var btnExcel = $('#btn-excel');
        var divContent = btnExcel.closest('div').html();
        btnExcel.closest('div').remove();
        $('#form-btn-group').append(divContent);


        $('.select2').select2();

        reportCreateForm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                fromDate: {validators: {notEmpty: {message: 'From date is required.'}}},
                toDate: {validators: {notEmpty: {message: 'To date is required.'}}}
            }
        }).
        on('success.form.bv', function (e) {
            $('.generateBtn').prop('disabled', false);
            e.preventDefault();

            var eventType = $('#eventType').val();

            if(eventType == 'Print'){
                printData();
            } else{
                generateData();
            }

        });
    });

    function isGenerateORPrint(eventType){
        $('#eventType').val(eventType);
        reportCreateForm.submit();
    }

    function generateData(){

        var postData = reportCreateForm.serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo site_url('Report/payee_registration/view/') ?>',
            data: postData,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#report-paye-reg').html(data).css('display' , 'block');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function printData(){
        var form= document.getElementById('reportCreate_form');
        form.target='_blank';
        form.method='post';
        form.action='<?php echo site_url('Report/payee_registration/print/PAYE-Registration'); ?>';
        form.submit();
    }

</script>

<?php
