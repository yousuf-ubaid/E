<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$defaultCurrencyID = $this->common_data['company_data']['company_default_currency'];
if($type==true){
    $class ='theadtr';
    ?>
<?php } else {
    $class ='';
}?>
    <div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
                                echo mPDFImage.$this->common_data['company_data']['company_logo'];  ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').';  ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('sales_maraketing_masters_customer_sales_prices_details');?><!--Customer Sales Price Details--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_code');?><!--Document Code--></strong></td>
                        <td><strong>:&nbsp;</strong></td>
                        <td><?php echo $extra['master']['documentSystemCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_maraketing_masters_customer_code');?><!--Customer Code--></strong></td>
                        <td><strong>:&nbsp;</strong></td>
                        <td><?php echo $extra['master']['customer']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                        <td><strong>:&nbsp;</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_narration');?><!--Narration--></strong></td>
                        <td><strong>:&nbsp;</strong></td>
                        <td><?php echo $extra['master']['narration']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
    <br>
    <br>
    <hr style="margin-top: 0%">
    <div class="table-responsive">
        <br>
        <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th class= "<?php echo $class?>" style="width: 5%" rowspan="2">#</th>
                <th class= "<?php echo $class?>" style="width: 15%" rowspan="2"><?php echo $this->lang->line('transaction_common_item_code');?><!--Item Code--></th>
                <th class= "<?php echo $class?>" style="width: 40%" rowspan="2"><?php echo $this->lang->line('transaction_common_item_description');?><!--Item Description--></th>
                <th class= "<?php echo $class?>" style="width: 20%" colspan="2"><?php echo $this->lang->line('sales_maraketing_masters_applicable_date');?><!--Applicable date--></th>
                <th class= "<?php echo $class?>" style="width: 20%" colspan="2"><?php echo $this->lang->line('common_price');?><!--Price--> (<?php echo $defaultCurrencyID; ?>)</th>
            </tr>
            <tr>
                <th class= "<?php echo $class?>"><?php echo $this->lang->line('common_date_from');?><!--Date From--></th>
                <th class= "<?php echo $class?>"><?php echo $this->lang->line('common_date_to');?><!--Date To--></th>
                <th class= "<?php echo $class?>"><?php echo $this->lang->line('sales_maraketing_masters_default_price');?><!--Default Price--></th>
                <th class= "<?php echo $class?>"><?php echo $this->lang->line('sales_maraketing_masters_customer_price');?><!--Customer Price--></th>
            </tr>
            </thead>
            <tbody id="grv_table_body">
            <?php
            if (!empty($extra['itemPriceDetails'])) {
                $a = 1;
                foreach ($extra['itemPriceDetails'] as $val) {
                    echo '<tr>';
                    echo '<td>' . $a . '</td>';
                    echo '<td>' . $val['itemSystemCode'] . '</td>';
                    echo '<td>' . $val['itemDescription'] . '</td>';
                    echo '<td>';
                    if(trim($val['applicableDateFrom'] ?? '') == null)
                    {
                        echo 'Not Assigned';
                    } else
                    {
                        echo $val['applicableDateFrom'];
                    }
                    echo '</td>';
                    echo '<td>';
                    if(trim($val['applicableDateTo'] ?? '') == null)
                    {
                        echo 'Not Assigned';
                    } else
                    {
                        echo $val['applicableDateTo'];
                    }
                    echo '</td>';
                    echo '<td style="text-align: right">' . format_number($val['companyLocalSellingPrice'], $extra['master']['customerCurrencyDecimalPlaces']) . '</td>';
                    echo '<td style="text-align: right">' . format_number($val['salesPrice'], $extra['master']['customerCurrencyDecimalPlaces']) . '</td>';
                }
            } else {
                echo '<tr class="danger"><td colspan="7" class="text-center"><b>'.$this->lang->line('common_no_records_found')/*No Records Found*/.'</b></td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-responsive">

            <table style="width: 600px !important;">
                <tbody>
                <tr>
                    <?php if ($extra['master']['confirmedYN']) { ?>
                    <td><b><?php echo $this->lang->line('common_confirmed_by');?><!-- Confirmed By--> </b></td>
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?></td>
                    <?php } ?>
                </tr>

                <?php if($extra['master']['approvedYN']){ ?>
                    <tr>
                        <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?></b></td><!--Electronically Approved By-->
                        <td><strong>:</strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?></b></td><!--Electronically Approved Date-->
                        <td><strong>:</strong></td>
                        <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

    </div>


