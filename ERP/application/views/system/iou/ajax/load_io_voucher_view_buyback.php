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
                                    class="link-person noselect" href="#"><?php echo $val['CurrencyCode']?> <?php echo number_format($val['transactionamount'],$val['transactionCurrencyDecimalPlaces']) ; ?></a><br><strong class="contacttitle"><?php echo $this->lang->line('iou_expense'); ?> : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['CurrencyCode']?> <?php echo number_format($val['bookingamount'],$val['transactionCurrencyDecimalPlaces']); ?></a><br><strong
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

                    <td class="mailbox-attachment" style="text-align: right"><span>
                                <a href="#" onclick="attachment_modal(<?php echo $val['voucherAutoID'] ?>, '<?php echo $this->lang->line('iou_voucher'); ?>', 'IOU', <?php echo $val['confirmedYN']; ?>)"><span title="<?php $this->lang->line('common_attachment')?>" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a> |


                             <?php if ($val['approvedYN'] == 1 ) {
                                 ?>
                                 <a href="#"
                                    onclick="documentPageView_modal('PV',<?php echo $val['paymentVoucherAutoID'] ?>)"><span
                                             title="<?php echo $this->lang->line('iou_view_payment_voucher'); ?>" rel="tooltip"
                                             class="glyphicon glyphicon-list-alt"></span></a> |

                                 <a href="#"
                                    onclick="fetchPage('system/iou/iou_voucher_view','<?php echo $val['voucherAutoID'] ?>','<?php echo $this->lang->line('iou_view_iou_voucher_details'); ?>')"><span
                                             title="<?php echo $this->lang->line('iou_view_iou_voucher_details'); ?>" rel="tooltip"
                                             class="glyphicon glyphicon-pencil"></span></a> |
                             <?php } ?>


                            <?php if ($val['closedYN'] != 1 && $val['approvedYN'] == 1) { ?>
                                <?php if ($val['expamt'] > 0) { ?>
                                    <a href="#"
                                       onclick="generatevoucher(<?php echo $val['voucherAutoID'] ?>,<?php echo $val['expamt'] ?>,'<?php echo $this->lang->line('iou_receipt_voucher'); ?>','1')"><span
                                                title="<?php echo $this->lang->line('iou_generate_receipt_voucher'); ?>" rel="tooltip"
                                                class="glyphicon glyphicon-remove"></span></a> |
                                <?php } else if ($val['expamt'] < 0) { ?>
                                    <a href="#"
                                       onclick="generatevoucher(<?php echo $val['voucherAutoID'] ?>,<?php echo $val['expamt'] ?>,'<?php echo $this->lang->line('iou_payment_voucher'); ?>','2')"><span
                                                title="<?php echo $this->lang->line('iou_generate_payment_voucher'); ?>" rel="tooltip"
                                                class="glyphicon glyphicon-remove"></span></a> |
                                <?php } else if ($val['expamt'] == 0) { ?>
                                    <a href="#" onclick="closeiouvoucher(<?php echo $val['voucherAutoID'] ?>)"><span
                                                title="<?php echo $this->lang->line('iou_close_voucher'); ?>" rel="tooltip"
                                                class="glyphicon glyphicon-remove"></span></a> |
                                <?php }else { ?>
                                    <a href="#"
                                       onclick="generatevoucher(<?php echo $val['voucherAutoID'] ?>,<?php echo $val['expamt'] ?>,'<?php echo $this->lang->line('iou_receipt_voucher'); ?>','1')"><span
                                            title="<?php echo $this->lang->line('iou_generate_receipt_voucher'); ?>" rel="tooltip"
                                            class="glyphicon glyphicon-remove"></span></a> |

                                <?php  } ?>
                            <?php } ?>


                            <?php if ($val['confirmedYN'] != 1 && $val['isDeleted'] != 1) {
                                ?>
                                <a href="#"
                                   onclick="fetchPage('system/iou/create_iou_voucher','<?php echo $val['voucherAutoID'] ?>','<?php echo $this->lang->line('iou_edit_iou_voucher'); ?>')"><span
                                            title="<?php echo $this->lang->line('common_edit'); ?>" rel="tooltip"
                                            class="glyphicon glyphicon-pencil"></span></a> |
                            <?php } ?>
                            <a target="_blank"
                               onclick="documentPageView_modal('IOU','<?php echo $val['voucherAutoID'] ?>')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="<?php echo $this->lang->line('common_view'); ?>"></span></a> |
                            <a onclick="load_printtemp(<?php echo $val['voucherAutoID'] ?>);"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;
                            <!-- <a target="_blank"
                             href="<?php // echo site_url('Iou/load_iou_voucher_confirmation/') . '/' . $val['voucherAutoID'] ?>"><span
                                      title="<?php //echo $this->lang->line('common_print'); ?>" rel="tooltip" class="glyphicon glyphicon-print"></span></a></span>&nbsp; -->
                        <?php if ($val['confirmedYN'] != 1 && $val['isDeleted'] != 1) {
                            ?> | <a onclick="delete_iou_voucher(<?php echo $val['voucherAutoID'] ?>);"><span
                                        title="<?php echo $this->lang->line('common_delete'); ?>" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a>
                        <?php } ?>
                        <?php if ($val['isDeleted'] == 1) {
                            ?>
                            |&nbsp;&nbsp;  <a onclick="reopen_iou_voucher(<?php echo $val['voucherAutoID'] ?>);"><span
                                        title="<?php echo $this->lang->line('iou_re_open'); ?>" rel="tooltip" class="glyphicon glyphicon-repeat"
                                        style="color:rgb(209, 91, 71);"></span></a>
                        <?php } ?>
                        <?php
                        if ($val['approvedYN'] == 0 and $val['confirmedYN'] == 1 && $val['isDeleted'] == 0) { ?>
                            |&nbsp;&nbsp;<a onclick="referback_iouvoucher(<?php echo $val['voucherAutoID'] ?>);"><span
                                        title="<?php echo $this->lang->line('common_refer_back'); ?>" rel="tooltip" class="glyphicon glyphicon-repeat"
                                        style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;
                        <?php } ?>
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
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">IOU Voucher Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="voucherAutoID">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><?php echo $this->lang->line('common_type');?></label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page'), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_iouvoucher_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
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
    function load_printtemp(voucherAutoID){
        $('#printSize').val(1);
        $('#voucherAutoID').val(voucherAutoID);
        $('#print_temp_modal').modal('show');
    }

    function print_iouvoucher_temp(){
        var printSize =  $('#printSize').val();
        var iouvoucherID = $('#voucherAutoID').val();

        if(iouvoucherID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Iou/load_iou_voucher_confirmation_buyback') ?>" +'/'+ iouvoucherID +'/'+ printSize +'/'+1);
        }
    }     
</script>