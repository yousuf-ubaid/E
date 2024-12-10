<!---- =============================================
-- File Name : production_report.php
-- Project Name : SME ERP
-- Module Name : Report - Production Report
-- Create date : 27 - May 2019
-- Description : This file contains Buyback Production Report.

-- REVISION HISTORY
-- =============================================-->
<style>
    hr {
        margin-top: 0px;
        margin-bottom: 0px;
        border: 0;
        border-top: 1px solid #eee;
    }

    .dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .tbAlign{
        text-align: center;
        font-size: 12px;
        background-color: #4ea897;
        color: whitesmoke;
    }


    .ver3ticalText {

        -ms-writing-mode: tb-rl;
        -webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;
        transform: rotate(270deg);
        white-space: nowrap;
        height:100%;
    }


</style>
<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div class="row">
    <div class="col-md-6">
        <div style="font-size: 16px; font-weight: 700;"></div>
    </div>

    <div class="col-md-6 damageassestmentrptcls_cls">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_purchase_order_list', 'District Wise  Disaster Assessment Report');
        } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
        </div>
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?> </strong>
        </div>
        <div class="text-center reportHeaderColor">
            <strong>Tel : <?php echo $this->common_data['company_data']['company_phone'] ?> </strong>
        </div>

        <div class="text-center reportHeader reportHeaderColor">District Wise Disaster Assessment Report</div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-6">
        <strong> Project : </strong> <?php echo $project[0]['projectName'] ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-6">
        <strong>Filters <i class="fa fa-filter"></i></strong><br/>
        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> Country &nbsp;: </strong> <?php if (!empty($country_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($country_drop as $row) {
                    $tmpArray[] = $row['CountryDes'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>
        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> Province : </strong> <?php if (!empty($province_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($province_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>
        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> District &nbsp;&nbsp;: </strong> <?php if (!empty($area_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($area_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>

        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> Division &nbsp;: </strong> <?php if (!empty($da_division_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($da_division_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>
    </div>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="districtWiseDA">
        <div id="tbl_purchase_order_list">
            <div class="row hide">
                <div class="col-md-12">
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                    </div>
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?> </strong>
                    </div>
                    <div class="text-center reportHeaderColor">
                        <strong>Tel : <?php echo $this->common_data['company_data']['company_phone'] ?> </strong>
                    </div>

                    <div class="text-center reportHeader reportHeaderColor"><u>Disaster Assessment Report</u></div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <strong> Project : </strong> <?php echo $project[0]['projectName'] ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Filters <i class="fa fa-filter"></i></strong><br/>
                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> Country : </strong> <?php if (!empty($country_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($country_drop as $row) {
                                    $tmpArray[] = $row['CountryDes'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>
                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> Province : </strong> <?php if (!empty($province_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($province_drop as $row) {
                                    $tmpArray[] = $row['Description'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>
                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> District : </strong> <?php if (!empty($area_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($area_drop as $row) {
                                    $tmpArray[] = $row['Description'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>

                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> Division : </strong> <?php if (!empty($da_division_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($da_division_drop as $row) {
                                    $tmpArray[] = $row['Description'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <?php if (!empty($daDisWiseDistric)) { ?>
            <div class="row" style="margin-top:2px">
                <div class="form-group">
                    <div class="fixHeader_Div" style="overflow-x:auto;">
                        <table class="borderSpace report-table-condensed responsive" id="tbl_report" style="width: 100%;height: auto;" border="1">
                            <thead class="report-header">
                            <tr class="tbAlign">
                                <td rowspan="2">District</td>
                                <td rowspan="2">DS Division</td>
                                <td rowspan="2">Village</td>
                                <td rowspan="2" class="verticalText">Total # Of Muslim Families</td>
                                <td rowspan="2" class="verticalText">Families Directly Effected</td>
                                <td rowspan="2" class="verticalText">Economically Vulnerable <br> Families Directly <br> Effected</td>
                                <td colspan="3">House Damaged</td>
                                <td colspan="3">Business Places Damaged</td>
                                <td colspan="4">Masjid Damaged</td>
                                <td colspan="2">Common Facilities Damaged</td>
                                <td colspan="7">Vehicles Damaged</td>
                                <td rowspan="2" class="verticalText">Personal Died</td>
                                <td rowspan="2" class="verticalText">Personal Injured</td>
                                <td rowspan="2" class="verticalText" style="font-weight: bold;display: none;">Date Of Incident</td>
                            </tr>
                            <tr class="tbAlign"><td class="verticalText"># Of Houses</td><td class="verticalText">Fully</td><td class="verticalText">Partially</td>
                            <td class="verticalText"># Of Business Places</td><td>Fully/Mora Than 75%</td><td class="verticalText">Partially /Minor</td>
                            <td class="verticalText">Jumma Masjid</td><td class="verticalText">Name Of The Masjid</td><td>Thakiya Masjid</td><td>Name Of The Masjid</td>
                            <td class="verticalText"># Of Facilities</td><td class="verticalText">Name Of The Facilities</td>
                                <?php if (!empty($vehicleTypes)) {
                                    foreach ($vehicleTypes as $row) {

                                        echo'<td>'.$row['vehicleDescription'].'</td>';
                                    }
                                } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $x = 1;

                            if (!empty($daDisWiseDistric)) {
                                foreach ($daDisWiseDistric as $rowDis) {

                                    echo "<tr>";
                                    echo "<td>" . $rowDis["DAdistrict"] . "</td><td>";
                                    $daDisWiseArea = $this->db->query("SELECT DISTINCT smdsDivision.stateID as smdstateID,smdsDivision.Description as DAdivision
FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district LEFT JOIN srp_erp_statemaster smdsDivision ON smdsDivision.stateID = bm.division
WHERE
	bm.district={$rowDis['districID']} AND bm.projectID = {$rowDis['projectID']} ")->result();
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                    foreach ($daDisWiseArea as $rowDivi) {

                                        echo "<tr><td style='padding: 4px;' height='40px;'>" . $rowDivi->DAdivision . "</td></tr>";
                                    }
                                    echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision
FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district LEFT JOIN srp_erp_statemaster smdsDivision ON smdsDivision.stateID = bm.division LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision
WHERE
	bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            echo "<tr><td style='padding: 4px;' height='40px;'>" . $rowMah->DAsubDivision . "</td></tr>";
                                        }

                                    }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmEthnicityCount = $this->db->query("SELECT COUNT(*) AS ethnicityCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE bm.ethnicityID=2 AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;font-weight: bold;text-align: center;' height='40px;'>" . $bmEthnicityCount->ethnicityCount . "</td></tr>";
                                            }

                                        }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmEthnicityCount = $this->db->query("SELECT COUNT(*) AS ethnicityCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;font-weight: bold;text-align: center;' height='40px;'>" . $bmEthnicityCount->ethnicityCount . "</td></tr>";
                                            }

                                        }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmEconVuCount = $this->db->query("SELECT COUNT(*) AS econVulnerableCount
FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_ngo_economicstatusmaster ecMas ON bm.da_economicStatus=ecMas.economicStatusID WHERE bm.da_economicStatus IN (4,5) AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;font-weight: bold;' height='40px;'>" . $bmEconVuCount->econVulnerableCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmHSdamageCount = $this->db->query("SELECT COUNT(*) AS hsDamageCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE (bm.da_typeOfhouseDamage is not null OR bm.da_typeOfhouseDamage !='') AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;font-weight: bold;' height='40px;'>" . $bmHSdamageCount->hsDamageCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmHSdmgFCount = $this->db->query("SELECT COUNT(*) AS hsDmgFullyCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE (bm.da_typeOfhouseDamage is not null OR bm.da_typeOfhouseDamage !='') AND da_housingCondition=2 AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'>" . $bmHSdmgFCount->hsDmgFullyCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmHSdmgPCount = $this->db->query("SELECT COUNT(*) AS hsDmgFullyCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE (bm.da_typeOfhouseDamage is not null OR bm.da_typeOfhouseDamage !='') AND da_housingCondition=1 AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'>" . $bmHSdmgPCount->hsDmgFullyCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmBSdamageCount = $this->db->query("SELECT COUNT(*) AS bsDamageCount
FROM srp_erp_ngo_businessdamagedassesment busDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON busDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;font-weight: bold;' height='40px;'>" . $bmBSdamageCount->bsDamageCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmBSdmgFCount = $this->db->query("SELECT COUNT(*) AS bsDmgFullyCount
FROM srp_erp_ngo_businessdamagedassesment busDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON busDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND busDmgAss.damageConditionID ='19'")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'>" . $bmBSdmgFCount->bsDmgFullyCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmBSdmgPCount = $this->db->query("SELECT COUNT(*) AS bsDmgPrslyCount
FROM srp_erp_ngo_businessdamagedassesment busDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON busDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND busDmgAss.damageConditionID ='20'")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'>" . $bmBSdmgPCount->bsDmgPrslyCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $pmJummaCount = $this->db->query("SELECT COUNT(*) AS jummaCount
FROM srp_erp_ngo_publicpropertybeneficiarymaster pm WHERE pm.subDivision={$rowMah->DAsubDivId} AND pm.projectID = {$rowDis['projectID']} AND pm.propertyType='2' AND pm.subPropertyId='3'")->row();

                                                echo "<tr><td style='padding: 4px;font-weight: bold;text-align: center;' height='40px;'>" . $pmJummaCount->jummaCount . "</td></tr>";
                                            }

                                        }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $pmJummaCountDel = $this->db->query("SELECT pm.PropertyName
FROM srp_erp_ngo_publicpropertybeneficiarymaster pm WHERE pm.subDivision={$rowMah->DAsubDivId} AND pm.projectID = {$rowDis['projectID']} AND pm.propertyType='2' AND pm.subPropertyId='3'")->result();
                                                $rowOTjmPDel = array();
                                                foreach ($pmJummaCountDel as $row_JummaCountDel) {

                                                    $rowOTjmPDel[] = $row_JummaCountDel->PropertyName;
                                                }

                                                $in_JummaCountDel =implode("<br/>\n",$rowOTjmPDel)."<br/>";

                                                echo "<tr><td style='padding: 4px;font-weight: bold;text-align: center;' height='40px;'>" . $in_JummaCountDel . "</td></tr>";
                                            }

                                        }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $pmThakiyaCount = $this->db->query("SELECT COUNT(*) AS thakkiyaCount
FROM srp_erp_ngo_publicpropertybeneficiarymaster pm WHERE pm.subDivision={$rowMah->DAsubDivId} AND pm.projectID = {$rowDis['projectID']} AND pm.propertyType='2' AND pm.subPropertyId='4'")->row();

                                                echo "<tr><td style='padding: 4px;font-weight: bold;text-align: center;' height='40px;'>" . $pmThakiyaCount->thakkiyaCount . "</td></tr>";
                                            }

                                        }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $pmthCountDel = $this->db->query("SELECT pm.PropertyName
FROM srp_erp_ngo_publicpropertybeneficiarymaster pm WHERE pm.subDivision={$rowMah->DAsubDivId} AND pm.projectID = {$rowDis['projectID']} AND pm.propertyType='2' AND pm.subPropertyId='4'")->result();
                                                $rowOTthPDel = array();
                                                foreach ($pmthCountDel as $row_thCountDel) {

                                                    $rowOTthPDel[] = $row_thCountDel->PropertyName;
                                                }

                                                $in_thakiyaCountDel =implode("<br/>\n",$rowOTthPDel)."<br/>";

                                                echo "<tr><td style='padding: 4px;font-weight: bold;text-align: center;' height='40px;'>" . $in_thakiyaCountDel . "</td></tr>";
                                            }

                                        }
                                        echo '</table>';

                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmOTdmgPCount = $this->db->query("SELECT COUNT(*) AS otDmgPrslyCount
FROM srp_erp_ngo_itemdamagedasssesment otrDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON otrDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND otrDmgAss.damageItemCategoryID ='5'")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;font-weight: bold;' height='40px;'>" . $bmOTdmgPCount->otDmgPrslyCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmOTdmgPDel = $this->db->query("SELECT itemDescription
FROM srp_erp_ngo_itemdamagedasssesment otrDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON otrDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND otrDmgAss.damageItemCategoryID ='5'")->result();

                                                $rowOTdmgPDel = array();
                                                foreach ($bmOTdmgPDel as $row_bmOTdmgPDel) {

                                                    $rowOTdmgPDel[] = $row_bmOTdmgPDel->itemDescription;
                                                }

                                                $in_otrDamages =implode("<br/>\n",$rowOTdmgPDel)."<br/>";

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'>" . $in_otrDamages . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td>';
                           if (!empty($vehicleTypes)) {
                                foreach ($vehicleTypes as $rowVehi) {

                                    echo'<td>';     if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmVehiDmgPCount = $this->db->query("SELECT COUNT(*) AS vehiPerDmgCount
FROM srp_erp_ngo_itemdamagedasssesment vehiDmg LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON vehiDmg.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND vehiDmg.vehicleAutoID ={$rowVehi['vehicleAutoID']}")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'>" . $bmVehiDmgPCount->vehiPerDmgCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }echo'</td>';
                                }
                            }
                                    echo'<td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $ddHIdmgPCount = $this->db->query("SELECT COUNT(*) AS ddDmgHiCount
FROM srp_erp_ngo_humaninjuryassesment hiDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON hiDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND hiDmgAss.damageTypeID ='21'")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;font-weight: bold;' height='40px;'>" . $ddHIdmgPCount->ddDmgHiCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td>';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmHIdmgPCount = $this->db->query("SELECT COUNT(*) AS otDmgHiCount
FROM srp_erp_ngo_humaninjuryassesment hiDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON hiDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND hiDmgAss.damageTypeID IN (6, 7, 8,9)")->row();

                                                echo "<tr><td style='padding: 4px;text-align: center;font-weight: bold;' height='40px;'>" . $bmHIdmgPCount->otDmgHiCount . "</td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td><td style="display: none;">';
                                    if (!empty($daDisWiseArea)){
                                        echo '<table border="1">';
                                        foreach ($daDisWiseArea as $rowDivi) {
                                            $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                            foreach ($daDisWiseMahalla as $rowMah) {

                                                $bmDatedmgCount = $this->db->query("SELECT bm.registeredDate AS beneRegDates
FROM srp_erp_ngo_beneficiarymaster bm WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} GROUP BY bm.registeredDate")->result();
                                                $rowDateDmgDel = array();
                                                foreach ($bmDatedmgCount as $row_bmDateDmgDel) {

                                                    $rowDateDmgDel[] = $row_bmDateDmgDel->beneRegDates;
                                                }

                                                $in_dateOfDamages =implode("<br/>\n",$rowDateDmgDel)."<br/>";

                                                echo "<tr><td style='padding: 4px;text-align: center;' height='40px;'></td></tr>";
                                            }
                                        }
                                        echo '</table>';
                                    }
                                    echo'</td>';
                                    echo "</tr>";
                                    $x++;

                                }
                            }
                            ?>
                            </tbody>

                        </table>
                    </div>
                    <br>
                    <?php
                    } else {
                        echo warning_message("No Records Found!");
                    }
                    ?>
                </div>

            </div>


        </div>
    </div>

</div>


    <script>

        Inputmask().mask(document.querySelectorAll("input"));

        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/districtWise_damage_assesment_report', '', 'District Wise Damage Assesment');
        });
        $(document).ready(function (e) {
            //$('.select2').select2();
        });


        function generateReportPdf() {

                var fieldNameChk = [];
                var captionChk = [];
                $("input[name=fieldName]:checked").each(function () {
                    fieldNameChk.push($(this).val());
                    captionChk.push($(this).data('caption'));
                });
                var form = document.getElementById('districtWise_damage_form');
                //document.getElementById('fieldNameChkpdf').value = fieldNameChk;
                //document.getElementById('captionChkpdf').value = captionChk;
                form.target = '_blank';
                form.action = '<?php echo site_url('OperationNgo/districtWise_damages_report_pdf'); ?>';
                form.submit();


        }
    </script>
<?php
