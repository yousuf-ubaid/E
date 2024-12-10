<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, true, $approval);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$company_reporting_currency=$this->common_data['company_data']['company_reporting_currency'];
$company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];

$html_img = '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAhvL-Nr_RGSeRVENLybjH_3eqB7h71xy5xw&usqp=CAU" width="20"/>';

?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo. $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4>Personal Action</h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created User Name</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['createdUserName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Created Emp Code</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ECode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Personal Action Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Document Created Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<hr>

<div class="row">
    <div class="col-sm-12 text-center"><h4>EMPLOYEE CHANGE OF STATUS </h4></div>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td><strong>Group</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Company']['currentText']) ? '' : $template_data['transfer_details']['Company']['currentText']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Segment</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Segment']['currentText']) ? '' : $template_data['transfer_details']['Segment']['currentText'] ; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Employee Name</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Name']['currentText']) ? '' : $template_data['transfer_details']['Name']['currentText']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Employee ID</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['EmpCODE']['currentText']) ? '': $template_data['transfer_details']['EmpCODE']['currentText']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date of Joining</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['EDOJ']['currentText']) ? '': $template_data['transfer_details']['EDOJ']['currentText']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Last Increment Amount&nbsp;&nbsp;&nbsp;&nbsp;(<?php echo $empCurrency['transactionCurrency'] ?? ''; ?>)</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo number_format(!empty($template_data['transfer_details']['Last Increment Amount']['currentText']) ? $template_data['transfer_details']['Last Increment Amount']['currentText'] : 0, $company_reporting_DecimalPlaces) ; ?></td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td><strong>Division</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Division']['currentText']) ? '' : $template_data['transfer_details']['Division']['currentText']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Sub Segment</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Sub Segment']['currentText']) ? '': $template_data['transfer_details']['Sub Segment']['currentText'] ; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Designation</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Designation']['currentText']) ? '': $template_data['transfer_details']['Designation']['currentText']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Grade</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo empty($template_data['transfer_details']['Grade']['currentText']) ? '': $template_data['transfer_details']['Grade']['currentText'] ; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Last Review Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo isset($template_data['transfer_details']['Last Review Date']['currentText']) ? $template_data['transfer_details']['Last Review Date']['currentText']: ''; ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="row" style="padding-left:15px;padding-right:15px;">
        <span class="col-sm-1">
            <p style="font-size:10px; word-wrap: break-word; overflow-wrap: break-word;">
                <strong>Remark&nbsp;&nbsp;:&nbsp;</strong>
            </p>
        </span>
        <span class="col-sm-9">
            <p style="font-size:10px; word-wrap: break-word; overflow-wrap: break-word;">
                <?php echo empty($remark) ? '': trim($remark); ?>
            </p>
        </span>
</div>

<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;vertical-align: top"><strong></strong></td>
            <td style="vertical-align: top"><strong></strong></td>
            <td style="width:70%;" colspan="4">
                <table>
                    <tr>
                        <td></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table id="add_new_grv_table" style="width: 100%" class="table table-bordered table-striped">
        <thead>
        <tr>
            <!-- <th class='theadtr' style="min-width: 5%"><strong>#</strong></th> -->
            <th class='theadtr' style="min-width: 5%"><strong>Description</strong></th>
            <?php if($actionType != 3){ ?>
                <th class='theadtr' style="min-width: 5%"><strong>Current</strong></th>
            <?php } ?>
            <?php if($actionType != 3){ ?>
                <th class='theadtr' style="min-width: 5%"><strong>New</strong></th>
            <?php }else{ ?>
                <th class='theadtr' style="min-width: 5%"><strong>Bonus&nbsp;&nbsp;:&nbsp;&nbsp;(<?php echo $empCurrency['transactionCurrency'] ?? ''; ?>)</strong></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php 

        $x = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) {
                if($extra['master']['actionType'] == 1){
                    if($val['fieldType'] != 'JD ATTACHED' && $val['fieldType'] != 'Last Increment Amount' && $val['fieldType'] != 'Last Review Date' && $val['fieldType'] != 'Name' && $val['fieldType'] != 'EmpCODE' && $val['fieldType'] != 'EDOJ'  && $val['fieldType'] != 'Justification' && $val['fieldType'] != 'New job description' && $val['fieldType'] != 'Reporting Structure' && $val['fieldType'] != 'KPI' && $val['fieldType'] != 'Performance Appraisal form')
                    {
                    echo '<tr>';
                        //column 1
                        echo '<td>' . $val['fieldType'] . '</td>';
                        //column 2
                        echo '<td>' . $val['currentText'] . '</td>';
                        //column 3
                        if(!empty($val['salaryCategoryID']) || $val['fieldType'] == 'Justification'){
                            if($val['fieldType'] != 'Justification'){
                                echo '<td class="text-right">' . number_format(floatval($val['NewValue']), $company_reporting_DecimalPlaces) . '</td>';
                            }else{
                                echo '<td class="text-left">' . $val['NewValue'] . '</td>';
                            }
                        }else{
                            echo '<td class="text-left">' . $val['NewValueText'] . '</td>';
                        }
                    
                    echo '</tr>';
                    }
                }
                else if($extra['master']['actionType'] == 2){
                    if($val['fieldType'] != 'Justification' && empty($val['monthlyDeclarationID']) && $val['fieldType'] != 'Last Increment Amount' && $val['fieldType'] != 'Segment' && $val['fieldType'] != 'Sub Segment' && $val['fieldType'] != 'JD ATTACHED' && $val['fieldType'] != 'Last Increment Amount' && $val['fieldType'] != 'Last Review Date' && $val['fieldType'] != 'Location' && $val['fieldType'] != 'Name' && $val['fieldType'] != 'EmpCODE' && $val['fieldType'] != 'EDOJ' && $val['fieldType'] != 'Division' && $val['fieldType'] != 'Reporting Manager' && $val['fieldType'] != 'Company')
                    {
                        echo '<tr>';
                        //column 1
                        echo '<td>' . $val['fieldType'] . '</td>';
                        //column 2
                        if(!empty($val['salaryCategoryID'])){
                            echo '<td class="text-right">' . number_format(empty($val['currentText']) ? 0 : $val['currentText'], $company_reporting_DecimalPlaces) . '</td>';
                        }
                        else{
                            if($val['fieldType'] != 'New job description' && $val['fieldType'] != 'Reporting Structure' && $val['fieldType'] != 'KPI' && $val['fieldType'] != 'Performance Appraisal form')
                            {
                                echo '<td>' . $val['currentText'] . '</td>';
                            }else if($val['fieldType'] != 'Justification'){
                                echo '<td>' . '' . '</td>';
                            }
                        }
                       
                        //column 3
                        if(!empty($val['salaryCategoryID'])){
                            echo '<td class="text-right">' . number_format(empty($val['NewValueText']) ? $val['currentText'] ?? 0 : $val['NewValueText'], $company_reporting_DecimalPlaces) . '</td>';
                        }
                        else{
                            if($val['fieldType'] != 'Justification' && $val['fieldType'] != 'Designation' && $val['fieldType'] != 'Grade' && $val['fieldType'] != 'Status' && $val['fieldType'] != 'Leave Group'){
                                if($val['NewValue'] == 1 && $val['fieldType'] == 'New job description'){
                                    echo  '<td class="text-right">'. $html_img .'</td>';
                                }else
                                if($val['NewValue'] == 1 && $val['fieldType'] == 'Reporting Structure'){
                                    echo  '<td class="text-right">'. $html_img .'</td>';
                                }else
                                if($val['NewValue'] == 1 && $val['fieldType'] == 'KPI'){
                                    echo  '<td class="text-right">'. $html_img .'</td>';
                                }else
                                if($val['NewValue'] == 1 && $val['fieldType'] == 'Performance Appraisal form'){
                                    echo  '<td class="text-right">'. $html_img .'</td>';
                                }else{
                                    echo  '<td class="text-right">-&nbsp;&nbsp;&nbsp;</td>';
                                }
                            }else{
                                    echo '<td>' . $val['NewValueText'] . '</td>';
                            }
                        }
                        
                        echo '</tr>';
                    }
                }else if($extra['master']['actionType'] == 3){
                    echo '<tr>';
                    if(!empty($val['monthlyDeclarationID'])){
                    //column 1
                    echo '<td>' . $val['fieldType'] . '</td>';
                    //column 2
                    echo '<td class="text-right">' . number_format(empty($val['NewValueText']) ? 0 : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) . '</td>';
                    }
                }
                $x++;
            }
        } else {
            $norec=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="4" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<br>

<?php if($extra['master']['actionType'] != 1 ){ ?>
<hr>
<div class="row" style="padding-left:15px;padding-right:15px;">
        <span class="col-sm-2">
            <p style="font-size:10px; word-wrap: break-word; overflow-wrap: break-word;">
                <strong>Justification for change in status&nbsp;&nbsp;:&nbsp;</strong>
            </p>
        </span>
        <span class="col-sm-8">
                <p style="font-size:10px; word-wrap: break-word; overflow-wrap: break-word;">
                    <?php if($extra['master']['actionType'] == 2 ){ ?>
                                <?php echo empty($justification) ? '': trim($justification); ?>
                            <?php }else{ ?>
                                <?php echo empty($justification) ? '': trim($justification); ?>
                    <?php } ?>
                    </p>
        </span>
</div>
<hr>
<br>
<br>
<?php } ?>
<div class="table-responsive">
    <br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
            </tr>
        <?php if($extra['master']['confirmedYN']==1){ ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedByName']; ?></td>
            </tr>
        <?php } ?>
        <?php if($extra['master']['approvedYN'] == 1){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;">
                    <?php
                    // Debug: Check if $approval_users_data is set and not empty
                    //print_r('helll');exit;
                    if(isset($approval_users_data) && !empty($approval_users_data)) {
                        foreach($approval_users_data as $userval){
                            echo $userval['Ename2'] . " on " . $userval['approvedDate'] . "&nbsp;" . ' | ';                            
                        }
                    } else {
                        // Debug: Output message if $approval_users_data is empty
                        echo "-";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<br>
<br>
<?php if($extra['master']['approvedYN']){ ?>
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
                           <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
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
    a_link = "<?php echo site_url('Employee/load_personal_action_conformation'); ?>/<?php echo $extra['master']['id'] ?>";

    $("#a_link").attr("href", a_link);

</script>

