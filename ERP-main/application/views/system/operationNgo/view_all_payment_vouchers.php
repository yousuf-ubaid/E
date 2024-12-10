<?php

?>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 15%">Pv Code</th>
            <th class='theadtr' style="min-width: 15%">Supplier Name</th>
            <th class='theadtr' style="min-width: 10%">Narration</th>
            <th class='theadtr' style="min-width: 15%">Paid Amount</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php
        $totalamt = 0;
        $totalamtpaid = 0;
        $x = 1;

        if (!empty($header)) {
            foreach ($header as $val) {
                echo '<tr>';
                echo '<td>' . $x . '</td>';
                echo '<td>  <a href="#" onclick="load_paymentvoucher_invoices(' . $val['payVoucherAutoId'] . ')">&nbsp;&nbsp;&nbsp;' . $val['PVcode'] . '</a></td>';
                echo '<td>' . $val['partyName'] . '</td>';
                echo '<td>' . $val['PVNarration'] . '</td>';
                echo '<td class="text-right">' . number_format($val['transactionAmount'], 2) . '</td>';
                echo '</tr>';
                $x++;
                $totalamt += $val['transactionAmount'];
            }

        } else {

            echo '<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="4">Total</td>
            <td class="text-right sub_total"><?php echo number_format($totalamt, 2) ?></td>

        </tr>
        </tfoot>
    </table>

</div>
