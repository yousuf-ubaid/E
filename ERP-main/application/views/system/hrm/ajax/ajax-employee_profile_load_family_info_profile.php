<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$showAddBtn = true;
$isNeedApproval = getPolicyValues('EPD', 'All');

?>
<style>
    .select2-container {
        min-width: 150px !important;
    }

    .select2-dropdown--below {
    / / select2-container min-width: 150 px !important;
        z-index: 2000 !important;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <?php if ($showAddBtn == true) { ?>
            <a onclick="addfamilydetails()" data-toggle="modal"
               class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"> </i>
              <?php echo $this->lang->line('common_add');?>   <!--Add--></a>
        <?php } ?>
    </div>
</div>
<hr>
<div id="familyMasterContainer">
    <?php
    $familydetail=$this->lang->line('emp_familydetail');

    if (!empty($empArray)) {

        echo '<h4>'.$familydetail.'<!--Family Detail--></h4>';

        foreach ($empArray as $familyInfo) {
            if ($isNeedApproval == 1) {
                $pendingData = get_pendingFamilyApprovalData($familyInfo['empfamilydetailsID']);
            }
            ?>
            <div class="container-fluid familyMasterContainer"
                 id="specificfamilydetail<?php echo $familyInfo['empfamilydetailsID'] ?>">

                <div class="row">
                    <?php if ($familyInfo['approvedYN'] == 0) {
                        ?>
                        <span id="msg-div" style="display: block; margin-left: 2px;">
                          <?php echo $this->lang->line('emp_master_pending_for_approval');?> <!-- Pending for Approval-->
                        </span>
                        <?php
                    }
                    ?>
                    <span style="margin: 5px;"
                          class="pull-right btn-group"> <?php if ($showAddBtn == true) { ?>
                            <button id="edit" style="margin-right: 5px;" title="Attachment" rel="tooltip"
                                    class="btn btn-xs btn-primary"
                                    onclick="attach_familydetail(<?php echo $familyInfo["empfamilydetailsID"]; ?>)">
                                <i class="fa fa-paperclip"></i>
                            </button>
                            <button id="edit" title="Delete" rel="tooltip"
                                    class="btn btn-xs btn-danger"
                                    onclick="delete_familydetail(<?php echo $familyInfo["empfamilydetailsID"]; ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php } ?> </span>
                    <div class="col-md-7">
                        <div class="familyContainer">

                            <table class="table table-condensed">
                                <!--border="0" cellpadding="10" cellspacing="0" width="100%"-->
                                <!---->
                                <tr>
                                    <td id="_relationship"><?php echo $this->lang->line('emp_master_relationship');?><!--Relationship--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'relationship', 1) : null;

                                        if (!empty($isPending)) {
                                            $titleID = $isPending[0];
                                            $description = $isPending[1];
                                            echo "<script> colorLabel('_relationship'); </script>";
                                        } else {
                                            $titleID = (empty($familyInfo['relationship'])) ? '' : $familyInfo['relationship'];
                                            $description = (empty($familyInfo['relationshipDesc'])) ? '' : $familyInfo['relationshipDesc'];
                                        }
                                        ?>
                                        <a href="#" data-type="select2"
                                           data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="relationship"
                                           data-title="Relationship Status"
                                           class="relationshipDrop"
                                           data-placement="right"
                                           data-value="<?php echo $titleID; ?>">
                                            <?php
                                            echo $description;
                                            ?>
                                        </a>

                                    </td>
                                </tr>
                                <tr>
                                    <td id="_name"><?php echo $this->lang->line('common_name');?><!--Name--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'name') : null;

                                        if (!empty($isPending)) {
                                            $name = $isPending;
                                            echo "<script> colorLabel('_name'); </script>";
                                        } else {
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
                                    <td id="_nationality"><?php echo $this->lang->line('common_nationality');?><!--Nationality--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'nationality', 1) : null;
                                        $filename = trim($familyInfo['flag_img'] ?? '');
                                        if (!empty($isPending)) {
                                            $titleID = $isPending[0];
                                            $description = $isPending[1];
                                            $filename = $isPending[2];

                                            $filename = ($filename != '')? "/images/flags/{$filename}.png": '';
                                            echo "<script> colorLabel('_nationality'); </script>";
                                        } else {
                                            $titleID = (empty($familyInfo['nationality'])) ? '' : $familyInfo['nationality'];
                                            $description = (empty($familyInfo['countryName'])) ? '' : $familyInfo['countryName'];

                                            $filename = ($filename != '')? "/images/flags/{$filename}.png": '';
                                        }

                                        echo  (!empty($filename))? '<img src="' . base_url($filename) . '"/>': '';
                                        ?>
                                        <a href="#" data-type="select2"
                                           data-url="<?php echo site_url('Profile/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="nationality" data-title="Nationality" class="countryDrop"
                                           data-value="<?php echo $titleID; ?>">
                                            <?php echo $description; ?>
                                        </a>
                                    </td>

                                </tr>
                                <tr>
                                    <td id="_DOB"><?php echo $this->lang->line('emp_date_of_birth');?><!--Date of Birth--> :</td>
                                    <td>
                                        <?php
                                        $dob = '0000-00-00';
                                        $dob2 = '0000-00-00';

                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'DOB') : null;
                                        if (!empty($isPending)) {
                                            $dob = format_date_dob($isPending);
                                            $dob2 = $isPending;
                                            echo "<script> colorLabel('_DOB'); </script>";
                                        } else {
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
                                    <td id="_gender"><?php echo $this->lang->line('common_gender');?><!--Gender--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'gender', 1) : null;

                                        if (!empty($isPending)) {
                                            $titleID = $isPending[0];
                                            $description = $isPending[1];
                                            echo "<script> colorLabel('_gender'); </script>";
                                        } else {
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
                                    <td id="_nationalCode"><?php echo $this->lang->line('common_national_no');?><!--National No-->. :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'nationalCode') : null;

                                        if (!empty($isPending)) {
                                            $nationalCode = $isPending;
                                            echo "<script> colorLabel('_nationalCode'); </script>";
                                        } else {
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
                                    <td id="_idNO"><?php echo $this->lang->line('common_id_no');?><!--ID No-->. :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'idNO') : null;

                                        if (!empty($isPending)) {
                                            $idNO = $isPending;
                                            echo "<script> colorLabel('_idNO'); </script>";
                                        } else {
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
                                    <td id="_passportNo"><?php echo $this->lang->line('common_passport_number_no');?><!--Passport No--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'passportNo') : null;

                                        if (!empty($isPending)) {
                                            $passportNo = $isPending;
                                            echo "<script> colorLabel('_passportNo'); </script>";
                                        } else {
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
                                    <td><?php echo $this->lang->line('emp_passport_expiry_date');?><!--Passport Expiry Date-->
                                        :
                                    </td>
                                    <td id="_passportExpiredate">
                                        <?php
                                        $pob = '0000-00-00';
                                        $pob2 = '0000-00-00';

                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'passportExpiredate') : null;
                                        if (!empty($isPending)) {
                                            $pob = format_date_dob($isPending);
                                            $pob2 = $isPending;
                                            echo "<script> colorLabel('_passportExpiredate'); </script>";
                                        } else {
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
                                    <td id="_VisaNo"><?php echo $this->lang->line('emp_visa_no');?><!--Visa No--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'VisaNo') : null;

                                        if (!empty($isPending)) {
                                            $VisaNo = $isPending;
                                            echo "<script> colorLabel('_VisaNo'); </script>";
                                        } else {
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
                                    <td id="_VisaexpireDate"><?php echo $this->lang->line('emp_visa_expiry_date');?><!--Visa Expiry Date-->
                                        :
                                    </td>
                                    <td>
                                        <?php
                                        $vob = '0000-00-00';
                                        $vob2 = '0000-00-00';

                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'VisaexpireDate') : null;
                                        if (!empty($isPending)) {
                                            $vob = format_date_dob($isPending);
                                            $vob2 = $isPending;
                                            echo "<script> colorLabel('_VisaexpireDate'); </script>";
                                        } else {
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
                                <img class="familyImgSize" style="height: 185px;" src="<?=$familyInfo['image'] ?>" alt="image">
                                <button onclick="modaluploadimages(<?php echo $familyInfo['empfamilydetailsID'] ?>)"
                                    class="update btn btn-warning btn-xs" type="button" value="Update">
                                    <i class="fa fa-upload"></i>
                                </button>
                            </div>
                            <table class="table table-condensed">
                                <tr>
                                    <td id="_insuranceCategory">
                                        <?php echo $this->lang->line('emp_master_insurance_category');?>     <!-- Insurance Category--> :
                                    </td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'insuranceCategory', 1) : null;

                                        if (!empty($isPending)) {
                                            $titleID = $isPending[0];
                                            $description = $isPending[1];
                                            echo "<script> colorLabel('_insuranceCategory'); </script>";
                                        } else {
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
                                    <td id="_insuranceCode"><?php echo $this->lang->line('common_insurance_code');?> <!--Insurance Code--> :</td>
                                    <td>
                                        <?php
                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'insuranceCode') : null;

                                        if (!empty($isPending)) {
                                            $insuranceCode = $isPending;
                                            echo "<script> colorLabel('_insuranceCode'); </script>";
                                        } else {
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
                                    <td id="_coverFrom"><?php echo $this->lang->line('common_cover_from');?><!--Cover From--> :</td>
                                    <td>
                                        <?php
                                        $cob = '0000-00-00';
                                        $cob2 = '0000-00-00';

                                        $isPending = ($isNeedApproval == 1) ? search_pendingData($pendingData, 'coverFrom') : null;
                                        if (!empty($isPending)) {
                                            $cob = format_date_dob($isPending);
                                            $cob2 = $isPending;
                                            echo "<script> colorLabel('_coverFrom'); </script>";
                                        } else {
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
    } else {

        ?>
        <div id="familydetails" style="">
            <div class="alert alert-danger" role="alert">
                <span class="fa fa-exclamation-circle" aria-hidden="true"></span>
                <span class="sr-only"><?php echo $this->lang->line('common_not_found');?>:</span><!--Not Found-->
                <?php echo $this->lang->line('emp_master_no_record_found');?> <!--No Records Found!-->
            </div>
        </div>
        <?php exit;
    }
    ?>
</div>


<script>
    $(document).ready(function () {
        //$('.select2').select2();

        //$('.xeditable').editable();
        $('.xeditable').editable({
            success: function(response, newValue) {
                fetch_family_details()
            }
        });

        $('.xeditableDate').editable({
            format: 'YYYY-MM-DD',
            viewformat: 'DD.MM.YYYY',
            template: 'D / MMMM / YYYY',
            success: function(response, newValue) {
                fetch_family_details()
            },
            combodate: {
                minYear: <?php echo format_date_getYear() - 80 ?>,
                maxYear: <?php echo format_date_getYear() + 10 ?>,
                minuteStep: 1
            }
        });

        $('.genderDrop').editable({
            source: [
                <?php
                $result = load_gender_drop();
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
            success: function(response, newValue) {
                fetch_family_details()
            }
        });

        $('.insuranceCategoryDrop').editable({
            source: [
                <?php
                $insuranceCategory = get_hrms_insuranceCategory();
                if (!empty($insuranceCategory)) {
                    $i = 1;
                    $count = count($insuranceCategory);
                    foreach ($insuranceCategory as $val) {
                        echo "{id: '" . $val['insurancecategoryID'] . "', text: '" . trim($val['description'] ?? '') . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function(response, newValue) {
                fetch_family_details()
            }
        });

        $('.countryDrop').editable({
            source: [
                <?php
                $result = load_nationality_drop();
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
            success: function(response, newValue) {
                fetch_family_details()
            }
        });


        $('.relationshipDrop').editable({
            source: [
                <?php
                $getResult = get_hrms_relationship();
                if (!empty($getResult)) {
                    $i = 1;
                    $count = count($getResult);
                    foreach ($getResult as $val) {
                        echo "{id: '" . $val['relationshipID'] . "', text: '" . trim($val['relationship'] ?? '') . "'} ";
                        if ($count != $i) {
                            echo ',';
                        }
                        $i++;
                    }
                }
                ?>
            ],
            success: function(response, newValue) {
                fetch_family_details()
            }


        });


    });
    /*if(fromHiarachy == 1){
     $('.btn ').addClass('hidden');
     $('.navdisabl ').removeClass('hidden');
     //$('.xeditable').editable();
     $('.xeditable').editable('destroy');
     $('.xeditableDate').editable('destroy');
     $('.genderDrop').editable('option', 'disabled', true);
     $('.insuranceCategoryDrop').editable('option', 'disabled', true);
     $('.countryDrop').editable('option', 'disabled', true);
     $('.relationshipDrop').editable('option', 'disabled', true);
     }*/


</script>

