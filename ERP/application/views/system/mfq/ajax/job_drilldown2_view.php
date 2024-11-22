<?php
$x = 1;
if (!empty($mfqJobDetail)) {
    ?>
    <table class="table table-condensed" style="background-color: #DAF4F0;" width="100%">
        <tbody>
        <?php foreach ($mfqJobDetail as $val) { ?>
            <tr>
                <td style="width: 4%"></td>
                <td style="width: 12%"><?php echo $val['documentCode']; ?></td>
                <td style="width: 12%"><?php echo $val['documentDate']; ?></td>
                <td style="width: 18%"><?php echo $val['CustomerName']; ?></td>
                <td style="width: 20%"><?php echo $val['itemDescription']; ?></td>
                <td style="width: 10%"><?php echo $val['description']; ?></td>
                <td style="width: 4%"><?php echo approval_status($val['approvedYN']); ?></td>
                <td style="width: 4%"><?php echo get_job_status($val['confirmedYN']); ?></td>
                <td style="width: 12%"><?php echo "<span class='text-center' style='vertical-align: middle'>" . job_status($val['percentage']) . "</span>"; ?></td>
                <td style="width: 5%"><?php echo editJob($val['workProcessID'], $val['confirmedYN'], $val['approvedYN'], $val['isFromEstimate'], $val['estimateMasterID'], $val['linkedJobID'], $val['documentCode']); ?></td>
            </tr>
            <?php
            $x++;
        } ?>
        </tbody>
    </table>
    <script>
        $("[rel=tooltip]").tooltip();
    </script>
<?php }

