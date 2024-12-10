<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


?>

<div class="modal fade" id="itemMasterSubConfig_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-red">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('accounts_receivable_tr_sub_item_configuration');?><!--Sub Item Configuration--> </h4>
            </div>
            <form id="subItemList_frm">
                <div class="modal-body">
                    <?php //echo $invoiceAutoID ?>
                    <div id="load_itemMasterSub_config_modal_body">

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="button" id="save_subItem" onclick="save_subItemList()" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function save_subItemList() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#subItemList_frm").serialize(),
            url: "<?php echo site_url('Invoices/save_subItemList'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                } else if (data['error'] == 0) {
                    myAlert('s', data['message']);
                }

                if ($('#soldDocumentID').val() == 'ST') {
                    fetch_detail();
                }
            }, error: function (xhr, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', xhr.responseText + ' : ' + errorThrown);
            }
        });
    }

    function load_itemMasterSub_config_modal(id, documentID, warehouseID, status = null,subItemapplicableon = null) {
        if(status){
            $('#save_subItem').hide();
        }
        if(status == 1 && subItemapplicableon == 2){
            $('#save_subItem').hide();
        }
        $("#itemMasterSubConfig_modal").modal('show');
        load_subItemList(id, documentID, warehouseID,status,subItemapplicableon);
    }

    function load_subItemList(detailID, documentID, warehouseID,status = null,subItemapplicableon = null) {
        var sub_Itemapplicableon = (subItemapplicableon == null ? 1: subItemapplicableon);
        var status = (status == null || status == 0 ? 0: status);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {detailID: detailID, documentID: documentID, warehouseID: warehouseID,status: status,subItemapplicableon: sub_Itemapplicableon},
            url: "<?php echo site_url('Invoices/load_subItemList'); ?>",
            beforeSend: function () {
                $("#load_itemMasterSub_config_modal_body").html('<div style="text-align: center; margin:10px;"><i class="fa fa-refresh fa-spin"></i></div>')
            },
            success: function (data) {
                $("#load_itemMasterSub_config_modal_body").html(data)
            }, error: function (xhr, textStatus, errorThrown) {
                $("#load_itemMasterSub_config_modal_body").html(xhr.responseText + ' : ' + errorThrown);
            }
        });
    }
</script>