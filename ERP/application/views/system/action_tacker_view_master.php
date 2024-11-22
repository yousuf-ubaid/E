<?php if ($Output) { ?>

    <div class="table-responsive">
        <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th class='theadtr'>#</th>
                <th class='theadtr' style="width: 28%;">Task</th>
                <th class='theadtr' style="width: 10%;">Month</th>
                <th class='theadtr'>Reported By</th>
                <th class='theadtr'>Assigned Emp</th>
                <th class='theadtr' style="width: 7%;">Status</th>
                <th class='theadtr'>Target Date</th>
                <th class='theadtr'>Completed Date</th>
                <th class='theadtr'>Company</th>
                <th class='theadtr'>Segment</th>
                <th class='theadtr'>Action</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $x = 1;
            foreach ($Output as $val) {
                $month = date('F - Y', strtotime($val['dateFrom']));
                ?>
                <tr>
                    <td class="text-left"><?php echo $x ?></td>
                    <td class="text-left"><?php echo $val['description'] ?></td>
                    <td class="text-left"><?php echo  $month ?></td>
                    <td class="text-left"><?php echo $val['Reportedby'] ?></td>
                    <td class="text-left"><?php echo $val['AssignedEmp'] ?></td>
                    <td class="text-center">
                        <?php if ($val['status'] == 3 && $val['approvedYN'] == 1) { ?>
                            <span class="label" style="background-color:#89de27; color: #FFFFFF; font-size: 11px;">Closed</span>
                        <?php } else if ($val['status'] == 0 && $val['approvedYN'] == 0) { ?>
                            <span class="label"
                                  style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;">Open</span>
                        <?php } else if ($val['status'] == 1 && $val['approvedYN'] == 0) { ?>
                            <span class="label" style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;">In Progress</span>
                        <?php } else if ($val['status'] == 2 && $val['approvedYN'] == 0) { ?>
                            <span class="label" style="background-color:#00a65a; color: #FFFFFF; font-size: 11px;">Completed</span>
                        <?php } ?>


                    </td>
                    <td class="text-left"><?php echo $val['targetDate'] ?></td>
                    <td class="text-left"><?php echo $val['completedDate'] ?></td>
                    <td class="text-left"><?php echo $val['companycode'] ?></td>
                    <td class="text-left"><?php echo $val['segment'] ?></td>
                    <td class="text-left">

                        <a onclick="view_mpr_view(<?php echo $val['actionID'] ?>);"><span
                                    title="View" rel="tooltip"
                                    class="glyphicon glyphicon-eye-open"></span></a>

                        <?php if (($val['status'] == 0 && $val['approvedYN'] == 0)||($val['status'] == 1 && $val['approvedYN'] == 0)) { ?>
                            &nbsp;|&nbsp;<a onclick="edit_action_tracker(<?php echo $val['actionID'] ?>);"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span></a>
                        <?php } ?>
                        <?php if(($val['status']== 2 && $val['approvedYN']== 0)){?>
                            &nbsp;|&nbsp;<a onclick="update_close_status(<?php echo $val['actionID'] ?>);"><span
                                        title="Approve" rel="tooltip"
                                        class="glyphicon glyphicon-ok"></span></a>
                        <?php } ?>


                    </td>
                </tr>

                <?php
                $x++;

            } ?>

            </tbody>

            <tfoot>

            </tfoot>
        </table>
    </div>
<?php } else { ?>
    <br>
    <div class="row" style="margin: 0 auto;">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                No Records found
            </div>
        </div>
    </div>
<?php } ?>







