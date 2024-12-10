<div class="table-responsive">
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr>
            <td style="width:40%;border: 1px solid black;">
                <img alt="Logo" style="height: 80px"
                     src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>"></td>
            <td style="width:60%;height:25px;border: 1px solid black;text-align: center;">
                <h5>
                    <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                </h5><h4>JOB ORDER</h4></td>
        </tr>
        <!--<tr style="border: 1px solid black;">
            <td colspan="2" style="width:100%;height:25px;border: 1px solid black;text-align: center;">&nbsp;
                <p><?php /*echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; */ ?></p>
            </td>
        </tr>
        <tr style="border: 1px solid black;">
            <td colspan="2" style="width:100%;border: 1px solid black;">&nbsp;</td>
        </tr>-->
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr>
            <td style="width:33%;height:25px;border: 1px solid black;"><strong>Job Order
                    No: <?php echo $jobMaster['documentCode']; ?></strong></td>
            <td style="width:33%;height:25px;border: 1px solid black;">
                <strong>Client: <?php echo $header['CustomerName']; ?></strong></td>
            <td style="width:33%;height:25px;border: 1px solid black;">
                <strong>Date: <?php echo date('Y-m-d'); ?></strong></td>
        </tr>
        <tr style="border: 1px solid black;">
            <td style="width:33%;height:25px;border: 1px solid black;"><strong>PO
                    No: <?php echo $header['poNumber']; ?></strong></td>
            <td style="width:33%;height:25px;border: 1px solid black;"><strong>Project
                    Title: <?php echo $header['jobTitle']; ?></strong></td>
            <td style="width:33%;height:25px;border: 1px solid black;"><strong>Ref. Quotation No:
                    <?php echo $header['estimateCode']; ?></strong></td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody>
        <tr style="border: 1px solid black;">
            <td colspan="6" style="width:100%; background-color: #c1e1e8;">
                <div style="font-weight: 600;font-size: 14px;">INCLUSION AND EXCLUSION IN SCOPE OF WORK :</div>
            </td>
        </tr>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;">
            <td colspan="6" style="width:100%;">
                <div style="font-weight: 600;font-size: 14px;color: red;">I. SCOPE OF WORK</div>
            </td>
        </tr>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;">
            <td colspan="6"
                style="width:100%;height:50px;"><?php echo $header['scopeOfWork'] ?>
                <!--<table style="width: 40%;" border="1">
                    <tr>
                        <td style="text-align: left;font-weight: 600;">Item Name</td>
                        <td style="text-align: left;font-weight: 600;">Qty</td>
                    </tr>
                    <tbody>
                    <?php
/*                    if ($estimateDetail) {
                        foreach ($estimateDetail as $val) {
                            */?>
                            <tr>
                                <td><?php /*echo $val["concatItemDescription"] */?></td>
                                <td><?php /*echo $val["expectedQty"] */?></td>
                            </tr>
                            <?php
/*                        }
                    }
                    */?>
                    </tbody>
                </table>-->
            </td>
        </tr>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;">
            <td colspan="6" style="width:100%;">
                <div style="font-weight: 600;font-size: 14px;color: red;">II. EXCLUSION</div>
            </td>
        </tr>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;">
            <td colspan="6" style="width:100%;height:50px;"><?php echo $header['exclusions']; ?></td>
        </tr>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;">
            <td colspan="6" style="width:100%;">
                <div style="font-weight: 600;font-size: 14px;color: red;">III. QUANTITY</div>
            </td>
        </tr>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;">
            <td colspan="6"
                style="width:100%;height:60px;"><?php echo $jobMaster['qty']; ?>
                <table style="width: 40%;" border="1">
                    <tr>
                        <td style="text-align: left;font-weight: 600;">Item Name</td>
                        <td style="text-align: left;font-weight: 600;">Qty</td>
                    </tr>
                    <tbody>
                    <?php
                    if ($estimateDetail) {
                        foreach ($estimateDetail as $val) {
                            ?>
                            <tr>
                                <td><?php echo $val["concatItemDescription"] ?></td>
                                <td><?php echo $val["expectedQty"] ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody>
        <tr style="border: 1px solid black;width:100%;">
            <td colspan="6" style="width:100%; background-color: #c1e1e8;">
                <div style="font-weight: 600;font-size: 14px;">DESIGN</div>
            </td>
        </tr>
        <tr style="border: 1px solid black;">
            <td style="width:25%;height:25px;border: 1px solid black;"><strong>Code of Construction</strong></td>
            <td style="width:25%;border: 1px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['designCode'];
                } else { ?>
                    <input type="text" name="designCode" value="<?php echo $header['designCode'] ?>"
                           style="width: 100%">
                <?php }
                ?></td>
            <td style="width:25%;border: 1px solid black;"></td>
            <td style="width:25%;border: 1px solid black;"></td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:25%;height:25px;border: 1px solid black;"><strong>Edition</strong></td>
            <td style="width:25%;border: 1px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['designEditor'];
                } else { ?>
                    <input type="text" name="designEditor" value="<?php echo $header['designEditor'] ?>"
                           style="width: 100%">
                <?php }
                ?></td>
            <td style="width:25%;border: 1px solid black;"></td>
            <td style="width:25%;border: 1px solid black;"></td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:25%;height:25px;border: 1px solid black;"><strong>Addenda/Errata</strong></td>
            <td style="width:25%;border: 1px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['addenta'];
                } else { ?>
                    <input type="text" name="addenta" style="width: 100%"  value="<?php echo  $userInput['addenta'] ?>">
                <?php }
                ?></td>
            <td style="width:25%;border: 1px solid black;"></td>
            <td style="width:25%;border: 1px solid black;"></td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr style="border-right: 1px solid black;border-left: 1px solid black;border-top: 1px solid black;">
            <td colspan="3" style="width:100%; background-color: #c1e1e8;">
                <div style="font-weight: 600;font-size: 14px;">CLIENT SPECIFICATION / OTHER REQUIREMENTS</div>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:40%;height:25px;border: 1px solid black;"><strong>1. Painting Specification</strong></td>
            <td style="width:30%;border: 1px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['paintingSpecifications'];
                } else { ?>
                    <input type="text" name="paintingSpecifications" style="width: 100%" value="<?php echo  $userInput['paintingSpecifications'] ?>">
                <?php }
                ?></td>
            <td style="width:30%;border: 1px solid black;text-align: center;">Remarks</td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:40%;height:25px;border: 1px solid black;">2. SUBMISION OF DRG</td>
            <td style="width:10%;border: 1px solid black;text-align: center;" rowspan="2">Applicable</td>
            <td style="width:20%;border: 1px solid black;"><?php echo $header['engineeringDrawings'] == 1 ? "Yes" : "No"; ?></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['submisionDRG'];
                } else { ?>
                    <input type="text" name="submisionDRG" style="width: 100%" value="<?php echo  $userInput['submisionDRG'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:40%;height:25px;border: 1px solid black;">3. SUBMISSION OF ITP</td>
            <td style="width:20%;border: 1px solid black;"><?php echo $header['submissionOfITP'] == 1 ? "Yes" : "No"; ?></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['submisionITP'];
                } else { ?>
                    <input type="text" name="submisionITP" style="width: 100%" value="<?php echo  $userInput['submisionITP'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:40%;height:25px;border: 1px solid black;text-align: center;" colspan="2">Activity</td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['activity'];
                } else { ?>
                    <input type="text" name="activity" style="width: 100%" value="<?php echo  $userInput['activity'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:40%;height:25px;border: 1px solid black;" colspan="2">4. Post weld heat treatment</td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['heatTreatment'];
                } else { ?>
                    <input type="text" name="heatTreatment" style="width: 100%" value="<?php echo  $userInput['heatTreatment'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody>
        <tr style="border-right: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;width:100%;">
            <td style="width:15%;height:25px;border: 1px solid black;" colspan="3">5. Pressure Testing</td>
            <td style="width:8%;border: 2px solid black;">Pneumatic</td>
            <td style="width:7%;border: 2px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['pressureTestingPneumatic'];
                } else { ?>
                    <input type="text" name="pressureTestingPneumatic" style="width: 100%" value="<?php echo  $userInput['pressureTestingPneumatic'] ?>">
                <?php }
                ?></td>
            <td style="width:5%;border: 1px solid black;" colspan="2"></td>
            <td style="width:8%;border: 2px solid black;">Hydro</td>
            <td style="width:7%;border: 2px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['pressureTestingHydro'];
                } else { ?>
                    <input type="text" name="pressureTestingHydro" style="width: 100%" value="<?php echo  $userInput['pressureTestingHydro'] ?>">
                <?php }
                ?></td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['pressureTestingComment'];
                } else { ?>
                    <input type="text" name="pressureTestingComment" style="width: 100%" value="<?php echo  $userInput['pressureTestingComment'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:50%;height:25px;border: 1px solid black;" colspan="9">6. NDT 1</td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['NDT1Comment'];
                } else { ?>
                    <input type="text" name="NDT1Comment" style="width: 100%" value="<?php echo  $userInput['NDT1Comment'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:8%;height:25px;border: 1px solid black;">&nbsp;</td>
            <td style="width:4%;border: 2px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['RT'];
                } else { ?>
                    <input type="text" name="RT" style="width: 100%" value="<?php echo  $userInput['RT'] ?>">
                <?php }
                ?></td>
            <td style="width:3%;border: 1px solid black;">RT</td>
            <td style="width:5%;border: 1px solid black;">&nbsp;</td>
            <td style="width:8%;border: 1px solid black;">%</td>
            <td style="width:7%;border: 2px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['UT'];
                } else { ?>
                    <input type="text" name="UT" style="width: 100%" value="<?php echo  $userInput['UT'] ?>">
                <?php }
                ?></td>
            <td style="width:7%;border: 1px solid black;">UT</td>
            <td style="width:7%;border: 1px solid black;"></td>
            <td style="width:7%;border: 1px solid black;">%</td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['RTUTComment'];
                } else { ?>
                    <input type="text" name="RTUTComment" style="width: 100%" value="<?php echo  $userInput['RTUTComment'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:50%;height:25px;border: 1px solid black;" colspan="9">7. NDT 2</td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['NDT2Comment'];
                } else { ?>
                    <input type="text" name="NDT2Comment" style="width: 100%" value="<?php echo  $userInput['NDT2Comment'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:8%;height:25px;border: 1px solid black;">&nbsp;</td>
            <td style="width:4%;border: 2px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['MPT'];
                } else { ?>
                    <input type="text" name="MPT" style="width: 100%" value="<?php echo  $userInput['MPT'] ?>">
                <?php }
                ?></td>
            <td style="width:3%;border: 1px solid black;">MPT</td>
            <td style="width:5%;border: 1px solid black;">&nbsp;</td>
            <td style="width:8%;border: 1px solid black;">%</td>
            <td style="width:7%;border: 2px solid black;"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['LPT'];
                } else { ?>
                    <input type="text" name="LPT" style="width: 100%" value="<?php echo  $userInput['LPT'] ?>">
                <?php }
                ?></td>
            <td style="width:7%;border: 1px solid black;">LPT</td>
            <td style="width:7%;border: 1px solid black;"></td>
            <td style="width:7%;border: 1px solid black;">%</td>
            <td style="width:20%;border: 1px solid black;"></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['MPTLPTComment'];
                } else { ?>
                    <input type="text" name="MPTLPTComment" style="width: 100%" value="<?php echo  $userInput['MPTLPTComment'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:50%;height:25px;border: 1px solid black;" colspan="9">8. Inspection and documentation</td>
            <td style="width:20%;border: 1px solid black;"><?php echo $header['qcqtDocumentation'] ?></td>
            <td style="width:30%;border: 1px solid black;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['inspectionDocumentation'];
                } else { ?>
                    <input type="text" name="inspectionDocumentation" style="width: 100%" value="<?php echo  $userInput['inspectionDocumentation'] ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="height:25px;border: 1px solid black;"><b>Remarks</b></td>
            <td colspan="10"><?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['remarks'];
                } else { ?>
                    <input type="text" name="remarks" style="width: 100%" value="<?php echo  $userInput['remarks'] ?>">
                <?php }
                ?></td>
        </tr>
        <?php if (isset($certifications) && !empty($certifications)) {
            foreach ($certifications as $key => $val) {
                ?>
                <tr style="border: 1px solid black;width:100%;">
                    <?php if(count($certifications) > 1 && $key==0){ ?>
                    <td style="width:36%;height:25px;border: 1px solid black;" colspan="7" rowspan="<?php echo $key==0?count($certifications):0; ?>">
                        MATERIAL CERTIFICATION
                    </td>
                    <?php } else if(count($certifications) == 1){ ?>
                        <td style="width:36%;height:25px;border: 1px solid black;" colspan="7">
                            MATERIAL CERTIFICATION
                        </td>
                    <?php } ?>
                    <td style="width:7%;border: 1px solid black;"></td>
                    <td style="width:7%;border: 1px solid black;"></td>
                    <td style="width:20%;border: 1px solid black;">&nbsp;
                        <?php echo $val["Description"]; ?>
                    </td>
                    <?php
                    if (isset($type) && $type == 'pdf') {
                        ?>
                        <td style="width:30%;border: 1px solid black;"> <?php echo $certificationComment[$key]['comment']; ?></td>
                        <?php
                    }else{
                        ?>
                        <input type="hidden" name="materialCertificateID[]" value="<?php echo $val['materialCertificateID']; ?>">
                        <td style="width:30%;border: 1px solid black;"> <input type="text" name="materialCertificationComment[]" style="width: 100%" value=""></td>
                    <?php
                    }
                    ?>
                </tr>
            <?php
            }
        }  else {
            ?>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:36%;height:25px;border: 1px solid black;" colspan="7" rowspan="2">MATERIAL
                    CERTIFICATION
                </td>
                <td style="width:7%;border: 1px solid black;"></td>
                <td style="width:7%;border: 1px solid black;"></td>
                <td style="width:20%;border: 1px solid black;">&nbsp;
                </td>
                <td style="width:30%;border: 1px solid black;"></td>
            </tr>
            <?php
        } ?>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tbody style="border: 1px solid black;">
        <tr style="border: 1px solid black;width:100%;">
            <td colspan="3" style="width:100%; background-color: #c1e1e8;">
                <div style="font-weight: 600;font-size: 14px;">DELIVERY ( Expected Delivery Date : <?php echo $jobMaster['expectedDelDate'] ?>)</div>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td colspan="3" style="width:100%;border: 1px solid black;height:25px;">
                <?php
                if (isset($type) && $type == 'pdf') {
                    echo $userInput['deliverycomments'];
                } else { ?>
                    <input type="text" name="deliverycomments" style="width: 100%" value="<?php echo $userInput['deliverycomments'] != '' ? $userInput["deliverycomments"]: $header["deliveryTerms"]; ?>">
                <?php }
                ?>
            </td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:33%;border: 1px solid black;"></td>
            <td style="width:33%;border: 1px solid black;height:25px;text-align: center;">Prepared by</td>
            <td style="width:33%;border: 1px solid black;text-align: center;">Reviewed by</td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:33%;border: 1px solid black;height:25px;">Name</td>
            <td style="width:33%;border: 1px solid black;text-align: center;"><?php echo $header['createdUserName']; ?></td>
            <td style="width:33%;border: 1px solid black;text-align: center;"><?php echo $header['approvedbyEmpName']; ?></td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:33%;border: 1px solid black;height:25px;">Designation</td>
            <td style="width:33%;border: 1px solid black;text-align: center;"><?php echo $header['DesDescription']; ?></td>
            <td style="width:33%;border: 1px solid black;text-align: center;"></td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:33%;border: 1px solid black;height:25px;">Signature</td>
            <td style="width:33%;border: 1px solid black;text-align: center;"></td>
            <td style="width:33%;border: 1px solid black;text-align: center;"></td>
        </tr>
        <tr style="border: 1px solid black;width:100%;">
            <td style="width:33%;height:25px;border: 1px solid black;">Date</td>
            <td style="width:33%;border: 1px solid black;text-align: center;"><?php echo $header['createdDateTime']; ?></td>
            <td style="width:33%;border: 1px solid black;text-align: center;"><?php echo $header['approvedDate']; ?></td>
        </tr>
        </tbody>
    </table>
</div>