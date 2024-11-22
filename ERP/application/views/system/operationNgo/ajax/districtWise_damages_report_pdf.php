<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
?>

    <div id="tbl_purchase_order_list">
        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style="width:50%;">
                        <table>
                            <tr>
                                <td>
                                    <img alt="Logo" style="height: 140px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                                </td>
                                <td style="text-align:center;font-size: 18px;font-family: tahoma;">
                                    <strong style="font-weight: bold;"><?php echo $this->common_data['company_data']['company_name']; ?>.</strong><br>

                                    <strong style="font-weight: bold;">  <?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?>.</strong><br>
                                    <strong style="font-weight: bold;">  Tel :  <?php echo $this->common_data['company_data']['company_phone'] ?></strong><br>
                                    <br>
                                    District Wise Disaster Assessment Summary Report
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <br>
        <div class="col-sm-12" style="width:100%;font-size: 15px;font-family: tahoma;font-weight: 900;">
            <strong>Project : </strong> <?php echo $project[0]['projectName'] ?>
        </div>
        <br>

        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Country &nbsp;&nbsp;&nbsp;: </strong> <?php if (!empty($country_drop)) {
                    $country = $this->lang->line('country');
                    echo '' . $country . ' ';
                    $tmpArray = array();
                    foreach ($country_drop as $row) {
                        $tmpArray[] = $row['CountryDes'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Province &nbsp;&nbsp;: </strong> <?php if (!empty($province_drop)) {
                    $province = $this->lang->line('province');
                    echo '' . $province . ' ';
                    $tmpArray = array();
                    foreach ($province_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> District &nbsp;&nbsp;&nbsp;&nbsp;: </strong> <?php if (!empty($area_drop)) {
                    $district = $this->lang->line('district');
                    echo '' . $district . ' ';
                    $tmpArray = array();
                    foreach ($area_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Division &nbsp;&nbsp;&nbsp;: </strong> <?php if (!empty($da_division_drop)) {
                    $division = $this->lang->line('division');
                    echo '' . $division . ' ';
                    $tmpArray = array();
                    foreach ($da_division_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="reportHeaderColor" style="font-size: 15px;font-family: tahoma; font-weight: 900">Summary</div>
            </div>
        </div>
        <?php if (!empty($daDisWiseDistric)) { ?>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report" style="width:100%;height: auto;" border="1">
                        <thead class="report-header">
                        <tr style="text-align:center;font-size: 23px;">
                            <td rowspan="2" style="text-align: center;font-size: 23px;">District</td>
                            <td rowspan="2" style="text-align: center;font-size: 23px;">DS Division</td>
                            <td rowspan="2" style="text-align: center;font-size: 23px;">Village</td>
                            <td rowspan="2" class="verticalText" style="text-align: center;font-size: 23px;">Total # Of Muslim Families</td>
                            <td rowspan="2" class="verticalText" style="text-align: center;font-size: 23px;">Families Directly Effected</td>
                            <td rowspan="2" class="verticalText" style="text-align: center;font-size: 23px;">Economically Vulnerable <br> Families Directly <br> Effected</td>
                            <td colspan="3" style="text-align: center;font-size: 23px;">House Damaged</td>
                            <td colspan="3" style="text-align: center;font-size: 23px;">Business Places Damaged</td>
                            <td colspan="4" style="text-align: center;font-size: 23px;">Masjid Damaged</td>
                            <td colspan="2" style="text-align: center;font-size: 23px;">Common Facilities Damaged</td>
                            <td colspan="7" style="text-align: center;font-size: 23px;">Vehicles Damaged</td>
                            <td rowspan="2" class="verticalText" style="text-align: center;font-size: 23px;">Personal Died</td>
                            <td rowspan="2" class="verticalText" style="text-align: center;font-size: 23px;">Personal Injured</td>
                            <td rowspan="2" class="" style=""></td>
                        </tr>
                        <tr  style="text-align: center;font-size: 23px;"><td style="text-align: center;font-size: 23px;" class="verticalText"># Of Houses</td><td style="text-align: center;font-size: 23px;" class="verticalText">Fully</td><td style="text-align: center;font-size: 23px;" class="verticalText">Partially</td>
                            <td style="text-align: center;font-size: 23px;" class="verticalText"># Of Business Places</td><td style="text-align: center;font-size: 23px;">Fully/Mora Than 75%</td><td style="text-align: center;font-size: 23px;" class="verticalText">Partially /Minor</td>
                            <td style="text-align: center;font-size: 23px;" class="verticalText">Jumma Masjid</td><td style="text-align: center;font-size: 23px;" class="verticalText">Name Of The Masjid</td><td style="text-align: center;font-size: 23px;">Thakiya Masjid</td><td style="text-align: center;font-size: 23px;">Name Of The Masjid</td>
                            <td style="text-align: center;font-size: 23px;" class="verticalText"># Of Facilities</td><td style="text-align: center;font-size: 23px;" class="verticalText">Name Of The Facilities</td>
                            <?php if (!empty($vehicleTypes)) {
                                foreach ($vehicleTypes as $row) {

                                    echo'<td style="text-align: center;font-size: 23px;">'.$row['vehicleDescription'].'</td>';
                                }
                            } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($daDisWiseDistric)) {

                            foreach ($daDisWiseDistric as $rowDis) {

                                echo "<tr>";
                                echo "<td style='font-size: 20px;'>" . $rowDis["DAdistrict"] . "</td><td>";
                                $daDisWiseArea = $this->db->query("SELECT DISTINCT smdsDivision.stateID as smdstateID,smdsDivision.Description as DAdivision
FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district LEFT JOIN srp_erp_statemaster smdsDivision ON smdsDivision.stateID = bm.division
WHERE
	bm.district={$rowDis['districID']} AND bm.projectID = {$rowDis['projectID']} ")->result();
                                if (!empty($daDisWiseArea)){
                                    echo '<table border="1">';
                                    foreach ($daDisWiseArea as $rowDivi) {

                                        echo "<tr><td style='padding: 4px;font-size: 20px;' height='48px;'>" . $rowDivi->DAdivision . "</td></tr>";
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

                                            echo "<tr><td style='padding: 4px;font-size: 20px;' height='44px;'>" . $rowMah->DAsubDivision . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmEthnicityCount = $this->db->query("SELECT COUNT(*) AS ethnicityCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE bm.ethnicityID=2 AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;font-weight: bold;text-align: center;font-size: 20px;' height='48px;'>" . $bmEthnicityCount->ethnicityCount . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmEthnicityCount = $this->db->query("SELECT COUNT(*) AS ethnicityCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;font-weight: bold;text-align: center;font-size: 20px;' height='48px;'>" . $bmEthnicityCount->ethnicityCount . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmEconVuCount = $this->db->query("SELECT COUNT(*) AS econVulnerableCount
FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_ngo_economicstatusmaster ecMas ON bm.da_economicStatus=ecMas.economicStatusID WHERE bm.da_economicStatus IN (4,5) AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-weight: bold;font-size: 20px;' height='48px;'>" . $bmEconVuCount->econVulnerableCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmHSdamageCount = $this->db->query("SELECT COUNT(*) AS hsDamageCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE (bm.da_typeOfhouseDamage is not null OR bm.da_typeOfhouseDamage !='') AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-weight: bold;font-size: 20px;' height='48px;'>" . $bmHSdamageCount->hsDamageCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmHSdmgFCount = $this->db->query("SELECT COUNT(*) AS hsDmgFullyCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE (bm.da_typeOfhouseDamage is not null OR bm.da_typeOfhouseDamage !='') AND da_housingCondition=2 AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-size: 20px;' height='48px;'>" . $bmHSdmgFCount->hsDmgFullyCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmHSdmgPCount = $this->db->query("SELECT COUNT(*) AS hsDmgParCount
FROM srp_erp_ngo_beneficiarymaster bm WHERE (bm.da_typeOfhouseDamage is not null OR bm.da_typeOfhouseDamage !='') AND da_housingCondition=1 AND bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-size: 20px;' height='48px;'>" . $bmHSdmgPCount->hsDmgParCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmBSdamageCount = $this->db->query("SELECT COUNT(*) AS bsDamageCount
FROM srp_erp_ngo_businessdamagedassesment busDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON busDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} ")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-weight: bold;font-size: 20px;' height='48px;'>" . $bmBSdamageCount->bsDamageCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmBSdmgFCount = $this->db->query("SELECT COUNT(*) AS bsDmgFullyCount
FROM srp_erp_ngo_businessdamagedassesment busDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON busDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND busDmgAss.damageConditionID ='19'")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-size: 20px;' height='48px;'>" . $bmBSdmgFCount->bsDmgFullyCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmBSdmgPCount = $this->db->query("SELECT COUNT(*) AS bsDmgPrslyCount
FROM srp_erp_ngo_businessdamagedassesment busDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON busDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND busDmgAss.damageConditionID ='20'")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-size: 20px;' height='48px;'>" . $bmBSdmgPCount->bsDmgPrslyCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $pmJummaCount = $this->db->query("SELECT COUNT(*) AS jummaCount
FROM srp_erp_ngo_publicpropertybeneficiarymaster pm WHERE pm.subDivision={$rowMah->DAsubDivId} AND pm.projectID = {$rowDis['projectID']} AND pm.propertyType='2' AND pm.subPropertyId='3'")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;font-weight:bold;padding: 4px;text-align: center;font-size: 20px;' height='40px;'>" . $pmJummaCount->jummaCount . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
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

                                            echo "<tr><td style='border-bottom: 1px solid black;font-weight:bold;padding: 4px;text-align: center;font-size: 20px;' height='40px;'>" . $in_JummaCountDel . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $pmThakiyaCount = $this->db->query("SELECT COUNT(*) AS thakkiyaCount
FROM srp_erp_ngo_publicpropertybeneficiarymaster pm WHERE pm.subDivision={$rowMah->DAsubDivId} AND pm.projectID = {$rowDis['projectID']} AND pm.propertyType='2' AND pm.subPropertyId='4'")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;font-weight:bold;padding: 4px;text-align: center;font-size: 20px;' height='40px;'>" . $pmThakiyaCount->thakkiyaCount . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
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

                                            echo "<tr><td style='border-bottom: 1px solid black;font-weight:bold;padding: 4px;text-align: center;font-size: 20px;' height='40px;'>" . $in_thakiyaCountDel . "</td></tr>";
                                        }

                                    }
                                    echo '</table>';

                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmOTdmgPCount = $this->db->query("SELECT COUNT(*) AS otDmgPrslyCount
FROM srp_erp_ngo_itemdamagedasssesment otrDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON otrDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND otrDmgAss.damageItemCategoryID ='5'")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-weight: bold;font-size: 20px;' height='48px;'>" . $bmOTdmgPCount->otDmgPrslyCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
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

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-size: 20px;' height='48px;'>" . $in_otrDamages . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td>';
                                if (!empty($vehicleTypes)) {
                                    foreach ($vehicleTypes as $rowVehi) {

                                        echo'<td>';     if (!empty($daDisWiseArea)){
                                            echo '<table>';
                                            foreach ($daDisWiseArea as $rowDivi) {
                                                $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                                foreach ($daDisWiseMahalla as $rowMah) {

                                                    $bmVehiDmgPCount = $this->db->query("SELECT COUNT(*) AS vehiPerDmgCount
FROM srp_erp_ngo_itemdamagedasssesment vehiDmg LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON vehiDmg.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND vehiDmg.vehicleAutoID ={$rowVehi['vehicleAutoID']}")->row();

                                                    echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-size: 20px;' height='48px;'>" . $bmVehiDmgPCount->vehiPerDmgCount . "</td></tr>";
                                                }
                                            }
                                            echo '</table>';
                                        }echo'</td>';
                                    }
                                }
                                echo'<td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $ddHIdmgPCount = $this->db->query("SELECT COUNT(*) AS ddDmgHiCount
FROM srp_erp_ngo_humaninjuryassesment hiDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON hiDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND hiDmgAss.damageTypeID ='21'")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-weight: bold;font-size: 20px;' height='48px;'>" . $ddHIdmgPCount->ddDmgHiCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table>';
                                    foreach ($daDisWiseArea as $rowDivi) {
                                        $daDisWiseMahalla = $this->db->query("SELECT DISTINCT subDivision.stateID as DAsubDivId,subDivision.Description as DAsubDivision FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_statemaster subDivision ON subDivision.stateID = bm.subDivision WHERE bm.division={$rowDivi->smdstateID} AND bm.projectID = {$rowDis['projectID']} ")->result();

                                        foreach ($daDisWiseMahalla as $rowMah) {

                                            $bmHIdmgPCount = $this->db->query("SELECT COUNT(*) AS otDmgHiCount
FROM srp_erp_ngo_humaninjuryassesment hiDmgAss LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON hiDmgAss.beneficiaryID= bm.benificiaryID WHERE bm.subDivision={$rowMah->DAsubDivId} AND bm.projectID = {$rowDis['projectID']} AND hiDmgAss.damageTypeID IN (6, 7, 8,9)")->row();

                                            echo "<tr><td style='border-bottom: 1px solid black;padding: 4px;text-align: center;font-weight: bold;font-size: 20px;' height='48px;'>" . $bmHIdmgPCount->otDmgHiCount . "</td></tr>";
                                        }
                                    }
                                    echo '</table>';
                                }
                                echo'</td><td>';
                                if (!empty($daDisWiseArea)){
                                    echo '<table border="1"><tr><td style="padding: 4px;"></td></tr>';

                                    echo '</table>';
                                }
                                echo'</td>';
                                echo "</tr>";


                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                } else {
                    echo warning_message("No Records Found!");
                }
                ?>
            </div>
        </div>
    </div>
<?php
