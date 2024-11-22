<?php

use Mpdf\Mpdf;

//$this->load->helper('operation_ngo_helper');

$mpdf = new Mpdf(
    [
        'mode'              => 'utf-8',
        'format'            => 'A4',
        'default_font_size' => 9,
        'default_font'      => 'arial',
        'margin_left'       => 5,
        'margin_right'      => 5,
        'margin_top'        => 5,
        'margin_bottom'     => 10,
        'margin_header'     => 0,
        'margin_footer'     => 3,
        'orientation'       => 'P'
    ]
);
$user = ucwords($this->session->userdata('username'));
$date = date('l jS \of F Y h:i:s A');
$stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
$stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
$stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
$mpdf->SetFooter();
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML($stylesheet2, 1);
$mpdf->WriteHTML($stylesheet3, 1);
$html = '';
//echo $estimateMasterID;
//var_dump($this->common_data['company_data']['company_logo']);
if (!empty($estimateMasterID)) {
    //echo "ok";
    //$html = warning_message(" Records Found!");

    $html .= " 
<div class='table-responsive'>
    
    </table>
        <table style='width: 100%;'>
        <tbody style='border: 1px solid black;'>
            <tr>
                <td style='width:40%;border: 1px solid black;'> <img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                <td style='width:60%;height:25px;border: 1px solid black;text-align: center;'>
                    <h5>
                    <strong>". $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')' ."</strong>
                    </h5><h4>". $this->lang->line('manufacturing_job_order')."<!--JOB ORDER--></h4>
                    
                </td>
            </tr>
        </tbody></table>
         <table style='width: 100%;'>
            <tbody style='border: 1px solid black;'>
                <tr>
                    <td style='width:33%;height:25px;border: 1px solid black;'><strong>Job Order
                            No: ". $jobMaster['documentCode'] ."</strong></td>
                    <td style='width:33%;height:25px;border: 1px solid black;'>
                        <strong>Client: ".$header['CustomerName']."</strong></td>
                    <td style='width:33%;height:25px;border: 1px solid black;'>
                        <strong>Date: ". $jobMaster['jobDate'] ."</strong></td>
                </tr>
                <tr style='border: 1px solid black;'>
                    <td style='width:33%;height:25px;border: 1px solid black;'><strong>PO
                            No: ". $header['poNumber']." </strong></td>
                    <td style='width:33%;height:25px;border: 1px solid black;'><strong>Project
                            Title: ". $header['jobTitle']."</strong></td>
                    <td style='width:33%;height:25px;border: 1px solid black;'><strong>Ref. Quotation No:
                            ". $header['estimateCode']."</strong></td>
                 </tr>
            </tbody>
         </table>
        
            <table style='width: 100%;'>
    <tbody>
    <tr style='border-right: 1px solid black;border-left: 1px solid black;' >
     <td colspan='6' style='width:100%;'></td>
    </tr>
    </tbody>
    </table>
         <table style='width: 100%;'>
            <tbody>
                <tr style='border: 1px solid black;'>
                    <td colspan='6' style='width:100%; background-color: #c1e1e8;'>
                        <div style='font-weight: 600;font-size: 14px;'>INCLUSION AND EXCLUSION IN SCOPE OF WORK :</div>
                    </td>
                </tr>
                
                <tr style='border-right: 1px solid black;border-left: 1px solid black;'>
                    <td colspan='6' style='width:100%;'>
                        <div style='font-weight: 600;font-size: 14px;color: red;'>I. SCOPE OF WORK</div>
                    </td>
                </tr>
            </tbody> 
         </table>   
         <div style='border-right: 1px solid black;border-left: 1px solid black; font-weight: 200;font-size: 10px;'>
            <p>". $header['scopeOfWork'] ."</p>
         </div>
         <table style='width: 100%;'>
            <tbody>   
                
                <tr style='border-right: 1px solid black;border-left: 1px solid black;'>
                    <td colspan='6' style='width:100%;'>
                        <div style='font-weight: 600;font-size: 14px;color: red;'>II. EXCLUSION</div>
                    </td>
                </tr>
                <tr style='border-right: 1px solid black;border-left: 1px solid black;'>
                    <td colspan='6' style='width:100%;height:50px;'>".$header['exclusions']."</td>
                </tr>
                <tr style='border-right: 1px solid black;border-left: 1px solid black;'>
                    <td colspan='6' style='width:100%;'>
                        <div style='font-weight: 600;font-size: 14px;color: red;'>III. QUANTITY</div>
                    </td>
                </tr>
                <tr style='border-right: 1px solid black;border-left: 1px solid black;'>
                    <td colspan='6' style='width:100%;height:60px;'>". $jobMaster['qty'] ."
                        <table style='width: 40%;' border='1'>
                            <tr>
                                <td style='text-align: left;font-weight: 600;'>Item Name</td>
                                <td style='text-align: left;font-weight: 600;'>Qty</td>
                            </tr>
                            <tbody>";
                             if ($estimateDetail) {
                                foreach ($estimateDetail as $val) {
                                     $html .= "  <tr>
                                        <td>". $val['concatItemDescription'] ."</td>
                                        <td>". $val['expectedQty']." </td>
                                    </tr>";

                                }
                             }
                             $html .= "   
                           </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
         </table>
         
          <table style='width: 100%;'>
            <tbody>
            <tr style='border: 1px solid black;width:100%;'>
                <td colspan='6' style='width:100%; background-color: #c1e1e8;'>
                    <div style='font-weight: 600;font-size: 14px;'>DESIGN</div>
                </td>
            </tr>
            <tr style='border: 1px solid black;'>
                <td style='width:25%;height:25px;border: 1px solid black;'><strong>Code of Construction</strong></td>
                <td style='width:25%;border: 1px solid black;'>".$userInput['designCode']
                    ."</td>
                <td style='width:25%;border: 1px solid black;'></td>
                <td style='width:25%;border: 1px solid black;'></td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:25%;height:25px;border: 1px solid black;'><strong>Edition</strong></td>
                <td style='width:25%;border: 1px solid black;'>". $userInput['designEditor']."</td>
                <td style='width:25%;border: 1px solid black;'></td>
                <td style='width:25%;border: 1px solid black;'></td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:25%;height:25px;border: 1px solid black;'><strong>Addenda/Errata</strong></td>
                <td style='width:25%;border: 1px solid black;'>".$userInput['addenta']."</td>
                <td style='width:25%;border: 1px solid black;'></td>
                <td style='width:25%;border: 1px solid black;'></td>
            </tr>
            </tbody>
        </table>
        <table style='width: 100%;'>
            <tbody style='border: 1px solid black;'>
            <tr style='border-right: 1px solid black;border-left: 1px solid black;border-top: 1px solid black;'>
                <td colspan='3' style='width:100%; background-color: #c1e1e8;'>
                    <div style='font-weight: 600;font-size: 14px;'>CLIENT SPECIFICATION / OTHER REQUIREMENTS</div>
                </td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:40%;height:25px;border: 1px solid black;'><strong>1. Painting Specification</strong></td>
                <td style='width:30%;border: 1px solid black;'>".$userInput['paintingSpecifications']."
                    </td>
                <td style='width:30%;border: 1px solid black;text-align: center;'>Remarks</td>
            </tr>
            </tbody>
        </table>
        <table style='width: 100%;'>
            <tbody style='border: 1px solid black;'>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:40%;height:25px;border: 1px solid black;'>2. SUBMISION OF DRG</td>
                <td style='width:10%;border: 1px solid black;text-align: center;' rowspan='2'>Applicable</td>
                <td style='width:20%;border: 1px solid black;'>"; $header['engineeringDrawings'] == 1 ? "Yes" : "No" ;
                $html.="</td>
                <td style='width:30%;border: 1px solid black;'>
                    ". $userInput['submisionDRG']."

                </td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:40%;height:25px;border: 1px solid black;'>3. SUBMISSION OF ITP</td>
                <td style='width:20%;border: 1px solid black;'>";$header['submissionOfITP'] == 1 ? "Yes" : "No";
                $html.="</td>
                <td style='width:30%;border: 1px solid black;'>
                   ". $userInput['submisionITP']."

                </td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:40%;height:25px;border: 1px solid black;text-align: center;' colspan='2'>Activity</td>
                <td style='width:20%;border: 1px solid black;'></td>
                <td style='width:30%;border: 1px solid black;'>
                    ". $userInput['activity']."

                </td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:40%;height:25px;border: 1px solid black;' colspan='2'>4. Post weld heat treatment</td>
                <td style='width:20%;border: 1px solid black;'></td>
                <td style='width:30%;border: 1px solid black;'>
                   ". $userInput['heatTreatment']."
                </td>
            </tr>
            </tbody>
        </table>
         <table style='width: 100%;'>
            <tbody>
                <tr style='border-right: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;width:100%;'>
                    <td style='width:15%;height:25px;border: 1px solid black;' colspan='3'>5. Pressure Testing</td>
                    <td style='width:8%;border: 2px solid black;'>Pneumatic</td>
                    <td style='width:7%;border: 2px solid black;'>". $userInput['pressureTestingPneumatic']."</td>
                    <td style='width:5%;border: 1px solid black;' colspan='2'></td>
                    <td style='width:8%;border: 2px solid black;'>Hydro</td>
                    <td style='width:7%;border: 2px solid black;'>". $userInput['pressureTestingHydro']."</td>
                    <td style='width:20%;border: 1px solid black;'></td>
                    <td style='width:30%;border: 1px solid black;'>
                     ". $userInput['pressureTestingComment']."
                    </td>
                </tr>
                <tr style='border: 1px solid black;width:100%;'>
                    <td style='width:50%;height:25px;border: 1px solid black;' colspan='9'>6. NDT 1</td>
                    <td style='width:20%;border: 1px solid black;'></td>
                    <td style='width:30%;border: 1px solid black;'>
                     ".$userInput['NDT1Comment']."                    
                    </td>
                </tr>
                <tr style='border: 1px solid black;width:100%;'>
                    <td style='width:8%;height:25px;border: 1px solid black;'>&nbsp;</td>
                    <td style='width:4%;border: 2px solid black;'> ". $userInput['RT']."</td>
                    <td style='width:3%;border: 1px solid black;'>RT</td>
                    <td style='width:5%;border: 1px solid black;'>&nbsp;</td>
                    <td style='width:8%;border: 1px solid black;'>%</td>
                    <td style='width:7%;border: 2px solid black;'> ". $userInput['UT']." </td>
                    <td style='width:7%;border: 1px solid black;'>UT</td>
                    <td style='width:7%;border: 1px solid black;'></td>
                    <td style='width:7%;border: 1px solid black;'>%</td>
                    <td style='width:20%;border: 1px solid black;'></td>
                    <td style='width:30%;border: 1px solid black;'>
                       ". $userInput['RTUTComment']."                    
                    </td>
                </tr>
                <tr style='border: 1px solid black;width:100%;'>
                    <td style='width:50%;height:25px;border: 1px solid black;' colspan='9'>7. NDT 2</td>
                    <td style='width:20%;border: 1px solid black;'></td>
                    <td style='width:30%;border: 1px solid black;'>
                      ". $userInput['NDT2Comment']."
                    </td>
                </tr>
                <tr style='border: 1px solid black;width:100%;'>
                    <td style='width:8%;height:25px;border: 1px solid black;'>&nbsp;</td>
                    <td style='width:4%;border: 2px solid black;'>". $userInput['MPT']."
                        </td>
                    <td style='width:3%;border: 1px solid black;'>MPT</td>
                    <td style='width:5%;border: 1px solid black;'>&nbsp;</td>
                    <td style='width:8%;border: 1px solid black;'>%</td>
                    <td style='width:7%;border: 2px solid black;'>".$userInput['LPT']."</td>
                    <td style='width:7%;border: 1px solid black;'>LPT</td>
                    <td style='width:7%;border: 1px solid black;'></td>
                    <td style='width:7%;border: 1px solid black;'>%</td>
                    <td style='width:20%;border: 1px solid black;'></td>
                    <td style='width:30%;border: 1px solid black;'>
                      ". $userInput['MPTLPTComment']."
                    </td>
                </tr>
                <tr style='border: 1px solid black;width:100%;'>
                    <td style='width:50%;height:25px;border: 1px solid black;' colspan='9'>8. Inspection and documentation</td>
                    <td style='width:20%;border: 1px solid black;'>". $header['qcqtDocumentation'] ."</td>
                    <td style='width:30%;border: 1px solid black;'>
                        ". $userInput['inspectionDocumentation']."
                    </td>
                </tr>
                <tr style='border: 1px solid black;width:100%;'>
                    <td style='height:25px;border: 1px solid black;'><b>Remarks</b></td>
                    <td colspan='10'>". $userInput['remarks']."</td>
                </tr>";
            if (!empty($certificationComment)) {
                foreach ($certificationComment as $key => $val) {
                    $html.="<tr style='border: 1px solid black;width:100%;'> ";
                        if (count($certificationComment) > 1 && $key == 0) {
                            $html.="<td style='width:36%;height:25px;border: 1px solid black;' colspan='7'
                                rowspan=' ".$key == 0 ? count($certificationComment) : 0 ."'>
                                    MATERIAL CERTIFICATION
                            </td>";
                        } else if (count($certificationComment) == 1) {
                            $html.="<td style='width:36%;height:25px;border: 1px solid black;' colspan='7'>
                                MATERIAL CERTIFICATION
                            </td>";
                        }

                    $html.="<td style='width:7%;border: 1px solid black;'></td>
                        <td style='width:7%;border: 1px solid black;'></td>
                        <td style='width:20%;border: 1px solid black;'>&nbsp;
                            ".$val["Description"] ."
                        </td>
                        <td style='width:30%;border: 1px solid black;'> ". $val['comment'] ."</td>

                    </tr>";

                }
            } else {
                $html.=" <tr style='border: 1px solid black;width:100%;'>
                        <td style='width:36%;height:25px;border: 1px solid black;' colspan='7' rowspan='2'>MATERIAL
                            CERTIFICATION
                        </td>
                        <td style='width:7%;border: 1px solid black;'></td>
                        <td style='width:7%;border: 1px solid black;'></td>
                        <td style='width:20%;border: 1px solid black;'>&nbsp;
                        </td>
                        <td style='width:30%;border: 1px solid black;'></td>
                    </tr>";
            }
            $html.="</tbody>
        </table>
        <table style='width: 100%;'>
            <tbody style='border: 1px solid black;'>
            <tr style='border: 1px solid black;width:100%;'>
                <td colspan='3' style='width:100%; background-color: #c1e1e8;'>
                    <div style='font-weight: 600;font-size: 14px;'>DELIVERY ( Expected Delivery Date :  ".$jobMaster['expectedDelDate'].")</div>
                </td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td colspan='3' style='width:100%;border: 1px solid black;height:25px;'>
                    ". $userInput['deliverycomments']."
                </td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:33%;border: 1px solid black;'></td>
                <td style='width:33%;border: 1px solid black;height:25px;text-align: center;'>Prepared by</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'>Reviewed by</td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:33%;border: 1px solid black;height:25px;'>Name</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'>".$header['createdUserName']."</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'>".$header['approvedbyEmpName']."</td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:33%;border: 1px solid black;height:25px;'>Designation</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'>". $header['DesDescription']."</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'></td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:33%;border: 1px solid black;height:25px;'>Signature</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'></td>
                <td style='width:33%;border: 1px solid black;text-align: center;'></td>
            </tr>
            <tr style='border: 1px solid black;width:100%;'>
                <td style='width:33%;height:25px;border: 1px solid black;'>Date</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'>". $header['createdDateTime']."</td>
                <td style='width:33%;border: 1px solid black;text-align: center;'>". $header['approvedDate']."</td>
            </tr>
            </tbody>
        </table>
        </div>
        ";
    $mpdf->WriteHTML($html, 2);
}else {
    $html = warning_message("No Records Found!");
}
if ($output == 'view') {
    $mpdf->Output();
} else {
    //$path = "uploads/NGO/ProjectProposal_".$proposalID.".pdf";
    //$mpdf->Output($path, 'F');

}


?>

