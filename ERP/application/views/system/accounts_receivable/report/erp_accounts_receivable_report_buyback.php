<!-- Created By : SAFEENA ANSAR  -->
<!-- Created For : Haira sales return amount issue  -->

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
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
        <?php echo form_open('login/loginSubmit', ' id="frm_filter" class="form-horizontal" name="frm_filter" role="form"'); ?>
        <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf" value="">
        <input type="hidden" id="captionChkpdf" name="captionChkpdf" value="">
        <input type="hidden" id="customerIDpdf" name="customerID" value="">
        <input type="hidden" id="customerNamepdf" name="customerName" value="">
        <input type="hidden" id="currencypdf" name="currency" value="">
        <input type="hidden" id="agepdf" name="age" value="">
        <div id="filters"> <!--load report content-->

        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="ar_report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 100%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $_POST["page_name"] ?></h4>
            </div>
            <div class="modal-body">
                <div id="reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<!--modal report-->
<div class="modal fade" id="ar_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_receivable_rs_cl_drill_down');?><!--Drill Down--> - <span class="myModalLabel"></span></h4>
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
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/accounts_receivable/report/erp_accounts_receivable_report_buyback','<?php echo $_POST["page_id"] ?>','<?php echo $_POST["page_name"] ?>');
        });
        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('Report/get_report_by_id_buyback'); ?>';
            urlPdf = '<?php echo site_url('Report/get_report_by_id_pdf_buyback'); ?>';
        }else{
            url = '<?php echo site_url('Report/get_group_report_by_id_buyback'); ?>';
            urlPdf = '<?php echo site_url('Report/get_report_by_id_pdf_buyback'); ?>';
        }
        get_accounts_receivable_filter();/*call filter for report method*/
        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });
    /*get filter for report*/
    function get_accounts_receivable_filter() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {formName: "frm_filter", reportID: '<?php echo $_POST["page_id"] ?>',type:type},
            url: "<?php echo site_url('Report/get_accounts_receivable_filter'); ?>",
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

    /*call report content*/
    function generateReport(formName) {
        var fieldNameChk = [];
        var captionChk = [];
        var serializeArray = $("#"+formName).serializeArray();
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });

        if($('#groupbycus').is(':checked')){
            //serializeArray.push({name: "groupbycus", value: 1});
        }else {
            serializeArray.push({name: "groupbycus", value: 0});
        }


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
                $('#ar_report_modal').modal("show");
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

        var form= document.getElementById('frm_filter');
        document.getElementById('fieldNameChkpdf').value = fieldNameChk.join();
        document.getElementById('captionChkpdf').value = captionChk.join();
        form.target='_blank';
        form.action=urlPdf;
        form.submit();
    }

    /*call report drilldown content*/
    function generateDrilldownReport(customerID,customerName,currency,age, SubCustomers, groupbycus) {
        var fieldNameChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
        });
        var serializeArray = $("#frm_filter").serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk);
        finalArray.push({name:"customerID",value:customerID});
        finalArray.push({name:"customerName",value:customerName});
        finalArray.push({name:"currency",value:currency});
        finalArray.push({name:"age",value:age});
        finalArray.push({name:"SubCustomers",value:SubCustomers});
        finalArray.push({name:"groupbycus",value:groupbycus});
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray,
            url: "<?php echo site_url('Report/get_report_drilldown_buyback'); ?>",
            beforeSend: function () {
                startLoad();
                $('#customerIDpdf').val(customerID);
                $('#customerNamepdf').val(customerName);
                $('#currencypdf').val(currency);
                $('#agepdf').val(age);
            },
            success: function (data) {
                stopLoad();
                $("#reportContentDrilldown").html(data);
                $(".myModalLabel").html(customerName);
                $('#ar_report_drilldown_modal').modal("show");
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*call drill down report content pdf*/
    function generateDrilldownReportPdf() {
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
        form.action = '<?php echo site_url('Report/get_report_drilldown_pdf'); ?>';
        form.submit();
    }
</script>