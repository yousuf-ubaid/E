<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$salesPerson_list = array_column($extra['details'], 'invoiceDetailID');
$company_decimal_place =get_company_currency_decimal();
//echo fetch_account_review(false,true,0);
?>
<br>
<?php if($type==true){?>
<div class="row">
    <div class="col-sm-10"></div>
    <div class="col-sm-2">
        <span class="no-print pull-right" style="margin-left: 6px;">
            <?php echo fetch_account_review(false,true,0); ?>
        </span>
        <span class="no-print pull-right">
            <?php echo export_buttons('invoiceCommisionReport', 'Invoice Commission Report', true, false, 'btn-sm');?>
        </span>
    </div>
</div>
<?php }?>
<div id="invoiceCommisionReport">
<div class="table-responsive" >

<table style="width: 100%">
    <tbody>
    <tr>
        <td style="width:60%;">
            <table>
                <tr>
                    <td>
                        <img alt="Logo" style="height: 130px" src="<?php
                        echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:40%;">
            <table>
                <tr>
                    <td colspan="3">
                        <h3>
                            <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                        </h3>
                        <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                        <h4>Invoice Commission</h4>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></strong></td><!--SC Number-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['invoiceCode']; ?></td>
                </tr>
                <tr>
                    <td><strong>Invoice Commission Code</strong></td>
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['documentSystemCode']; ?></td>
                </tr>
                <tr>
                    <td><strong>Invoice Date</strong></td>
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['invoiceDate']; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</div>
<hr>
<?php

//$num = count($extra['details']);
if (!empty($extra['details'])) {
    /* foreach( $extra['details'] as $row){
            $dat[$row['invoiceDetailID']] = ['item' => $row['salesPersonEmpID'],'designationID' => $row['designationID'],'commissionAmount' => $row['commissionAmount']]
            $dat[$row['invoiceDetailID']][$row['commissionDetailID']] = ['salesPersonEmpID' => $row['salesPersonEmpID'],'designationID' => $row['designationID'],'commissionAmount' => $row['commissionAmount']];
        }
        //print_r($data[$row['invoiceDetailID']][$row['commissionDetailID']]);
        foreach ($dat as $invoiceDetailID => $commissionDetailID) {

            echo $invoiceDetailID.'<br>';
            print_r($commissionDetailID);
            foreach($commissionDetailID as $det => $val) {
            print_r($val['salesPersonEmpID']) .'<br>';
            echo 'acd';
            }
            
        } */
    ?>
        
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th class='theadtr'
                    colspan="3"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?></th>
                <!--Item Details-->
                <th class='theadtr'
                    colspan="8">Commission Details</th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 4%">#</th>
                
                <th class='theadtr'
                    style="min-width: 6%"><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?></th>
                <!--Item Code-->
                <th class='theadtr'
                    style="min-width: 20%"><?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?></th>
                <!--Item Description-->
                <th class='theadtr'
                    style="min-width: 5%" title="Salesperson Code">SP Code</th>
                <!--Sales Person Code-->
                <th class='theadtr'
                    style="min-width: 10%" >Sales Person</th>
                <!--Sales Person Name-->
                <th class='theadtr'
                    style="min-width: 5%" title="Employee Code">EMP Code </th>
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('common_employee'); ?> </th>
                <!--Employee-->
                <th class='theadtr'
                    style="min-width: 10%"><?php echo $this->lang->line('common_designation'); ?> </th>
                <!--Employee-->
                <th class='theadtr' style="min-width:10%" >Qty </th>
                <th class='theadtr' style="min-width:10%" >Unit Amount </th>
                <th class='theadtr' style="min-width:10%" ><?php echo $this->lang->line('common_amount'); ?>  <!-- (<?php //echo $extra['master']['transactionCurrency']; ?>) -->
                </th>
            </tr>
        </thead>
        <tbody>
            
            <?php 
            $num = 1;
            $item_total = 0;
            if (!empty($extra['details'])) {
                foreach ($extra['details'] as $val) {
                ?><tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:left;"><?php echo $val['seconeryItemCode']; ?></td>
                    <td>
                         <?php echo $val['itemDescription']; ?>
                         <?php if(!empty($val['comment']) && empty($val['partNo']))
                        {
                            echo ' - ' .  $val['comment'];
                        }else if(!empty($val['comment']) && !empty($val['partNo']))
                        {
                            echo ' - ' .  $val['comment'] . ' - ' .'Part No : ' .$val['partNo'];
                        }
                        else if(!empty($val['partNo']))
                        {
                            echo  ' - ' . 'Part No : ' .$val['partNo'];
                        }
                        ?>
                    </td>
                    <td style="text-align:left;"><?php echo $val['salesPersonSecondaryCode']; ?></td>
                    <td style="text-align:left;"><?php echo $val['salesPersonName']; ?></td>
                    <td style="text-align:left;"><?php echo $val['empoyeeSecondarycode']?></td>
                    <td style="text-align:left;"><?php echo $val['employeeName']; ?></td>
                    <td style="text-align:left;"><?php echo $val['DesDescription']; ?></td>
                    <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['UnitcommisionAmount'], $company_decimal_place); ?></td>
                    <?php if($confirmedYN == 1){ ?>
                        <td style="text-align:right;"><?php echo format_number($val['commissionAmount'], $company_decimal_place); ?></td>
                    <?php }else{ ?>
                        <td style="text-align:right;">
                        <input type="text" class="number form-control commissionAmount " type="text" name="commissionAmount"
                        data-commissionDetailID='<?php echo $val['commissionDetailID']; ?>'
                        value='<?php echo format_number($val['commissionAmount'], $company_decimal_place); ?>' 
                        data-currentCommission='<?php echo format_number($val['commissionAmount'], $company_decimal_place); ?>' > 
                    </td>
                    <?php } ?>
                   
                    </tr>
                <?php 
                $num++;
                }
            }?>
            
        </tbody>
    </table>

    </div>
    </div>
    <hr>
    <?php  
    $i = 0; 
   
      
  
    $i++;
 
    if($extra['master']['confirmedYN']!=1 ){
        if ($type == true) {
        ?>
    <div class="text-right m-t-xs">
        <button class="btn btn-success submitWizard" onclick="ic_confirmation(<?php echo $extra['master']['commissionAutoID'] ?>)">Confirm<!--Confirm--></button>
        <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close-commissionAutoID--></button>
    </div>
    <?php
        }
    }
  
?>
<br>
<br>
<br>
<br>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
         <?php if ($extra['master']['confirmedYN']==1) { ?>
        <tr>
            <td style="width:30%;"><b>Confirmed By </b></td>
            <td><strong>:</strong></td>
            <td style="width:70%;"><?php echo $extra['master']['confirmedYNn'];?></td>
        </tr>
        <?php } ?>
        <?php if ($extra['master']['approvedYN']) { ?>
            <tr>
                <td style="width:30%;"><b>Electronically Approved By </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b>Electronically Approved Date</b></td><!--Electronically Approved Date -->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
}else{
    
} ?>
<br>
<br>
<script>
    commissionAutoID=<?php echo $extra['master']['commissionAutoID'] ?>;
    var company_decimal_place = <?php echo get_company_currency_decimal();  ?>;
    
    $( document ).ready(function() {
        number_validation();
    });
    $(".commissionAmount").change(function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }
        var commissionDetailID = $(this).attr('data-commissionDetailID');
        var commissoinAmount = $(this).val();
        $(this).val(parseFloat(commissoinAmount));
        update_new_commission_amount(commissionDetailID,commissoinAmount, this);
    });

    function update_new_commission_amount(commissionDetailID,commissoinAmount, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                commissionDetailID: commissionDetailID,
                commissoinAmount: commissoinAmount
            },
            url: "<?php echo site_url('CommissionScheme/update_new_commission_amount'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                //var commissoinAmount = parseFloat(commissoinAmount).formatMoney(company_decimal_place, '.', ',');
                $(element).attr('data-currentCommission',commissoinAmount);
            },
            error: function () {
            }
        });
    }
</script>
