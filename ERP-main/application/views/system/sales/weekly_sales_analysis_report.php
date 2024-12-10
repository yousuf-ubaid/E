<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('buyback_helper');
$title = $this->lang->line('sales_markating_sales_analysis_report');
echo head_page($title, false);
 ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open('login/loginSubmit', ' id="frm_filter_weeklySalesAnalysis" class="form-horizontal" name="frm_filter_weeklySalesAnalysis" role="form"'); ?>
        <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf" value="">
        <input type="hidden" id="captionChkpdf" name="captionChkpdf" value="">
        <input type="hidden" id="itemIDpdf" name="itemID" value="">
        <input type="hidden" id="itemNamepdf" name="itemName" value="">
        <input type="hidden" id="currencypdf" name="currency" value="">
        <div id="filters"> <!--load report content-->

        </div>
        </form>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="weeklySalesAnalysis_report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
            </div>
        </div>
    </div>
</div>
<!--modal report-->
<div class="modal fade" id="weeklySalesAnalysis_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_sales_analysis_report_drill_down');?><span class="myModalLabel"></span></h4><!--Drill Down-->
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
        $('.headerclose').click(function () {
            fetchPage('system/sales/weekly_sales_analysis_report', '', 'Sales Analysis Report')
        });

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('Sales/get_erp_weeklySalesIncome_report'); ?>';
            urlPdf = '<?php echo site_url('Sales/get_erp_weeklySalesIncome_report_pdf'); ?>';
            urlDrill1 = '<?php echo site_url('Sales/get_erp_weeklySalesIncome_drilldown_report'); ?>';
        }

        get_weekly_sales_analysis_filter();/*call filter for report method*/
        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

    });

    function get_weekly_sales_analysis_filter() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {formName: "frm_filter_weeklySalesAnalysis", reportID: '<?php echo $_POST["page_id"] ?>',type:type},
            url: "<?php echo site_url('Sales/get_weekly_sales_analysis_filter'); ?>",
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
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
                $('#weeklySalesAnalysis_report_modal').modal("show");
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function openSalesAnalysis_weeklyDoc(subsubCatID, weekID) {
        var location = $('#location').val();
        var dataFilter = $('#dataFilter').val();
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {itemCategoryID: subsubCatID, weekID: weekID, location: location, dataFilter: dataFilter},
            url: urlDrill1,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data){
                    $("#reportContentDrilldown").html(data);
                    $('#weeklySalesAnalysis_report_drilldown_modal').modal('show');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
</script>