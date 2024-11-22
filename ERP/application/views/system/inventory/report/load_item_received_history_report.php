<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$barcode = false;
$partNo = false;

if (isset($columnSelectionDrop)) {
    if (in_array("barcode", $columnSelectionDrop)) {
        $barcode = true;
    }
    if (in_array("partNo", $columnSelectionDrop)) {
        $partNo = true;
    }
}

if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('tbl_rpt_itemreceivedhistory', 'Item Received History', True, True);
            } ?>
        </div>
    </div>

    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item Received History</strong></div>
            <div style="">
                <div style="height: 600px">
                <table id="tbl_rpt_itemreceivedhistory" class="borderSpace report-table-condensed">
                    <thead class="report-header">
                    <tr>
                        <th style="width: 2%;">#</th>
                        <th style="width: 14%;">Supplier</th>
                        <th style="width: 5%;">Item Code</th>
                        <th style="width: 5%;">Item Secondary Code</th>
                        <?php if($barcode){ ?>
                            <th style="width: 4% ;">Barcode</th>
                        <?php } ?>
                        <?php if($partNo){ ?>
                            <th style="width: 3% ;">Part No</th>
                        <?php } ?>
                        <th style="width: 7%;">Item Name</th>
                        <th style="width: 5% ;">Description</th>
                        <th style="width: 5%;">Item Category</th>
                        <th style="width: 4% ;">Document No</th>
                        <th style="width: 5%; min-width: 75px;">Date</th>
                        <th style="width: 3%;">PO NO</th>
                        <th style="width: 4% !important;">QTY</th>
                        <th style="width: 4% !important;">Rate</th>
                        <th style="width: 5% !important;">Gross</th>
                        <th style="width: 5% !important;">Discount</th>
                        <th style="width: 5% !important;">Price Without Tax</th>
                        <?php foreach ($taxtype as $val){?>
                            <th style="width: 5% !important;"><?php echo $val['taxShortCode']?></th>
                        <?php }?>
                        <th style="width: 5% !important;">Tax Total</th>
                        <th style="width: 5% !important;">Net Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $a = 1;
                    if($details['item_received_details']){
                        foreach ($details['item_received_details'] as $val){
                            $taxtotal = 0; ?>
                        <tr>
                            <td style=""><?php echo $a  ?></td>
                            <td><?php echo $val['supplierName'] ?></td>
                            <td><?php echo $val['itemCode'] ?></td>
                            <td><?php echo $val['seconeryItemCode'] ?></td>
                            <?php if($barcode){ ?>
                                <td><?php echo $val['barcode'] ?></td>
                            <?php } ?>
                            <?php if($partNo){ ?>
                                <td><?php echo $val['partNo'] ?></td>
                            <?php } ?>
                            <td><?php echo $val['itemName'] ?></td>
                            <td><?php echo $val['description'] ?></td>
                            <td><?php echo $val['itemCategory'] ?></td>
                            <td style=""><?php echo $val['documentSystemCode'] ?></td>
                            <td><?php echo $val['date'] ?></td>
                            <td><?php echo $val['poNo']  ?></td>

                        <td class="text-right"><?php echo ($val['qty']) ?></td>
                        <td class="text-right"><?php echo number_format($val['rate'],2) ?></td>
                        <td class="text-right"><?php echo number_format($val['gross'] ,$this->common_data['company_data']['company_default_decimal']) ?></td>
                        <td class="text-right"><?php echo number_format($val['discount'],2) ?></td>
                        <td class="text-right"><?php echo number_format(($val['gross']-$val['discount']),2) ?></td>
                        <?php foreach ($taxtype as $val1){  ?>
                            <td class="text-right">
                                <?php
                                $taxcalculation = receivedhistory_taxcalculation($val['DocumentCode'],$val['gross'],$val['documentprimaryID'],$val['discount'],$val1['taxMasterAutoID'],$val['currenctexchange']);
                                echo number_format($taxcalculation,2)
                                ?></td>
                        <?php

                            $taxtotal+= $taxcalculation;
                        }
                        ?>
                        <td class="text-right"><?php echo number_format($taxtotal,2) ?></td>
                        <td class="text-right"><?php echo number_format((($val['gross']-$val['discount'])+$taxtotal),2) ?></td>



                    </tr>
                            <?php $a++;

                        }
                    } ?>
                    </tbody>


                </table>
            </div>
        </div>
    </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_itemreceivedhistory').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
    function generateReportPdf() {
        var form = document.getElementById('item_recieved_history_filter_frm');
        form.target = '_blank';
        /*form.action = 'php echo site_url('template_paySheet/get_payScale_report_pdf'); ?>';*/
        form.action = '<?php echo site_url('Inventory/load_item_received_history/pdf'); ?>';
        form.submit();
    }
</script>