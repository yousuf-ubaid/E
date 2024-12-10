<?php
//$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('employee_master', $primaryLanguage);
$showAddBtn = true;
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

if ($type == 'edit') {
    $showAddBtn = true;
} else {
    $showAddBtn = false;
}
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
<?php
if (!empty($benificiaryArray)) {

    foreach ($benificiaryArray as $familyInfo) {
        if ($familyInfo != 0) {
            ?>
            <div class="container-fluid familyMasterContainer"
                 id="specificfamilydetail<?php echo $familyInfo['empfamilydetailsID'] ?>">

                <div class="row">
    <span style="margin: 5px;"
          class="pull-right btn-group"> <?php if ($showAddBtn == true) { ?>
            <button id="edit" title="Delete" rel="tooltip"
                    class="btn btn-xs btn-danger"
                    onclick="delete_beneficiary_familydetail(<?php echo $familyInfo["empfamilydetailsID"]; ?>)">
                <i class="fa fa-trash"></i>
            </button>
        <?php } ?> </span>
                <span style="margin: 5px;"
                      class="pull-right btn-group"> <?php if ($showAddBtn == true) { ?>
                        <button id="edit_fam_detail" title="edit" rel="tooltip"
                                class="btn btn-xs btn-primary"
                                onclick="edit_beneficiary_familydetail(<?php echo $familyInfo["empfamilydetailsID"]; ?>)">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </button>
                    <?php } ?> </span>

                    <div class="col-md-7">
                        <div class="familyContainer">
                            <h5 style="color:#002100">


                            </h5>
                            <table class="table table-condensed">
                                <!--border="0" cellpadding="10" cellspacing="0" width="100%"-->
                                <!---->
                                <tr>
                                    <td>Name :</td>
                                    <td>
                                        <?php if ($showAddBtn == true) { ?>
                                            <a href="#" data-type="text"
                                               data-placement="bottom"
                                               data-url="<?php echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') ?>"
                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                               data-name="name"
                                               data-title="Name"
                                               class="xeditable"
                                               data-value="<?php echo isset($familyInfo['name']) ? $familyInfo['name'] : ''; ?>">
                                                <?php echo $familyInfo['name'] ?>
                                            </a>
                                        <?php } else {
                                            echo $familyInfo['name'];
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Relationship :</td>
                                    <td>
                                        <?php if ($showAddBtn == true) { ?>
                                            <a href="#" data-type="select2"
                                               data-url="<?php echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') ?>"
                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                               data-name="relationship"
                                               data-title="Relationship Status"
                                               class="relationshipDrop"
                                               data-value="<?php echo isset($familyInfo['relationship']) ? $familyInfo['relationship'] : ''; ?>">
                                                <?php
                                                echo isset($familyInfo['relationshipDesc']) ? $familyInfo['relationshipDesc'] : '';
                                                ?>
                                            </a>
                                        <?php } else {
                                            echo isset($familyInfo['genderDesc']) ? $familyInfo['genderDesc'] : '';
                                        } ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Nationality :</td>
                                    <td>
                                        <?php
                                        $filename = '/gs_sme/images/flags/' . trim($familyInfo['countryName'] ?? '') . '.png';
                                        if (!empty($familyInfo['countryName'])) {
                                            if (url_exists($filename)) {
                                                echo '<img src="' . $filename . '" />';
                                            }
                                        }
                                        if ($showAddBtn == true) { ?>
                                            <a href="#" data-type="select2"
                                               data-url="<?php echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') ?>"
                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                               data-name="nationality"
                                               data-title="Nationality"
                                               class="countryDrop"
                                               data-value="<?php echo $familyInfo['nationality']; ?>">
                                                <?php echo $familyInfo['countryName']; ?>
                                            </a>
                                        <?php } else {
                                            echo $familyInfo['countryName'];
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date of Birth :</td>
                                    <td>
                                        <?php if ($showAddBtn == true) { ?>
                                            <a href="#" data-type="combodate"
                                               data-url="<?php echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') ?>"
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
                                        <?php } else {
                                            if (isset($familyInfo['DOB'])) {
                                                if (!empty($familyInfo['DOB']) && $familyInfo['DOB'] != '0000-00-00 00:00:00') {
                                                    echo format_date_dob($familyInfo['DOB']);
                                                }
                                            }
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender :</td>
                                    <td>
                                        <?php if ($showAddBtn == true) { ?>
                                            <a href="#" data-type="select2"
                                               data-url="<?php echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') ?>"
                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                               data-name="gender"
                                               data-title="Gender"
                                               class="genderDrop"
                                               data-value="<?php echo isset($familyInfo['gender']) ? $familyInfo['gender'] : ''; ?>">
                                                <?php
                                                echo isset($familyInfo['genderDesc']) ? $familyInfo['genderDesc'] : '';
                                                ?>
                                            </a>
                                        <?php } else {
                                            echo isset($familyInfo['genderDesc']) ? $familyInfo['genderDesc'] : '';
                                        } ?>
                                    </td>
                                </tr>
                                <!--                            <tr>
                                <td>National No. :</td>
                                <td>
                                    <a href="#" data-type="text"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="nationalCode"
                                       data-title="National No.  "
                                       class="xeditable"
                                       data-value="<?php /*echo isset($familyInfo['nationalCode']) ? $familyInfo['nationalCode'] : ''; */ ?>">
                                        <?php
                                /*                                        echo isset($familyInfo['nationalCode']) ? $familyInfo['nationalCode'] : '';
                                                                        */ ?>
                                    </a>
                                </td>
                            </tr>-->
                                <tr>
                                    <td>NIC No. :</td>
                                    <td>
                                        <?php if ($showAddBtn == true) { ?>
                                            <a href="#" data-type="text"
                                               data-url="<?php echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') ?>"
                                               data-pk="<?php echo $familyInfo['empfamilydetailsID'] ?>"
                                               data-name="idNO"
                                               data-title="ID No.  "
                                               class="xeditable"
                                               data-value="<?php echo isset($familyInfo['idNO']) ? $familyInfo['idNO'] : ''; ?>">
                                                <?php
                                                echo isset($familyInfo['idNO']) ? $familyInfo['idNO'] : '';
                                                ?>
                                            </a>
                                        <?php } else {
                                            echo isset($familyInfo['idNO']) ? $familyInfo['idNO'] : '';
                                        } ?>
                                    </td>
                                </tr>
                                <!--                            <tr>
                                <td>Passport No :</td>
                                <td>
                                    <a href="#" data-type="text"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="passportNo"
                                       data-title="Passport No"
                                       class="xeditable"
                                       data-value="<?php /*echo isset($familyInfo['passportNo']) ? $familyInfo['passportNo'] : ''; */ ?>">
                                        <?php /*echo $familyInfo['passportNo'] */ ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Passport Expiry Date
                                    :
                                </td>
                                <td>
                                    <a href="#" data-type="combodate"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="passportExpiredate"
                                       data-title="Passport Expire Date"
                                       class="xeditableDate"
                                       data-value="<?php /*if (!empty($familyInfo['passportExpiredate']) && $familyInfo['passportExpiredate'] != '0000-00-00 00:00:00') {
                                           echo format_date($familyInfo['passportExpiredate']);
                                       } */ ?>">
                                        <?php
                                /*                                        if (isset($familyInfo['passportExpiredate'])) {
                                                                            if (!empty($familyInfo['passportExpiredate']) && $familyInfo['passportExpiredate'] != '0000-00-00 00:00:00') {
                                                                                echo format_date_other($familyInfo['passportExpiredate']);
                                                                            }
                                                                        }
                                                                        */ ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Visa No :</td>
                                <td>
                                    <a href="#" data-type="text"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="VisaNo"
                                       data-title="Visa No"
                                       class="xeditable"
                                       data-value="<?php /*echo isset($familyInfo['VisaNo']) ? $familyInfo['VisaNo'] : ''; */ ?>">
                                        <?php /*echo $familyInfo['VisaNo'] */ ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Visa Expiry Date
                                    :
                                </td>
                                <td>
                                    <a href="#" data-type="combodate"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="VisaexpireDate"
                                       data-title="Visa Expire Date"
                                       class="xeditableDate"
                                       data-value="<?php /*if (!empty($familyInfo['VisaexpireDate']) && $familyInfo['VisaexpireDate'] != '0000-00-00 00:00:00') {
                                           echo format_date($familyInfo['VisaexpireDate']);
                                       } */ ?>">
                                        <?php
                                /*                                        if (isset($familyInfo['VisaexpireDate'])) {
                                                                            if (!empty($familyInfo['VisaexpireDate']) && $familyInfo['VisaexpireDate'] != '0000-00-00 00:00:00') {
                                                                                echo format_date_other($familyInfo['VisaexpireDate']);
                                                                            }
                                                                        }
                                                                        */ ?>
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                </td>
                            </tr>-->


                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="familyContainer">
                            <div class="show-image" style="text-align: center;">
                                <?php if (!empty($familyInfo['image'])) { ?>
                                    <img
                                        src="<?php echo base_url('uploads/NGO/beneficiaryFamilyImage/' . $familyInfo['image']); ?>"
                                        alt="image" style="width: 200px; height: 145px;">
                                <?php } else { ?>
                                    <img src="<?php echo base_url('images/item/no-image.png'); ?>" alt="image"
                                         style="width: 200px; height: 145px;">
                                <?php } ?>

                                <button
                                    onclick="modaluploadimages(<?php echo $familyInfo['empfamilydetailsID'] ?>)"
                                    class="update btn btn-warning btn-xs"
                                    type="button" value="Update"><i
                                        class="fa fa-upload"></i></button>

                            </div>
                            <!--                        <table class="table table-condensed">
                            <tr>
                                <td>Insurance Code :</td>
                                <td>
                                    <a href="#" data-type="text"
                                       data-placement="left"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="insuranceCode"
                                       data-title="Insurance Code  "
                                       class="xeditable"
                                       data-value="<?php /*echo isset($familyInfo['insuranceCode']) ? $familyInfo['insuranceCode'] : ''; */ ?>">
                                        <?php /*echo $familyInfo['insuranceCode'] */ ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Cover From :</td>
                                <td>
                                    <a href="#" data-type="combodate"
                                       data-placement="left"
                                       data-url="<?php /*echo site_url('OperationNgo/ajax_update_beneficiary_familydetails') */ ?>"
                                       data-pk="<?php /*echo $familyInfo['empfamilydetailsID'] */ ?>"
                                       data-name="coverFrom"
                                       data-title="Cover From"
                                       class="xeditableDate"
                                       data-value="<?php /*if (!empty($familyInfo['coverFrom']) && $familyInfo['coverFrom'] != '0000-00-00 00:00:00') {
                                           echo format_date($familyInfo['coverFrom']);
                                       } */ ?>">
                                        <?php
                            /*                                        if (isset($familyInfo['coverFrom'])) {
                                                                        if (!empty($familyInfo['coverFrom']) && $familyInfo['coverFrom'] != '0000-00-00 00:00:00') {
                                                                            echo format_date_other($familyInfo['coverFrom']);
                                                                        }
                                                                    }
                                                                    */ ?>
                                    </a>
                                </td>

                            </tr>-->
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <hr>
        <?php }
    }
} else {

    ?>
    <div id="familydetails" style="">
        <div class="alert alert-danger" role="alert">
            <span class="fa fa-exclamation-circle" aria-hidden="true"></span>
            <span class="sr-only">Not Found:</span>
            No Family Details Found!
        </div>
    </div>
    <?php exit;
}
?>


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

</script>

