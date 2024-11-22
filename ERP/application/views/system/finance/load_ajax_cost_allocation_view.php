<div class="col-sm-12 col-md-12">
    <table class="table table-hover" width="100%">
        <thead>
            <tr>
                <td>Description</td>
                <td>Amount</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $table = '';
            foreach ($report as $key => $items) {
                $table .= '<tr>
                <td>' . $key . '</td>
                <td></td>
                </tr>';
                foreach ($items as $item) {
                    $table .= '<tr>
                    <td>&nbsp;&nbsp;&nbsp;' . $item['detail_description'] . '</td>
                    <td class="text-right">
                        ' . format_number($item['allocatedAmount'], $item['transactionCurrencyDecimalPlaces']) . '
                    </td>
                    </tr>';
                }
            }

            echo $table;

            ?>
        </tbody>
    </table>
</div>