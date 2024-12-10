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


<div id="PVcode" style=" font-weight: bold; border: 0px solid green; position: absolute;top: 5.2cm;Left: 3cm;"><?php echo $extra['master']['PVcode'] ?></div>
<div id="PVcode" style=" font-weight: bold; border: 0px solid green; position: absolute;top: 6.4cm;Left: 3cm;"><?php echo $extra['master']['PVdate'] ?></div>
<div id="PVcode" style=" font-weight: bold; border: 0px solid green; position: absolute;top: 7.0cm;Left: 3cm;"><?php
    if (in_array($extra['master']['pvType'],$chequetype)){
        echo $extra['master']['partyName'];
    }else{
        echo $extra['master']['nameOnCheque'];
    }
    ?></div>


<div style=" font-weight: bold; border: 0px solid green; position: absolute;top: 9.4cm;Left: 1cm;">
    <table style="font-size: 12px">
        <?php if(!empty($extra['invoices'])){?>
            <?php
            foreach($extra['invoices'] as $invoicesval){
                ?>
                <tr>
                    <td style="font-weight: bold;border: 0px solid; width: 90px"><?php echo  $invoicesval['invoiceDate'] ?></td>
                    <td style="font-weight: bold;border: 0px solid; width: 120px"><?php echo  $invoicesval['invoiceCode'] ?> </td>
                    <td style="font-weight: bold;/*padding-left: 70px*/"><?=$invoicesval['supplierInvoiceNo'].' '.$invoicesval['comment'].' '.$invoicesval['inv_com'] ?></td>
                    <td style="font-weight: bold;text-align:right; padding-right: 71px"><?php echo  format_number($invoicesval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
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
                    <td style="font-weight: bold;width: 90px;">&nbsp;</td>
                    <td style="font-weight: bold;width: 120px;">&nbsp;</td>
                    <td style="font-weight: bold;/*padding-left: 70px*/"><?=$glval['GLCode'].' '.$glval['description'].' '.$glval['comment']?></td>
                    <td style="font-weight: bold;text-align:right; padding-right: 71px"><?php echo format_number($glval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
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
                    <td style="font-weight: bold;width: 90px;">&nbsp;</td>
                    <td style="font-weight: bold;width: 120px;">&nbsp;</td>
                    <td style="font-weight: bold;/*padding-left: 70px*/"><?=$Itemsval['itemSystemCode'].' '.$Itemsval['description'].' '.$Itemsval['comment'] ?></td>
                    <td style="font-weight: bold;text-align:right; padding-right: 71px"><?php echo format_number($Itemsval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
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
                    <td style="font-weight: bold;width: 90px;">&nbsp;</td>
                    <td style="font-weight: bold;width: 120px;">&nbsp;</td>
                    <td style="font-weight: bold;/*padding-left: 70px*/"><?php echo  $Advancesval['description'] ?> - Advances</td>
                    <td style="font-weight: bold;text-align:right; padding-right: 71px"><?php echo format_number($Advancesval['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
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
                    <td style="font-weight: bold;width: 90px;">&nbsp;</td>
                    <td style="font-weight: bold;width: 120px;">&nbsp;</td>
                    <td style="font-weight: bold;/*padding-left: 70px*/"><?php echo  $debitnoteval['description'] ?> - Debitnote</td>
                    <td style="font-weight: bold;text-align:right; padding-right: 71px"><?php echo format_number($debitnoteval['transactionAmount'] *-1,$extra['master']['transactionCurrencyDecimalPlaces']) ?></td>
                </tr>
                <?php
                $total+=$debitnoteval['transactionAmount'] *-1;
            }
        }
        ?>
    </table>
</div>

<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 14.8cm;Left: 2cm;"><?php echo $extra['master']['PVNarration']; ?></div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 14.8cm;Left: 17.5cm;font-size: 12px;"><?php echo format_number($total,$extra['master']['transactionCurrencyDecimalPlaces']) ?></div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 23.9cm;Left: 16.5cm;"><?php echo $day[0]; ?><?php echo $day[1]; ?>-<?php echo $month[0]; ?><?php echo $month[1]; ?>-
    <?php echo $year[0]; ?><?php echo $year[1];?><?php echo $year[2];?><?php echo $year[3]; ?></div>

<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 24.6cm;Left: 2cm;"><?php
    if (in_array($extra['master']['pvType'],$chequetype)){
        echo $extra['master']['partyName'];
    }else{
        echo $extra['master']['nameOnCheque'];
    }
    ?></div>

<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 25.5cm;Left: 2.6cm;"> <table>
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
    </table></div>

<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 25.7cm;Left: 16.6cm;"><?php echo $totwithdecimal; ?></div>