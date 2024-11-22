<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(false); ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                        <th style=" border-left: 3px solid #daa520;"><strong style="font-family: tahoma;font-weight: 900;font-size: 108%;">Estimate</strong></th>
                        </td>

                    </tr>
                    <tr>
                        <td>
                        <th style=" border-left: 3px solid #daa520;"><strong style="font-weight: 800;font-size: 76%;font-family: sans-serif;color: #cc9a1c;">PROPOSAL TO:</strong></th>
                        </td>

                    </tr>
                    <tr>
                        <td>
                        <th style=" border-left: 3px solid #daa520;"><strong style="font-family: tahoma;font-weight: 900;"><?php echo $master['contactfirstname']?> </strong></th>
                        </td>

                    </tr>
                    <tr>
                        <td>
                        <th style=" border-left: 3px solid #daa520;padding-top: 0.5px;"><strong
                                    style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['Organizationname']?> </strong></th>
                        </td>

                    </tr>
                    <tr>
                        <td>
                        <th style=" border-left: 3px solid #daa520;padding-top: -9px;"><strong
                                    style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['orgaddress']?></strong></th>
                        </td>

                    </tr>
                    <tr>
                        <td>
                        <th style=" border-left: 3px solid #daa520;padding-top:-9px;"><strong style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['quotationPersonEmail']?></strong></th>
                        </td>

                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td><th style="padding-top: 30px;">&nbsp;<strong style="font-weight: 300;font-size: 127%;color: #b4b3b3;"><lablel id="quotationcode"><?php echo $master['quotationCode'] ?></lablel></strong></th></td>
                    </tr>
                    <tr>
                        <td><th style="padding-top: -7px;">&nbsp;<strong style="font-family: tahoma;font-size: 70%;color: #c6c1c1;">Date of Issuance : <?php echo $master['quotationDate']?></strong></th></td>
                    </tr>
                    <tr>
                    <td><th style="padding-top: -7px;">&nbsp;<strong style="font-family: tahoma; font-size: 70%;color: #3e3e3e;font-weight: 700;">Open Till : <?php echo $master['quotationExpDate']?> </strong></th></td>
                    </tr>
                    <tr>
                        <td><th style="padding-top: -7px;">&nbsp;<strong style="font-family: tahoma; font-size: 70%;color: #3e3e3e;font-weight: 700;">Reference Number : <?php
                                if($master['referenceNo'])
                                {
                                    echo $master['referenceNo'];
                                }else
                                {
                                    echo '-';
                                }


                                ?> </strong></th></td>
                    </tr>
                    <tr>
                        <td><th style="padding-top: -7px;">&nbsp;<strong style="font-family: tahoma; font-size: 70%;color: #3e3e3e;font-weight: 700;">Narration :
                                <?php
                                if($master['quotationNarration'])
                                {
                                    echo $master['quotationNarration'];
                                }else
                                {
                                    echo '-';
                                }


                                ?> </strong></th></td>
                    </tr>
                  <tr>
                      <td><th>&nbsp;<strong style="font-weight: 800;font-size: 90%;font-family: sans-serif;"><?php echo $this->common_data['company_data']['company_name']; ?></strong></th></td>
                  </tr>
                    <tr>
                        <td>
                        <th style="padding-top: -7px;">&nbsp;<strong style="color:rgba(155,155,155,0.41);font-family: tahoma;font-size: 9px;"><?php echo $this->common_data['company_data']['company_address1'] . ',' . $this->common_data['company_data']['company_address2'] . ',' . $this->common_data['company_data']['company_city'] . ',' . $this->common_data['company_data']['company_country'] ?></strong>
                        </th>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <th style="padding-top: -7px;">&nbsp;<strong style="color:rgba(155,155,155,0.41);font-family: tahoma;font-size: 9px;">Phone : <?php echo $this->common_data['company_data']['company_phone'] ?></strong>
                        </th>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <th style="padding-top: -9px;">&nbsp;<strong style="color:rgba(155,155,155,0.41);font-family: tahoma;font-size: 9px;"><?php echo $this->common_data['company_data']['company_email'] ?></strong>
                        </th>
                        </td>
                    </tr>


                </table>
            </td>

        </tr>
        </tbody>
    </table>
</div>
<hr style="color: #c3c2c2;">
<br>
<br>

<div class="table-responsive">
    <table class="table">
        <thead class='thead'>
        <tr>

            <th style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;min-width: 50%;text-align:left;color: #585858;font-family:tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
                >Description
            </th><!--Product-->
            <th style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
               >Delivery Date
            </th>
            <th style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
               >Qty
            </th><!--UOM-->
            <th style="background-color: #ececec;color: black;border-bottom: 2px solid #ffffff;width: 9%;text-align:center;color: #585858;font-family: tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
                >Price
            </th>
            <th style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;min-width: 50%;text-align:left;color: #585858;font-family:tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
            >Discount
            </th><!--Product-->
            <th style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
            >TAX
            </th><!--Delivery Date-->
            <th style="background-color: #fde49d;color: black;border-bottom: 2px solid #ffffff;width: 8%;text-align:center;color: #585858;font-family: tahoma;font-weight: bold;border-bottom: 2px solid #ffffff;"
                >Total
            </th><!--Price-->

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
        foreach ($detail

        as $val) { ?>
        <tr>

            <td class="text-left" width="48%;" style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;"><strong style="font-weight: bold; color: #a2cc7afc;font-family:tahoma;font-size: 12px;"><?php echo $val['productnamewithcomment']; ?></strong><br>

                <strong style="font-family: tahoma;font-size: 11px;color: #999494;"><?php if ($val['remarks'])
                { echo 'Customer Remarks :'.  $val['remarks'];}?></strong>


            </td>
            <td class="text-left" style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;"><strong
                        style="color:rgba(115,115,115,0.68);font-family: inherit;font-weight: 100;font-size: 12px;"><?php echo $val['expectedDeliveryDateformated']; ?>
                </strong></td>
            </td>
            <td class="text-right" style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;"><strong
                        style="color:rgba(115,115,115,0.68);font-family: inherit;font-weight: 100;font-size: 12px;"><?php echo number_format($val['requestedQty'],2)?>
                </strong></td>
            </td>

            <td class="text-right" style="background-color: #ececec;color: black;border-bottom: 2px solid #ffffff;width: 9%;color: #585858;font-family: tahoma;font-weight: 700;border-bottom: 2px solid #ffffff;"><strong
                        style="color:rgba(115,115,115,0.68);font-family: inherit;font-weight: 100;font-size: 12px;"><?php echo number_format($val['unittransactionAmount'],$master['transactionCurrencyDecimalPlacesqut'])?>
                </strong></td>
            <td class="text-right" style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;">
                <strong
                        style="color:rgba(115,115,115,0.68);font-family: inherit;font-weight: 100;font-size: 12px;"><?php echo ($val['discountPercentage']).'%'?>
                </strong>

            </td>
            <td class="text-right" style="background-color: #f7f7f7;color: black;border-bottom: 2px solid #ffffff;">
                <strong
                        style="color:rgba(115,115,115,0.68);font-family: inherit;font-weight: 100;font-size: 12px;"><?php echo ($val['taxPercentage']).'%'?>
                </strong>

            </td>
            <td class="text-right" style="background-color: #fde49d;color: black;border-bottom: 2px solid #ffffff;width: 8%;color: #585858;font-family: tahoma;font-weight: 700;border-bottom: 2px solid #ffffff;"><strong
                        style="color:rgba(115,115,115,0.68);font-family: inherit;font-weight: 100;font-size: 12px;">


                    <?php
                    $SubTotalLine = (($val['requestedQty'] * $val['unittransactionAmount']));
                    $lineTotal = (($val['requestedQty'] * $val['unittransactionAmount']) - $val['discountAmount']) + $val['taxAmount'];

                    echo number_format($lineTotal, $master['transactionCurrencyDecimalPlacesqut']);?></strong>
            </td>
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
            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:rgba(92,92,92,0.68);font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="4">
                SUB TOTAL
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="5">
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
                class="text-right" colspan="5">
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
                class="text-right" colspan="5">
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
                class="text-right" colspan="5">
                <?php echo number_format($grandTotal, $master['transactionCurrencyDecimalPlacesqut']) ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <br>
    <br>
    <?php if($master['termsAndConditions']){?>
        <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 110%;color: #3e3e3e;font-weight: bold;"><label id="expiarydateright"><u>Terms And Condition</u></strong></h6>
        <br>
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;vertical-align: top;"><strong style="font-size: 13px;"> <?php echo $master['termsAndConditions']?></strong></td><!--Organization Name-->
            </tr>

            </tbody>
        </table>

    <!--  --><?php /*echo $master['termsAndConditions']*/?>

    <?php }?>
        <br>
    <?php if($master['confirmedYNqut']==1){?>
        <div class="table-responsive" style="height: 25%;">
            <table style="width: 100%;">
                <tr>
                    <td style="width:100%;">

                        <table style="width: 100%">
                            <tbody>
                            <tr>
                                <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"><b>Confirmed By</b></td>
                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"><strong>: </strong></td>
                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;">&nbsp;<?php echo $master['qutConfirmedUser']?></td>
                            </tr>
                            <tr>
                                <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"><b>Confirmed Date </b></td>
                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"><strong>: </strong></td>
                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"> &nbsp;<?php echo $master['qutConfirmDate']?> </td>
                            </tr>
                            <?php if(($master['approvedYN1'] == '1')||($master['approvedYN1'] == '2')) {?>
                                <tr>
                                    <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"><b>Status </b></td>
                                    <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 20px;"><strong>: </strong></td>
                                    <?php if($master['approvedYN1'] == '1'){?>
                                        <td style="color:#636363ad;font-family: inherit;font-weight: 300;font-size: 8px;">&nbsp;<span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 15px;">Accepted<!--Confirmed--></span></td>
                                    <?php } else {?>
                                        <td style="color:#636363ad;font-family: inherit;font-weight: 300;font-size: 8px;">&nbsp;<span class="label" style="background-color:#d80004; color: #FFFFFF; font-size: 15px;">Rejected<!--Confirmed--></span></td>
                                    <?php }?>
                                </tr>


                            <?php }?>
                            <?php if(($master['approvalComment']!='')) {?>
                                <tr>
                                    <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Comment </b></td>
                                    <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                    <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"> &nbsp;<?php echo $master['approvalComment']?> </td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>

                    </td>
                    <td style="width:60%;">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </div>
    <?php }?>
</div>