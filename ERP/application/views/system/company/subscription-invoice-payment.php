<?php

$logo = '';
$dPlace = $master_data['invDecPlace'];
$cur_code = $master_data['CurrencyCode'];

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';

?>
<style>
    @media print {
        .hortree-label {
            background-color: white !important;
            -webkit-print-color-adjust: exact;
        }
    }
    @page
    {
        size: auto;   /* auto is the initial value */
        margin-left: 4mm;  /* this affects the margin in the printer settings */
        margin-top: 8mm;  /* this affects the margin in the printer settings */
        margin-bottom: 0mm;  /* this affects the margin in the printer settings */
    }
</style>
<br>
<br>

<link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">

<?php if ($isview == 1){?>
<div class="row" style="margin:0 auto;max-width:600px">
    <table role="presentation" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="word-wrap:break-word;font-size:0;padding:0 20px 2px" align="left">
                &nbsp;
            </td>
            <td style="word-wrap:break-word;font-size:0;padding:0 510px 2px" align="left">
                <button class="btn btn-default pull-right"  onclick="print_receipt()"><i class="fa fa-print"></i></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php }?>

<div id="payment_receipt">
    <div class="row" style="margin:0 auto;max-width:600px">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td style="word-wrap:break-word;font-size:0;padding:0 20px 2px" align="left">
                    <img width="120px" src="<?php echo $productlogo ?>">
                </td>

            </tr>

            <tr>
                <td style="word-wrap:break-word;font-size:0;padding:0 20px 10px" align="left">
                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left"><?=$master_data['company_name']?>
                        <br/>
                        <?=$master_data['companyPrintAddress']?></div>
                </td>
            </tr>

            <tr>
                <td style="word-wrap:break-word;font-size:0;padding:10px 20px 5px" align="left">
                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;font-weight:bold;text-align:left">Transaction ID - <?=$TransactionID ?></div>
                </td>
            </tr>
            <tr>
                <td style="word-wrap:break-word;font-size:0;padding:0 20px 10px" align="left">
                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;font-weight:bold;text-align:left">   <?=date('l, F dS, Y ', strtotime($master_data['createdDateTime']));?></div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="margin:0 auto;max-width:600px">
        <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0;width:100%" align="center" border="0">
            <tbody>
            <tr>
                <td style="text-align:center;vertical-align:top;border-left:0 solid #aaa;border-right:0 solid #aaa;direction:ltr;font-size:0;padding:10px 20px;padding-bottom:0">
                    <div style="vertical-align:top;display:inline-block;direction:ltr;font-size:14px;text-align:left;width:100%">
                        <table role="presentation" cellpadding="0" cellspacing="0" style="background:#fafafa;border:1px solid #dcdbdb" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:5px 5px 5px" align="left">
                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:22px;font-weight:bold;text-align:left">Invoice Summary</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="vertical-align:top;display:inline-block;direction:ltr;font-size:14px;text-align:left;width:100%">
                        <table role="presentation" cellpadding="0" cellspacing="0" style="background:#fafafa;border:1px solid #dcdbdb" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:20px 0;padding-top:8px;padding-bottom:5px">
                                    <div style="margin:0 auto;max-width:600px">
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0;width:100%" align="center" border="0">
                                            <tbody>
                                            <tr>
                                                <td style="word-wrap:break-word;font-size:0;padding:5px 25px 5px" align="left">
                                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;font-weight:bold;text-align:left">Details</div>
                                                </td>
                                                <td style="word-wrap:break-word;font-size:0;padding:5px 25px 5px" align="left">
                                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;font-weight:bold;text-align:left">Amount</div>
                                                </td>
                                                <td style="word-wrap:break-word;font-size:0;padding:-1px 25px 5px" align="left">
                                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;font-weight:bold;text-align:left">Discount %	</div>
                                                </td>
                                                <td style="word-wrap:break-word;font-size:0;padding:5px 25px 5px" align="left">
                                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;font-weight:bold;text-align:left">Discount</div>
                                                </td>
                                                <td style="word-wrap:break-word;font-size:0;padding:5px 25px 5px" align="left">
                                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;font-weight:bold;text-align:left">Sub Total</div>
                                                </td>
                                            </tr>
                                            <?php
                                            $total = 0;
                                            foreach ($det_data as $row){

                                                $total += round($row['amount'], $dPlace);
                                                ?>

                                                <tr>
                                                    <td style="word-wrap:break-word;font-size:0;padding: 0px 10px 5px;" align="left">
                                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;text-align:left"><?php echo $row['itemDescription']?></div>
                                                    </td>
                                                    <td style="word-wrap:break-word;font-size:0;padding: 0px 26px 5px;" align="left">
                                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;text-align:right"><?php echo number_format($row['amountBeforeDis'], $dPlace)?></div>
                                                    </td>
                                                    <td style="word-wrap:break-word;font-size:0;padding: 0px 26px 5px;" align="left">
                                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;text-align:right"><?php echo number_format($row['discountPer'], 2)?></div>
                                                    </td>
                                                    <td style="word-wrap:break-word;font-size:0;padding: 0px 26px 5px;" align="left">
                                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;text-align:right"><?php echo number_format($row['discountAmount'], $dPlace)?></div>
                                                    </td>
                                                    <td style="word-wrap:break-word;font-size:0;padding: 0px 26px 5px;" align="left">
                                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:22px;text-align:right"><?php echo number_format($row['amount'], $dPlace)?></div>
                                                    </td>
                                                </tr>
                                            <?php }?>

                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="margin:0 auto;max-width:600px">
        <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0;width:100%" align="center" border="0">
            <tbody>
            <tr>
                <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0;padding:40px 20px 0">

                    <div class="m_4201083474960442700mj-column-per-100" style="vertical-align:top;display:inline-block;direction:ltr;font-size:14px;text-align:left;width:100%">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:10px 25px" align="left">
                                    <table cellpadding="0" cellspacing="0" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;table-layout:auto" width="100%" border="0">
                                        <tbody><tr>
                                            <td style="padding-bottom:4px;font-size:16px">Total Invoice Amount</td>
                                            <td align="right" style="font-size:16px">&nbsp;<?= $cur_code?></td>
                                            <td width="65px" align="right" style="font-size:16px">&nbsp;<?= number_format($total, $dPlace)?> </td>
                                        </tr>

                                        </tbody></table>
                                </td>
                            </tr>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:1px 0">
                                    <div style="font-size:1px;margin:0 auto;border-top:2px solid #d3d3d3;width:90%"></div>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="margin:0 auto;max-width:600px">
        <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0;width:100%" align="center" border="0">
            <tbody>
            <tr>
                <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0;padding:10px 20px 5px">

                    <div class="m_-8976691987589760592mj-column-per-100" style="vertical-align:top;display:inline-block;direction:ltr;font-size:14px;text-align:left;width:100%">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:10px 25px" align="left">
                                    <table cellpadding="0" cellspacing="0" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;table-layout:auto" width="100%" border="0">
                                        <tbody><tr>
                                            <td width="80px" style="font-size:16px">PAID BY</td>
                                            <td></td>
                                        </tr>
                                        <tr>

                                        </tr><tr>

                                            <td width="60px" align="left" style="font-size:20px;color:#263238;font-weight:bold">

                                                <img src="<?php echo $visa?>" alt="Visa" height="40px" width="40px" align="none">

                                            </td>
                                            <td style="font-size:8px;color:#263238;font-weight:bold" align="none" valign="top">●●●●</td>
                                            <td align="right" style="font-size:16px;font-weight:bold;color:#263238"> <?= $cur_code?></td>
                                            <td width="65px" align="right" style="font-size:16px;font-weight:bold;color:#263238"><?= number_format($total, $dPlace)?> </td>
                                        </tr>
                                        </tbody></table>
                                </td>
                            </tr>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:1px 0">
                                    <div style="font-size:1px;margin:0 auto;border-top:2px solid #d3d3d3;width:90%"></div>

                                </td>
                            </tr>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:2px 0">
                                    <div style="font-size:1px;margin:0 auto;border-top:2px solid #d3d3d3;width:90%"></div>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="margin:0 auto;max-width:600px">
        <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0;width:100%" align="center" border="0">
            <tbody>
            <tr>
                <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0;padding:20px 20px 0">

                    <div class="m_-8976691987589760592mj-column-per-100" style="vertical-align:top;display:inline-block;direction:ltr;font-size:14px;text-align:left;width:100%">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td style="word-wrap:break-word;font-size:0;padding:10px 25px" align="left">
                                    <table cellpadding="0" cellspacing="0" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;table-layout:auto" width="100%" border="0">
                                        <tbody><tr>
                                            <td style="padding-bottom:4px;color:#546e7a;font-size:16px">Transaction Status</td>

                                            <?php if($TransactionStatus == 'CAPTURED') {?>
                                                <td align="right" style="color:green;font-size:16px">&nbsp;<?= $TransactionStatus ?></td>

                                            <?php } else {?>
                                                <td align="right" style="color:red;font-size:16px">&nbsp;<?= $TransactionStatus ?></td>
                                            <?php }?>




                                        </tr>
                                        <tr>
                                            <td style="padding-bottom:4px;color:#546e7a;font-size:16px">Post Date</td>
                                            <td align="right" style="color:#546e7a;font-size:16px">&nbsp;<?= $PostDate ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding-bottom:4px;color:#546e7a;font-size:16px">Transaction Reference ID</td>
                                            <td align="right" style="color:#546e7a;font-size:16px">&nbsp;<?= $TransactionRefeID ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding-bottom:4px;color:#546e7a;font-size:16px">Mrch Track ID</td>
                                            <td align="right" style="color:#546e7a;font-size:16px">&nbsp;<?= $MrchTrackID ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding-bottom:4px;color:#546e7a;font-size:16px">Payment ID</td>
                                            <td align="right" style="color:#546e7a;font-size:16px">&nbsp;<?= $PaymentID ?></td>
                                        </tr>
                                        </tbody></table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php if ($isview == 1){?>
<table align=center>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td>
            <FONT size=3 color="BLUE"><a href="<?php echo  $this->config->item('redirectpath');?>">Redirect to Spur</a>
            </FONT>
        </td>
    </tr>
</table>
<?php }?>


<script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script>
    function print_receipt() {
        $.print("#payment_receipt");
    }
</script>
