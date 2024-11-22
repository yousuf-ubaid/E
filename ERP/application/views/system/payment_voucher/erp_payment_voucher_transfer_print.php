<?php
/*$str =  $extra['master']['PVchequeDate'];
$date = explode("-",$str);

$year = str_split($date[0]);
$month = str_split($date[1]);
$day = str_split($date[2]);*/
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
//echo "numberinword : $numberinword <br/>";
$finStr = '';
if($str_arr[1]>0){
    if($extra['master']['transactionCurrency']=="OMR"){
        $numberinword=$numberinword.' and '.$str_arr[1].' / 1000';
    }else{
        $numberinword=$numberinword.' and '.$str_arr[1].' / 100';
    }
}
$bankTransfer = array('SupplierAdvance','SupplierInvoice','SupplierItem','SupplierExpense','Supplier');
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<div style="padding-left: 50px;"><p><span style="font-weight: bold;">Doc Ref No : </span> <?php echo $extra['master']['PVcode'] ?></p></div>
<div style="padding-left: 50px;"><p><span style="font-weight: bold;">Document Date : </span> <?php echo $extra['master']['PVdate'] ?></p></div>
<br>
<br>
<div style="padding-left: 50px;"><p>The Manager,</p></div>
<div style="padding-left: 50px;"><p><?php echo $extra['master']['bankName'] ?></p></div>
<br>

<div style="padding-left: 50px;"><p>Dear Sir / Madam,</p></div>
<div style="font-weight: bold;text-align: center;"><u>FUND TRANSFER</u></div>
<br>
<div style="padding-left: 50px;"><p>By debiting our AccountNo : <span style="font-weight: bold;"><?php echo $extra['master']['bankAccountNumber'] ?></span> Kindly transfer a sum of &nbsp;   <span style="font-weight: bold;"><?php echo $extra['master']['transactionCurrency'] ?> <?php echo $totwithdecimal ?></span></p> <p>(<span style="font-weight: bold;"><?php echo $extra['master']['transactionCurrency'] ?></span> <?php echo $numberinword ?>)</p> <p>to the following account as detailed below </p>  </div>
<br>
<div style="padding-left: 50px;">

    <?php
    if (in_array($extra['master']['pvType'],$bankTransfer)){
            ?>
            <table>
                <tbody>
                <tr>
                    <td style="font-size: 1em; width: 200px;">Beneficiary Name</td>
                    <td>:</td>
                    <td style="font-size: 1em; width: 450px;"><?php echo $extra['supplier']['nameOnCheque'] ?></td>
                </tr>
                <tr>
                    <td style="font-size: 1em; width: 200px;">Beneficiary Bank Name</td>
                    <td>:</td>
                    <td style="font-size: 1em; width: 450px;"><?php echo $extra['bank']['bankName'] ?></td>
                </tr>
                <tr>
                    <td style="font-size: 1em; width: 200px;">Beneficiary Bank Address</td>
                    <td>:</td>
                    <td style="font-size: 1em; width: 450px;"><?php echo $extra['bank']['bankAddress'] ?></td>
                </tr>
                <tr>
                    <td style="font-size: 1em; width: 200px;">Beneficiary Bank Account</td>
                    <td>:</td>
                    <td style="font-size: 1em; width: 450px;"><?php echo $extra['bank']['accountNumber'] ?></td>
                </tr>
                <?php if(!empty($extra['bank']['swiftCode'])){?>
                    <tr>
                        <td style="font-size: 1em; width: 200px;">Beneficiary Swift Code</td>
                        <td>:</td>
                        <td style="font-size: 1em; width: 450px;"><?php echo $extra['bank']['swiftCode'] ?></td>
                    </tr>
                <?php } ?>
                <?php if(!empty($extra['bank']['IbanCode'])){?>
                    <tr>
                        <td style="font-size: 1em; width: 200px;">Beneficiary ABA/Routing</td>
                        <td>:</td>
                        <td style="font-size: 1em; width: 450px;"><?php echo $extra['bank']['IbanCode'] ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="font-size: 1em; width: 200px;">Reference</td>
                    <td>:</td>
                    <td style="font-size: 1em; width: 450px;">Supplier Payment</td>
                </tr>
                <!--<tr>
                    <td style="font-size: 1em; width: 200px;">Bank Charges</td>
                    <td>:</td>
                    <td style="font-size: 1em; width: 450px;">&nbsp;</td>
                </tr>-->
                </tbody>
            </table>
    <?php
        }else{
            ?>
            <?php echo $extra['master']['bankTransferDetails'] ?>
    <?php
        }
    ?>


</div>

<br>
<br>
<br>
<div style="padding-left: 50px;"><p>Yours faithfully,</p></div>
<div style="padding-left: 50px;"><p><i><span style="">for :</span></i></p> <p><i><?php echo  current_companyName(true); ?></i></p></div>
<br>
<br>
<br>

<?php
if ($signature) { ?>
    <?php
    if ($signature['approvalSignatureLevel'] <= 2) {
        $width = "width: 60%";
    } else {
        $width = "width: 100%";
    }
    ?>

    <?php
        if($signature['approvalSignatureLevel']== '' || $signature['approvalSignatureLevel']==0){
            $signaturelevels = 1;
        }else
        {
            $signaturelevels = $signature['approvalSignatureLevel'];
        }
    ?>

    <div class="table-responsive">
        <table style="text-align: center;margin-left:50px;<?php echo $width ?> margin-top: 35px;">
            <tbody>
            <tr>
                <?php
                for ($x = 0; $x < $signaturelevels; $x++) {

                    ?>

                    <td>
                        <span>_______________________________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                    </td>

                    <?php
                }
                ?>
            </tr>


            </tbody>
        </table>
    </div>
<?php } ?>

<!--<table style="text-align: center; margin-left:50px; width:60%; margin-top: 35px;">
    <tbody>
    <tr>
        <td style="">
            <span>_______________________________________________</span><br><span> Authorized Signature</span>
        </td>
        <td style="padding-left:150px;">
            <span>_______________________________________________</span><br><span> Authorized Signature</span>
        </td>
    </tr>
    </tbody>
</table>-->
