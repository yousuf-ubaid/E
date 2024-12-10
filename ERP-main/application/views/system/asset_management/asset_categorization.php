<?php echo head_page('Asset Categorization', false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5 text-center">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Confirmed /
                    Approved
                </td>
                <td><span class="label label-danger">&nbsp;</span> Not Confirmed
                    / Not Approved
                </td>
                <td><span class="label label-warning">&nbsp;</span> Refer-back
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center"></div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/asset_management/add_new_asset',null,'Add New Asset','FA');">
            <i class="fa fa-plus"></i>Add New Asset
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="assetMaster_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 10%">Asset Code</th>
            <th>Description</th>
            <th>Serial No</th>
            <th>Main Cateogry</th>
            <th style="min-width: 7%">&nbsp;Sub Category</th>
            <th style="min-width: 5%">GRV No</th>
            <th style="max-width: 4%">Confirmed</th>
            <th style="max-width: 4%">Approved</th>
            <th style="max-width: 3%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feedAssetMaster();
    });

    function feedAssetMaster() {
        var Otable = $('#assetMaster_table').DataTable({
            "Processing": true,
            "ServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_assetmaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function () {
            },
            "aoColumns": [
                {"mData": "segmentID"},
                {"mData": "companyID"},
                {"mData": "segmentCode"},
                {"mData": "description"},
                {"mData": "action"},
                {"mData": "status"},
                {"mData": "status"},
                {"mData": "status"}
            ],
            "columnDefs": [{
                "targets": [4, 5],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
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