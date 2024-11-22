<style>
    .tableth {
        background-color: #f7f7f7;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .tablethcol2 {
        background-color: #ececec;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .tablethcoltotal {
        background-color: #fde49d;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .vl {
        border-left: 3px solid #f7f4f4;
        height: 500px;
    }

    .buttonacceptanddecline {
        border-radius: 0;
    }

</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(false); ?>

<div class="row" style="margin-top: 5px">
    <div class="col-md-8">
        <hr>
        <div class="table-responsive">
            <table class="table">
                <thead class='thead'>
                <tr>

                    <th style="min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                        class='tableth'>Description
                    </th>
                    <th style="min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                        class='tableth'>UOM
                    </th><!--Product-->
                    <th style="min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                        class='tableth'>Delivery Date
                    </th>
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
                    <th style="width: 8%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                        class='tablethcoltotal'>Total
                    </th><!--Price-->
                    <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                        class='tableth'>
                    </th>&nbsp;

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

                as $val) {

                if ($val['selectedByCustomer'] == 1) {
                    $status = "checked";
                } else {
                    $status = "";
                }
                    ?>
                <tr>

                    <td class="text-left tableth" width="35%;"><strong
                                style="color: #a2cc7afc;font-family:tahoma;"><?php echo $val['productnamewithcomment']; ?>
                            <strong><br>
                            <?php if($val['remarks']) {
                                echo '<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Customer Remarks : '.$val['remarks'].'</strong>';
                            }?>
                    </td>
                    <td class="text-left tableth"><label
                                style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['unitOfMeasure']; ?>
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
                                style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php
                            $SubTotalLine = (($val['requestedQty'] * $val['unittransactionAmount']));
                            $lineTotal = (($val['requestedQty'] * $val['unittransactionAmount']) - $val['discountAmount']) + $val['taxAmount'];

                            echo number_format($lineTotal, $master['transactionCurrencyDecimalPlacesqut']);?><label>
                    </td>
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
        </div>
        <?php if($master['termsAndConditions']){?>
            <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 110%;color: #3e3e3e;font-weight: 700;"><label id="expiarydateright"><u>Terms And Condition</u></strong></h6>

            <p><?php echo $master['termsAndConditions']?></p>

        <?php }?>
        <br>
        <?php if($Genqutso == 1){?>

            <div class="table-responsive" style="height: 25%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width:100%;">

                            <table style="width: 100%">
                                <tbody>
                                <tr>
                                    <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Generate Quotation / Contract / So</b></td>
                                    <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                    <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;">&nbsp; <?php echo form_dropdown('typeID',array(''=>'Select A Type','1'=>'Quotation','2'=>'Sales Order','3'=>'Contract'), '', 'class="form-control select2" style="width:75%;margin-top: -2%;" id="typeID"'); ?></td>
                                    <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;">&nbsp;
                                        <button type="button" style="margin-left: -16%" onclick="save_qut_so();" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                                            aria-hidden="true"></span> Generate
                                        </button>


                                    </td>
                                </tr>


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
    <div class="col-md-4">
        <div class="vl">
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-12">&nbsp;&nbsp;<strong
                            style="font-weight: 800;font-size: 90%;font-family: sans-serif;"><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                </div>
                <div class="form-group col-sm-12">&nbsp;<strong
                            style="color:#63636369;font-family: tahoma;font-size: 9px;"> <?php echo $this->common_data['company_data']['company_address1'] . ',' . $this->common_data['company_data']['company_address2'] . ',' . $this->common_data['company_data']['company_city'] . ',' . $this->common_data['company_data']['company_country'] ?></strong>
                </div>
                <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                            style="color:#63636369;font-family: tahoma;font-size: 9px;">Phone
                        : <?php echo $this->common_data['company_data']['company_phone'] ?></strong>


                </div>
                <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                            style="color:#63636369;font-family: tahoma;font-size: 9px;"><?php echo $this->common_data['company_data']['company_email'] ?></strong>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-12">&nbsp;&nbsp;<strong
                            style="font-weight: 800;font-size: 76%;font-family: sans-serif;color: #cc9a1c;">PROPOSAL
                    TO:</strong><br>&nbsp;&nbsp;<label style="font-family: tahoma;font-weight: 900;"><?php echo $master['quotationPersonName']?></label>
                </div>
                <div class="form-group col-sm-12">&nbsp;<strong
                            style="color:#63636369;font-family: tahoma;font-size: 61%;">&nbsp;<?php echo $master['organizationName']; ?></strong>
                </div>
                <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                            style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['orgAddress']; ?></strong>


                </div>
                <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                            style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['email']; ?></strong>
                </div>
            </div>
            <hr style="width: 88%;">
            <?php if($type !=1) {?>
            <div class="row">
                <div class="form-group col-sm-12" style="margin-left: 10px;">
                    <button class="btn buttonacceptanddecline btn-md btn-responsive"
                            style="background-color: #64A758;border-color: #64A758;width: 38%;" type="button"
                            onclick="()"><i class="fa fa-check" aria-hidden="true"></i><br><strong>Accept</strong>
                    </button>
                    <button class="btn buttonacceptanddecline btn-md btn-responsive"
                            style="background-color: #DF5240;border-color: #DF5240;width: 38%;" type="button"
                            onclick="()"><i class="fa fa-times" aria-hidden="true"></i><br><strong>DECLINE</strong>
                    </button>

                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-12" style="margin-left: 10px;margin-top: 4%;">
                    <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-left: 10px;;margin-top: 2%;">
                    <button class="btn btn-default buttonacceptanddecline btn-md btn-responsive"
                            style="width: 100%;" type="button"
                            onclick="()"><strong>Add Comment</strong>
                    </button>
                </div>
            </div>
        <?php }?>
        <br>
        <br>
        <?php if($master['confirmedYNqut']==1){?>
            <div class="table-responsive" style="height: 25%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width:100%;">

                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Confirmed By</b></td>
                                        <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                        <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;">&nbsp;<?php echo $master['qutConfirmedUser']?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Confirmed Date </b></td>
                                        <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                        <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"> &nbsp;<?php echo $master['qutConfirmDate']?> </td>
                                    </tr>
                                    <?php if(($master['approvedYN1'] == '1')||($master['approvedYN1'] == '2')) {?>
                                        <tr>
                                            <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Status </b></td>
                                            <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                            <?php if($master['approvedYN1'] == '1'){?>
                                                <td style="color:#636363ad;font-family: inherit;font-weight: 300;font-size: 8px;">&nbsp;<span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Accepted<!--Confirmed--></span></td>
                                            <?php } else {?>
                                                <td style="color:#636363ad;font-family: inherit;font-weight: 300;font-size: 8px;">&nbsp;<span class="label" style="background-color:#d80004; color: #FFFFFF; font-size: 11px;">Rejected<!--Confirmed--></span></td>
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
    </div>



    </div>
    </div>
    <br>

<script>

    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });
    });
    </script>