<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true, $approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?></strong>
                            </h3>
                            <h4><?php echo $this->lang->line('transaction_material_issue'); ?> </h4>
                            <!--Material Issue-->
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma;"><strong><?php echo $this->lang->line('transaction_material_issue_number'); ?> </strong></td>
                        <!--Material issue Number-->
                        <td><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma;  font-weight: bold;"><?php echo $extra['master']['itemIssueCode']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><strong><?php echo $this->lang->line('transaction_material_issue_date'); ?> </strong></td>
                        <!--Material Issue Date-->
                        <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['issueDate']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><strong><?php echo $this->lang->line('common_reference_number'); ?></strong></td>
                        <!--Reference Number-->
                        <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
                        <td style="font-size: 17px;font-family: tahoma; vertical-align: top; padding-top: -3px;" ><?php echo $extra['master']['issueRefNo']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="table-responsive">
    <hr>
    <br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="font-size: 15px;font-family: tahoma; " ><?php echo $this->lang->line('transaction_material_requested_by'); ?> </strong></td>
            <td><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma;"><?php echo $extra['master']['employeeName'] . ' (' . $extra['master']['employeeCode'] . ' ) '; ?></td>

            <td style="font-size: 15px;font-family: tahoma;" ><?php echo $this->lang->line('transaction_common_narration'); ?></strong></td>
            <td><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma;"><?php echo $extra['master']['comment']; ?></td>

        </tr>

        <tr>
            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;" >Issued Warehouse </strong></td>
            <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['wareHouseDescription'] . ' ( ' . $extra['master']['wareHouseCode'] . ' )'; ?></td>

            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;" >Primary Segment </strong></td>
            <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['segmentCode'] ?> </td>

        </tr>

        <tr>
            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;">Financial Period  </strong></td>
            <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
            <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['FYbegining'] .' - ' . $extra['master']['FYend'] ?> </td>


        </tr>
        <tr>
            <?php
            if ($extra['master']['issueType'] == 'Material Request') { ?>
                <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><strong>Requested Warehouse</td>
                <td style="vertical-align: top; padding-top: -3px;"><strong>:</strong></td>
                <td style="font-size: 15px;font-family: tahoma; vertical-align: top; padding-top: -3px;"><?php echo $extra['master']['requestedWareHouseDescription'] . ' ( ' . $extra['master']['requestedWareHouseCode'] . ' )'; ?></td>
            <?php } ?>

        </tr>

        </tbody>
    </table>
</div>
<?php
$mrClass = '';
$colspan = 5;
if ($extra['master']['issueType'] != 'Material Request') {
    $mrClass = 'hide';
    $colspan = 5;
}?>
<div class="table-responsive">
    <br>
    <table class="table table-bordered table-striped">
        <thead>
        <!-- <tr>
            <th class='theadtr' colspan="4">Item Details</th>
            <th class='theadtr' colspan="1">Qty </th>
            <th>&nbsp;</th>
        </tr> -->
        <tr>
            <th class='theadtr' style="width: 2%">#</th>
            <th class='theadtr <?php echo $mrClass; ?>' style="width: 12%; font-size: 12px;" >MR Code</th>
            <th class='theadtr'
                style="width: 12%; font-size: 12px;"><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
            <!--Item Code-->
            <th class='theadtr'
                style="width: 25%;font-size: 12px;"><?php echo $this->lang->line('transaction_common_item_description'); ?></th>
            <!--Item Description-->
            <th class='theadtr' style="width: 5%;font-size: 12px;"><?php echo $this->lang->line('transaction_common_uom'); ?></th>
            <th class='theadtr'
                style="width: 20%;font-size: 12px;">Cost GL A/C Name</th>
            <!--UOM-->
            <!--<th class='theadtr' style="min-width: 10%">Requested</th>-->
            <th class='theadtr'
                style="width: 8%;font-size: 12px;"><?php echo $this->lang->line('transaction_material_issued'); ?></th><!--Issued-->
            <th class='theadtr' style="width: 15%;font-size: 12px;"><?php echo $this->lang->line('common_value'); ?>
                (<?php echo $extra['master']['companyLocalCurrency']; ?>)
            </th><!--Value-->
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $total_count = 0;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;font-size: 10px;"><?php echo $num; ?>.&nbsp;</td>
                    <td class="<?php echo $mrClass; ?>" style="font-size: 10px;"><?php echo $val['MRCode']; ?>.&nbsp;</td>
                    <td style="text-align:center; font-size: 10px;"><?php echo $val['itemSystemCode']; ?></td>
                    <td style="font-size: 10px;"><?php echo $val['itemDescription']; ?></td>
                    <td style="text-align:center;font-size: 10px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="font-size: 10px;"><?php echo $val['costglname']; ?></td>
                    <!--<td style="text-align:right;"><?php /*echo $val['qtyRequested']; */ ?></td>-->
                    <td style="text-align:right;font-size: 10px;"><?php echo $val['qtyIssuedFormated']; ?></td>
                    <td style="text-align:right;font-size: 10px;"><?php echo format_number($val['totalValue'], $extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num++;
                $total_count += $val['totalValue'];
            }
        } else {
            $norecfound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="8" class="text-center">' . $norecfound . '<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td>

            </td>
            <td>

            </td>
            <td class="text-right sub_total" style="font-size: 13px;" colspan="<?php echo $colspan; ?>"><?php echo $this->lang->line('transaction_common_item_total'); ?>
                (<?php echo $extra['master']['companyLocalCurrency']; ?>)
            </td><!--Item Total-->
            <td class="text-right total" style="font-size: 13px;"><?php echo format_number($total_count, $extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<br>
<div class="table-responsive">

    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=" font-size: 13px;font-family: tahoma; " ><b>Confirmed By </b></td>
            <td><strong>:</strong></td>
            <td style=" font-size: 13px;font-family: tahoma; "><?php echo $extra['master']['confirmedYNn']; ?></td>

            <td style=" font-size: 13px;font-family: tahoma; padding-left:100px ">&nbsp;</td>
            <td>&nbsp;</td>
            <td style=" font-size: 13px;font-family: tahoma; ">&nbsp;</td>
        </tr>
        <?php if ($extra['master']['approvedYN']) { ?>
        <tr>
            <td style=" font-size: 13px;font-family: tahoma; " >
                <b><?php echo $this->lang->line('transaction_common_electronically_approved_by'); ?> </b></td>
            <!--Electronically Approved By-->
            <td><strong>:</strong></td>
            <td style=" font-size: 13px;font-family: tahoma; "><?php echo $extra['master']['approvedbyEmpName']; ?></td>

            <td style=" font-size: 13px;font-family: tahoma; padding-left:100px ">
                <b><?php echo $this->lang->line('transaction_common_electronically_approved_date'); ?> </b></td>
            <!--Electronically Approved Date-->
            <td><strong>:</strong></td>
            <td style=" font-size: 13px;font-family: tahoma; "><?php echo $extra['master']['approvedDate']; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
    <br>
    <br>
    <br>
    <br>
<?php /*if($extra['master']['approvedYN']){ */?><!--
    <?php
/*    if ($signature) { */?>
        <?php
/*        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 50%";
        } else {
            $width = "width: 100%";
        }
        */?>
        <div class="table-responsive">
            <table style="<?php /*echo $width */?>">
                <tbody>
                <tr>
                    <?php
/*                    for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                        */?>

                        <td>
                            <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                        </td>

                        <?php
/*                    }
                    */?>
                </tr>


                </tbody>
            </table>
        </div>
    <?php /*} */?>
--><?php /*} */?>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/<?php echo $extra['master']['itemIssueAutoID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + <?php echo $extra['master']['itemIssueAutoID'] ?> +'/MI';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>
