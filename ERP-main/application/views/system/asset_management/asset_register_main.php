<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_register');
echo head_page($title, false);


/*echo head_page('Asset Register', false);*/

$companyId = current_companyID();
$categories = $_POST['data_arr'];

$fiancecategory = array();
$mainCategory = array();
$locationID = array();
$subCategory = array();

$date_format_policy = date_format_policy();
$datAf = $categories['dateAsOf'];
$dateAsOf = input_format_date($datAf,$date_format_policy);
//$dateAsOf = $categories['dateAsOf'];
if (array_key_exists('fiancecategory', $categories)) {
    $fiancecategory = array_filter($categories['fiancecategory']);
    $fiancecategoryJoin = join(',', $fiancecategory);
}
if (array_key_exists('mainCategory', $categories)) {
    $mainCategory = array_filter($categories['mainCategory']);
    $mainCategoryJoin = join(',', $mainCategory);
}

if (array_key_exists('locationID', $categories)) {
    $locationID = array_filter($categories['locationID']);
    $locationIDJoin = join(',', $locationID);
}
if (array_key_exists('segment', $categories)) {
    $segment = array_filter($categories['segment']);
    $segmentIDJoin = join(',', $segment);
}
/*if (array_key_exists('subCategory', $categories)) {
    $subCategory = $categories['subCategory'];
}*/


$fieldName = $categories['fieldName'];


/*$subCategory = $_POST['subCategory'];*/
$wh = '';
if ($fiancecategory) {
    //$wh .= "AND srp_erp_fa_asset_master.faCatID IN ($fiancecategoryJoin)";
}

if ($mainCategory) {
    $wh .= " AND srp_erp_fa_asset_master.faCatID IN ($mainCategoryJoin)";//faSubCatID
}

if ($locationID) {
    $wh .= " AND srp_erp_fa_asset_master.currentLocation IN ($locationIDJoin)";//faSubCatID
}

if ($segment) {
    $wh .= " AND srp_erp_fa_asset_master.segmentID IN ($segmentIDJoin)";//faSubCatID
}

/*if ($subCategory) {
    $wh .= "AND srp_erp_fa_asset_master.faSubCatID IN ($subCategory)";//faSubCatID2
}*/

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
 `srp_erp_fa_asset_master`.`docOrigin`,
 `srp_erp_fa_asset_master`.`docOriginSystemCode`,
 `srp_erp_fa_asset_master`.`costGLCode`,
 `srp_erp_fa_asset_master`.`faID` AS faID,
 `srp_erp_fa_asset_master`.`faUnitSerialNo`,
  DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d')AS dateAQ,
  DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d')AS dateDEP,
 `srp_erp_fa_asset_master`.`transactionCurrencyDecimalPlaces` AS transactionCurrencyDecimalPlaces,
 `srp_erp_fa_asset_master`.`companyReportingDecimalPlaces` AS companyReportingDecimalPlaces,
 `srp_erp_fa_asset_master`.`assetDescription` AS assetDescription,
 `srp_erp_location`.`locationName` AS locationName,
 `srp_erp_fa_asset_master`.`serialNo` AS serialNo,
 `srp_erp_itemcategory`.`description` AS description,
 `srp_erp_segment`.`segmentCode` AS segmentCode
FROM
	srp_erp_fa_asset_master
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
LEFT JOIN srp_erp_location ON srp_erp_fa_asset_master.currentLocation = srp_erp_location.locationID
LEFT JOIN srp_erp_segment ON srp_erp_fa_asset_master.segmentID = srp_erp_segment.segmentID
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
AND `srp_erp_fa_asset_master`.`faCatID` IN ({$mainCategoryJoin}) AND srp_erp_fa_asset_master.currentLocation IN ({$locationIDJoin}) AND srp_erp_fa_asset_master.segmentID IN ({$segmentIDJoin})")->result_array();

$this->db->SELECT("itemCategoryID,description");
$this->db->FROM('srp_erp_itemcategory');
$this->db->where_in('itemCategoryID', $mainCategory);
$result = $this->db->get()->result_array();
$result = array_column($result, 'description');

$this->db->SELECT("locationID,locationName");
$this->db->FROM('srp_erp_location');
$this->db->where_in('locationID', $locationID);
$locatn = $this->db->get()->result_array();
$locatn = array_column($locatn, 'locationName');

$this->db->SELECT("segmentID,description");
$this->db->FROM('srp_erp_segment');
$this->db->where_in('segmentID', $segment);
$segmnt = $this->db->get()->result_array();
$segmnt = array_column($segmnt, 'description');




?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="tab-content">
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <button class="btn btn-pdf btn-xs hide" id="btn-pdf" type="button" onclick="generatePdf()">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                    </button>
                    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Asset Register.xls"
                       onclick="var file = tableToExcel('asset_register_table', 'Asset Register'); $(this).attr('href', file);">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                    </a></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center reportHeaderColor">
                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                </div>
                <!--<div class="text-center reportHeader reportHeaderColor">Asset Register</div>-->
                <div class="text-center reportHeaderColor"><strong><?php echo $this->lang->line('assetmanagement_as_of');?><!--As of-->: </strong><?php echo $datAf ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <strong><?php echo $this->lang->line('common_filters');?><!--Filters--> <i class="fa fa-filter"></i></strong><br>
                <strong><i><?php echo $this->lang->line('common_item_category');?><!--Item Category-->:</i></strong> <?php echo join(",", $result) ?>
                <br>
                <strong><i><?php echo $this->lang->line('common_Location');?><!--Location-->:</i></strong> <?php echo join(",", $locatn) ?>
                <br>
                <strong><i><?php echo $this->lang->line('common_segment');?><!--Segment-->:</i></strong> <?php echo join(",", $segmnt) ?>
            </div>
        </div>
        <div class="row">
            <table class="<?php echo table_class(); ?>" id="asset_register_table">
                <thead>
                <tr>
                    <th rowspan="2"><?php echo $this->lang->line('assetmanagement_finance_category');?><!--Finance Category--></th>
                    <th rowspan="2"><?php echo $this->lang->line('assetmanagement_fa_code');?><!--FA Code--></th>
                    <th rowspan="2"><?php echo $this->lang->line('assetmanagement_serial_no');?><!--Serial No-->.</th>
                    <th rowspan="2"><?php echo $this->lang->line('assetmanagement_asset_description');?><!--Asset Description--></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_Location');?><!--Location--></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_origin_documnet_code');?><!--Origin Document Code--></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                    <th rowspan="2"><?php echo $this->lang->line('assetmanagement_date_acquired');?><!--Date Acquired--></th>
                    <th rowspan="2"><?php echo $this->lang->line('assetmanagement_dep_started_date');?><!--Dep Started Date--></th>
                    <?php if ($fieldName != 'companyReportingAmount') { ?>
                        <th colspan="3"><?php echo $this->common_data['company_data']['company_default_currency'] ?></th>
                    <?php } else { ?>
                        <th colspan="3"><?php echo $this->common_data['company_data']['company_reporting_currency'] ?></th>
                    <?php } ?>
                    <th rowspan="2" width="50px">#</th>
                </tr>
                <tr>
                    <?php if ($fieldName != 'companyReportingAmount') { ?>
                        <th><?php echo $this->lang->line('common_unit_cost');?><!--Unit Cost--></th>
                        <th><?php echo $this->lang->line('assetmanagement_acc_dep_amount');?><!--Acc Dep Amount--></th>
                        <th><?php echo $this->lang->line('assetmanagement_net_book_value');?><!--Net Book Value--></th>
                    <?php } else { ?>
                        <th><?php echo $this->lang->line('common_unit_cost');?><!--Unit Cost--></th>
                        <th><?php echo $this->lang->line('assetmanagement_acc_dep_amount');?><!--Acc Dep Amount--></th>
                        <th><?php echo $this->lang->line('assetmanagement_net_book_value');?><!--Net Book Value--></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php

                $datas = array_group_by($datas, 'description');

                $grandAmount = 0;
                $companyid  = current_companyID();
                $grandAmountDep = 0;
                $grandNetBookValue = 0;
                $decimal=2;
                $documentcodedrilldown = [];
                foreach ($datas as $key => $data) {
                  //echo '<pre>'; print_r($data);  echo '</pre>';
                    $amount = 0;
                    $amountDep = 0;
                    $netBookValue = 0;


                    ?>
                    <tr>
                        <td colspan="11"><span class="mainCategoryHead2"><?php echo $key ?></span></td>
                    </tr>
                    <?php
                    foreach ($data as $item) {
                        //echo '<pre>'; print_r($data);  echo '</pre>';
                        $decimal=$item['companyLocalCurrencyDecimalPlaces'];
                        if($item["docOrigin"]=='GRV')
                        {
                            $documentcodedrilldown = $this->db->query("SELECT grvPrimaryCode as systemcode FROM `srp_erp_grvmaster` where companyID = '{$companyid}' AND grvAutoID = '".$item['docOriginSystemCode']."'")->row_array();
                        }else if($item["docOrigin"]=='PV')
                        {
                            $documentcodedrilldown= $this->db->query("SELECT PVcode as systemcode FROM `srp_erp_paymentvouchermaster` where companyID = '{$companyid}' AND payVoucherAutoId = '".$item['docOriginSystemCode']."'")->row_array();
                        }else if($item["docOrigin"]=='BSI')
                        {
                            $documentcodedrilldown = $this->db->query("SELECT bookingInvCode as systemcode FROM `srp_erp_paysupplierinvoicemaster` where companyID = '{$companyid}' And InvoiceAutoID = '".$item['docOriginSystemCode']."' ")->row_array();
                        }else
                        {
                            $documentcodedrilldown = '';
                        }
                        ?>
                        <tr>
                            <td><?php echo $item['description']; ?></td>
                            <td><?php echo $item['faCode']; ?></td>
                            <td><?php echo $item['faUnitSerialNo']; ?></td>

                            <td><?php echo $item['assetDescription']; ?></td>
                            <td><?php echo $item['segmentCode']; ?></td>
                            <td><?php echo $item['locationName']; ?></td>
                            <td>
                                  <?php if(!empty($systemcode)){?>
                                <a href="#" class="drill-down-cursor"
                                   onclick="documentPageView_modal('<?php echo $item["docOrigin"] ?>','<?php echo $item["docOriginSystemCode"] ?>')"><?php echo $documentcodedrilldown["systemcode"] ?></a>
                                  <?php }else {?>
                        <?php echo '-'?>
                <?php }?>

                            </td>

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
                                <td style="text-align: right;"><?php echo number_format($item['companyReportingAmount'],$item['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align: right;"><?php echo number_format($item['totalReportingDepAmount'],$item['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td style="text-align: right;"><?php echo number_format($item['netBookRepotingValue'],$item['companyLocalCurrencyDecimalPlaces']); ?></td>
                            <?php } ?>

                            <td><a href="#" onclick="assetMasterView('<?php echo $item['faID'] ?>')"><i
                                        class="glyphicon glyphicon-eye-open"></i></a>
                                <a onclick="configure_ivms_no();"><span title="" rel="tooltip" class="fa fa-search" data-original-title="Asset Tracing"></span></a>
                            </td>
                        </tr>
                    <?php }
                    $grandAmount += $amount;
                    $grandAmountDep += $amountDep;
                    $grandNetBookValue += $netBookValue;

                    ?>
                    <tr>
                        <td colspan="10" style="text-align: right;font-weight: bold"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                        <td style="text-align: right;font-weight: bold"><?php echo number_format($amount,$decimal) ?></td>
                        <td style="text-align: right;font-weight: bold"><?php echo number_format($amountDep,$decimal) ?></td>
                        <td style="text-align: right;font-weight: bold"><?php echo number_format($netBookValue,$decimal) ?></td>
                        <td></td>
                    </tr>
                    <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="10" style="text-align: right;"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total-->:</th>

                    <th style="text-align: right;"><?php echo number_format($grandAmount,$decimal) ?></th>
                    <th style="text-align: right;"><?php echo number_format($grandAmountDep,$decimal) ?></th>
                    <th style="text-align: right;"><?php echo number_format($grandNetBookValue,$decimal) ?></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="assetMasterViewModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="AssetCodeTitle"><?php echo $this->lang->line('assetmanagement_asset');?><!--Asset--></h4>
            </div>
            <div class="modal-body" id="assetMasterViewModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="ivms_no_cong">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Asset Tracing</h4>
            </div>
            <?php echo form_open('', 'role="form" id="ivmsnoconfig"'); ?>
            <div class="modal-body">
                <input type="hidden" name="jpmasterid" id="jpmasterid">
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <img src="<?php echo base_url('images/journeyplan/ivmsmap.jpg'); ?>" style="width: 100%; opacity: 0.3;
    filter: alpha(opacity=30);">
                        <div style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);font-weight:bold;font-size:22px;color: #ca0000"><strong>Tracing Not Configured</strong></div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!---->
<form action="<?php echo site_url('AssetManagement/generate_asset_pdf'); ?>" method="post" target="_blank" name=""
      id="pdfForm" style="display: none;">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="dateAsOf" value="<?php echo $categories['dateAsOf']; ?>">
    <input type="hidden" name="mainCategory" value="<?php echo $mainCategoryJoin; ?>">
    <input type="hidden" name="fieldName" value="<?php echo $fieldName; ?>">
</form>
<!---->
<script type="text/javascript">

    $('.headerclose').click(function () {
        fetchPage('system/asset_management/asset_register', '', 'Asset Register');
    });
    /*Asset_register_table();*/
    function Asset_register_table() {
        var Otable = $('#asset_register_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/fetch_asset_register'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var api = this.api();
                var rows = api.rows({page: 'current'}).nodes();
                var last = null;

                api.column(0, {page: 'current'}).data().each(function (group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group" style=""><td colspan="17"><strong>' + group + '</strong></td></tr>'
                        );

                        last = group;
                    }
                });

                makeTdAlign('asset_register_table', 'right', [6, 7, 8]);
            },
            "aoColumns": [
                {"mData": "description"},
                {"mData": "faCode"},
                {"mData": "serialNo"},
                {"mData": "assetDescription"},
                {"mData": "costGLCode"},
                {"mData": "dateAQ"},
                {"mData": "dateDEP"},
                <?php if ($fieldName != 'companyReportingAmount') { ?>
                {"mData": "companyLocalAmount"},
                {"mData": "companyLocalAmountDep"},
                {"mData": "netBookTransectionValue"},
                <?php } else { ?>
                {"mData": "companyReportingAmount"},
                {"mData": "totalReportingDepAmount"},
                {"mData": "netBookRepotingValue"},
                <?php } ?>
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "fiancecategory",
                    "value": '<?php echo join(',', $fiancecategory);  ?>'
                }, {"name": "mainCategory", "value": '<?php echo join(',', $mainCategory);  ?>'}, {
                    "name": "dateAsOf",
                    "value": '<?php echo $dateAsOf;  ?>'
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    "orderable": false
                }
            ]
        });
    }

    function assetMasterView(index) {
        if (index) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'faID': index, 'html': true},
                url: "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#fa_modal").modal({backdrop: "static"});
                    $('#assetMasterViewModalBody').html(data);
                    $('#AssetCodeTitle').html($('#AssetCode').text());
                    $('#assetMasterViewModal').modal('show');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function generatePdf() {
        $('#pdfForm').submit()
    }

    function configure_ivms_no() {
        $('#ivms_no_cong').modal("show");
    }
</script>
