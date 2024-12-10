<?php
$current_date = current_date(false);
$dStart  = new DateTime($current_date);
?>
<div class="col-md-12">
<?php if($cage){
    foreach ($cage as $var){
        if($var['cageID']) {
            ?>
            <div class="col-md-3">

                <div class="box box-primary drill-down-cursor"
                     style="background-color: white; box-shadow: 0px 2px 2px 0px #7a7373">
                    <div class="box-body box-profile">
                        <div class="col-md-1 pull-right">
                            <a onclick="edit_Cage(<?php echo $var['cageID'] ?>)"><span class="glyphicon glyphicon-pencil"></span></a>
                        </div>
                        <h3 class="profile-username drill-down-cursor"
                            onclick="show_all_batch(<?php echo $var['cageID'] ?>)"
                            style="color: #5a0099"><?php echo $var['cageName'] ?></h3>
                        <h5 class="text-muted" onclick="show_all_batch(<?php echo $var['cageID'] ?>)"><?php echo $var['cageCode']; ?></h5>
                        <h5 class="text-muted" onclick="show_all_batch(<?php echo $var['cageID'] ?>)">Ongoing
                            Batch <?php
                            if (!empty($var['batchCode'])) {
                                echo ' : ' . $var['batchCode'];
                            } else {
                                echo 'N/A';
                            } ?></h5>

                        <hr>
                        <div>
                            <table id="fcr_tbl" class="" style="width: 100%; margin-top: 10%; border: none;">
                                <thead style="border: none">
                                <tr style=" background-color: white; text-align: center">
                                    <td>Input</td>
                                    <td>Output</td>
                                    <td>Balance</td>
                                </tr>
                                <tr style="font-size: 20px; text-align: center; background-color: white">
                                    <?php if ($var['chicksTotal']) {
                                        echo '<td><a> ' . $var['chicksTotal'] . ' </a></td>';
                                    } else {
                                        echo '<td><a>N/A</a></td>';
                                    }
                                    if ($var['grn']) {
                                        echo '<td><a> ' . $var['grn'] . ' </a></td>';
                                    } else {
                                        echo '<td><a>N/A</a></td>';
                                    }
                                    $balance = $var['chicksTotal'] - $var['grn'] - $var['mortal'];
                                    if ($balance != 0) {
                                        echo '<td><a> ' . $balance . ' </a></td>';
                                    } else {
                                        echo '<td><a>N/A</a></td>';
                                    } ?>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <hr>
                        <ul class="list-group" onclick="show_all_batch(<?php echo $var['cageID'] ?>)">
                            <li class="list-group-item" style="border: none">
                                <b>Status</b>
                                <?php if (!empty($var['batchCode'])) {
                                    echo '<a class="pull-right" style="color: #b3b300"><b>Ongoing</b></a>';
                                } else if (strtotime($var['RestEndDate']) >= strtotime($current_date)) {
                                    echo '<a class="pull-right" style="color: #df6101"><b>Rest</b> </a>';
                                } else {
                                    echo '<a class="pull-right" style="color: #00cc00"><b>Available</b> </a>';
                                } ?>
                            </li>
                            <li class="list-group-item" style="border: none">
                                <?php if (!empty($var['batchCode'])) {
                                    $newFormattedDate = chicks_age_dashboard($var['batchMasterID'],'','');
                                   /* $dEnd = new DateTime($var['batchStartDate']);
                                    $dDiff = $dStart->diff($dEnd);
                                    $newFormattedDate = $dDiff->days;*/ ?>
                                    <b>Age</b> <a class="pull-right"> <?php if($newFormattedDate){
                                        echo $newFormattedDate;
                                        } else {
                                        echo 0;
                                        } ?></a>
                                <?php } else { ?>
                                    <b>&nbsp;</b>
                                <?php } ?>
                            </li>
                        </ul>
                        <?php if (!empty($var['batchCode'])) {
                            echo '  <a class="btn btn-primary btn-block" onclick="ongoingBatch(\'Ongoing\')">Batch Ongoing</a>';
                        } else if (strtotime($var['RestEndDate']) > strtotime($current_date)) {
                            $dEnd = new DateTime($var['RestEndDate']);
                            $dDiff = $dStart->diff($dEnd);
                            $newFormattedDate = $dDiff->days;
                            echo '  <a class="btn btn-primary btn-block" onclick="ongoingBatch(\'Rest\')">Create in ' . $newFormattedDate . ' days</a>';
                        } else { ?>
                            <a class="btn btn-primary btn-block drill-down-cursor"
                               onclick="show_add_batch(<?php echo $var['cageID'] ?>)">Create Batch</a>
                        <?php } ?>

                    </div>

                </div>

            </div>
            <?php
        }
    }
} ?>

</div>

<script>
    function ongoingBatch(val) {
        if(val == 'Ongoing'){
            myAlert('w', 'You cannot create the batch "Batch is ongoing "');
        } else if(val == 'Rest'){
            myAlert('w', 'You cannot create the batch "Cage is in rest period "');
        }

    }
</script>

<?php
