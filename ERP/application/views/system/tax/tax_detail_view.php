
<?php

$taxDetails =  fetch_tax_details($documentCode,$documentID,(isset($isRcmDocument)?$isRcmDocument:0));
?>
<?php if(!empty($taxDetails)){?>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:40%;">
                &nbsp;
            </td>
            <td style="width:60%;padding: 0;">
                <table style="width: 100%" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <td  class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tax Details</strong></td>
                    </tr>
                    <tr>
                        <th  class='theadtr'>#</th>
                        <th  class='theadtr'>Type</th>
                        <th  class='theadtr'>Detail</th>
                        <th  class='theadtr'>Tax Percentage %</th>
                        <th  class='theadtr'> Transaction (<?php echo $transactionCurrency ?>)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $x = 1;
                        $tax_totl=0;
                        foreach ($taxDetails as $tax){  ?>

                        <tr>
                        <td style="text-align:right;font-size: 10px" ><?php echo $x?></td>
                        <td style="text-align:left;font-size: 10px"><?php echo $tax['taxShortCode']?></td>
                        <td style="text-align:left;font-size: 10px"><?php echo $tax['taxDescription']?></td>
                        <td style="text-align:left;font-size: 10px"><?php echo $tax['taxPercentage'].'%' ?></td>
                        <td style="text-align:right;font-size: 10px"><?php echo number_format($tax['taxAmount'],$transactionCurrencyDecimal) ?></td>
                             </tr>

                    <?php
                            $tax_totl+=$tax['taxAmount'];
                            $x++;
                        } ?>

                    </tbody>
                    <tfoot>


                    <tr>
                        <td class="text-right sub_total" colspan="3" style="font-size: 12px;text-align:right;">Tax Total</td>
                        <td colspan="2" class="text-right sub_total" style="font-size: 12px;text-align:right;"><?php echo format_number($tax_totl,$transactionCurrencyDecimal)?></td>
                    </tr>


                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
</div>
    <br>
<?php }?>