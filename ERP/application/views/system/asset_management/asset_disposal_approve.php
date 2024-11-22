<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$page_id = $_POST['assetdisposalMasterAutoID'];
//$confirmed = $confirmedYN;

$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];
?>
<div class="row">
    <div class="table-responsive">
        <table id="assetMaster_dep_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_asset_no');?><!--Asset No--></th>
                <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_serial_no');?><!--Serial No--></th>
                <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="width: 54px;"><?php echo $this->lang->line('assetmanagement_acq_date');?><!--Acq Date--></th>
                <th style="width: 60px;"><?php echo $this->lang->line('common_cost');?><!--Cost--></th>
                <th style="width: 60px;"><?php echo $this->lang->line('assetmanagement_acq_dep');?><!--Acc Dep--></th>
                <th style="width: 60px;"><?php echo $this->lang->line('assetmanagement_nbv');?><!--NBV--></th>
                <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_disposal_amt');?><!--Disposal Amt--></th>
            </tr>
            </thead>
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
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_selected_disposal_asset'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSetting) {
                makeTdAlign('selectedAssetTable', 'right', [4, 5, 6])
            },
            "aoColumns": [
                {"mData": "faCode"},
                {"mData": "serialNo"},
                {"mData": "assetDescription"},
                {"mData": "dateAQ"},
                {"mData": "companyLocalAmount"},
                {"mData": "accLocalAmount"},
                {"mData": "netBookValueLocalAmount"},
                {"mData": "disposalAmount"}
            ],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5, 6, 7],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push(
                    {name: "assetdisposalMasterAutoID", value: '<?php echo $page_id; ?>'}
                );
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

</script>