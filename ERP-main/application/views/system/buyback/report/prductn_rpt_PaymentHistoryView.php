<?php if ($master['isclosed'] == 1) { ?>
    <div class="row">
        <div class="col-md-6">
            <div style="font-size: 16px; font-weight: 700;padding-left: 4%">Payment History</div>
        </div>
        <div class="col-md-6" style="padding-right: 5%">
            <?php if ($type == 'html') {
                ?>
                <button class="btn btn-pdf btn-xs pull-right" id="btn-pdf" type="button" style="margin-left: 2px"
                        onclick="generatePaymentHistoryPdf()">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                </button>
                <?php
                echo export_buttons('tbl_payment_history', 'Payment History', true, false);
            } ?>
        </div>
    </div>
    <br>
        <div class="table-responsive" style="margin-left: 4%">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td width="15%"><strong>Farmer </td>
                    <td width="5%"><strong>:</strong></td>
                    <td><?php echo $master['farmName'] . " ( " . $master['farmCode'] . " ) " ; ?></td>

                    <td width="15%"><strong>Batch Code</strong></td>
                    <td width="5%"><strong>:</strong></td>
                    <td><?php echo $master['batchCode']; ?></td>
                </tr>
                <tr>
                    <td width="15%"><strong>Start Date</td>
                    <td width="5%"><strong>:</strong></td>
                    <td><?php echo $master['batchStartDate']; ?></td>

                    <td width="15%"><strong>Batch Age</strong></td>
                    <td width="5%"><strong>:</strong></td>
                    <td><?php
                        $chicksAge = $this->db->query("SELECT dpm.dispatchedDate,batch.closedDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 LEFT JOIN srp_erp_buyback_batch batch ON batch.batchMasterID = dpm.batchMasterID WHERE dpm.batchMasterID ={$master['batchMasterID']} ORDER BY dpm.dispatchAutoID ASC")->row_array();
                        if (!empty($chicksAge)) {
                            $dStart = new DateTime($chicksAge['dispatchedDate']);
                            if ($chicksAge['closedDate'] != ' ') {
                                $dEnd = new DateTime($chicksAge['closedDate']);
                            } else {
                                $dEnd = new DateTime(current_date());
                            }
                            $dDiff = $dStart->diff($dEnd);
                            $newFormattedDate = $dDiff->days + 1;
                            echo $newFormattedDate;
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    if (!empty($voucherDetails)) {
        ?>
        <div id="tbl_purchase_order_list" style="padding-left: 4%; padding-right: 4%">
            <br>
            <div class="row" style="margin-top: 10px">
                <div class="col-md-12">
                    <div class="fixHeader_Div">
                        <table class="borderSpace report-table-condensed" id="tbl_payment_history">
                            <thead class="report-headercolor">
                            <tr>
                                <th>Document</th>
                                <th>Date</th>
                                <th>Narration</th>
                                <th>Amount</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            echo "<tr>";
                            echo "<td colspan='4' ></td>";
                            $wages = wagesPayableAmount($batchID, False);
                            $wagesPayable = $wages['transactionAmount'];
                            $amount = number_format($wagesPayable, 2);
                            echo "<td style='text-align: right'>" . $amount . "</td>";
                            echo "</tr>";

                            $balanceAmout = $wagesPayable;
                            foreach ($voucherDetails as $val) {
                                echo "<tr>";
                                echo "<td><a onclick='documentPageView_modal( \"BBPV\"," . $val['pvMasterAutoID'] . ")'>" . $val["documentSystemCode"] . "</a></td>";
                                echo "<td>" . $val["documentDate"] . "</td>";
                                echo "<td>" . $val["PVNarration"] . "</td>";
                                echo "<td style='text-align: right'>" . number_format($val['transactionAmount'], 2) . "</td>";

                                $balanceAmout = $balanceAmout - $val['transactionAmount'];

                                echo "<td style='text-align: right'>" . number_format($balanceAmout, 2) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <hr>
                            <tr>
                                <td colspan="3"></td>
                                <td>Balance</td>
                                <td style='text-align: right'
                                    class="text-center reporttotal"><?php echo number_format($balanceAmout, 2); ?></td>
                            </tr>

                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <?php
    } else {
        echo warning_message("No Records Found!");
    }
}
    ?>

