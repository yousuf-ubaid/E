<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('report');
echo head_page($_POST["page_name"], false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<style>/*.fixHeader_Div {
        height: 370px;
        border: 1px solid #c0c0c0;
    }*/</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open('login/loginSubmit', ' id="frm_filter" class="form-horizontal" action="" name="frm_filter" role="form"'); ?>
            <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf" value="">
            <input type="hidden" id="captionChkpdf" name="captionChkpdf" value="">
            <input type="hidden" id="glCodepdf" name="glCode" value="">
            <input type="hidden" id="masterCategorypdf" name="masterCategory" value="">
            <input type="hidden" id="currencypdf" name="currency" value="">
            <input type="hidden" id="monthpdf" name="month" value="">
            <input type="hidden" id="documentIDpdf" name="documentIDpdf" value="">
            <div id="filters" class="customer_master_style "> <!--load report content-->

            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="finance_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 100%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $_POST["page_name"] ?></h4>
            </div>
            <div class="modal-body">
                <?php if($_POST['page_id']=='ITM_LG' || $_POST['page_id']=='ITM_FM' || $_POST['page_id']=='FIN_GL'){
                ?>
                <div class="row">
                    <div class="col-sm-4">
                        <label><?php $this->lang->line('common_document')?> <!--Document--></label>
                        <?php echo form_dropdown('documentID[]',generalLedgerDocumentID(),'', 'class="form-control" id="documentID" onchange="loadDocumentIDwise()" multiple="multiple"'); ?>
                    </div>
                </div>
                    <?php
                } ?>
                <div id="reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<!--modal report-->
<div class="modal fade" id="finance_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('finance_rs_tb_drill_down');?><!--Drill Down--> - <span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="reportContentDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var type;
    var url;
    var urlPdf;
    var urlDrill;
    var urlDrillPdf;
    var formNamerpt;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/finance/report/erp_finance_report_new', '<?php echo $_POST["page_id"] ?>', '<?php echo $_POST["page_name"] ?>')
        });
        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];
        //alert(type);

        if(type == 1){
            url = '<?php echo site_url('Report/get_report_by_id_new'); ?>';
            urlPdf = '<?php echo site_url('Report/get_report_by_id_pdf'); ?>';
            urlDrill = '<?php echo site_url('Report/get_report_drilldown_new'); ?>';
            urlDrillPdf = '<?php echo site_url('Report/get_report_drilldown_pdf'); ?>';
        }else{
            url = '<?php echo site_url('Report/get_group_report_by_id'); ?>';
            urlPdf = '<?php echo site_url('Report/get_group_report_by_id_pdf'); ?>';
            urlDrill = '<?php echo site_url('Report/get_report_group_drilldown'); ?>';
            urlDrillPdf = '<?php echo site_url('Report/get_group_report_drilldown_pdf'); ?>';
        }
        get_finance_filter();
        /*call filter for report method*/
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });
    /*get filter for report*/
    function get_finance_filter() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {formName: "frm_filter", reportID: '<?php echo $_POST["page_id"] ?>',type:type},
            url: "<?php echo site_url('Report/get_finance_filter_new'); ?>",
            beforeSend: function () {
                $("#filters").html("<div class='text-center'><i class='fa fa-refresh fa-spin fa-2'></i> Loading</div>");
            },
            success: function (data) {
                $("#filters").html("");
                $("#filters").html(data);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_finance_date(currentDate, financeYear) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {currentDate: currentDate, financeYear: financeYear,type:type},
            url: "<?php echo site_url('Report/get_financial_year'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (data["error"]) {
                    var explode = $('#financeYear').find("option:selected").text().split('-');
                    var date = explode[3] + "-" + explode[4] + "-" + explode[5];
                    $('#from').val(date.trim());
                    notification("Invalid Date Range Selected", "e")
                } else {

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*call report content*/
    function generateReport(formName) {
        formNamerpt=formName;
        var reportID = $("#reportID").val();
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });
        var serializeArray = $("#" + formName).serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk, captionChk);
        var finalArray2 = $.merge(finalArray, captionChk);
        finalArray2.push({name: "documentID", value: $('#documentID').val()});
        //alert(finalArray2);
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray2,
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
                $('#finance_report_modal').modal("show");
                <?php if($_POST['page_id']=='ITM_LG' || $_POST['page_id']=='ITM_FM' || $_POST['page_id']=='FIN_GL'){
                ?>
                $("#documentID").multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    numberDisplayed: 1,
                    buttonWidth: '180px',
                    maxHeight: '30px'
                });
                //$("#documentID").multiselect2('selectAll', false);
                $("#documentID").multiselect2('updateButtonText');
                <?php
                } ?>
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*call report drilldown content*/
    function generateDrilldownReport(glCode, masterCategory, glDescription, currency, month, segment) {
       
        var fieldNameChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
        });
        var serializeArray = $("#frm_filter").serializeArray();

        if(segment !== undefined){
            serializeArray = $("#frm_filter").find("input[id!=segment]").serializeArray();
            serializeArray.push({name: "segment[]", value: segment});
        }

        var finalArray = $.merge(serializeArray, fieldNameChk);
        finalArray.push({name: "glCode", value: glCode});
        finalArray.push({name: "masterCategory", value: masterCategory});
        finalArray.push({name: "currency", value: currency});
        finalArray.push({name: "month", value: month});


        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray,
            url: urlDrill,
            beforeSend: function () {
                startLoad();
                $('#glCodepdf').val(glCode);
                $('#masterCategorypdf').val(masterCategory);
                $('#currencypdf').val(currency);
                $('#monthpdf').val(month);
            },
            success: function (data) {
                stopLoad();
                $("#reportContentDrilldown").html(data);
                $(".myModalLabel").html(glDescription);
                $('#finance_report_drilldown_modal').modal("show");
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*call report content pdf*/
    function generateReportPdf() {
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push($(this).val());
            captionChk.push($(this).data('caption'));
        });

        var form = document.getElementById('frm_filter');
        document.getElementById('fieldNameChkpdf').value = fieldNameChk;
        document.getElementById('captionChkpdf').value = captionChk;
        document.getElementById('documentIDpdf').value =  $('#documentID').val();
        form.target = '_blank';
        form.action = urlPdf;
        form.submit();
    }

    /*call drill down report content pdf*/
    function generateDrilldownReportPdf(seg=null) {
        /*** if (rptType=9) then only seg variable will be not null ***/
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push($(this).val());
            captionChk.push($(this).data('caption'));
        });

        let allSeg = $('#segment').val();
        if(seg != null){
            $('#segment').val(seg);
        }

        var form = document.getElementById('frm_filter');
        document.getElementById('fieldNameChkpdf').value = fieldNameChk;
        document.getElementById('captionChkpdf').value = captionChk;
        form.target = '_blank';
        form.action = urlDrillPdf;
        form.submit();

        if(seg != null){
            $('#segment').val(allSeg);
        }
    }


    function loadDocumentIDwise() {
        var reportID = $("#reportID").val();
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });
        var serializeArray = $("#" + formNamerpt).serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk, captionChk);
        var finalArray2 = $.merge(finalArray, captionChk);
        finalArray2.push({name: "documentID", value: $('#documentID').val()});
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray2,
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").empty();
                $("#reportContent").html(data);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
</script>

