<?php if ($Output) { ?>

<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr'>#</th>
            <th class='theadtr' style="width: 35%;">Task</th>
            <th class='theadtr'>Reported By</th>
            <th class='theadtr'>Assigned Emp</th>
            <th class='theadtr'>Target Date</th>
            <th class='theadtr'>Completed Date</th>
            <th class='theadtr'>Company</th>
            <th class='theadtr'>Segment</th>
        </tr>
        </thead>
        <tbody>

        <?php
            $x = 1;
            foreach ($Output as $val) {
        ?>
                <tr>
                <td class="text-left"><?php echo $x ?></td>
                <td class="text-left"><?php echo $val['description']?></td>
                <td class="text-left"><?php echo $val['Reportedby']?></td>
                <td class="text-left"><?php echo $val['AssignedEmp']?></td>
                <td class="text-left"><?php echo $val['targetDate']?></td>
                <td class="text-left"><?php echo $val['completedDate']?></td>
                <td class="text-left"><?php echo $val['companycode']?></td>
                <td class="text-left"><?php echo $val['segment']?></td>
                </tr>

        <?php
            $x++;

            }?>

        </tbody>

        <tfoot>

        </tfoot>
    </table>
</div>
<?php } else {?>
    <br>
    <div class="row" style="margin: 0 auto;">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                No Records found
            </div>
        </div>
    </div>
<?php }?>
