<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage); ?>

<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs mainpanel">
        <li class="btn-default-new btn-xs tab-style-one mr-1 stepOneClass active">
            <a class="buybackTab" data-id="0" href="#pulledDocOne" data-toggle="tab" aria-expanded="true">
                <span><i class="fa fa-list tachometerColor" aria-hidden="true"
                         style="color: #fff;font-size: 16px;"></i>&nbsp;&nbsp;Pulled Documents</span>
            </a>
        </li>
        <li class="btn-default-new btn-xs tab-style-one mr-1 stepTwoClass">
            <a class="buybackTab" data-id="0" href="#purchaseDocTwo" data-toggle="tab" aria-expanded="true">
                <span><i class="fa fa-list tachometerColor" aria-hidden="true"
                         style="color: #fff;font-size: 16px;"></i>&nbsp;Purchase Requests</span>
            </a>
        </li>
    </ul>
</div>
<br>
<div class="tab-content pb-5">
    <div id="pulledDocOne" class="tab-pane active">
        <div>
            <table id="tbl_rpt_pulledJobsdrilldown" class="borderSpace report-table-condensed" style="width: 100%">
                <thead class="report-header">
                <tr>
                    <th>#</th>
                    <th>Document Code</th>
                    <th>Document</th>
                    <th>Document Date</th>
                    <th>Status</th>
                    <th>Currency</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total = 0;
                $decimalPlaces = 0;
                if ($details['pulledDoc']) {
                    $a = 1;
                    foreach ($details['pulledDoc'] as $val) {
                        $total += $val["transactionAmount"];
                        $decimalPlaces = $val["transactionCurrencyDecimalPlaces"];
                        ?>
                        <tr class='hoverTr'>
                            <td><?php echo $a ?></td>
                            <td><a href="#" class="drill-down-cursor"
                                   onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentAutoID"] ?>)"><?php echo $val["DocumentCode"] ?></a>
                            </td>
                            <td><?php echo $val["documentID"] ?></td>
                            <td><?php echo $val["documentDate"] ?></td>
                            <td class="text-center"><?php if ($val['approvedYN'] == 1) {
                                    echo '<span class="label label-success">Approved</span>';
                                } else {
                                    echo '<span class="label label-danger">Not Approved</span>';
                                } ?></td>
                            <td><?php echo $val["transactionCurrency"] ?></td>
                            <td style="text-align: right"><?php echo number_format($val["transactionAmount"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                        </tr>
                        <?php
                        $a++;
                    } ?>
                    <!--<tr>
                            <td colspan="4"><b><?php /*echo $this->lang->line('common_total'); */ ?></b></td>
                            <td class="text-right reporttotal"><?php /*echo number_format($total,$decimalPlaces); */ ?></td>
                        </tr>-->
                <?php } else { ?>
                    <td colspan="7"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <br>
    </div>
    <div id="purchaseDocTwo" class="tab-pane">
        <div class="row" style="margin-bottom: 5px">
            <div class="col-md-12">
                <?php echo export_buttons('tbl_rpt_POdrilldown', 'Pulled Purchase Request Details', True, False); ?>
            </div>
        </div>
        <table id="tbl_rpt_POdrilldown" class="borderSpace report-table-condensed" style="width: 100%">
            <thead class="report-header">
            <tr>
                <th>#</th>
                <th>Document Code</th>
                <th>Document</th>
                <th>Document Date</th>
                <th>PR Status</th>
                <th>Currency</th>
                <th>Amount</th>
                <th>PO Code</th>
                <th>Item Code</th>
                <th style="width: 20%;">Item Description</th>
                <th>PO Status</th>
                <th>PO Amount</th>
                <th>GRV Code</th>
                <th>GRV Amount</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            $decimalPlaces = 0;
            if ($details['PRQ']) {
                $a = 1;
                foreach ($details['PRQ'] as $val) {
                    $total += $val["transactionAmount"];
                    $decimalPlaces = $val["transactionCurrencyDecimalPlaces"];
                    $linkedPO_code = '';
                    $linkedPO_status = '';
                    $linkedPO_amount = '';
                    $linkedPO_itemCode = '';
                    $linkedPO_itemDescription = '';
                    $linkedPO_grvCode = '';
                    $linkedPO_grvAmount = '';
                        $linked_po = $this->db->query("SELECT 
                                                            DISTINCT(srp_erp_purchaseordermaster.purchaseOrderCode) AS purchaseOrderCode,srp_erp_purchaseorderdetails.purchaseOrderDetailsID,
                                                            srp_erp_purchaseordermaster.purchaseOrderID,
                                                            srp_erp_purchaseordermaster.documentID,
                                                            srp_erp_purchaseordermaster.approvedYN,
                                                            ((totalAmount + IFNULL(srp_erp_purchaseorderdetails.taxAmount, 0)) / srp_erp_purchaseordermaster.companyLocalExchangeRate) AS totalAmount,
                                                            srp_erp_purchaseorderdetails.itemSystemCode,
                                                            srp_erp_purchaseorderdetails.itemDescription,
                                                            grvPrimaryCode,
                                                            srp_erp_grvdetails.grvAutoID,
                                                            ((receivedTotalAmount + IFNULL(srp_erp_grvdetails.taxAmount, 0)) / srp_erp_grvmaster.companyLocalExchangeRate) AS grvAmount
                                                        FROM 
                                                            srp_erp_purchaseordermaster 
                                                        JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                                                        LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID
                                                        LEFT JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID
                                                        WHERE 
                                                            prMasterID = {$val['documentAutoID']}")->result_array();
                        if ($linked_po) {
                            foreach ($linked_po as $item) {
                                if ($item['approvedYN'] == 1) {
                                    $linkedPO_status .= '<span class="label label-success">Approved</span><br>';
                                } else {
                                    $linkedPO_status .= '<span class="label label-danger">Not Approved</span><br>';
                                }
                                $linkedPO_code .= '<a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $item["documentID"] . '\',' . $item["purchaseOrderID"] . ')">' . $item["purchaseOrderCode"] . '</a><br>';
                                $linkedPO_amount .= number_format($item['totalAmount'], $val["transactionCurrencyDecimalPlaces"]) . '<br>';
                                $linkedPO_itemCode .= $item['itemSystemCode'] . '<br>';
                                $linkedPO_itemDescription .= $item['itemDescription'] . '<br>';
                                $linkedPO_grvCode .= '<a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'GRV\',' . $item["grvAutoID"] . ')">' . $item["grvPrimaryCode"] . '</a><br>';
                                $linkedPO_grvAmount .= number_format($item['grvAmount'], $val["transactionCurrencyDecimalPlaces"]) . '<br>';
                            }
                        }
                        ?>
                    <tr class='hoverTr'>
                        <td><?php echo $a ?></td>
                        <td><a href="#" class="drill-down-cursor"
                               onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentAutoID"] ?>)"><?php echo $val["DocumentCode"] ?></a>
                        </td>
                        <td><?php echo $val["documentID"] ?></td>
                        <td><?php echo $val["documentDate"] ?></td>
                        <td class="text-center"><?php if ($val['approvedYN'] == 1) {
                                echo '<span class="label label-success">Approved</span>';
                            } else {
                                echo '<span class="label label-danger">Not Approved</span>';
                            } ?></td>
                        <td><?php echo $val["transactionCurrency"] ?></td>
                        <td style="text-align: right"><?php echo number_format($val["transactionAmount"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                        <td><?php echo $linkedPO_code; ?></td>
                        <td><?php echo $linkedPO_itemCode; ?></td>
                        <td><?php echo $linkedPO_itemDescription; ?></td>
                        <td class="text-center"><?php echo $linkedPO_status; ?></td>
                        <td style="text-align: right"><?php echo $linkedPO_amount ?></td>
                        <td><?php echo $linkedPO_grvCode; ?></td>
                        <td style="text-align: right"><?php echo $linkedPO_grvAmount; ?></td>
                    </tr>
                    <?php
                        $a++;
                    } ?>
                <!--<tr>
                        <td colspan="4"><b><?php /*echo $this->lang->line('common_total'); */ ?></b></td>
                        <td class="text-right reporttotal"><?php /*echo number_format($total,$decimalPlaces); */ ?></td>
                    </tr>-->
            <?php } else { ?>
                <td colspan="7"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#tbl_rpt_pulledJobsdrilldown').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
    $('#tbl_rpt_POdrilldown').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
</script>