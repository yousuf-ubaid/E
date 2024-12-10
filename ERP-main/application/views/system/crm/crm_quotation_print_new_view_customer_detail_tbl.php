<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div class="table-responsive">
    <table class="table">
        <thead class='thead'>
        <tr>

            <th style="width: 38%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Description
            </th>
            <th style="width: 11%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>UOM
            </th>
            <th style="width: 11%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Delivery Date
            </th>

            <!--Product-->
            <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Qty
            </th><!--UOM-->
            <th style="width: 9%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tablethcol2'>Price
            </th><!--Delivery Date-->
            <th style="min-width: 5%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Discount
            </th><!--Narration-->
            <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>TAX
            </th><!--Qty-->
            <th style="width: 12%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tablethcoltotal'>Total
            </th><!--Price-->
            <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $num = 1;
        $lineTotal = 0;
        $TaxTotal = 0;
        $SubTotalLine = 0;
        $discountamt = 0;
        $grandTotal = 0;
        $subtotal = 0;
        if (!empty($detail)) {
        foreach ($detail as $val) {
        if ($val['selectedByCustomer'] == 1) {
            $status = "checked";
        } else {
            $status = "";
        }

            ?>


        <tr>
            <?php if(($master['approvedYN1'] == 1) || ($master['approvedYN1'] == 2)||($master['approvedYN1'] == 3)||($master['approvedYN1'] == 4)){ ?>
            <td class="text-left tableth" width="40%;"><strong
                        style="color: #a2cc7afc;font-family:tahoma;"><?php echo $val['productnamewithcomment']; ?>
                    <strong><br>
                        <strong><br>
                            <textarea class="form-control" rows="2" name="narration" id="narration" onchange="save_customer_remarks('<?php echo $val['contractDetailsAutoID'] ?>',this.value,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>',quatationId,companyID,quatationId)" disabled><?php echo $val['remarks']; ?></textarea>
            </td>
            <?php } else {?>
            <td class="text-left tableth" width="40%;"><strong
                        style="color: #a2cc7afc;font-family:tahoma;"><?php echo $val['productnamewithcomment']; ?>
                    <strong><br>
                        <strong><br>
                            <textarea class="form-control" rows="2" name="narration" id="narration" onchange="save_customer_remarks('<?php echo $val['contractDetailsAutoID'] ?>',this.value,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>',quatationId,companyID,quatationId)"><?php echo $val['remarks']; ?></textarea>
            </td>
            <?php }?>

            <td class="text-left tableth"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php
                            if($val['uomshortcode'])
                            {

                                echo $val['uomshortcode'];
                            }else
                            {
                                echo '-';
                            }


                        ?>
                    <label></td>
            </td>

            <td class="text-left tableth"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['expectedDeliveryDateformated']; ?>
                    <label></td>
            </td>
            <td class="text-right tableth"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['requestedQty'],2)?>
                    <label></td>
            </td>
            <td class="text-right tablethcol2"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['unittransactionAmount'],$master['transactionCurrencyDecimalPlacesqut'])?>
                    <label></td>
            <td class="text-right tableth"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo ($val['discountPercentage']).'%'?><label>
            </td>
            <td class="text-right tableth"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo ($val['taxPercentage']).'%'?><label>
            </td>
            <td class="text-right tablethcoltotal"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php $SubTotalLine = (($val['requestedQty'] * $val['unittransactionAmount']));
                    $lineTotal = (($val['requestedQty'] * $val['unittransactionAmount']) - $val['discountAmount']) + $val['taxAmount'];

                    echo number_format($lineTotal, $master['transactionCurrencyDecimalPlacesqut']);?><label>
            </td>
            <?php if(($master['approvedYN1'] == 1)||($master['approvedYN1'] == 2)||($master['approvedYN1'] == 3)||($master['approvedYN1'] == 4)){ ?>
                <td class="text-right tableth">
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns" style="text-align: right">
                            <input id="isactive_<?php echo $val['contractDetailsAutoID'] ?>"
                                   type="checkbox" <?php echo $status ?>
                                   data-caption=""
                                   class="columnSelected isactive"
                                   name="isactive[]"
                                   value="<?php echo $val['contractDetailsAutoID'] ?>" disabled>
                        </div>
                    </div>
                </td>
            <?php } else {?>
                <td class="text-right tableth">
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns" style="text-align: right">
                            <input id="isactive_<?php echo $val['contractDetailsAutoID'] ?>"
                                   type="checkbox" <?php echo $status ?>
                                   data-caption=""
                                   class="columnSelected isactive"
                                   name="isactive[]"
                                   value="<?php echo $val['contractDetailsAutoID'] ?>">
                        </div>
                    </div>
                </td>
            <?php }?>




        </tr>
        <?php
        $num++;
        $total += $val['unittransactionAmount'];
        $grandTotal += $lineTotal;
        $subtotal += $SubTotalLine;
        $discountamt += $val['discountAmount'];
        $TaxTotal += $val['taxAmount'];
        }
        } else {
            $norecfound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecfound . '</td></tr>';
        } ?><!--No Records Found-->
        </tbody>
        <tfoot>
        <tr>
            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="4">
                SUB TOTAL
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="3">
                <?php echo number_format($subtotal, $master['transactionCurrencyDecimalPlacesqut']) ?>
            </td>
        </tr>
        <tr>
            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="4">
                DISCOUNT
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="3">
                <?php echo number_format($discountamt, $master['transactionCurrencyDecimalPlacesqut']) ?>
            </td>
        </tr>
        <tr>
            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="4">
                TAX
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="3">
                <?php echo number_format($TaxTotal, $master['transactionCurrencyDecimalPlacesqut']) ?>
            </td>
        </tr>
        <tr style="border-bottom: 2px solid #ffffff;">
            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #ffffff;color:#565555;font-family: tahoma;font-weight: bold;font-size: 13px;"
                class="text-right" colspan="4">
                GRAND TOTAL
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #ffffff;color:#565555;font-family: tahoma;font-weight: bold;font-size: 13px;"
                class="text-right" colspan="3">
                <?php echo number_format($grandTotal, $master['transactionCurrencyDecimalPlacesqut']) ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<script>


    var quatationdetailid = '<?php echo $quatationId?>'
    var companyID = '<?php echo $companyID?>'
    var quatationId = '<?php echo $quatationId?>'
    $('input').on('ifChecked', function (event) {
        update_item_detail_active(this.value, 1,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>',quatationId,companyID,quatationId);

    });

    $('input').on('ifUnchecked', function (event) {
        update_item_detail_active(this.value, 2,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>',quatationId,companyID,quatationId);

    });

    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });
    });
    function update_item_detail_active(detailid,val,csrf,hash,quatationId,companyID,quatationId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {DetailID: detailid,val:val,companyID:companyID,csrf_token:hash},
            url: "<?php echo site_url('QuotationPortal/save_sales_qutation_accepted_item'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if(data[0]='s')
                {
                    detailtable(quatationId,companyID,csrf,hash);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }
    function save_customer_remarks(detailid,val,csrf,hash,quatationId,companyID) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {DetailID: detailid,val:val,companyID:companyID,csrf_token:hash},
            url: "<?php echo site_url('QuotationPortal/save_sales_qutation_accepted_item_remarks'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if(data[0]='s')
                {
                    detailtable(quatationId,companyID,csrf,hash);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }



</script>