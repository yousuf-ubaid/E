<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$conc = '';
?>

<div class="row" style="margin-top: 5px">
    <div class="col-md-12">
        <div class="pull-right"><a href="" class="btn btn-excel btn-xs" id="btn-excel" download="PO Tracking Report.xls"
                                   onclick="var file = tableToExcel('PO_tracking_details_tbl_excel', 'PO Tracking Report'); $(this).attr('href', file);">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i><?php echo $this->lang->line('common_excel'); ?> <!-- Excel -->
            </a></div>
    </div>
</div>
<hr>
<div class="row" id="PO_tracking_report">
    <div class="table-responsive" style="height: 500px">
        <table class="table table-bordered table-striped" id="PO_tracking_details_tbl"
               style="width: 100%;border: 1px solid #cec8c8;">
            <thead class="thead">
            <tr>
                <th style="min-width: 10px">#</th>
                <th style="min-width: 50px">
                    <?php echo $this->lang->line('procurement_approval_po_number'); ?><!--PO Number--></th>
                <th style="min-width: 83px">PO <?php echo $this->lang->line('common_date'); ?><!--PO Date--></th>
                <th style="min-width: 83px"><?php echo $this->lang->line('procurement_expected_delivery_date'); ?><!--Expected Delivery Date--></th>
                <th style="min-width: 20px"><?php echo $this->lang->line('common_narration'); ?><!--Narration--></th>
                <th style="min-width: 20px"><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                
                <th style="min-width: 100px"><?php echo $this->lang->line('common_supplier').' '; ?> <?php echo $this->lang->line('common_code'); ?><!--Supplier Code--></th>
                <th style="min-width: 100px"><?php echo $this->lang->line('common_supplier_name'); ?><!--Supplier Name--></th>
                <th style="min-width: 100px"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>

                <th><?php echo $this->lang->line('transaction_common_grv_code'); ?><!--GRV Code--></th>
                <th style="min-width: 83px"><?php echo $this->lang->line('transaction_goods_received_voucher_grv_date'); ?><!--GRV Date--></th>
                <th><?php echo $this->lang->line('common_amount'); ?><!--Supplier Name--></th>

                <th><?php echo $this->lang->line('common_invoice_code'); ?><!--Invoice Code--></th>
                <th style="min-width: 83px"><?php echo $this->lang->line('common_invoice_date'); ?><!--Invoice Date--></th>
                <th><?php echo $this->lang->line('common_amount'); ?><!--Supplier Name--></th>

                <th><?php echo $this->lang->line('common_payment_type'); ?><!--Payment Type--></th>
                <th><?php echo $this->lang->line('common_payment_code'); ?><!--Payment Code--></th>
                <th style="min-width: 83px"><?php echo $this->lang->line('common_payment_date'); ?><!--Payment Date--></th>
                <th><?php echo $this->lang->line('common_paid'); ?>
                    <?php echo $this->lang->line('common_amount'); ?><!--Supplier Name--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $x = 1;
            if ($details['po_details']) {
                foreach ($details['po_details'] as $poDet) { ?>
                    <tr style=" ">
                        <td style=""><?php echo $x?></td>
                        <td style=""><a href="#" class="drill-down-cursor"
                                        onclick="documentPageView_modal('<?php echo $poDet['documentID'] ?>','<?php echo $poDet['purchaseOrderID'] ?>')"><?php echo $poDet['purchaseOrderCode'] ?></a>
                        </td>
                        <td style=""><?php echo $poDet['documentDate'] ?></td>
                        <td style=""><?php echo $poDet['expectedDeliveryDate'] ?></td>
                        <td style=""><?php echo $poDet['narration'] ?></td>
                        <td style=""><?php echo $poDet['segmentCode'] ?></td>
                     
                        <td style=" "><?php echo $poDet['supplierCode'] ?></td>
                        <td style=""><?php echo $poDet['supplierName'] ?></td>
                        <td style=""><?php echo $poDet['CurrencyCode'] ?></td>
                        <td style="text-align: right;"><?php echo number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) ?></td>
                        <?php
                        $grv_index = 0;
                        /*GRV For PO*/
                        if ($details['grv_details']) {
                            foreach ($details['grv_details'] as $PO_GRV) {
                                if ($PO_GRV['purchaseOrderMastertID'] == $poDet['purchaseOrderID']) {
                                    if ($grv_index > 0) {
                                        echo '<tr style="">';
                                        echo '<td colspan="10" >&nbsp;</td>';
                                    } else if ($grv_index == 0) {
                                        $conc = ' ';
                                    }
                                    echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"onclick="documentPageView_modal(\'' . $PO_GRV["documentID"] . '\',' . $PO_GRV["grvAutoID"] . ')">' . $PO_GRV['grvPrimaryCode'] . '</a></td>';
                                    echo '<td style="' . $conc . '">' . $PO_GRV['grvDate'] . '</td>';
                                    echo '<td style="text-align: right;' . $conc . '">' . number_format($PO_GRV['grvAmount'], $PO_GRV['transactionCurrencyDecimalPlaces']) . '</td>';

                                    /*Supplier Invoice For GRV*/
                                    $grv_invoiceIndex = 0;
                                    if ($details['sup_grv_details']) {
                                        foreach ($details['sup_grv_details'] as $GRV_INV) {
                                            if ($GRV_INV['grvAutoID'] == $PO_GRV['grvAutoID']) {
                                                if ($grv_invoiceIndex > 0) {
                                                    echo '<tr style="">';
                                                    echo '<td colspan="13">&nbsp;</td>';
                                                } else if ($grv_invoiceIndex == 0) {
                                                    $conc = '';
                                                }
                                                echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $GRV_INV["documentID"] . '\',' . $GRV_INV["InvoiceAutoID"] . ')">' . $GRV_INV['bookingInvCode'] . '</a></td>';
                                                echo '<td style="' . $conc . '">' . $GRV_INV['invoiceDate'] . '</td>';
                                                echo '<td style="text-align: right; ' . $conc . '">' . number_format($GRV_INV['invoiceAmount'], $GRV_INV['transactionCurrencyDecimalPlaces']) . '</td>';

                                                /*Payment For Supplier Invoice GRV*/
                                                $grv_paymentIndex = 0;
                                                if ($details['payment_details']) {
                                                    foreach ($details['payment_details'] as $GRV_PAY) {
                                                        if ($GRV_PAY['InvoiceAutoID'] == $GRV_INV['InvoiceAutoID']) {
                                                            if ($grv_paymentIndex > 0) {
                                                                echo '<tr style="">';
                                                                echo '<td colspan="16">&nbsp;</td>';
                                                            } else if ($grv_paymentIndex == 0) {
                                                                $conc = '';
                                                            }
                                                            echo '<td>Invoice</td>';
                                                            echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $GRV_PAY["documentID"] . '\',' . $GRV_PAY["payVoucherAutoId"] . ')">' . $GRV_PAY['PVcode'] . '</a></td>';
                                                            echo '<td style="' . $conc . '">' . $GRV_PAY['PVdate'] . '</td>';
                                                            echo '<td style="text-align: right; ' . $conc . '">' . number_format($GRV_PAY['paymentGRV'], $GRV_PAY['transactionCurrencyDecimalPlaces']) . '</td>';
                                                            if ($grv_paymentIndex > 0) {
                                                                echo '</tr>';
                                                            }
                                                            $grv_paymentIndex++;
                                                        }
                                                    }
                                                }
                                                if ($grv_invoiceIndex > 0) {
                                                    echo '</tr>';
                                                }
                                                $grv_invoiceIndex++;
                                            }
                                        }
                                    }
                                    if ($grv_index > 0) {
                                        echo '</tr>';
                                    }
                                    $grv_index++;

                                }
                            }
                        }

                        /*Direct Supplier Invocie for PO*/
                        $PO_INV_Index = 0;
                        if ($details['sup_po_details']) {
                            foreach ($details['sup_po_details'] as $PO_INV) {
                                if ($PO_INV['purchaseOrderMastertID'] == $poDet['purchaseOrderID']) {
                                    if ($PO_INV_Index > 0 || $grv_index > 0) {
                                        echo '<tr style="">';
                                        echo '<td colspan="13">&nbsp;</td>';
                                    } else if ($grv_index == 0 && $PO_INV_Index == 0) {
                                        echo '<td colspan="3">&nbsp;</td>';
                                    }
                                    if ($PO_INV_Index == 0) {
                                        $conc = '';
                                    }

                                    echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $PO_INV["documentID"] . '\',' . $PO_INV["InvoiceAutoID"] . ')">' . $PO_INV['bookingInvCode'] . '</a></td>';
                                    echo '<td style="' . $conc . '">' . $PO_INV['invoiceDate'] . '</td>';
                                    echo '<td style="text-align: right; ' . $conc . '">' . number_format($PO_INV['invoiceAmount'], $PO_INV['transactionCurrencyDecimalPlaces']) . '</td>';

                                    /*Payment For Direct Supplier Invoice For PO*/
                                    $PO_INV_paymentIndex = 0;
                                    if ($details['payment_details']) {
                                        foreach ($details['payment_details'] as $PO_INV_PAY) {
                                            if ($PO_INV_PAY['InvoiceAutoID'] == $PO_INV['InvoiceAutoID']) {
                                                if ($PO_INV_paymentIndex > 0) {
                                                    echo '<tr style="">';
                                                    echo '<td colspan="16">&nbsp;</td>';
                                                } else if ($PO_INV_paymentIndex == 0) {
                                                    $conc = '';
                                                }
                                                echo '<td>Invoice</td>';
                                                echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $PO_INV_PAY["documentID"] . '\',' . $PO_INV_PAY["payVoucherAutoId"] . ')">' . $PO_INV_PAY['PVcode'] . '</a></td>';
                                                echo '<td style="' . $conc . '">' . $PO_INV_PAY['PVdate'] . '</td>';
                                                echo '<td style="text-align: right; ' . $conc . '">' . number_format($PO_INV_PAY['paymentGRV'], $PO_INV_PAY['transactionCurrencyDecimalPlaces']) . '</td>';
                                                if ($PO_INV_paymentIndex > 0) {
                                                    echo '</tr>';
                                                }
                                                $PO_INV_paymentIndex++;
                                            }
                                        }
                                    }
                                    if ($PO_INV_paymentIndex > 0) {
                                        echo '</tr>';
                                    }
                                    $PO_INV_paymentIndex++;
                                    if ($PO_INV_Index > 0) {
                                        echo '</tr>';
                                    }
                                    $PO_INV_Index++;
                                }
                            }
                        }

                        /*Direct Payment For PO*/
                        $PO_PAY_Index = 0;
                        if ($details['po_payment_details']) {
                            foreach ($details['po_payment_details'] as $PO_PAY) {
                                if ($PO_PAY['purchaseOrderID'] == $poDet['purchaseOrderID']) {
                                    if ($PO_INV_Index > 0 || $grv_index > 0 || $PO_PAY_Index > 0) {
                                        echo '<tr style="">';
                                        echo '<td colspan="16">&nbsp;</td>';
                                    } else if ($grv_index == 0 && $PO_INV_Index == 0 && $PO_PAY_Index == 0) {
                                        echo '<td colspan="6">&nbsp;</td>';
                                    }
                                    if ($PO_PAY_Index == 0) {
                                        $conc = '';
                                    }
                                    echo '<td>Advance</td>';
                                    echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $PO_PAY["documentID"] . '\',' . $PO_PAY["payVoucherAutoId"] . ')">' . $PO_PAY['PVcode'] . '</a></td>';
                                    echo '<td style="' . $conc . '">' . $PO_PAY['PVdate'] . '</td>';
                                    echo '<td style="text-align: right; ' . $conc . '">' . number_format($PO_PAY['paymentGRV'], $PO_PAY['transactionCurrencyDecimalPlaces']) . '</td>';
                                    if ($PO_PAY_Index > 0) {
                                        echo '</tr>';
                                    }
                                    $PO_PAY_Index++;
                                }
                            }
                        }
                        ?>
                    </tr>
                    <?php
                    $x++;
                }
            }
            ?>

            </tbody>
        </table>
    </div>
</div>

<!------------------------------------------------Excel Start------------------------------------------------------------------------------>
<div class="row hide" id="PO_tracking_report_excel">
    <div class="table-responsive" style="height: 500px">
        <table class="table table-bordered table-striped" id="PO_tracking_details_tbl_excel"
               style="width: 100%;border: 1px solid #cec8c8;">
            <thead class="thead">
            <tr>
                <th style="min-width: 10px">#</th>
                <th style="min-width: 50px">
                    <?php echo $this->lang->line('procurement_approval_po_number'); ?><!--PO Number--></th>
                <th style="min-width: 83px">PO <?php echo $this->lang->line('common_date'); ?><!--PO Date--></th>
                <th style="min-width: 83px">Expected Delivery Date</th>
                <th style="min-width: 20px"><?php echo $this->lang->line('common_narration'); ?><!--Narration--></th>
                <th style="min-width: 20px"><!--Narration-->Segment</th>
                <th style="min-width: 100px">Supplier Code<!--Supplier--></th>
                <th style="min-width: 100px">Supplier Name<!--Supplier--></th>
                <th style="min-width: 100px">Currency</th>
                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>

                <th>GRV Code<!--GRV Code--></th>
                <th style="min-width: 83px">GRV Date<!--GRV Date--></th>
                <th><?php echo $this->lang->line('common_amount'); ?><!--Supplier Name--></th>

                <th>Invoice Code<!--Invoice Code--></th>
                <th style="min-width: 83px">Invoice Date<!--Invoice Date--></th>
                <th><?php echo $this->lang->line('common_amount'); ?><!--Supplier Name--></th>

                <th>Payment Type<!--Payment Code--></th>
                <th>Payment Code<!--Payment Code--></th>
                <th style="min-width: 83px">Payment Date<!--Payment Date--></th>
                <th><?php echo $this->lang->line('common_paid'); ?>
                    <?php echo $this->lang->line('common_amount'); ?><!--Supplier Name--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $x = 1;
            if ($details['po_details']) {
                foreach ($details['po_details'] as $poDet) { ?>
                    <tr style=" ">
                        <td style=""><?php echo $x ?></td>
                        <td style=""><?php echo $poDet['purchaseOrderCode'] ?></td>
                        <td style=""><?php echo $poDet['documentDate'] ?></td>
                        <td style=""><?php echo $poDet['expectedDeliveryDate'] ?></td>
                        <td style=""><?php echo $poDet['narration'] ?></td>
                        <td style=""><?php echo $poDet['segmentCode'] ?></td>
                        <td style=" "><?php echo $poDet['supplierCode'] ?></td>
                        <td style=""><?php echo $poDet['supplierName'] ?></td>
                        <td style=""><?php echo $poDet['CurrencyCode'] ?></td>
                        <td style="text-align: right;"><?php echo number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) ?></td>
                        <?php
                        $grv_index = 0;
                        /*GRV For PO*/
                        if ($details['grv_details']) {
                            foreach ($details['grv_details'] as $PO_GRV) {
                                if ($PO_GRV['purchaseOrderMastertID'] == $poDet['purchaseOrderID']) {
                                    if ($grv_index > 0) {
                                        echo '<tr style="">';
                                        echo '<td>&nbsp;</td>';
                                        echo '<td>' . $poDet['purchaseOrderCode'] . '</td>';
                                        echo '<td>' . $poDet['documentDate'] . '</td>';
                                        echo '<td>' . $poDet['expectedDeliveryDate'] . '</td>';
                                        echo '<td>' . $poDet['narration'] . '</td>';
                                        echo '<td>' . $poDet['segmentCode'] . '</td>';
                                        
                                        echo '<td>' . $poDet['supplierCode'] . '</td>';
                                        echo '<td>' . $poDet['supplierName'] . '</td>';
                                        echo '<td>' . $poDet['CurrencyCode'] . '</td>';
                                        echo '<td>' . number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) . '</td>';
                                    } else if ($grv_index == 0) {
                                        $conc = ' ';
                                    }
                                    echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"onclick="documentPageView_modal(\'' . $PO_GRV["documentID"] . '\',' . $PO_GRV["grvAutoID"] . ')">' . $PO_GRV['grvPrimaryCode'] . '</a></td>';
                                    echo '<td style="' . $conc . '">' . $PO_GRV['grvDate'] . '</td>';
                                    echo '<td style="text-align: right;' . $conc . '">' . number_format($PO_GRV['grvAmount'], $PO_GRV['transactionCurrencyDecimalPlaces']) . '</td>';

                                    /*Supplier Invoice For GRV*/
                                    $grv_invoiceIndex = 0;
                                    if ($details['sup_grv_details']) {
                                        foreach ($details['sup_grv_details'] as $GRV_INV) {
                                            if ($GRV_INV['grvAutoID'] == $PO_GRV['grvAutoID']) {
                                                if ($grv_invoiceIndex > 0) {
                                                    echo '<tr style="">';
                                                    echo '<td>&nbsp;</td>';
                                                    echo '<td>' . $poDet['purchaseOrderCode'] . '</td>';
                                                    echo '<td>' . $poDet['documentDate'] . '</td>';
                                                    echo '<td>' . $poDet['expectedDeliveryDate'] . '</td>';
                                                    echo '<td>' . $poDet['narration'] . '</td>';
                                                    echo '<td>' . $poDet['segmentCode'] . '</td>';
                                                    echo '<td>' . $poDet['supplierCode'] . '</td>';
                                                    echo '<td>' . $poDet['supplierName'] . '</td>';
                                                    echo '<td>' . $poDet['CurrencyCode'] . '</td>';
                                                    echo '<td>' . number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) . '</td>';
                                                    echo '<td colspan="3">&nbsp;</td>';
                                                } else if ($grv_invoiceIndex == 0) {
                                                    $conc = '';
                                                }
                                                echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $GRV_INV["documentID"] . '\',' . $GRV_INV["InvoiceAutoID"] . ')">' . $GRV_INV['bookingInvCode'] . '</a></td>';
                                                echo '<td style="' . $conc . '">' . $GRV_INV['invoiceDate'] . '</td>';
                                                echo '<td style="text-align: right; ' . $conc . '">' . number_format($GRV_INV['invoiceAmount'], $GRV_INV['transactionCurrencyDecimalPlaces']) . '</td>';

                                                /*Payment For Supplier Invoice GRV*/
                                                $grv_paymentIndex = 0;
                                                if ($details['payment_details']) {
                                                    foreach ($details['payment_details'] as $GRV_PAY) {
                                                        if ($GRV_PAY['InvoiceAutoID'] == $GRV_INV['InvoiceAutoID']) {
                                                            if ($grv_paymentIndex > 0) {
                                                                echo '<tr style="">';
                                                                echo '<td>&nbsp;</td>';
                                                                echo '<td>' . $poDet['purchaseOrderCode'] . '</td>';
                                                                echo '<td>' . $poDet['documentDate'] . '</td>';
                                                                echo '<td>' . $poDet['expectedDeliveryDate'] . '</td>';
                                                                echo '<td>' . $poDet['narration'] . '</td>';
                                                                echo '<td>' . $poDet['segmentCode'] . '</td>';
                                                                echo '<td>' . $poDet['supplierCode'] . '</td>';
                                                                echo '<td>' . $poDet['supplierName'] . '</td>';
                                                                echo '<td>' . $poDet['CurrencyCode'] . '</td>';
                                                                echo '<td>' . number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) . '</td>';
                                                                echo '<td colspan="6">&nbsp;</td>';
                                                            } else if ($grv_paymentIndex == 0) {
                                                                $conc = '';
                                                            }
                                                            echo '<td>Invoice</td>';
                                                            echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $GRV_PAY["documentID"] . '\',' . $GRV_PAY["payVoucherAutoId"] . ')">' . $GRV_PAY['PVcode'] . '</a></td>';
                                                            echo '<td style="' . $conc . '">' . $GRV_PAY['PVdate'] . '</td>';
                                                            echo '<td style="text-align: right; ' . $conc . '">' . number_format($GRV_PAY['paymentGRV'], $GRV_PAY['transactionCurrencyDecimalPlaces']) . '</td>';
                                                            if ($grv_paymentIndex > 0) {
                                                                echo '</tr>';
                                                            }
                                                            $grv_paymentIndex++;
                                                        }
                                                    }
                                                }
                                                if ($grv_invoiceIndex > 0) {
                                                    echo '</tr>';
                                                }
                                                $grv_invoiceIndex++;
                                            }
                                        }
                                    }
                                    if ($grv_index > 0) {
                                        echo '</tr>';
                                    }
                                    $grv_index++;

                                }
                            }
                        }

                        /*Direct Supplier Invocie for PO*/
                        $PO_INV_Index = 0;
                        if ($details['sup_po_details']) {
                            foreach ($details['sup_po_details'] as $PO_INV) {
                                if ($PO_INV['purchaseOrderMastertID'] == $poDet['purchaseOrderID']) {
                                    if ($PO_INV_Index > 0 || $grv_index > 0) {
                                        echo '<tr style="">';
                                        echo '<td>&nbsp;</td>';
                                        echo '<td>' . $poDet['purchaseOrderCode'] . '</td>';
                                        echo '<td>' . $poDet['documentDate'] . '</td>';
                                        echo '<td>' . $poDet['expectedDeliveryDate'] . '</td>';
                                        echo '<td>' . $poDet['narration'] . '</td>';
                                        echo '<td>' . $poDet['segmentCode'] . '</td>';
                                        echo '<td>' . $poDet['supplierCode'] . '</td>';
                                        echo '<td>' . $poDet['supplierName'] . '</td>';
                                        echo '<td>' . $poDet['CurrencyCode'] . '</td>';
                                        echo '<td>' . number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) . '</td>';
                                        echo '<td colspan="3">&nbsp;</td>';
                                    } else if ($grv_index == 0 && $PO_INV_Index == 0) {
                                        echo '<td colspan="3">&nbsp;</td>';
                                    }
                                    if ($PO_INV_Index == 0) {
                                        $conc = '';
                                    }

                                    echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $PO_INV["documentID"] . '\',' . $PO_INV["InvoiceAutoID"] . ')">' . $PO_INV['bookingInvCode'] . '</a></td>';
                                    echo '<td style="' . $conc . '">' . $PO_INV['invoiceDate'] . '</td>';
                                    echo '<td style="text-align: right; ' . $conc . '">' . number_format($PO_INV['invoiceAmount'], $PO_INV['transactionCurrencyDecimalPlaces']) . '</td>';

                                    /*Payment For Direct Supplier Invoice For PO*/
                                    $PO_INV_paymentIndex = 0;
                                    if ($details['payment_details']) {
                                        foreach ($details['payment_details'] as $PO_INV_PAY) {
                                            if ($PO_INV_PAY['InvoiceAutoID'] == $PO_INV['InvoiceAutoID']) {
                                                if ($PO_INV_paymentIndex > 0) {
                                                    echo '<tr style="">';
                                                    echo '<td>&nbsp;</td>';
                                                    echo '<td>' . $poDet['purchaseOrderCode'] . '</td>';
                                                    echo '<td>' . $poDet['documentDate'] . '</td>';
                                                    echo '<td>' . $poDet['expectedDeliveryDate'] . '</td>';
                                                    echo '<td>' . $poDet['narration'] . '</td>';
                                                    echo '<td>' . $poDet['segmentCode'] . '</td>';
                                                    echo '<td>' . $poDet['supplierCode'] . '</td>';
                                                    echo '<td>' . $poDet['supplierName'] . '</td>';
                                                    echo '<td>' . $poDet['CurrencyCode'] . '</td>';
                                                    echo '<td>' . number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) . '</td>';
                                                    echo '<td colspan="6">&nbsp;</td>';
                                                } else if ($PO_INV_paymentIndex == 0) {
                                                    $conc = '';
                                                }
                                                echo '<td>Invoice</td>';
                                                echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $PO_INV_PAY["documentID"] . '\',' . $PO_INV_PAY["payVoucherAutoId"] . ')">' . $PO_INV_PAY['PVcode'] . '</a></td>';
                                                echo '<td style="' . $conc . '">' . $PO_INV_PAY['PVdate'] . '</td>';
                                                echo '<td style="text-align: right; ' . $conc . '">' . number_format($PO_INV_PAY['paymentGRV'], $PO_INV_PAY['transactionCurrencyDecimalPlaces']) . '</td>';
                                                if ($PO_INV_paymentIndex > 0) {
                                                    echo '</tr>';
                                                }
                                                $PO_INV_paymentIndex++;
                                            }
                                        }
                                    }
                                    if ($PO_INV_paymentIndex > 0) {
                                        echo '</tr>';
                                    }
                                    $PO_INV_paymentIndex++;
                                    if ($PO_INV_Index > 0) {
                                        echo '</tr>';
                                    }
                                    $PO_INV_Index++;
                                }
                            }
                        }

                        /*Direct Payment For PO*/
                        $PO_PAY_Index = 0;
                        if ($details['po_payment_details']) {
                            foreach ($details['po_payment_details'] as $PO_PAY) {
                                if ($PO_PAY['purchaseOrderID'] == $poDet['purchaseOrderID']) {
                                    if ($PO_INV_Index > 0 || $grv_index > 0 || $PO_PAY_Index > 0) {
                                        echo '<tr style="">';
                                        echo '<td>&nbsp;</td>';
                                        echo '<td>' . $poDet['purchaseOrderCode'] . '</td>';
                                        echo '<td>' . $poDet['documentDate'] . '</td>';
                                        echo '<td>' . $poDet['expectedDeliveryDate'] . '</td>';
                                        echo '<td>' . $poDet['narration'] . '</td>';
                                             echo '<td>' . $poDet['segmentCode'] . '</td>';
                                        echo '<td>' . $poDet['supplierCode'] . '</td>';
                                        echo '<td>' . $poDet['supplierName'] . '</td>';
                                        echo '<td>' . $poDet['CurrencyCode'] . '</td>';
                                        echo '<td>' . number_format($poDet['Amount'], $poDet['transactionCurrencyDecimalPlaces']) . '</td>';
                                        echo '<td colspan="6">&nbsp;</td>';
                                    } else if ($grv_index == 0 && $PO_INV_Index == 0 && $PO_PAY_Index == 0) {
                                        echo '<td colspan="6">&nbsp;</td>';
                                    }
                                    if ($PO_PAY_Index == 0) {
                                        $conc = '';
                                    }
                                    echo '<td>Advance</td>';
                                    echo '<td style="' . $conc . '"><a href="#" class="drill-down-cursor"
                                                                    onclick="documentPageView_modal(\'' . $PO_PAY["documentID"] . '\',' . $PO_PAY["payVoucherAutoId"] . ')">' . $PO_PAY['PVcode'] . '</a></td>';
                                    echo '<td style="' . $conc . '">' . $PO_PAY['PVdate'] . '</td>';
                                    echo '<td style="text-align: right; ' . $conc . '">' . number_format($PO_PAY['paymentGRV'], $PO_PAY['transactionCurrencyDecimalPlaces']) . '</td>';
                                    if ($PO_PAY_Index > 0) {
                                        echo '</tr>';
                                    }
                                    $PO_PAY_Index++;
                                }
                            }
                        }
                        ?>
                    </tr>
                    <?php
                    $x++;
                }
            }
            ?>

            </tbody>
        </table>
    </div>
</div>
<!------------------------------------------------Excel End------------------------------------------------------------------------------>
<script>
    $('#PO_tracking_details_tbl').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
</script>
