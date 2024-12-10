<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$showAddBtn = true;
$emiratesLang=getPolicyValues('LNG', 'All');
/*function url_exists($url)
{
    $url = str_replace("http://", "", $url);
    if (strstr($url, "/")) {
        $url = explode("/", $url, 2);
        $url[1] = "/" . $url[1];
    } else {
        $url = array($url, "/");
    }

    $fh = fsockopen($url[0], 80);
    if ($fh) {
        fputs($fh, "GET " . $url[1] . " HTTP/1.1\nHost:" . $url[0] . "\n\n");
        if (fread($fh, 22) == "HTTP/1.1 404 Not Found") {
            return FALSE;
        } else {
            return TRUE;
        }

    } else {
        return FALSE;
    }
}*/

?>
<style>
    .select2-container {
        min-width: 150px !important;
    }

    .select2-dropdown--below {
        min-width: 150px !important;
        z-index: 2000 !important;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <?php if ($showAddBtn == true) { ?>
        <a onclick="addfamilydetails()" data-toggle="modal"
           class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"> </i>
            <?php echo $this->lang->line('common_add'); ?> </a>
        <?php } ?><!-- Add-->
    </div>
</div>
<hr>
<div id="familyMasterContainer">
    <!--Family Detail-->
    <?php
    if (!empty($empArray)) {

        $famdetail = $this->lang->line('emp_familydetail');

        echo '<h4>' . $famdetail . '</h4>';

        foreach ($empArray as $familyInfo) {
            ?>

            <div class="container-fluid familyMasterContainer"
                 id="specificfamilydetail<?php echo $familyInfo['empfamilydetailsID'] ?>">

                <div class="row">
                    <span style="margin: 5px;" class="pull-right btn-group">
                        <?php if ($showAddBtn == true) { ?>
                            <button id="edit" style="margin-right: 5px;" title="Attachment" rel="tooltip" class="btn btn-xs btn-primary"
                                 onclick="attach_familydetail(<?php echo $familyInfo["empfamilydetailsID"]; ?>)">
                                <i class="fa fa-paperclip"></i>
                            </button>
                            <button id="edit" title="Delete" rel="tooltip" class="btn btn-xs btn-danger"
                               onclick="delete_familydetail(<?php echo $familyInfo["empfamilydetailsID"]; ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php } ?>
                    </span>
                    <div class="col-md-7">
                        <div class="familyContainer">

                            <h5 style="color:#002100">
                                <a href="#" data-type="select2"
                                   data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                   data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                   data-name="relationship"
                                   data-title="Relationship Status"
                                   class="relationshipDrop"
                                   data-placement="right"
                                   data-value="<?php echo isset($familyInfo['relationship']) ? $familyInfo['relationship'] : ''; ?>">
                                    <?php
                                    echo isset($familyInfo['relationshipDesc']) ? $familyInfo['relationshipDesc'] : '';
                                    ?>
                                </a>

                            </h5>
                            <table class="table table-condensed">
                                <!--border="0" cellpadding="10" cellspacing="0" width="100%"-->
                                <!---->
                                <tr>
                                    <td><?php echo $this->lang->line('common_name'); ?> :</td><!--Name-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-placement="bottom"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="name"
                                           data-title="Name"
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['name']) ? $familyInfo['name'] : ''; ?>">
                                            <?php echo $familyInfo['name'] ?>
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?php echo $this->lang->line('common_nationality'); ?> :</td><!--Nationality-->
                                    <td>

                                        <?php

                                        if (!empty($familyInfo['flag_img'])) {
                                            $filename = base_url('images/flags/'.$familyInfo['flag_img']);
                                           /*if (url_exists($filename)) {*/
                                                echo '<img src="' . $filename . '" />';
                                            /*}*/
                                      }

                                        ?>
                                        <a href="#" data-type="select2"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="nationality"
                                           data-title="Nationality"
                                           class="countryDrop"
                                           data-value="<?php echo $familyInfo['nationality']; ?>">
                                            <?php echo $familyInfo['countryName']; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('common_date_of_birth'); ?> :</td>
                                    <!--Date of Birth-->
                                    <td>
                                        <a href="#" data-type="combodate"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="DOB"
                                           data-title="Date of Birth"
                                           class="xeditableDate"
                                           data-value="<?php if (!empty($familyInfo['DOB']) && $familyInfo['DOB'] != '0000-00-00 00:00:00') {

                                               echo format_date($familyInfo['DOB']);
                                           } ?>">
                                            <?php
                                            if (isset($familyInfo['DOB'])) {
                                                if (!empty($familyInfo['DOB']) && $familyInfo['DOB'] != '0000-00-00 00:00:00') {
                                                    echo format_date_dob($familyInfo['DOB']);
                                                }
                                            }
                                            ?>
                                        </a>
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('common_gender'); ?> :</td><!--Gender-->
                                    <td>
                                        <a href="#" data-type="select2"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="gender"
                                           data-title="Gender"
                                           class="genderDrop"
                                           data-value="<?php echo isset($familyInfo['gender']) ? $familyInfo['gender'] : ''; ?>">
                                            <?php
                                            echo isset($familyInfo['genderDesc']) ? $familyInfo['genderDesc'] : '';
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('common_national_no'); ?>. :</td><!--National No-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="nationalCode"
                                           data-title="National No."
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['nationalCode']) ? $familyInfo['nationalCode'] : ''; ?>">
                                            <?php
                                            echo isset($familyInfo['nationalCode']) ? $familyInfo['nationalCode'] : '';
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="control-label" for="id_no">
                                                <?php
                                                    if (in_array($emiratesLang, ['MSE', 'SOP', 'GCC', 'ASAAS', 'Flowserve'])) {
                                                        echo $this->lang->line('common_emirates_no');
                                                    } else {
                                                        echo $this->lang->line('common_national_no');
                                                    }
                                                ?>
                                        </label>        
                                    </td><!--ID No-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="idNO"
                                           data-title="ID No.  "
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['idNO']) ? $familyInfo['idNO'] : ''; ?>">
                                            <?php
                                            echo isset($familyInfo['idNO']) ? $familyInfo['idNO'] : '';
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                <tr>
                                    <td>
                                        <label class="control-label" for="id_no">
                                            <?php
                                                if (in_array($emiratesLang, ['MSE', 'SOP', 'GCC', 'ASAAS', 'Flowserve'])) {
                                                    echo $this->lang->line('emp_emirate_expiry_date');
                                                } else {
                                                    echo $this->lang->line('emp_id_expiry_date');
                                                }
                                            ?>
                                        </label>    
                                    </td><!--ID expiry date-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="idExpiryDate"
                                           data-title="ID No.  "
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['idExpiryDate']) ? $familyInfo['idExpiryDate'] : ''; ?>">
                                            <?php
                                            echo isset($familyInfo['idExpiryDate']) ? $familyInfo['idExpiryDate'] : '';
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('common_passport_number_no'); ?> :</td>
                                    <!--Passport No-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="passportNo"
                                           data-title="Passport No"
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['passportNo']) ? $familyInfo['passportNo'] : ''; ?>">
                                            <?php echo $familyInfo['passportNo'] ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('emp_passport_expiry_date'); ?>
                                        :
                                    </td><!--Passport Expiry Date-->
                                    <td>
                                        <a href="#" data-type="combodate"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="passportExpiredate"
                                           data-title="Passport Expire Date"
                                           class="xeditableDate"
                                           data-value="<?php if (!empty($familyInfo['passportExpiredate']) && $familyInfo['passportExpiredate'] != '0000-00-00 00:00:00') {
                                               echo format_date($familyInfo['passportExpiredate']);
                                           } ?>">
                                            <?php
                                            if (isset($familyInfo['passportExpiredate'])) {
                                                if (!empty($familyInfo['passportExpiredate']) && $familyInfo['passportExpiredate'] != '0000-00-00 00:00:00') {
                                                    echo format_date_other($familyInfo['passportExpiredate']);
                                                }
                                            }
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('emp_visa_no'); ?> :</td><!--Visa No-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="VisaNo"
                                           data-title="Visa No"
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['VisaNo']) ? $familyInfo['VisaNo'] : ''; ?>">
                                            <?php echo $familyInfo['VisaNo'] ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('emp_visa_expiry_date'); ?>
                                        :
                                    </td><!--Visa Expiry Date-->
                                    <td>
                                        <a href="#" data-type="combodate"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="VisaexpireDate"
                                           data-title="Visa Expire Date"
                                           class="xeditableDate"
                                           data-value="<?php if (!empty($familyInfo['VisaexpireDate']) && $familyInfo['VisaexpireDate'] != '0000-00-00 00:00:00') {
                                               echo format_date($familyInfo['VisaexpireDate']);
                                           } ?>">
                                            <?php
                                            if (isset($familyInfo['VisaexpireDate'])) {
                                                if (!empty($familyInfo['VisaexpireDate']) && $familyInfo['VisaexpireDate'] != '0000-00-00 00:00:00') {
                                                    echo format_date_other($familyInfo['VisaexpireDate']);
                                                }
                                            }
                                            ?>
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
                                <?php if (!empty($familyInfo['image'])) {
                                    //$image = document_uploads_family_url() . $familyInfo['image'];
                                    $image = $familyInfo['image'];
                                    ?>
                                    <img class="familyImgSize" style="height: 185px;"
                                         src="<?=$familyInfo['image'] ?>"
                                         alt="image">
                                <?php } else { ?>
                                    <img class="familyImgSize" style="height: 185px;"
                                         src="<?php echo base_url('../gs_sme/images/no_image.jpg'); ?>"
                                         alt="image">
                                <?php } ?>
                                <button
                                    onclick="modaluploadimages(<?php echo $familyInfo['empfamilydetailsID'] ?>)"
                                    class="update btn btn-warning btn-xs"
                                    type="button" value="Update"><i
                                        class="fa fa-upload"></i></button>
                            </div>
                            <table class="table table-condensed">
                                <tr>
                                    <td>
                                        <?php echo $this->lang->line('emp_insurance_category'); ?> :
                                    </td><!--Insurance Category-->
                                    <td>
                                        <a href="#" data-type="select2"
                                           data-placement="left"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="insuranceCategory"
                                           data-title="Insurance Category "
                                           class="insuranceCategoryDrop"
                                           data-value="<?php echo isset($familyInfo['insuranceCategory']) ? $familyInfo['insuranceCategory'] : ''; ?>">
                                            <?php
                                            echo isset($familyInfo['insuranceCategory']) ? $familyInfo['insuranDesc'] : '';
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('common_insurance_code'); ?> :</td>
                                    <!--Insurance Code-->
                                    <td>
                                        <a href="#" data-type="text"
                                           data-placement="left"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="insuranceCode"
                                           data-title="Insurance Code  "
                                           class="xeditable"
                                           data-value="<?php echo isset($familyInfo['insuranceCode']) ? $familyInfo['insuranceCode'] : ''; ?>">
                                            <?php echo $familyInfo['insuranceCode'] ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->lang->line('emp_cover_form'); ?> :</td><!--Cover From-->
                                    <td>
                                        <a href="#" data-type="combodate"
                                           data-placement="left"
                                           data-url="<?php echo site_url('Employee/ajax_update_familydetails') ?>"
                                           data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                           data-name="coverFrom"
                                           data-title="Cover From"
                                           class="xeditableDate"
                                           data-value="<?php if (!empty($familyInfo['coverFrom']) && $familyInfo['coverFrom'] != '0000-00-00 00:00:00') {
                                               echo format_date($familyInfo['coverFrom']);
                                           } ?>">
                                            <?php
                                            if (isset($familyInfo['coverFrom'])) {
                                                if (!empty($familyInfo['coverFrom']) && $familyInfo['coverFrom'] != '0000-00-00 00:00:00') {
                                                    echo format_date_other($familyInfo['coverFrom']);
                                                }
                                            }
                                            ?>
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
                <span class="sr-only"><?php echo $this->lang->line('common_not_found'); ?>:</span><!--Not Found-->
                <?php echo $this->lang->line('common_no_records_found'); ?><!-- No Records Found!-->
            </div>
        </div>
        <?php exit;
    }
    ?>
</div>


<script>
    $(document).ready(function () {
        //$('.select2').select2();

        $('.xeditable').editable();

        $('.xeditableDate').editable({
            format: 'YYYY-MM-DD',
            viewformat: 'DD.MM.YYYY',
            template: 'D / MMMM / YYYY',
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
            ]
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
            ]
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
            ]
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
            ]


        });


    });

    if (fromHiarachy == 1) {
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
        //$('.xeditable').editable();
        $('.xeditable').editable('destroy');
        $('.xeditableDate').editable('destroy');
        $('.genderDrop').editable('option', 'disabled', true);
        $('.insuranceCategoryDrop').editable('option', 'disabled', true);
        $('.countryDrop').editable('option', 'disabled', true);
        $('.relationshipDrop').editable('option', 'disabled', true);
    }
</script>

