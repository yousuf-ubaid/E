<?php
$companyId = current_companyID();
$company_code = $this->common_data['company_data']['company_code'];
$decimal_places = $this->common_data['company_data']['company_default_decimal'];
$currency_code = $this->common_data['company_data']['company_default_currency'];

$categroy = $_POST['categroy'];
$startFinanceYear = $_POST['startFinanceYear'];
$endFinanceYear = $_POST['endFinanceYear'];
$lastFinanceYear = $_POST['lastFinanceYear'];
$dateAsOf = $_POST['dateAsOf'];
$type = $_POST['type'];

?>
<table class="borderSpace report-table-condensed" id="astTable">
    <thead>
    <tr class="reportTableHeader">
        <th style="width: 100px">Asset Code</th>
        <th>Description</th>
        <th style="width: 105px">Acq Date</th>
        <th style="width: 105px">Capitalized Date</th>
        <?php if ($type == 'disposal') { ?>
            <th>Disposed Date</th><?php } ?>
        <th style="width: 105px">Main Category</th>
        <th style="width: 105px">Sub Category</th>
        <th style="width: 100px">Cost (<?php echo $currency_code ?>)</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    foreach ($datas as $data) {
        $total += $data['companyLocalAmount'];
        ?>
        <tr class="hoverTr">
            <td><?php echo $data['faCode']; ?></td>
            <td><?php echo $data['assetDescription']; ?></td>
            <td><?php echo $data['dateAQ']; ?></td>
            <td><?php echo $data['postDate']; ?></td>
            <?php if ($type == 'disposal') { ?>
                <td><?php echo $data['disposedDate']; ?></td><?php } ?>
            <td><?php echo $data['masterDescription']; ?></td>
            <td><?php echo $data['subDescription']; ?></td>
            <td class="text-right"><?php echo number_format($data['companyLocalAmount'], $data['companyLocalCurrencyDecimalPlaces']); ?></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="6" class="text-right">Total</th>
        <th id="totalFoot"
            class="reporttotal text-right"><?php echo empty($datas) ? 0 : number_format($total, $data['companyLocalCurrencyDecimalPlaces']) ?></th>
    </tr>
    </tfoot>
</table>