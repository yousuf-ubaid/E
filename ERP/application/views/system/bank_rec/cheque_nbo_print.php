<?php
$str =  $extra['chequeDate'];
$date = explode("-",$str);

$year = str_split($date[0]);
$month = str_split($date[1]);
$day = str_split($date[2]);
$grand_total=0;
$invoice_total=0;
$debitnote=0;
$total=0;
/*foreach($extra['detail'] as $val){
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
}*/
$grand_total =$extra['transferedAmount'];
$total= $extra['transferedAmount'];


if($extra['fromBankCurrencyID']=="OMR"){
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
        if($extra['fromBankCurrencyID']=="OMR"){
            $str1=$str1.' and '.$str_arr[1].' / 1000 Only';
        }else{
            $str1=$str1.' and '.$str_arr[1].' / 100 Only';
        }
    }else{
        $str1=$str1.' Only';
    }
}

if(!empty($str1) && !empty($str) && empty($str3)){
    if($extra['fromBankCurrencyID']=="OMR"){
        $str=$str.' and '.$str_arr[1].' / 1000 Only';
    }else{
        $str=$str.' and '.$str_arr[1].' / 100 Only';
    }
}

if(!empty($str1) && !empty($str) && !empty($str3)){
    if($extra['fromBankCurrencyID']=="OMR"){
        $str3=$str3.' and '.$str_arr[1].' / 1000 Only';
    }else{
        $str3=$str3.' and '.$str_arr[1].' / 100 Only';
    }
}
/*echo "str1 :$str1 <br/>";
echo "str2 :$str <br/>";
echo "str3 :$str3 <br/>";*/
?>
<div style="border: 1px solid white;width: 759.68503937px; height: 340.157480315px;  position: absolute;left: 6cm; rotate: 90;"><!--position: absolute;left: 6cm; rotate: 90;--><!--background-repeat: no-repeat; background-image: url(<?php //echo base_url('images/cheques/nbo_bank.png'); ?>); background-size: contain;-->
    <table style="margin-top: 50px;">
        <tr>
            <td style="width:50%;">
                <table >
                    <tbody>
                    <tr>
                       <td>&nbsp;</td>

                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:30%;">
                <table >
                    <tbody>
                    <tr>
                        <?php
                        if($extra['accountPayeeOnly'] == 1){
                            ?>
                        <td style=" font-weight: bold; font-size: 1em;"><div style="border-bottom:1px solid black;border-top:1px solid black;">A/C Payee only</div></td>
                        <?php
                        }else{
                            ?>
                        <td><img style="width: 88px;" class="hidden" src="<?php echo base_url('images/NTB_logo.png'); ?>"></td>
                        <?php
                        }
                        ?>

                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 20%; vertical-align: top;">
                <table>
                    <tbody>
                    <tr>
                        <!--<td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $day[0]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $day[1]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $month[0]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $month[1]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $year[0]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $year[1]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $year[2]; */?></td>
                        <td style=" height: 30px;width:30px; font-weight: bold; font-size: 1em;  text-align: center;"><?php /*echo $year[3]; */?></td>
                        <td style=" width:80px;">&nbsp;</td>-->
                        <td style="width:200px; font-weight: bold; font-size: 1em;  text-align:right; "><?php echo $day[0]; ?><?php echo $day[1]; ?>-<?php echo $month[0]; ?><?php echo $month[1]; ?>-<?php echo $year[0]; ?><?php echo $year[1]; ?><?php echo $year[2]; ?><?php echo $year[3]; ?></td>
                        <td style=" width:80px;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <table style="margin-top:55px;">
        <tbody>
        <tr>
            <td colspan="" style="">
                <table>
                    <tbody>
                    <tr>
                        <td style="padding-left: 60px;"><strong class="hidden">Pay :-</strong></td>

                            <td style="font-size: 1em;font-weight: bold;vertical-align: top;"><?php echo $extra['nameOnCheque']; ?> </td>


                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="font-weight: bold;text-align: center;font-size: 0.9em; padding-left: 15px;">
                <?php echo $totwithdecimal; ?>
            </td>
        </tr>
        <tr>
            <td style="width:70%; vertical-align: bottom;padding-left: 50px; ">
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

        </tr>
        </tbody>
    </table>
    <table style="text-align: center; margin-left:410px; width:50%;">
        <tbody>
        <tr>
            <?php
            for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {
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


<script>

</script>
