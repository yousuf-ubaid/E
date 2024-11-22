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
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr style="background-color: #E6EED5">
            <td style="font-size: 13px;font-weight: 700;"><strong>D.O.B</strong></td>
            <td style="font-size: 13px;font-weight: 500;" colspan="3">
                <strong><?php echo $extra['dateOfBirth']; ?></strong></td>
        </tr>
        <tr style="background-color: #FFFFFF">
            <td style="font-size: 13px;font-weight: 700;"><strong>NIC No</strong></td>
            <td style="font-size: 13px;font-weight: 500;" colspan="3"><strong><?php echo $extra['NIC']; ?></strong></td>
        </tr>
        <tr style="background-color: #E6EED5">
            <td style="font-size: 13px;font-weight: 700;"><strong>Family Details</strong></td>
            <td style="font-size: 13px;font-weight: 500;" colspan="3">
                <strong><?php echo $extra['FamilySystemCode'].' |'.$extra['FamilyName']; ?></strong></td>
        </tr>
        <tr style="background-color: #FFFFFF">
            <td style="font-size: 13px;font-weight: 700;"><strong>Province / State</strong>  </td>
            <td style="font-size: 13px;font-weight: 700;"><strong>
                    <?php echo $extra['provinceName']; ?>
                </strong> </td>
            <td style="font-size: 13px;font-weight: 500;"><strong>Area / District</strong>
            </td>
            <td style="font-size: 13px;font-weight: 700;"><strong>
                    <?php echo $extra['districtName']; ?>
                </strong> </td>
        </tr>
        <tr style="background-color: #E6EED5">
            <td style="font-size: 13px;font-weight: 700;"><strong>Division</strong>  </td>
            <td style="font-size: 13px;font-weight: 700;"><strong>
                    <?php echo $extra['divisionName']; ?>
                </strong> </td>
            <td style="font-size: 13px;font-weight: 500;"><strong>Mahalla</strong>
            </td>
            <td style="font-size: 13px;font-weight: 700;"><strong>
                    <?php echo $extra['subDivisionName']; ?>
                </strong> </td>
        </tr>
        <tr style="background-color: #FFFFFF">
            <td style="font-size: 13px;font-weight: 700;"><strong>Reason in Brief</strong></td>
            <td style="font-size: 13px;font-weight: 500;">
                <strong><?php echo $extra['reasoninBrief']; ?></strong></td>
        </tr>
        <tr style="background-color: #E6EED5">
            <td style="font-size: 13px;font-weight: 700;"><strong>Documents Completed</strong></td>
            <td style="font-size: 13px;font-weight: 500;" colspan="3">
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
                foreach ($benImages as $benI) { ?>
                    <td style="border-left: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;"
                        align="center" width="48%">&nbsp;&nbsp;<img alt="House" style="width: 100%;"
                                                                    src="<?php echo NGOImage ."beneficiaryImage/". $benI['beneficiaryImage']; ?> ">
                    </td>
                <?php
                }
            }
            ?>
        </tr>
        </tbody>
    </table>
</div>