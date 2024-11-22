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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    .contacttitle {
        width: 170px;
        text-align: right;
        color: #525252;
        padding: 4px 10px 0 0;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="8">
                    <div class="task-cat-upcoming-label">Vouchers</div>
                    <div class="taskcount"><?php echo sizeof($header) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Detail</td>
                <!--<td class="headrowtitle" style="border-top: 1px solid #F76F01;">Total Value</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;text-align: center">Confirmed</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;text-align: center">Approved</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                $status = '';
                if($val['isDeleted'] == 1){
                    $status = 'line-through';
                }
                ?>
                <tr style="text-decoration: <?php echo $status ?>;">
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['documentSystemCode']; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Type : </strong><a
                                    class="link-person noselect" href="#">
                                    <?php if($val['PVtype'] == 1) {
                                        echo "Payment Voucher";
                                    } else if ($val['PVtype'] == 2) {
                                        echo "Receipt Voucher";
                                    } else if($val['PVtype'] == 3) {
                                        echo "Settlement";
                                    } else if($val['PVtype'] == 4) {
                                        echo "Journal Entry";
                                    } ?></a><br><strong class="contacttitle">Farm Name : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['farmName'] ?></a><br><strong
                                    class="contacttitle">Document Date : </strong><a class="link-person noselect"
                                                                                     href="#"><?php echo $val['documentDate'] ?></a><br><strong
                                    class="contacttitle">Memo : </strong><a class="link-person noselect"
                                                                            href="#"><?php echo $val['PVNarration'] ?></a>
                            </div>
                        </div>
                    </td>
<!--                    <td class="mailbox-name">
                        <div class="arrow-steps clearfix">
                            <?php
/*                            if ($val['PVtype'] == 1) {
                                $expense = $this->db->query("SELECT sum(transactionAmount) as expenseTotal,transactionCurrency FROM srp_erp_buyback_paymentvoucherdetail WHERE type = 'Expense' AND pvMasterAutoID ={$val['pvMasterAutoID']}")->row_array();
                                if (!empty($expense)) {
                                    echo "<strong>Expense</strong> : " . $expense['transactionCurrency'] . " : " . number_format($expense['expenseTotal'], 2);
                                }
                                $advance = $this->db->query("SELECT sum(transactionAmount) as advanceTotal,transactionCurrency FROM srp_erp_buyback_paymentvoucherdetail WHERE type = 'Advance' AND pvMasterAutoID ={$val['pvMasterAutoID']}")->row_array();
                                if (!empty($advance)) {
                                    echo "<br><strong> Advance</strong> : " . $advance['transactionCurrency'] . " : " . number_format($advance['advanceTotal'], 2);
                                }
                                $loan = $this->db->query("SELECT sum(transactionAmount) as expenseTotal,transactionCurrency FROM srp_erp_buyback_paymentvoucherdetail WHERE type = 'Loan' AND pvMasterAutoID ={$val['pvMasterAutoID']}")->row_array();
                                if (!empty($loan)) {
                                    echo "<br><strong>Loan</strong> : " . $expense['transactionCurrency'] . " : " . number_format($expense['expenseTotal'], 2);
                                }
                            } else {
                                $income = $this->db->query("SELECT sum(transactionAmount) as incomeTotal,transactionCurrency FROM srp_erp_buyback_paymentvoucherdetail WHERE type = 'Deposit' AND pvMasterAutoID ={$val['pvMasterAutoID']}")->row_array();
                                if (!empty($income)) {
                                    echo "<br><strong> Deposit</strong> : " . $income['transactionCurrency'] . " : " . number_format($income['incomeTotal'], 2);
                                }
                            }
                            */?>
                        </div>
                    </td>-->

                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['confirmedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['approvedYN'] == 1) { ?>
                          <!--  <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Approved</span> -->
                            <a style="cursor: pointer"
                               onclick="fetch_approval_user_modal('<?php echo $val['documentID'] ?>','<?php echo $val['pvMasterAutoID'] ?>')"><span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Approved <i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Approved</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-name" width="10%"><span class="pull-right">
                            <?php if ($val['confirmedYN'] != 1) { ?>
                        <?php if ($val['isDeleted'] == 1) { ?>
                            <!--<a target="_blank"
                               href="<?php /*echo site_url('buyback/load_paymentVoucher_confirmation/') . '/' . $val['pvMasterAutoID'] */?>"><span
                                    title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                            <a onclick="load_printtemp(<?php echo $val['pvMasterAutoID'] ?>)"><span
                                        title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                            &nbsp;&nbsp;|&nbsp;&nbsp; <a
                                onclick="reOpen_paymentVoucher(<?php echo $val['pvMasterAutoID'] ?>);"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>
                            <?php
                        } else { ?>
                            <a href="#"
                               onclick="fetchPage('system/buyback/create_payment_voucher','<?php echo $val['pvMasterAutoID'] ?>','Edit Voucher','BUYBACK')"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                              &nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<!--<a target="_blank"
                                 href="<?php /*echo site_url('buyback/load_paymentVoucher_confirmation/') . '/' . $val['pvMasterAutoID'] */?>"><span
                                      title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                             <a onclick="load_printtemp(<?php echo $val['pvMasterAutoID'] ?>)"><span
                                        title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;<a
                                onclick="delete_paymentVoucher(<?php echo $val['pvMasterAutoID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                        <?php }
                        } else { ?>
                            <a onclick='attachment_modal(<?php echo $val['pvMasterAutoID'] ?>,"Vouchers","BBPV",<?php echo $val['confirmedYN'] ?>)'><span
                                    title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;

                            <?php if ($val['createdUserID'] == trim(current_userID()) && $val['confirmedYN'] == 1 && $val['approvedYN'] != 1) {
                                ?>
                                <a onclick="referback_paymentVoucher(<?php echo $val['pvMasterAutoID'] ?>);"><span
                                        title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"
                                        style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <?php
                            }
                            ?>
                            <a target="_blank"
                               onclick="documentPageView_modal('BBPV','<?php echo $val['pvMasterAutoID'] ?>')"><span
                                    title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                    data-original-title="View"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <!--<a target="_blank"
                               href="<?php /*echo site_url('buyback/load_paymentVoucher_confirmation/') . '/' . $val['pvMasterAutoID'] */?>"><span
                                    title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                            <a onclick="load_printtemp(<?php echo $val['pvMasterAutoID'] ?>)"><span
                                        title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                            <?php
                        }
                        ?>
                    </td>

                </tr>
                <?php
                $x++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO VOUCHERS TO DISPLAY, PLEASE CLICK <b>NEW VOUCHER</b> TO CREATE A NEW VOUCHER.</div>
    <?php
}
?>
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Vouchers Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pvMasterAutoID">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Print Option</label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page' ), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_voucher_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
    function load_printtemp(pvMasterAutoID)
    {
        $('#printSize').val(1);
        $('#pvMasterAutoID').val(pvMasterAutoID);
        $('#print_temp_modal').modal('show');
    }

    function print_voucher_temp(){
        var printtype =  $('#printSize').val();
        var pvMasterAutoID =   $('#pvMasterAutoID').val();

        if(pvMasterAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('buyback/load_paymentVoucher_confirmation') ?>" +'/'+ pvMasterAutoID +'/'+ printtype +'/'+1);
        }
    }
</script>