<?php echo head_page('<i class="fa fa-bar-chart"></i> Sales Detail Report', false);
$locations = get_gpos_location_with_status();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
//$customer_arr = all_customer_drop_gpos(false, true);
//$customer_arr = array();
?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
    }

    .dropdown-check-list {
        display: inline-block;
    }

    .dropdown-check-list .anchor {
        position: relative;
        cursor: pointer;
        display: inline-block;
        padding: 5px 50px 5px 10px;
        border: 1px solid #ccc;
    }

    .dropdown-check-list .void_anchor {
        position: relative;
        cursor: pointer;
        display: inline-block;
        padding: 5px 50px 5px 10px;
        border: 1px solid #ccc;
    }

    .anchor {
        width: 100%;
    }

    .void_anchor {
        width: 100%;
    }

    #customer_search {
        width: 100%;
    }

    #void_customer_search {
        width: 100%;
    }

    ul.items {
        text-align: left;
    }

    .dropdown-check-list .anchor:after {
        position: absolute;
        content: "";
        border-left: 2px solid black;
        border-top: 2px solid black;
        padding: 5px;
        right: 10px;
        top: 20%;
        -moz-transform: rotate(-135deg);
        -ms-transform: rotate(-135deg);
        -o-transform: rotate(-135deg);
        -webkit-transform: rotate(-135deg);
        transform: rotate(-135deg);
    }

    .dropdown-check-list .void_anchor:after {
        position: absolute;
        content: "";
        border-left: 2px solid black;
        border-top: 2px solid black;
        padding: 5px;
        right: 10px;
        top: 20%;
        -moz-transform: rotate(-135deg);
        -ms-transform: rotate(-135deg);
        -o-transform: rotate(-135deg);
        -webkit-transform: rotate(-135deg);
        transform: rotate(-135deg);
    }

    .dropdown-check-list .anchor:active:after {
        right: 8px;
        top: 21%;
    }

    .dropdown-check-list .void_anchor:active:after {
        right: 8px;
        top: 21%;
    }

    .dropdown-check-list ul.items {
        padding: 2px;
        display: none;
        margin: 0;
        border: 1px solid #ccc;
        border-top: none;
    }

    .dropdown-check-list ul.items li {
        list-style: none;
    }

    .dropdown-check-list.visible .anchor {
        color: #0094ff;
    }

    .dropdown-check-list.visible .void_anchor {
        color: #0094ff;
    }

    .dropdown-check-list.visible .items {
        display: block;
    }
    #salesDetailsReportV2 tbody tr td:nth-child(7),#salesDetailsReportV2 tbody tr td:nth-child(8),#salesDetailsReportV2 tbody tr td:nth-child(9),#salesDetailsReportV2 tbody tr td:nth-child(10),
    #salesDetailsReportV2 tbody tr td:nth-child(11),#salesDetailsReportV2 tbody tr td:nth-child(12),#salesDetailsReportV2 tbody tr td:nth-child(13),#salesDetailsReportV2 tbody tr td:nth-child(14){
        text-align:right;
    }
</style>

<div class="box-body" style="display: block;width: 100%">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#full_sales_detail_report" data-toggle="tab" aria-expanded="false">Sales Detail
                    Report</a>

            </li>
            <li class=""><a href="#void_sales_detail_report" data-toggle="tab" aria-expanded="true">Void Bills</a></li>
        </ul>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="full_sales_detail_report">

        <div id="filter-panel" class="collapse filter-panel"></div>
        <div>
            <div class="row">
                <?php echo form_open('login/loginSubmit', ' name="form_salesReturnDetailsReport" id="form_salesReturnDetailsReport" class="form-group" role="form"'); ?>
                    <input type="hidden" id="customerAutoID" name="customerAutoID" value="0">
                    <input type="hidden" id="ps_outletID2" name="outletID" value="0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-3">
                                <label class="" for="">Outlet</label>
                                <select class=" filters" multiple required name="outletID_f[]" id="outletID_f" onchange="loadCashier()">
                                    <?php
                                    foreach ($locations as $loc) {
                                        echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] . ' - ' . $loc['isActive'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">

                                    <label class="" for="">
                                        <?php echo $this->lang->line('posr_cashier'); ?>

                                    </label>
                                    <span id="cashier_option">
                                        <?php echo form_dropdown('cashier[]', get_cashiers_gpos(), '', 'multiple required id="cashier2"  class="form-control input-sm"'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('common_from'); ?>

                                    </label>
                                    <input type="hidden" id="tmpFromDate" value="">
                                    <input type="hidden" id="tmpToDate" value="">
                                    <input type="text" required class="form-control input-sm startdateDatepic" id="sr_fromDate" name="filterFrom" value="<?php echo date('d-m-Y 00:00:00') ?>" style="width: 130px;">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('common_to'); ?>

                                    </label>
                                    <input type="text" class="form-control input-sm startdateDatepic" id="sr_toDate" value="<?php echo date('d-m-Y 23:59:59') ?>" style="width: 130px;" name="filterTo" placeholder="To">

                                </div>
                            </div>

                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">



                            <div class="col-sm-3">
                                <div id="customerDropdown" class="dropdown-check-list" tabindex="100">
                                    <span class="anchor" id="Customer_drp_title">Select Customers</span>
                                    <ul class="items" id="customerList">
                                        <li><input type="text" id="customer_search" autocomplete="off" /></li>
                                        <li id="customer_select_all"><input type="checkbox" class="cus_checkbox selectAllOption" id="customer_select_all" onchange='selectCustomerAll(this)' value="-1">Select All Customers</li>
                                        <li><input type="checkbox" class="cus_checkbox cus_checkboxRemove cash_selected" value="0" onchange="customerTitleChange()">Cash</li>
                                    </ul>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary btn-sm pull-left">
                                    <?php echo $this->lang->line('posr_generate_report'); ?>

                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div id="detailsReportContainer">
                <div class="table-responsive" style="margin-top: 20px;">
                    <h4>Sales Details Report</h4>
                    <div class="row" style="margin-top: 5px">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <a href="#" class="btn btn-excel btn-xs" id="btn-excel" onclick="generateReportSalesDetailsExcel()">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <table id="salesDetailsReportV2" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">Date & Time</th>
                                <th style="width: 30%">Bill ID</th>
                                <th style="width: 30%">Outlet</th>
                                <th style="width: 10%">Customer</th>
                                <th style="width: 10%">Contact No</th>
                                <th style="width: 10%">Gross Total</th>
                                <th style="width: 10%">Total Discount</th>
                                <th style="width: 10%">VAT</th>
                                <th style="width: 10%">Other Tax</th>
                                <th style="width: 10%">Net Total</th>
                                <th style="width: 10%">Paid Amount</th>
                                <th style="width: 10%">Balance</th>
                                <th style="width: 10%">Return</th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tfoot>
                        <tr>
                                <th colspan="6" style="text-align:right">Total:</th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th style="text-align:right"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>


                <div class="table-responsive" style="margin-top: 20px;">
                    <h4>Refund Details</h4>
                    <table id="refund_salesDetailsReportV2" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th> #</th>
                                <th> Date &amp; Time</th>
                                <th> Document Code</th>
                                <th> Outlet</th>
                                <th> Refund Amount</th>
                                <th> Exchange Amount</th>
                                <th> Total</th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="4" style="text-align:right">Total:</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
            <br>
            <div style="font-weight: bold;" class="salesDetailFooterView" id="Total_refund_amount">Total Cash Collection (Cash-Refund): 0.00</div>
            <div style="margin:4px 0px" class="salesDetailFooterView">Report print by : <?php echo current_user() ?></div>

            <hr>
            <div id="pos_modalBody_posPayment_sales_report2" class="reportContainer" style="min-height: 200px;display:none;">
                <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
                    Report
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="void_sales_detail_report">

        <div id="filter-panel" class="collapse filter-panel"></div>
        <div>
            <div class="row">
                <form id="frm_salesReportVoid" method="post" class="form-inline text-center" role="form">
                    <input type="hidden" id="void_customerAutoID" name="void_customerAutoID" value="0">
                    <input type="hidden" id="void_ps_outletID" name="void_outletID" value="0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-3">
                                <label class="" for="">Outlet</label>
                                <select class=" filters" multiple required name="void_outletID_f[]" id="void_outletID_f" onchange="loadCashier()">
                                    <?php
                                    foreach ($locations as $loc) {
                                        echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] . ' - ' . $loc['isActive'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">

                                    <label class="" for="">
                                        <?php echo $this->lang->line('posr_cashier'); ?>

                                    </label>
                                    <span id="cashier_option">
                                        <?php echo form_dropdown('void_cashier[]', get_cashiers_gpos(), '', 'multiple required id="void_cashier"  class="form-control input-sm"'); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div id="void_customerDropdown" class="dropdown-check-list" tabindex="100">
                                    <span class="void_anchor" id="void_Customer_drp_title">Select Customers</span>
                                    <ul class="items" id="void_customerList">
                                        <li><input type="text" id="void_customer_search" autocomplete="off" /></li>
                                        <li id="void_customer_select_all"><input type="checkbox" class="void_cus_checkbox" id="void_customer_select_all" onchange='void_selectCustomerAll(this)' value="-1">Select All Customers</li>
                                        <li><input type="checkbox" class="void_cus_checkbox" value="0">Cash</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('common_from'); ?>

                                    </label>
                                    <input type="hidden" id="void_tmpFromDate" value="">
                                    <input type="hidden" id="void_tmpToDate" value="">
                                    <input type="text" required class="form-control input-sm startdateDatepic" id="void_sr_fromDate" name="void_filterFrom" value="<?php echo date('d-m-Y 00:00:00') ?>" style="width: 130px;">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('common_to'); ?>

                                    </label>
                                    <input type="text" class="form-control input-sm startdateDatepic" id="void_sr_toDate" value="<?php echo date('d-m-Y 23:59:59') ?>" style="width: 130px;" name="void_filterTo" placeholder="To">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-sm-12" style="margin-top: 5px;">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary btn-sm pull-right">
                                <?php echo $this->lang->line('posr_generate_report'); ?>

                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <hr>
            <div id="pos_modalBody_posPayment_void_sales_report2" class="reportContainer" style="min-height: 200px;">
                <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
                    Report
                </div>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="print_template" data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 420px">
        <div class="modal-content">
            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="newInvoice(1)">
                    <i class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Print </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template" style="height: 400px;overflow-y: auto;">
                <div id="wrapper">
                    <div id="print_content"></div>

                    <div id="bkpos_wrp" style="margin-top: 10px;">


                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-block btn-primary btn-flat" onclick="print_pos_report()" style="">
                    <i class="fa fa-print"></i> Print
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="newInvoice(1)">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="return_amounts_model" data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Returned Documents </h4>
            </div>
            <div class="modal-body">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Document Code</td>
                            <td>Amount</td>
                        </tr>
                    </thead>
                    <tbody id="pos_return_amount_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<script>
    var counterMaster_table;
    var refund_salesDetailsReportV2;
    $(document).ready(function(e) {
        $('.headerclose').click(function() {
            fetchPage('system/pos-general/reports/gpos_sales_detail_report', '', 'Sales Detail Report');
        });

        $("#salesDetailsReportV2").hide();
        $("#refund_salesDetailsReportV2").hide();
        $("#salesDetailFooterView").hide();

        $("#cashier2").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#cashier2").multiselect2('updateButtonText');

        $("#outletID_f").multiselect2({
            enableCaseInsensitiveFiltering: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        //$("#outletID_f").multiselect2('selectAll', false);
        $("#outletID_f").multiselect2('updateButtonText');
        $("#form_salesReturnDetailsReport").submit(function(e) {
            loadPaymentSalesReport_ajax2();
            counterMaster_table.draw();
            refund_salesDetailsReportV2.draw();
            totalCashCollected();
            return false;
        });
    });


    $("#frm_salesReportVoid").submit(function(e) {
        loadPaymentSalesReport_ajaxVoid();
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
    }).on('dp.change', function(ev) {
        $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
    });

    loadCashier();

    //================= Void bills ====================

    $("#void_outletID_f").multiselect2({
        enableFiltering: true,
        filterPlaceholder: 'Search Cashier',
        includeSelectAllOption: true
    });
    $("#void_outletID_f").multiselect2('selectAll', false);
    $("#void_outletID_f").multiselect2('updateButtonText');

    $("#void_cashier").multiselect2({
        enableFiltering: true,
        filterPlaceholder: 'Search Cashier',
        includeSelectAllOption: true
    });
    $("#void_cashier").multiselect2('selectAll', false);
    $("#void_cashier").multiselect2('updateButtonText');

    LoadSalesDetailsReportV2();
    LoadSalesDetailsReportV2_refund();


    function LoadSalesDetailsReportV2() {
        $("#salesDetailsReportV2").show();
        var selectedCustomers = $(".cus_checkbox:checked");
        var warehouses = $('#outletID_f').val();
        var cashiers = $('#cashier2').val();

        var fromdate = $("#sr_fromDate").val();
        var todate = $("#sr_toDate").val();



        counterMaster_table = $('#salesDetailsReportV2').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos/sales_details_report_v2'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "iDisplayLength": 100,
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                if (RLength == '' || RLength == 'NaN') {
                    RLength = 1;
                }

                // Total over all pages
                var GrossTotal = api.column(15).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0); 
                $(api.column(6).footer()).html((GrossTotal / RLength).toFixed(2));

                var totalDiscount = api.column(16).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(7).footer()).html((totalDiscount / RLength).toFixed(2));

                var totalVAT = api.column(17).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(8).footer()).html((totalVAT / RLength).toFixed(2));

                var totalOtherTax = api.column(18).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(9).footer()).html((totalOtherTax  / RLength).toFixed(2));

                var netTotal = api.column(19).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(10).footer()).html((netTotal / RLength).toFixed(2));

                var paidAmount = api.column(20).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(11).footer()).html((paidAmount / RLength).toFixed(2));

                var balance = api.column(21).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(12).footer()).html((balance / RLength).toFixed(2));

                var returnAmount = api.column(22).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(13).footer()).html((returnAmount / RLength).toFixed(2));
            },

            "aoColumns": [{
                    "mData": "invoiceID"
                },
                {
                    "mData": "createdDateTime"
                },
                {
                    "mData": "invoiceCode"
                },
                {
                    "mData": "wareHouseLocation"
                },
                {
                    "mData": "customernam"
                },
                {
                    "mData": "customerTelephone"
                },
                {
                    "mData": "subTotal"
                },
                {
                    "mData": "discount"
                },
                {
                    "mData": "amount"
                },
                {
                    "mData": "Otheramount"
                },
                {
                    "mData": "netTotal"
                },
                {
                    "mData": "paidAmount"
                },
                {
                    "mData": "balanceAmount"
                },
                {
                    "mData": "totalreturncol"
                },
                {
                    "mData": "viewbill"
                },
                {
                    "mData": "subTotalTot"
                },
                {
                    "mData": "discountTot"
                },
                {
                    "mData": "vatTotalTot"
                },
                {
                    "mData": "otherTaxTot"
                },
                {
                    "mData": "netTotalTot"
                },
                {
                    "mData": "paidAmountTot"
                },
                {
                    "mData": "balanceAmountTot"
                },
                {
                    "mData": "totalreturncolTot"
                }
            ],
            "columnDefs": [{
                "visible": false,
                "searchable": false,
                "targets": [15,16, 17, 18, 19,20,21,22]
            }],

            "fnServerData": function(sSource, aoData, fnCallback) {
                var selectedCustomers = $(".cus_checkbox:checked");
                var cusArray = [];
                selectedCustomers.each(function(index) {
                    cusArray.push($(this).context.defaultValue);
                });
                aoData.push({
                    "name": "customers",
                    "value": cusArray
                });
                aoData.push({
                    "name": "warehouses",
                    "value": $('#outletID_f').val()
                });
                aoData.push({
                    "name": "cashiers",
                    "value": $('#cashier2').val()
                });
                aoData.push({
                    "name": "fromdate",
                    "value": $("#sr_fromDate").val()
                });
                aoData.push({
                    "name": "todate",
                    "value": $("#sr_toDate").val()
                });

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': function(json) {
                        RLength = json.aaData.length;
                        fnCallback(json);
                    }
                });
            }

        });
    }

    var checkList = document.getElementById('customerDropdown');
    checkList.getElementsByClassName('anchor')[0].onclick = function(evt) {
        if (checkList.classList.contains('visible')) {
            checkList.classList.remove('visible');
        } else {
            checkList.classList.add('visible');
        }
    }

    var void_checkList = document.getElementById('void_customerDropdown');
    void_checkList.getElementsByClassName('void_anchor')[0].onclick = function(evt) {
        if (void_checkList.classList.contains('visible')) {
            void_checkList.classList.remove('visible');
        } else {
            void_checkList.classList.add('visible');
        }
    }


    $("#void_customer_search").keyup(function() {
        load_void_customer_dropdown($("#void_customer_search").val());
    });


    $("#customer_search").keyup(function() {
        load_customer_dropdown($("#customer_search").val());
    });



    var currentRequest = null;

    function load_void_customer_dropdown(skey) {


        currentRequest = $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Pos/load_customer_dropdown'); ?>",
            data: {
                skey: skey
            },
            beforeSend: function() {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function(data) {

                var Name = "";
                var ID = "";
                var options = null;

                data.customers.forEach(function(item, index) {
                    Name = item.customerSystemCode + ' | ' + item.customerName;
                    ID = item.customerAutoID;
                    $("#void_customerList").append('<li id="' + ID + '"><input type="checkbox" class="void_cus_checkbox" value="' + ID + '" onchange="void_customerTitleChange()">' + Name + '</li>');

                });

                var map = {};
                $('#void_customerList li').each(function() {
                    if (map[this.id]) {
                        $(this).remove()
                    }
                    map[this.id] = true;
                });
                $("#void_customerList").append('<li><input type="checkbox" class="void_cus_checkbox" value="0" onchange="void_customerTitleChange()">Cash</li>');

            }
        });
    }

    function load_customer_dropdown(skey) {
        selected = '';
        if($('.cash_selected').is(':checked')){
            selected = 'checked';
        }
        $('.selectAllOption').attr('checked', false);
        currentRequest = $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Pos/load_customer_dropdown'); ?>",
            data: {
                skey: skey
            },
            beforeSend: function() {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function(data) {
                var Name = "";
                var ID = "";
                var options = null;

                data.customers.forEach(function(item, index) {
                    Name = item.customerSystemCode + ' | ' + item.customerName;
                    ID = item.customerAutoID;
                    $("#customerList").append('<li id="' + ID + '"><input type="checkbox" class="cus_checkbox cus_checkboxRemove" value="' + ID + '" onchange="customerTitleChange()">' + Name + '</li>');

                });

                var map = {};
                $('#customerList li').each(function() {
                    if (map[this.id]) {
                        $(this).remove()
                    }
                    map[this.id] = true;
                });
                $("#customerList").append('<li><input type="checkbox" class="cus_checkbox cus_checkboxRemove cash_selected" onchange="customerTitleChange()" '+selected+' value="0">Cash</li>');
            }
        });
    }

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
            url: "<?php echo site_url('Pos_general_report/loadPaymentSalesReportAdmin'); ?>",
            data: {
                id: null
            },
            cache: false,
            beforeSend: function() {
                $("#title_paymentSales2").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#ps_outletID2").val($("#wareHouseAutoID").val());
                $("#rpos_Payment_sales_report2").modal('show');
                startLoadPos();
                $("#pos_modalBody_posPayment_sales_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view'); ?></div>');

            },
            success: function(data) {
                stopLoad();
                $("#pos_modalBody_posPayment_sales_report2").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentSalesReport_ajaxVoid() {

        var selectedCustomers = $(".void_cus_checkbox:checked");
        var cusArray = [];
        selectedCustomers.each(function(index) {
            cusArray.push($(this).context.defaultValue);
        });
        $("#void_customerAutoID").val(cusArray.join());
        var data = $("#frm_salesReportVoid").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_general_report/load_gpos_detail_void_sales_report'); ?>",
            data: data,
            cache: false,
            beforeSend: function() {
                $("#pos_modalBody_posPayment_void_sales_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view'); ?></div>');
            },
            success: function(data) {
                $("#pos_modalBody_posPayment_void_sales_report2").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentSalesReport_ajax2() {

        var selectedCustomers = $(".cus_checkbox:checked");
        var cusArray = [];
        selectedCustomers.each(function(index) {
            cusArray.push($(this).context.defaultValue);
        });
        $("#customerAutoID").val(cusArray.join());
        var data = $("#form_salesReturnDetailsReport").serialize();

        var cusDropdwn = document.getElementById('customerDropdown');
        cusDropdwn.classList.remove('visible');
        $(".cus_checkboxRemove:not(:checked)").parent().remove();
        $("#customer_search").val('');
    }

    function load_refund_sales_report() {
        var data = $("#form_salesReturnDetailsReport").serialize();
    }

    function loadCashier() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_general_report/get_gpos_outlet_cashier'); ?>",
            data: {
                warehouseAutoID: $('#outletID_f').val()
            },
            cache: false,
            beforeSend: function() {

            },
            success: function(data) {
                if (!$.isEmptyObject(data)) {
                    $('#cashier_option').html(data);
                    $("#cashier2").multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    // $("#cashier2").multiselect2('selectAll', false);
                    $("#cashier2").multiselect2('updateButtonText');
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function invoicePrint(invID, invCode, doSysCode_refNo) {
        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            data: {
                'doSysCode_refNo': doSysCode_refNo,
                'receipt': 1,
                'isVoid': 0
            },
            url: "<?php echo site_url('Pos/invoice_print'); ?>/" + invID + "/" + invCode,
            success: function(data) {
                $('#print_template').modal({
                    backdrop: 'static'
                });
                $('#print_content').html(data);
                $("#gposvoidbillbtn").addClass('hidden');
            },
            error: function(xhr) {
                myAlert('e', 'Error in print call. ' + xhr.status + ': ' + xhr.statusText)
            }
        });
    }

    function print_pos_report() {
        $.print("#print_content");
        return false;
    }

    function load_return_pos_invoices(invoiceID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_general_report/load_return_pos_invoices'); ?>",
            data: {
                'invoiceID': invoiceID
            },
            cache: false,
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $('#pos_return_amount_body').empty();
                x = 1;
                $.each(data, function(key, value) {
                    $('#pos_return_amount_body').append('<tr><td>' + x + '</td><td>' + value['documentSystemCode'] + '</td><td>' + value['netTotal'] + '</td></tr>');
                    x++;
                });

                $('#return_amounts_model').modal({
                    backdrop: 'static'
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_statusbased_customer(tab) {
        if (tab == 1) {
            var status_filter = $('#status_filter_customer').val();

        } else {
            var status_filter = $('#void_status_filter_customer').val();

        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                type: 1,
                activeStatus: status_filter,
                tab: tab
            },
            url: "<?php echo site_url('Pos_general_report/load_statusbased_customer_gpos'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (tab == 1) {
                    $('#div_load_customers').html(data);
                } else {
                    $('#void_div_load_customers').html(data);
                }
                stopLoad();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function LoadSalesDetailsReportV2_refund() {
        $("#refund_salesDetailsReportV2").show();
        var warehouses = $('#outletID_f').val();
        var cashiers = $('#cashier2').val();

        var fromdate = $("#sr_fromDate").val();
        var todate = $("#sr_toDate").val();

        refund_salesDetailsReportV2 = $('#refund_salesDetailsReportV2').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos/refund_sales_details_report_v2'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "iDisplayLength": 100,
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                if (RLength == '' || RLength == 'NaN') {
                    RLength = 1;
                }

                var refund_refund = api.column(9).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(4).footer()).html((refund_refund / RLength).toFixed(2));

                var exchange_refund = api.column(10).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(5).footer()).html((exchange_refund / RLength).toFixed(2));

                var netTotal_refund = api.column(8).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(6).footer()).html((netTotal_refund / RLength).toFixed(2));

            },

            "aoColumns": [{
                    "mData": "salesReturnID"
                },
                {
                    "mData": "createdDateTime"
                },
                {
                    "mData": "documentSystemCode"
                },
                {
                    "mData": "wareHouseLocation"
                },
                {
                    "mData": "refund"
                },
                {
                    "mData": "exchange"
                },
                {
                    "mData": "netTotal"
                },
                {
                    "mData": "viewreturnbillcolumn"
                },
                {
                    "mData": "netTotalTot"
                },
                {
                    "mData": "refundTot"
                },
                {
                    "mData": "exchangeTot"
                }
            ],
            "columnDefs": [{
                "visible": false,
                "searchable": false,
                "targets": [8, 9, 10]
            }],

            "fnServerData": function(sSource, aoData, fnCallback) {
                var selectedCustomers = $(".cus_checkbox:checked");
                var cusArray = [];
                selectedCustomers.each(function(index) {
                    cusArray.push($(this).context.defaultValue);
                });
                aoData.push({
                    "name": "customers",
                    "value": cusArray
                });
                aoData.push({
                    "name": "warehouses",
                    "value": $('#outletID_f').val()
                });
                aoData.push({
                    "name": "cashiers",
                    "value": $('#cashier2').val()
                });
                aoData.push({
                    "name": "fromdate",
                    "value": $("#sr_fromDate").val()
                });
                aoData.push({
                    "name": "todate",
                    "value": $("#sr_toDate").val()
                });

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': function(json) {
                        RLength = json.aaData.length;
                        fnCallback(json);
                    }
                });
            }
        });
    }

    function return_print(returnID, returnCode) {
        exchangePrint(returnID, returnCode);
    }

    function exchangePrint(returnID, returnCode, returnMode = 'exchange') {
        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            url: "<?php echo site_url('Pos/return_print'); ?>/" + returnID + "/" + returnCode,
            data: {
                returnMode: returnMode
            },
            success: function(data) {
                $('#print_template').modal({
                    backdrop: 'static'
                });
                $('#print_content').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function selectCustomerAll(checkbox)
    {
        if (checkbox.checked == true) {
            $(".cus_checkbox").prop("checked", true);
            var selectedCustomers = $(".cus_checkbox:checked");
            $("#Customer_drp_title").html((selectedCustomers.length - 1) + ' Customers Selected');
        } else {
            $(".cus_checkbox").prop("checked", false);
            $("#Customer_drp_title").html('Select Customer');
        }
    }

    function customerTitleChange() 
    {    
        selected = 0;
        if($('#customer_select_all').is(':checked')){
            selected = 1;
        }
        var selectedCustomers = $(".cus_checkbox:checked");
        if (selectedCustomers.length > 0) {
            $("#Customer_drp_title").html((selectedCustomers.length - selected) + ' Customers Selected');
        } else {
            $("#Customer_drp_title").html('Select Customer');
        }
    }

    function void_selectCustomerAll(checkbox) {
        if (checkbox.checked == true) {
            $(".void_cus_checkbox").prop("checked", true);
            var selectedCustomers = $(".void_cus_checkbox:checked");
            $("#void_Customer_drp_title").html((selectedCustomers.length - 1) + ' Customers Selected');
        } else {
            $(".void_cus_checkbox").prop("checked", false);
            $("#void_Customer_drp_title").html('Select Customer');
        }
    }

    function void_customerTitleChange() {
        var selectedCustomers = $(".void_cus_checkbox:checked");
        if (selectedCustomers.length > 0) {
            $("#void_Customer_drp_title").html((selectedCustomers.length) + ' Customers Selected');
        } else {
            $("#void_Customer_drp_title").html('Select Customer');
        }
    }

    function generateReportSalesDetailsExcel()
    {
        $("#customerAutoID").val('');
        var selectedCustomers = $(".cus_checkbox:checked");
        var cusArray = [];
        selectedCustomers.each(function(index) {
            cusArray.push($(this).context.defaultValue);
        });
        $("#customerAutoID").val(cusArray.join());

        var form = document.getElementById('form_salesReturnDetailsReport');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#form_salesReturnDetailsReport').serializeArray();
        form.action = '<?php echo site_url('Pos/sales_details_report_v2_excel'); ?>';
        form.submit();
    }

    var ignoreClickOnMeElement = document.getElementById('customerDropdown');
    document.addEventListener('click', function(event) {
        var isClickInsideElement = ignoreClickOnMeElement.contains(event.target);
        if (!isClickInsideElement) {
            ignoreClickOnMeElement.classList.remove('visible');
        }

        var selectAllOption = $(".selectAllOption:checked");
        var selectedCustomers = $(".cus_checkbox:checked");
        if (selectedCustomers.length > 0) {
            $("#Customer_drp_title").html((selectedCustomers.length) - selectAllOption.length + ' Customers Selected');
        } else {
            $("#Customer_drp_title").html('Select Customer');
        }
    });

    function totalCashCollected()
    {
        
        $("#salesDetailFooterView").show();
        var selectedCustomers = $(".cus_checkbox:checked");
        var cusArray = [];
        selectedCustomers.each(function(index) {
            cusArray.push($(this).context.defaultValue);
        });
             
        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            url: "<?php echo site_url('Pos/total_cash_collected'); ?>",
            data: {
                customers: cusArray,
                warehouses: $("#outletID_f").val(),
                cashiers: $("#cashier2").val(),
                fromdate: $("#sr_fromDate").val(),
                todate: $("#sr_toDate").val()
            },
            success: function(data) {
                $('#Total_refund_amount').html("Total Cash Collection (Cash-Refund): " + data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }
</script>
