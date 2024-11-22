<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('report');
$itemfnDocumentID=array("'CINV'" => 'CINV - Customer Invoice', "'RV'" => 'RV - Receipt Voucher', "'POS'" => 'POS - Point of Sales');
echo head_page($_POST["page_name"], false); ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <form method="POST" id="frm_filter" class="form-horizontal" action="" name="frm_filter">
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
            <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf" value="">
            <input type="hidden" id="captionChkpdf" name="captionChkpdf" value="">
            <input type="hidden" id="itemIDpdf" name="itemID" value="">
            <input type="hidden" id="itemNamepdf" name="itemName" value="">
            <input type="hidden" id="currencypdf" name="currency" value="">
            <input type="hidden" id="fieldNameChkexcel" name="fieldNameChkexcel" value="">
            <input type="hidden" id="captionChkexcel" name="captionChkexcel" value="">
            <div id="filters"> <!--load report content-->

            </div>
        </form>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="item_report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 100%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $_POST["page_name"] ?></h4>
            </div>
            <div class="modal-body">
                <?php if($_POST['page_id']=='ITM_LG'  || $_POST['page_id']=='FIN_GL'){
                    ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <label> Document </label>
                            <?php echo form_dropdown('documentID[]',itemLedgerDocumentID(),'', 'class="form-control" id="documentID" onchange="loadDocumentIDwise()" multiple="multiple"'); ?>
                        </div>
                    </div>
                    <?php
                } ?>

                <?php if($_POST['page_id']=='ITM_FM'){
                    ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <label> Document </label>
                            <?php echo form_dropdown('documentID[]',$itemfnDocumentID,'', 'class="form-control" id="documentID" onchange="loadDocumentIDwise()" multiple="multiple"'); ?>
                        </div>
                    </div>
                    <?php
                } ?>

                <div id="reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
            </div>
        </div>
    </div>
</div>
<!--modal report-->
<div class="modal fade" id="item_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('transaction_drill_down');?> - <span class="myModalLabel"></span></h4><!--Drill Down-->
            </div>
            <div class="modal-body">
                <div id="reportContentDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var type;
    var url;
    var urlPdf;
    var formNamerpt;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/inventory/report/erp_item_report','<?php echo $_POST['page_id']?>','<?php echo $_POST['page_name'] ?>');
        });

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('Report/get_report_by_id'); ?>';
            urlPdf = '<?php echo site_url('Report/get_report_by_id_pdf'); ?>';
        }else{
            url = '<?php echo site_url('Report/get_group_report_by_id'); ?>';
            urlPdf = '<?php echo site_url('Report/get_group_report_by_id_pdf'); ?>';
        }

        get_item_filter();/*call filter for report method*/
        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

    });
    /*get filter for report*/
    function get_item_filter() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            //data: {formName: "frm_filter", reportID: '<?php // echo $_POST["page_id"] ?>',type:type},
            data: {formName: "frm_filter", reportID: 'ITM_AG',type:type},

            url: "<?php echo site_url('Report/get_stock_aging_report_item'); ?>",
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
        formNamerpt=formName;
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });
        var serializeArray = $("#"+formName).serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk, captionChk);
        var finalArray2 = $.merge(finalArray, captionChk);
        finalArray2.push({name: "documentID", value: $('#documentID').val()});

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray2,
            url: "<?php echo site_url('Report/stock_aging_drilldown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
                $('#item_report_modal').modal("show");
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*call report drilldown content*/
    function generateDrilldownReport(itemID,itemName,currency) {
        var fieldNameChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
        });
        var serializeArray = $("#frm_filter").serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk);
        finalArray.push({name:"itemID",value:itemID});
        finalArray.push({name:"itemName",value:itemName});
        finalArray.push({name:"currency",value:currency});
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray,
            url: "<?php echo site_url('Report/get_report_drilldown'); ?>",
            beforeSend: function () {
                startLoad();
                $('#itemIDpdf').val(itemID);
                $('#itemNamepdf').val(itemName);
                $('#currencypdf').val(currency);
            },
            success: function (data) {
                stopLoad();
                $("#reportContentDrilldown").html(data);
                $(".myModalLabel").html(itemName);
                $('#item_report_drilldown_modal').modal("show");
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
        document.getElementById('fieldNameChkpdf').value = fieldNameChk;
        document.getElementById('captionChkpdf').value = captionChk;
        form.target='_blank';
        form.action=urlPdf;
        form.submit();
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

    function loadDocumentIDwise(){
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });
        var serializeArray = $("#"+formNamerpt).serializeArray();
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

    /*call report content excel*/
    function excel_export_stock_aging() {
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push($(this).val());
            captionChk.push($(this).data('caption'));
        });
        var form = document.getElementById('frm_filter');
        document.getElementById('fieldNameChkexcel').value = fieldNameChk.join();
        document.getElementById('captionChkexcel').value = captionChk.join();
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#frm_filter').serializeArray();
        form.action = '<?php echo site_url('Report/export_excel_stock_aging_report'); ?>';
        form.submit();
    }
</script>