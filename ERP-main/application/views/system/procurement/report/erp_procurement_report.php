<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('procurement_purchase_order_list');
echo head_page($title, false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<style>/*.fixHeader_Div {
        height: 370px;
        border: 1px solid #c0c0c0;
    }*/</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open('login/loginSubmit', ' id="frm_filter" class="form-horizontal" name="frm_filter" role="form"'); ?>
            <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf" value="">
            <input type="hidden" id="captionChkpdf" name="captionChkpdf" value="">
            <div id="filters"> <!--load report content-->

            </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="procurement_report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 100%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
            </div>
            <div class="modal-body">
                <div id="reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var type;
    var url;
    var url2;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/procurement/report/erp_procurement_report', 'PROC_POL', 'Purchase Order List');
        });
        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('Report/get_report_by_id'); ?>';
        }else{
            url = '<?php echo site_url('Report/get_group_report_by_id'); ?>';
        }

        get_procurement_filter();
        /*call filter for report method*/
    });
    /*get filter for report*/
    function get_procurement_filter() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {formName: "frm_filter", reportID: '<?php echo $_POST["page_id"] ?>',type:type},
            url: "<?php echo site_url('Report/get_procurement_filter'); ?>",
            beforeSend: function () {
                $("#filters").html("<div class='text-center'><i class='fa fa-refresh fa-spin fa-2'></i> Loading</div>");
            },
            success: function (data) {
                $("#filters").html("");
                $("#filters").html(data);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function get_procurement_date(currentDate) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {currentDate: currentDate},
            url: "<?php echo site_url('Report/get_financial_year'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $("#procurementDate").html("<b>" + data["beginingDate"] + "</b> - <b>" + data["endingDate"] + "</b>");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    /*call report content*/
    function generateReport(formName) {
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });
        var serializeArray = $("#" + formName).serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk, captionChk);
        var finalArray2 = $.merge(finalArray, captionChk);

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
                $('#procurement_report_modal').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
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
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_report_by_id_pdf'); ?>';
        form.submit();
    }
</script>