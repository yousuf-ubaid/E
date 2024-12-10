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
    .actionicon{
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
    .headrowtitle{
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
    .contacttitle {
        width: 170px;
        text-align: right;
        color: #525252;
        padding: 4px 10px 0 0;
    }
    .numberColoring{
             font-size: 12px;
             font-weight: 500;
             color: saddlebrown;
         }
</style>
<?php
if (!empty($header)) {
    //print_r($header);
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Latest Expense Claims</div>
                    <div class="taskcount"><?php echo sizeof($header) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Detail</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Total Value</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Confirmed</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Approved</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#" onclick="#" ><?php echo $val['expenseClaimCode']; ?></a></td>
                    <td class="mailbox-name"><div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Claimed By Name : </strong><a class="link-person noselect" href="#" ><?php echo $val['claimedByEmpName'] ?></a><br><strong class="contacttitle">Claimed Date : </strong><a class="link-person noselect" href="#" ><?php echo $val['expenseClaimDate'] ?></a><br><strong class="contacttitle">Description : </strong><a class="link-person noselect" href="#" ><?php echo $val['comments'] ?></a></div></div>
                    </td>
                    <td class="mailbox-name"><a href="#"><?php
                            $detailValue = $this->db->query("SELECT SUM(empCurrencyAmount) as TotalExpense,empCurrencyDecimalPlaces,empCurrency FROM srp_erp_expenseclaimdetails WHERE expenseClaimMasterAutoID = {$val['expenseClaimMasterAutoID']}")->row_array();
                            if(!empty($detailValue)){
                                echo $detailValue['empCurrency']." : ".number_format($detailValue['TotalExpense'], 2);
                            }else {
                                echo number_format(0, 2);
                            }
                            ?>
                        </a></td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['confirmedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                        <?php } else {?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['approvedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Approved</span>
                        <?php } else {?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Approved</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-attachment"><span class="pull-right">

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
    <div class="search-no-results">THERE ARE NO EXPENSE CLAIMS TO DISPLAY.</div>
    <?php
}

?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>