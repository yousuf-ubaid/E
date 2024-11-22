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


<div class="row" style="margin-left: 2%;">
    <label><strong>Dispatch For :</strong> <?php echo $dispatch['master']['farmName'] . ' | ' . $dispatch['master']['batchCode']; ?></label>
</div>
</br>
    <?php
if (!empty($dispatch['master']['noOfSubItem'])) {
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
            </tr>
            <?php
            $x = 1;
            if(!empty($dispatch['subItemDetails'])){
                foreach ($dispatch['subItemDetails'] as $val) {
            ?>
                    <tr>
                        <input class="hidden" name="serialNumber[]" id="serialNumber" value="<?php echo $val['serialNumber']; ?>">
                        <input class="hidden" name="itemAutoID[]" id="itemAutoID" value="<?php echo $dispatch['master']['itemAutoID']; ?>">
                        <input class="hidden" name="dispatchDetailsID[]" id="dispatchDetailsID" value="<?php echo $dispatch['master']['dispatchDetailsID']; ?>">
                        <td class="mailbox-star"><?php echo $x; ?></td>
                        <td class="mailbox-star"><?php echo $dispatch['master']['DispatchCode']; ?></td>
                        <td class="mailbox-star"><?php echo $dispatch['master']['item']; ?></td>
                        <td class="mailbox-star"><input style="border: none; background: none;" name="subItemSystemCode[]" value="<?php echo $val['subItemSystemCode']; ?>" readonly></td>
                        <td class="mailbox-star"><input
                                    <?php if(!empty($type)){
                                        echo 'style="border: none; background: none;" readonly';
                                    } ?>name="referenceCode[]" value="<?php echo $val['referenceCode']; ?>"></td>
                    </tr>

                    <?php
                    $x++;
                }
            } else{
                echo '<tr class="danger" style="margin-top: 1px"><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>';
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


<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/8/2019
 * Time: 12:11 PM
 */