<?php
$companyId = current_companyID();
$company_code = $this->common_data['company_data']['company_code'];
$decimal_places = $this->common_data['company_data']['company_default_decimal'];

$dateAsOf = $_POST['dateAsOf'];

//exit;

$itemCategoryID = $this->db->query("SELECT itemCategoryID FROM `srp_erp_itemcategory` WHERE `categoryTypeID` = '3' AND `companyCode` = '{$company_code}'")->row_array();

$fa_categories = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_itemcategory WHERE companyCode='$company_code' AND masterID='{$itemCategoryID['itemCategoryID']}'")->result_array();

$fa_categories_count = count($fa_categories);

$financeYear = $this->db->query("SELECT * FROM srp_erp_companyfinanceyear WHERE (beginingDate <='{$dateAsOf}' AND endingDate>='{$dateAsOf}') AND companyID='$companyId'")->row_array();
$startFinanceYear = $financeYear['beginingDate'];
$endFinanceYear = $financeYear['endingDate'];

$lastFinanceYear = date('Y-m-d', (strtotime('-1 day', strtotime($startFinanceYear))));

/*Cost begging of year */
$costs = $this->db->query("SELECT
	'COST' AS Title,
	`srp_erp_itemcategory`.`description` AS description,
	`srp_erp_itemcategory`.`itemCategoryID` AS itemCategoryID,
	SUM(`srp_erp_fa_asset_master`.`assetCompanyLocalAmount`) AS Cost
FROM
	srp_erp_itemcategory
LEFT JOIN (
	SELECT
		SUM(companyLocalAmount) AS assetCompanyLocalAmount,
		postDate,
		approvedDate,
		approvedYN,
		faID,
		faCatID,
		disposedDate,
		companyID,
		assetType
	FROM
		srp_erp_fa_asset_master
	WHERE
		approvedYN = 1
	AND postDate < '{$startFinanceYear}'
	AND companyID = '{$companyId}'
	GROUP BY
		faID
) srp_erp_fa_asset_master ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
WHERE
	srp_erp_fa_asset_master.approvedYN = 1
AND srp_erp_fa_asset_master.assetType = 1
AND srp_erp_fa_asset_master.postDate < '{$startFinanceYear}'
AND srp_erp_fa_asset_master.companyID = '{$companyId}'
AND (
	srp_erp_fa_asset_master.disposedDate > '{$lastFinanceYear}'
	OR srp_erp_fa_asset_master.disposedDate IS NULL
)
GROUP BY
	srp_erp_fa_asset_master.faCatID")->result_array();

//echo '<pre>';
//exit($this->db->last_query());

$retrunCost = array();
foreach ($costs as $cost) {
    $retrunCost[$cost['itemCategoryID']] = $cost['Cost'];
}

/*current Year Addintion */
$addtions = $this->db->query("SELECT
		'ADDITITON' AS Title,
		`srp_erp_itemcategory`.`description` AS description,
		`srp_erp_itemcategory`.`itemCategoryID` AS itemCategoryID,
		 SUM(`srp_erp_fa_asset_master`.`assetCompanyLocalAmount`) AS assetCompanyLocalAmount
	FROM
		(
			SELECT
				SUM(companyLocalAmount) AS assetCompanyLocalAmount,
				approvedDate,
				postDate,
				approvedYN,
				faID,
				faCatID,
				disposedDate,
				companyID,
				assetType
			FROM
				srp_erp_fa_asset_master
			WHERE
				approvedYN = 1
			AND companyID = '{$companyId}'
			AND (srp_erp_fa_asset_master.postDate > '{$lastFinanceYear}' AND srp_erp_fa_asset_master.postDate <= '{$dateAsOf}' )  GROUP BY faID
		) srp_erp_fa_asset_master
	LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
	WHERE
		srp_erp_fa_asset_master.approvedYN = 1
		AND srp_erp_fa_asset_master.assetType = 1
		AND srp_erp_fa_asset_master.companyID = '{$companyId}'
	AND (srp_erp_fa_asset_master.postDate > '{$lastFinanceYear}' AND srp_erp_fa_asset_master.postDate <= '{$dateAsOf}' )
	GROUP BY
		srp_erp_fa_asset_master.faCatID")->result_array();


//echo '<pre>';
//echo $this->db->last_query();
//exit;


$retrunAddition = array();
foreach ($addtions as $addtion) {
    $retrunAddition[$addtion['itemCategoryID']] = $addtion['assetCompanyLocalAmount'];
}

/*Current year disposal*/
$disposals = $this->db->query("SELECT
	'DISPOSAL' AS Title,
	`srp_erp_itemcategory`.`description` AS description,
	`srp_erp_itemcategory`.`itemCategoryID` AS itemCategoryID,
	SUM(
		`srp_erp_fa_asset_master`.`companyLocalAmount`
	) AS assetCompanyLocalAmount
FROM
	srp_erp_fa_asset_disposaldetail
INNER JOIN srp_erp_fa_asset_disposalmaster ON srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID = srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
WHERE
	srp_erp_fa_asset_master.approvedYN = '1'
AND srp_erp_fa_asset_master.companyID = '{$companyId}'
AND disposed = '1'
AND (
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate > '{$lastFinanceYear}'
	AND srp_erp_fa_asset_disposalmaster.disposalDocumentDate <= '{$dateAsOf}'
)
AND srp_erp_fa_asset_disposalmaster.approvedYN = '1'
GROUP BY
	srp_erp_fa_asset_master.faCatID")->result_array();

//echo $this->db->last_query();
//exit;

$retrunDisposal = array();
foreach ($disposals as $disposal) {
    $retrunDisposal[$disposal['itemCategoryID']] = $disposal['assetCompanyLocalAmount'];
}


/*Depreciation*/
$depStarts = $this->db->query("SELECT
	SUM(
		disposedAssets.companyLocalAmount
	) companyLocalDepAmount,
	srp_erp_itemcategory.itemCategoryID,
	srp_erp_itemcategory.description
FROM
	srp_erp_fa_depmaster
INNER JOIN (
	SELECT
		srp_erp_fa_assetdepreciationperiods.companyLocalAmount,
		srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
		srp_erp_fa_assetdepreciationperiods.faMainCategory,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
	WHERE
		(
			srp_erp_fa_asset_master.disposedDate >= '{$lastFinanceYear}'
			OR srp_erp_fa_asset_master.disposedDate IS NULL
		)
) disposedAssets ON srp_erp_fa_depmaster.depMasterAutoID = disposedAssets.depMasterAutoID
LEFT JOIN srp_erp_itemcategory ON disposedAssets.faMainCategory = srp_erp_itemcategory.itemCategoryID
LEFT JOIN srp_erp_fa_asset_master ON disposedAssets.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.approvedYN = 1
AND srp_erp_fa_depmaster.companyID = '{$companyId}'
AND srp_erp_fa_depmaster.depDate <= '{$lastFinanceYear}'
GROUP BY
	disposedAssets.faMainCategory")->result_array();

//echo '<pre>';
//echo $this->db->last_query();
//exit;
$retrunDepBigYear = array();

foreach ($depStarts as $depStart) {
    $retrunDepBigYear[$depStart['itemCategoryID']] = $depStart['companyLocalDepAmount'];
}

//print_r($retrunDepBigYear);
//exit;

$chargeFortheYears = $this->db->query("SELECT
	SUM(
		srp_erp_fa_assetdepreciationperiods.companyLocalAmount
	) companyLocalDepAmount,
	srp_erp_itemcategory.itemCategoryID,
	srp_erp_itemcategory.description,
	srp_erp_fa_asset_master.faCatID
FROM
	srp_erp_fa_depmaster
INNER JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_assetdepreciationperiods.faMainCategory = srp_erp_itemcategory.itemCategoryID
LEFT JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.approvedYN = 1
	AND srp_erp_fa_depmaster.companyID = '{$companyId}'
AND (srp_erp_fa_depmaster.depDate >= '{$startFinanceYear}' AND srp_erp_fa_depmaster.depDate <= '{$dateAsOf}') GROUP BY srp_erp_fa_asset_master.faCatID")->result_array();


/*echo '<pre>';
print_r($chargeFortheYears);
print_r($this->db->last_query());
exit('</pre>');*/

$retrunChargeThisYears = array();
foreach ($chargeFortheYears as $chargeFortheYear) {
    $retrunChargeThisYears[$chargeFortheYear['faCatID']] = $chargeFortheYear['companyLocalDepAmount'];
}

$depDisposals = $this->db->query("SELECT
	'DISPOSAL' AS Title,
	`srp_erp_itemcategory`.`description` AS description,
	`srp_erp_itemcategory`.`itemCategoryID` AS itemCategoryID,
	SUM(
		srp_erp_fa_assetdepreciationperiods.companyLocalAmount
	) AS assetCompanyLocalAmount
FROM
	srp_erp_fa_asset_disposaldetail
INNER JOIN srp_erp_fa_asset_disposalmaster ON srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID = srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
LEFT JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_assetdepreciationperiods.faID
LEFT JOIN srp_erp_fa_depmaster ON srp_erp_fa_assetdepreciationperiods.depMasterAutoID = srp_erp_fa_depmaster.depMasterAutoID
WHERE
	srp_erp_fa_asset_master.approvedYN = '1'
AND srp_erp_fa_asset_master.companyID = '{$companyId}'
AND disposed = '1'
AND (
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate > '{$lastFinanceYear}'
	AND srp_erp_fa_asset_disposalmaster.disposalDocumentDate <= '{$dateAsOf}'
)
AND srp_erp_fa_asset_disposalmaster.approvedYN = '1'
AND srp_erp_fa_depmaster.approvedYN = '1'
GROUP BY
	srp_erp_fa_asset_master.faCatID")->result_array();

//echo '<pre>';
//echo $this->db->last_query();
//exit;

$retrunDepDisposal = array();
foreach ($depDisposals as $depDisposal) {
    $retrunDepDisposal[$depDisposal['itemCategoryID']] = $depDisposal['assetCompanyLocalAmount'];
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong>Asset Register Summary</strong>
        </div>
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
        </div>
        <div class="text-center reportHeaderColor"><strong>As of: </strong><?php echo $dateAsOf; ?></div>
    </div>
</div>
<div id="assetRegisterTable">
    <table class="<?php echo table_class() ?> assetRegisterTable">
        <thead>
        <tr>
            <th style=""></th>
            <?php
            foreach ($fa_categories as $fa_category) {
                ?>
                <th style="width: 120px"><?php echo $fa_category['description']; ?></th>
                <?php
            }
            ?>
            <th style="background-color: rgba(119, 119, 119, 0.33);width: 150px">Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="<?php echo $fa_categories_count + 1 ?>"><strong>Cost</strong></td>
            <td style="background-color: rgba(119, 119, 119, 0.33)"></td>
        </tr>
        <tr>
            <td><?php echo date('d-M-Y', strtotime($startFinanceYear)) ?></td>
            <?php
            $totalCost = array();
            $cost1 = $cost2 = $cost3 = $cost4 = 0;
            foreach ($fa_categories as $key => $fa_category) {
                /*final Total Cal*/
                $totalCost[$fa_category['itemCategoryID']] = array_key_exists($fa_category['itemCategoryID'], $retrunCost) ? $retrunCost[$fa_category['itemCategoryID']] : 0;
                /* //final Total Cal*/
                $cost1 += array_key_exists($fa_category['itemCategoryID'], $retrunCost) ? $retrunCost[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right"
                    onclick="viewAssetRegister(this)"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>"
                    data-type="pre_year"
                    data-page="view_asset_register"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunCost) ? number_format($retrunCost[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right "
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($cost1, $decimal_places) ?></strong></td>
        </tr>
        <tr>
            <td>Additions during the year</td>
            <?php
            foreach ($fa_categories as $fa_category) {
                /*final Total Cal*/
                $totalCost[$fa_category['itemCategoryID']] += array_key_exists($fa_category['itemCategoryID'], $retrunAddition) ? $retrunAddition[$fa_category['itemCategoryID']] : 0;
                /* //final Total Cal*/
                $cost2 += array_key_exists($fa_category['itemCategoryID'], $retrunAddition) ? $retrunAddition[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right"
                    onclick="viewAssetRegister(this)"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>"
                    data-type="cur_year"
                    data-page="view_asset_register"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunAddition) ? number_format($retrunAddition[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right "
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($cost2, $decimal_places) ?></strong></td>
        </tr>
        <tr>
            <td>Disposals</td>
            <?php
            foreach ($fa_categories as $fa_category) {
                /*final Total Cal*/
                $totalCost[$fa_category['itemCategoryID']] -= array_key_exists($fa_category['itemCategoryID'], $retrunDisposal) ? $retrunDisposal[$fa_category['itemCategoryID']] : 0;
                /* //final Total Cal*/
                $cost3 += array_key_exists($fa_category['itemCategoryID'], $retrunDisposal) ? $retrunDisposal[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right"
                    onclick="viewAssetRegister(this)" data-type="disposal" data-page="view_asset_register"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunDisposal) ? '(' . number_format($retrunDisposal[$fa_category['itemCategoryID']], $decimal_places) . ')' : '(' . number_format(0, $decimal_places) . ')'; ?></td>
                <?php
            }
            ?>
            <td class="text-right " style="background-color: rgba(119, 119, 119, 0.33)">
                <strong>(<?php echo number_format($cost3, $decimal_places) ?> ) </strong></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td>As at end
                of <?php echo date('Y', strtotime($startFinanceYear)) . '/' . date('M', strtotime($dateAsOf)) ?></td>
            <?php
            foreach ($fa_categories as $fa_category) {
                $cost4 += array_key_exists($fa_category['itemCategoryID'], $totalCost) ? $totalCost[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right total"><?php echo array_key_exists($fa_category['itemCategoryID'], $totalCost) ? number_format($totalCost[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right total"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($cost4, $decimal_places) ?></strong></td>
        </tr>
        </tfoot>
    </table>
    <!--Depreciation-->
    <table class="<?php echo table_class() ?> assetRegisterTable">
        <tbody>
        <tr>
            <td colspan="<?php echo $fa_categories_count + 1 ?>"><strong>Depreciation</strong></td>
            <td style="background-color: rgba(119, 119, 119, 0.33)"></td>
        </tr>
        <tr>
            <td style=""><?php echo date('d-M-Y', strtotime($startFinanceYear)) ?></td>
            <?php
            $totalDep = array();
            $dep1 = $dep2 = $dep3 = $dep4 = 0;
            foreach ($fa_categories as $key => $fa_category) {
                /*final Total Cal*/
                $totalDep[$fa_category['itemCategoryID']] = array_key_exists($fa_category['itemCategoryID'], $retrunDepBigYear) ? $retrunDepBigYear[$fa_category['itemCategoryID']] : 0;
                /* //final Total Cal*/
                $dep1 += array_key_exists($fa_category['itemCategoryID'], $retrunDepBigYear) ? $retrunDepBigYear[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right" style="width: 120px;"
                    onclick="viewAssetRegister(this)" data-type="pre_year" data-page="view_dep_register"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunDepBigYear) ? number_format($retrunDepBigYear[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right"
                style="background-color: rgba(119, 119, 119, 0.33);width: 150px;">
                <strong><?php echo number_format($dep1, $decimal_places); ?></strong></td>
        </tr>
        <tr>
            <td>Charge For The Period</td>
            <?php
            foreach ($fa_categories as $key => $fa_category) {
                /*final Total Cal*/
                $totalDep[$fa_category['itemCategoryID']] += array_key_exists($fa_category['itemCategoryID'], $retrunChargeThisYears) ? $retrunChargeThisYears[$fa_category['itemCategoryID']] : 0;
                /* //final Total Cal*/
                $dep2 += array_key_exists($fa_category['itemCategoryID'], $retrunChargeThisYears) ? $retrunChargeThisYears[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right"
                    onclick="viewAssetRegister(this)"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>" data-type="cur_year"
                    data-page="view_dep_register"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunChargeThisYears) ? number_format($retrunChargeThisYears[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }

            ?>
            <td class="text-right"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($dep2, $decimal_places); ?></strong></td>
        </tr>
        <tr>
            <td>Disposals</td>
            <?php

            foreach ($fa_categories as $key => $fa_category) {
                /*final Total Cal*/
                $totalDep[$fa_category['itemCategoryID']] -= array_key_exists($fa_category['itemCategoryID'], $retrunDepDisposal) ? $retrunDepDisposal[$fa_category['itemCategoryID']] : 0;
                /* //final Total Cal*/
                $dep3 += array_key_exists($fa_category['itemCategoryID'], $retrunDepDisposal) ? $retrunDepDisposal[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right"
                    onclick="viewAssetRegister(this)"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>" data-type="disposal"
                    data-page="view_dep_register"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunDepDisposal) ? '(' . number_format($retrunDepDisposal[$fa_category['itemCategoryID']], $decimal_places) . ')' : '(' . number_format(0, $decimal_places) . ')'; ?></td>
                <?php
            }

            ?>
            <td class="text-right"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($dep3, $decimal_places); ?></strong></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td>As at end
                of <?php echo date('Y', strtotime($startFinanceYear)) . '/' . date('M', strtotime($dateAsOf)) ?></td>
            <?php
            foreach ($fa_categories as $fa_category) {
                $dep4 += array_key_exists($fa_category['itemCategoryID'], $totalDep) ? $totalDep[$fa_category['itemCategoryID']] : 0;
                ?>
                <td class="text-right total"><?php echo array_key_exists($fa_category['itemCategoryID'], $totalDep) ? number_format($totalDep[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }

            ?>
            <td class="text-right total"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($dep4, $decimal_places); ?></strong></td>
        </tr>
        </tfoot>
    </table>
    <table class="<?php echo table_class() ?> assetRegisterTable">
        <tbody>
        <tr>
            <td colspan="<?php echo $fa_categories_count + 1 ?>"><strong>Net Book Value</strong></td>
            <td style="background-color: rgba(119, 119, 119, 0.33)"></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th style="">As at end
                of <?php echo date('Y', strtotime($startFinanceYear)) . '/' . date('M', strtotime($dateAsOf)) ?></th>
            <?php
            $netBookValue = 0;
            foreach ($fa_categories as $fa_category) {
                $netBookValue += ($totalCost[$fa_category['itemCategoryID']]) - ($totalDep[$fa_category['itemCategoryID']]);
                ?>
                <td class="text-right total" style="width: 120px;"><?php echo number_format((($totalCost[$fa_category['itemCategoryID']]) - ($totalDep[$fa_category['itemCategoryID']])), $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right total"
                style="background-color: rgba(119, 119, 119, 0.33);width: 150px">
                <strong><?php echo number_format($netBookValue, $decimal_places) ?></strong></td>
        </tr>
        </tfoot>
    </table>
</div>