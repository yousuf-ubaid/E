<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="" class="theadtr">#</th>
            <th style="" class="theadtr">Invoice Code</th>
            <th style="" class="theadtr">Invoice Date</th>
            <th style="" class="theadtr">Invoice Due Date</th>
            <th  class="theadtr">Customer system Code</th>
            <th class="theadtr">Customer Name</th>
            <th style="" class="theadtr">Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($details)) {
            foreach ($details as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['invoiceCode']; ?></td>
                    <td class="text-left"><?php echo $val['invoiceDate']; ?></td>
                    <td class="text-left"><?php echo $val['invoiceDueDate']; ?></td>
                    <td class="text-left"><?php echo $val['customerSystemCode']; ?></td>
                    <td class="text-left"><?php echo $val['customerName']; ?></td>
                    <td class="text-right"><?php echo number_format($val['transactionAmount'],2) ; ?></td>
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
