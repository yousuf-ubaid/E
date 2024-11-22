<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('inventory_item_exceeded_matching');
$date_format_policy = date_format_policy();
$financeyear_arr = all_financeyear_drop(true);
$item_records = fetch_item_data_by_company();
$outlets = all_delivery_location_drop(false);
$current_date = convert_date_format(current_date());
$startdate =date('Y-M-01', strtotime($current_date));
$start_date = convert_date_format($startdate);
echo head_page($title, false);
?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#itemexceed" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('inventory_item_exceeded_details'); ?><!--Item Exceeded Detail--></a></li>
        <li class=""><a href="#exceedmatching" data-toggle="tab" aria-expanded="true" onclick="get_item_match_report()"><?php echo $this->lang->line('inventory_exceeded_matching'); ?><!--Exceeded Matching--></a></li>
        <li class=""><a href="#itemexceedsummery" data-toggle="tab" aria-expanded="true" onclick="get_item_exceed_summery_report()"><?php echo $this->lang->line('inventory_item_exceeded_summary'); ?><!--Item Exceed Summary--></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="itemexceed">
            <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' name="frm_rpt_item_exceeded" id="frm_rpt_item_exceeded" class="form-group" role="form"'); ?>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrom"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $start_date ?>" id="datefrom" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                            <div class="input-group datepicto">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateto"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date ?>" id="dateto" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="status_filter_item"><?php echo $this->lang->line('common_item_status');?></label>
                            <?php echo form_dropdown('status_filter_item', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item" '); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="item"><?php echo $this->lang->line('common_item'); ?><!--Item--></label>
                            <div id="div_load_item">
                                <?php
                                foreach ($item_records as $row) {
                                    $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
                                    if($itemSecondaryCodePolicy){
                                        $item_code = $row["seconeryItemCode"];
                                    }else{
                                        $item_code = $row["itemSystemCode"];
                                    }
                                    $item_arr[trim($row['itemAutoID'] ?? '')] = trim($item_code) . ' | ' . trim($row['itemName'] ?? '');
                                }
                                echo form_dropdown('item[]', $item_arr, '', 'multiple class="form-control select2" id="item" required');
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-sm-2">
                            <label for="segment"><?php echo $this->lang->line('common_outlets'); ?><!--Outlets--></label>
                            <?php
                            echo form_dropdown('wareHouseAutoID[]', $outlets, '', 'multiple class="form-control select2" id="wareHouseAutoID" required');
                            ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <label for="segment">&nbsp;</label>
                            <div class="input-group" id="">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="viewZeroBalace" id="viewZeroBalace" value="1" checked>
                                </span>
                                <input type="text" class="form-control" disabled="" value="Hide Zero Balance Qty">
                            </div>
                        </div>

                        <div class="form-group col-sm-1">
                            <label for=""></label>
                            <button style="margin-top: 5px" type="button" onclick="get_item_exceed_report()"
                                    class="btn btn-primary btn-xs">
                                <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </fieldset>
            </div>
            <hr style="margin: 0px;">
            <div id="div_item_exceed">
            </div>
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="exceedmatching">
            <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' name="frm_rpt_item_matching" id="frm_rpt_item_matching" class="form-group" role="form"'); ?>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrom"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $start_date ?>" id="datefrom" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                            <div class="input-group datepicto">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateto"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date ?>" id="dateto" class="form-control">
                            </div>
                        </div>

                        <div class="form-group col-sm-1">
                            <label for=""></label>
                            <button style="margin-top: 5px" type="button" onclick="get_item_match_report()"
                                    class="btn btn-primary btn-xs">
                                <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                        </div>


                    </div>
                    <?php echo form_close(); ?>
                </fieldset>
            </div>
            <hr style="margin: 0px;">
            <div id="div_item_matching">
            </div>
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="itemexceedsummery">
            <div>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                    <?php echo form_open('login/loginSubmit', ' name="frm_rpt_item_exceeded_summery" id="frm_rpt_item_exceeded_summery" class="form-group" role="form"'); ?>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_as_of_date'); ?><!--As of Date--></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrom"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date ?>" id="datefrom" class="form-control">
                            </div>
                        </div>
                       <!-- <div class="form-group col-sm-2">
                            <label for="">Date To</label>
                            <div class="input-group datepicto">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateto"
                                       data-inputmask="'alias': '<?php /*echo $date_format_policy */?>'"
                                       value="<?php /*echo $current_date */?>" id="dateto" class="form-control">
                            </div>
                        </div>-->
                        <div class="col-sm-2">
                            <label for="status_filter_item_sum"><?php echo $this->lang->line('common_item_status');?></label>
                            <?php echo form_dropdown('status_filter_item_sum', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item_sum" '); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="segment"><?php echo $this->lang->line('common_item'); ?><!--Item--></label>
                            <div id="div_load_item_sum">
                            <?php
                            $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
                            foreach ($item_records as $row) {
                                if($itemSecondaryCodePolicy){
                                    $item_code = $row["seconeryItemCode"];
                                }else{
                                    $item_code = $row["itemSystemCode"];
                                }
                                $item_arr[trim($row['itemAutoID'] ?? '')] = trim($item_code) . ' | ' . trim($row['itemName'] ?? '');
                            }
                            echo form_dropdown('itemSum[]', $item_arr, '', 'multiple class="form-control select2" id="itemSum" required');
                            ?>
                            </div>
                        </div>

                        <div class="form-group col-sm-2">
                            <label for="segment"><?php echo $this->lang->line('common_outlets'); ?><!--Outlets--></label>
                            <?php
                            echo form_dropdown('wareHouseAutoIDSum[]', $outlets, '', 'multiple class="form-control select2" id="wareHouseAutoIDSum" required');
                            ?>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="segment">&nbsp;</label>
                            <div class="input-group" id="">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="viewZeroBalace_sum" id="viewZeroBalace_sum" value="1" checked>
                                </span>
                                <input type="text" class="form-control" disabled="" value="Hide Zero Balance Qty">
                            </div>
                        </div>

                        <div class="form-group col-sm-1">
                            <label for=""></label>
                            <button style="margin-top: 5px" type="button" onclick="get_item_exceed_summery_report()"
                                    class="btn btn-primary btn-xs">
                                <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </fieldset>
            </div>
            <hr style="margin: 0px;">
            <div id="div_item_exceed_summery">
            </div>
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="itemExceedMatchdrilldownModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Item Matching Drill Down</h4>
                <?php echo form_open('login/loginSubmit', ' name="frm_rpt_item_matching_detail" id="frm_rpt_item_matching_detail" class="form-group" role="form"'); ?>
                <input type="hidden" id="exceedmatchid" name="exceedmatchid">
                <?php echo form_close(); ?>
            </div>
            <div class="modal-body" id="matchdd">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#status_filter_item").change(function () {
        load_statusbase_item(1)
    });
    $("#status_filter_item_sum").change(function () {
        load_statusbase_item(2)
    });
    $('#item').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#item").multiselect2('selectAll', false);
    $("#item").multiselect2('updateButtonText');

    $('#wareHouseAutoID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#wareHouseAutoID").multiselect2('selectAll', false);
    $("#wareHouseAutoID").multiselect2('updateButtonText');

    $('#itemSum').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#itemSum").multiselect2('selectAll', false);
    $("#itemSum").multiselect2('updateButtonText');

    $('#wareHouseAutoIDSum').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#wareHouseAutoIDSum").multiselect2('selectAll', false);
    $("#wareHouseAutoIDSum").multiselect2('updateButtonText');

    $('.headerclose').click(function () {
        fetchPage('system/accounts_receivable/report/erp_collection_summary_report', '', 'Collection Summary')
    });
    $(document).ready(function (e) {

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        get_item_exceed_report();
        // get_item_match_report();
        // get_item_exceed_summery_report();
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    function get_item_exceed_report() {
        var data = $("#frm_rpt_item_exceeded").serializeArray();
        var viewZeroBalace = ($('#viewZeroBalace').prop('checked'))? '1' : '0';
        data.push({'name':'viewZeroBalace', 'value':viewZeroBalace});
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_item_exceed_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_item_exceed").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_item_exceed_summery_report() {
        $(window).unbind('scroll');
        var data = $("#frm_rpt_item_exceeded_summery").serializeArray();
        var viewZeroBalace = ($('#viewZeroBalace_sum').prop('checked'))? '1' : '0';
        data.push({'name':'viewZeroBalace', 'value':viewZeroBalace});
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_item_exceed_summery_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_item_exceed_summery").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportitemexceedExcel(){
        var form = document.getElementById('frm_rpt_item_exceeded');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#frm_rpt_item_exceeded').serializeArray();
        form.action = '<?php echo site_url('Report/get_item_exceed_report_excel'); ?>';
        form.submit();
    }

    function generateReportitemexceedPdf() {
        var form = document.getElementById('frm_rpt_item_exceeded');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_item_exceed_report_pdf'); ?>';
        form.submit();
    }
    function generateReportmatchingPdf() {
        var form = document.getElementById('frm_rpt_item_matching');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_item_matching_report_pdf'); ?>';
        form.submit();
    }

    function generateReportMatchingSummaryPdf() {
        var form = document.getElementById('frm_rpt_item_exceeded_summery');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_item_exceed_summary_report_pdf'); ?>';
        form.submit();
    }

    function generateReportmatchingDetailPdf() {
        var form = document.getElementById('frm_rpt_item_matching_detail');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_item_matching_detail_report_pdf'); ?>';
        form.submit();
    }

    function get_item_match_report() {
        $(window).unbind('scroll');
        var data = $("#frm_rpt_item_matching").serialize();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_item_match_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_item_matching").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_item_match_detail_report(id){
        $('#exceedmatchid').val(id);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_item_match_detail_report') ?>",
            data: {'exceededMatchID': id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#matchdd").html(data);
                $('#itemExceedMatchdrilldownModal').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
    function load_statusbase_item(tab) {
        var status_filter_item = '';
        if(tab == 1){
             status_filter_item = $('#status_filter_item').val();
        }else{
            status_filter_item = $('#status_filter_item_sum').val();
        }
        if (status_filter_item) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {activeStatus: status_filter_item,tab:tab},
                url: "<?php echo site_url('Inventory/fetch_statusbase_item'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if(tab == 1){
                        $('#div_load_item').html(data);
                        $("#item").multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            buttonWidth: '180px',
                            maxHeight: '30px'
                        });
                        $("#item").multiselect2('selectAll', false);
                        $("#item").multiselect2('updateButtonText');
                    }else{
                        $('#div_load_item_sum').html(data);
                        $("#itemSum").multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            buttonWidth: '180px',
                            maxHeight: '30px'
                        });
                        $("#itemSum").multiselect2('selectAll', false);
                        $("#itemSum").multiselect2('updateButtonText');
                    }
                    
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } 
    }
</script>
