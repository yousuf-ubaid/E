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
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages" id="advancerecid">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('iou_voucher_code'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('iou_expense_category'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('common_segment'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('common_description'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align:right;"><?php echo $this->lang->line('common_amount'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('common_action'); ?></td>
            </tr>
            <?php
            $x = 1;
            $total = 0;
            foreach ($detail as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="1%"> <?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['iouCode'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['categoryDescription'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['segmentCode'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['bookingdescription'] ?></td>
                    <td class="mailbox-star"
                        width="9%" style="text-align:right;"><?php echo number_format($val['bookingAmount'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                    <td class="mailbox-attachment taskaction_td" width="6%">
                        <span>
                            <?php if ($val['isRequiredFields'] == 1): ?><!--SMSD-->
                                <a onclick="Fields_modal(<?php echo $val['bookingDetailsID'];?>,<?php echo $val['bookingMasterID'];?>,<?php echo $val['expenseCategoryAutoID'];?>,<?php echo $master['confirmedYN'];?>,<?php echo $master['approvedYN'];?>)">
                                    <span title="Required Fields" rel="tooltip" class="glyphicon glyphicon-cog"></span>
                                </a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                            <?php endif; ?><!--SMSD-->
                            
                            <a onclick="attachment_modal(<?php echo $val['bookingDetailsID'];?>,'<?php echo $this->lang->line('iou_iou_voucher_expenses'); ?>','IOUE',<?php echo $master['confirmedYN'];?>)">
                                <span title="<?php echo $this->lang->line('common_attachment'); ?>" rel="tooltip" class="glyphicon glyphicon-paperclip"></span>
                            </a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a onclick="delete_iou_bookingdetail(<?php echo $val['bookingDetailsID'] ?>)">
                                <span title="<?php echo $this->lang->line('common_delete'); ?>" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>
                            </a>
                        </span>
                    </td>
                </tr>
                <?php
                $x++;
                $total += $val['bookingAmount'];
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right " colspan="5">
                    <b><?php echo $this->lang->line('common_total'); ?></b>
                </td>
                <td class="text-right">
                    <b><?php echo number_format($total, $master['transactionCurrencyDecimalPlaces']) ?></b>
                </td>
                <td class="text-right">&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } else { ?>
    <br>
    <div class="search-no-results" id="advancerecid"><?php echo $this->lang->line('common_no_records_found'); ?>.</div>
<?php } ?>
