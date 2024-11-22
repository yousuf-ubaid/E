<?php echo head_page($_POST["page_name"], false);
$this->load->helper('buyback_helper');
$batchMasterID_arr = load_buyBack_batches_report();
$farms_arr = load_all_farms();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<style>/*.fixHeader_Div {
        height: 370px;
        border: 1px solid #c0c0c0;
    }*/
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li class="active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i>
            Display </a></li>
    <li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="display">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Master</legend>
                    <?php echo form_open('', 'role="form" id="buyback_productionReport_form"'); ?>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Farm :</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('farmID', $farms_arr, '', 'class="form-control select2" id="farmID" onchange="fetch_farmBatch(this.value)"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Batch :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_loadBatch">
                                        <?php echo form_dropdown('batchMasterID', array(''=>' Select batch'), '', 'class="form-control select2" '); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="generateReport('buyback_productionReport_form')" name="filtersubmit"
                        id="filtersubmit"><i
                        class="fa fa-plus"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="finance_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Production Statement</h4>
            </div>
            <div class="modal-body" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979" >
                <div id="reportContent"></div>
            </div>
            <div class="modal-body" id="PaymentHistoryModal" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979">
                <div id="PaymentHistory"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
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
                <h4 class="modal-title" id="myModalLabel">Drill Down - <span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="reportContentDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
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
            fetchPage('system/buyback/report/production_report', '<?php echo $_POST["page_id"] ?>', '<?php echo $_POST["page_name"] ?>')
        });
        $(".select2").select2();
        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr = typeArr.split('-');
        type = typeArr[1];

        if (type == 1) {
            url = '<?php echo site_url('Report/get_report_by_id'); ?>';
        } else {
            url = '<?php echo site_url('Report/get_group_report_by_id'); ?>';
        }
        //get_finance_filter();
        /*call filter for report method*/
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    /*call report content*/
    function generateReport(formName) {
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
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray2,
            url: '<?php echo site_url('Buyback/buyback_production_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
                $('#finance_report_modal').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    /*call report content pdf*/



    function fetch_farmBatch(farmID) {
       // $('#div_loadBatch').empty('');
        if(farmID)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {farmID: farmID},
                url: "<?php echo site_url('Buyback/fetch_farm_BatchesDropdown_all'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loadBatch').html(data);
                    $(".select2").select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

</script>