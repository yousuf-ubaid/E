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
    .total_row{
        font-size: medium !important;
        font-weight: bold;
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
    <button type="button" id="btn_print_sales3" class="btn btn-default btn-xs"> <i
                class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>

        <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Gift_Card_Report.xls"
           onclick="var file = tableToExcel('container_gift_card_topup_redeem_report3', 'Gift Card Top Up Redeem Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<?php } ?>
<div id="container_gift_card_topup_redeem_report3">
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outletID_f3');
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


    <h3 class="text-center">Top-up / Redeem Report</h3>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>
            <strong>
                <?php
                $filterFrom = $this->input->post('filterFrom3');
                if (!empty($filterFrom)) {
                    echo '  <i> From: </i><span id="to_date">' . $filterFrom . '</span>';
                }
                ?>
            </strong><br/>
            <strong>
                <?php
                $filterTo = $this->input->post('filterTo3');
                $today = $this->lang->line('posr_today');
                if (!empty($filterTo)) {
                    echo '  <i> To: </i><span id="to_date">' . $filterTo . '</span>';
                }
                ?>
            </strong>
        </div>
    </div>

    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">
        <table class="<?php echo table_class_pos(5) ?>">
            <thead>
            <tr>
                <th>Card Number</th>
                <th>Outlet Name</th>
                <th>Customer Name</th>
                <th>Mobile No</th>
                <th>Top-up</th>
                <th>Refund</th>
                <th>Redeem</th>
                <th>Issued By</th>
            </tr>
            </thead>
            <tbody id="top-up_redeem_rows">
            <?php
            if (!empty($gift_card_details)) {

                $total_topup = 0;
                $total_refund = 0;
                $total_redeem = 0;

                foreach ($gift_card_details as $row) {
                    $topup = 0;
                    $refund = 0;
                    $redeem = 0;
                    if($row['isRefund']==1){
                        $refund = $row['amount'];
                        $total_refund+=$row['amount'];
                    }elseif ($row['isRefund']==0 && $row['amount']>0){
                        $topup = $row['amount'];
                        $total_topup+=$row['amount'];
                    }elseif ($row['isRefund']==0 && $row['amount']<0){
                        $redeem = $row['amount'];
                        $total_redeem+=$row['amount'];
                    }

                    ?>
                    <tr style="cursor: pointer"
                            data-card="<?php echo $row['barcode']; ?>"
                            invoice="<?php echo $row['invoice']; ?>"
                            receipt="<?php echo $row['receipt']; ?>"
                            outletID="<?php echo $row['outletID']; ?>" >

                        <td><?php echo $row['barcode'] ?></td>
                        <td><?php echo $row['wareHouseDescription'] ?></td>
                        <td><?php echo $row['CustomerName'] ?></td>
                        <td><?php echo $row['customerTelephone'] ?></td>
                        <td class="text-center"><?php echo get_numberFormat($topup) ?></td>
                        <td class="text-center"><?php echo get_numberFormat($refund) ?></td>
                        <td class="text-center"><?php echo get_numberFormat($redeem) ?></td>
                        <td><?php echo $row['issued_user'] ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr style="background: #DEDEDE;">
                   <td class="total_row" colspan="4"><strong>Total</strong></td>
                   <td class="total_row text-right"><?php echo number_format($total_topup,2)?></td>
                   <td class="total_row text-right"><?php echo number_format($total_refund,2)?></td>
                   <td class="total_row text-right"><?php echo number_format($total_redeem,2)?></td>
                    <td></td>
                </tr>
                <?php
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


<!--show invoice for redeem card-->
<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_topup_redeem_template" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 400px">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body" id="topup_redeem_Print_template" style="min-height: 400px;">

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
<div aria-hidden="true" role="dialog" id="pos_giftCardInvoiceBill" class="modal" data-keyboard="true"
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
                <div id="receiptContainer3">
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
        $("#btn_print_sales3").click(function (e) {
            $.print("#container_gift_card_topup_redeem_report3");
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
    });

    //show receipt or invoice
    $('#top-up_redeem_rows').on('click', 'tr', function () {
        var barcode = $(this).attr('data-card');
        var giftCardReceiptID = $(this).attr('receipt');
        var menuSalesID = $(this).attr('invoice');
        var outletID = $(this).attr('outletID');

        if (barcode) {
            if (giftCardReceiptID != 0) {
                viewTopUpReceipt(giftCardReceiptID, barcode);
            } else if (menuSalesID != 0) {
                viewRedeemInvoice(menuSalesID, outletID);
            }
        }

    });

    //show invoice
    function viewRedeemInvoice(invoice, outletID) {
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
                $('#rpos_print_topup_redeem_template').modal('show');
                $("#topup_redeem_Print_template").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
            }
        });
    }

    //show receipt
    function viewTopUpReceipt(receiptID, barcode) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {receiptID: receiptID, barcode: barcode},
            url: "<?php echo site_url('Pos_giftCard/load_giftCard_receipt'); ?>",
            beforeSend: function () {
                $(".giftCardLoader").show();
                $("#receiptContainer3").html('');
                $("#pos_giftCardInvoiceBill").modal('show');

            },
            success: function (data) {
                $(".giftCardLoader").hide();
                $("#receiptContainer3").html(data);

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


</script>