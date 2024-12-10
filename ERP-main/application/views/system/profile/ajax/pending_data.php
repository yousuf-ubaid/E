<?php
//echo '<pre>'; print_r($pendingData); echo '</pre>';

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$title = $this->lang->line('profile_my_profile');

$passwordComplexityExist  = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();

$empData = $empArray['employees'];
$isNeedApproval = getPolicyValues('EPD', 'All');
 
?>
<style>
    #profileInfoTable tr td:first-child {
        color: #095db3;
    }

    #profileInfoTable tr td:nth-child(2) {
        font-weight: bold;
    }

    #newFamilyTab{
        height: 310px;
        overflow-y: scroll;
    }
</style>


<div class="row">
    <div class="col-md-12" style="margin-bottom: 10px">
        <strong><?php echo $empData['ECode'] .' - '.$empData['Ename2']; ?></strong>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default animated zoomIn">
            <div class="panel-body" style="padding: 0px;">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#changesTab" data-toggle="tab"> <i class="fa fa-user" aria-hidden="true"></i>
                                <?php echo $this->lang->line('common_data_changes'); ?><!--Data changes -->
                            </a>
                        </li>
                        <li class="">
                            <a class="" href="#newFamilyTab" data-toggle="tab">
                                <i class="fa fa-users" aria-hidden="true"></i> <?php echo $this->lang->line('common_family_changes'); ?><!--Family data changes-->
                            </a>
                        </li>
                        <li class="">
                            <a class="" href="#newReportingManager" data-toggle="tab">
                                <i class="fa fa-users" aria-hidden="true"></i> <?php echo $this->lang->line('common_reporting_changes'); ?><!--Reporting Manager data changes-->
                            </a>
                        </li>
                        <li class="">
                            <a class="" href="#newdepartment" data-toggle="tab">
                                <i class="fa fa-users" aria-hidden="true"></i> <?php echo $this->lang->line('common_department_changes'); ?><!--Department changes-->
                            </a>
                        </li>
                        <li class="">
                            <a class="" href="#newbank" data-toggle="tab">
                                <i class="fa fa-users" aria-hidden="true"></i> <?php echo $this->lang->line('common_bank_changes'); ?><!--Bank Detail changes-->
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="changesTab">
                            <?php
                            if(empty($pendingData)){
                                echo '<div style="margin: 10px; font-weight: bold;">No record</div>';
                            }
                            ?>

                            <table class="table table-striped" id="profileInfoTable" style="background-color: #ffffff;width: 100%">
                                <tbody>
                                <?php
                                $isPending = search_pendingDataApproval($pendingData, 'EmpTitleId', 1);
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td style="width:30%">
                                            <strong><?php echo $this->lang->line('common_title'); ?><!--Title: --></strong>
                                        </td>
                                        <?php echo $isPending; ?>
                                    </tr>
                                    <?php
                                }

                                if($is_tibian != 'Y') {
                                    $initial = search_pendingData($pendingData, 'initial');
                                    $eName4 = search_pendingData($pendingData, 'Ename4');

                                    if ($initial !== null || $eName4 !== null) { ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $this->lang->line('emp_name_with_initials'); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $initial = ($initial !== null) ? $initial : get_DataEmployee($empID, 'initial');
                                                $initial_changed = ($initial !== null) ? 1 : 0;
                                                $eName4 = ($eName4 !== null) ? $eName4 : get_DataEmployee($empID, 'Ename4');
                                                $eName4_changed = ($eName4 !== null) ? 1 : 0;

                                                echo $initial . ' ' . $eName4;
                                                echo '<input type="hidden" name="initial" value="' . $initial . '" />';
                                                echo '<input type="hidden" name="initial_changed" value="' . $initial_changed . '" />';
                                                echo '<input type="hidden" name="Ename4" value="' . $eName4 . '" />';
                                                echo '<input type="hidden" name="Ename4_changed" value="' . $eName4_changed . '" />';
                                                ?>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="upDateNameWithInitial" value="1"/>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    
                                    $isPending = search_pendingDataApproval($pendingData, 'Ename1');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('profile_full_name'); ?><!--Full Name: --></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'Ename3');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('profile_surname'); ?><!--Surname:--> </strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                else{
                                    $isPending = search_pendingDataApproval($pendingData, 'Ename1');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_emp_first_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'Enameother1');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_arabic').' '.$this->lang->line('common_emp_first_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }


                                    $isPending = search_pendingDataApproval($pendingData, 'empSecondName');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_emp_second_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'empSecondNameOther');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_arabic').' '.$this->lang->line('common_emp_second_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'Ename3');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_emp_third_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'Enameother3');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_arabic').' '.$this->lang->line('common_emp_third_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'Ename4');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_emp_fourth_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'Enameother4');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_arabic').' '.$this->lang->line('common_emp_fourth_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'EFamilyName');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_emp_family_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingDataApproval($pendingData, 'EFamilyNameOther');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_arabic').' '.$this->lang->line('common_emp_family_name'); ?></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EpTelephone');
                                if( !empty($isPending) ){ ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('common_telephone'); ?><!--Telephone :--></strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EpAddress1');
                                if( !empty($isPending) ){
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('emp_address_line1'); ?><!--Address Line1 :--></strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EpAddress2');
                                if( !empty($isPending) ){
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('emp_address_line2'); ?><!--Address Line2 :--></strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EpAddress3');
                                if( !empty($isPending) ){
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('emp_address_line3'); ?><!--Address Line3 :--></strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EcMobile');
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('common_mobile'); ?><!--Address :--></strong></td>
                                        <?php echo $isPending; ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EDOB', 2);
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('profile_date_of_birth'); ?><!--Date of Birth :--></strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'Nid', 1);
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td><strong><?php echo $this->lang->line('common_nationality'); ?><!--Nationality :--></strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'MaritialStatus', 1);
                                if( !empty($isPending) ) {
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $this->lang->line('profile_marital_status'); ?> <!--Marital Status :--></strong>
                                        </td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'Rid', 1);
                                if( !empty($isPending) ) {
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $this->lang->line('common_religion'); ?><!--Religion :--></strong>
                                        </td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'Gender', 1);
                                if( !empty($isPending) ) {
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $this->lang->line('common_gender'); ?><!--Gender :--></strong>
                                        </td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'BloodGroup', 1);
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $this->lang->line('profile_blood_group'); ?><!--Blood Group: --></strong>
                                        </td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'personalEmail');
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td><strong>Personal Email</strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'EVisaExpiryDate', 2);
                                if( !empty($isPending) ) {
                                    ?>
                                    <tr>
                                        <td><strong>Visa Expiry Date</strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                    <?php
                                }

                                $isPending = search_pendingDataApproval($pendingData, 'manPowerNo');
                                if( !empty($isPending) ) { ?>
                                    <tr>
                                        <td><strong>Man Power No</strong></td>
                                        <?php echo $isPending ?>
                                    </tr>
                                <?php }  ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="newFamilyTab">
                            <?php

                            foreach ($familyData_changes as $FCkey=>$familyChanges) {

                                $changeName = $familyData_changes[$FCkey][0]['changeName'];
                                $pendingData = $familyChanges;
                                ?>

                                <div class="col-sm-12" style="background: #e8e8e8; border-bottom: 1px solid #ccc;">
                                    <div class="col-sm-10" style="font-size: 14px; font-weight: bold;"> <?php echo $changeName; ?> </div>
                                    <div class="col-sm-2">
                                        <!--<input type="checkbox" name="addFamilyData[]" value="" />-->
                                    </div>
                                </div>
                                <table class="table table-striped" id="profileInfoTable" style="background-color: #ffffff;width: 100%">
                                    <tbody>
                                    <?php
                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'relationship', 1);
                                    if( !empty($isPending) ){  ?>
                                        <tr>
                                            <td><strong>Relationship</strong></td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'name');
                                    if( !empty($isPending) ){  ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('profile_full_name'); ?><!--Full Name: --></strong></td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'nationality', 1);
                                    if( !empty($isPending) ){ ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('common_nationality'); ?><!--Nationality :--></strong></td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'DOB', 2);
                                    if( !empty($isPending) ){ ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('profile_date_of_birth'); ?><!--Date of Birth :--></strong></td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'gender', 1);
                                    if( !empty($isPending) ){
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('common_gender'); ?><!--Gender :--></strong></td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'nationalCode');
                                    if( !empty($isPending) ) { ?>
                                        <tr>
                                            <td><strong>National No.</strong></td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'idNO');
                                    if( !empty($isPending) ) { ?>
                                        <tr>
                                            <td><strong>ID No. </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'passportNo');
                                    if( !empty($isPending) ) {
                                        ?>
                                        <tr>
                                            <td>
                                                <strong>Passport No</strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'passportExpiredate', 2);
                                    if( !empty($isPending) ) {
                                        ?>
                                        <tr>
                                            <td>
                                                <strong>Passport Expiry Date</strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'VisaNo');
                                    if( !empty($isPending) ) {
                                        ?>
                                        <tr>
                                            <td>
                                                <strong>Visa No</strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    }

                                    $isPending = search_pendingFamilyDataApproval($FCkey, $pendingData, 'VisaexpireDate', 2);
                                    if( !empty($isPending) ) { ?>
                                        <tr>
                                            <td>
                                                <strong>Visa Expiry Date </strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                    <?php
                                    }

                                    ?>
                                    </tbody>
                                </table>

                            <?php
                            }
                            foreach ($familyData_new as $familyInfo) {
                                if( $isNeedApproval == 1){
                                    $pendingData = get_pendingFamilyApprovalData($familyInfo['empfamilydetailsID']);
                                }
                                ?>

                                <div class="container-fluid familyMasterContainer" id="" style="/*padding: 0px*/">
                                    <div class="col-sm-12" style="background: #e8e8e8; border-bottom: 1px solid #ccc;">
                                        <div class="col-sm-10" style="font-size: 14px; font-weight: bold;"> New record </div>
                                        <div class="col-sm-2">
                                            <input type="checkbox" name="addFamilyData[]" value="<?php echo $familyInfo['empfamilydetailsID']; ?>" class="approveNewChk"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="familyContainer">

                                                <table class="table table-condensed">
                                                    <tr>
                                                        <td id="_relationship">Relationship :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'relationship',1) : null;

                                                            if( !empty($isPending) ){
                                                                $titleID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_relationship'); </script>";
                                                            }
                                                            else{
                                                                $titleID = (empty($familyInfo['relationship'])) ? '' : $familyInfo['relationship'];
                                                                $description = (empty($familyInfo['relationshipDesc'])) ? '' : $familyInfo['relationshipDesc'];
                                                            }
                                                            ?>
                                                            <a href="#"  data-type="select2"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="relationship"
                                                               data-title="Relationship Status"
                                                               class="relationshipDrop"
                                                               data-placement="right"
                                                               data-value="<?php echo $titleID ; ?>">
                                                                <?php
                                                                echo $description;
                                                                ?>
                                                            </a>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_name">Name :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'name') : null;

                                                            if( !empty($isPending) ){
                                                                $name=$isPending;
                                                                echo "<script> colorLabel('_name'); </script>";
                                                            }
                                                            else{
                                                                $name = (empty($familyInfo['name'])) ? '' : $familyInfo['name'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text"
                                                               data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="name"
                                                               data-title="Name"
                                                               class="xeditable"
                                                               data-value="<?php echo $name; ?>">
                                                                <?php echo $name; ?>
                                                            </a>
                                                        </td>


                                                    </tr>

                                                    <tr>
                                                        <td id="_nationality">Nationality :</td>
                                                        <td>

                                                            <?php
                                                            $filename = '/gs_sme/images/flags/' . trim($familyInfo['countryName'] ?? '') . '.png';
                                                            if (!empty($familyInfo['countryName'])) {
                                                                if (url_exists($filename)) {
                                                                    echo '<img src="' . $filename . '" />';
                                                                }
                                                            }

                                                            ?>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'nationality',1) : null;

                                                            if( !empty($isPending) ){
                                                                $titleID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_nationality'); </script>";
                                                            }
                                                            else{
                                                                $titleID = (empty($familyInfo['nationality'])) ? '' : $familyInfo['nationality'];
                                                                $description = (empty($familyInfo['countryName'])) ? '' : $familyInfo['countryName'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="nationality"
                                                               data-title="Nationality"
                                                               class="countryDrop"
                                                               data-value="<?php echo $titleID; ?>">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td id="_DOB">Date of Birth :</td>
                                                        <td>
                                                            <?php
                                                            $dob = '0000-00-00';
                                                            $dob2 = '0000-00-00';

                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'DOB') : null;
                                                            if( !empty($isPending) ){
                                                                $dob = format_date_dob($isPending);
                                                                $dob2 = $isPending;
                                                                echo "<script> colorLabel('_DOB'); </script>";
                                                            }
                                                            else{
                                                                $dob = (empty($familyInfo['DOB'])) ? '' : $familyInfo['DOB'];
                                                                $dob = format_date_dob($dob);
                                                                $dob2 = $familyInfo['DOB'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="combodate"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="DOB"
                                                               data-title="Date of Birth"
                                                               class="xeditableDate"
                                                               data-value="<?php echo $dob2 ?>">
                                                                <?php echo $dob ?>
                                                            </a>
                                                            &nbsp;
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td id="_gender">Gender :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'gender',1) : null;

                                                            if( !empty($isPending) ){
                                                                $titleID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_gender'); </script>";
                                                            }
                                                            else{
                                                                $titleID = (empty($familyInfo['gender'])) ? '' : $familyInfo['gender'];
                                                                $description = (empty($familyInfo['genderDesc'])) ? '' : $familyInfo['genderDesc'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="gender"
                                                               data-title="Gender"
                                                               class="genderDrop"
                                                               data-value="<?php echo $titleID; ?>">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_nationalCode">National No. :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'nationalCode') : null;

                                                            if( !empty($isPending) ){
                                                                $nationalCode=$isPending;
                                                                echo "<script> colorLabel('_nationalCode'); </script>";
                                                            }
                                                            else{
                                                                $nationalCode = (empty($familyInfo['nationalCode'])) ? '' : $familyInfo['nationalCode'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="nationalCode"
                                                               data-title="National No.  "
                                                               class="xeditable"
                                                               data-value="<?php echo $nationalCode; ?>">
                                                                <?php
                                                                echo $nationalCode;
                                                                ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_idNO">ID No. :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'idNO') : null;

                                                            if( !empty($isPending) ){
                                                                $idNO=$isPending;
                                                                echo "<script> colorLabel('_idNO'); </script>";
                                                            }
                                                            else{
                                                                $idNO = (empty($familyInfo['idNO'])) ? '' : $familyInfo['idNO'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="idNO"
                                                               data-title="ID No.  "
                                                               class="xeditable"
                                                               data-value="<?php echo $idNO; ?>">
                                                                <?php echo $idNO; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_passportNo">Passport No :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'passportNo') : null;

                                                            if( !empty($isPending) ){
                                                                $passportNo=$isPending;
                                                                echo "<script> colorLabel('_passportNo'); </script>";
                                                            }
                                                            else{
                                                                $passportNo = (empty($familyInfo['passportNo'])) ? '' : $familyInfo['passportNo'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="passportNo"
                                                               data-title="Passport No"
                                                               class="xeditable"
                                                               data-value="<?php echo $passportNo; ?>">
                                                                <?php echo $passportNo; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Passport Expiry Date
                                                            :
                                                        </td>
                                                        <td id="_passportExpiredate">
                                                            <?php
                                                            $pob = '0000-00-00';
                                                            $pob2 = '0000-00-00';

                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'passportExpiredate') : null;
                                                            if( !empty($isPending) ){
                                                                $pob = format_date_dob($isPending);
                                                                $pob2 = $isPending;
                                                                echo "<script> colorLabel('_passportExpiredate'); </script>";
                                                            }
                                                            else{
                                                                $pob = (empty($familyInfo['passportExpiredate'])) ? '' : $familyInfo['passportExpiredate'];
                                                                $pob = format_date_dob($pob);
                                                                $pob2 = $familyInfo['passportExpiredate'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="combodate"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="passportExpiredate"
                                                               data-title="Passport Expire Date"
                                                               class="xeditableDate"
                                                               data-value="<?php echo $pob2 ?>">
                                                                <?php echo $pob ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_VisaNo">Visa No :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'VisaNo') : null;

                                                            if( !empty($isPending) ){
                                                                $VisaNo=$isPending;
                                                                echo "<script> colorLabel('_VisaNo'); </script>";
                                                            }
                                                            else{
                                                                $VisaNo = (empty($familyInfo['VisaNo'])) ? '' : $familyInfo['VisaNo'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="VisaNo"
                                                               data-title="Visa No"
                                                               class="xeditable"
                                                               data-value="<?php echo $VisaNo; ?>">
                                                                <?php echo $VisaNo; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_VisaexpireDate">Visa Expiry Date
                                                            :
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $vob = '0000-00-00';
                                                            $vob2 = '0000-00-00';

                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'VisaexpireDate') : null;
                                                            if( !empty($isPending) ){
                                                                $vob = format_date_dob($isPending);
                                                                $vob2 = $isPending;
                                                                echo "<script> colorLabel('_VisaexpireDate'); </script>";
                                                            }
                                                            else{
                                                                $vob = (empty($familyInfo['VisaexpireDate'])) ? '' : $familyInfo['VisaexpireDate'];
                                                                $vob = format_date_dob($vob);
                                                                $vob2 = $familyInfo['VisaexpireDate'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="combodate"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="VisaexpireDate"
                                                               data-title="Visa Expire Date"
                                                               class="xeditableDate"
                                                               data-value="<?php echo $vob2 ?>">
                                                                <?php echo $vob ?>
                                                            </a>
                                                            &nbsp;
                                                            &nbsp;
                                                            &nbsp;
                                                        </td>
                                                    </tr>


                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="familyContainer">
                                                <div class="show-image" style="text-align: center;">
                                                    <?php if (!empty($familyInfo['image'])) { ?>
                                                        <img class="familyImgSize" style="height: 185px;"
                                                             src="<?php echo document_uploads_family_url() . $familyInfo['image'] ?>"
                                                             alt="image">
                                                    <?php } else { ?>
                                                        <img class="familyImgSize" style="height: 185px;"
                                                             src="<?php echo base_url('../gs_sme/images/no_image.jpg'); ?>"
                                                             alt="image">
                                                    <?php } ?>
                                                </div>
                                                <table class="table table-condensed">
                                                    <tr>
                                                        <td id="_insuranceCategory">
                                                            Insurance Category :
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'insuranceCategory',1) : null;

                                                            if( !empty($isPending) ){
                                                                $titleID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_insuranceCategory'); </script>";
                                                            }
                                                            else{
                                                                $titleID = (empty($familyInfo['insuranceCategory'])) ? '' : $familyInfo['insuranceCategory'];
                                                                $description = (empty($familyInfo['insuranDesc'])) ? '' : $familyInfo['insuranDesc'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-placement="left"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="insuranceCategory"
                                                               data-title="Insurance Category "
                                                               class="insuranceCategoryDrop"
                                                               data-value="<?php echo $titleID; ?>">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_insuranceCode">Insurance Code :</td>
                                                        <td>
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'insuranceCode') : null;

                                                            if( !empty($isPending) ){
                                                                $insuranceCode=$isPending;
                                                                echo "<script> colorLabel('_insuranceCode'); </script>";
                                                            }
                                                            else{
                                                                $insuranceCode = (empty($familyInfo['insuranceCode'])) ? '' : $familyInfo['insuranceCode'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text"
                                                               data-placement="left"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="insuranceCode"
                                                               data-title="Insurance Code  "
                                                               class="xeditable"
                                                               data-value="<?php echo $insuranceCode; ?>">
                                                                <?php echo $insuranceCode ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="_coverFrom">Cover From :</td>
                                                        <td>
                                                            <?php
                                                            $cob = '0000-00-00';
                                                            $cob2 = '0000-00-00';

                                                            $isPending = ($isNeedApproval == 1)? search_pendingData($pendingData, 'coverFrom') : null;
                                                            if( !empty($isPending) ){
                                                                $cob = format_date_dob($isPending);
                                                                $cob2 = $isPending;
                                                                echo "<script> colorLabel('_coverFrom'); </script>";
                                                            }
                                                            else{
                                                                $cob = (empty($familyInfo['coverFrom'])) ? '' : $familyInfo['coverFrom'];
                                                                $cob = format_date_dob($cob);
                                                                $cob2 = $familyInfo['coverFrom'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="combodate"
                                                               data-placement="left"
                                                               data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                                               data-name="coverFrom"
                                                               data-title="Cover From"
                                                               class="xeditableDate"
                                                               data-value="<?php echo $cob2 ?>">
                                                                <?php echo $cob ?>
                                                            </a>
                                                        </td>

                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            <?php }
                            ?>
                        </div>

                        <div class="tab-pane" id="newReportingManager">
                            <table class="table table-striped" id="profileInfoTable" style="background-color: #ffffff;width: 100%">
                                <tbody>
                                <?php
                                if(empty($reporting)){
                                    echo '<div style="margin: 10px; font-weight: bold;">No record</div>';
                                }
                                ?>
                                <?php
                                    $isPending = search_pendingReportingDataApproval($reporting, 'active');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_active'); ?><!--Active --></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    
                                        }?>
                                         <?php
                                    $reporting = search_pendingReportingDataApproval($reporting, 'isprimary');
                                    if (!empty($reporting)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_is_primary'); ?><!--Primary --></strong>
                                            </td>
                                            <?php echo $reporting ?>
                                        </tr>
                                        <?php
                                    
                                        }?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="tab-pane" id="newdepartment">
                            <table class="table table-striped" id="profileInfoTable" style="background-color: #ffffff;width: 100%">
                                <tbody>
                                <?php
                                if(empty($department)){
                                    echo '<div style="margin: 10px; font-weight: bold;">No record</div>';
                                }
                                ?>
                                <?php
                                    $isPending = search_pendingDepartmentDataApproval($department, 'isActive');
                                    if (!empty($isPending)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_active'); ?><!--Active --></strong>
                                            </td>
                                            <?php echo $isPending ?>
                                        </tr>
                                        <?php
                                    
                                        }?>
                                         <?php
                                    $reporting = search_pendingDepartmentDataApproval($department, 'isPrimary');
                                    if (!empty($reporting)) { ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo $this->lang->line('common_is_primary'); ?><!--Primary --></strong>
                                            </td>
                                            <?php echo $reporting ?>
                                        </tr>
                                        <?php
                                    
                                        }?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="tab-pane" id="newbank">
                            <table class="table table-striped" id="profileInfoTable" style="background-color: #ffffff;width: 100%">
                                <tbody>
                                <?php
                                if(empty($bankprimary) && empty($bankdetail)){
                                    echo '<div style="margin: 10px; font-weight: bold;">No record</div>';
                                }
                                ?>
                                <?php
                                $isPending = search_pendingBankIsPrimaryDataApproval($bankprimary, 'isPrimary');
                                if (!empty($isPending)) { ?>
                                    <tr>
                                        <td><strong>
                                                <?php echo $this->lang->line('common_is_primary'); ?><!--Active --></strong>
                                        </td>
                                        <?php echo $isPending ?>
                                    </tr>
                                <?php
                                }?>
                                </tbody>
                            </table>
                                
                                <?php  if(!empty($bankdetail)){?>
                                <table class="table table-striped" id="profileInfoTable" style="background-color: #ffffff;width: 100%">
                                    <tbody>
                                  
                                    <div class="container-fluid familyMasterContainer" id="" style="/*padding: 0px*/">
                                        <div class="col-sm-12" style="background: #e8e8e8; border-bottom: 1px solid #ccc;">
                                            <div class="col-sm-10" style="font-size: 14px; font-weight: bold;"> New record </div>

                                            <div class="col-sm-2">
                                                <input type="checkbox" name="addBankDetail[]"value="<?php echo isset($bankdetail[0]['relatedColumnID']) ? $bankdetail[0]['relatedColumnID'] : ''; ?>" class="approveNewChk"/>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="familyContainer">
                                                <input type="hidden" id="relatedid" value="<?php echo isset($bankdetail[0]['relatedColumnID']) ? $bankdetail[0]['relatedColumnID'] : ''; ?>">

                                                    <table class="table table-condensed">
                                                        <tr>
                                                            <td id="_relationship">Account Holder Name :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){
                                                                    if ($bank['columnName'] === 'accountHolderName') {
                                                                        $accountHolderName = $bank['columnVal'];
                                                                        echo $accountHolderName?>
                                                                        <input type="hidden" disabled id="bankdetails[]" Value="<?php echo $bank['empID'] ?>">
                                                                    <?php break;
                                                                    }?>
                                                            <?php }?>  
                                                            
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td id="_name">Bank :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'bankID') {
                                                                        $bankname = $bank['columnVal'];
                                                                        foreach($bankdetailtext as $bkdetail){
                                                                            if($bankname==$bkdetail['bankID']){
                                                                                echo $bkdetail['bankName']?>
                                                                                <input type="hidden" name="bankdetails[]"  disabled Value="<?php echo $bkdetail['bankID'] ?>">
                                                                                <?php }
                                                                        }
                                                                    break;
                                                                    }?>
                                                            <?php }?>  
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td id="_nationality">Branch :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'branchID') {
                                                                        $branchname = $bank['columnVal'];
                                                                        foreach($bankdetailtext as $bkdetail){
                                                                            if($branchname==$bkdetail['branchID']){
                                                                                echo $bkdetail['branchName']?>
                                                                                <input name="bankdetails[]"  type="hidden" disabled Value="<?php echo $bkdetail['branchID'] ?>">
                                                                                <?php }
                                                                        }
                                                                    break;
                                                                    }?>
                                                            <?php }?> 
                                                            
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td id="_DOB">Account No :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'accountNo') {
                                                                        $accountnum = $bank['columnVal'];?>
                                                                        <input name="bankdetails[]"  type="text" disabled Value="<?php echo $accountnum ?>">
                                                                    <?php break;
                                                                    }?>
                                                            <?php }?>  
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td id="_gender">Sales Transfer % :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'toBankPercentage') {
                                                                        $toBankPercentage = $bank['columnVal'];?>
                                                                        <input name="bankdetails[]"   type="text" disabled Value="<?php echo $toBankPercentage ?>">
                                                                    <?php break;
                                                                    }?>
                                                            <?php }?>  
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td id="_nationalCode">IBAN Code :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'ibancode') {
                                                                        $ibancode = $bank['columnVal'];?>
                                                                        <input name="bankdetails[]"  type="text" disabled Value="<?php echo $ibancode ?>">
                                                                    <?php break;
                                                                    }?>
                                                            <?php }?>  
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td id="_idNO">Swift Code :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'swiftcode') {
                                                                        $swiftcode = $bank['columnVal'];?>
                                                                        <input name="bankdetails[]"  type="text" disabled Value="<?php echo $swiftcode ?>">
                                                                    <?php break;
                                                                    }?>
                                                            <?php }?>  
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td id="_passportNo">Status :</td>
                                                            <td>
                                                            <?php 
                                                            foreach($bankdetail as $bank){?>
                                                                    <?php
                                                                    if ($bank['columnName'] === 'isActive') {
                                                                        $isActive = $bank['columnVal'];
                                                                        if($isActive==1){
                                                                            $active='Active';
                                                                        }
                                                                        else{
                                                                            $active='Inactive';} ?>
                                                                            <?php echo $active ?>
                                                                        <input name="bankdetails[]"  type="hidden" disabled value="<?php echo  $isActive ?>">
                                                                    <?php break;
                                                                    }?>
                                                            <?php }?>  
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </tbody>
                                </table>
                                <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
