<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$assignBuyersPolicy = getPolicyValues('ABFC', 'All');
echo fetch_account_review(false,true,$approval); ?>
   
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
                                <input type="hidden" name="request_id" value="<?php echo $extra['detail']['id']; ?>" id="requestid">
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('common_travel_request');?><!--Travel Request--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_travel_request_number');?><!--Purchase Request Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['detail']['id']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_name');?><!--Name--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['detail']['employeeName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_travel_request_Date');?><!--Purchase Request Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['detail']['requestDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('commom_trip_type');?><!--Reference Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['detail']['tripType']; ?><br>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
      <thead class='thead'>
      <tr>
        <th style="min-width: 5%">#</th>
        <th style="min-width: 10%"><?php echo $this->lang->line('common_employee'); ?></th>
        <?php
            if($extra['detail']['requestType']!='Trip Request'){ ?>
        <th style="min-width: 10%"><?php echo $this->lang->line('common_family_name'); ?></th>
        <th style="min-width: 5%"><?php echo $this->lang->line('commom_birthdate'); ?></th>
        <?php }?>
        <th><?php echo $this->lang->line('commom_from_destination'); ?></th>
        <th><?php echo $this->lang->line('commom_to_destination'); ?></th>
        <th style="min-width: 5%"><?php echo $this->lang->line('common_start_date'); ?></th>
        <th style="min-width: 5%"><?php echo $this->lang->line('common_end_date'); ?></th>
        <th style="min-width: 8%"><?php echo $this->lang->line('common_currency'); ?></th>
        <th style="min-width: 8%"><?php echo $this->lang->line('common_type'); ?></th>
        <!-- <th style="min-width: 8%"><?php echo $this->lang->line('common_amount'); ?></th> -->
        <th style="min-width: 8%"><?php echo $this->lang->line('common_comment'); ?></th>
      </tr>
      </thead>
        <tbody>
            <?php
                $num = 1;
                $merged_details = array(); 
                foreach ($extra['details'] as $detail):
                    $currency = $detail['CurrencyCode'];
                    if (!isset($merged_details[$currency])) {
                        $merged_details[$currency] = array();
                    }
                    $merged_details[$currency][] = $detail;
                endforeach;

                foreach ($merged_details as $currency => $details):
                    foreach ($details as $detail):
                        $tripType=$detail['roundTrip']?$detail['roundTrip']:'-';
                        $fromDestination=$detail['fromDestinationCity']?$detail['fromDestinationCity']:'-';
                        $dob=$detail['DOB']?date('Y-m-d', strtotime($detail['DOB'])):'-';
                    ?>
                        <tr>
                            
                            <td><?php echo $num; ?></td>
                            <td><?php echo $detail['employeeName2']; ?></td>
                            <!-- <td><?php echo $detail['name']; ?></td> -->

                             <?php
                             if($detail['requestType']!='Trip Request'){
                                if($detail['name']){ ?>
                                    <td><?php echo $detail['name'] . " (" . $detail['relationName'] . ")"; ?></td>
                                <?php }else{ ?>
                                    <td>-</td>
                                <?php } ?>
                                <td><?php echo $dob; ?></td>
                            <?php } ?>
                            <!-- <td><?php echo $detail['countryName']; ?></td> -->
                            <td><?php echo $fromDestination; ?></td>
                            <td><?php echo $detail['cityName']; ?></td>
                            <td><?php echo $detail['startDate']; ?></td>
                            <td><?php echo $detail['endDate']; ?></td>
                            <td><?php echo $detail['CurrencyCode']; ?></td>
                            <td><?php echo $tripType; ?></td>
                            <!-- <td><?php echo $detail['amount']; ?></td> -->
                            <td><?php echo $detail['comments']; ?></td>
                        </tr>
                        <?php
                        $num++;
                    endforeach;

                    // $total = array_sum(array_column($details, 'amount'));
                    ?>
                    <!-- <tr>
                        <td colspan="7"></td> 
                        <td colspan="1"><b>Total <?php echo $currency; ?>:</b></td>
                        <td colspan="1"><b><?php echo $total; ?></b></td>
                    </tr> -->
                <?php endforeach;
            ?>
        </tbody>
  </table>
</div>

<?php if ($extra['detail']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:28%;"><strong><?php echo $this->lang->line('hrms_expanse_claim_electronically_approved_by');?><!--Electronically Approved By--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['detail']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('hrms_expanse_claim_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['detail']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Employee/load_travel_request_conformation'); ?>/<?php echo $extra['detail']['id'] ?>";
    $("#a_link").attr("href", a_link);
</script>




<script>
  
</script>