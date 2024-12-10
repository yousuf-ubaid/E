<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .outletInfo {

    }

    .subHeadingTitle {
        margin-top: 10px;
        color: #cd3b43;
        font-size: 15px;
        font-weight: bold;
        text-decoration: underline;
    }
    .clickable {
        cursor: pointer;
    }
</style>
<?php
if (!isset($pdf)) {
    ?>
    <style>
        .customPad {
            padding: 3px 0px;
        }
    </style>
    <span class="pull-right">
    <button type="button" id="btn_print_sales2" class="btn btn-default btn-xs"> <i
                class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>
        <!--        <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePaymentSalesReportPdf()">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                </button>-->
        <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Gift_Card_Report.xls"
           onclick="var file = tableToExcel('container_gift_card_report3', 'Gift Card Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<?php } ?>
<div id="container_gift_card_report3">
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outletID_f');
                echo get_outletFilterInfo($outletInput);

                if (isset($outletID) && !empty($outletID)) {
                    $tmpArrayout = array();
                    foreach ($outletID as $c) {
                        $tmpArrayout[] = get_outletInfo_byid2($c);
                    }
                }
                ?>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="pull-right"><?php echo $this->lang->line('common_date'); ?><!--Date-->:
                <strong><?php echo date('d/m/Y'); ?></strong>
                <br/><?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong>
                    <span class="pcCurrentTime"></span></strong>
            </div>
        </div>
    </div>

    <hr style="margin:2px 0px;">


    <h3 class="text-center"><?php echo $this->lang->line('pos_gift_card_report'); ?><!--Sales Report--> </h3>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>

            <?php //echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date--> <strong>
                <?php
                $filterTo = $this->input->post('filterTo');
                $today = $this->lang->line('posr_today');
                if (!empty($filterTo)) {
                    echo '  <i> As of Date: </i><span id="to_date">'.$filterTo.'</span>';
                }
                ?>
            </strong>
        </div>
    </div>

    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">
        <div class="subHeadingTitle">Gift Card Details</strong></div>
        <table class="<?php echo table_class_pos(5) ?>">
            <thead>
            <tr>
                <th>Card Number</th>
                <th>Outlet Name</th>
                <th>Customer Name</th>
                <th>Mobile No</th>
                <th>Top Up</th>
                <th>Redeemed</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Issued By</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($gift_card_details)) {
                foreach ($gift_card_details as $row) {
                    ?>
                    <tr data-card="<?php echo $row['barcode']; ?>"
                        <?php if ($row['cardIssueID'] <= 0) {
                            echo "class='danger'";
                        } else {
                            echo "class='show-card-details' style='cursor: pointer'";
                        } ?>
                    >
                        <td><?php echo $row['barcode'] ?></td>
                        <td><?php echo $row['wareHouseDescription'] ?></td>
                        <td><?php echo $row['CustomerName'] ?></td>
                        <td><?php echo $row['customerTelephone'] ?></td>
                        <td class="text-right"><?php echo number_format($row['topup_amount'], 2) ?></td>
                        <td class="text-right"><?php echo number_format($row['redeemed_amount'], 2) ?></td>
                        <td class="text-right"><?php echo number_format($row['total_amount'], 2) ?></td>
                        <td class="text-center">
                            <?php if ($row['cardIssueID'] <= 0) {
                                echo "Not Issued";
                            } else {
                                echo "Issued";
                            } ?>
                        </td>
                        <td><?php echo $row['issued_user'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>

    <hr>
    <div style="margin:4px 0px; ">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>
</div>

<!--Drill down report modal-->
<div aria-hidden="true" role="dialog" id="pos_giftCardHistory" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-history"></i> Gift Card Transaction Details
                    <span id="giftCardLoader" style="display: none;"
                          class="pull-right">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="<?php echo table_class_pos(); ?>" id="giftCardPaymentHistory">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time <i class="fa fa-clock-o"></i></th>
                            <th>Invoice No</th>
                            <th>Outlet</th>
                            <th>Description</th>
                            <th>Amount <i class="fa fa-money"></i></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>
<!--End of Drill down report modal-->

<!--show invoice for redeem card-->
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
<!--end of show invoice for redeem invoice-->

<!--show receipt for card topup-->
<div aria-hidden="true" role="dialog" id="pos_giftCardBill" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 400px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Receipt
                    <span style="display: none;"
                          class="pull-right giftCardLoader">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div id="gc_receiptContainer">
                    &nbsp;
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default btn-block" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>
<!--end of show receipt for card topup-->

<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<script>

    $(document).ready(function (e) {
        $("#btn_print_sales2").click(function (e) {
            $.print("#container_gift_card_report3");
        });
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


        date = hour + ":" + minute + " " + ampm;
        $(".pcCurrentTime").html(date);
    })

    $("#container_gift_card_report3").on('click', '.show-card-details', function (e) {
        var barcode = $(this).data('card');
        var to_date = $('#to_date').text();
        $("#pos_giftCardHistory").modal('show');
        loadCardTransactionHistory(barcode,to_date);
    });

    function loadCardTransactionHistory(barCode,to_date) {
        $('#giftCardPaymentHistory').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadHistoryGiftCard'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "cardTopUpID"},
                {"mData": "gc_date"},
                {"mData": "gc_time"},
                {"mData": "invoice_code"},
                {"mData": "gc_outlet"},
                {"mData": "description"},
                {"mData": "gc_amount"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'barCode', 'value': barCode});
                aoData.push({'name': 'to_date', 'value': to_date});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            'createdRow': function( row, data, dataIndex ) {
                $(row).attr('bc', barCode);
                $(row).attr('receipt', data.receipt);
                $(row).attr('invoice', data.invoice);
                $(row).attr('outletID', data.outletID);
                $(row).addClass('clickable');
            }
        });
        
        //show receipt or invoice
        $('#giftCardPaymentHistory').on('click','tr',function () {
            var barcode = $(this).attr('bc');
            var giftCardReceiptID = $(this).attr('receipt');
            var menuSalesID = $(this).attr('invoice');
            var outletID = $(this).attr('outletID');

            if(barcode){
                if(giftCardReceiptID != 0){
                    viewTopUpReceipt(giftCardReceiptID,barcode);
                }else if(menuSalesID != 0){
                    viewRedeemInvoice(menuSalesID,outletID);
                }
            }

        });

        //show invoice
        function viewRedeemInvoice(invoice,outletID){
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplate_salesDetailForReport'); ?>",
                data: {invoiceID: invoice, outletID: outletID},
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
        }

        //show receipt
        function viewTopUpReceipt(receiptID, barcode){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {receiptID: receiptID, barcode: barcode},
                url: "<?php echo site_url('Pos_giftCard/load_giftCard_receipt'); ?>",
                beforeSend: function () {
                    $(".giftCardLoader").show();
                    $("#gc_receiptContainer").html('');
                    $("#pos_giftCardBill").modal('show');

                },
                success: function (data) {
                    $(".giftCardLoader").hide();
                    $("#gc_receiptContainer").html(data);

                }, error: function () {
                    $(".giftCardLoader").hide();
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
        }
    }

</script>