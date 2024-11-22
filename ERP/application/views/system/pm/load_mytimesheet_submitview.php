<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
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

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>

<?php

$totalcount = sizeof($daterange) + 12;

?>
<?php
if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">

                <td class="task-cat-upcoming" colspan="<?php echo $totalcount ?>">
                    <div class="task-cat-upcoming-label">My Timesheets</div>
                    <div class="taskcount"><?php echo sizeof($detail) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Submit</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Project Code</td>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Description</td>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Customer</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Project Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Staus</td>
                <?php
                foreach ($daterange as $date) {
                    echo '<td class="headrowtitle" style="border-top: 1px solid #ffffff;">' . $date . '</td>';
                }

                ?>

            </tr>


            <?php
            $x = 1;
            $disable = '';
            $cheackedYN = '';
            foreach ($detail as $val) {
                if($val['confirmedYN']==1)
                {
                    $disable ='disabled';
                    $cheackedYN = 'checked';
                }else
                {
                    $disable ='';
                    $cheackedYN = '';
                }
                ?>
                <tr>


                    <td class="mailbox-name">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><input id="issubmit_<?php echo $val['projectPlannningID']?>" type="checkbox"
                                                                          data-caption="" class="columnSelected issubmit"
                                                                          name="issubmit[]" value="<?php echo $val['timesheetDetailID']?>" <?php echo $disable.' '.$cheackedYN?>><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </td>
                    <td class="mailbox-name"><?php echo $x ?></td>
                    <td class="mailbox-name"><?php echo $val['projectCode'] ?> </td>
                    <td class="mailbox-name"><?php echo $val['projectdescription'] ?> </td>
                    <td class="mailbox-name"><?php echo $val['customerName'] ?> </td>
                    <td class="mailbox-name"><?php echo $val['projectName'] ?> </td>
                    <td class="mailbox-name">
                        <?php if(($val['confirmedYN'] == 0)&&($val['approvedYN']==0)){?>
                        <span class="label" style="background-color:#dd4b39 !important; color: #ffffff; font-size: 11px;">Not Submitted</span>
                        <?php } else if(($val['confirmedYN'] == 1)&&($val['approvedYN']==0)){?>
                            <span class="label" style="background-color:#00a65a !important; color: #ffffff; font-size: 11px;">Submitted</span>
                        <?php }else if(($val['confirmedYN'] == 1)&&($val['approvedYN']==1)){?>
                            <span class="label" style="background-color:#00a65a !important; color: #ffffff; font-size: 11px;">Approved</span>
                        <?php }?>

                    </td>
                    <?php
                    foreach ($dateproject as $date1) {
                        if (($date1 >= $val['startDate']) && ($date1 <= $val['endDate'])) {
                            echo '<td class="mailbox-name">' . $val['description'] . '</td>';
                        } else {
                            echo '<td class="mailbox-name">&nbsp;</td>';
                        }
                    }


                    ?>
                </tr>
                <?php
                $x++;
            }


            ?>


            </tbody>
        </table>
    </div>
<?php } else { ?>
    <br>
    <div class="search-no-results col-sm-12">THERE ARE NO RECORDS TO DISPLAY.</div>
<?php } ?>

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