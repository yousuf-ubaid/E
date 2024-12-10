<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false,true,$approval && $extra['master']['approvedYN']);

// echo fetch_account_review(false,true,$approval);
//echo '<pre>';print_r($extra);exit;
?>

<?php 
 if(isset($html)) { ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4>Work From Home Request</h4>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<?php } else {

if(($printHeaderFooterYN == 1) || ($printHeaderFooterYN == 2)){?>

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
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'] ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <h3 class="text-start"><strong>Work From Home Request</strong></h3>
<?php } else { ?>

    <br>
    <br>
    <br>
    <br>
    <br>
    <hr>
    <h4 class="text-center"><strong>Work From Home Request</strong></h4>
    <?php
    }
}?>

<div class="table-responsive"><br>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width:15%;"><h4><strong>Request Details</strong></h4></td>
                <td style="width:1%;"><strong></strong></td>
                <td style="width:20%;"></td>

                <!-- <td style="width:24%;"><h4><strong>Expected Time Period</strong></h4></td>
                <td style="width:1%;"><strong></strong></td>
                <td style="width:25%;"></td>
                 -->
            </tr>
            <tr style="margin-top:10px;">
                <td style="text-align: left;"><strong>WFH Request No</strong></td>
                <td><strong>:</strong></td>
                <td style="text-align: left;"><?php echo $extra['master']['documentCode']; ?></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>WFH Request Date</strong></td>
                <td><strong>:</strong></td>
                <td style="text-align: left;"><?php echo $extra['master']['documentDate']; ?></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Date From</strong></td>
                <td><strong>:</strong></td>
                <td style="text-align: left;"><?php echo $extra['master']['startDate']; ?></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Date To</strong></td>
                <td><strong>:</strong></td>
                <td style="text-align: left;"><?php echo $extra['master']['endDate']; ?></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Comment</strong></td>
                <td><strong>:</strong></td>
                <td style="text-align: left;"><?php echo $extra['master']['comments']; ?></td>
            </tr>
        </tbody>
    </table>
</div><br> 

<br><br><br>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <?php if ($ALD_policyValue == 1) { 
            $created_user_designation = designation_by_empid($extra['master']['createdUserID']);
            $confirmed_user_designation = designation_by_empid($extra['master']['confirmedByEmpID']);
            ?>
                <tr>
                <td style="width:24%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:1%;"><strong>:</strong></td>
                <td style="width:75%;"><?php echo $extra['master']['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['createdDateTime']; ?></td>
            </tr>
        <?php if($extra['master']['confirmedYN']==1){ ?>
            <tr>
                <td style="width:24%;"><b>Confirmed By </b></td>
                <td style="width:1%;"><strong>: </strong></td>
                <td style="width:75%;"><?php echo $extra['master']['confirmedbyName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['confirmedDate'];?></td>
            </tr>
        <?php } ?>
            <?php if(!empty($approver_details)) {
                foreach ($approver_details as $val) {
                    echo '<tr>
                            <td style="width:24%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                            <td style="width:1%;"><strong>:</strong></td>
                            <td style="width:75%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                        </tr>';
                }
            }
        }
        else {?>
            <tr>
                <td style="width:24%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:1%;"><strong>:</strong></td>
                <td style="width:75%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
            </tr>
        <?php if ($extra['master']['confirmedYN']==1) { ?>
        <tr>
            <td style="width:24%;"><b><?php echo $this->lang->line('common_confirmed_by');?><!--Confirmed By-->  </b></td>
            <td style="width:1%;"><strong>:</strong></td>
            <td style="width:75%;"><?php echo $extra['master']['confirmedYNn'];?></td>
        </tr>
        <?php } ?>
        <?php if ($extra['master']['approvedYN']) { ?>
        <tr>
            <td style="width:24%;"><strong><?php echo $this->lang->line('procurement_approval_electronically_approved_by');?><!--Electronically Approved By--> </strong></td>
            <td style="width:1%;"><strong>:</strong></td>
            <td style="width:75%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('procurement_approval_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['approvedDate']; ?></td>
        </tr>
        <?php }
        } ?>
        </tbody>
    </table>
</div>
    <br>
    <br>
    <br>



<?php if ($extra['master']['approvedYN']) { ?>
    <?php
    if ($signature) { ?>
        <?php
        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 50%";
        } else {
            $width = "width: 100%";
        }
        ?>
        <div class="table-responsive">
            <table style="<?php echo $width ?>">
                <tbody>
                <tr>
                    <?php
                    for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                        ?>

                        <td>
                            <span>____________________________</span><br><br><span><b>&nbsp;<?php echo $this->lang->line('common_authorized_signature');?> <!-- Authorized Signature --></b></span>
                        </td>

                        <?php
                    }
                    ?>
                </tr>


                </tbody>
            </table>
        </div>
    <?php } ?>

<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Employee/load_WFH_request_conformation'); ?>/<?php echo $extra['master']['wfhID'] ?>";
    $("#a_link").attr("href",a_link);

</script>



