<?php
$currency =  explode('|',$total_arr['payment_currency']);
$currency_str = '';

if($currency && isset($currency[1])){
    $currency_str = $currency[1];
}
?>

<table id="vendor_allocation_total" class="<?php echo table_class() ?>">
    <thead>
        <tr>
            <th style="min-width: 10%">Bank Balance Due</th><!--Code-->
            <th style="min-width: 10%">Schedule for PMT</th><!--Code-->
            <th style="min-width: 10%">Allocation</th><!--Code-->
            <th style="min-width: 10%">Fund Remain</th><!--Code-->
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center"><?php echo '<span class="text-bold">'. $currency_str.'</span>'.' '.number_format(round($total_arr['balance_due'],3),2) ?></td>
            <td class="text-center"><?php echo '<span class="text-bold">'. $currency_str.'</span>'.' '.number_format(round($total_arr['total_pmt'],3),2) ?></td>
            <td class="text-center"><?php echo '<span class="text-bold">'. $currency_str.'</span>'.' '.number_format(round($total_arr['total_allocation'],3),2) ?></td>
            <td id="total_allocation_reamin" class="text-center <?php echo ($total_arr['remaining_value'] < 0) ? 'text-danger' : 'text-success'?>" 
                style="font-weight:600;"> <?php echo '<span class="text-bold">'. $currency_str.'</span>'.' '.number_format(round($total_arr['remaining_value'],3),2) ?>
            </td>
        </tr>
    </tbody>
</table>