<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$showAddBtn = true;
function url_exists($url)
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

<div id="familyMasterContainer">
    <?php
    if (!empty($empArray)) {

        echo '<h4>Family Detail</h4>';

        foreach ($empArray as $familyInfo) {
            ?>
            <div class="container-fluid familyMasterContainer"
                 id="specificfamilydetail<?php echo $familyInfo['empfamilydetailsID'] ?>">

                <div class="row">
                    <div class="col-md-7">
                        <div class="familyContainer">

                            <h5 style="color:#002100">
                                <?php
                                echo isset($familyInfo['relationshipDesc']) ? $familyInfo['relationshipDesc'] : '-';
                                ?>
                            </h5>
                            <table class="table table-condensed">
                                <tr>
                                    <td>Name :</td>
                                    <td><?php echo $familyInfo['name'] ?></td>
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

                                        ?>

                                        <?php echo isset($familyInfo['countryName']) ? $familyInfo['countryName'] : '-'; ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td>Date of Birth :</td>
                                    <td>
                                        <?php
                                        if (isset($familyInfo['DOB'])) {
                                            if (!empty($familyInfo['DOB']) && $familyInfo['DOB'] != '0000-00-00 00:00:00') {
                                                echo format_date_dob($familyInfo['DOB']);
                                            }
                                        }
                                        ?>
                                        &nbsp;
                                    </td>

                                </tr>
                                <tr>
                                    <td>Gender :</td>
                                    <td>
                                        <?php echo isset($familyInfo['genderDesc']) ? $familyInfo['genderDesc'] : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>National No. :</td>
                                    <td>
                                        <?php echo isset($familyInfo['nationalCode']) ? $familyInfo['nationalCode'] : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ID No. :</td>
                                    <td>
                                        <?php echo isset($familyInfo['idNO']) ? $familyInfo['idNO'] : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Passport No :</td>
                                    <td><?php echo isset($familyInfo['passportNo']) ? $familyInfo['passportNo'] : '-';?></td>
                                </tr>
                                <tr>
                                    <td>Passport Expiry Date
                                        :
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($familyInfo['passportExpiredate'])) {
                                            if (!empty($familyInfo['passportExpiredate']) && $familyInfo['passportExpiredate'] != '0000-00-00 00:00:00') {
                                                echo format_date_other($familyInfo['passportExpiredate']);
                                            }
                                        }else{
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Visa No :</td>
                                    <td><?php echo isset($familyInfo['VisaNo']) ? $familyInfo['VisaNo'] : '-'; ?></td>
                                </tr>
                                <tr>
                                    <td>Visa Expiry Date
                                        :
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($familyInfo['VisaexpireDate'])) {
                                            if (!empty($familyInfo['VisaexpireDate']) && $familyInfo['VisaexpireDate'] != '0000-00-00 00:00:00') {
                                                echo format_date_other($familyInfo['VisaexpireDate']);
                                            }
                                        }else{
                                            echo '-';
                                        }
                                        ?>
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
                                    <td>
                                        Insurance Category :
                                    </td>
                                    <td>
                                        <?php
                                        echo isset($familyInfo['insuranceCategory']) ? $familyInfo['insuranDesc'] : '-';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Insurance Code :</td>
                                    <td>
                                        <?php echo isset($familyInfo['insuranceCode']) ? $familyInfo['insuranceCode'] : '-'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cover From :</td>
                                    <td>
                                        <?php
                                        if (isset($familyInfo['coverFrom'])) {
                                            if (!empty($familyInfo['coverFrom']) && $familyInfo['coverFrom'] != '0000-00-00 00:00:00') {
                                                echo format_date_other($familyInfo['coverFrom']);
                                            }
                                        }else{
                                            echo '-';
                                        }
                                        ?>
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
                <span class="sr-only">Not Found:</span>
                No Records Found!
            </div>
        </div>
        <?php exit;
    }
    ?>
</div>

