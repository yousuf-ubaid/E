<script>var selected = 0;</script>
<style>
        .search-no-results {
            text-align: center;
            background-color: #f6f6f6;
            border: solid 1px #ddd;
            margin-top: 10px;
            padding: 1px;
        }

        .label {
            display: inline;
            padding: .2em .8em .3em;
        }

        .actionicon {
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #89e68d;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 2px 5px 2px 5px;
            line-height: 14px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }

        .headrowtitle {
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }
    </style>
<?php
$grnQty = $grn['master']['noOfSubItem'];
?>

<div class="row" style="margin-left: 2%;">
    <label><strong>GRN For :</strong> <?php echo $grn['master']['farmName'] . ' | ' . $grn['master']['batchCode']; ?></label>
</div>
</br>
    <?php
if (!empty($grn['master']['noOfSubItem'])) {
    $com_currency = $this->common_data['company_data']['company_default_currency'];
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>#</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>Dispatch Code</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>Item Code</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>Sub Item Code</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>Reference Code</strong></td>
                <?php if ($type != 'View'){
                echo '<td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>GRN Item</strong></td>';
                 }?>
            </tr>
            <?php
            $x = 1;
            if(!empty($grn['subItemDetails'])){
                foreach ($grn['subItemDetails'] as $val) {
            ?>
                    <tr>
                        <input class="hidden" name="serialNumber[]" id="serialNumber" value="<?php echo $val['serialNumber']; ?>">
                        <input class="hidden" name="itemAutoID[]" id="itemAutoID" value="<?php echo $grn['master']['itemAutoID']; ?>">
                        <input class="hidden" name="grnDetailsID[]" id="grnDetailsID" value="<?php echo $grn['master']['grnDetailsID']; ?>">
                        <td class="mailbox-star"><?php echo $x; ?></td>
                        <td class="mailbox-star"><?php echo $val['DispatchCode']; ?></td>
                        <td class="mailbox-star"><?php echo $grn['master']['item']; ?></td>
                        <td class="mailbox-star"><input style="border: none; background: none;" name="subItemSystemCode[]" value="<?php echo $val['subItemSystemCode']; ?>" readonly></td>
                        <td class="mailbox-star"><input style="border: none; background: none;" name="referenceCode[]" value="<?php echo $val['referenceCode']; ?>" readonly></td>
                       <?php if ($type != 'View'){ ?>
                           <td class="mailbox-star text-center">
                               <input type="checkbox" class="grn-iCheck" id="grnItem<?php echo $val['serialNumber']; ?>" name="grnItem[]" value="1" onclick="validateItemQty(this, <?php echo $grnQty?>, <?php echo $val['serialNumber']; ?>)"
                                   <?php if ($val['isReceived'] == 1){ echo 'checked'; }?>>
                               <input class="hidden" id="grnChecked<?php echo $val['serialNumber']; ?>" name="grnChecked[]"
                                   <?php if ($val['isReceived'] == 1){
                                       echo 'value="1"'; } else { echo 'value="0"'; }
                                   ?>>
                           </td>
                       <?php }?>
                    </tr>

                    <?php
                    $x++;
                    if ($val['isReceived'] == 1){
                        echo '<script>selected += 1;</script>';
                    }
                }
            } else{
                echo '<tr class="danger" style="margin-top: 1px"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" style="text-align: center;">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>

<script>
    function validateItemQty(val, grnQty, idNo) {
        if ($(val).is(':checked')) {
            if(selected == grnQty){
                myAlert('w',"Can not Select Items more than selected no of birds");
                $('#grnItem' + idNo).closest('tr').iCheck('uncheck');
            } else{
                selected += 1;
                $('#grnChecked' + idNo).val(1);
            }
        }
        else {
            selected -= 1;
            $('#grnChecked' + idNo).val(0);
        }
    }
</script>

<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/9/2019
 * Time: 11:30 AM
 */