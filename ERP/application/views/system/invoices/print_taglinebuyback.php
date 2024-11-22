<?php
echo fetch_account_review(false, true); ?>
<br>
<div class="table-responsive">
    <table style="width: 45%">
        <tbody>
        <?php foreach ($extra['detail'] as $val) {?>

        <tr>
            <td style="width:40%;">
                <table>
                        <tbody>
                        <tr>
                            <td style='border-width:thin;height:40px;border: 1px solid #ddd;'>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">Date</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['invoiceDate']?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">Customer Name</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['customerName']?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">Item Name</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['itemDescription']?></td>td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">NOB</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['noOfUnits']?></td>


                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">Gross</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['grossQty']?></td>


                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">Bkt</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['deduction']?></td>


                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;">Net</td>
                                        <td>:</td>
                                        <td style="font-size: 11px;font-weight: 800; font-family: tahoma;font-weight: bold;"><?php echo $val['requestedQty']?></td>


                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        </tbody>
                    </table>


            </td>
        </tr>
        <?php }?>
        </tbody>

    </table>
</div>
<script>

</script>