<!--<br/>
<br/>
<br/>
<br/>
<br/>-->
<pre style="background: none;border: none; margin-top: 5%"><?php echo $masterData['letterDet']; ?> </pre>
<div style="margin: 2%"> &nbsp;&nbsp; </div>
<?php
$i = 1;
$j = 0;
$n = 0;
$tot = 0;
$lastBank = null;
$lastCurrency = null;
$lastGroup = null;

foreach($bankTransferDet as $data){

    $bankName = trim($data['bankName'] ?? '');
    $trCurrency = trim($data['transactionCurrency'] ?? '');
    $thisGroup = $bankName.'|'.$trCurrency;

    if( $lastBank != $bankName ){
         $lastBank = $bankName;

    echo '<div style="margin-left: 2%;">'.$bankName.'</div>';

    ?>
    <div class="table-responsive">
        <table class="<?php echo table_class(); ?>"  id="bankTransferDetailsTB" style="margin-bottom: 2%;border:1px solid">
            <thead>
                <tr style="font-size: 12px;">
                    <th class="theadtr" style="width: 5%;"> # </th>
                    <th class="theadtr" style="width: 10%"> EMP ID </th>
                    <th class="theadtr" style="width: 30%"> Name </th>
                    <th class="theadtr" style="width: 15%"> Swift Code </th>
                    <th class="theadtr" style="width: 20%"> Account No </th>
                    <th class="theadtr" style="width: 5%"> Currency </th>
                    <th class="theadtr" style="width: 20%"> Amount </th>
                </tr>
            </thead>

            <tbody>
    <?php
    } //end of 1st  if( $lastGroup != $thisGroup ){

    if( $lastCurrency != $trCurrency ){
        $lastGroup = $thisGroup;
        $lastCurrency = $trCurrency;
        echo
            '<tr style="font-size: 12px;">
                <th class="theadtr" colspan="7">'.$trCurrency.'</th>
            </tr>';
    }

        echo '<tr>
                <td align="right">'.$i++.'</td>
                <td>'.$data['ECode'].'</td>
                <td>'.$data['acc_holderName'].'</td>
                <td>'.$data['swiftCode'].'</td>
                <td>'.$data['accountNo'].'</td>
                <td>'.$trCurrency.'</td>
                <td align="right">'.number_format( $data['transactionAmount'] , $data['transactionCurrencyDecimalPlaces']).'</td>
             </tr>';

    $m = $j + 1;
    if( array_key_exists( $m , $bankTransferDet) ) {
        $nextBank = trim($bankTransferDet[$m]['bankName']);
        $nextGroup = trim($bankTransferDet[$m]['bankName']).'|'.trim($bankTransferDet[$m]['transactionCurrency']);


        if ( $lastGroup != $nextGroup ) {
            $totLine = '';
            if( $n > 0 ){
                $totThis = number_format( $data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.','') ;
                $tot += $totThis;
                $totLine = '<tr> <td colspan="6" align="right">Total</td>  <td class="theadtr" align="right">'. number_format($tot, $data['transactionCurrencyDecimalPlaces']) .'</td></tr>';
            }
            echo $totLine; //'</tbody></table></div>';
            $tot = 0;
            $n = 0;
            $lastCurrency = null;
        }
        else{
            $totThis = number_format( $data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.','') ;
            $tot += $totThis;
            $n++;
        }

        if ( $lastBank != $nextBank ) {
            echo '</tbody></table></div>';
        }
    }
    else{
        $totThis = number_format( $data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.','') ;
        $tot += $totThis;
    }

    $j++;
}
    if( $n > 0 ){
        echo '<tr> <td colspan="6" align="right">Total</td>  <td class="theadtr" align="right">'. number_format($tot, 2) .'</td></tr>';
    }
?>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="<?php echo table_class(); ?>"  id="bankTransferDetailsTB" style="margin-bottom: 2%">
        <tbody>
        <?php
        foreach($currencySum as $keyCurr=>$currencySumRow){
            $grandTitle = ($keyCurr > 0 )? '' : 'Grand Total';
            echo'<tr>
                    <th style="font-size: 10px; width:75%">'.$grandTitle.'</th>
                    <th style="font-size: 10px; width:5%"> '.$currencySumRow['transactionCurrency'].'</th>
                    <th style="font-size: 10px; text-align:right;width:20%">'.$currencySumRow['trAmount'].'</th>
                </tr>';
        }
        ?>
        </tbody>
    </table>
</div>
