<?php
$x = 1;
if (!empty($mfqJobDetail)) {
    ?>
    <table class="table table-condensed no-header action-btn-col" id="drilldown_d_<?php echo $workProcessID ?>" style="background-color: rgb(138 98 124 / 20%);" width="100%">
        <tbody>
        <?php foreach ($mfqJobDetail as $val) { ?>
            <tr>
                <td style="text-align: center;width: 4%;vertical-align:middle;"><i class="fa fa-plus-square accordion-toggle coll text-success"
                                          data-toggle="collapse" data-target="#<?php echo $val['workProcessID'] ?>"
                                          data-id="<?php echo $val['workProcessID'] ?>" data-set="0"></i>
                </td>
                <td style="width: 12%"><?php echo $val['documentCode']; ?></td>
                <td style="width: 12%"><?php echo '<span title="Document Date"> <b> Doc D : </b>' .$val['documentDate'] . '<br></span><span title="Expected Document Date"> <b> EDD : </b>' . $val['deliveryDate'] . '</span> <br><span title="Actual Document Date"> <b> ADD : </b>' . $val['actualDeliveryDate'] . '</span><br><span title="Document Closed Date"> <b> DCD : </b>' . $val['closedDate'] . '</span>'; ?></td>
                <td style="width: 18%"><?php echo $val['CustomerName']; ?></td>
                <td style="width: 20%"><?php echo $val['itemDescription'] . '<br> Quantity : ' . $val['qty']; ?></td>
                <td style="width: 10%"><?php echo $val['description']; ?></td>
                <td style="width: 4%"><?php echo approval_status($val['approvedYN']); ?></td>
                <td style="width: 4%"><?php echo get_job_status($val['confirmedYN'], $val['deliveryNoteID'], $val['invoiceAutoID'], $val['expectedDeliveryDate']); ?></td>
                <td style="width: 12%"><?php echo "<span class='text-center' style='vertical-align: middle'>" . job_status($val['percentage']) . "</span>"; ?></td>
                <td style="width: 5%"><?php echo editJob($val['workProcessID'], $val['confirmedYN'], $val['approvedYN'], $val['isFromEstimate'], $val['estimateMasterID'], $val['linkedJobID'], '',$val['documentCode'],$val['varianceYN']); ?></td>
            </tr>
            <tr>
                <td colspan="12" class="hiddenRow">
                    <div class="accordian-body collapse" id="<?php echo $val['workProcessID'] ?>">
                        <!--<div><i class="icon-refresh icon-spin"></i> Refresh</div>-->
                    </div>
                </td>
            </tr>
            <?php
            $x++;
        } ?>
        </tbody>
    </table>
    <script>
        $("[rel=tooltip]").tooltip();
        $('.coll').unbind('click');
        $('.coll').click(function () {
            var workProcessID = $(this).attr('data-id');
            if($(this).hasClass('fa fa-plus-square'))
            {
                if($(this).attr('data-set') == 0) {
                    job_drillDown2_table(workProcessID);
                    $(this).attr("data-set", "1");
                }
                $(this).removeClass("fa fa-plus-square").addClass("fa fa-minus-square");
                $(this).removeClass("text-success").addClass("text-danger");
            }
            else
            {
                $(this).removeClass("fa fa-minus-square").addClass("fa fa-plus-square");
                $(this).removeClass("text-danger").addClass("text-success");
            }
        });

    </script>
<?php } ?>

