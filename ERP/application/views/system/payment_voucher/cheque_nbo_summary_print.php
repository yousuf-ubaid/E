<?php
$str =  $extra['master']['PVchequeDate'];
$date = explode("-",$str);

$year = str_split($date[0]);
$month = str_split($date[1]);
$day = str_split($date[2]);
$grand_total=0;
$invoice_total=0;
$debitnote=0;
$total=0;
foreach($extra['detail'] as $val){
    $grand_total +=$val['transactionAmount'];
}
if(!empty($extra['debitnote'])){
    foreach($extra['debitnote'] as $debt){
        $debitnote +=$debt['transactionAmount'];
    }

    $grand_total=$grand_total-$debitnote;
}
//$grand_total=5000;
if(!empty($extra['tax']['taxPercentage'])){
    if(!empty($extra['invoice'])){
        foreach($extra['invoice'] as $valu){
            $invoice_total +=$valu['transactionAmount'];
        }
        $tax=($grand_total-$invoice_total)*$extra['tax']['taxPercentage']/100;
        $total=$tax+$grand_total-$debitnote;

    }else{
        $tax=$grand_total*$extra['tax']['taxPercentage']/100;
        $total=$tax+$grand_total-$debitnote;
    }
}else{
    $total= $grand_total-$debitnote;
}

if($extra['master']['transactionCurrency']=="OMR"){
    $point=format_number($total,3);
}else{
    $point=format_number($total,2);
}
$str_arr = explode('.',$point);
/*if($str_arr[1]>0){
    if($extra['master']['transactionCurrency']=="OMR"){
        $totwithdecimal=$str_arr[0].'.'.$str_arr[1].' / 1000';
    }else{
        $totwithdecimal=$str_arr[0].'.'.$str_arr[1].' / 100';
    }
}else{
    $totwithdecimal=$point;
}*/
$totwithdecimal=$point;

/*$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
$word= $f->format($grand_total);*/


$str1 = $str = $str3 = '';
$a=$this->load->library('NumberToWords');
$number=$total;
$numberinword= $this->numbertowords->convert_number($number);
//echo "numberinword : $numberinword <br/>";
$finStr = '';
if(strlen($numberinword) > 50){
    $pos=strpos($numberinword, ' ', 50);

    $finStr = $str1 = substr($numberinword,0,$pos );
    //echo $str1.' 899 <br/>';
    $str = substr($numberinword,$pos);

    if($str1!='') {
        //echo $str . ' 129 <br/>';
        $pos2 = strpos($str1, ' ', 50);
        $finStr .= ' <br/>';
        $finStr .= $str3 = substr($numberinword, $pos, $pos2);
        //echo $str2 . ' <br/>';

        if($str3!=''){
            $pos3 = strpos($str3, ' ', 50);
            $finStr .= ' <br/>';
            $finStr .= $str4 =  substr($numberinword,$pos2,$pos3 );
            //echo $str4.'<br/>';
        }
    }else{
        $str1=$str;
        $str='';
    }

}
else{
    $str1 = $numberinword;
}

if(!empty($str1) && empty($str) && empty($str3)){
    if($str_arr[1]>0){
        if($extra['master']['transactionCurrency']=="OMR"){
            $str1=$str1.' and '.$str_arr[1].' / 1000 Only';
        }else{
            $str1=$str1.' and '.$str_arr[1].' / 100 Only';
        }
    }else{
        $str1=$str1.' Only';
    }
}

if(!empty($str1) && !empty($str) && empty($str3)){
    if($extra['master']['transactionCurrency']=="OMR"){
        $str=$str.' and '.$str_arr[1].' / 1000 Only';
    }else{
        $str=$str.' and '.$str_arr[1].' / 100 Only';
    }
}

if(!empty($str1) && !empty($str) && !empty($str3)){
    if($extra['master']['transactionCurrency']=="OMR"){
        $str3=$str3.' and '.$str_arr[1].' / 1000 Only';
    }else{
        $str3=$str3.' and '.$str_arr[1].' / 100 Only';
    }
}
/*echo "str1 :$str1 <br/>";
echo "str2 :$str <br/>";
echo "str3 :$str3 <br/>";*/
$total=0;
$chequetype = array('Direct','DirectItem','DirectExpense','Employee','EmployeeExpense','EmployeeItem');
?>
<div style="border: 1px solid white; " ><!--background-repeat: no-repeat; background-image: url(<?php //echo base_url('images/cheques/receipt_straight.jpg'); ?>); background-size: contain;-->


<table style="margin-top: 30px; margin-right:33px; ">
    <tr>
        <td style="font-weight:bold;" align="right"><?php echo $extra['master']['PVcode'] ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;" align="right"><?php echo $extra['master']['PVchequeDate'] ?></td>
    </tr>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<div style="height: 325px;">

    <table>
        <?php if(!empty($extra['invoices'])){?>
            <?php
            foreach($extra['invoices'] as $invoicesval){
                ?>
                <tr>
                    <td><?php echo  $invoicesval['invoiceDate'] ?></td>
                    <td><?php echo  $invoicesval['invoiceCode'] ?> </td>
                    <td><?php echo  $invoicesval['supplierInvoiceNo'] ?></td>
                    <td align="right"><?php echo  format_number($invoicesval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
                </tr>
                <?php
                $total+=$invoicesval['transactionAmount'];
            }
        }
        ?>

        <?php if(!empty($extra['GLs'])){?>
            <?php
            foreach($extra['GLs'] as $glval){
                ?>
                <tr>
                    <td colspan="1" align="left">&nbsp;</td>
                    <td colspan="2" align="left"><?php echo  $glval['description'] ?> - Expense</td>
                    <td align="right"><?php echo format_number($glval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
                </tr>
                <?php
                $total+=$glval['transactionAmount'];
            }
        }
        ?>

        <?php if(!empty($extra['Items'])){?>
            <?php
            foreach($extra['Items'] as $Itemsval){
                ?>
                <tr>
                    <td colspan="1" align="left">&nbsp;</td>
                    <td colspan="2" align="left"><?php echo  $Itemsval['description'] ?> - Items</td>
                    <td align="right"><?php echo format_number($Itemsval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
                </tr>
                <?php
                $total+=$Itemsval['transactionAmount'];
            }
        }
        ?>

        <?php if(!empty($extra['Advances'])){?>
            <?php
            foreach($extra['Advances'] as $Advancesval){
                ?>
                <tr>
                    <td colspan="1" align="left">&nbsp;</td>
                    <td colspan="2" align="left"><?php echo  $Advancesval['description'] ?> - Advances</td>
                    <td align="right"><?php echo format_number($Advancesval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
                </tr>
                <?php
                $total+=$Advancesval['transactionAmount'];
            }
        }
        ?>

        <?php if(!empty($extra['debitnote'])){?>
            <?php
            foreach($extra['debitnote'] as $debitnoteval){
                ?>
                <tr>
                    <td colspan="1" align="left">&nbsp;</td>
                    <td colspan="2" align="left"><?php echo  $debitnoteval['description'] ?> - Debitnote</td>
                    <td align="right"><?php echo format_number($debitnoteval['transactionAmount'] *-1,$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
                </tr>
                <?php
                $total+=$debitnoteval['transactionAmount'] *-1;
            }
        }
        ?>


    </table>
</div>

<table style="">
    <tr>
        <td colspan="3" align="center"><?php echo $extra['master']['PVNarration']; ?></td>
        <td align="right"><?php echo format_number($total,$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
    </tr>
</table>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<div style="border: 1px solid white;width: 759.68503937px; height: 340.157480315px;"><!--position: absolute;left: 6cm; rotate: 90;--><!--background-repeat: no-repeat; background-image: url(<?php //echo base_url('images/cheques/nbo_bank.png'); ?>); background-size: contain;-->
    <table style="margin-top: 87px;">
        <tr>
            <td style="width:50%;">

            </td>
            <td style="width:30%;">

            </td>
            <td style="width: 20%; vertical-align:bottom;">
                <table>
                    <tbody>
                    <tr>
                        <td style="width:200px; font-weight: bold; font-size: 1em;  text-align:right; vertical-align:bottom;"><?php echo $day[0]; ?><?php echo $day[1]; ?>-<?php echo $month[0]; ?><?php echo $month[1]; ?>-<?php echo $year[0]; ?><?php echo $year[1]; ?><?php echo $year[2]; ?><?php echo $year[3]; ?></td>
                        <td style=" width:80px;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <table style="margin-top:0px;">
        <tbody>
        <tr>
            <td colspan="" style="">
                <table>
                    <tbody>
                    <tr>
                        <td style="padding-left: 80px;"><strong class="hidden">Pay :-</strong></td>
                        <?php
                        if (in_array($extra['master']['pvType'],$chequetype)){
                            ?>
                            <td style="font-size: 1em;font-weight: bold;vertical-align: top;"><?php echo $extra['master']['partyName']; ?> </td>
                        <?php
                        }else{
                            ?>
                            <td style="font-size: 1em;font-weight: bold;vertical-align: top;"><?php echo $extra['master']['nameOnCheque']; ?> </td>
                        <?php
                        }
                        ?>

                    </tr>
                    </tbody>
                </table>
            </td>

        </tr>
        <tr>
            <td style="width:70%; vertical-align: top;padding-left: 45px; ">
                <table>
                    <tbody>
                    <tr>
                        <td style="width: 40px;"><strong class="hidden">Rupees :- </strong></td>
                        <td style="font-weight: bold;font-size: 0.9em;"><?php echo $str1 ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;padding-left: 30px;  padding-top: 13px;font-size: 0.9em;" colspan="2"><?php echo $str ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;padding-left: 30px; padding-top: 14px;font-size: 0.9em;" colspan="2"><?php echo $str3 ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="font-weight: bold;text-align: center;font-size: 0.9em; width:30%; vertical-align: center;">
                <?php echo $totwithdecimal; ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table style="text-align: center; margin-left:410px; width:50%;">
        <tbody>
        <tr>
            <?php
            for ($x = 0; $x < $extra['signature']['authourizedSignatureLevel']; $x++) {
                ?>
                <td style="vertical-align: bottom;">
                    <span>_________________________</span><br><span> Authorized Signature</span>
                </td>

                <?php
            }
            ?>
        </tr>
        </tbody>
    </table>
</div>
</div>


<script>

</script>
