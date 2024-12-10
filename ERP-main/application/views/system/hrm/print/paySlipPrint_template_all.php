<?php

use Mpdf\Mpdf;

$mpdf = new Mpdf(
    [
        'mode'              => 'utf-8',
        'format'            => 'A5',
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
$date = date('Y-m-d H:i:s ');
$stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
$stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
$stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
$mpdf->SetFooter('Printed By : ' . $user . '|Page : {PAGENO}|' . $date);
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML($stylesheet2, 1);
$mpdf->WriteHTML($stylesheet3, 1);


$date = $masterData['payrollYear']."-".$masterData['payrollMonth']."-01";
$emp_arr = $this->input->post('empID');
$companyName = $this->common_data['company_data']['company_name'];


$countRecord = count($emp_arr);
$html = '';
foreach($emp_arr as $empKey=>$empID){

    foreach($details as $detKey=>$row){
        $arr = $row['empDet'];
        if($arr['E_ID'] == $empID){
            //echo '$row:'.$empID;
            //echo '<pre>'; print_r($arr); echo '</pre>';

            $thisEmpDet = $arr;
            $masterID = $thisEmpDet['masterID'];
            $thisEmpSalaryDet = $row['empSalDec'];
            $dPlace = 2;

            $keys = array_keys(array_column($payroll_data, 'payrollMasterID'), $masterID);
            $thisPayrollData = array_map(function ($k) use ($payroll_data) {
                return $payroll_data[$k];
            }, $keys);

            $thisPayrollData = $thisPayrollData[0];
            $date = $thisPayrollData['payrollYear'] . "-" . $thisPayrollData['payrollMonth'] . "-01";

            $html .= '<h4 style="text-align:center;"> '.$companyName.'</h4>
                    <hr>
                    <table style="width: 100%" border="0px">
                        <tbody>
                        <tr>
                            <th align="center">Pay Slip For The Month Of '. date('F',  strtotime($date )).' '. date('Y',  strtotime($date )).'</th>
                        </tr>
                        <tr>
                            <th align="center">'.$thisPayrollData['narration'].'</th>
                        </tr>
                        </tbody>
                    </table>';

            $html .= '<hr>';

            $html .=  '<table style="width: 100%" border="0px">
                        <tbody>
                        <tr>
                            <td width="20%"><strong>Number</strong></td>
                            <td width="2%" align="center"><strong>:</strong></td>
                            <td width="78%"><strong>'.$thisEmpDet['ECode'].'</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Name</strong></td>
                            <td align="center"><strong>:</strong></td>
                            <td><strong>'.$thisEmpDet['Ename2'].'</strong></td>
                        </tr>
                        </tbody>
                    </table>';

            $html .=  '<table class="'.table_class().'" id="paysheet-tb" style="margin-top: -3px">
                        <tbody>';

            foreach($header_det as $headerKey=>$headerRow){
                /**** Making the header [th] ****/
                $html .= '<tr>';

                if( $headerRow['detailType'] != 'H'){
                    $html .= '<th class="theadtr" align="left">'.$headerRow['captionName'].'</th>';
                }
                $value = ' - ';

                if ($headerRow['detailType'] == 'H') {
                    //$value = '<th align="left">' . $thisEmpDet[ $headerRow['fieldName'] ] . '</th>';
                }
                else if ($headerRow['detailType'] == 'A' || $headerRow['detailType'] == 'D') {
                    /************************************************************************************
                     * If 'fieldName' name equal to 'MA'=> Monthly Addition or 'MD' => Monthly Deduction
                     * than the searching key will be 'MA' OR 'MD'
                     ************************************************************************************/

                    $searchingKey = ($headerRow['fieldName'] == 'MA' OR $headerRow['fieldName'] == 'MD' OR $headerRow['fieldName'] == 'LO')?
                        $headerRow['fieldName'] : $headerRow['catID'];
                    $val = search_paysheetEmpDetails($thisEmpSalaryDet, $searchingKey);

                    $value = '<td align="right">'. format_number($val, $dPlace) . '</td>';
                }
                else if ($headerRow['detailType'] == 'G') {
                    $searchingKey = 'G_'.$headerRow['payID'];
                    $val = search_paysheetEmpDetails($thisEmpSalaryDet, $searchingKey);

                    $value = '<td align="right">' . format_number($val, $dPlace). '</td>';
                }

                $html .= $value;
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $mpdf->WriteHTML($html, 2);
            if ($countRecord > ($empKey+1) ) {
                $mpdf->AddPage();
            }
            $html = "";
        }
    }

}

$mpdf->Output();
?>


<?php
