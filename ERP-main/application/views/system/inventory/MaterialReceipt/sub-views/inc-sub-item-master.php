<div class="modal fade" id="subItemMaster_modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="subItemMaster_form">
                <div class="modal-header">
                    <button type="button" class="close text-red" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Sub Item Master </h4>
                </div>
                <div class="modal-body" style="min-height: 300px;">
                    <div id="subItemMasterList"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveSubItemMasterTmp()">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function load_itemMasterSub_MRN_modal(mrnDetailID) {
        $("#subItemMaster_modal").modal('show');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {mrnDetailID: mrnDetailID},
            url: "<?php echo site_url('MaterialReceiptNote/load_itemMasterSub'); ?>",
            beforeSend: function () {
                $("#subItemMasterList").html('<div style="text-align: center; margin: 10px;"><i class="fa fa-refresh fa-spin"></i> Loading </div>');
            },
            success: function (data) {
                stopLoad();
                $("#subItemMasterList").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#subItemMasterList").html('<br>Error : ' + errorThrown);
            }
        });
    }


</script>