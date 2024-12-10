<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);


$companyId = current_companyID();
$company_code = $this->common_data['company_data']['company_code'];
$decimal_places = $this->common_data['company_data']['company_default_decimal'];

$dateAsOf = $date;

$itemCategoryID = $this->db->query("SELECT itemCategoryID FROM `srp_erp_itemcategory` WHERE `categoryTypeID` = '3' AND `companyID` = '{$companyId}'")->row_array();

$fa_categories = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_itemcategory WHERE companyID='$companyId' AND masterID='{$itemCategoryID['itemCategoryID']}'")->result_array();

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


// echo '<pre>';
// echo $this->db->last_query();
// exit;


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
		srp_erp_fa_asset_master.faCatID,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
	WHERE
		(
			srp_erp_fa_asset_master.disposedDate > '{$lastFinanceYear}'
			OR srp_erp_fa_asset_master.disposedDate IS NULL
		)
) disposedAssets ON srp_erp_fa_depmaster.depMasterAutoID = disposedAssets.depMasterAutoID
LEFT JOIN srp_erp_itemcategory ON disposedAssets.faCatID = srp_erp_itemcategory.itemCategoryID
LEFT JOIN srp_erp_fa_asset_master ON disposedAssets.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.approvedYN = 1
AND srp_erp_fa_depmaster.companyID = '{$companyId}'
AND srp_erp_fa_depmaster.depDate <= '{$lastFinanceYear}'
GROUP BY
	disposedAssets.faCatID")->result_array();

// echo '<pre>';
// echo $this->db->last_query();
// exit;
$retrunDepBigYear = array();

foreach ($depStarts as $depStart) {
    $retrunDepBigYear[$depStart['itemCategoryID']] = $depStart['companyLocalDepAmount'];
}
/*
echo '<pre>'; print_r($retrunDepBigYear);
exit;*/

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
LEFT JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
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
<style>
    .assetRegisterTable td:not(:first-child) {
        width: 100px !important;
    }

    .assetRegisterTable th:not(:first-child) {
        width: 100px !important;
    }

    /*.assetRegisterTable td:last-child,.assetRegisterTable th:last-child {
        background-color: rgba(119, 119, 119, 0.33);
    }*/

    /*.assetRegisterTable tr:not(:first-child):hover td:not(:last-child) {*/
    .assetRegisterTable tbody td:not(:first-child):not(:last-child):hover {
        cursor: pointer !important;
        background-color: #DEDEDE;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePdf()">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
            </button>
            <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Asset Register Summary.xls"
               onclick="var file = tableToExcel('assetRegisterTable', 'Asset Register Summary'); $(this).attr('href', file);">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
            </a></div>
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
                <th><?php echo $fa_category['description']; ?></th>
                <?php
            }
            ?>
            <th style="background-color: rgba(119, 119, 119, 0.33)"><?php echo $this->lang->line('common_total');?><!--Total--></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="<?php echo $fa_categories_count + 1 ?>"><strong><?php echo $this->lang->line('common_cost');?><!--Cost--></strong></td>
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
            <td><?php echo $this->lang->line('assetmanagement_additions_during_the_year');?><!--Additions during the year--></td>
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
            <td><?php echo $this->lang->line('assetmanagement_disposal');?><!--Disposals--></td>
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
            <td><?php echo $this->lang->line('assetmanagement_as_at_end_of');?><!--As at end of--> <?php echo date('Y', strtotime($dateAsOf)) . '/' . date('M', strtotime($dateAsOf)) ?></td>
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

    <hr>
    <!--Depreciation-->
    <table class="<?php echo table_class() ?> assetRegisterTable">
        <tbody>
        <tr>
            <td colspan="<?php echo $fa_categories_count + 1 ?>"><strong><?php echo $this->lang->line('assetmanagement_depreciation');?><!--Depreciation--></strong></td>
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
                <td class="text-right"
                    onclick="viewAssetRegister(this)" data-type="pre_year" data-page="view_dep_register"
                    data-index="<?php echo $fa_category['itemCategoryID']; ?>"><?php echo array_key_exists($fa_category['itemCategoryID'], $retrunDepBigYear) ? number_format($retrunDepBigYear[$fa_category['itemCategoryID']], $decimal_places) : number_format(0, $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($dep1, $decimal_places); ?></strong></td>
        </tr>
        <tr>
            <td><?php echo $this->lang->line('assetmanagement_charge_for_the_period');?><!--Charge For The Period--></td>
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
            <td><?php echo $this->lang->line('assetmanagement_depreciation_for_disposed_assets');?><!--Depreciation for Disposed Assets--></td>
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
            <td><?php echo $this->lang->line('assetmanagement_as_at_end_of');?><!--As at end of-->
                <?php echo date('Y', strtotime($dateAsOf)) . '/' . date('M', strtotime($dateAsOf)) ?></td>
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
    <hr>
    <table class="<?php echo table_class() ?> assetRegisterTable">
        <tbody>
        <tr>
            <td colspan="<?php echo $fa_categories_count + 1 ?>"><strong><?php echo $this->lang->line('assetmanagement_net_book_value');?><!--Net Book Value--></strong></td>
            <td style="background-color: rgba(119, 119, 119, 0.33)"></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th style=""><?php echo $this->lang->line('assetmanagement_as_at_end_of');?><!--As at end of--> <?php echo date('Y', strtotime($dateAsOf)) . '/' . date('M', strtotime($dateAsOf)) ?></th>
            <?php
            $netBookValue = 0;
            foreach ($fa_categories as $fa_category) {
                $netBookValue += ($totalCost[$fa_category['itemCategoryID']]) - ($totalDep[$fa_category['itemCategoryID']]);
                ?>
                <td class="text-right total"><?php echo number_format((($totalCost[$fa_category['itemCategoryID']]) - ($totalDep[$fa_category['itemCategoryID']])), $decimal_places); ?></td>
                <?php
            }
            ?>
            <td class="text-right total"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($netBookValue, $decimal_places) ?></strong></td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="assetRegisterViewModal">
    <div class="modal-dialog" style="width: 94% !important;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="AssetCodeTitle"><?php echo $this->lang->line('assetmanagement_asset');?><!--Asset--></h4>
            </div>
            <div class="modal-body" id="assetRegisterViewModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<!---->
<form action="<?php echo site_url('AssetManagement/generate_asset_register_summary_pdf'); ?>" method="post"
      target="_blank" name="" id="pdfForm" style="display: none;">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="dateAsOf" value="<?php echo $dateAsOf; ?>">
</form>
<!---->
<script>
    function viewAssetRegister(item) {
        var startFinanceYear = '<?php echo $startFinanceYear; ?>'
        var endFinanceYear = '<?php echo $endFinanceYear; ?>'
        var lastFinanceYear = '<?php echo $lastFinanceYear; ?>'
        var dateAsOf = '<?php echo $dateAsOf; ?>'
        var categroy = $(item).data('index');
        var type = $(item).data('type');
        var page = $(item).data('page');
        if (page == 'view_asset_register') {
            var url = "<?php echo site_url('AssetManagement/view_asset_register'); ?>";
        } else if (page == 'view_dep_register') {
            var url = "<?php echo site_url('AssetManagement/view_dep_register'); ?>";
        } else {
            return false;
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                categroy: categroy,
                startFinanceYear: startFinanceYear,
                endFinanceYear: endFinanceYear,
                lastFinanceYear: lastFinanceYear,
                dateAsOf: dateAsOf,
                type: type
            },
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#assetRegisterViewModalBody').html(data);
                $('#assetRegisterViewModal').modal('show');
            },
            error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function generatePdf() {
        $('#pdfForm').submit()
    }
</script>
