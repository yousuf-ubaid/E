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
    .center {
        text-align: center;
    }
</style>
<?php

if (!empty($batch)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Farmer</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Batch Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Start Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Closing Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Input</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Output</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Mortality</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Balance</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Age</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Confirmed</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Approved</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            $totalChicks = 0;
            $mortalityChicksTotal = 0;
            foreach ($batch as $val) {

                $chicksTotal = $this->db->query("SELECT COALESCE(sum(qty), 0) AS chicksTotal FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 WHERE batchMasterID ={$val['batchMasterID']}")->row_array();

                $balancechicksTotal = $this->db->query("SELECT COALESCE(sum(grnd.noOfBirds), 0) AS balanceChicksTotal FROM srp_erp_buyback_grn grn INNER JOIN srp_erp_buyback_grndetails grnd ON grnd.grnAutoID = grn.grnAutoID WHERE batchMasterID ={$val['batchMasterID']}")->row_array();

                $mortalityChicks = $this->db->query("SELECT COALESCE(sum(noOfBirds), 0) AS deadChicksTotal FROM srp_erp_buyback_mortalitymaster mm INNER JOIN srp_erp_buyback_mortalitydetails md ON mm.mortalityAutoID = md.mortalityAutoID WHERE batchMasterID ={$val['batchMasterID']}")->row_array();
                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['farmerName'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['batchCode'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['batchStartDate'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['batchClosingDate'] ?></td>
                    <td class="mailbox-star tableHeader center" width="5%">
                        <?php
                        if (!empty($chicksTotal)) {
                            echo $chicksTotal['chicksTotal'];
                        }
                        ?>
                    </td>
                    <td class="mailbox-star tableHeader center" width="5%">
                        <?php if(!empty($val['receivedtotal']))
                        {
                            echo $val['receivedtotal'];
                        }else
                        {
                            echo 0;
                        }
                        ?>
                    </td>
                    <td class="mailbox-star tableHeader center" width="5%">
                        <?php
                        if (!empty($mortalityChicks)) {
                            echo $mortalityChicks['deadChicksTotal'];
                        }
                        ?>
                    </td>
                    <td class="mailbox-star tableHeader center" width="5%">
                        <?php
                        if (!empty($balancechicksTotal)) {
                            $totalChicks = ($chicksTotal['chicksTotal'] - ($balancechicksTotal['balanceChicksTotal'] + $mortalityChicks['deadChicksTotal']));
                            echo $totalChicks;
                        }
                        ?>
                    </td>
                    <td class="mailbox-star tableHeader center" width="5%">
                        <?php
                        $chicksAge = $this->db->query("SELECT dpm.dispatchedDate,batch.closedDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 LEFT JOIN srp_erp_buyback_batch batch ON batch.batchMasterID = dpm.batchMasterID WHERE dpm.batchMasterID ={$val['batchMasterID']} ORDER BY dpm.dispatchAutoID ASC")->row_array();
                        //echo $this->db->last_query();
                        if (!empty($chicksAge)) {
                            $dStart = new DateTime($chicksAge['dispatchedDate']);
                            if($chicksAge['closedDate'] != ' '){
                                $dEnd  = new DateTime($chicksAge['closedDate']);
                            }else{
                                $dEnd  = new DateTime(current_date());
                            }
                            $dDiff = $dStart->diff($dEnd);
                            $newFormattedDate = $dDiff->days + 1;
                            echo $newFormattedDate;
                        }
                        ?>
                    </td>
                    <td class="mailbox-name" style="text-align: center" width="5%">
                        <?php if ($val['confirmedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-name" style="text-align: center" width="5%">
                        <?php if ($val['approvedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #000000; color: #FFFFFF; font-size: 11px;">Locked</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Approved</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-attachment" width="5%">
                        <span class="pull-right">
                            <a href="#" onclick="generateBatchProductionReport_view(<?php echo $val['batchMasterID'] ?>)"><i class="fa fa-eye" aria-hidden="true" title="View" style="font-size: 14px"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php if($val['isclosed'] == 1){ ?>
                            <i class="fa fa-lock" aria-hidden="true" title="Locked" style="font-size: 14px"></i>
                        <?php } else { ?>
                            <a href="#" onclick="generateBatchProductionReport(<?php echo $val['batchMasterID'] ?>)"><i
                                    class="fa fa-unlock" aria-hidden="true" title="Lock" style="font-size: 14px"></i></a>
                            <?php } ?>

                        </span>
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
    <div class="alert" role="alert" style="background: #9ab9f1;">THERE ARE NO BATCHES TO DISPLAY, PLEASE <b>CREATE BATCH IN FARM MASTER</b>.</div>
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