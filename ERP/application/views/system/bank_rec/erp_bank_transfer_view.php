<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
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
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']?></strong></h3>
                            <h4><?php 
                            if ($master['transferType'] == 1) {
                                echo "Bank Transfer";
                            } else if($master['transferType'] == 2){
                                echo "Cheque";
                            } else if($master['transferType'] == 3){
                                echo "ATM Transfer";
                            } else {
                                echo "Online Transfer";
                            }
                            ?></h4>
                        </td>
                    </tr>

                    <tr>
                        <td><strong><?php echo $this->lang->line('common_code');?><!--Code--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['bankTransferCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['transferedDate']; ?></td>
                    </tr>


                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td><strong><?php echo $this->lang->line('treasury_transfer_type');?><!--Transfer Type--></strong></td>
            <td><strong>:</strong></td>
            <td>
            <?php 
                if ($master['transferType'] == 1) {
                    echo "Bank Transfer";
                } else if($master['transferType'] == 2){
                    echo "Cheque";
                } else if($master['transferType'] == 3){
                    echo "ATM Transfer";
                } else {
                    echo "Online Transfer";
                }
                ?>           
            </td>
        </tr>
        <?php if($master['transferType'] == 2){ ?>
        <tr>
            <td><strong><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $master['chequeNo']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('treasury_common_cheque_date');?><!--Cheque Date--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $master['chequeDate']; ?></td>
        </tr>
        <?php }?>
        <tr>
            <td><strong><?php echo $this->lang->line('treasury_bta_bank_from');?><!--Bank From --></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $master['bankfrom']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('treasury_bta_bank_to');?><!--Bank To--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $master['bankto']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('treasury_common_reference_no');?><!--Reference No--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $master['referenceNo']; ?></td>
        </tr>


        <tr>
            <td style="vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--></strong></td>
            <td style="vertical-align: top"> <strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $master['narration']);?></td>
                    </tr>
                </table>
                <?php //echo $master['narration']; ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_amount');?><!--Amount--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo number_format($master['transferedAmount'],$master['fromDecimalPlaces']); ?> (<?php echo $master['CurrencyCode'];?>)</td>
        </tr>
        </tbody>
    </table>
</div>




<div class="table-responsive">
    <br>
    <table style="width: 100%">
        <tbody>
        <?php if ($ALD_policyValue == 1) { 
            $created_user_designation = designation_by_empid($master['createdUserID']);
            $confirmed_user_designation = designation_by_empid($master['confirmedByEmpID']);
            ?>
                <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $master['createdDateTime']; ?></td>
            </tr>
        <?php if($master['confirmedYN']==1){ ?>
            <tr>
                <td style="width:30%;"><b>Confirmed By </b></td>
                <td><strong>: </strong></td>
                <td style="width:70%;"><?php echo $master['confirmedbyName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $master['confirmedDate'];?></td>
            </tr>
        <?php } ?>
            <?php if(!empty($approver_details)) {
                foreach ($approver_details as $val) {
                    echo '<tr>
                            <td style="width:30%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                            <td><strong>:</strong></td>
                            <td style="width:70%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                        </tr>';
                }
            }
        } else {?>
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
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date --></b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $master['approvedDate']; ?></td>
            </tr>
        <?php }
        }?>
        </tbody>
    </table>
</div>
<br>


