<br>
<header class="head-title">
        <h2>Material Planning</h2>
</header>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 10%" class='theadtr'>#</th>
            <th style="min-width: 30%" class="text-left theadtr">Item Code</th>
            <th style="min-width: 5%" class='theadtr'>Item Description</th>
            <th style="min-width: 5%" class='theadtr'>Current Stock</th>
            <th style="min-width: 10%" class='theadtr'>Required Qty</th>
            <th style="min-width: 11%" class='theadtr'>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($detail)) {
            foreach ($detail as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['itemSystemCode']; ?></td>
                    <td class="text-left"><?php echo $val['itemDescription']; ?></td>
                    <td class="text-right"><?php echo number_format($val['currentStock'],2) ; ?></td>
                    <td class="text-right"><?php echo number_format($val['requiredQty'],2) ; ?></td>
                    <td class="text-right"><?php echo number_format(($val['currentStock']-$val['requiredQty']),2) ; ?></td>
                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="7" class="text-center">No Records Found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>

    </table>
</div>