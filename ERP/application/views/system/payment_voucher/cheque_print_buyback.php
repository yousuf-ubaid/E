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
$totwithdecimal=$point;
$str1 = $str = $str3 = '';
$a=$this->load->library('NumberToWords');
$number=$total;
$numberinword= $this->numbertowords->convert_number($number);

$finStr = '';
if(strlen($numberinword) > 40){
    $pos=strpos($numberinword, ' ', 40);

    $finStr = $str1 = substr($numberinword,0,$pos );
    //echo $str1.' 899 <br/>';
    $str = substr($numberinword,$pos);

    if($str1!='') {
        //echo $str . ' 129 <br/>';
        $pos2 = strpos($str1, ' ', 40);
        $finStr .= ' <br/>';
        $finStr .= $str3 = substr($numberinword, $pos, $pos2);
        //echo $str2 . ' <br/>';

        if($str3!=''){
            $pos3 = strpos($str3, ' ', 40);
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

?>
<?php
if($extra['master']['accountPayeeOnly'] == 1){?>
<div style="border: 0px solid green; position: absolute;top: 8cm;Left: 7.5cm; rotate: 90;border-bottom:1px solid black;border-top:1px solid black;font-family:'Times New Roman', Times, serif">
A/C Payee only
</div>
                       
                        
                        <?php
                        }?>

<div style="font-family:'Times New Roman', Times, serif;border: 0px solid green; position: absolute;top: 1.3cm;Left: 6.2cm; rotate: 90;">
<strong class="hidden">Pay :-</strong>
    <?php
    echo ((in_array($extra['master']['pvType'],$chequetype)))? $extra['master']['partyName']:  $extra['master']['nameOnCheque'];
    ?>

</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.1cm;Left: 7.8cm; rotate: 90;">
        <?= $day[1]; ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.3cm;Left: 7.8cm; rotate: 90;">
        <?= $day[0];?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.5cm;Left: 7.8cm; rotate: 90;">
        <?= '/'?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.7cm;Left: 7.8cm; rotate: 90;">
        <?= $month[0]; ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.9cm;Left: 7.8cm; rotate: 90;">
        <?= $month[1]; ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.1cm;Left: 7.8cm; rotate: 90;">
        <?= '/'?>
</div>

<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.3cm;Left: 7.8cm; rotate: 90;">
<?= $year[0]; ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.5cm;Left: 7.8cm; rotate: 90;">
<?= $year[1]; ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.1cm;Left: 7.8cm; rotate: 90;">
        <?= '/'?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.7cm;Left: 7.8cm; rotate: 90;">
<?= $year[2]; ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.9cm;Left: 7.8cm; rotate: 90;">
<?= $year[3]; ?>
</div>

<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 1.6cm;Left: 5.2cm; rotate: 90;">

<?= $str1 ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 0.9cm;Left: 4.4cm; rotate: 90;">

<?= $str ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 1cm;Left: 3.6cm; rotate: 90;">

<?= $str3 ?>
</div>

<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.1cm;Left: 4.7cm; rotate: 90;">

<?= $totwithdecimal ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.4cm;Left: 3.4cm; rotate: 90;">

<?= $this->common_data['company_data']['company_name'] ?>
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 12.7cm;Left: 1.9cm; rotate: 90;">

................................
</div>
<div style="font-family:'Times New Roman', Times, serif;  border: 0px solid green; position: absolute;top: 13.8cm;Left: 1.5cm; rotate: 90;">
Director
</div>
<script>

</script>

