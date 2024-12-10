<?php

use Mpdf\Mpdf;

$this->load->helper('operation_ngo_helper');

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
if (!empty($master)) {
    $projectlogo  = get_all_operationngo_images( $master['projectImage'],'uploads/ngo/projectImage/');
    $html .= "<div class='table-responsive'>
    <table style='width: 100%;'>
        <tbody>
        <tr>
            <td style='width:30%;'>
                <img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                     <td style='width:50%;'>&nbsp;</td>

            <td style='width:20%;'>
                <img alt='Logo' style='height: 80px'
                     src=" .$projectlogo. "></td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;height:30px;'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 30px;font-weight: bold;font-family: tahoma;text-decoration: underline;'>PROJECT PROPOSAL</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;height:50px;'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 25px;font-weight: bold;font-family: tahoma;color:rgb(0,128,0);'>" . $master['ppProposalName'] . "</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;height:15px;'>&nbsp;</td>
        </tr>";

    if (!empty($images)) {
        foreach ($images as $img) {
            if ($img['imageType'] == 1) {
                $coverImage  = get_all_operationngo_images($img['imageName'],'uploads/ngo/projectProposalImage/');
                $html .= "<tr><td colspan='3' style='width:20%;text-align: center;'> <img alt='Cover Image' style='height: 200px;border-radius: 10px;' src=" .$coverImage. "></td></tr>";
            }
        }
    }
    $html .= "<tr>
            <td colspan='3' style='width:100%;height:50px;'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 25px;font-weight: bold;font-family: tahoma;text-decoration: underline;text-transform: uppercase;'>" . $master['proProjectName'] . "</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 25px;font-weight: bold;font-family: tahoma;text-decoration: underline;text-transform: uppercase;'>" . $master['subprojectName'] . "</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;height:20px;'>&nbsp;</td>
        </tr><tr>";
    if (!empty($images)) {
        $fp_width = 0;
        foreach ($images as $imgFP) {
            if ($imgFP['imageType'] == 2) {
                $frontPageImage  = get_all_operationngo_images($imgFP['imageName'],'uploads/ngo/projectProposalImage/');
                $html .= "<td style='width:30%;text-align: center;'> <img alt='Front Page Image' style='height: 200px;border-radius: 10px;' src=" .$frontPageImage. "></td>";
            }
        }
    }
    $html .= "</tr>
        <tr>
            <td colspan='3' style='width:100%;height:30px;'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 25px;font-weight: bold;font-family: tahoma;text-transform: uppercase;color: rgb(47, 84, 150);'>" . $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')' . "</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 17px;font-weight: bold;font-family: tahoma;text-decoration: underline;text-transform: uppercase;'>&nbsp;
                <p>" . $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country'] . "</p>
            </td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;height:70px;'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3' style='width:100%;text-align:center;font-size: 25px;font-family: garamond;'>" . $moto['companyPrintTagline'] . "</td>
        </tr>
        </tbody>
    </table>
</div>";
    $mpdf->WriteHTML($html, 2);
    $html = '';
    $mpdf->AddPage();
    $html = "<div class='table-responsive'>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td style='width:30%;'><img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                     <td style='width:50%;'>&nbsp;</td>
                     <td style='width:20%;'><img alt='Logo' style='height: 80px'
                     src=" .$projectlogo. "></td></tr>
                </tbody>
            </table>
    <table style='width: 100%;'>
        <tbody><tr>
            <td style='width:100%;font-family: tahoma;'>" . $master['ppDetailDescription'] . "</td>
        </tr></tbody></table></div>";
    $mpdf->WriteHTML($html, 2);
    $mpdf->AddPage();
    $html = "<div class='table-responsive'>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td style='width:30%;'><img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                     <td style='width:50%;'>&nbsp;</td>
                     <td style='width:20%;'><img alt='Logo' style='height: 80px'
                     src=" .$projectlogo. "></td></tr>
                </tbody>
            </table>
    <table style='width: 100%;'>
        <tbody>
        <tr>
            <td colspan='2' style='width:100%;text-align:center;font-size: 25px;font-weight: bold;font-family: tahoma;text-decoration: underline;color:green;'>" . $master['ppProposalTitle'] . "</td>
        </tr>
        <tr>
            <td colspan='2' style='width:100%;font-size: 15px;font-family: tahoma;font-weight: bold;'><u>Project Summary : </u>" . $master['ppProjectSummary'] . "</td>
        </tr>
        <tr>
            <td colspan='2' style='width:100%;height:20px;'>&nbsp;</td>
        </tr>
        </tbody>
        </table>
        <table style='width: 100%;'>
        <tbody style='border: 1px solid black;'>
        <tr>
            <td style='width:30%;height:50px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    &nbsp;&nbsp;Total number of houses &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;font-weight: bold;'>" . $master['ppTotalNumberofHouses'] . "</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    &nbsp;&nbsp;Floor Area &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['ppFloorArea'] . "</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    Cost of a House &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['ppCostofhouse'] . "</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    Additional Cost &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['ppAdditionalCost'] . "</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    Estimated completion time for a house &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['ppEstimatedDays'] . "&nbsp;days</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    Project starting date &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['ppStartDate'] . "</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    Estimate completion date &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['ppEndDate'] . "</td>
        </tr>
        <tr>
            <td style='width:30%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>
                    Contractor &nbsp;</td>
            <td style='width:70%;height:40px;border: 1px solid black;font-family: tahoma;font-size: 13px;'>" . $master['contractorName'] . "</td>
        </tr>
        <tr>
            <td colspan='2' style='width:100%;height:20px;'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='2' style='width:100%;font-size: 15px;font-family: tahoma;text-decoration: underline;'>Sample House Plan:</td>
        </tr><tr>";
    if (!empty($images)) {
        $fp_width = 0;
        foreach ($images as $imgFP) {
            if ($imgFP['imageType'] == 3) {
                $frontPageImage  = get_all_operationngo_images($imgFP['imageName'],'uploads/ngo/projectProposalImage/');
                $html .= "<td colspan='2' style='width:100%;text-align: center;'> <img alt='Front Page Image' style='height: 400px;border-radius: 10px;' src=" .$frontPageImage. "></td>";
            }
        }
    }
    $html .= "</tr></tbody>
    </table>
        </div>";
    $mpdf->WriteHTML($html, 2);
    $mpdf->AddPage();
    $html = '';
    if (!empty($detail)) {
        foreach ($detail as $rec) {
            $beneppimages = selectedImagespp($rec['ppbBeneficiaryID']);
            $pic1  = get_all_operationngo_images( $beneppimages[0]['beneficiaryImage'],'uploads/ngo/beneficiaryImage/');
            $pic2  = get_all_operationngo_images( $beneppimages[1]['beneficiaryImage'],'uploads/ngo/beneficiaryImage/');
            $html = "<div class='table-responsive'>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td style='width:30%;'><img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                     <td style='width:50%;'>&nbsp;</td>
                     <td style='width:20%;'><img alt='Logo' style='height: 80px'
                     src=" .$projectlogo. "></td></tr>
                </tbody>
            </table>
            <br>
            <hr>
            <br><br>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td style='width:100%;font-size: 16px;font-weight: bold;font-family: tahoma;text-decoration: underline;'>" . $rec['bmNameWithInitials'] . " - " . $rec['bmSystemCode'] . "</td>
                </tr>
                <tr>
                    <td style='width:100%;height:30px;'>&nbsp;</td>
                </tr>
                </tbody>
            </table>
            <table style='width: 100%'>
                <tbody>
                <tr style='background-color: #E6EED5'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:40px;font-family: tahoma;'>D.O.B</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>" . $rec['bmDateOfBirth'] . "</td>
                </tr>
                <tr style='background-color: #FFFFFF'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:40px;font-family: tahoma;'>NIC No</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>" . $rec['bmNIC'] . "</td>
                </tr>
                <tr style='background-color: #E6EED5'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:40px;font-family: tahoma;'>Family Details</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>" . $rec['bmFamilyMembersDetail'] . "</td>
                </tr>
                <tr style='background-color: #FFFFFF'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:50px;font-family: tahoma;'>Own Land Available</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>" . $rec['bmOwnLandAvailable'] . ", " . $rec['bmOwnLandAvailableComments'] . "</td>
                </tr>
                <tr style='background-color: #E6EED5'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:120px;font-family: tahoma;'>Reason in Brief</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>" . $rec['bmReasoninBrief'] . "</td>
                </tr>
                <tr style='background-color: #FFFFFF'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:40px;font-family: tahoma;'>Documents Completed</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>Yes.</td>
                </tr>
                <tr style='background-color: #E6EED5'>
                    <td style='width:30%;font-size: 13px;font-weight: bold;height:40px;font-family: tahoma;'>Total Sq Ft</td>
                    <td style='width:70%;font-size: 13px;font-family: tahoma;'>" . $rec['bmTotalSqFt'] . "</td>
                </tr>
                </tbody>
            </table>
       <hr><br><br>
       <table style='width: 100%'>
        <tbody>
        <tr>
            <td width='48%' style='border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;font-size: 20px;height:20px;'>&nbsp;&nbsp;Photos</td>
            <td width='4%'>&nbsp;&nbsp;</td>
            <td width='48%' style='border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;font-size: 20px;'>Photos</td>
        </tr>
        <tr>
            <td style='border-left: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;' align='center' width='48%'>&nbsp;&nbsp;<img alt='House' style='width: 100%;' src=" . $pic1 . "></td>
            <td width='4%'>&nbsp;&nbsp;</td>
            <td align='center' width='48%' style='border-left: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;'><img alt='House' style='width: 100%;' src=" . $pic2 . "></td>
        </tr>
        </tbody>
    </table>
       </div>";
            $mpdf->WriteHTML($html, 2);
            $mpdf->AddPage();
            $html = "";
        }
    }
    $html = "<div class='table-responsive'>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td style='width:30%;'><img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                     <td style='width:50%;'>&nbsp;</td>
                     <td style='width:20%;'><img alt='Logo' style='height: 80px'
                     src=" .$projectlogo. "></td></tr>
                </tbody>
            </table>
            <br>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td colspan='3' style='width:100%;text-align:center;font-size: 20px;font-weight: bold;font-family: tahoma;'>BUDGET PROPOSAL FOR THE PROPOSED PROJECT</td>
                </tr>
                <tr>
                    <td style='width:100%;height:30px;'>&nbsp;</td>
                </tr>
                </tbody>
            </table>
            <table style='width: 100%;'>
                <tbody style='border: 1px solid black;'>
                <tr>
                    <th style='border: 1px solid black;border-bottom: 2px solid #f15727;text-align: center;height:40px;'>#</th>
                    <th style='border: 1px solid black;border-bottom: 2px solid #f15727;height:40px;'>Description</th>
                    <th style='border: 1px solid black;border-bottom: 2px solid #f15727;text-align: center;height:40px;'>Total (LKR)</th>
                </tr>";
    if (!empty($detail)) {
        $budNumber = 1;
        $budTotal = 0;
        foreach ($detail as $bud) {
            $html .= "<tr><td style='width:10%;border: 1px solid black;height:40px;text-align: center;font-size:15px;'>" . $budNumber . "</td><td style='width:70%;border: 1px solid black;font-size:15px;'>" . $bud['bmNameWithInitials'] . "</td><td style='width:20%;border: 1px solid black;text-align: center;font-size:15px;'>" . number_format($bud['totalEstimatedValue'], 2) . "</td></tr>";
            $budTotal += $bud['totalEstimatedValue'];
            $budNumber++;
        }
    }
    $html .= "
            <tr>
                    <td style='width:10%;border: 1px solid black;height:40px;font-size:15px;font-weight: bold;' colspan='2'>Total</td>
                    <th style='border: 1px solid black;border-bottom: 2px solid #f15727;text-align: center;'>" . number_format($budTotal, 2) . "</th>
         </tr>
        <tr>
        <td colspan='3' style='width:100%;height:30px;'>&nbsp;</td></tr>
        </tbody>
            </table>
        <table style='width: 100%;'>
            <tbody style='border: 1px solid black;'>
                <tr>
                    <td colspan='3' style='width:100%;font-size: 20px;font-family: tahoma;font-weight: bold;height:40px;'>Project Specific Bank details:</td>
                </tr>
                <tr>
                    <td style='width:15%;font-size: 15px;font-family: tahoma;height:40px;'>Acc Name</td>
                    <td style='width:5%;font-size: 15px;font-family: tahoma;'>:</td>
                    <td style='width:75%;font-size: 15px;font-family: tahoma;'>" . $master['caBankAccName'] . "</td>
                </tr>
                <tr>
                    <td style='width:15%;font-size: 15px;font-family: tahoma;height:40px;'>Bank</td>
                    <td style='width:5%;font-size: 15px;font-family: tahoma;'>:</td>
                    <td style='width:75%;font-size: 15px;font-family: tahoma;'>" . $master['caBankName'] . "</td>
                </tr>
                <tr>
                    <td style='width:15%;font-size: 15px;font-family: tahoma;height:40px;'>Acc No</td>
                    <td style='width:5%;font-size: 15px;font-family: tahoma;'>:</td>
                    <td style='width:75%;font-size: 15px;font-family: tahoma;'>" . $master['caBankAccountNumber'] . "</td>
                </tr>
            </tbody>
        </table>
        ";

    if (!empty($master['approvedYN'])!=0){
        $prnt_name='Electronically Approved By : ' . $master['approvedbyEmpName'];
        $prnt_date='Electronically Approved Date : ' . $master['approvedDate'];
    }


    $mpdf->WriteHTML($html, 2);
    $mpdf->AddPage();
    $html = "<div class='table-responsive'>
            <table style='width: 100%'>
                <tbody>
                <tr>
                    <td style='width:30%;'><img alt='Logo' style='height: 80px'
                     src=" . mPDFImage . $this->common_data['company_data']['company_logo'] . "></td>
                     <td style='width:50%;'>&nbsp;</td>
                     <td style='width:20%;'><img alt='Logo' style='height: 80px'
                     src=" .$projectlogo. "></td></tr>
                </tbody>
            </table>
            <hr>
    <table style='width: 100%;'>
        <tbody>
                <tr>
            <td style='width:100%;font-size: 20px;font-family: tahoma;font-weight: bold;text-decoration: underline;'>PROCESS:</td>
        </tr>
        <tr><td colspan='3' style='width:100%;height:30px;'>&nbsp;</td></tr>
        <tr>
            <td style='width:100%;font-family: tahoma;'>" . $master['ppProcessDescription'] . "</td>
        </tr>
        <tr>
        <td colspan='3' style='width:100%;text-align:center;font-size: 20px;font-family: garamond;'>Jazakallah hairun!</td>
        </tr>
         <tr>
   
        </tr>
        </tbody></table><br><hr>  
       
         <br>
         <table style='width: 100%;'>
       <tr>
             <td style='width:70%;font-size: 11px;font-family: tahoma;height:40px;'>$prnt_name</td>
 
        </tr>
        <tr>
             <td style='width:70%;font-size: 11px;font-family: tahoma;height:40px;'>$prnt_date</td>
 
        </tr>
      </tbody></table>
        
        </div>";
    $mpdf->WriteHTML($html, 2);
} else {
    $html = warning_message("No Records Found!");
}
if ($output == 'view') {
    $mpdf->Output();
} else {
    $path = "uploads/NGO/ProjectProposal_" . $proposalID . ".pdf";
    $mpdf->Output($path, 'F');
}

?>

