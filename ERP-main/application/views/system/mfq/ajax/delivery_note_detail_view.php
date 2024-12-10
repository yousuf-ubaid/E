<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div>
    <?php echo form_open('', 'role="form" id="delivery_note_details_frm"'); ?>
        <div class="table-responsive">
            <table>
                <tbody>
                <tr style="border: 1px solid black;background-color: lightgray;">
                    <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_serial_no') ?><!--Sr No--></strong></td>
                    <td class="text-uppercase" style="width:10%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('common_qty') ?><!--QTY--></strong></td>
                    <td class="text-uppercase" style="width:10%;height:25px;border: 1px solid black;text-align: center;"><strong>Delivered Qty</strong></td>
                    <td style="width:20%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_purchase_order_reference') ?><!--PO Ref--> #</strong></td>
                    <td class="text-uppercase" style="width:25%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_description_or_particulars') ?><!--DESCRIPTION / PARTICULARS--></strong></td>
                    <td class="text-uppercase" style="width:15%;height:25px;border: 1px solid black;text-align: center;"><strong>Order Status</strong></td>
                    <td class="text-uppercase" style="width:10%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                </tr>

                <?php
                $x = 1;
                foreach ($details as $det) { ?>
                    <tr style="border: 1px solid black;">
                        <td class="hidden"><input class="text-right" name="deliveryNoteDetailID[]" id="deliveryNoteDetailID" value="<?php echo $det['deliveryNoteDetailID']; ?>">
                                    <input class="text-right" name="estimateMasterID[]" id="estimateMasterID" value="<?php echo $det['estimateMasterID']; ?>"></td>
                        <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><?php echo $x; ?></td>
                        <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><?php echo $det['detailQty']; ?><input class="text-right detailQty hidden" name="detailQty[]" id="detailQty" value="<?php echo $det['detailQty']; ?>"></td>
                        <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><input class="text-right deliveredQty" name="deliveredQty[]" id="deliveredQty" value="<?php echo $det['deliveredQty']; ?>" onkeyup="validateQty(this)"></td>
                        <td style="width:25%;height:25px;border: 1px solid black;text-align: center;">&nbsp;
                        <a href="#" data-type="text" data-placement="bottom"  id="poNumber_<?php echo $det['deliveryNoteDetailID']; ?>"
                                data-pk= "<?php echo $det['deliveryNoteDetailID']; ?>"
                                 data-name="expectedqty" data-title="PO number" class="xEditable poNumber" 
                                 data-value="" data-related="infectionOrDisease"><?php echo $det['estmPoNumber']; ?></a>
                    
                        </td>
                        <td style="width:20%;height:25px;border: 1px solid black;">&nbsp;<?php echo $det['itemName']; ?></td>
                        <td style="width:5%;height:25px;border: 1px solid black;vertical-align: middle;">&nbsp;<?php echo form_dropdown('orderStatus', array('1'=> 'Pending','2'=> 'Confirmed & Received'), $det['orderStatus'], 'class="form-control select2" id="orderStatus" onchange="updateOrderStatus(this)"'); ?></td>
                        <td style="width:10%;height:25px;border: 1px solid black;text-align: center;">&nbsp;&nbsp;&nbsp;<a onclick="delete_Delivery_order_Detail(<?php echo $det['deliveryNoteDetailID']; ?>)"><span class="glyphicon glyphicon-trash delete-icon"></span></a>&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <?php
                    $x++;
                } ?>
                </tbody>
            </table>
        </div>
        <div class="row col-md-12" style="margin-top: 10px;">
            <div class="text-right m-t-xs">
                <button class="btn btn-primary" type="button" onclick="add_delivery_note_details()">Save Details</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    function validateQty(element) {
        var detailQty = $(element).closest('tr').find('.detailQty').val();
        var updatedQty = $(element).closest('tr').find('.deliveredQty').val();
        if(Number(updatedQty) > Number(detailQty)) {
            myAlert('w', 'Delivered Qty Cannot be greater than Qty!');
            $(element).closest('tr').find('.deliveredQty').val('');
        }
    }

    function updateOrderStatus(element) {
        var detailID = $(element).closest('tr').find('#deliveryNoteDetailID').val();
        var estimateMasterID = $(element).closest('tr').find('#estimateMasterID').val();
        var status = element.value;
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to change Order Status!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_save');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('MFQ_DeliveryNote/save_est_order_status'); ?>",
                    data: {deliveryNoteDetailID: detailID, estimateMasterID: estimateMasterID, status : status},
                    cache: false,
                    beforeSend: function () {
                        // startLoad();
                    },
                    success: function (data) {
                        // stopLoad();
                        myAlert(data[0], data[1]);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // stopLoad();
                        myAlert('e', 'Message: ' + "Select Widget");
                    }
                });
            });
    }

    $(".poNumber").editable({
        url: '<?php echo site_url('MFQ_DeliveryNote/update_dn_po_number') ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){
                    var qty_xEditable = $('#poNumber');
                    setTimeout(function (){
                        qty_xEditable.attr('data-pk', qty_xEditable.html());
                        },400);
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });
</script>