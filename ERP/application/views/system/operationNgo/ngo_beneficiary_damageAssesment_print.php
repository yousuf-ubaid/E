<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->load->helper('crm_helper');
$this->lang->load('common', $primaryLanguage);
$newurl = explode("/", $_SERVER['SCRIPT_NAME']);
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
                                     src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <br><br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:100%;font-size: 16px;font-weight: 700;">
                    <strong><?php echo $extra['nameWithInitials'] . " - " . $extra['systemCode']; ?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
            <strong> Country &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong><?php echo $extra['CountryDes'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
            <strong> Province &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong> <?php echo $extra['provinceNam'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
            <strong> District &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong> <?php echo $extra['districtName'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
            <strong> Division &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong><?php echo $extra['divisionName'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
            <strong> Sub Division : </strong><?php echo $extra['subDivisionName'] ?>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="reportHeaderColor" style="font-size: 15px;font-family: tahoma; font-weight: 900">Summary</div>
        </div>
    </div>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr style="background-color: #E6EED5">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>D.O.B</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong><?php echo $extra['dateOfBirth']; ?></strong></td>
            </tr>
            <tr style="background-color: #FFFFFF">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>NIC No</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;"><strong><?php echo $extra['NIC']; ?></strong></td>
            </tr>
            <tr style="background-color: #E6EED5">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Family Details</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong><?php echo $extra['familyMembersDetail']; ?></strong></td>
            </tr>
            <tr style="background-color: #FFFFFF">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Phone (Primary)</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;"><strong><?php echo $extra['phoneCountryCodePrimary']." - ".$extra['phoneAreaCodePrimary'].$extra['phonePrimary']; ?></strong>
                </td>
            </tr>
            <tr style="background-color: #E6EED5">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Ethnicity</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong><?php echo $extra['Ethnicity']; ?></strong></td>
            </tr>
            <tr style="background-color: #FFFFFF">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Occupation</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong>
                        <?php echo $extra['Occupation']; ?>
                    </strong>
                </td>
            </tr>
            <tr style="background-color: #E6EED5">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Economic Status</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;"><strong><?php echo $extra['econShortCode']; ?></strong>
                </td>
            </tr>

            <tr style="background-color: #FFFFFF">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Eligible For Zakath</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong>
                        <?php
                        $Zakat = '';
                        if ($extra['eligibleForZakathYN'] == 1) {
                            $Zakat = 'Yes. ';
                        } else {
                            $Zakat = 'No. ';
                        }
                        echo $Zakat; ?>
                    </strong>
                </td>
            </tr>
            <tr style="background-color: #E6EED5">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Reason in Brief</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong><?php echo $extra['reasoninBrief']; ?></strong></td>
            </tr>
            <tr style="background-color: #FFFFFF">
                <td style="width:30%;font-size: 13px;font-weight: 700;"><strong>Documents Completed</strong></td>
                <td style="width:70%;font-size: 13px;font-weight: 500;">
                    <strong>
                        <?php
                        if ($extra['confirmedYN'] == 1) {
                            echo 'Yes.';
                        } else {
                            echo 'No.';
                        } ?>
                    </strong>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <br><br>
    <div class="table-responsive">
        <table style='width: 100%'>
            <tbody>
            <tr>
                <td width="48%"
                    style="border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;font-size: 15px;">
                    &nbsp;&nbsp;Photos
                </td>
                <td width="48%"
                    style="border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;font-size: 15px;">
                    Photos
                </td>
            </tr>
            <tr>
                <?php
                if (!empty($benImages)) {
                    foreach ($benImages as $benI) {
                        $bnimage = get_all_operationngo_images($benI['beneficiaryImage'],'uploads/ngo/beneficiaryImage/','beneficiary'); ?>
                        <td style="border-left: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;"
                            align="center" width="48%">&nbsp;&nbsp;<img alt="House" style="width: 100%;"
                                                                        src="<?php echo $bnimage; ?> ">
                        </td>
                        <?php
                    }
                }
                ?>
            </tr>
            </tbody>
        </table>
    </div>

<?php
