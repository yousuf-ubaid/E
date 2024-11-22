<div class="row">
    <div class="col-md-12" style="margin-bottom: 10px;margin-right: 10px;">
        <?php if($type == 'html') { ?>
            <button class="btn btn-pdf btn-xs pull-right" id="btn-pdf" type="button" onclick="generateReportPdf_job(<?php echo $estimateMasterID;?>,<?php echo $workProcessID;?>)">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
            </button>
        <?php } ?>
    </div>
</div>
<div id="jobOrderPDF">
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
                    </h5><h4><?php echo $this->lang->line('manufacturing_job_order')?><!--JOB ORDER--></h4></td>
            </tr>
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
                    <strong>Date: <?php echo $jobMaster['jobDate']; ?></strong></td>
            </tr>
            <tr style="border: 1px solid black;">
               
                <td style="width:33%;height:25px;border: 1px solid black;"><strong>Project
                        Title: <?php echo $header['jobTitle']; ?></strong></td>
                <td style="width:33%;height:25px;border: 1px solid black;"><strong>Ref. Quotation No:
                        <?php echo $header['estimateCode']; ?></strong></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="width:33%;height:25px;border: 1px solid black;"><strong>PO
                        No: <?php echo $jobMaster['poNumber']; ?></strong></td>
                <td style="width:33%;height:25px;border: 1px solid black;"><strong>PO
                        Date: <?php echo date('Y-m-d',strtotime($jobMaster['poDate'])); ?></strong></td>
              
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
                    echo $userInput['designCode'];
                    ?></td>
                <td style="width:25%;border: 1px solid black;"></td>
                <td style="width:25%;border: 1px solid black;"></td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:25%;height:25px;border: 1px solid black;"><strong>Edition</strong></td>
                <td style="width:25%;border: 1px solid black;"><?php
                    echo $userInput['designEditor'];
                    ?></td>
                <td style="width:25%;border: 1px solid black;"></td>
                <td style="width:25%;border: 1px solid black;"></td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:25%;height:25px;border: 1px solid black;"><strong>Addenda/Errata</strong></td>
                <td style="width:25%;border: 1px solid black;"><?php
                    echo $userInput['addenta'];
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
                    echo $userInput['paintingSpecifications'];
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
                    echo $userInput['submisionDRG'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:40%;height:25px;border: 1px solid black;">3. SUBMISSION OF ITP</td>
                <td style="width:20%;border: 1px solid black;"><?php echo $header['submissionOfITP'] == 1 ? "Yes" : "No"; ?></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['submisionITP'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:40%;height:25px;border: 1px solid black;text-align: center;" colspan="2">Activity</td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['activity'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:40%;height:25px;border: 1px solid black;" colspan="2">4. Post weld heat treatment</td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['heatTreatment'];
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
                    echo $userInput['pressureTestingPneumatic'];
                    ?></td>
                <td style="width:5%;border: 1px solid black;" colspan="2"></td>
                <td style="width:8%;border: 2px solid black;">Hydro</td>
                <td style="width:7%;border: 2px solid black;"><?php
                    echo $userInput['pressureTestingHydro'];
                    ?></td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['pressureTestingComment'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:50%;height:25px;border: 1px solid black;" colspan="9">6. NDT 1</td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['NDT1Comment'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:8%;height:25px;border: 1px solid black;">&nbsp;</td>
                <td style="width:4%;border: 2px solid black;"><?php
                    echo $userInput['RT'];
                    ?></td>
                <td style="width:3%;border: 1px solid black;">RT</td>
                <td style="width:5%;border: 1px solid black;">&nbsp;</td>
                <td style="width:8%;border: 1px solid black;">%</td>
                <td style="width:7%;border: 2px solid black;"><?php
                    echo $userInput['UT'];
                    ?></td>
                <td style="width:7%;border: 1px solid black;">UT</td>
                <td style="width:7%;border: 1px solid black;"></td>
                <td style="width:7%;border: 1px solid black;">%</td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['RTUTComment'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:50%;height:25px;border: 1px solid black;" colspan="9">7. NDT 2</td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['NDT2Comment'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:8%;height:25px;border: 1px solid black;">&nbsp;</td>
                <td style="width:4%;border: 2px solid black;"><?php
                    echo $userInput['MPT'];
                    ?></td>
                <td style="width:3%;border: 1px solid black;">MPT</td>
                <td style="width:5%;border: 1px solid black;">&nbsp;</td>
                <td style="width:8%;border: 1px solid black;">%</td>
                <td style="width:7%;border: 2px solid black;"><?php
                    echo $userInput['LPT'];
                    ?></td>
                <td style="width:7%;border: 1px solid black;">LPT</td>
                <td style="width:7%;border: 1px solid black;"></td>
                <td style="width:7%;border: 1px solid black;">%</td>
                <td style="width:20%;border: 1px solid black;"></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['MPTLPTComment'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="width:50%;height:25px;border: 1px solid black;" colspan="9">8. Inspection and documentation</td>
                <td style="width:20%;border: 1px solid black;"><?php echo $header['qcqtDocumentation'] ?></td>
                <td style="width:30%;border: 1px solid black;">
                    <?php
                    echo $userInput['inspectionDocumentation'];
                    ?>
                </td>
            </tr>
            <tr style="border: 1px solid black;width:100%;">
                <td style="height:25px;border: 1px solid black;"><b>Remarks</b></td>
                <td colspan="10"><?php
                    echo $userInput['remarks'];
                    ?></td>
            </tr>
            <?php if (!empty($certificationComment)) {
                foreach ($certificationComment as $key => $val) {
                    ?>
                    <tr style="border: 1px solid black;width:100%;">
                        <?php if (count($certificationComment) > 1 && $key == 0) { ?>
                            <td style="width:36%;height:25px;border: 1px solid black;" colspan="7"
                                rowspan="<?php echo $key == 0 ? count($certificationComment) : 0; ?>">
                                MATERIAL CERTIFICATION
                            </td>
                        <?php } else if (count($certificationComment) == 1) { ?>
                            <td style="width:36%;height:25px;border: 1px solid black;" colspan="7">
                                MATERIAL CERTIFICATION
                            </td>
                        <?php } ?>
                        <td style="width:7%;border: 1px solid black;"></td>
                        <td style="width:7%;border: 1px solid black;"></td>
                        <td style="width:20%;border: 1px solid black;">&nbsp;
                            <?php echo $val["Description"]; ?>
                        </td>
                        <td style="width:30%;border: 1px solid black;"> <?php echo $val['comment']; ?></td>

                    </tr>
                    <?php
                }
            } else {
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
                    echo $userInput['deliverycomments'];
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
</div>