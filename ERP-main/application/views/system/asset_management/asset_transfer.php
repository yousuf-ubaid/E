<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_transfer');
echo head_page($title, false);

?>

<div id="assetTransferMasterDiv">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div class="row">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/asset_management/add_new_asset_transfer',null,'Add Asset Transfer');">
            <i class="fa fa-plus"></i>
            <?php echo $this->lang->line('assetmanagement_add_new_asset_transfer'); ?><!--/*Add New asset transfer*/-->
    </div>
    <hr>
    <div class="table-responsive">
        <table id="assetTransferTable" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style=""><?php echo $this->lang->line('assetmanagement_document_code'); ?><!--Document Code--></th>
                <th style=""><?php echo $this->lang->line('assetmanagement_document_date'); ?><!--Document Code--></th>
                <th><?php echo $this->lang->line('assetmanagement_transfer_from'); ?><!--Transfer from--></th>
                <th><?php echo $this->lang->line('assetmanagement_transfer_to'); ?><!--Transfer to--></th>
                <th><?php echo $this->lang->line('assetmanagement_transfer_by'); ?><!--Transfer by--></th>
                <th style=""><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                <th style="width: 50px;"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
        </table>
    </div>

</div>

<script>
    $(document).ready(function () {
        getAssetTransfer();

        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });

    });

    function getAssetTransfer() {
        $('#assetTransferTable').DataTable({
            "language":
                {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetTransfer/getAssetTransfer'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                const tmp_i = oSettings._iDisplayStart;
                const iLen = oSettings.aiDisplay.length;
                let x = 0;
                for (let i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "documentCode"},
                {"mData": "documentDate"},
                {"mData": "locationFromName"},
                {"mData": "locationToName"},
                {"mData": "requestedEmpName"},
                {"mData": "confirmed"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function deleteAssetTransfer(id) {
        if (id) {
            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            }, function () {
                $.ajax({
                    type: 'POST',
                    url: "<?php echo site_url('AssetTransfer/delete'); ?>",
                    data: {id: id},
                    success: function (response) {
                        const data = JSON.parse(response);
                        myAlert(data.status === 's' ? 's' : 'e', 'Message: ' + data.message);
                        if (data.status === 's') {
                            getAssetTransfer()
                        }
                    },
                    error: function () {
                        myAlert('e', 'Message: An error occurred while deleting the update.');
                    }
                });
            });
        }
    }
</script>