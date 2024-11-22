<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .tdIn {
        width: 140px;
        padding: 2px;
    }

    .warehouseTransferQty {
        display: inline;
        height: 24px;
        padding: 0px;
        padding-right: 2px;
        padding-left: 2px;
        font-size: 11px;
        width: 70px;
    }

    tr:hover, tr.selected {
        background-color: #E3E1E7;
        opacity: 1;
        z-index: -1;
    }

</style>

<div class="col-md-12" style="margin-bottom: 25px">
    <div class="table-responsive">
        <table id="warehoouseTranferTbl" class="<?php echo table_class(); ?>" style="">
            <thead>
            <tr>
                <th style="min-width: 10px" rowspan="2">#</th>
                <th style="min-width: 100px;" rowspan="2"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
                <th style="min-width: 150px" rowspan="2"><?php echo $this->lang->line('transaction_common_item_description');?></th><!--Item Description-->
                <th style="min-width: 10%" rowspan="2"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->

                <th style="min-width: 50px">From Location</th><!--UOM-->
                <th colspan="<?php echo count($extra['toWarehouse'])?>">Issue Qty to Location</th>
                <th style="min-width: 50px" rowspan="2">Total Issue Qty</th>
                <th style="min-width: 50px" rowspan="2">Balance In Hand</th>
                <th style="min-width: 50px" rowspan="2">
                    <?php if(!empty($extra['details'])) { ?>
                    <a onclick="delete_all_bulk_transfer_details(<?php echo $stockTransferAutoID; ?>)">
                        <span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>
                    </a>
                    <?php } else {
                        echo '&nbsp;';
                    } ?>
                </th>
            </tr>
            <tr>
                <th style="min-width: 5%">In hand Qty</th>
                <?php if($extra['toWarehouse']) {
                    foreach ($extra['toWarehouse'] as $warehouseid) {
                        $warehouse = load_warehouses($warehouseid);
                        echo '<th style="min-width: 100px" title="' . $warehouse['wareHouseDescription'] . ' | ' . $warehouse['wareHouseLocation'] . '">' . $warehouse['wareHouseCode'] . '</th>';
                    }
                }?>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($extra['details'])) {
                $a = 1;
                foreach ($extra['details'] as $det) {
                    echo '<tr>';
                    echo '<td>' . $a . '</td>';
                    echo '<td>' . $det['itemSystemCode'] . '</td>';
                    echo '<td>' . $det['itemDescription'] . '</td>';
                    echo '<td>' . $det['unitOfMeasure'] . '</td>';
                    echo '<td><span id="currentStock_'. $det['itemAutoID'] .'" disabled>' . $det['currentWareHouseStock'] . '</span></td>';
                    if ($extra['toWarehouse']) {
                        $b = 1;
                        $totalQty = 0;
                        foreach ($extra['toWarehouse'] as $items) { ?>
                            <td class="tdIn" style="text-align:center;">
                                <input class="form-control warehouseTransferQty" type="text" name="warehouseTransferQty" id="groupAdd_<?php echo $a . '_' . $b;?>"
                                       data-stockTransferAutoID="<?php echo $det['stockTransferAutoID']?>"
                                       data-itemAutoID="<?php echo $det['itemAutoID'] ?>"
                                       data-stockTransferDetailAutoID="<?php echo $det[$items . '_transferDetailID'] ?>"
                                       data-warehouseAutoID="<?php echo ($det[$items])?>"
                                       data-curentQty="<?php echo $det[$items . '_qty']?>"
                                       value="<?php echo $det[$items . '_qty'] ?>"
                                       onkeyup="validateQty(this, this.value, <?php echo $det[$items . '_qty']?>, <?php echo $det['itemAutoID']?>)"
                                >
                            </td>
                            <?php
                            $totalQty += $det[$items . '_qty'];
                            $b++;
                        }
                    }
                    echo '<td><span id="updatedStock_'. $det['itemAutoID'] .'" disabled>' . $totalQty . '</span></td>';
                    echo '<td><span id="balanceStock_'. $det['itemAutoID'] .'" disabled>' . ($det['currentWareHouseStock'] - $totalQty) . '</span></td>';
                    echo '<td><a onclick="delete_bulk_transfer_details(' . $det['stockTransferAutoID'] . ',' . $det['itemAutoID'] . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td>';
                    echo '</tr>';

                    $a++;
                }

            } else { ?>
                <tr class="danger">
                    <td colspan="14" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    var leftAlign = 0;
    if(<?php echo count($extra['toWarehouse'])?> > 4){
        leftAlign = 5;
    }

    $('#warehoouseTranferTbl').tableHeadFixer({
        head: true,
        foot: true,
        left: leftAlign,
        right: 0,
        'z-index': 10
    });

    $(".warehouseTransferQty").change(function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }
        var stockTransferAutoID = $(this).attr('data-stockTransferAutoID');
        var itemAutoID = $(this).attr('data-itemAutoID');
        var stockTransferDetailAutoID = $(this).attr('data-stockTransferDetailAutoID');
        var warehouseAutoID = $(this).attr('data-warehouseAutoID');
        var transferQty = $(this).val();
        $(this).val(parseFloat(transferQty));
        update_warehouse_item_transfer(stockTransferAutoID, itemAutoID, stockTransferDetailAutoID, transferQty, warehouseAutoID, this);
    });

    function update_warehouse_item_transfer(stockTransferAutoID, itemAutoID, stockTransferDetailAutoID, transferQty, warehouseAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                stockTransferAutoID: stockTransferAutoID,
                itemAutoID: itemAutoID,
                stockTransferDetailAutoID: stockTransferDetailAutoID,
                transferQty: transferQty,
                warehouseAutoID: warehouseAutoID
            },
            url: "<?php echo site_url('Inventory/update_bulk_transfer_qty'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                var currentStock = $('#currentStock_' + itemAutoID).text();
                $('#updatedStock_' + itemAutoID).html(data);
                $('#balanceStock_' + itemAutoID).html(currentStock - data);
                $(element).attr('data-curentQty',transferQty);
            },
            error: function () {

            }
        });
    }

    function validateQty(element, Qty, UpdatedQty, itemAutoID) {
        var curentQty = $(element).attr('data-curentQty');
        var balanceStock = $('#balanceStock_' + itemAutoID).text();
        var totalbalance = parseFloat(balanceStock) + parseFloat(curentQty);
        if(totalbalance < Qty) {
            myAlert('w', 'You cannot update Qty larger than balance Qty ( Balance Qty : ' + totalbalance + ')');
            $(element).val(0);
        }
    }
</script>