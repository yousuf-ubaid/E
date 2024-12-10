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
$chequetype = array('Direct','DirectItem','DirectExpense','Employee','EmployeeExpense','EmployeeItem');
/*echo "str1 :$str1 <br/>";
echo "str2 :$str <br/>";
echo "str3 :$str3 <br/>";*/
?>
<div style="border: 1px solid white;width: 690px; height: 340px; position: absolute;left: 6cm; rotate: 90;" ><!--position: absolute;left: 6cm; rotate: 90;--><!--background-repeat: no-repeat; background-image: url(<?php //echo base_url('images/cheques/dhofar_bank.png'); ?>); background-size: contain;-->
    <table style="margin-top: 95px;">
        <tr>
            <td style="width:30%;">
                <table >
                    <tbody>
                    <tr>
                       <td>&nbsp;</td>

                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%; ">
                <table >
                    <tbody>
                    <tr>
                        <?php
                        if($extra['master']['accountPayeeOnly'] == 1){
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
                        <td style="width:320px;  font-weight: bold; font-size: 1em;  text-align: center; vertical-align: top;"><?php echo $day[0]; ?><?php echo $day[1]; ?>-<?php echo $month[0]; ?><?php echo $month[1]; ?>-<?php echo $year[0]; ?><?php echo $year[1]; ?><?php echo $year[2]; ?><?php echo $year[3]; ?></td>
                        <td style=" width:60px;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <table style="margin-top:28px;">
        <tbody>
        <tr>
            <td colspan="9" style="">
                <table>
                    <tbody>
                    <tr>
                        <td style="width: 80px"><strong class="hidden">Pay :-</strong></td>
                        <?php
                        if (in_array($extra['master']['pvType'],$chequetype)){
                            ?>
                            <td style="font-size: 1em;font-weight: bold;vertical-align: bottom;padding-bottom: 0px;"><?php echo $extra['master']['partyName']; ?></td>
                        <?php
                        }else{
                            ?>
                            <td style="font-size: 1em;font-weight: bold;vertical-align: bottom;padding-bottom: 0px;"><?php echo $extra['master']['nameOnCheque']; ?> </td>
                        <?php
                        }
                        ?>

                    </tr>
                    </tbody>
                </table>
            </td>
            <td style=" width:27px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width:67%; vertical-align: bottom; padding-right: 30px;">
                <table>
                    <tbody>
                    <tr>
                        <td style="width: 40px;"><strong class="hidden">Rupees :- </strong></td>
                        <td style="font-weight: bold;font-size: 0.9em;"><?php echo $str1 ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;padding-left: 20px;  padding-top: 13px;font-size: 0.9em;" colspan="2"><?php echo $str ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;padding-left: 20px; padding-top: 14px;font-size: 0.9em;" colspan="2"><?php echo $str3 ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 33%;padding-top:0px; padding-right: 15px;">
                <table style="border: 1px solid white;">
                    <tbody>
                    <tr>
                        <td
                            style="font-weight: bold;text-align: center;height: 60px;font-size: 0.9em; vertical-align: bottom;">
                           <?php echo $totwithdecimal; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <table style="font-weight: bold;  margin-left:300px; width:90%;vertical-align: top;">
        <tbody>
        <tr>
            <!--<td style="">
                <span><?php /*echo $this->common_data['company_data']['company_name']; */?></span>
            </td>-->
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
    <table style="text-align: center; margin-left:410px; width:50%; margin-top: 0px; margin-bottom: 50px;">
        <tbody>
        <tr>

        </tr>
        </tbody>
    </table>
</div>


<script>

</script>
