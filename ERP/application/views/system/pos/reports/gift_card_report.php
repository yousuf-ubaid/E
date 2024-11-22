<?php echo head_page('<i class="fa fa-bar-chart"></i> Gift Card Report', false);
// $locations = load_pos_location_drop();
$locations = load_pos_location_drop_with_status();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$customers=get_exist_erp_pos_customers();
?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>


<!--tab-->
<div class="box-body" style="display: block;width: 100%">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_public_1"  data-toggle="tab" aria-expanded="false">Full Report</a></li>
            <li class=""><a href="#tab_public_2"  data-toggle="tab" aria-expanded="true">Top-up / Redeem Report</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_public_1">
                <div>
                    <div class="row">
                        <form id="frm_gift_card_report" method="post" class="form-inline text-center" role="form">
                            <input type="hidden" id="ps_outletID2" name="outletID" value="0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label class="" for="">Outlet</label>
                                        <select class=" filters" multiple required name="outletID_f[]" id="outletID_f" >
                                            <?php
                                            foreach ($locations as $loc) {
                                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-'. $loc['wareHouseLocation']. ' - ' . $loc['isActive'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="" for="">As of Date<!--to--></label>
                                            <input type="text" class="form-control input-sm startdateDatepic" id="sr_toDate"
                                                   value="<?php echo date('d-m-Y 23:59:59') ?>"
                                                   style="width: 130px;" name="filterTo" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-sm-12" style="margin-top: 5px;">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-sm pull-right">
                                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>
                    <div id="pos_gift_card_report_body" class="reportContainer" style="min-height: 200px;">
                        <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
                            Report
                        </div>
                    </div>


                </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane " id="tab_public_2">
                <div>
                    <div class="row">
                        <form id="frm_gift_card_report3" method="post" class="form-inline text-center" role="form">
                            <input type="hidden" id="ps_outletID3" name="outletID3" value="0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label class="" for="">Outlet</label>
                                        <select class=" filters" multiple required name="outletID_f3[]" id="outletID_f3" >
                                            <?php
                                            foreach ($locations as $loc) {
                                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-'. $loc['wareHouseLocation']. ' - ' . $loc['isActive'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="" for="">Customer</label>
                                            <?php echo form_dropdown('customer[]', $customers, '', 'multiple id="customer"  class="form-control input-sm"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--to--></label>
                                            <input type="text" class="form-control input-sm startdateDatepic" id="sr_FromDate3"
                                                   value="<?php echo date('d-m-Y 00:00:00') ?>"
                                                   style="width: 130px;" name="filterFrom3" placeholder="From"> <!--id="filterTo2"-->
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="" for=""><?php echo $this->lang->line('common_to'); ?><!--to--></label>
                                            <input type="text" class="form-control input-sm startdateDatepic" id="sr_toDate3"
                                                   value="<?php echo date('d-m-Y 23:59:59') ?>"
                                                   style="width: 130px;" name="filterTo3" placeholder="To"> <!--id="filterTo2"-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-sm-12" style="margin-top: 5px;">

                                    <div class="col-sm-3 pull-right">
                                        <button type="submit" id="submit3" class="btn btn-primary btn-sm pull-right">
                                            <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                                    </div>

                            </div>
                        </form>
                    </div>

                    <hr>
                    <div id="pos_gift_card_report_body3" class="reportContainer" style="min-height: 200px;">
                        <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
                            Report
                        </div>
                    </div>


                </div>
            </div>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

</div>
<!--tab-->



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {

        $("#outletID_f").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#outletID_f").multiselect2('selectAll', false);
        $("#outletID_f").multiselect2('updateButtonText');
        $("#frm_gift_card_report").submit(function (e) {
            load_pos_gift_card_report();
            return false;
        });

        $("#outletID_f3").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#outletID_f3").multiselect2('selectAll', false);
        $("#outletID_f3").multiselect2('updateButtonText');

        $('#customer').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customer").multiselect2('deselectAll', true);
        $("#customer").multiselect2('updateButtonText');

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
        }).on('dp.change', function (ev) {
            $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
            //$(this).datetimepicker('hide');
        });

    });

    function load_pos_gift_card_report() {
        var data = $("#frm_gift_card_report").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_pos_gift_card_report'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                $("#pos_gift_card_report_body").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_gift_card_report_body").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_pos_gift_card_topup_redeem_report() {
        var data = $("#frm_gift_card_report3").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_pos_gift_card_topup_redeem_report'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                $("#pos_gift_card_report_body3").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_gift_card_report_body3").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $(document).on('click','#submit3',function (e) {
        e.preventDefault();
        load_pos_gift_card_topup_redeem_report();
    });

</script>
