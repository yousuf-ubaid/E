<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);


$this->lang->load('common', $primaryLanguage);
?>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="Item Master Sub Modal"
     id="itemMasterSub_modalWindow">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_master_sub_item_configuration');?><!--Sub Item Configuration--> </h4>
            </div>
            <div class="modal-body" id="subItemMasterListview">

            </div>
        </div>
    </div>
</div>


<script>
    /*function subItemConfigList_modal(id) {
        $('#itemMasterSub_modalWindow ').modal('show');
        load_subItemList(id);
    }*/
    function subItemConfigList_modal(id) {
        $("#itemMasterSub_modalWindow").modal('show');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {itemCode: id},
            url: "<?php echo site_url('ItemMaster/load_sub_itemMaster_view'); ?>",
            beforeSend: function () {
                $("#subItemMasterListview").html('<div style="text-align: center; margin: 10px;"><i class="fa fa-refresh fa-spin"></i> Loading </div>');
            },
            success: function (data) {
                stopLoad();
                $("#subItemMasterListview").html(data);
                $('#itemMasterSubItemListTbl').DataTable({
                    aLengthMenu: [
                        [25, 50, 100, 200, -1],
                        [25, 50, 100, 200, "All"]
                    ],
                    iDisplayLength: 100
                });
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#subItemMasterListview").html('<br>Error : ' + errorThrown);
            }
        });
    }

    /*function load_subItemList(id) {
        Otable = $('#itemMasterSubItemListTbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('ItemMaster/fetch_subItem'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $("[name='itemchkbox']").bootstrapSwitch();
            },
            "aoColumns": [
                {"mData": "subItemSerialNo"},
                {"mData": "subItemCode"},
                {"mData": "description"},
                {"mData": "productReferenceNo"},
                {"mData": "warehouseDescription"}
            ],
            "columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "itemCode", "value": id});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }*/


    /*itemMasterSubItemListTbl*/

    /*itemMasterSub_modalWindow */
</script>