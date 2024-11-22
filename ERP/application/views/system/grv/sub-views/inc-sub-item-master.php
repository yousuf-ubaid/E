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
                    <div id="subItemMasterList" style="overflow: auto"></div>

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
    function load_itemMasterSub_modal(grvDetailsID, documentID) {
        $("#subItemMaster_modal").modal('show');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {grvDetailsID: grvDetailsID, documentID: documentID},
            url: "<?php echo site_url('Grv/load_itemMasterSub'); ?>",
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

    function saveSubItemMasterTmp() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#subItemMaster_form").serialize(),
            url: "<?php echo site_url('Grv/saveSubItemMasterTmpDynamic'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Error : ' + errorThrown);
            }
        });
    }


</script>