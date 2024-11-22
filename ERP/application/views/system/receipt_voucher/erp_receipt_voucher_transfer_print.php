<?php
$total=0;

$total = $extra['total']['transactionAmount'];
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
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<div style="padding-left: 50px;"><p><span style="font-weight: bold;">Doc Ref No : </span> <?php echo $extra['master']['RVcode'] ?></p></div>
<div style="padding-left: 50px;"><p><span style="font-weight: bold;">Document Date : </span> <?php echo $extra['master']['RVdate'] ?></p></div>
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
    <?php echo $extra['master']['bankTransferDetails'] ?>
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