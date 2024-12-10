<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);
?>
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
if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages" id="advancerecid">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('iou_expense_code');?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('iou_expense_description'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('common_status'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('common_amount'); ?></td>

            </tr>
            <?php
            $x = 1;
            $total = 0;
            foreach ($detail as $val) {
                ?>
                <tr>
                    <td class="mailbox-star"> <?php echo $x; ?></td>
                    <td class="mailbox-star" ><a href="#" onclick="viewiouvoucherexpencedetails(<?php echo $val['bookingMasterID'] ?>)"><?php echo $val['bookingCode'] ?></a>

                    </td>
                    <td class="mailbox-star" ><?php echo $val['comments'] ?></td>
                    <td class="mailbox-star">
                        <?php if ($val['submittedYN'] == 1 && $val['confirmedYN'] != 1 && $val['confirmedYN'] != 2) {
                            ?>
                            <span class="label" style="background-color:#ff9a43; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_submited'); ?></span>
                            <?php
                        } else if ($val['submittedYN'] != 1){
                            ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_draft'); ?></span>
                            <?php
                        }else if($val['submittedYN'] == 1 && $val['confirmedYN'] == 1 && $val['approvedYN'] != 1){ ?>
                            <span class="label" style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_confirmed'); ?></span>
                        <?php } else if($val['submittedYN'] == 1 && $val['confirmedYN'] == 2){?>
                            <a style="cursor: pointer"
                               onclick="fetch_approval_reject_user_modal('IOUE','<?php echo $val['bookingMasterID'] ?>')"> <span
                                        class="label"
                                        style="background-color:#ff784f; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('iou_referredback'); ?><i
                                            class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php } else if ($val['approvedYN'] == 1 && $val['confirmedYN'] == 1 && $val['submittedYN'] == 1){?>
                            <a style="cursor: pointer"
                               onclick="fetch_approval_user_modal('IOUE','<?php echo $val['bookingMasterID'] ?>')"><span
                                        class="label"
                                        style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_approved'); ?> <i
                                            class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php }?>

                    </td>
                    <td class="mailbox-star" ><?php echo number_format($val['transactionAmount'], $val['transactionCurrencyDecimalPlaces']) ?></td>

                </tr>
                <?php
                $x++;
                $total += $val['transactionAmount'];
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right" colspan="4">
                    <?php echo $this->lang->line('common_total'); ?>
                </td>
                <td class="text">
                    <?php echo number_format($total, $val['transactionCurrencyDecimalPlaces']) ?>
                </td >
            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" id="advancerecid"><?php echo $this->lang->line('common_no_records_found'); ?>.</div>
    <?php
}
?>
