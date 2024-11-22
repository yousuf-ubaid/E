<?php
$page_id = $depMasterAutoID;
//$confirmed = $confirmedYN;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];
?>
<div class="row">
    <div class="table-responsive">
        <table id="assetMaster_dep_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th rowspan="2" style="min-width: 10%"><?php echo $this->lang->line('assetmanagement_fa_code');?><!--FA Code--></th>
                <th rowspan="2" style="min-width: 400px !important;"><?php echo $this->lang->line('assetmanagement_asset_description');?><!--Asset Description--></th>
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
                <td colspan="3"><?php echo $this->lang->line('common_total');?><!--Total--></td>
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
            "fnDrawCallback": function () {
            },
            "aoColumns": [
                {"mData": "faCode"},
                {"mData": "assetDescription"},
                {"mData": "depPercent"},
                {"mData": "companyLocalAmount"},
                {"mData": "companyReportingAmount"},
                {"mData": "DepcompanyLocalAmount"},
                {"mData": "DepcompanyReportingAmount"}
            ],
            "columnDefs": [{
                "targets": [2, 3, 4, 5, 6],
                "orderable": false
            }],
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
                makeTdAlign('assetMaster_dep_table', 'right', [2, 3, 4, 5, 6, 7, 8]);
            }
        });
    }

</script>