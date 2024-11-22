<?php  echo head_page('<i class="fa fa-bar-chart"></i> Sales Detail Report', false);
//$locations = load_pos_location_drop();
$locations = load_pos_location_drop_with_status();

$customerType = getCustomerTypesFromMenuSalesMaster();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$customer_arr = get_creditCustomers();
$customers = array();
if (isset($customer_arr)) {
    foreach ($customer_arr as $row) {
        $customers[trim($row['posCustomerAutoID'] ?? '')] = (trim($row['posCustomerAutoID'] ?? '') ? trim($row['CustomerName'] ?? '') . " - " . $row['customerTelephone'] : '');
    }
}
?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="box-body" style="display: block;width: 100%">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#full_sales_detail_report" data-toggle="tab" aria-expanded="false">Sales Detail
                    Report</a></li>
            <li class=""><a href="#void_sales_detail_report" onclick="loadCashier_voidbill()" data-toggle="tab"
                            aria-expanded="true">Void Bills</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="full_sales_detail_report"><!--sales detail report-->
                <div>
                    <div class="row">
                        <form id="frm_salesReport2" method="post" class="form-inline text-center" role="form">
                            <input type="hidden" id="ps_outletID2" name="outletID" value="0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-sm-2">
                                        <label class="" for="">Outlet</label>
                                        <select class=" filters" multiple required name="outletID_f[]" id="outletID_f"
                                                onchange="loadCashier()">
                                            <?php
                                            foreach ($locations as $loc) {
                                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-' . $loc['wareHouseLocation'] . ' - ' . $loc['isActive'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">

                                            <label class="" for="">
                                                <?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                                            <span id="cashier_option">
                                <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier2"  class="form-control input-sm"'); ?>
                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="" for="">Order Type</label>
                                        <select class=" filters" multiple required name="orderType[]" id="orderType">
                                            <?php
                                            foreach ($customerType as $type) {
                                                echo '<option value="' . $type['customerTypeID'] . '">' . $type['customerDescription'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="" for="">
                                                <?php echo $this->lang->line('common_from'); ?><!--From--> </label>
                                            <input type="hidden" id="tmpFromDate" value="">
                                            <input type="hidden" id="tmpToDate" value="">
                                            <input type="text" required class="form-control input-sm startdateDatepic"
                                                   id="sr_fromDate"
                                                   name="filterFrom" value="<?php echo date('d-m-Y 00:00:00') ?>"
                                                   style="width: 130px;"> <!-- id="filterFrom2" inputDate-->
                                            <!--data-date-end-date="0d"-->
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="" for="">
                                                <?php echo $this->lang->line('common_to'); ?><!--to--></label>
                                            <input type="text" class="form-control input-sm startdateDatepic"
                                                   id="sr_toDate"
                                                   value="<?php echo date('d-m-Y 23:59:59') ?>"
                                                   style="width: 130px;" name="filterTo" placeholder="To">
                                            <!--id="filterTo2"-->
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row col-sm-12">

                            </div>

                            <div class="row col-sm-12" style="margin-top: 5px;">
                                <div class="col-sm-3" style="text-align: left;">
                                    <div class="form-group">
                                        <label class="" for=""><?php echo $this->lang->line('common_customer');?></label>
                                        <?php //echo form_dropdown('customer[]', $customers, '', 'multiple id="customer"  class="form-control input-sm"'); ?>
                                        <div id="div_load_customers" style="display: inline;">
                                            <select name="customer[]" class="form-control customer input-sm"  id="customer" multiple="multiple"  >
                                            <?php
                                                if (!empty($customers)) {
                                                    foreach ($customers as $key => $val) {
                                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-primary btn-sm pull-left">
                                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <div id="pos_modalBody_posPayment_sales_report2" class="reportContainer"
                         style="min-height: 200px;overflow: auto;">
                        <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the
                            Generate
                            Report
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="void_sales_detail_report"><!--Void detail Report-->
                <div>
                    <div class="row">
                        <form id="frm_salesVoidReport2" method="post" class="form-inline text-center" role="form">
                            <input type="hidden" id="ps_outletID3" name="outletID3" value="0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-sm-2">
                                        <label class="" for="">Outlet</label>
                                        <select class=" filters" multiple required name="outletID_f3[]" id="outletID_f3"
                                                onchange="loadCashier_voidbill()">
                                            <?php
                                            foreach ($locations as $loc) {
                                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-' . $loc['wareHouseLocation'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">

                                            <label class="" for="">
                                                <?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                                            <span id="cashier_option3">
                                <?php echo form_dropdown('cashier3[]', get_cashiers(), '', 'multiple required id="cashier3"  class="form-control input-sm"'); ?>
                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="" for="">Order Type</label>
                                        <select class=" filters" multiple required name="orderType2[]" id="orderType2">
                                            <?php
                                            foreach ($customerType as $type) {
                                                echo '<option value="' . $type['customerTypeID'] . '">' . $type['customerDescription'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="" for="">
                                                <?php echo $this->lang->line('common_from'); ?><!--From--> </label>
                                            <input type="hidden" id="tmpFromDate3" value="">
                                            <input type="hidden" id="tmpToDate3" value="">
                                            <input type="text" required class="form-control input-sm startdateDatepic"
                                                   id="sr_fromDate3"
                                                   name="filterFrom3" value="<?php echo date('d-m-Y 00:00:00') ?>"
                                                   style="width: 130px;"> <!-- id="filterFrom2" inputDate-->
                                            <!--data-date-end-date="0d"-->
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="" for="">
                                                <?php echo $this->lang->line('common_to'); ?><!--to--></label>
                                            <input type="text" class="form-control input-sm startdateDatepic"
                                                   id="sr_toDate3"
                                                   value="<?php echo date('d-m-Y 23:59:59') ?>"
                                                   style="width: 130px;" name="filterTo3" placeholder="To">
                                            <!--id="filterTo2"-->
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row col-sm-12">

                            </div>


                            <div class="row col-sm-12" style="margin-top: 5px;">
                                <div class="col-sm-3" style="text-align: left;">
                                    <div class="form-group">
                                        <label class="" for="">Customer</label>
                                        <?php //echo form_dropdown('customer3[]', $customers, '', 'multiple id="customer3"  class="form-control input-sm"'); ?>
                                        <div id="div_load_customers3" style="display: inline;">
                                            <select name="customer3[]" class="form-control customer3 input-sm"  id="customer3" multiple="multiple"  >
                                            <?php
                                                if (!empty($customers)) {
                                                    foreach ($customers as $key => $val) {
                                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary btn-sm pull-left">
                                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <div id="pos_modalBody_posPayment_sales_void_report2" class="reportContainer"
                         style="min-height: 200px;overflow: auto;">
                        <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the
                            Generate
                            Report
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_template" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 400px">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template" style="min-height: 400px;">

            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm btn-block" data-dismiss="modal">
                    <i class="fa fa-times text-red" aria-hidden="true"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {

        $("#cashier2").multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#cashier2").multiselect2('selectAll', false);
        $("#cashier2").multiselect2('updateButtonText');


        $("#outletID_f").multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });

        $("#outletID_f").multiselect2('selectAll', false);
        $("#outletID_f").multiselect2('updateButtonText');

        $("#orderType").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#orderType").multiselect2('selectAll', false);
        $("#orderType").multiselect2('updateButtonText');

        $("#orderType2").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#orderType2").multiselect2('selectAll', false);
        $("#orderType2").multiselect2('updateButtonText');

        $('#customer').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customer").multiselect2('selectAll', true);
        $("#customer").multiselect2('updateButtonText');


        $("#frm_salesReport2").submit(function (e) {
            loadPaymentSalesReport_ajax2();
            return false;
        });


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

        loadCashier();
        loadCashier_voidbill();
        /*
        * Void Report filter
        * */
        $("#cashier3").multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#cashier3").multiselect2('selectAll', false);
        $("#cashier3").multiselect2('updateButtonText');


        $("#outletID_f3").multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });

        $("#outletID_f3").multiselect2('selectAll', false);
        $("#outletID_f3").multiselect2('updateButtonText');

        $('#customer3').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customer3").multiselect2('selectAll', true);
        $("#customer3").multiselect2('updateButtonText');


        $("#frm_salesVoidReport2").submit(function (e) {
            loadPaymentSalesVoidReport_ajax2();
            return false;
        });


        /*$('#sr_fromDate').on('dp.change', function (e) {

         var datetime = $('#sr_fromDate').val();
         var splitDate = datetime.split(' ');
         if ($("#tmpFromDate").val() != splitDate[0]) {
         var setDatetime = splitDate[0] + ' 12:00 AM';
         $('#sr_fromDate').val(setDatetime[0]).change();
         }

         $("#tmpFromDate").val(splitDate[0])

         })
         $('#sr_toDate').on('dp.change', function (e) {
         //var datetime = e.date._i;
         var datetime = $('#sr_toDate').val();
         var splitDate = datetime.split(' ');
         if ($("#tmpToDate").val() != splitDate[0]) {
         var setDatetime = splitDate[0] + ' 11:59 PM';
         $('#sr_toDate').val(setDatetime[0]).change();
         }

         $("#tmpToDate").val(splitDate[0])
         })*/
    })

    function loadPaymentSalesReport2() {
        var curDate = '<?php echo date('d-m-Y') ?>';
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = curDate + ' ' + hour + ":" + minute + " " + ampm;

        $("#filterTo2").val(date);


        $("#ps_outletID2").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_pos_detail_sales_report'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#title_paymentSales2").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#ps_outletID2").val($("#wareHouseAutoID").val());
                $("#rpos_Payment_sales_report2").modal('show');
                startLoadPos();
                $("#pos_modalBody_posPayment_sales_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posPayment_sales_report2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentSalesReport_ajax2() {
        var data = $("#frm_salesReport2").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_pos_detail_sales_report'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posPayment_sales_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_modalBody_posPayment_sales_report2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentSalesVoidReport_ajax2() {
        var data = $("#frm_salesVoidReport2").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_pos_detail_void_report'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posPayment_sales_void_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_modalBody_posPayment_sales_void_report2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function loadCashier() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier'); ?>",
            data: {warehouseAutoID: $('#outletID_f').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
                    $('#cashier_option').html(data);
                    $("#cashier2").multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true,
                        maxHeight: 400
                    });
                    $("#cashier2").multiselect2('selectAll', false);
                    $("#cashier2").multiselect2('updateButtonText');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function viewDrillDown_report(invoiceID, outletID) {
        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplate_salesDetailForReport'); ?>",
                data: {invoiceID: invoiceID, outletID: outletID},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#rpos_print_template').modal('show');
                    $("#pos_modalBody_posPrint_template").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
        } else {
            myAlert('e', 'Load the invoice and click again.')
        }
    }

    function loadCashier_voidbill() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier_voidbills'); ?>",
            data: {warehouseAutoID: $('#outletID_f3').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
                    $('#cashier_option3').html(data);
                    $("#cashier3").multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true,
                        maxHeight: 400
                    });
                    $("#cashier3").multiselect2('selectAll', false);
                    $("#cashier3").multiselect2('updateButtonText');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>
