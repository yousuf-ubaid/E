<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$convertFormat=convert_date_format();

?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <h4> <?php echo $this->lang->line('treasury_ap_br_bank_reconcilation');?><!--Bank Reconciliation--></h4>
                        </td>
                    </tr>

                    <tr>
                        <td><strong><?php echo $this->lang->line('treasury_common_document_code');?><!--Document Code--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['bankRecPrimaryCode']; ?></td>
                    </tr>

                    <tr>
                        <td><strong><?php echo $this->lang->line('common_currency');?><!--Currency--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $GLdetails['bankCurrencyCode']; ?></td>
                    </tr>

                    <tr>
                        <td><strong><?php echo $this->lang->line('treasury_common_as_of');?><!--As of--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['bankRecAsOf']; ?></td>
                    </tr>

                    <tr>
                        <td><strong><?php echo $this->lang->line('common_month');?><!--Month--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo date("F", strtotime('00-'.$master["month"].'-01')); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_year');?><!--Year--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['year']; ?></td>
                    </tr>
<!-- 
                    <tr>
                        <td><strong>Description</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php //echo $master['description']; ?></td>
                    </tr> -->
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_bank');?><!--Bank--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $GLdetails['bankName'].' ( '.$GLdetails['bankBranch'].' )'; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_account_no');?><!--Account No-->.</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $GLdetails['bankAccountNumber']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<div class="">
    <table id="table1" width="100%"  class="">
        <tr>
            <td style=""><p style="font-weight: bolder;"><?php echo $this->lang->line('treasury_ap_br_ending_bank_balance');?><!--Ending Bank Balance--></p></td>
            <td align="right"><h5 style="padding-right: 35px;font-weight: bolder"><?php echo format_number($openingbalance,$GLdetails['bankCurrencyDecimalPlaces'])?></h5></td>
        </tr>
    </table>

    <div class="page-header">
        <h5 style="font-weight: bolder;color: #3c8dbc"><?php echo $this->lang->line('treasury_ap_br_un_cleared_receipt');?><!--Un-Cleared Receipt--> </h5>
    </div>

    <div class="table-responsive">
        <table id="table1" width="100%"  class="table table-bordered table-striped">
            <thead class="thead">
            <tr>
                <th class="theadtr"><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                <th class="theadtr"><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_ap_br_un_party_id');?><!--Party ID--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_ap_br_un_party_no');?><!--Party No--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_common_cheque_date');?><!--Cheque Date--></th>
                <th class="theadtr"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th class="theadtr"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $recieptTotal=0;
            $numformat = $GLdetails['bankCurrencyDecimalPlaces'];
            if ($details) {
                foreach ($details as $value) {
                    if ($value['transactionType'] == 1) {
                        $recieptTotal +=$value['bankCurrencyAmount'];
                        $numformat=$value['bankCurrencyDecimalPlaces'];
                        ?>
                        <tr>
                            <td><?php  echo format_date($value['documentDate'],$convertFormat) ; ?></td>
                            <td><?php echo $value['documentSystemCode'] ?></td>
                            <td><?php echo $value['partyCode'] ?></td>
                            <td><?php echo $value['partyName'] ?></td>
                            <td><?php echo $value['chequeNo'] ?></td>
                            <td><?php  echo format_date($value['chequeDate'],$convertFormat) ; ?></td>
                            <td style="text-align: right"><?php echo format_number($value['bankCurrencyAmount'],$value['bankCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            <tr style="">
                <td colspan="6" style="text-align: right"><?php echo $this->lang->line('treasury_ap_br_tot_un_rec');?><!--Total Un-Cleared Receipt--></td>
                <td class="sub_total" style="text-align: right;"></td>
                <td class="sub_total" style="text-align: right"><?php echo number_format($recieptTotal,$numformat) ?></td>
            <tr>
            </tbody>
        </table>

        <div class="page-header">
            <h5 style="font-weight: bolder;color: #3c8dbc"><?php echo $this->lang->line('treasury_ap_br_un_cleared_payment');?><!--Un-Cleared Payment--> </h5>
        </div>

        <table id="table1" width="100%"  class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="theadtr"><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                <th class="theadtr"><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_ap_br_un_party_id');?><!--Party ID--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_ap_br_un_party_no');?><!--Party No--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></th>
                <th class="theadtr"><?php echo $this->lang->line('treasury_common_cheque_date');?><!--Cheque Date--></th>


                <th class="theadtr"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th class="theadtr"></th>

            </tr>
            </thead>
            <tbody>
            <?php
            $pvTotal=0;
            $num=$GLdetails['bankCurrencyDecimalPlaces'];
            if ($details) {
                foreach ($details as $val) {
                    if ($val['transactionType'] == 2) {
                        $pvTotal +=$val['bankCurrencyAmount'];
                        $num=$val['bankCurrencyDecimalPlaces'];

                        ?>

                        <tr>
                            <td><?php  echo format_date($val['documentDate'],$convertFormat) ; ?></td>
                            <td><?php echo $val['documentSystemCode'] ?></td>
                            <td><?php echo $val['partyCode'] ?></td>
                            <td><?php echo $val['partyName'] ?></td>
                            <td><?php echo $val['chequeNo'] ?></td>
                            <td><?php  echo format_date($val['chequeDate'],$convertFormat) ; ?></td>
                            <td style="text-align: right"><?php echo number_format($val['bankCurrencyAmount'],$val['bankCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align: right"></td>


                        </tr>
                        <?php

                    }
                }
            }
            ?>
            <tr style="">
                <td class="" style="text-align: right" colspan="6"><?php echo $this->lang->line('treasury_ap_br_tot_un_pay');?><!--Total Un-Cleared Payment--> </td>
                <td class="sub_total" style="text-align: right"></td>
                <td class="sub_total" style="text-align: right"><?php echo number_format($pvTotal,$num) ?></td>
            </tr>
            <?php
            $num = -1 * abs($pvTotal);
            $bookbalance=$openingbalance+$recieptTotal+$num?>
            <tr>
                <td colspan="6" style="text-align: right"><?php echo $this->lang->line('treasury_ap_br_un_book_balance');?><!--Book Balance--></td>
                <td class="total" style="text-align: right;"></td>
                <td class="total" style="text-align: right"><?php echo number_format($bookbalance,$numformat) ?></td>
            <tr>
            </tbody>




        </table>
    </div>
</div>
<div class="table-responsive">
    <br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['createdUserName']; ?> on <?php echo $master['createdDateTime']; ?></td>
            </tr>
        <?php if($master['confirmedYN']==1){ ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $master['confirmedYNn']; ?></td>
            </tr>
        <?php }?>
        <?php if($master['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By --></b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['approvedDate']; ?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<br>


