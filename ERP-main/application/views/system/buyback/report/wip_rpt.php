<?php
$this->load->helper('buyback_helper');
$farmer_arr = load_all_farms(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
echo head_page('WIP Report', true);

?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
     tr.highlighted td {
         background:rgb(161, 191, 252);
     }

</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
    <?php echo form_open('login/loginSubmit', ' name="frm_rpt_wip_rpr" id="frm_rpt_wip_rpr" class="form-horizontal" role="form"'); ?>
    <div class="col-md-12" style=" margin-bottom: 2%">
        <label for="inputData" class="col-md-1 control-label">
            Farm :</label>
        <div class="col-md-2">
            <?php echo form_dropdown('famerid[]', $farmer_arr, '', 'multiple  class="form-control select2" id="famerid" onchange="get_wip_rep()" required'); ?>
        </div>
        <label for="inputData" class="col-md-2 control-label">As Of Date :</label>
        <div class="col-sm-2">
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="asdateof"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                       value="<?php echo $current_date; ?>" id="asdateof" class="form-control">
            </div>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-2">
            <div class="input-group" id="hide_total_row">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="hideTotalRow" id="hideTotalRow" value="Y" onclick="get_wip_rep()">
                                </span>
                <input type="text" class="form-control" disabled="" value="View Farm Total">
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-4" style=" margin-bottom: 2%">
            <input type="text" id="search" name="search" class="form-control" placeholder="Search........." onkeyup="get_wip_rep()">
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<div id="div_wip_report">
</div>
<div class="modal fade" id="drilldownModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="sales_order_drilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dispatchnote" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Dispatch Note Detail Drill Down</h4>
            </div>
            <div class="modal-body" id="dispatchdd">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    var type;
    var url;
    var urlPdf;
    var urlDrill;
    $(document).ready(function (e) {

  var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            get_wip_rep();
        });


        $('#famerid').multiselect2({
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#famerid").multiselect2('selectAll', false);
        $("#famerid").multiselect2('updateButtonText');
        $('.headerclose').click(function () {
            fetchPage('system/buyback/report/wip_rpt', '', 'WIP Report')
        });

        get_wip_rep();
    });

    function get_wip_rep() {
        var search = $('#search').val();
        var famerid = $('#famerid').val();
        var asdateof = $('#asdateof').val();
        var hideTotalRow = ($('#hideTotalRow').prop('checked'))? 'Y' : 'N';

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Buyback/get_wip_report'); ?>",
         //   data: $("#frm_rpt_wip_rpr").serialize(),
            data: {'search' : search, 'famerid' : famerid, 'asdateof' : asdateof, 'hideTotalRow' : hideTotalRow },
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_wip_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_wip_rpr');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/get_wip_report_pdf'); ?>';
        form.submit();
    }

    function drilldownSalesOrder(autoID,documentCode,type,title) {
        var form = $("#frm_rpt_wip_rpr").serializeArray();
        form.push({name:'autoID',value:autoID});
        form.push({name:'type',value:type});
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('sales/get_group_sales_order_drilldown_report'); ?>",
            data: form,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#drilldownModal').modal('show');
                $('.drilldown-title').html(title+" - "+documentCode);
                $("#sales_order_drilldown").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
    function open_dis_dd(batchid) {//dispatchnote drill down
        var date = $('#asdateof').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Buyback/dispatchnote_drilldown'); ?>",
            data: {'batchid': batchid,'date':date},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#dispatchdd").html(data);
                $('#dispatchnote').modal('show');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }



</script>
