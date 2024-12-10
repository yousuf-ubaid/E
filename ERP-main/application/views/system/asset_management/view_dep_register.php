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


?>

<table class="borderSpace report-table-condensed" id="depTable">
    <thead>
    <tr class="reportTableHeader">
        <th>Asset Code</th>
        <th>Description</th>
        <th>Acq Date</th>
        <th>Capitalized Date</th>
        <th>Dep Date</th>
        <th>Main Category</th>
        <th>Sub Category</th>
        <th>Dep Amount (<?php echo $currency_code ?>)</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total=0;
    foreach ($datas as $data) {
        $total += $data['companyLocalAmount'];
        ?>
        <tr class="hoverTr">
            <td><?php echo $data['faCode']; ?></td>
            <td><?php echo $data['assetDescription']; ?></td>
            <td><?php echo $data['dateAQ']; ?></td>
            <td><?php echo $data['postDate']; ?></td>
            <td><?php echo $data['dateDEP']; ?></td>
            <td><?php echo $data['masterDescription']; ?></td>
            <td><?php echo $data['subDescription']; ?></td>
            <td class="text-right"><?php echo number_format($data['companyLocalAmount'], $data['companyLocalCurrencyDecimalPlaces']); ?></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="7" class="text-right">Total</th>
        <th class="reporttotal text-right" id="totalFoot"><?php echo empty($datas) ? 0 : number_format($total, $data['companyLocalCurrencyDecimalPlaces']) ?></th>
    </tr>
    </tfoot>
</table>