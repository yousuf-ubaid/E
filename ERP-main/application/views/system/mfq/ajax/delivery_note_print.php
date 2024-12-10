<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
/*$jobCode = '';
if (!empty($details)) {
    $jobCode = array_column($details, 'documentCode');
    $jobCode = implode("<br>&nbsp;", array_unique($jobCode));
}*/
?>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr>
            <td style="width:40%;border: 1px solid black;">
                <img alt="Logo" style="height: 80px"
                     src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>"></td>
            <td style="width:60%;height:25px;border: 1px solid black;text-align: center;">
                <h5>
                    <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                </h5><h4 class="text-uppercase"><?php echo $this->lang->line('manufacturing_delivery_note') ?><!--DELIVERY NOTE--></h4></td>
        </tr>
        <tr style="border: 1px solid black;">
            <td colspan="2" style="width:100%;height:25px;border: 1px solid black;text-align: center;">&nbsp;
                <p><?php echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; ?></p>
            </td>
        </tr>
        <tr style="border: 1px solid black;">
            <td colspan="2" style="width:100%;border: 1px solid black;">&nbsp;</td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr>
            <td colspan="3" rowspan="3" style="width:40%;height:40px;border: 1px solid black;"><strong>
                    &nbsp;&nbsp;TO </strong> &nbsp;<?php echo $header['CustomerName']; ?></td>
            <td style="width:5%;height:40px;border: 1px solid black;text-align: right;background-color: lightgray;"><strong>&nbsp;<?php echo $this->lang->line('manufacturing_delivery_note_number') ?><!--D.N. No--></strong></td>
            <td style="width:25%;height:40px;border: 1px solid black;">
                &nbsp;<?php echo $header['deliveryNoteCode']; ?></td>
            <td style="width:5%;height:40px;border: 1px solid black;text-align: right;background-color: lightgray;"><strong>&nbsp;<?php echo $this->lang->line('common_date') ?><!--Date--></strong></td>
            <td style="width:20%;height:40px;border: 1px solid black;"><?php echo $header['deliveryDate']; ?></td>
        </tr>
        <tr style="border: 1px solid black;">
            <td style="width:20%;height:40px;border: 1px solid black;text-align: right;background-color: lightgray;"><strong>&nbsp;<?php echo $this->lang->line('manufacturing_job_no') ?><!--Job No--></strong>
            </td>
            <td style="width:30%;height:25px;border: 1px solid black;" colspan="3">
            <?php if(($type == 'html')&&($header['confirmedYN']!=1)){?>
                <textarea class="form-control" rows="4" onchange="update_delivernoteheader(<?php echo $header['deliverNoteID']?>,this.value);"><?php echo str_replace('|', PHP_EOL, $header['jobreferenceNo']);?></textarea>
            <?php }else{?>
                <?php echo str_replace(PHP_EOL, '<br /> ', $header['jobreferenceNo']);?>
            <?php }?>
            </td>
        </tr>
        <tr style="border: 1px solid black;">
            <td style="width:20%;height:40px;border: 1px solid black;text-align: right;background-color: lightgray;"><strong>&nbsp;Sub Job<!--Job No--></strong>
            </td>
            <td style="width:30%;height:25px;border: 1px solid black;" colspan="3">
                <?php echo $linkedSubJobs;?>
            </td>
        </tr>
        <tr style="border: 1px solid black;">
            <td colspan="7" style="width:100%;height:25px;border: 1px solid black;">&nbsp;</td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody>
        <tr style="border: 1px solid black;background-color: lightgray;">
            <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_serial_no') ?><!--Sr No--></strong></td>
            <td class="text-uppercase" style="width:10%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('common_qty') ?><!--QTY--></strong></td>
            <td style="width:20%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_purchase_order_reference') ?><!--PO Ref--> #</strong></td>
            <td class="text-uppercase" style="width:60%;height:25px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_description_or_particulars') ?><!--DESCRIPTION / PARTICULARS--></strong></td>
        </tr>

        <?php
        $x = 1;
        foreach ($details as $det) { ?>
            <tr style="border: 1px solid black;">
                <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><?php echo $x; ?></td>
                <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><?php echo $det['deliveredQty']; ?></td>
                <td style="width:20%;height:25px;border: 1px solid black;text-align: center;">&nbsp;<?php echo $det['estmPoNumber']; ?></td>
                <td style="width:60%;height:25px;border: 1px solid black;">&nbsp;<?php echo $det['itemName']; ?></td>
            </tr>
       <?php
        $x++;
        }
        while($x <= 15){ ?>
            <tr style="border: 1px solid black;">
                <td style="width:10%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                <td style="width:10%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                <td style="width:20%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                <td style="width:60%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
            </tr>
        <?php
            $x++;
        }
        ?>
        <!--<tr style="border: 1px solid black;">
            <td style="width:10%;height:25px;border: 1px solid black;text-align: center;">1</td>
            <td style="width:10%;height:25px;border: 1px solid black;text-align: center;"><?php /*echo $header['detailQty']; */?></td>
            <td style="width:20%;height:25px;border: 1px solid black;text-align: center;">&nbsp;<?php /*echo $header['estmPoNumber']; */?></td>
            <td style="width:60%;height:25px;border: 1px solid black;">&nbsp;<?php /*echo $header['itemName']; */?></td>
        </tr>
        <?php
/*        for ($x = 0; $x <= 15; $x++) { */?>
            <tr style="border: 1px solid black;">
                <td style="width:10%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                <td style="width:10%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                <td style="width:20%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
                <td style="width:60%;height:25px;border: 1px solid black;text-align: center;">&nbsp;</td>
            </tr>
            --><?php
/*        }
        */?>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody>
        <tr style="border: 1px solid black;background-color: lightgray;">
            <td style="width:40%;height:30px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('common_name') ?><!--Name--></strong></td>
            <td style="width:20%;height:30px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_vehicle_no') ?><!--Vehicle No--></strong>
            </td>
            <td style="width:20%;height:30px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_mobile_no') ?><!--Mobile No--></strong>
            </td>
            <td style="width:20%;height:30px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('common_signature') ?><!--Signature--></strong>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:40%;height:30px;border: 1px solid black;text-align: center;">
                &nbsp;<?php echo $header['driverName']; ?></td>
            <td style="width:20%;border: 1px solid black;text-align: center;">
                &nbsp;<?php echo $header['vehicleNo']; ?></td>
            <td style="width:20%;border: 1px solid black;text-align: center;">
                &nbsp;<?php echo $header['mobileNo']; ?></td>
            <td style="width:20%;border: 1px solid black;text-align: center;">&nbsp;</td>
        </tr>
        <tr style="border: 1px solid black;">
            <td colspan="4" style="width:100%;height:30px;border: 1px solid black;text-align: center;"><?php echo $this->lang->line('manufacturing_certifies_that_the_above_mentioned_materials_have_been_received_in_good_order_and_condition_or_as_per_scope_of_work') ?><!--Certifies that-->
                <!--the above mentioned materials have been received in good order and condition / as per scope of work-->
            </td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;border: 1px solid black" border="1">
        <tbody>
        <tr style="border: 1px solid black;background-color: lightgray;">
            <td colspan="4" style="width:40%;height:30px;border: 1px solid black;text-align: center;">
                <span><b>Notes</b><?php echo $header['note']; ?></span>
                <span></span>
            </td>
        </tr>
        <tr style="border: 1px solid black;background-color: lightgray;">
            <td colspan="2" style="width:40%;height:30px;border: 1px solid black;text-align: center;"><strong>Signed for : <?php echo $header['companyName']; ?>
                    </strong></td>
            <td colspan="2" style="width:60%;height:30px;border: 1px solid black;text-align: center;"><strong><?php echo $this->lang->line('manufacturing_customer_signature_and_stamp_after_completion_or_receipt') ?><!--Customer-->
                    <!--Signature & Stamp after completion / receipt--></strong></td>
        </tr>
        <tr>
            <td style="width:20%;height:30px;background-color: lightgray;">&nbsp;<?php echo $this->lang->line('common_name') ?><!--Name--></td>
            <td style="width:30%;"><?php echo $header['confirmedByName']; ?></td>
            <td style="width:20%;background-color: lightgray;">&nbsp;<?php echo $this->lang->line('common_name') ?><!--Name--></td>
            <td style="width:30%;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width:20%;height:30px;background-color: lightgray;">
                &nbsp;<?php echo $this->lang->line('common_signature') ?><!--Signature-->
            </td>
            <td style="width:30%;">&nbsp;</td>
            <td style="width:20%;background-color: lightgray;">&nbsp;<?php echo $this->lang->line('common_signature') ?><!--Signature--></td>
            <td style="width:30%;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width:20%;height:30px;background-color: lightgray;">
                &nbsp;<?php echo $this->lang->line('common_date') ?><!--Date-->
            </td>
            <td style="width:30%;"><?php echo $header['confirmedDate']; ?></td>
            <td style="width:20%;background-color: lightgray;">
                &nbsp;<?php echo $this->lang->line('common_date') ?><!--Date-->
            </td>
            <td style="width:30%;">
                &nbsp;</td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">

    function update_delivernoteheader(DNAutoID,JobValue)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'DNAutoID':DNAutoID,'value':JobValue},
            url: "<?php echo site_url('MFQ_DeliveryNote/save_deliverynote_jobno'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                } else {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


</script>