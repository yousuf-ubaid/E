<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, true, $approval);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$company_reporting_currency = $this->common_data['company_data']['company_reporting_currency'];
$company_reporting_DecimalPlaces = $this->common_data['company_data']['company_reporting_decimal'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>PDF Print View</title> -->
</head>

<body style="font-family: Arial, sans-serif; font-size: 12px;color: #000;margin: 0;padding: 20px;">

    <div class="table-responsive">
        <table style="width: 100%;border-collapse: collapse;margin-bottom: 20px;">
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
                                <h3 style="margin: 5px 0;">
                                    <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
                                </h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4 style="margin: 5px 0;">Personal Action</h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created User Name</strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['createdUserName']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Document Emp Code</strong></td>
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

    <hr>

    <!-- Title Section -->
    <div class="section-title" style="font-size: 18px;text-transform:uppercase;margin: 20px 0;text-align: center;">
        PERSONAL ACTION / PAYROLL AUTHORIZATION FORM
    </div>

    <br>

    <!-- Company Details -->
    <table style="width: 100%;border-collapse: collapse;margin-bottom: 10px;">
        <tr>
            <th style="text-align: left;">COMPANY NAME&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Company']['currentText'] ? $template_data['transfer_details']['Company']['currentText']:''; ?></th>
            <th style="text-align: right;">DIVISION&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Division']['currentText'] ? $template_data['transfer_details']['Division']['currentText'] : ''; ?></th>
        </tr>
    </table>

    <hr>

    <!-- Employee Information -->
    <table style="width: 100%;border-collapse: collapse;margin-bottom: 5px;">
        <tr>
            <th style="text-align:left; padding:5px 5px 5px 5px; font-size:12px;">ACTION TYPE&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Action Type']['currentText'] ? $template_data['transfer_details']['Action Type']['currentText']: '-'; ?></th>
        </tr>
        <tr>
            <th style="text-align:left; padding:5px 5px 5px 5px; font-size:12px;">EMP NO&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['EmpCODE']['currentText'] ?$template_data['transfer_details']['EmpCODE']['currentText']: '-'; ?></th>
            <th style="text-align:left; padding:5px 5px 5px 5px; font-size:12px;">NAME&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Name']['currentText'] ?$template_data['transfer_details']['Name']['currentText']: '-'; ?></th>
        </tr>
        <tr>
        <th style="text-align: left;padding:5px 5px 5px 5px; font-size:12px;">DATE OF JOINED&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['EDOJ']['currentText'] ?$template_data['transfer_details']['EDOJ']['currentText']: '-'; ?></th>
            <th style="text-align:left; padding:5px 5px 5px 5px; font-size:12px;">LAST PROMOTION DATE&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Last Promotion Date']['currentText'] ?$template_data['transfer_details']['Last Promotion Date']['currentText']: '-'; ?></th>
        </tr>
        <tr>
            <th style="text-align:left; padding:5px 5px 5px 5px; font-size:12px;">LAST INCREMENT DATE&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Last Review Date']['currentText'] ?$template_data['transfer_details']['Last Review Date']['currentText']: '-'; ?></th>
            <th style="text-align:left; padding:5px 5px 5px 5px; font-size:12px;">NEXT PERIODICAL MEDICAL DATE&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $template_data['transfer_details']['Periodical Medical Date']['currentText'] ?$template_data['transfer_details']['Periodical Medical Date']['currentText']: '-'; ?></th>
        </tr>
    </table>

    <hr>

    <!-- Effective Date 1-->
    <table style="width: 100%;border-collapse: collapse;margin-bottom: 5px;">
        <tr>
            <th style="text-align: left;">Effective Date&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $template_data['transfer_details']['effectiveDate']['NewValueText'] ?$template_data['transfer_details']['effectiveDate']['NewValueText'] : ''; ?></th>
        </tr>
    </table>

    <!-- Transfer Details -->
    <table style="width: 100%;border: 1px solid #f9f8f8;border-collapse: collapse;margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid #f9f8f8;text-align: center;">DESCRIPTION</th>
                <th style="border: 1px solid #f9f8f8;text-align: center;">FROM</th>
                <th style="border: 1px solid #f9f8f8;text-align: center;">TO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">DIVISION</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Division']['currentText'] ?$template_data['transfer_details']['Division']['currentText']: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Division']['NewValueText'] ?$template_data['transfer_details']['Division']['NewValueText']: '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">DESIGNATION</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Designation']['currentText'] ?$template_data['transfer_details']['Designation']['currentText']: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Designation']['NewValueText'] ?$template_data['transfer_details']['Designation']['NewValueText']: '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">GRADE</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Grade']['currentText'] ?$template_data['transfer_details']['Grade']['currentText']: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Grade']['NewValueText'] ?$template_data['transfer_details']['Grade']['NewValueText'] : '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">BASIC SALARY (<?php echo isset($template_data['transfer_details']['currency']['currentText']) ? $template_data['transfer_details']['currency']['currentText']: ''; ?>)</td> <!--<?php //echo $empCurrency['transactionCurrency']; ?>-->
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo number_format($template_data['transfer_details']['basicSalary']['currentText'], $company_reporting_DecimalPlaces) ?: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo number_format($template_data['transfer_details']['basicSalary']['NewValue'], $company_reporting_DecimalPlaces) ?: '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">CURRENCY</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['currency']['currentText'] ?: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['currency']['NewValueText'] ?: '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">WORK/ LEAVE SCHEDULE</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Leave Group']['currentText'] ?$template_data['transfer_details']['Leave Group']['currentText']: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Leave Group']['NewValueText'] ?$template_data['transfer_details']['Leave Group']['NewValueText']: '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">STATUS</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Status']['currentText'] ?$template_data['transfer_details']['Status']['currentText']: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['Status']['NewValueText'] ?$template_data['transfer_details']['Status']['NewValueText']: '-'; ?></td>
            </tr>
            <tr>
                <td style="border: 1px solid #f9f8f8;text-align: left;">OVERTIME ENTITLEMENT</td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['overtime_entitlment']['currentText'] ?$template_data['transfer_details']['overtime_entitlment']['currentText']: '-'; ?></td>
                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $template_data['transfer_details']['overtime_entitlment']['NewValueText'] ?$template_data['transfer_details']['overtime_entitlment']['NewValueText']: '-'; ?></td>
            </tr>
        </tbody>
    </table>

    <div>
        <table style="width: 100%;border-collapse: collapse;margin-bottom: 20px;">
            <tbody>
                <tr>
                    <td style="width: 50%;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="font-size: 12px;text-align: left;"><strong>Remark&nbsp;&nbsp;:&nbsp;</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 100%;">
                                    <p style="width: 100%; word-break: break-word;font-size: 12px;">
                                        <?php
                                        $remark = $template_data['transfer_details']['remark1']['NewValueText'] ? $template_data['transfer_details']['remark1']['NewValueText']: '';
                                        if (!empty($remark)) {
                                            // Break the string into an array of lines, each line has maximum 120 characters
                                            $lines = str_split(trim($remark), 100);
                                            foreach ($lines as $line) {
                                                echo htmlspecialchars($line) . "<br/>";
                                            }
                                        }else{
                                            echo '';
                                        }
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr>

    <!-- Allowance Section -->
    <table style="width: 100%;border-collapse: collapse;margin-bottom: 5px;">
        <tr>
            <th style="text-align: left;">
                Allowance&nbsp;&nbsp;:&nbsp;&nbsp;
                <?php echo ($template_data['transfer_details']['allowance']['NewValue'] == 'true') ? 'Yes' : 'No'; ?>
            </th>
            
        </tr>
    </table>

    <!-- Additional Salary Details -->
    <table style="width: 100%;border-collapse: collapse;margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid #f9f8f8;padding: 8px;text-align: center;">DESCRIPTION</th>
                <!-- <th style="border: 1px solid #f9f8f8;padding: 8px;text-align: center;">CURRENCY</th> -->
                <th style="border: 1px solid #f9f8f8;padding: 8px;text-align: center;">FROM&nbsp;&nbsp;(<?php echo isset($template_data['transfer_details']['currency']['currentText']) ? $template_data['transfer_details']['currency']['currentText']: ''; ?>)</th>
                <th style="border: 1px solid #f9f8f8;padding: 8px;text-align: center;">TO&nbsp;&nbsp;(<?php echo $template_data['transfer_details']['currency']['NewValueText'] ? $template_data['transfer_details']['currency']['NewValueText']: ''; ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php  
            $x=1;
            foreach ($details as $val) {
                                                            
                ?>
                <tr>
                    <?php if(!empty($val['salaryCategoryID']) && $val['fieldType'] != 'Basic Salary' && $val['fieldType'] != 'Designation' && $val['fieldType'] != 'Grade' && $val['fieldType'] != 'Leave Group' && $val['fieldType'] != 'Status' && $val['fieldType'] != 'JD ATTACHED' && $val['fieldType'] != 'Last Increment Amount' && $val['fieldType'] != 'Last Review Date' && $val['fieldType'] != 'DEPARTMENT' && $val['fieldType'] != 'Sub Segment' && $val['fieldType'] != 'Segment' && $val['fieldType'] != 'Location' && $val['fieldType'] != 'Name' && $val['fieldType'] != 'EmpCODE' && $val['fieldType'] != 'EDOJ' && $val['fieldType'] != 'Division' && $val['fieldType'] != 'Reporting Manager' && $val['fieldType'] != 'Company' && $val['fieldType'] != 'Justification' && $val['fieldType'] != 'New job description' && $val['fieldType'] != 'Reporting Structure' && $val['fieldType'] != 'KPI' && $val['fieldType'] != 'Performance Appraisal form'){ ?>
                    <!--1st column -->
                    <?php if($actionType != 3){ ?>
                        <?php if(!empty($val['salaryCategoryID'])){ ?>
                            <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $val['fieldType']; ?></td>
                            <?php } ?>
                        <?php }else{ ?>
                            <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $val['fieldType']; ?></td>
                        <?php } ?>
                    <?php }else{ 
                        if(!empty($val['monthlyDeclarationID'])){ ?>
                            <td style="border: 1px solid #f9f8f8;text-align: left;"><?php echo $val['fieldType']; ?></td>
                        <?php } ?>
                    <?php } ?>
                                                                    
                                                                

                    <?php if($actionType !=3 ){
                        if(!empty($val['salaryCategoryID'])){ ?> <!--2nd column -->
                            <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo  number_format(empty($val['currentText']) ? 0 : floatval($val['currentText']), $company_reporting_DecimalPlaces) ;?></td>
                            <?php } ?>
                        <?php }else{ ?>
                            <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo !empty($val['currentText']) ? $val['currentText'] : '-'; ?></td> <!--2nd column -->
                        <?php } 
                    }?>

                    <!-- 3rd column -->
                    <?php if($actionType != 3){
                        if(!empty($val['salaryCategoryID'])){ ?>
                            <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo  number_format(empty($val['NewValueText']) ? floatval($val['currentText']) : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) ;?></td>
                            <?php } ?>
                        <?php }
                        else{
                            if($val['fieldType'] == 'Grade'){ ?>
                                <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo empty($template_data['transfer_details']['Grade']['NewValueText']) ? $template_data['transfer_details']['Grade']['currentText'] : $template_data['transfer_details']['Grade']['NewValueText'] ?></td>
                            <?php }else if($val['fieldType'] == 'Status'){ ?> 
                                <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo empty($template_data['transfer_details']['Status']['NewValueText']) ? $template_data['transfer_details']['Status']['currentText'] : $template_data['transfer_details']['Status']['NewValueText'] ?></td>
                            <?php }
                             else if($val['fieldType'] == 'Designation'){?>
                                <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo empty($template_data['transfer_details']['Designation']['NewValueText']) ? $template_data['transfer_details']['Designation']['currentText'] : $template_data['transfer_details']['Designation']['NewValueText'] ?></td>
                            <?php }
                            else if($val['fieldType'] == 'Leave Group'){ ?>
                                <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo empty($template_data['transfer_details']['Leave GroupP']['NewValueText']) ? $template_data['transfer_details']['Leave Group']['currentText'] : $template_data['transfer_details']['Leave Group']['NewValueText'] ?></td>
                            <?php }
                        }
                    }else{?>
                        <?php if(!empty($val['monthlyDeclarationID'])){ ?>
                            <td style="border: 1px solid #f9f8f8;text-align: right;"><?php echo  number_format(empty($val['NewValueText']) ? 0 : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) ;?></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <?php $x++;
                }
            } ?>
        </tbody>
    </table>

    <div>
        <table style="width: 100%;border-collapse: collapse;margin-bottom: 20px;">
            <tbody>
                <tr>
                    <td style="width: 50%;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="font-size: 12px;text-align: left;"><strong>Remark&nbsp;&nbsp;:&nbsp;</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 100%;">
                                    <p style="width: 100%; word-break: break-word;font-size: 12px;">
                                        <?php
                                        $remark = $template_data['transfer_details']['remark2']['NewValueText'] ? $template_data['transfer_details']['remark2']['NewValueText']: '';
                                        if (!empty($remark)) {
                                            // Break the string into an array of lines, each line has maximum 120 characters
                                            $lines = str_split(trim($remark), 100);
                                            foreach ($lines as $line) {
                                                echo htmlspecialchars($line) . "<br/>";
                                            }
                                        }else{
                                            echo '';
                                        }
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr>
    <div class="col-sm-12 table-responsive">
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
        <div class="col-sm-12 table-responsive">
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

</body>
</html>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Employee/load_personal_action_conformation_mse'); ?>/<?php echo $extra['master']['id'] ?>";

    $("#a_link").attr("href", a_link);

</script>
