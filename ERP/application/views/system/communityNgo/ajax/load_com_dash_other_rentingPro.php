<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
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
$date_format_policy = date_format_policy();
if (!empty($rentingPro)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming" style="">
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 4%">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 15%"><?php echo $this->lang->line('communityngo_issueNo'); ?><!--Issue NO--></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 40%"><?php echo $this->lang->line('common_details'); ?><!--Details--></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 15%"><?php echo $this->lang->line('common_total_value'); ?><!--Total Value--></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #50749f;min-width: 10%">
                    <?php echo $this->lang->line('communityngo_returned_status'); ?><!--Returned--></td>
            </tr>

            <?php
            $x = 1;
            $totRentAmount = 0;
            foreach ($rentingPro as $val) {

                $totRentAmount += $val['total_value'];

                if($val['itemIssueAutoID']){

                    ?>
                    <tr>
                        <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                        <td class="mailbox-star" width="18%"><?php echo $val['itemIssueCode']; ?></td>
                        <td class="mailbox-star" width="27%">
                            <div class="contact-box">
                                <div class="link-box">
                                    <strong class="contacttitle">Member Name : </strong><a class="link-person noselect" href="#"><?php echo $val['requestedMemberName']; ?></a>
                                    <br><strong class="contacttitle">Exp Return Date : </strong><a class="link-person noselect" href="#"><?php echo $val['expectedReturnDate']; ?></a>
                                    <br><strong class="contacttitle">Narration : <?php echo $val['narration'] ?> </strong>  <a class="link-person noselect" href="#"> </a>
                                </div>
                            </div>
                        </td>
                        <td class="mailbox-star" style="text-align: right;" width="20%"><?php echo format_number($val['total_value'], $this->common_data['company_data']['company_default_decimal']); ?></td>
                        <td class="mailbox-star" width="15%" style="text-align: center;">
                            <?php if($val['confirmedYN']==0){
                                ?>
                                <span class="label label-danger">Not Confirmed</span>
                                <?php
                            }else{
                                ?>
                                <span class="label label-success">Confirmed</span>
                                <?php
                            }
                            ?>
                       </td>
                        <td class="mailbox-star" width="15%" style="text-align: center;">
                            <?php if($val['isReturned']==0){
                                ?>
                                <span class="label label-danger">Not Returned</span>
                                <?php
                            }else{
                                ?>
                                <span class="label label-success">Returned</span>
                                <?php
                            }
                            ?>
                        </td>

                    </tr>
                    <?php
                    $x++;
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td>
                <td class="text-right" colspan="2">
                    Total
                </td>
                <td class="text-right">
                    <?php echo number_format($totRentAmount, 2) ?>
                </td>
                <td colspan="2"></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
</script>


<?php
