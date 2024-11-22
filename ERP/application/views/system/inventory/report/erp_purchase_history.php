<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div id="tbl_itemLedger">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('common_purchase_history'); ?></div>
        </div>
    </div>

    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <table class="borderSpace report-table-condensed" id="tbl_report">
                <thead class='thead'>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_doc_type'); ?></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_doc_number'); ?></th>
                        <th style="min-width: 25%" class="text-left"><?php echo $this->lang->line('common_document_date'); ?></th>
                        <!-- <th style="min-width: 5%"><?php echo $this->lang->line('common_segment'); ?></th> -->
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_narration'); ?></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_reference_no'); ?></th>
                        <th style="min-width: 8%"><?php echo $this->lang->line('common_location'); ?></th>
                        <th style="min-width: 8%"><?php echo $this->lang->line('common_transection_qty'); ?></th>
                        <th style="min-width: 8%"><?php echo $this->lang->line('common_local_currency'); ?>(<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</th>
                        <th style="min-width: 8%"><?php echo $this->lang->line('common_transection_currency'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itemdetail)) : 
                        $num = 1;
                        foreach ($itemdetail as $data) : ?>
                            <tr>
                                <td><?php echo $num; ?></td>
                                <td><?php echo $data['documentCode']; ?></td>
                                <td><?php echo $data['documentSystemCode']; ?></td>
                                <td><?php echo $data['documentDate']; ?></td>
                                <!-- <td><?php echo $data['segmentCode']; ?></td> -->
                                <td><?php echo $data['narration']; ?></td>
                                <td><?php echo $data['referenceNumber']; ?></td>
                                <td><?php echo $data['wareHouseLocation']; ?></td>
                                <td><?php echo $data['transactionQTY']; ?></td>
                                <td><?php echo $data['companyLocalAmount']; ?></td>
                                <td><?php echo $data['transactionAmount']; ?></td>
                            </tr>
                            <?php $num++; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="10" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
