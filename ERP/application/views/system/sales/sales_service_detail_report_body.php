<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$currncy = $this->common_data['company_data']['company_default_currency'];
if($currency=='Reporting'){
    $d = $this->common_data['company_data']['company_reporting_decimal'];
    $currncy = $this->common_data['company_data']['company_reporting_currency'];
}

$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$barcode = false;
$partNo = false;
$colspan=11;
if (isset($columnSelectionDrop)) {
    if (in_array("barcode", $columnSelectionDrop)) {
        $barcode = true;
        $colspan +=1;
    }
    if (in_array("partNo", $columnSelectionDrop)) {
        $partNo = true;
        $colspan +=1;
    }
}
?>

<link rel="stylesheet" href="<?php echo base_url('plugins/pos/gpos-reports.css'); ?>">
<span class="pull-right">
    <!--<button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php /*echo $this->lang->line('common_print'); */?>
    </button>-->


</span>

<table id="sales_service_detail_report_table" class="<?php echo table_class() ?> customTbl">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%">Customer Code</th>
            <th style="min-width: 12%">Customer Name</th>
            <th style="min-width: 12%">Document Code</th>
            <th style="min-width: 20%">Document Date</th>
            <th style="min-width: 12%">Item Code</th>
            <th style="min-width: 12%">Secondary Code</th>
            <?php if($barcode){ ?>
                <th style="width: 10% ;">Barcode</th>
            <?php } ?>
            <?php if($partNo){ ?>
                <th style="width: 10% ;">Part No</th>
            <?php } ?>
            <th style="min-width: 12%">Item Description</th>
            <th style="min-width: 12%">UOM</th>
            <th style="min-width: 8%">Qty</th>
            <th style="min-width: 12%">Unit Sales Value (<?php echo $currncy; ?>)</th>
            <th style="min-width: 12%">Total Sales Value (<?php echo $currncy; ?>)</th>
            <!--<th>Total Cost (<?php /*echo $currncy; */?>)</th>
            <th style="min-width: 10%">Profit (<?php /*echo $currncy; */?>)</th>
            <th style="min-width: 10%">Profit Margin</th>
            <th style="min-width: 10%">Average Cost (<?php /*echo $currncy; */?>) up to To Date</th>-->
            <th style="min-width: 10%">Sub Category</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $totalQty = 0;
        $totalAmount = 0;
        //$totalWAC = 0;
        //$totalProfit = 0;
        //$averageCost = 0;
        $totalSalesVal = 0;
        if (!empty($reportData)) {
            $i = 1;
            foreach ($reportData as $item) {
                $totalQty += ($item['qty']);
                $totalAmount += ($item['totSalesVal']);
                /*$totalWAC += ($item['totalCost']);
                $totalProfit += ($item['totSalesVal'])-($item['totalCost']);
                $averageCost += ($item['averageCost']);*/
                ?>
                <tr>
                    <td> <?php echo $i;$i++; ?> </td>
                    <td><?php echo $item['customerSystemCode'] ?></td>
                    <td><?php echo $item['customerName'] ?></td>
                    <!--<td><?php /*echo $item['DocumentCode'] */?></td>-->
                    <td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal('<?php echo $item["documentID"] ?>',<?php echo $item["masterTableID"] ?>)"><?php echo $item["DocumentCode"] ?></a></td>

                    <td><?php echo $item['documentDate'] ?></td>
                    <td><?php echo $item['itemSystemCode'] ?></td>
                    <td><?php echo $item['seconeryItemCode'] ?></td>
                    <?php if($barcode){ ?>
                        <td><?php echo $item['barcode'] ?></td>
                    <?php } ?>
                    <?php if($partNo){ ?>
                        <td><?php echo $item['partNo'] ?></td>
                    <?php } ?>
                    <td><?php echo $item['itemDescription'] ?></td>
                    <td><?php echo $item['defaultUnitOfMeasure'] ?></td>
                    <td class="ar"><?php echo $item['qty']; ?></td>
                    <td class="ar"><?php echo number_format(($item['unitSalesVal']), $d); ?></td>
                    <td class="ar"><?php echo number_format(($item['totSalesVal']), $d); ?></td>
                    <!--<td class="ar"><?php /*echo number_format(($item['totalCost']), $d); */?></td>
                    <td class="ar"><?php /*echo number_format(($item['totSalesVal'])-($item['totalCost']), $d); */?></td>
                    <td class="ar">
                        <?php
/*                        if ($item['totSalesVal'] != 0) {
                            $totalSalesVal = 1;
                            echo number_format(((($item['totSalesVal'])-($item['totalCost'])) / ($item['totSalesVal'])) * 100, 2);
                        } else {
                            echo 0;
                        }
                        */?>%
                    </td>
                    <td class="ar"><?php /*echo number_format(($item['averageCost']), $d); */?></td>-->
                    <td class="ar"><?php echo $item['subCategory']; ?></td>
                </tr>
                <?php

            }
        }
        ?>
        </tbody>

        <tfoot>
        <tr style="font-size:15px !important;">
            <td colspan="<?php echo $colspan ?>" style="text-align: right;"><strong> Total </strong>
            </td>

            <td class="text-right"><strong><?php echo number_format($totalAmount, $d); ?></strong></td>
            <!--<td class="text-right"><strong><?php /*echo number_format($totalWAC, $d); */?></strong></td>
            <td class="text-right"><strong><?php /*echo number_format($totalProfit, $d); */?></strong></td>
            <td class="ar">
                <?php
/*                if ($totalSalesVal != 0) {
                    echo number_format(($totalProfit / $totalAmount) * 100, 2);
                } else {
                    echo 0;
                }
                */?>%
            </td>
            <td class="text-right"><strong><?php /*echo number_format($averageCost, $d); */?></strong></td>-->
            <td style="text-align: right;">&nbsp;</td>
        </tr>
        </tfoot>
    </table>


<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_itemizedSalesReport");
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

    function generateItemSalesReportPdf() {
        /*var form = document.getElementById('frm_itemizedSalesReport');
        form.target = '_blank';
        form.action = '<?php //echo site_url('Pos_restaurant/loadItemizedSalesReportPdf'); ?>';
        form.submit();*/
    }

    $('#sales_detail_report_table').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>