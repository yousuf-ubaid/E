<div class="row">
    <div class="form-group col-sm-4">
        <i class="fa fa-long-arrow-right" aria-hidden="true">&nbsp;<?php echo $taxCatName ?></i>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <table class="<?php echo table_class(); ?>">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tax</th>
                    <th>Description</th>
                    <th>Tax Percentage</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $x = 1;
                $totalTax = 0;
                foreach ($taxDetail as $val) { ?>
                    <tr>
                        <td style="text-align: right;"><?php echo $x ?></td>
                        <td> Description : <b> <?php echo $val['taxDescription'] ?> </b> &nbsp;&nbsp;Secondary Code : <b> <?php echo $val['taxShortCode'] ?> </b></td>
                        <td> <?php echo $val['description'] ?></td>
                        <?php if($isFromView == 1){?>

                            <td><input class="text taxpercentage" onkeypress="return validateFloatKeyPress(this,event);"  id="taxpercentage_<?php echo $val['taxLedgerAutoID'] ?>" value="<?php echo $val['taxPercentage'] ?>" name="taxpercentage" disabled></td>
                            <td style="text-align:right"><input class="text amount" id="amount_<?php echo $val['taxLedgerAutoID'] ?>" value="<?php echo $val['amount'] ?>" name="amount" disabled></td>
                        <?php } else {?>

                            <td><input class="text taxpercentage" onkeypress="return validateFloatKeyPress(this,event);" onchange="calculateTax(this,<?php echo $val['taxLedgerAutoID'] ?>,<?php echo $val['taxFormulaDetailID'] ?>,<?php echo $documentMasterAutoID ?>,<?php echo $documentDetailAutoID ?>,'<?php echo $detailTBL ?>','<?php echo $detailColName ?>','<?php echo $documentID ?>','1','<?php echo ($taxDetailAutoID) ?>',<?php echo $currency_decimal ?>)" id="taxpercentage_<?php echo $val['taxLedgerAutoID'] ?>" value="<?php echo $val['taxPercentage'] ?>" name="taxpercentage"></td>
                            <td style="text-align:right"><input class="text amount" onchange="calculateTax(this,<?php echo $val['taxLedgerAutoID'] ?>,<?php echo $val['taxFormulaDetailID'] ?>,<?php echo $documentMasterAutoID ?>,<?php echo $documentDetailAutoID ?>,'<?php echo $detailTBL ?>','<?php echo $detailColName ?>','<?php echo $documentID ?>','2','<?php echo ($taxDetailAutoID) ?>',<?php echo $currency_decimal ?>)" id="amount_<?php echo $val['taxLedgerAutoID'] ?>" value="<?php echo $val['amount'] ?>" name="amount"></td>

                        <?php }?>


                    </tr>

                <?php
                    $totalTax += $val['amount'];
                    $x++;
                } ?>

            </tbody>
            <tfoot>
                <tr>
                <td colspan="4" class="text-right">Tax Total </td><td class="text-right total"><span><?php echo number_format($totalTax,$currency_decimal)?></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="text/javascript">
 var currency_decimal = <?php echo $currency_decimal?>; 
function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }
</script>