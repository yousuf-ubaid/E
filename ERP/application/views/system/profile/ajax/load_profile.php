<?php
/** Translation added  */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$title = $this->lang->line('profile_my_profile');

$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
$isADVcost = getPolicyValues('ACC', 'All');
$empData = $empArray['employees'];
$isNeedApproval = getPolicyValues('EPD', 'All');

$current_date = '';
$date_format_policy = date_format_policy();

$hrDocuments = get_hrDocuments();
?>
<script>
    var isNeedApproval = '<?php echo $isNeedApproval; ?>';
    function colorLabel(labelID) {
        if (isNeedApproval == 1) {
            $('#' + labelID).addClass('pendingApproval');
            $('#msg-div').show();
        }
    }
</script>

<style>
    .select2-container .select2-choice > .select2-chosen {
        min-width: 200px;
    }

    .familyContainer {
        padding: 3px;
    }

    .familyMasterContainer {
        border: 1px dashed #a3a3a3;
        margin-bottom: 10px;
        background-color: #ffffff;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    .familyImgSize {
        max-width: 180px;
        max-height: 150px;
    }

    .account-in-activate {
        color: #c5bdc2;
        text-align: center;
    }

    .account-activate {
        color: #109400;
        text-align: center;
    }

    .bankInfoContainer {
        padding: 2px;
        font-size: 12px;
        border: 1px solid rgba(215, 215, 215, 0.54);
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        margin-bottom: 10px;
    }

    .bankItem {
        padding: 2px 0px;
    }

    .hrms_imageDiv2 {
        max-height: 52px;
        max-width: 52px;
        margin-top: -17px;
        border: 1px solid rgba(0, 0, 0, 0.25);
        padding: 0px;
    }

    .hrms_imageSize2 {
        max-height: 50px !important;
        max-width: 50px !important;
    }

    .ar {
        text-align: right !important;
    }

    div.show-image {
        position: relative;

        margin: 5px;
    }

    div.show-image:hover img {
        opacity: 0.5;
    }

    div.show-image:hover button {
        display: block;
    }

    div.show-image button {
        position: absolute;
        display: none;
    }

    div.show-image button.update {
        top: 0;
        left: 0;
    }

    #profileInfoTable tr td:first-child {
        color: #095db3;
    }

    #profileInfoTable tr td:nth-child(2) {
        font-weight: bold;
    }

    .progress {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }

    .pendingApproval {
        color: #adad45 !important;
    }

    #msg-div {
        color: red;
    / / font-weight: bold;
        display: none;
    }

    .thumbnailDoc {
        width: 100px;
        height: 110px !important;
        text-align: center;
        display: inline-block;
        margin: 0 10px 10px 0;
        float: left;
    }

    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #cacaca !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }
    .img-thumbnail-2{
        padding: 1rem;
        background-color: #f8f8f9;
        border: 1px solid #d2d4e4;
        border-radius: 50rem;
        max-width: 100%;
        margin-top: 25px;
        margin-bottom: 20px;
        width: 20rem;
        height: 20rem;
    }
</style>

<div id="loadsearchpage">
    <div class="row">
        <div class="col-md-12"><!--col-md-offset-2-->
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo $this->lang->line('profile_my_profile'); ?><!--My Profile--></div>
                <div class="panel-body">
                    <div class="tabbable nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#pdetail" data-toggle="tab">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    <?php echo $this->lang->line('profile_personal_detail'); ?><!--Personal Details -->
                                </a></li>
                            <li class="hide"><a class="" href="#employmentTab" data-toggle="tab"><i
                                        class="fa fa-building-o"
                                        aria-hidden="true"></i>
                                    <?php echo $this->lang->line('profile_employment_data'); ?><!--Employment Data-->
                                </a></li>
                            <!-- <li><a href="#settings" data-toggle="tab" aria-expanded="true"><i
                                        class="fa fa-key"
                                        aria-hidden="true"></i>
                                    <?php /*echo $this->lang->line('profile_change_password'); */ ?><!--Change Password--></a>
                            <!-- </li>-->
                            <li class="hide"><a href="#paysheet" data-toggle="tab" aria-expanded="true">
                                    <?php echo $this->lang->line('profile_pay_slip'); ?><!--Pay Slip--></a>
                            </li>
                            <li class="" onclick="fetch_family_details()"><a class="" href="#familyTab"
                                                                             data-toggle="tab"><i
                                        class="fa fa-users"
                                        aria-hidden="true"></i>
                                    <?php echo $this->lang->line('profile_family_details'); ?><!--Family Details-->
                                </a></li>
                            <li class="" onclick="fetch_qualification()">
                                <a class="" href="#qualification_tab" data-toggle="tab">
                                    <i class="fa fa-file-o"
                                       aria-hidden="true"></i> <?php echo $this->lang->line('profile_education'); ?>
                                    <!--Education-->
                                </a>
                            </li>
                            <li class="" onclick="fetch_bank_details()"><a class="" href="#bankTab" data-toggle="tab"><i
                                        class="fa fa-university"
                                        aria-hidden="true"></i>
                                    <?php echo $this->lang->line('profile_bank_details'); ?><!--Bank Details-->
                                </a></li>
                            <li class="" onclick="fetch_document()"><a class="" href="#documentTab" data-toggle="tab"><i
                                        class="fa fa-file-text-o"
                                        aria-hidden="true"></i>
                                    <?php echo $this->lang->line('profile_family_documents'); ?><!--Documents-->
                                </a>
                            </li>
                            <li class="" onclick="fetch_my_employee_list()" id="emplist"><a class=""
                                                                                            href="#myEmployeeTab"
                                                                                            data-toggle="tab"><i
                                        class="fa fa-users"
                                        aria-hidden="true"></i>
                                    <?php echo $this->lang->line('profile_my_employee_list'); ?><!--My Employee List-->
                                </a></li>
                            <li class="">
                                <a class="" href="#hrDocumnetsTab" data-toggle="tab">
                                    <i class="fa fa-file-o" aria-hidden="true"></i>
                                    <?php echo $this->lang->line('common_hr_documents'); ?><!--HR Documents-->
                                </a>
                            </li>
                            <li class="" onclick="fetch_employee_assets()">
                                <a class="" href="#assets_tab" data-toggle="tab">
                                    <i class="fa fa-cubes" aria-hidden="true"></i>
                                    <?php echo $this->lang->line('emp_master_assets'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="pdetail">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div id="msg-div">
                                                    Pending approval data are shown in
                                                    <span class="label" style="background: #adad45; padding: 0px 5px">&nbsp;</span>
                                                    color
                                                </div>
                                                <table class="table table-striped table-cus-inner" id="profileInfoTable"
                                                       style="background-color: #ffffff;width: 100%">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width:30%"><strong>
                                                                <?php echo $this->lang->line('emp_employee_code'); ?><!--Emp ID:--> </strong>
                                                        </td>
                                                        <td style="width:60%" colspan="3">
                                                            <?php echo is_null($empData['ECode']) ? '-' : $empData['ECode'] ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:30%">
                                                            <strong id="_EmpTitleId">
                                                                <?php echo $this->lang->line('common_title'); ?><!--Title: --></strong>
                                                        </td>
                                                        <td style="width:60%" colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EmpTitleId', 1) : null;
                                                            if (!empty($isPending)) {
                                                                $titleID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_EmpTitleId'); </script>";
                                                            } else {
                                                                $titleID = (empty($empData['EmpTitleId'])) ? '' : $empData['EmpTitleId'];
                                                                $description = (empty($empData['TitleDescription'])) ? '' : $empData['TitleDescription'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID; ?>" data-name="EmpTitleId"
                                                               data-title="Title" class="titleDrop"
                                                               data-value="<?php echo $titleID ?>"
                                                               data-related="_EmpTitleId">
                                                                <?php echo $description; ?>
                                                            </a> &nbsp;&nbsp;
                                                        </td>
                                                    </tr>
                                                    <?php if($is_tibian != 'Y') { ?>
                                                    <tr>
                                                        <td>
                                                            <strong id="_nameWithInitial">
                                                                <?php echo $this->lang->line('emp_name_with_initials'); ?><!--Initial:--> </strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            //echo '<pre>'; print_r(search_pendingData($pendingData, 'initial')); echo '</pre>';
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'initial') : null;
                                                            if ($isPending !== null) {
                                                                $initial = $isPending;
                                                                echo "<script> colorLabel('_nameWithInitial'); </script>";
                                                            } else {
                                                                $initial = (empty($empData['initial'])) ? '' : $empData['initial'];
                                                            }


                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Ename4') : null;
                                                            if ($isPending !== null) {
                                                                $empName = $isPending;
                                                                echo "<script> colorLabel('_nameWithInitial'); </script>";
                                                            } else {
                                                                $empName = (empty($empData['Ename4'])) ? '' : $empData['Ename4'];
                                                            }

                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="initial"
                                                               data-title="Initial" class="xEditable"
                                                               data-value="<?php echo $initial ?>"
                                                               data-related="_nameWithInitial">
                                                                <?php echo $initial ?>
                                                            </a> &nbsp;
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="Ename4"
                                                               data-title="Name" class="xEditable"
                                                               data-value="<?php echo $empName ?>"
                                                               data-related="_nameWithInitial">
                                                                <?php echo $empName ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_Ename1">
                                                                <?php echo $this->lang->line('profile_full_name'); ?><!--Full Name: --></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php

                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Ename1') : null;
                                                            if ($isPending !== null) {
                                                                $fullName = $isPending;
                                                                echo "<script> colorLabel('_Ename1'); </script>";
                                                            } else {
                                                                $fullName = (empty($empData['Ename1'])) ? '' : $empData['Ename1'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="Ename1"
                                                               data-title="Full Name" class="xEditable"
                                                               data-value="<?php echo $fullName ?>"
                                                               data-related="_Ename1">
                                                                <?php echo $fullName ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_Ename3">
                                                                <?php echo $this->lang->line('profile_surname'); ?><!--Surname:--> </strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Ename3') : null;
                                                            if ($isPending !== null) {
                                                                $surName = $isPending;
                                                                echo "<script> colorLabel('_Ename3'); </script>";
                                                            } else {
                                                                $surName = (empty($empData['Ename3'])) ? '' : $empData['Ename3'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="Ename3"
                                                               data-title="Sur Name" class="xEditable"
                                                               data-value="<?php echo $surName ?>"
                                                               data-related="_Ename3">
                                                                <?php echo $surName ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } else {?>
                                                        <tr>
                                                            <td colspan="2" class="" style="width: 50%; padding: 0px">
                                                                <fieldset class="scheduler-border">
                                                                    <legend><?php echo $this->lang->line('emp_name_primary'); ?></legend>
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <td>
                                                                                <strong id="_Ename1">
                                                                                    <?php echo $this->lang->line('common_emp_first_name'); ?> </strong>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Ename1') : null;
                                                                                if ($isPending !== null) {
                                                                                    $name1 = $isPending;
                                                                                    echo "<script> colorLabel('_Ename1'); </script>";
                                                                                } else {
                                                                                    $name1 = (empty($empData['Ename1'])) ? '' : $empData['Ename1'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail/tibian') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="Ename1"
                                                                                   data-title="First Name" class="xEditable"
                                                                                   data-value="<?php echo $name1 ?>"
                                                                                   data-related="_Ename1">
                                                                                    <?php echo $name1 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <strong id="_empSecondName">
                                                                                    <?php echo $this->lang->line('common_emp_second_name'); ?> </strong>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'empSecondName') : null;
                                                                                if ($isPending !== null) {
                                                                                    $empSecondName = $isPending;
                                                                                    echo "<script> colorLabel('_empSecondName'); </script>";
                                                                                } else {
                                                                                    $empSecondName = (empty($empData['empSecondName'])) ? '' : $empData['empSecondName'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="empSecondName"
                                                                                   data-title="Second Name" class="xEditable"
                                                                                   data-value="<?php echo $empSecondName ?>"
                                                                                   data-related="_empSecondName">
                                                                                    <?php echo $empSecondName ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <strong id="_Ename3">
                                                                                    <?php echo $this->lang->line('common_emp_third_name'); ?> </strong>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Ename3') : null;
                                                                                if ($isPending !== null) {
                                                                                    $name3 = $isPending;
                                                                                    echo "<script> colorLabel('_Ename3'); </script>";
                                                                                } else {
                                                                                    $name3 = (empty($empData['Ename3'])) ? '' : $empData['Ename3'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="Ename3"
                                                                                   data-title="Third Name" class="xEditable"
                                                                                   data-value="<?php echo $name3 ?>"
                                                                                   data-related="_Ename3">
                                                                                    <?php echo $name3 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <strong id="_Ename4">
                                                                                    <?php echo $this->lang->line('common_emp_fourth_name'); ?> </strong>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Ename4') : null;
                                                                                if ($isPending !== null) {
                                                                                    $name4 = $isPending;
                                                                                    echo "<script> colorLabel('_Ename4'); </script>";
                                                                                } else {
                                                                                    $name4 = (empty($empData['Ename4'])) ? '' : $empData['Ename4'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="Ename4"
                                                                                   data-title="Fourth Name" class="xEditable"
                                                                                   data-value="<?php echo $name4 ?>"
                                                                                   data-related="_Ename4">
                                                                                    <?php echo $name4 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <strong id="_EFamilyName">
                                                                                    <?php echo $this->lang->line('common_emp_family_name'); ?> </strong>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EFamilyName') : null;
                                                                                if ($isPending !== null) {
                                                                                    $family_name = $isPending;
                                                                                    echo "<script> colorLabel('_EFamilyName'); </script>";
                                                                                } else {
                                                                                    $family_name = (empty($empData['EFamilyName'])) ? '' : $empData['EFamilyName'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail/tibian') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="EFamilyName"
                                                                                   data-title="Family Name" class="xEditable"
                                                                                   data-value="<?php echo $family_name ?>"
                                                                                   data-related="_EFamilyName">
                                                                                    <?php echo $family_name ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </fieldset>
                                                            </td>

                                                            <td colspan="2" class="" style="width: 50%; padding: 0px">
                                                                <fieldset class="scheduler-border">
                                                                    <legend><?php echo $this->lang->line('emp_name_in_arabic'); ?></legend>
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <td><strong id="_Enameother1"> <?php echo $this->lang->line('common_emp_first_name'); ?> </strong></td>
                                                                            <td>

                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Enameother1') : null;
                                                                                if ($isPending !== null) {
                                                                                    $arabic_name1 = $isPending;
                                                                                    echo "<script> colorLabel('_Enameother1'); </script>";
                                                                                } else {
                                                                                    $arabic_name1 = (empty($empData['Enameother1'])) ? '' : $empData['Enameother1'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail/tibian') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="Enameother1"
                                                                                   data-title="First Name Arabic" class="xEditable"
                                                                                   data-value="<?php echo $arabic_name1 ?>"
                                                                                   data-related="_Enameother1">
                                                                                    <?php echo $arabic_name1 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong id="_empSecondNameOther"> <?php echo $this->lang->line('common_emp_second_name'); ?> </strong></td>
                                                                            <td>

                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'empSecondNameOther') : null;
                                                                                if ($isPending !== null) {
                                                                                    $arabic_name2 = $isPending;
                                                                                    echo "<script> colorLabel('_empSecondNameOther'); </script>";
                                                                                } else {
                                                                                    $arabic_name2 = (empty($empData['empSecondNameOther'])) ? '' : $empData['empSecondNameOther'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="empSecondNameOther"
                                                                                   data-title="Second Name Arabic" class="xEditable"
                                                                                   data-value="<?php echo $arabic_name2 ?>"
                                                                                   data-related="_empSecondNameOther">
                                                                                    <?php echo $arabic_name2 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong id="_Enameother3"> <?php echo $this->lang->line('common_emp_third_name'); ?> </strong></td>
                                                                            <td>

                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Enameother3') : null;
                                                                                if ($isPending !== null) {
                                                                                    $arabic_name3 = $isPending;
                                                                                    echo "<script> colorLabel('_Enameother3'); </script>";
                                                                                } else {
                                                                                    $arabic_name3 = (empty($empData['Enameother3'])) ? '' : $empData['Enameother3'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="Enameother3"
                                                                                   data-title="Third Name Arabic" class="xEditable"
                                                                                   data-value="<?php echo $arabic_name3 ?>"
                                                                                   data-related="_Enameother3">
                                                                                    <?php echo $arabic_name3 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong id="_Enameother4"> <?php echo $this->lang->line('common_emp_fourth_name'); ?> </strong></td>
                                                                            <td>

                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Enameother4') : null;
                                                                                if ($isPending !== null) {
                                                                                    $arabic_name4 = $isPending;
                                                                                    echo "<script> colorLabel('_Enameother4'); </script>";
                                                                                } else {
                                                                                    $arabic_name4 = (empty($empData['Enameother4'])) ? '' : $empData['Enameother4'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="Enameother4"
                                                                                   data-title="Fourth Name Arabic" class="xEditable"
                                                                                   data-value="<?php echo $arabic_name4 ?>"
                                                                                   data-related="_Enameother4">
                                                                                    <?php echo $arabic_name4 ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong id="_EFamilyNameOther"> <?php echo $this->lang->line('common_emp_family_name'); ?> </strong></td>
                                                                            <td>

                                                                                <?php
                                                                                $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EFamilyNameOther') : null;
                                                                                if ($isPending !== null) {
                                                                                    $arabic_family_name = $isPending;
                                                                                    echo "<script> colorLabel('_EFamilyNameOther'); </script>";
                                                                                } else {
                                                                                    $arabic_family_name = (empty($empData['EFamilyNameOther'])) ? '' : $empData['EFamilyNameOther'];
                                                                                }
                                                                                ?>

                                                                                <a href="#" data-type="text" data-placement="bottom"
                                                                                   data-url="<?php echo site_url('Profile/update_empDetail/tibian') ?>"
                                                                                   data-pk="<?php echo $empID ?>" data-name="EFamilyNameOther"
                                                                                   data-title="Family Name Arabic" class="xEditable"
                                                                                   data-value="<?php echo $arabic_family_name ?>"
                                                                                   data-related="_EFamilyNameOther">
                                                                                    <?php echo $arabic_family_name ?>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td><strong>
                                                                <?php echo $this->lang->line('emp_primary_e-mail'); ?><!--Email :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php echo empty($empData['EEmail']) ? '' : $empData['EEmail']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_EpTelephone">
                                                                <?php echo $this->lang->line('common_telephone'); ?><!--Telephone :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EpTelephone') : null;
                                                            if ($isPending !== null) {
                                                                $telNo = $isPending;
                                                                echo "<script> colorLabel('_EpTelephone'); </script>";
                                                            } else {
                                                                $telNo = (empty($empData['EpTelephone'])) ? '' : $empData['EpTelephone'];
                                                            }
                                                            ?>

                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="EpTelephone"
                                                               data-title="Telephone No" class="xEditable"
                                                               data-value="<?php echo $telNo ?>"
                                                               data-related="_EpTelephone">
                                                                <?php echo $telNo ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_EcMobile">
                                                                <?php echo $this->lang->line('common_mobile'); ?><!--Mobile :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EcMobile') : null;
                                                            if ($isPending !== null) {
                                                                $mobileNo = $isPending;
                                                                echo "<script> colorLabel('_EcMobile'); </script>";
                                                            } else {
                                                                $mobileNo = (empty($empData['EcMobile'])) ? '' : $empData['EcMobile'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="EcMobile"
                                                               data-title="Mobile No" class="xEditable"
                                                               data-value="<?php echo $mobileNo ?>"
                                                               data-related="_EcMobile">
                                                                <?php echo $mobileNo ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_address">
                                                                <?php echo $this->lang->line('common_address'); ?><!--Address :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EpAddress1') : null;
                                                            if (!empty($isPending)) {
                                                                $add1 = $isPending;
                                                                echo "<script> colorLabel('_address'); </script>";
                                                            } else {
                                                                $add1 = (empty($empData['EpAddress1'])) ? '' : $empData['EpAddress1'];
                                                            }

                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EpAddress2') : null;
                                                            if (!empty($isPending)) {
                                                                $add2 = $isPending;
                                                                echo "<script> colorLabel('_address'); </script>";
                                                            } else {
                                                                $add2 = (empty($empData['EpAddress2'])) ? '' : $empData['EpAddress2'];
                                                            }

                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EpAddress3') : null;
                                                            if (!empty($isPending)) {
                                                                $add3 = $isPending;
                                                                echo "<script> colorLabel('_address'); </script>";
                                                            } else {
                                                                $add3 = (empty($empData['EpAddress3'])) ? '' : $empData['EpAddress3'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="EpAddress1"
                                                               data-title="Address Line1" class="xEditable"
                                                               data-value="<?php echo $add1 ?>" data-related="_address">
                                                                <?php echo $add1 ?>
                                                            </a>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="EpAddress2"
                                                               data-title="Address Line2" class="xEditable"
                                                               data-value="<?php echo $add2 ?>" data-related="_address">
                                                                <?php echo $add2 ?>
                                                            </a>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="EpAddress3"
                                                               data-title="Address Line3" class="xEditable"
                                                               data-value="<?php echo $add3 ?>" data-related="_address">
                                                                <?php echo $add3 ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_EDOB">
                                                                <?php echo $this->lang->line('profile_date_of_birth'); ?><!--Date of Birth :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $dob = '0000-00-00';
                                                            $dob2 = '0000-00-00';

                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EDOB') : null;
                                                            if ($isPending !== null) {
                                                                $dob = format_date_dob($isPending);
                                                                $dob2 = $isPending;
                                                                echo "<script> colorLabel('_EDOB'); </script>";
                                                            } else {
                                                                $dob = (empty($empData['EDOB'])) ? '' : $empData['EDOB'];
                                                                $dob = format_date_dob($dob);
                                                                $dob2 = $empData['EDOB'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="combodate" data-placement="bottom"
                                                               id="dob"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="EDOB"
                                                               data-title="Date of Birth" class="xEditableDate"
                                                               data-value="<?php echo $dob2 ?>" data-related="_EDOB">
                                                                <?php echo $dob ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_Nid">
                                                                <?php echo $this->lang->line('common_nationality'); ?><!--Nationality :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Nid', 1) : null;
                                                            if (!empty($isPending)) {
                                                                $nationalID = $isPending[0];
                                                                $nationalText = $isPending[1];
                                                                echo "<script> colorLabel('_Nid'); </script>";
                                                            } else {
                                                                $nationalID = (empty($empData['Nid'])) ? '' : $empData['Nid'];
                                                                $nationalText = (empty($empData['Nationality'])) ? '' : $empData['Nationality'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID; ?>" data-name="Nid"
                                                               data-title="Nationality" class="nationalityDrop"
                                                                <?php echo $nationalText; ?>
                                                               data-value="<?php echo $nationalID ?>"
                                                               data-related="_Nid">
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong
                                                                id="_MaritialStatus"><?php echo $this->lang->line('profile_marital_status'); ?>
                                                                <!--Marital Status :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'MaritialStatus', 1) : null;
                                                            if (!empty($isPending)) {
                                                                $mID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_MaritialStatus'); </script>";
                                                            } else {
                                                                $mID = (empty($empData['MaritialStatus'])) ? '' : $empData['MaritialStatus'];
                                                                $description = (empty($empData['maritialDescription'])) ? '' : $empData['maritialDescription'];
                                                            }

                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID; ?>"
                                                               data-name="MaritialStatus" data-title="Marital Status"
                                                               class="maritalStatus"
                                                               data-value="<?php echo $mID ?>"
                                                               data-related="_MaritialStatus">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_Rid">
                                                                <?php echo $this->lang->line('common_religion'); ?><!--Religion :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Rid', 1) : null;
                                                            if (!empty($isPending)) {
                                                                $rID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_Rid'); </script>";
                                                            } else {
                                                                $rID = (empty($empData['Rid'])) ? '' : $empData['Rid'];
                                                                $description = (empty($empData['Religion'])) ? '' : $empData['Religion'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID; ?>" data-name="Rid"
                                                               data-title="Religion" class="religionDrop"
                                                               data-value="<?php echo $rID ?>" data-related="_Rid">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_Gender">
                                                                <?php echo $this->lang->line('common_gender'); ?><!--Gender :--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'Gender', 1) : null;
                                                            if (!empty($isPending)) {
                                                                $genderID = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_Gender'); </script>";
                                                            } else {
                                                                $genderID = (empty($empData['Gender'])) ? '' : $empData['Gender'];
                                                                $description = (empty($empData['GenderDesc'])) ? '' : $empData['GenderDesc'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID; ?>" data-name="Gender"
                                                               data-title="Gender" class="genderDrop"
                                                               data-value="<?php echo $genderID ?>"
                                                               data-related="_Gender">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong id="_BloodGroup">
                                                                <?php echo $this->lang->line('profile_blood_group'); ?><!--Blood Group: --></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'BloodGroup', 1) : null;
                                                            if (!empty($isPending)) {
                                                                $bloodGroup = $isPending[0];
                                                                $description = $isPending[1];
                                                                echo "<script> colorLabel('_BloodGroup'); </script>";
                                                            } else {
                                                                $bloodGroup = (empty($empData['BloodGroup'])) ? '' : $empData['BloodGroup'];
                                                                $description = (empty($empData['BloodDescription'])) ? '' : $empData['BloodDescription'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="select2"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID; ?>" data-name="BloodGroup"
                                                               data-title="Blood Group" class="bloodGroup"
                                                               data-value="<?php echo $bloodGroup ?>"
                                                               data-related="_BloodGroup">
                                                                <?php echo $description; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong id="_personalEmail">
                                                                <?php echo $this->lang->line('profile_Personal_email'); ?><!--Personal Email--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'personalEmail') : null;
                                                            if ($isPending !== null) {
                                                                $personalEmail = $isPending;
                                                                echo "<script> colorLabel('_personalEmail'); </script>";
                                                            } else {
                                                                $personalEmail = (empty($empData['personalEmail'])) ? '' : $empData['personalEmail'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="personalEmail"
                                                               data-title="Personal Email" class="xEditable"
                                                               data-value="<?php echo $personalEmail ?>"
                                                               data-related="_personalEmail">
                                                                <?php echo $personalEmail ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong id="_EVisaExpiryDate">
                                                                <?php echo $this->lang->line('profile_visa_expiry_date'); ?><!--Visa Expiry Date--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $expDate = '0000-00-00';
                                                            $expDate2 = '0000-00-00';

                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'EVisaExpiryDate') : null;
                                                            if ($isPending !== null) {
                                                                $expDate = format_date_dob($isPending);
                                                                $expDate2 = $isPending;
                                                                echo "<script> colorLabel('_EVisaExpiryDate'); </script>";
                                                            } else {
                                                                $expDate = (empty($empData['EVisaExpiryDate'])) ? '' : $empData['EVisaExpiryDate'];
                                                                $expDate = format_date_dob($expDate);
                                                                $expDate2 = $empData['EVisaExpiryDate'];
                                                            }
                                                            ?>
                                                            <?php echo $expDate ?>
                                                            <!--<a href="#" data-type="combodate" data-placement="bottom"
                                                               id="visaExpiryDate"
                                                               data-url="<?php /*echo site_url('Profile/update_empDetail') */?>"
                                                               data-pk="<?php /*echo $empID */?>"
                                                               data-name="EVisaExpiryDate" data-title="Visa Expiry Date"
                                                               class="xEditableDate"
                                                               data-value="<?php /*echo $expDate2 */?>"
                                                               data-related="_EVisaExpiryDate">
                                                                <?php /*echo $expDate */?>
                                                            </a>-->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>
                                                                <?php echo $this->lang->line('profile_date_of_join'); ?><!--Date of Join--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php if (isset($empData['EDOJ'])) {
                                                                if (!empty($empData['EDOJ']) && $empData['EDOJ'] != '0000-00-00') {
                                                                    echo format_date_dob($empData['EDOJ']);
                                                                }
                                                            } ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong id="_manPowerNo">
                                                                <?php echo $this->lang->line('profile_man_power_no'); ?><!--Man Power No--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'manPowerNo') : null;
                                                            if ($isPending !== null) {
                                                                $manPowerNo = $isPending;
                                                                echo "<script> colorLabel('_manPowerNo'); </script>";
                                                            } else {
                                                                $manPowerNo = (empty($empData['manPowerNo'])) ? '' : $empData['manPowerNo'];
                                                            }
                                                            ?>
                                                            <a href="#" data-type="text" data-placement="bottom"
                                                               data-url="<?php echo site_url('Profile/update_empDetail') ?>"
                                                               data-pk="<?php echo $empID ?>" data-name="manPowerNo"
                                                               data-title="Man Power No" class="xEditable"
                                                               data-value="<?php echo $manPowerNo ?>"
                                                               data-related="_manPowerNo">
                                                                <?php echo $manPowerNo ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php if ($isADVcost == 1){ ?>
                                                        <tr>
                                                            <td><strong>
                                                                    <?php echo $this->lang->line('profile_emp_bussines_level_deviion'); ?><!--bussines-level-division--></strong>
                                                            </td>
                                                            <td colspan="3">
                                                            <?php echo empty($reportingdeivition) ? '' : $reportingdeivition; ?>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td><strong>
                                                                    <?php echo $this->lang->line('profile_emp_bussines_level_segment'); ?><!--business-level-segment--></strong>
                                                            </td>
                                                            <td colspan="3">
                                                            <?php echo empty($reportingsegment) ? '' : $reportingsegment; ?>
                                                            </td>
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td><strong>
                                                                    <?php echo $this->lang->line('profile_emp_bussines_level_sub_segment'); ?><!--bussines-level-subsegment--></strong>
                                                            </td>
                                                            <td colspan="3">
                                                                <?php echo empty($reportingsubsegment) ? '' : $reportingsubsegment; ?>
                                                            </td>
                                                        </tr>

                                                      

                                                     

                                                    <?php } ?>


                                                    <?php if ($isADVcost == 1): ?>
                                                        <tr>
                                                            <td><strong>
                                                                    <?php echo $this->lang->line('profile_emp_funtion'); ?><!--Function--></strong>
                                                            </td>
                                                            <td colspan="3">
                                                                <?php echo empty($empArray['department']['DepartmentDes']) ? '' : $empArray['department']['DepartmentDes']; ?>
                                                            </td>
                                                        </tr>
                                                    <?php else: ?>
                                                        
                                                    <tr>
                                                        <td><strong>
                                                                <?php echo $this->lang->line('emp_attendance_department'); ?><!--Departments--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php echo empty($empArray['department']['DepartmentDes']) ? '' : $empArray['department']['DepartmentDes']; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>



                                                    <tr>
                                                        <td><strong>
                                                                <?php echo $this->lang->line('profile_report_manager'); ?><!--Reporting Manager--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php echo empty($empArray['manager']['Ename2']) ? '' : $empArray['manager']['Ename2']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>
                                                                <?php echo $this->lang->line('emp_grade'); ?><!--Grade--></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php echo empty($grade) ? '' : $grade; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('common_mobile_credit_limit'); ?></strong>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php
                                                            $currency_det = get_employee_currency(current_userID(), 'det');
                                                            $dPlace = (!empty($currency_det))? $currency_det->dPlace: 2;
                                                            $currency_code = (!empty($currency_det))? $currency_det->code: '';
                                                            echo empty($empData['mobileCreditLimit']) ? number_format(0, $dPlace) :  number_format($empData['mobileCreditLimit'], $dPlace);
                                                            echo " ( {$currency_code} )";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <?php
                                        $empImage = $empData['EmpImage'];
                                        //$empImage = base_url() . "images/users/{$empImage}";
                                        ?>
                                        <img src="<?=$empImage?>" class="img-thumbnail-2" alt="<?=$empData['ECode']?>" width="304"
                                            height="236">
                                        <h3 style="text-align: center;margin: 0;color: #095db3;font-weight: bold">
                                            <?php
                                            $full_name_display = ($is_tibian != 'Y')? $empData['Ename1']: $empData['Ename2'];
                                            echo empty($full_name_display) ? '' : $full_name_display;
                                            ?>
                                        </h3>
                                        <h5 style="text-align: center;margin: 0;color: #095db3;font-weight: bold"><?php echo $empArray['designation']['DesDescription'] ?? '' ?></h5>
                                        <h6 id="changeProfilePic" style="text-align: center;margin: 0;color: #095db3;cursor: pointer;"><?php echo $this->lang->line('profile_change_profile_pic'); ?></h6>
                                        <input type="file" id="fileInput" style="display: none;" accept="image/*" />

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="employmentTab">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php if (!empty($leaveDetails)) { ?>
                                            <div class="table-responsive">
                                                <div style="margin-top: 5%">
                                                    <?php echo $this->lang->line('profile_leave_detail'); ?><!--Leave Details--></div>
                                                <table class="<?php echo table_class(); ?>" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th class="theadtr" style="auto">
                                                            <?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                                        <th class="theadtr" style="auto">
                                                            <?php echo $this->lang->line('profile_policy'); ?><!--Policy--></th>
                                                        <th class="theadtr" style="auto">
                                                            <?php echo $this->lang->line('profile_leave_entitled'); ?><!--Entitled--></th>
                                                        <th class="theadtr" style="auto">
                                                            <?php echo $this->lang->line('profile_taken'); ?><!--Taken--></th>
                                                        <th class="theadtr" style="auto">
                                                            <?php echo $this->lang->line('profile_balance'); ?><!--Balance--></th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <?php
                                                    foreach ($leaveDetails as $leave) {
                                                        $leaveTaken = ($leave['leaveTaken'] == '') ? '-' : $leave['leaveTaken'];
                                                        $entitled = ($leave['accrued'] == '') ? '-' : $leave['accrued'];
                                                        $balance = (!is_int($leave['days'])) ? round($leave['days'], 1) : round($leave['days']);
                                                        echo
                                                            '<tr>
                                                                    <td>' . $leave['description'] . '</td>
                                                                    <td>' . $leave['policyDescription'] . '</td>
                                                                     <td align="right">' . $entitled . '</td>
                                                                    <td align="right">' . $leaveTaken . '</td>
                                                                     <td align="right">' . $balance . '</td>

                                                                </tr>';
                                                    }
                                                    ?>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php

                                        }

                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="settings">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form class="form-horizontal" method="post" id="passwordForm"
                                              autocomplete="off">
                                            <div class="form-group">
                                                <label for="currentPassword" class="col-sm-3 control-label">
                                                    <?php echo $this->lang->line('profile_current_password'); ?><!--Current Password--></label>
                                                <div class="col-sm-6">
                                                    <input type="password"
                                                           placeholder="<?php echo $this->lang->line('profile_current_password'); ?>"
                                                           class="form-control" name="currentPassword"
                                                           id="currentPassword"/><!--"Current Password"-->

                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label for="newPassword" class="col-sm-3 control-label">
                                                    <?php echo $this->lang->line('profile_new_password'); ?><!--New Password--></label>
                                                <div class="col-sm-6">
                                                    <input type="password" onkeyup="validatepwsStrength()"
                                                           class="form-control" id="newPassword"
                                                           name="newPassword"
                                                           placeholder="<?php echo $this->lang->line('profile_new_password'); ?>">
                                                    <div class="progress" id="progrssbar">

                                                    </div>
                                                </div>
                                                <div class="col-sm-3" id="message"></div>

                                            </div>
                                            <div class="form-group">
                                                <label for="confirmPassword" class="col-sm-3 control-label">
                                                    <?php echo $this->lang->line('profile_confirm_password'); ?><!--Confirm Password--></label>
                                                <div class="col-sm-6">
                                                    <input type="password" class="form-control" id="confirmPassword"
                                                           name="confirmPassword"
                                                           placeholder="<?php echo $this->lang->line('profile_confirm_password'); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" id="passwordsavebtn" onclick=""
                                                            class="btn btn-primary">
                                                        <?php echo $this->lang->line('common_save_and_confirm'); ?><!--Submit-->
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="paysheet">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form class="form-horizontal" method="post" id="passwordForm">
                                            <div class="form-group">
                                                <label for="confirmPassword" class="col-sm-3 control-label">
                                                    <?php echo $this->lang->line('common_month'); ?><!--Month--></label>
                                                <div class="col-sm-4">
                                                    <?php echo form_dropdown('payrollMasterID', payroll_dropdown(), '', ' onchange="get_payScale()" class="form-control select2" id="payrollMasterID" required'); ?>
                                                </div>
                                            </div>


                                        </form>
                                        <div id="div_payscale"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="familyTab">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="div_familyDetail">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bankTab">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="div_bankDetail">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="documentTab">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="div_documentDetail">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="myEmployeeTab">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="div_myEmployeeDetail">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="hrDocumnetsTab">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="div_myEmployeeDetail">
                                        <form class="form-horizontal">
                                            <div class="box-body" style="text-align: center; background: #ffffff;">
                                                <?php

                                                if(!empty($hrDocuments)){
                                                    $this->load->library('s3');
                                                    $CI =& get_instance();

                                                    foreach ($hrDocuments as $doc) {

                                                        /*$file = base_url() . 'documents/hr_documents/' . $doc['documentFile'];
                                                        $link=generate_encrypt_link_only($file);*/
                                                        $file = $doc['documentFile'];
                                                        $link = $empImage = $CI->s3->createPresignedRequest($file, '+1 hour');
                                                        $linkStart = '<a href="' . $link . '" target="_blank">';
                                                        $linkEnd = '</a>';

                                                        echo '<div class="thumbnail thumbnailDoc" >
                                                            ' . $linkStart . '
                                                                <img class="" src="' . base_url() . 'images/doc1.ico" style="width:80px; height:65px; ">
                                                                <h6 style="margin: 2px;" class="text-muted text-center">' . $doc['documentDescription'] . '</h6>
                                                                <h6 style="margin: 2px;" class="text-muted text-center"> </h6>
                                                                <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                                                            ' . $linkEnd . '
                                                        </div>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="qualification_tab"></div>
                            <div class="tab-pane" id="assets_tab"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="addFamilyDetailModal" data-width="80%"
     role="dialog">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5> <?php echo $this->lang->line('profile_add_family_detail'); ?> <!--Add Family Detail--></h5>
            </div>
            <div class="modal-body" id="modal_contact">
                <form method="post" name="frm_FamilyContactDetails" id="frm_FamilyContactDetails"
                      class="form-horizontal">
                    <input type="hidden" value="0" id="empfamilydetailsID" name="empfamilydetailsID"/>
                    <input type="hidden" value="1" id="frmprofile" name="frmprofile"/>
                    <input type="hidden" value="" id="empID_familyDetail" name="employeeID"/>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="textinput">
                            <?php echo $this->lang->line('common_name'); ?><!--Name--></label>

                        <div class="col-md-7">
                            <input class="form-control input-md"
                                   placeholder="<?php echo $this->lang->line('common_name'); ?>" id="name" name="name"
                                   type="text" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="relationshipType">
                            <?php echo $this->lang->line('profile_relationship'); ?><!--Relationship--></label>

                        <div class="col-md-7">
                            <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="relationshipType" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="country">
                            <?php echo $this->lang->line('common_nationality'); ?><!--Nationality--></label>

                        <div class="col-md-7">
                            <?php echo form_dropdown('nationality', load_all_nationality_drop(), '', 'id="nationality" class="form-control select2"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo $this->lang->line('common_date_of_birth'); ?><!--Date of Birth--></label>

                        <div class="input-group datepic col-md-7" style="padding-left: 15px;">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="DOB" style="width: 94%;"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="DOB" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="gender">
                            <?php echo $this->lang->line('common_gender'); ?><!--Gender--></label>

                        <div class="col-md-7">
                            <select name="gender" class="form-control empMasterTxt" id="gender">
                                <option value="1"> <?php echo $this->lang->line('common_male'); ?><!--Male--></option>
                                <option value="2">
                                    <?php echo $this->lang->line('common_female'); ?><!--Female--></option>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="familyDetail_msg"></div>
            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="button" class="btn btn-primary" onclick="saveFamilyDetails()">
                    <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade pddLess" data-backdrop="static" id="modaluploadattachment" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <!-- <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
             <h5>Attachments</h5>
         </div>-->
        <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: white">
            <?php echo form_open_multipart('', 'id="family_attachment_uplode_form" class="form-horizontal"'); ?>
            <fieldset>
                <!-- Text input-->

                <input type="hidden" class="form-control" value="" id="empfamilydetailsAttachID"
                       name="empfamilydetailsAttachID">
                <input type="hidden" class="form-control" value="" id="empIDFamilyAttach"
                       name="empIDFamilyAttach">


                <!-- File Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">
                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></label>

                    <div class="col-md-8">
                        <input type="file" name="document_file" class=" input-md" id="image_file">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">
                        <?php echo $this->lang->line('common_description'); ?><!--Description--></label>

                    <div class="col-md-8">
                        <input type="text" name="attachmentDescription" id="attachmentDescription">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">
                        <?php echo $this->lang->line('common_document'); ?><!--Document--></label>

                    <div class="col-md-8">
                        <select name="documentID" id="documentID" class="form-control">
                            <option value="1">
                                <?php echo $this->lang->line('common_passport'); ?><!--Passport--></option>
                            <option value="2"><?php echo $this->lang->line('common_visa'); ?><!--Visa--></option>
                            <option value="3">
                                <?php echo $this->lang->line('common_insurance'); ?><!--Insurance--></option>
                        </select>
                    </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton"></label>

                    <div class="col-md-8">

                        <button type="button" class="btn btn-xs btn-primary" onclick="familyattachment_uplode()"><span
                                class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span></button>
                    </div>
                </div>


            </fieldset>
            </form>

            <hr>
            <div class="table-responsive">
                <table id="family_attachment_table" class="<?php echo table_class() ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 4%">#</th>
                        <th style="min-width: 15%">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th style="min-width: 20%">
                            <?php echo $this->lang->line('common_document'); ?><!--Document--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                </table>
            </div>

        </div>
        <div class="modal-footer" style="background-color: #ffffff">

            <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">
                <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
        </div>
    </div>
</div>

<div class="modal fade " data-backdrop="static" id="modaluploadimages" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <!--<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5>Attachments</h5>
        </div>-->
        <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: #F5F5F5">
            <?php echo form_open_multipart('', 'id="family_image_uplode_form" class="form-horizontal"'); ?>
            <fieldset>
                <!-- Text input-->

                <input type="hidden" class="form-control" value="" id="empfamilydetailzID"
                       name="empfamilydetailsID">


                <!-- File Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">
                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></label>

                    <div class="col-md-8">
                        <input type="file" name="document_file" class=" input-md" id="image_file">
                    </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton"></label>

                    <div class="col-md-8">

                        <button type="button" class="btn btn-xs btn-primary" onclick="familyimage_uplode()"><span
                                class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span></button>
                    </div>
                </div>
            </fieldset>
            </form>
        </div>
        <div class="modal-footer" style="background-color: #ffffff">

            <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">
                <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        fetch_family_details();
        fetch_document();

        $('.xEditable').editable({
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.select2').select2();

        $('.titleDrop').editable({
            source: [
                <?php
                $result = get_employeetitle();
                if (!empty($result)) {
                    $i = 1;
                    $count = count($result);
                    foreach ($result as $val) {
                        $string = str_replace(' ', '-', $val['TitleDescription']); // Replaces all spaces with hyphens.
                        $finalOutput = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
                        echo "{id: '" . $val['TitleID'] . "', text: '" . $finalOutput . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.xEditableDate').editable({
            format: 'YYYY-MM-DD',
            viewformat: 'DD.MM.YYYY',
            template: 'D / MMMM / YYYY',
            combodate: {
                minYear: 1930,
                maxYear: <?php echo format_date_getYear() + 10 ?>,
                minuteStep: 1
            },
            success: function (response) {
                colorLabel($(this).data('related'));
                var thisID = $(this).attr('id');
                if (thisID == 'dob' || thisID == 'visaExpiryDate') {
                    var dataArr = JSON.parse(response);
                    setTimeout(function () {
                        $('#' + thisID).text(dataArr[2]);
                    }, 300);
                }
            }
        });

        $('.genderDrop').editable({
            source: [
                <?php
                $result = get_gender();
                if (!empty($result)) {
                    $i = 1;
                    $count = count($result);
                    foreach ($result as $val) {
                        echo "{id: '" . $val['genderID'] . "', text: '" . trim($val['name'] ?? '') . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.nationalityDrop').editable({
            source: [
                <?php
                $result = $empArray['nationality'];
                if (!empty($result)) {
                    $i = 1;
                    $count = count($result);
                    foreach ($result as $val) {
                        $string = str_replace(' ', '-', $val['Nationality']); // Replaces all spaces with hyphens.
                        $finalOutput = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

                        echo "{id: '" . $val['NId'] . "', text: '" . $finalOutput . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.religionDrop').editable({
            source: [
                <?php
                $religionDrop = $empArray['religion'];
                if (!empty($religionDrop)) {
                    $i = 1;
                    $count = count($religionDrop);
                    foreach ($religionDrop as $religion) {
                        echo "{id: '" . $religion['RId'] . "', text: '" . trim($religion['Religion'] ?? '') . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.bloodGroup').editable({
            source: [
                <?php
                $bloodDrop = fetch_emp_blood_type(1);
                if (!empty($bloodDrop)) {
                    $i = 1;
                    $count = count($bloodDrop);
                    foreach ($bloodDrop as $blood) {
                        echo "{id: '" . $blood['BloodTypeID'] . "', text: '" . trim($blood['BloodDescription'] ?? '') . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.maritalStatus').editable({
            source: [
                <?php
                $mDrop = fetch_emp_maritialStatus(1);
                if (!empty($mDrop)) {
                    $i = 1;
                    $count = count($mDrop);
                    foreach ($mDrop as $row) {
                        echo "{id: '" . $row['maritialstatusID'] . "', text: '" . trim($row['description'] ?? '') . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        /**$('#passwordFormLogin').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                currentPassword: {validators: {notEmpty: {message: 'Current Password is required.'}}},
                newPassword: {validators: {notEmpty: {message: 'New Password is required.'}}}
                /!*confirmPassword: {
                    validators: {
                        identical: {
                            field: 'newPassword',
                            message: 'The password and its confirm are not the same'
                        },
                        notEmpty: {message: 'Confirm Password are required.'}
                    }
                }*!/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Profile/change_password'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#passwordFormLogin')[0].reset();
                    $("#passwordFormLogin").data('bootstrapValidator').resetForm();
                    stopLoad();
                    myAlert(data[0],data[1]);
                    $('#messagelogin').html('');
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                }, error: function (data) {
                    stopLoad();
                    var msg = JSON.parse(data.responseText);
                    myAlert('w', msg[1])
                }
            });
        });*/

        /*$('#currentPassword').strengthMeter('text', {
         container: $('#example-getting-started-text'),
         hierarchy: {
         '0': ['text-danger', 'Weak'],
         '5': ['text-warning', 'good'],
         '10': ['text-warning', 'strong'],
         '15': ['text-success', 'very strong']
         }
         });*/

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });


    });

    function get_payScale() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Bank_rec/get_paySlip_report') ?>",
            data: {
                payrollMasterID: $('#payrollMasterID').val(),
                empID:<?php  echo empty($empArray['employees']['EIdNo']) ? 0 : $empArray['employees']['EIdNo']; ?>},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_payscale").html(data);
                $("#payscaleReport").tableHeadFixer({
                    // fix table header
                    head: true,
                    // fix table footer
                    foot: true,
                    // fix x left columns
                    left: 4,
                    // fix x right columns
                    right: 0,
                    // z-index
                    'z-index': 0
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_family_details() {
        var empID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {empID: empID},
            url: "<?php echo site_url('Profile/fetch_family_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_familyDetail").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                //$("#familydetails_tab").html('<div class="alert alert-danger">An Error Occurred! Please Try Again.<br/><strong>Error Message: </strong>' + errorThrown + '</div>');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function fetch_document() {
        var empID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Profile/load_empDocumentProfileView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#div_documentDetail').html(data);

            }, error: function () {
                myAlert('e', 'Error');
                /* 'An Error Occurred! Please Try Again.'*/
                stopLoad();
            }
        });
    }

    function validatepwsStrength() {
        var passwordComplexityExist = '<?php echo $passwordComplexityExist; ?>';
        if (passwordComplexityExist == 1) {

            var word = $('#newPassword').val();
            var Score = 0;
            var conditions = 0;
            var iscapital = 0;
            var isspecial = 0;
            var lengt = word.length;
            var Capital = word.match(/[A-Z]/);
            var format = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (format.test(word) == true) {
                isspecial = 1
            } else {
                isspecial = 0
            }
            if (jQuery.isEmptyObject(Capital)) {
                iscapital = 0
            } else {
                iscapital = 1
            }
            $('#message').html('<label class="label label-danger">Weak</label>');
            $('#progrssbar').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
            $('#passwordsavebtn').attr('disabled', true);
            var minimumLength = '<?php echo $passwordComplexity['minimumLength'] ?? '' ?>';
            if (!jQuery.isEmptyObject(minimumLength)) {
                if (minimumLength <= lengt) {
                    conditions = conditions + 1;
                    Score = Score + 1;
                    $('#message').html(' ');
                    var isCapitalLettersMandatory = '<?php echo $passwordComplexity['isCapitalLettersMandatory'] ?? '' ?>';
                    var isSpecialCharactersMandatory = '<?php echo $passwordComplexity['isSpecialCharactersMandatory'] ?? '' ?>';
                    if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 1) {
                        conditions = conditions + 2;
                        if (iscapital == 1) {

                            Score = Score + 1;
                        }
                        if (isspecial == 1) {
                            Score = Score + 1;
                        }

                    } else if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 0) {
                        conditions = conditions + 1;
                        if (iscapital == 1) {
                            Score = Score + 1;
                        }

                    } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 1) {
                        conditions = conditions + 1;
                        if (isspecial == 1) {
                            Score = Score + 1;
                        }

                    } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 0) {

                    }


                    if (conditions == Score) {
                        $('#passwordsavebtn').attr('disabled', false);
                        $('#progrssbar').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                        $('#message').html('<label class="label label-success">Strong</label>');
                    } else if ((conditions % Score) > 0) {
                        $('#passwordsavebtn').attr('disabled', true);
                        $('#progrssbar').html('<div class="progress-bar progress-bar-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>');
                        $('#message').html('<label class="label label-warning">Medium</label>');
                    } else {
                        $('#passwordsavebtn').attr('disabled', true);
                        $('#progrssbar').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                        $('#message').html('<label class="label label-danger">Weak</label>');
                    }


                }
            }


        } else {

            var word = $('#newPassword').val();
            var lengt = word.length;

            if (lengt < 6) {
                $('#passwordsavebtn').attr('disabled', true);
                $('#progrssbar').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#message').html('<label class="label label-danger">Weak</label>');
            } else {
                $('#passwordsavebtn').attr('disabled', false);
                $('#progrssbar').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#message').html('<label class="label label-success">Strong</label>');
            }
        }
    }

    function fetch_my_employee_list() {
        var empID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {empID: empID},
            url: "<?php echo site_url('Profile/fetch_my_employee_list'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_myEmployeeDetail").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                //$("#familydetails_tab").html('<div class="alert alert-danger">An Error Occurred! Please Try Again.<br/><strong>Error Message: </strong>' + errorThrown + '</div>');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function fetch_bank_details() {
        var empID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {empID: empID},
            url: "<?php echo site_url('Profile/fetch_bank_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_bankDetail").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                //$("#familydetails_tab").html('<div class="alert alert-danger">An Error Occurred! Please Try Again.<br/><strong>Error Message: </strong>' + errorThrown + '</div>');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function addfamilydetails() {
        $('#addFamilyDetailModal').modal('show');
        $('#frm_FamilyContactDetails')[0].reset();
        $('#empfamilydetailsID').val('0');
        $('.select2').select2();
    }

    function saveFamilyDetails() {
        var empID = <?php echo current_userID(); ?>;
        $('#empID_familyDetail').val(empID);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/saveFamilyDetails') ?>", /*ajax/ajax-add-profile-contact-detail.php*/
            data: $("#frm_FamilyContactDetails").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                $("#familyDetail_msg").html('');
                $("#familyDetail_msg").show();
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#familyDetail_msg").html('<div class="alert alert-success"><strong> Success </strong><br>' + data['message'] + '</div>');
                    $("#addFamilyDetailModal").modal('hide');
                    fetch_family_details(data['empID']);
                    myAlert('s', data['message']);
                } else if (data.error == 1) {
                    $("#familyDetail_msg").html('<div class="alert alert-danger"><strong> Error </strong><br>' + data['message'] + '</div>');
                }
                setTimeout(function () {
                    $("#familyDetail_msg").hide();
                }, 5000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                setTimeout(function () {
                    $("#familyDetail_msg").hide();
                }, 5000);
                $("#familyDetail_msg").html('<div class="alert alert-danger"><strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown + '</div>');
            }
        });
        return false;
    }

    function attach_familydetail(empfamilydetailsID) {
        var empID = $('#updateID').val();
        $('#empfamilydetailsAttachID').val(empfamilydetailsID);
        $('#empIDFamilyAttach').val(empID);
        $('#modaluploadattachment').modal('show');
        fetch_family_attachment_details(empfamilydetailsID);
    }

    function fetch_family_attachment_details(empfamilydetailsID, selectedID=null) {
        Otable = $('#family_attachment_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_family_attachment_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "searching": false,
            "bLengthChange": false,
            "columnDefs": [
                {}
            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['expenseClaimCategoriesAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "attachmentID"},
                {"mData": "desc"},
                {"mData": "document"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "empFamilyDetailsID", "value": empfamilydetailsID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function delete_familydetail(id) {
        var empID = <?php echo current_userID(); ?>;
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                /* title: "Are you sure", /!*Are you sure?*!/
                 text: "You want to delete this record", /!*You want to delete this record!*!/
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#DD6B55",
                 confirmButtonText: "Delete" /!*Delete*!/,
                 cancelButtonText: "cancel" /!*cancel *!/*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'empfamilydetailsID': id},
                    url: "<?php echo site_url('Employee/delete_familydetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_family_details(empID);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function modaluploadimages(empfamilydetailsID) {
        $('#empfamilydetailzID').val(empfamilydetailsID);
        $('#modaluploadimages').modal('show');

    }

    function familyimage_uplode() {
        var empID = <?php echo current_userID(); ?>;
        var formData = new FormData($("#family_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/familyimage_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_family_details(empID);
                    $('#modaluploadimages').modal('hide');
                }
                $('#family_image_uplode_form')[0].reset();


            },
            error: function (data) {
                stopLoad();
                myAlert('e', 'Please contact support Team');
            }
        });
        return false;
    }

    function familyattachment_uplode() {
        var empfamilydetailsID = $('#empfamilydetailsAttachID').val();
        var formData = new FormData($("#family_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/familyattachment_uplode'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_family_attachment_details(empfamilydetailsID);
                    //$('#modaluploadattachment').modal('hide');
                    $('#family_attachment_uplode_form')[0].reset();
                }


            },
            error: function (data) {
                stopLoad();
                myAlert('e', 'Please contact support Team');
            }
        });
        return false;
    }


    function delete_family_attachment(id, empFamilyDetailsID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                /*title: "Are you sure", /!*Are you sure?*!/
                 text: "You want to delete this record", /!*You want to delete this record!*!/
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#DD6B55",
                 confirmButtonText: "Delete" /!*Delete*!/,
                 cancelButtonText: "cancel" /!*cancel *!/*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id},
                    url: "<?php echo site_url('Employee/delete_family_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_family_attachment_details(empFamilyDetailsID);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function fetch_qualification(subTab='') {
        var empID = '<?php echo current_userID() ?>';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, isFrom: 'profile', subTab: subTab},
            url: '<?php echo site_url("Employee/load_empQualificationView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var empID = '<?php echo current_userID(); ?>';
                $('#qualification_tab').html(data + '<input type="hidden" id="updateID" value="' + empID + '" />');
                if (fromHiarachy == 1) {
                    $('.btn ').addClass('hidden');
                    $('.navdisabl ').removeClass('hidden');
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function fetch_employee_assets() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {empID: '<?php echo current_userID() ?>'},
            url: "<?php echo site_url('Employee/fetch_employee_assets/frm_profile'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#assets_tab").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#assets_tab").html('<div class="alert alert-danger"><?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.<br/><strong><?php echo $this->lang->line('emp_error_message');?> : </strong>' + errorThrown + '</div>');<!--An Error Occurred! Please Try Again-->/*Error Message*/
                stopLoad();
            }
        });
    }


    document.getElementById('changeProfilePic').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });

    document.getElementById('fileInput').addEventListener('change', function(event) {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        const formData = new FormData();
        formData.append('empID', '<?php echo current_userID() ?>'); 
        formData.append('empImage', selectedFile);

        $.ajax({
            async: true,
            type: 'POST',
            url: "<?php echo site_url('Employee/updateEmpImage'); ?>",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                startLoad(); 
            },
            success: function (response) {
                stopLoad(); 
                if (response.status === 's') {
                    myAlert('s', response.message); 
                    fetchPage('system/profile/profile_information', '0', 'Profile');
                    if (response.imageURL) {
                        $('.current-user-img').attr('src', response.imageURL);
                        $('.img-thumbnail').attr('src', response.imageURL);
                    }else{
                        myAlert('Image URL no');
                    }
                } else {
                    myAlert('e', response.message); 
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Failed to update image');
            }
        });
    } else {
        myAlert('e', 'No file selected');
    }
});

</script>

