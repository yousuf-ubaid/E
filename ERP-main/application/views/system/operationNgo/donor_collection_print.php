<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(true,true,$extra['master']['approvedYN']); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
                              echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('operationngo_donor_collections');?><!--Donor Collections--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_code');?><!--Code--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentSystemCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('operationngo_donor');?><!--Donor--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_currency');?><!--Currency--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['transactionCurrency']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_bank');?><!--Bank--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['DCbank'].' - '.$extra['master']['DCbankBranch'] ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('operationngo_cheque_no');?><!--Cheque No--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['DCchequeNo'] ?></td>
                    </tr>


                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<br>

    <h5><?php echo $this->lang->line('operationngo_cash_details');?><!--CASH DETAILS--></h5>

<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' style="width: 5%">#</th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_project');?><!--Project--></th>
            <th class='theadtr' style=""><?php echo $this->lang->line('common_description');?><!--Description--></th>


            <th class='theadtr' style="width: 120px"><?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0;$received_total = 0; $grandTotal=0;
          if (!empty($extra['detail_cash'])) {
            for ($i=0; $i < count($extra['detail_cash']); $i++) {
              echo '<tr>';
              echo '<td>'.($i+1).'</td>';
              echo '<td>'.$extra['detail_cash'][$i]['projectName'].'</td>';
              echo '<td>'.$extra['detail_cash'][$i]['description'].'</td>';

              echo '<td class="text-right">'.format_number($extra['detail_cash'][$i]['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
              $received_total += ($extra['detail_cash'][$i]['transactionAmount']);
            }
          }else{
              $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="4" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
          }
          $grandTotal+=$received_total;
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="3"><?php echo $this->lang->line('common_total');?><!--Total--> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td>
            <td class="text-right sub_total"><?php echo format_number($received_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>

<h5>ITEM DETAILS</h5>

<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_project');?><!--Project--></th>
            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('operationngo_item_code');?><!--Item Code--></th>
            <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('common_item_description');?><!--Item Description--></th>


            <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
            <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
            <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('operationngo_unit_amount');?><!--Unit Amount--></th>
            <th class='theadtr' style="width: 120px"><?php echo $this->lang->line('common_net_amount');?><!--Net Amount--> </th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0;$received_total = 0;
          if (!empty($extra['detail_item'])) {
            for ($i=0; $i < count($extra['detail_item']); $i++) {
              echo '<tr>';
              echo '<td>'.($i+1).'</td>';
              echo '<td>'.$extra['detail_item'][$i]['projectName'].'</td>';
              echo '<td>'.$extra['detail_item'][$i]['itemSystemCode'].'</td>';
              if($extra['detail_item'][$i]['documentSystemCode']!='')
              {
                  echo '<td>'.$extra['detail_item'][$i]['itemDescription'].'-'.$extra['detail_item'][$i]['description'].' ('.$extra['detail_item'][$i]['documentSystemCode'].')'.'</td>';
              }
              else
              {
                  echo '<td>'.$extra['detail_item'][$i]['itemDescription'].'-'.$extra['detail_item'][$i]['description'].'</td>';
              }
              echo '<td class="text-center">'.$extra['detail_item'][$i]['unitOfMeasure'].'</td>';
              echo '<td class="text-right">'.$extra['detail_item'][$i]['itemQty'].'</td>';
              echo '<td class="text-right">'.format_number($extra['detail_item'][$i]['unittransactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
              echo '<td class="text-right">'.format_number(($extra['detail_item'][$i]['itemQty']*$extra['detail_item'][$i]['unittransactionAmount']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
              echo '</tr>';
              $requested_total += ($extra['detail_item'][$i]['itemQty']*$extra['detail_item'][$i]['unittransactionAmount']);
              $received_total += ($extra['detail_item'][$i]['transactionAmount']);
            }
          }else{
              $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="8" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
          }
          $grandTotal+=$received_total;
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('common_total');?><!--Total--> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td>
            <td class="text-right sub_total"><?php echo format_number($requested_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

        </tr>
        <tr>
            <td class="text-right sub_total" colspan="7"><?php echo $this->lang->line('common_grand_total');?><!--Grand Total--> <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($grandTotal,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

        </tr>
        </tfoot>
    </table>
</div>
<?php if($extra['master']['approvedYN']){ ?>
<div class="table-responsive">

                  <table style="width: 500px !important;">
                      <tbody>
                      <tr>
                          <td><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </b></td>
                          <td><strong>:</strong></td>
                          <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                      </tr>
                      <tr>
                          <td><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </b></td>
                          <td><strong>:</strong></td>
                          <td><?php echo $extra['master']['approvedDate']; ?></td>
                      </tr>
                      </tbody>
                  </table>


</div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>/<?php echo $extra['master']['collectionAutoId'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_donor_collection'); ?>/" + <?php echo $extra['master']['collectionAutoId'] ?> + '/DC';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>