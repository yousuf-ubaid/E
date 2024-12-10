<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('srm_request_for_quotation');?><!--Request For Quotation--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('srm_inquiry_number');?><!--Inquiry Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $header['documentCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $header['inquiryDocumentDate']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;vertical-align: top;"><strong><?php echo $this->lang->line('common_supplier');?><!--Supplier--></strong></td>
            <td style="width:2%;vertical-align: top;"><strong>:</strong></td>
            <td style="width:33%;vertical-align: top;"><?php echo $supplier['supplierName'] . ' (' . $supplier['supplierSystemCode'] . ').<br>' . $supplier['supplierAddress1']; ?></td>
            <td style="width:15%;vertical-align: text-top"><strong><?php echo $this->lang->line('srm_ship_to');?><!--Ship To--></strong></td>
            <td style="width:2%;vertical-align: text-top"><strong>:</strong></td>
            <td style="width:33%;vertical-align: text-top"><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').<br>' . $company['company_address1']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_contact');?><!--Contact--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $supplier['supplierName']; ?></td>

            <td><strong><?php echo $this->lang->line('srm_inquiry_ship_contact');?><!--Ship Contact--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo ''; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_phone');?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $supplier['supplierTelephone']; ?></td>

            <td><strong><?php echo $this->lang->line('common_phone');?><!--Phone--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $company['company_phone']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_fax');?><!--Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $supplier['supplierFax']; ?></td>

            <td><strong><?php echo $this->lang->line('common_fax');?><!--Fax--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo ''; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_email');?><!--Email--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $supplier['supplierEmail']; ?></td>

            <td><strong><?php echo $this->lang->line('common_email');?><!--Email--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $company['company_email']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('srm_item_code');?><!--Item Code--></th>
            <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
            <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('srm_expected_delivery_date');?><!--Expected Delivery Date--></th>

        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($detail)) {
            $num = 1;
            foreach ($detail as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-center"><?php echo $val['itemSystemCode']; ?></td>
                    <td><?php echo $val['itemName']; ?></td>
                    <td class="text-center"><?php echo $val['UnitShortCode']; ?></td>
                    <td class="text-center"><?php echo $val['requestedQty']; ?></td>
                    <td class="text-center"><?php echo $val['expectedDeliveryDate']; ?></td>
                </tr>
                <?php
                $num++;
            }
        } else {
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecfound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
    </table>
</div><br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:28%;"><strong><?php echo $this->lang->line('srm_delivery_terms');?><!--Delivery Terms--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:70%;"><?php echo $header['deliveryTerms']; ?></td>
        </tr>
        </tbody>
    </table>
</div>




