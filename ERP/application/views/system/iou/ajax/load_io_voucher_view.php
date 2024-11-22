<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
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

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }

    .deleted {
        text-decoration: line-through;

    }

    .deleted div {
        text-decoration: line-through;

    }
</style>
<?php
if (!empty($master)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label"><?php echo $this->lang->line('iou_latest_vouchers'); ?></div>
                    <div class="taskcount"><?php echo sizeof($master) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_code'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_name'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('iou_voucherdate'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_narration'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_amount'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;"><?php echo $this->lang->line('iou_status'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('iou_iou_status'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;"><?php echo $this->lang->line('common_action'); ?></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>

            </tr>
            <?php
            $x = 1;
            foreach ($master as $val) {
                ?>
                <?php if ($val['isDeleted'] == 1) {
                    $delete = 'deleted deleted div';
                } else {
                    $delete = '';
                } ?>
                <tr>
                    <td class="mailbox-name <?php echo $delete ?>"><a href="#"
                                                                      class="numberColoring"> <?php echo $x; ?></a></td>
                    <td class="mailbox-name <?php echo $delete ?>"><a href="#"><?php echo $val['iouCode']; ?></a></td>
                    <td class="mailbox-name <?php echo $delete ?>"><a href="#"><?php echo $val['empNameiou']; ?></a>
                    <td class="mailbox-name <?php echo $delete ?>"><a href="#"><?php echo $val['voucherdate']; ?></a>
                    </td>
                    <td class="mailbox-name <?php echo $delete ?>"><a
                                href="#"><?php echo ucwords(trim_value($val['narration'], 8)); ?></a></td>

                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle"><?php echo $this->lang->line('iou_voucheramount'); ?> : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['CurrencyCode']?> <?php echo number_format($val['transactionamount'] ?? 0,$val['transactionCurrencyDecimalPlaces'] ?? 0) ; ?></a><br><strong class="contacttitle"><?php echo $this->lang->line('iou_expense'); ?> : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['CurrencyCode']?> <?php echo number_format($val['bookingamount'] ?? 0,$val['transactionCurrencyDecimalPlaces'] ?? 0); ?></a><br><strong
                                    class="contacttitle"><?php echo $this->lang->line('common_balance'); ?> : <a class="link-person noselect" href="#"><?php echo $val['CurrencyCode']?>  <?php echo number_format($val['expamt'],$val['transactionCurrencyDecimalPlaces']); ?></strong></a>
                            </div>
                        </div>
                    </td>
               <!--     <td class="mailbox-name <?php /*echo $delete */?>"><a
                                href="#"><?php /*echo $val['CurrencyCode'] . ' ' . number_format($val['transactionamount'], $val['transactionCurrencyDecimalPlaces']) */?></a>
                    </td>-->
                    <td class="mailbox-name <?php echo $delete ?>">
                        <?php if ($val['confirmedYN'] != 1 && $val['confirmedYN'] != 2) {
                            ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_draft'); ?></span>
                            <?php
                        } else if ($val['confirmedYN'] == 1 && $val['approvedYN'] != 1) {
                            ?>

                            <a style="cursor: pointer"
                               onclick="fetch_all_approval_users_modal('IOU','<?php echo $val['voucherAutoID'] ?>')"><span
                                        class="label"
                                        style="background-color:#ff661d; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_confirmed'); ?> <i
                                            class="fa fa-external-link" aria-hidden="true"></i></span></a>
                            <?php
                        } elseif ($val['confirmedYN'] == 2 && $val['approvedYN'] != 1) {
                            ?>

                            <a style="cursor: pointer"
                               onclick="fetch_approval_reject_user_modal('IOU','<?php echo $val['voucherAutoID'] ?>')"> <span
                                        class="label"
                                        style="background-color:#ff784f; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('iou_referredback'); ?> <i
                                            class="fa fa-external-link" aria-hidden="true"></i></span></a>
                            <?php
                        } else if ($val['confirmedYN'] == 1 && $val['approvedYN'] == 1){
                            ?>
                            <a style="cursor: pointer"
                               onclick="fetch_approval_user_modal('IOU','<?php echo $val['voucherAutoID'] ?>')"><span
                                        class="label"
                                        style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_approved'); ?> <i
                                            class="fa fa-external-link" aria-hidden="true"></i></span></a>
                            <?php

                        } ?>
                    </td>
                    <td class="mailbox-name <?php echo $delete ?>">
                        <?php if($val['closedYN'] != 1){?>
                            <?php if($val['bookingamount']==0){?>
                                <span class="label" style="background-color:rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('iou_open'); ?></span>
                                <?php }?>
                            <?php if($val['bookingamount'] < $val['transactionamount'] && $val['bookingamount']!=0){?>
                                <span class="label" style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('iou_partiallymatched'); ?></span>
                            <?php }?>

                            <?php if($val['bookingamount'] >= $val['transactionamount'] && $val['bookingamount']!=0){?>
                                <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('iou_fullymatched'); ?></span>
                            <?php }?>
                        <?php } else {?>
                            <a style="cursor: pointer"
                               onclick="closedvoucherdetails(<?php echo $val['voucherAutoID'] ?>)"> <span
                                        class="label"
                                        style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_closed'); ?> <i
                                            class="fa fa-external-link" aria-hidden="true"></i></span></a>
                             <?php }?>
                        </td>

                        <td class="mailbox-attachment" style="text-align: right">
                        <div class="btn-group" style="display: flex;justify-content: center"> 
                            <button type="button" class="btn btn-secondary dropdown-toggle" id="actionDropdown<?php echo $val['voucherAutoID']; ?>" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                                Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left" aria-labelledby="actionDropdown<?php echo $val['voucherAutoID']; ?>">

                                <li>
                                    <a class="dropdown-item" href="#" onclick="attachment_modal(<?php echo $val['voucherAutoID']; ?>, '<?php echo $this->lang->line('iou_voucher'); ?>', 'IOU', <?php echo $val['confirmedYN']; ?>)">
                                        <span class="glyphicon glyphicon-paperclip" style="color: #4caf50;" title="<?php echo $this->lang->line('common_attachment'); ?>" rel="tooltip"></span> Attachment
                                    </a>
                                </li>

                                <?php if ($val['approvedYN'] == 1) { ?>
                                    <li>
                                        <?php if ($val['paymentVoucherAutoID']) { ?>
                                            <a class="dropdown-item" href="#" onclick="documentPageView_modal('PV',<?php echo $val['paymentVoucherAutoID']; ?>)">
                                                <span class="glyphicon glyphicon-list-alt" style="color: #03a9f4;" title="<?php echo $this->lang->line('iou_view_payment_voucher'); ?>" rel="tooltip"></span> View Payment Voucher
                                            </a>
                                        <?php } else { ?>
                                            <a class="dropdown-item" href="#" onclick="generatePvvoucher(<?php echo $val['voucherAutoID']; ?>, <?php echo $val['transactionamount']; ?>, '<?php echo $this->lang->line('iou_payment_voucher'); ?>', '2')">
                                                <span class="glyphicon glyphicon-file" style="color: #116f5e;" title="<?php echo $this->lang->line('iou_generate_payment_voucher'); ?>" rel="tooltip"></span> Generate Payment Voucher
                                            </a>
                                        <?php } ?>
                                        <a class="dropdown-item" href="#" onclick="fetchPage('system/iou/iou_voucher_view','<?php echo $val['voucherAutoID']; ?>','<?php echo $this->lang->line('iou_view_iou_voucher_details'); ?>')">
                                            <span class="glyphicon glyphicon-pencil" style="color: #116f5e;" title="<?php echo $this->lang->line('iou_view_iou_voucher_details'); ?>" rel="tooltip"></span> View IOU Voucher Details
                                        </a>
                                    </li>
                                <?php } ?>

                            <?php if ($val['closedYN'] != 1 && $val['approvedYN'] == 1) { ?>
                                <?php if ($val['expamt'] > 0) { ?>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="generatevoucher(<?php echo $val['voucherAutoID']; ?>, <?php echo $val['expamt']; ?>, '<?php echo $this->lang->line('iou_receipt_voucher'); ?>', '1')">
                                                <span class="glyphicon glyphicon-remove" style="color: #f46a6a;" title="<?php echo $this->lang->line('iou_generate_receipt_voucher'); ?>" rel="tooltip"></span> Generate Receipt Voucher
                                            </a>
                                        </li>
                                    <?php } elseif ($val['expamt'] < 0) { ?>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="generatevoucher(<?php echo $val['voucherAutoID']; ?>, <?php echo $val['expamt']; ?>, '<?php echo $this->lang->line('iou_payment_voucher'); ?>', '2')">
                                                <span class="glyphicon glyphicon-remove" style="color: #f46a6a;" title="<?php echo $this->lang->line('iou_generate_payment_voucher'); ?>" rel="tooltip"></span> Generate Payment Voucher
                                            </a>
                                        </li>
                                    <?php } elseif ($val['expamt'] == 0) { ?>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="closeiouvoucher(<?php echo $val['voucherAutoID']; ?>)">
                                                <span class="glyphicon glyphicon-remove" style="color: #f46a6a;" title="<?php echo $this->lang->line('iou_close_voucher'); ?>" rel="tooltip"></span> Close Voucher
                                            </a>
                                        </li>
                                    <?php } ?>
                            <?php } ?>

                                <?php if ($val['confirmedYN'] != 1 && $val['isDeleted'] != 1) { ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="fetchPage('system/iou/create_iou_voucher','<?php echo $val['voucherAutoID']; ?>','<?php echo $this->lang->line('iou_edit_iou_voucher'); ?>')">
                                            <span class="glyphicon glyphicon-pencil" style="color: #ff2d7c;" title="<?php echo $this->lang->line('common_edit'); ?>" rel="tooltip"></span> Edit
                                        </a>
                                    </li>
                            <?php } ?>

                                <li>
                                    <a class="dropdown-item" target="_blank" href="#" onclick="documentPageView_modal('IOU','<?php echo $val['voucherAutoID']; ?>')">
                                        <span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;" title="<?php echo $this->lang->line('common_view'); ?>" rel="tooltip"></span> View
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" target="_blank" href="<?php echo site_url('Iou/load_iou_voucher_confirmation/') . '/' . $val['voucherAutoID']; ?>">
                                        <span class="glyphicon glyphicon-print" style="color: #607d8b;" title="<?php echo $this->lang->line('common_print'); ?>" rel="tooltip"></span> Print
                                    </a>
                                </li>

                                <?php if ($val['confirmedYN'] != 1 && $val['isDeleted'] != 1) { ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="delete_iou_voucher(<?php echo $val['voucherAutoID']; ?>);">
                                            <span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);" title="<?php echo $this->lang->line('common_delete'); ?>" rel="tooltip"></span> Delete
                                        </a>
                                    </li>
                        <?php } ?>

                                <?php if ($val['isDeleted'] == 1) { ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="reopen_iou_voucher(<?php echo $val['voucherAutoID']; ?>);">
                                            <span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);" title="<?php echo $this->lang->line('iou_re_open'); ?>" rel="tooltip"></span> Reopen
                                        </a>
                                    </li>
                        <?php } ?>

                                <?php if ($val['approvedYN'] == 0 && $val['confirmedYN'] == 1 && $val['isDeleted'] == 0) { ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="referback_iouvoucher(<?php echo $val['voucherAutoID']; ?>);">
                                            <span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);" title="<?php echo $this->lang->line('common_refer_back'); ?>" rel="tooltip"></span> Refer Back
                                        </a>
                                    </li>
                        <?php } ?>

                            </ul>
                        </div>
                    </td>
                </tr>
                <?php
                $x++;
            } ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('iou_there_are_no_iou_vouchers_to_display'); ?></div>
    <?php
}

?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>