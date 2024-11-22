<?php
$companyId = current_companyID();

$fiancecategory = array();
$mainCategory = array();
$subCategory = array();

$date_format_policy = date_format_policy();
$datAf = $_POST['dateAsOf'];
$dateAsOf = input_format_date($datAf,$date_format_policy);

$fieldName = $_POST['fieldName'];
$wh = '';

$mainCategory = $_POST['mainCategory'];
if ($mainCategory) {
    $wh .= "AND srp_erp_fa_asset_master.faCatID IN ($mainCategory)";//faSubCatID
}

$datas = $this->db->query("SELECT
	@LocalAmountDep :=
IF (
	ISNULL(`LocalAmountDep`),
	0,
	`LocalAmountDep`
) AS LocalAmountDep,
 @LocalAmountDep AS companyLocalAmountDep,
 `srp_erp_fa_asset_master`.`companyLocalAmount` AS companyLocalAmount,
 srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces,
 @ntbTransection := (

	IF (
		ISNULL(
			`srp_erp_fa_asset_master`.`companyLocalAmount`
		),
		0,

	IF (
		ISNULL(
			`srp_erp_fa_asset_master`.`companyLocalAmount`
		),
		0,
		`srp_erp_fa_asset_master`.`companyLocalAmount`
	)
	) -
	IF (
		ISNULL(`LocalAmountDep`),
		0,
		`LocalAmountDep`
	)
) AS ntbTransection,
 @ntbTransection AS netBookTransectionValue,
 @ReportingDepAmount :=
IF (
	ISNULL(`ReportingDepAmount`),
	0,
	`ReportingDepAmount`
) AS `ReportingDepAmount`,
 @ReportingDepAmount AS totalReportingDepAmount,
 `srp_erp_fa_asset_master`.`companyReportingAmount` AS companyReportingAmount,
 @nbvReporting := (

	IF (
		ISNULL(
			`srp_erp_fa_asset_master`.`companyReportingAmount`
		),
		0,
		`srp_erp_fa_asset_master`.`companyReportingAmount`
	) -
	IF (
		ISNULL(`ReportingDepAmount`),
		0,
		`ReportingDepAmount`
	)
) AS nbvReporting,
 @nbvReporting AS  netBookRepotingValue,
 `srp_erp_fa_asset_master`.`faCode`,
 `srp_erp_fa_asset_master`.`costGLCode`,
 `srp_erp_fa_asset_master`.`faID` AS faID,
 `srp_erp_fa_asset_master`.`faUnitSerialNo`,
  DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d')AS dateAQ,
  DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d')AS dateDEP,
 `srp_erp_fa_asset_master`.`transactionCurrencyDecimalPlaces` AS transactionCurrencyDecimalPlaces,
 `srp_erp_fa_asset_master`.`companyReportingDecimalPlaces` AS companyReportingDecimalPlaces,
 `srp_erp_fa_asset_master`.`assetDescription` AS assetDescription,
 `srp_erp_fa_asset_master`.`serialNo` AS serialNo,
 `srp_erp_itemcategory`.`description` AS description
FROM
	srp_erp_fa_asset_master
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
LEFT JOIN (
	SELECT
		SUM(
			srp_erp_fa_assetdepreciationperiods.companyLocalAmount
		) LocalAmountDep,
		SUM(
			`srp_erp_fa_assetdepreciationperiods`.`companyReportingAmount`
		) ReportingDepAmount,
		faID
	FROM
		srp_erp_fa_depmaster
	INNER JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
	WHERE
		srp_erp_fa_depmaster.approvedYN = 1
	AND srp_erp_fa_depmaster.depDate <= \"{$dateAsOf}\"
	GROUP BY
		faID
) depAmountQry ON srp_erp_fa_asset_master.faID = depAmountQry.faID
WHERE
	`srp_erp_fa_asset_master`.`approvedYN` = 1
AND `srp_erp_fa_asset_master`.`assetType` = 1
AND `srp_erp_fa_asset_master`.`postDate` <= \"{$dateAsOf}\"
AND (
	srp_erp_fa_asset_master.disposedDate >= \"{$dateAsOf}\"
	OR `srp_erp_fa_asset_master`.`disposedDate` IS NULL
)
AND `srp_erp_fa_asset_master`.`faCatID` IN ({$mainCategory})")->result_array();

?>

<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
        </div>
        <div class="text-center reportHeaderColor"><strong>As of: </strong><?php echo $dateAsOf; ?></div>
    </div>
</div>
<div class="row">
    <div class="table-responsive">
        <table class="<?php echo table_class();?>"
           id="asset_register_table" style="border: 1px solid #c0c0c0">
        <thead>
        <tr>
            <th rowspan="2" class="theadtr">Finance Category</th>
            <th rowspan="2" class="theadtr">FA Code</th>
            <th rowspan="2" class="theadtr">Serial No.</th>
            <th rowspan="2" class="theadtr">Asset Description</th>
            <th rowspan="2" class="theadtr">Code GL Code</th>
            <th rowspan="2" class="theadtr">Date Acquired</th>
            <th rowspan="2" class="theadtr">Dep Started Date</th>
            <?php if ($fieldName != 'companyReportingAmount') { ?>
                <th colspan="3" class="theadtr"><?php echo $this->common_data['company_data']['company_default_currency'] ?></th>
            <?php } else { ?>
                <th colspan="3" class="theadtr"><?php echo $this->common_data['company_data']['company_reporting_currency'] ?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php if ($fieldName != 'companyReportingAmount') { ?>
                <th class="theadtr">Unit Cost</th>
                <th class="theadtr">Acc Dep Amount</th>
                <th class="theadtr">Net Book Value</th>
            <?php } else { ?>
                <th class="theadtr">Unit Cost</th>
                <th class="theadtr">Acc Dep Amount</th>
                <th class="theadtr">Net Book Value</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php

        $datas = array_group_by($datas, 'description');

        $grandAmount = 0;
        $grandAmountDep = 0;
        $grandNetBookValue = 0;
        foreach ($datas as $key => $data) {
            $amount = 0;
            $amountDep = 0;
            $netBookValue = 0;
            ?>
            <tr>
                <td colspan="11"><span class="mainCategoryHead2"><?php echo $key ?></span></td>
            </tr>
            <?php
            foreach ($data as $item) {
                ?>
                <tr>
                    <td><?php echo $item['description']; ?></td>
                    <td><?php echo $item['faCode']; ?></td>
                    <td><?php echo $item['serialNo']; ?></td>
                    <td><?php echo $item['assetDescription']; ?></td>
                    <td><?php echo $item['costGLCode']; ?></td>
                    <td><?php echo $item['dateAQ']; ?></td>
                    <td><?php echo $item['dateDEP']; ?></td>
                    <?php if ($fieldName != 'companyReportingAmount') {
                        $amount += $item['companyLocalAmount'];
                        $amountDep += $item['companyLocalAmountDep'];
                        $netBookValue += $item['netBookTransectionValue'];
                        ?>
                        <td style="text-align: right;"><?php echo number_format($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align: right;"><?php echo number_format($item['companyLocalAmountDep'], $item['companyLocalCurrencyDecimalPlaces']); ?></td>
                        <td style="text-align: right;"><?php echo number_format($item['netBookTransectionValue'], $item['companyLocalCurrencyDecimalPlaces']); ?></td>
                    <?php } else {
                        $amount += $item['companyReportingAmount'];
                        $amountDep += $item['totalReportingDepAmount'];
                        $netBookValue += $item['netBookRepotingValue'];
                        ?>
                        <td style="text-align: right;"><?php echo $item['companyReportingAmount']; ?></td>
                        <td style="text-align: right;"><?php echo $item['totalReportingDepAmount']; ?></td>
                        <td style="text-align: right;"><?php echo $item['netBookRepotingValue']; ?></td>
                    <?php } ?>
                </tr>
            <?php }
            $grandAmount += $amount;
            $grandAmountDep += $amountDep;
            $grandNetBookValue += $netBookValue;
            ?>
            <tr>
                <td colspan="7" style="text-align: right;">Total</td>
                <td style="text-align: right;"><?php echo $amount ?></td>
                <td style="text-align: right;"><?php echo $amountDep ?></td>
                <td style="text-align: right;"><?php echo $netBookValue ?></td>
            </tr>
            <?php
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="7" style="text-align: right;">Grand Total:</th>
            <th style="text-align: right;"><?php  echo $grandAmount ?></th>
            <th style="text-align: right;"><?php echo $grandAmountDep ?></th>
            <th style="text-align: right;"><?php echo $grandNetBookValue ?></th>
        </tr>
        </tfoot>
    </table>
    </div>
</div>