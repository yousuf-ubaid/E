<?php

?>

<div class="table-responsive">
    <table class="table table-bordered table-striped  table-condensed">
    <thead>
    <tr>
        <th class="theadtr">#</th>
        <th class="theadtr">Document ID</th>
        <th class="theadtr">Date</th>
        <th class="theadtr">Project</th>
        <th class="theadtr">Description</th>
        <th class="theadtr">Amount committed</th>
    </tr>
    </thead>
        <tbody>
        <?php
        $num = 1;
        if(!empty($cash)) {
            foreach ($cash as $cashamt) {
                ?>
                <tr>
                    <td style="text-align: right"><?php echo $num ?>.</td>
                    <td><?php echo $cashamt['documentSystemCode'] ?> </td>
                    <td><?php echo $cashamt['documentDate'] ?> </td>
                    <td><?php echo $cashamt['projectName'] ?> </td>
                    <td><?php echo $cashamt['description'] ?> </td>
                    <td><?php echo $cashamt['transactionAmount'] ?> </td>
                </tr>
            <?php
            $num ++;
            }
        }else
        {
            echo "<tr class='danger'><td colspan='6' class='text-center'>No Records Found</td></tr>";
        }

        ?>
        </tbody>
    </table>
</div>
