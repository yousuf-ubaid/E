<?php
$page_id = $datas['assetdisposalMasterAutoID'];

$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];
?>
<table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
    <tbody>
    <tr>
        <td style="width: 110px;">Document Code</td>
        <td class="bgWhite" style="width:35%"><?php echo $datas['disposalDocumentCode'] ?></td>
        <td style="width: 110px;">Financial Year</td>
        <td colspan="2" class="bgWhite">From: <span
                class=""><?php echo date('d/m/Y', strtotime($datas['FYBegin'])) ?></span> To: <span
                class=""><?php echo date('d/m/Y', strtotime($datas['FYEnd'])) ?></td>
    </tr>
    <tr>
        <td>Document Date</td>
        <td class="bgWhite"><?php echo date('d/m/Y', strtotime($datas['disposalDocumentDate'])) ?></td>
        <td>Financial Period</td>
        <td class="bgWhite" colspan="2">From: <span
                class=""><?php echo date('d/m/Y', strtotime($datas['FYPeriodDateFrom'])) ?></span> To: <span
                class=""><?php echo date('d/m/Y', strtotime($datas['FYPeriodDateTo'])) ?></span></td>
    </tr>
    <tr>
        <td>Segment</td>
        <td class="bgWhite"><?php echo $datas['segmentCode'] ?></td>
        <td>Narration</td>
        <td class="bgWhite" colspan="2"><?php echo $datas['narration'] ?></td>
    </tr>
    </tbody>
</table>
<hr>

<div class="row">
    <div class="table-responsive">
        <table id="assetMaster_dep_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th style="width: 80px;">Asset No</th>
                <th style="width: 80px;">Serial No</th>
                <th>Description</th>
                <th style="width: 54px;">Acq Date</th>
                <th style="width: 60px;">Cost</th>
                <th style="width: 60px;">Acc Dep</th>
                <th style="width: 60px;">NBV</th>
                <th style="width: 80px;">Disposal Amt</th>
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