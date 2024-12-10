<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<table id="loanSettlementTable" class="<?php echo table_class() ?>">
    <thead style="">
    <tr>
        <th rowspan="2" style="width: 50px"><?php echo $this->lang->line('common_date');?><!--Date--></th>
        <th rowspan="2" style="width: 100px"><?php echo $this->lang->line('treasury_bta_opening_balance');?><!--Opening Balance--></th>
        <th rowspan="2" style="width: 100px"><?php echo $this->lang->line('treasury_tr_lm_principal_payment');?><!--Principal Repayment--></th>
        <th rowspan="2" style="width: 100px"><?php echo $this->lang->line('treasury_common_closing_balance');?><!--Closing Balance--></th>
        <th rowspan="2" style="width: 100px"><?php echo $this->lang->line('common_days');?><!--Days--></th>
        <th colspan="3" style="width: 100px;text-align: center"><?php echo $this->lang->line('treasury_common_interest');?><!--Interest--></th>
        <th rowspan="2"><?php echo $this->lang->line('treasury_tr_lm_total_payment');?><!--Total Payment--></th>
        <?php if($loan_master['receiptVoucherYN']==1){ ?>
        <th rowspan="2">Action</th>

        <?php } ?>
    </tr>
    <tr>

        <th style="width:100px;"><?php echo $this->lang->line('treasury_tr_lm_fixed');?><!--Fixed--></th>
        <th style="width:100px;"><?php echo $this->lang->line('treasury_tr_lm_variable');?><!--Variable--> (<?php echo $this->lang->line('treasury_tr_lm_libor');?>)%</th><!--LIBOR-->
        <th><?php echo $this->lang->line('treasury_tr_lm_variable_amount');?><!--Variable Amount--></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $amount = 0;
    $interest = 0;
    $variableamount=0;
    $variabletotal=0;



    if ($settlement) {
    foreach ($settlement as $val) {
        $amount += $val['principalRepayment'];
        $interest += $val['interestAmount'];
        $variableamount +=$val['variableAmount'];
        $variabletotal +=$val['variableTotal'];

        ?>

        <tr>
            <td><?php echo $val['date'] ?></td>
            <!--        <td><b>Reference No </b>- <?php /*echo $val['referenceNo'] */?></td>-->




            <td style="text-align: right"><?php echo number_format($val['principleAmount'], 2) ?></td>
            <td style="text-align: right"><?php echo number_format($val['principalRepayment'],2)?></td>
            <td style="text-align: right"><?php echo number_format($val['closingBalance'],2)?></td>
            <?php if($val['paymentVoucherYN']==1){ ?>
                <td style="text-align: right" class=""><?php echo $val['installmentDueDays'] ?></td>
            <?php }else{ ?>
            <td style="text-align: right">
                <a href="#" class="setdata1" data-name="installmentDueDays" data-type="number" data-step="Any"
                   data-url="<?php echo site_url('Bank_rec/update_loandetail'); ?>"
                   data-pk="<?php echo $val["bankFacilityDetailID"]; ?>"><?php echo  $val['installmentDueDays'] ?></a>

            </td>
            <?php } ?>
            <td style="text-align: right" class="interest"><?php echo number_format($val['interestAmount'], 2) ?></td>
            <?php if($val['paymentVoucherYN']==1){ ?>
           
            <td style="text-align: right" class=""><?php echo $val["variableLibor"]; ?></td>
            <?php }else{ ?>
                <td style="text-align: right">
                <a href="#" class="setdata" data-type="number" data-name="variableLibor" data-step="Any"
                   data-url="<?php echo site_url('Bank_rec/update_loandetail'); ?>"
                   data-pk="<?php echo $val["bankFacilityDetailID"]; ?>"><?php echo $val["variableLibor"]; ?></a>

                </td>
            <?php } ?>
            <td style="text-align: right" class="nr"><?php echo number_format($val['variableAmount'],2)?></td>
            <td style="text-align: right" class="mr"><?php echo number_format($val['variableTotal'],2)?></td>
            <?php if($loan_master['receiptVoucherYN']==1){ ?>

                <?php if($val['paymentVoucherYN']==1){ ?>
                  <td style="text-align: right" class=""><a target="_blank" onclick="documentPageView_modal('PV',<?php echo $val['paymentVoucherID'] ?>,'UOM')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a></td>
                <?php }else{ ?>
                    <td style="text-align: right" class=""><a onclick="save_payment_voucher_LO_settlement(<?php echo $val['bankFacilityDetailID'] ?>)"><span title="Create Payment Voucher" rel="tooltip" class="glyphicon glyphicon-list-alt" style="color:rgb(65, 122, 211);"></span></a></td>
                <?php } ?>
            <?php } ?>
                


        </tr>

        <?php
    }}
    ?>

    </tbody>
    <tfoot>
    <tr>
        <td></td>



        <td></td>
        <td style="text-align: right"><b><span><?php echo number_format($amount, 2); ?></span></b></td>
        <td></td>
        <td></td>
        <td style="text-align: right"><b><span class="totalinterestAmount"><?php echo number_format($interest, 2); ?></span></b></td>
        <td></td>
        <td style="text-align: right"><b><span class="variableAmount"><?php echo number_format($variableamount, 2); ?></span></b></td>
        <td style="text-align: right"><b><span class="totalPayment"><?php echo number_format($variabletotal, 2); ?></span></b></td>

    </tr>
    </tfoot>


</table>
<script>
    $('.setdata').editable({
        inputclass: 'mytextarea',
        ajaxOptions: {
            type: 'POST',
            dataType: 'json'
        },
        success: function (response, newValue) {
            var row = $(this).closest("tr");    // Find the row
            var text = row.find(".nr").text(response.variableAmount); // Find the text
            var xtext = row.find(".mr").text(response.variableTotal);
            $('#loanSettlementTable .totalPayment').text(response.Total);
            $('#loanSettlementTable .totalvariableAmount').text(response.totalvariableAmount);
            $('#loanSettlementTable .totalinterestAmount').text(response.totalinterestAmount);
            if (response.status == 'error') return response.msg; //msg will be shown in editable form
        },
        error: function (response, newValue) {
            if (response.status === 500) {
                return 'Service unavailable. Please try later.';
            } else {
                return response.responseText;
            }
        }
    });

    $('.setdata1').editable({
        inputclass: 'mytextarea',
        ajaxOptions: {
            type: 'POST',
            dataType: 'json'
        },
        success: function (response, newValue) {
            var row = $(this).closest("tr");    // Find the row
            var text = row.find(".interest").text(response.interestAmount); // Find the text
            var xtext = row.find(".mr").text(response.variableTotal);
            var textx = row.find(".nr").text(response.variableAmount);
            $('#loanSettlementTable .totalPayment').text(response.Total);
            $('#loanSettlementTable.totalvariableAmount').text(response.totalvariableAmount);
            $('#loanSettlementTable .totalinterestAmount').text(response.totalinterestAmount);
            if (response.status == 'error') return response.msg; //msg will be shown in editable form
        },
        error: function (response, newValue) {
            if (response.status === 500) {
                return 'Service unavailable. Please try later.';
            } else {
                return response.responseText;
            }
        }
    });
    </script>



