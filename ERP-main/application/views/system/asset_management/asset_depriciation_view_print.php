<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$page_id = $extra['depMasterAutoID'];

$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];

$depMaster = $this->db->query("SELECT * FROM `srp_erp_fa_depmaster` WHERE `depMasterAutoID` = '{$page_id}'")->row_array();
?>
<div class="row">
    <div class="col-md-12" id="assetDepHeader">
        <div class="col-md-8">

        </div>
        <div class="col-md-4">
            <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank"
                   href="<?php echo site_url('/Double_entry/fetch_double_entry_asset_depreciation_master/' . $page_id . '/FAD') ?>"><span
                        class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp <?php echo $this->lang->line('assetmanagement_account_review_entries');?><!--Account Review entries-->
                </a>
                </span>
        </div>
    </div>
</div>
<hr>
<table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
    <tbody>
    <tr>
        <td style="width: 110px;"><?php echo $this->lang->line('assetmanagement_dep_month_year');?><!--Dep Month Year--></td>
        <td class="bgWhite" style="width:35%"><?php echo $depMaster['depMonthYear'] ?></td>
        <td style="width: 110px;"><?php echo $this->lang->line('assetmanagement_financial_year');?><!--Financial Year--></td>
        <td colspan="2" class="bgWhite">From: <span
                class=""><?php echo date('d/m/Y', strtotime($depMaster['FYBegin'])) ?></span> To: <span
                class=""><?php echo date('d/m/Y', strtotime($depMaster['FYEnd'])) ?></td>
    </tr>
    <tr>
        <td><?php echo $this->lang->line('assetmanagement_doc_code');?><!--Doc Code--></td>
        <td class="bgWhite"><?php echo $depMaster['depCode'] ?></td>
        <td><?php echo $this->lang->line('assetmanagement_financial_period');?><!--Financial Period--></td>
        <td class="bgWhite" colspan="2">From: <span
                class=""><?php echo date('d/m/Y', strtotime($depMaster['FYPeriodDateFrom'])) ?></span><?php echo $this->lang->line('common_to');?> <!--To-->: <span
                class=""><?php echo date('d/m/Y', strtotime($depMaster['FYPeriodDateTo'])) ?></span></td>
    </tr>
    <tr>
        <td><?php echo $this->lang->line('assetmanagement_doc_date');?><!--Doc Date--></td>
        <td class="bgWhite"><?php echo date('d/m/Y', strtotime($depMaster['depDate'])) ?></td>
        <td></td>
        <td class="bgWhite" colspan="2"></td>
    </tr>
    </tbody>
</table>
<hr>
<div class="row">
    <div class="table-responsive">
        <table id="assetMaster_dep_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_fa_code');?><!--FA Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_asset_description');?><!--Asset Description--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_date_capitalized');?><!--Date Capitalized--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_depreciation_start_date');?><!--Depreciation Start Date--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_debit_gl_code');?><!--Debit GL Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_credit_gl_code');?><!--Credit GL Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th rowspan="2"><?php echo $this->lang->line('assetmanagement_dep');?><!--DEP--> %</th>
                <th colspan="2"><?php echo $this->lang->line('assetmanagement_cost_amount');?><!--Cost Amount--></th>
                <th colspan="2"><?php echo $this->lang->line('assetmanagement_dep_amount');?><!--Dep Amount--></th>
            </tr>
            <tr>
                <th style="">(<?php echo $com_currency; ?>)</th>
                <th style="">(<?php echo $rep_currency; ?>)</th>
                <th style="">(<?php echo $com_currency; ?>)</th>
                <th style="">(<?php echo $rep_currency; ?>)</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="8">Total</td>
                <td><div class="pull-right"><?php echo format_number($total["companyLocalAmount"],$total["companyLocalCurrencyDecimalPlaces"]); ?></div></td>
                <td><div class="pull-right"><?php echo format_number($total["companyReportingAmount"],$total["companyReportingDecimalPlaces"]); ?></div></td>
                <td><div class="pull-right"><?php echo format_number($total["DepcompanyLocalAmount"],$total["DepcompanyLocalCurrencyDecimalPlaces"]); ?></div></td>
                <td><div class="pull-right"><?php echo format_number($total["DepcompanyReportingAmount"],$total["DepcompanyReportingCurrencyDecimalPlaces"]); ?></div></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        feedAssetdep();
    })

    function feedAssetdep() {
        var Otable = $('#assetMaster_dep_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_asset_dep_generated'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumns": [
                {"mData": "faCode"},
                {"mData": "assetDescription"},
                {"mData": "postDate"},
                {"mData": "dateDEP"},
                {"mData": "DEPGLCODE"},
                {"mData": "costGLCode"},
                {"mData": "segmentCode"},
                {"mData": "depPercent"},
                {"mData": "companyLocalAmount"},
                {"mData": "companyReportingAmount"},
                {"mData": "DepcompanyLocalAmount"},
                {"mData": "DepcompanyReportingAmount"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,1,2,3,4,5,6,7,8,9,10,11]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "depMasterAutoID", "value": "<?php echo $page_id;  ?>"});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "fnDrawCallback": function (oSettings) {
                makeTdAlign('assetMaster_dep_table', 'right', [9, 10]);
            }
        });
    }