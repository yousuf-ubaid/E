<?php
$str =  $extra['transferedDate'];
$date = explode("-",$str);

$year = str_split($date[0]);
$month = str_split($date[1]);
$day = str_split($date[2]);
$grand_total=0;
$invoice_total=0;
$debitnote=0;
$total= $extra['transferedAmount'];

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
        if($extra['CurrencyCode']=="OMR"){
            $str1=$str1.' and '.$str_arr[1].' / 1000 Only';
        }else{
            $str1=$str1.' and '.$str_arr[1].' / 100 Only';
        }
    }else{
        $str1=$str1.' Only';
    }
}

if(!empty($str1) && !empty($str) && empty($str3)){
    if($extra['CurrencyCode']=="OMR"){
        $str=$str.' and '.$str_arr[1].' / 1000 Only';
    }else{
        $str=$str.' and '.$str_arr[1].' / 100 Only';
    }
}

if(!empty($str1) && !empty($str) && !empty($str3)){
    if($extra['CurrencyCode']=="OMR"){
        $str3=$str3.' and '.$str_arr[1].' / 1000 Only';
    }else{
        $str3=$str3.' and '.$str_arr[1].' / 100 Only';
    }
}
$chequetype = array('Direct','DirectItem','DirectExpense','Employee','EmployeeExpense','EmployeeItem');
?>
<!--position: absolute;left: 6cm; rotate: 90;--><!--background-repeat: no-repeat; background-image: url(<?php //echo base_url('images/cheques/dhofar_bank.png'); ?>); background-size: contain;-->

<?php
if($extra['accountPayeeOnly'] == 1){
    ?>
    <div style="font-weight: bold;  border-bottom:1px solid black;border-top:1px solid black; position: absolute;top: 9.5cm;Left: 14.5cm; rotate: 90;">A/C Payee only</div>
    <?php
}else{
    ?>
    <div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 4.7cm;Left: 14.4cm; rotate: 90;">
        <img style="width: 88px;" class="hidden" src="<?php echo base_url('images/NTB_logo.png'); ?>">
    </div>
    <?php
}
?>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 14.5cm;Left: 14.1cm; rotate: 90;">
<?php echo $day[0]; ?><?php echo $day[1]; ?>-<?php echo $month[0]; ?><?php echo $month[1]; ?>-<?php echo $year[0]; ?><?php echo $year[1]; ?><?php echo $year[2]; ?><?php echo $year[3]; ?>
</div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 4.2cm;Left: 12.3cm; rotate: 90;">
<strong class="hidden">Pay :-</strong>
    <?php
    echo ((in_array($extra['master']['pvType'],$chequetype)))? $extra['master']['partyName']:  $extra['master']['nameOnCheque'];
    ?>

</div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 2.5cm;Left: 11.3cm; rotate: 90;">
    <strong class="hidden">Rupees :- </strong>
    <?php
    echo $str1;
    ?>
</div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 2cm;Left: 9.8cm; rotate: 90;width: 100%">
    <?php echo $str; ?>
</div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 2cm;Left: 8.9cm; rotate: 90;width: 100%">
    <?php echo $str3; ?>
</div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 13cm;Left: 11cm; rotate: 90;width: 100%">
        <?php
                           $am_arr = explode('.', $totwithdecimal);
                           echo $am_arr[0];?>
</div>
<div style="font-weight: bold;  border: 0px solid green; position: absolute;top: 16.8cm;Left: 11cm; rotate: 90;width: 100%">
   <?php echo $am_arr[1]; ?>
</div>


<script>

</script>
