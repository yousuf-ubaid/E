<?php

use Mpdf\Mpdf;

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
$stylesheet = file_get_contents('plugins/bootstrap/css/print_style.css');
$mpdf->SetFooter('Printed By : ' . $user . '|Page : {PAGENO}|' . $date);
$mpdf->WriteHTML($stylesheet, 1);

$isTransCost = true;
$html = "";
if (!empty($output)) {
    $customerArr = array();
    $customer = get_all_customers();
    if (!empty($customer)) {
        foreach ($customer as $val) {
            $customerArr[$val["customerSystemCode"] . " - " . $val["customerName"]] = $val;
        }
    }
    $count = 8;
    $category = array();
    if ($isTransCost && !$isRptCost && !$isLocCost) {
        foreach ($output as $val) {
            $category[$val["customerSystemCode"] . " - " . $val["customerName"]][$val["transactioncurrency"]][] = $val;
        }
    } else {
        foreach ($output as $val) {
            $category[$val["customerSystemCode"] . " - " . $val["customerName"]][] = $val;
        }
    }
    $countCategory = count($category);
    $i = 1;
    $grandtotal = array();
    if ($isTransCost && !$isRptCost && !$isLocCost) {
        if (!empty($category)) {
            $htmlHeader = "";

            foreach ($category as $key2 => $currency) {
                $html .= '<table>
            <tbody>
            <tr>
                <td style="width:30%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 50px" src="' .mPDFImage. '/' . $this->common_data['company_data']['company_logo'] . '">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:70%;">
                    <table>
                        <tr>
                            <td>
                                <h3><strong>' . $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').' . '</strong></h3>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>';
                $html .= '<hr>';
                $html .= '<table>
            <tbody>
            <tr>
                <td style="width:70%;">
                    <table>
                        <tr>
                            <td colspan="2">
                                Customer Address: <br>
                                ' . $key2 . '<br>
                                ' . $customerArr[$key2]["customerAddress1"] . '<br>
                               ' . $customerArr[$key2]["customerAddress2"] . '
                            </td>
                        </tr>
                         <tr>
                            <td>
                               Tel : ' . $customerArr[$key2]["customerTelephone"] . '<br>
                               Fax : ' . $customerArr[$key2]["customerFax"] . '
                            </td>

                        </tr>
                    </table>
                </td>
                <td style="width:30%;" valign="top">
                    <table>
                        <tr>
                            <td colspan="2">
                               <h4><strong>Customer Ledger</strong></h4>
                            </td>
                        </tr>
                         <tr>
                            <td style="width: 19%">Date :</td>
                            <td>' . current_format_date() . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>';
                $html .= '<table class="table" id="tbl_report"><thead><tr>
<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black;width:10%">Doc Date</th>
<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black;width:20%">Doc Type</th>
<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black;width:20%">Doc Number</th>
<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black;width:20%">Narration</th>';
                if (!empty($fieldNameDetails)) {
                    foreach ($fieldNameDetails as $val) {
                        if ($val['fieldName'] == 'transactionAmount') {
                            $html .= '<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black;width:10%">Currency</th>';
                            /*$html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Transaction Currency</th>';*/
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Debit</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Credit</th>';
                        }
                        else if($val['fieldName'] == 'companylocalAmount'){
                            //$html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Local Currency</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Debit (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Credit (' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                        }
                        else if($val['fieldName'] == 'companyReportingAmount'){
                            //$html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Reporting Currency</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Debit (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Credit (' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                        }
                    }
                }
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                $date_format_policy = date_format_policy();
                foreach ($currency as $key3 => $customers) {
                    $subtotal = array();
                    $subtotalcred = array();
                    $subtotaldeb = array();
                    foreach ($customers as $key4 => $val) {
                        if($val['type'] == 2)
                        {
                            $html .= "<tr>";
                            $html .= "<td><u><b>Post Dated Cheques</b></u></td>";
                            $html .= "</tr>";
                        }
                        $html .= "<tr>";
                        $datefromconvert = input_format_date("1970-01-01", $date_format_policy);
                        if($val["documentDate"]== $datefromconvert){
                            $html .= "<td><div style='margin-left: 30px;color: #ffffff;opacity: 0'>" . $val["documentDate"] . "</div></td>";
                        }else{
                            $html .= "<td><div style='margin-left: 30px'>" . $val["documentDate"] . "</div></td>";
                        }
                        $html .= "<td>" . $val["document"] . "</td>";
                        $html .= '<td>' . $val["documentSystemCode"] . '</td>';
                        $html .= "<td>" . $val["documentNarration"] . "</td>";
                        if (!empty($fieldNameDetails)) {
                            foreach ($fieldNameDetails as $val2) {
                                $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                $grandtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                if ($val2["fieldName"] == 'transactionAmount') {
                                    $html .= "<td>" . $val["transactionCurrency"] . "</td>";
                                    //$html .= "<td align='right'>" .format_number($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"])."</td>";
                                    if($val['type'] == 2)
                                    {
                                        $transammnt=format_number($val[$val2["fieldName"]]*-1, $val[$val2["fieldName"] . "DecimalPlaces"]);
                                    }else
                                    {
                                        $transammnt=format_number($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]);
                                    }

                                    if($transammnt<0){
                                        $html .= "<td align='right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                        $html .= "<td align='right'>" . $transammnt . "</td>";
                                    }else{
                                        $html .= "<td align='right'>" . $transammnt . "</td>";
                                        $html .= "<td align='right'>" . format_number(0, $val[$val2 . "DecimalPlaces"]) . "</td>";
                                    }
                                } else {
                                    if($val['type'] == 2)
                                    {
                                        $locammnt=format_number($val[$val2["fieldName"]]*-1, $val[$val2["fieldName"] . "DecimalPlaces"]);
                                    }else
                                    {
                                        $locammnt=format_number($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]);
                                    }

                                    if($locammnt<0){
                                        $subtotalcred[$val2["fieldName"]][] = (float)$val[$val2["fieldName"]];
                                        $subtotaldeb[$val2["fieldName"]][] = (float)0;
                                        $html .= "<td class='text-right'>" . format_number(0, $val[$val2["fieldName"] . "DecimalPlaces"]) . "</td>";
                                        $html .= "<td class='text-right'>" . $locammnt . "</td>";
                                    }else{
                                        $subtotaldeb[$val2["fieldName"]][] = (float)$val[$val2["fieldName"]];
                                        $subtotalcred[$val2["fieldName"]][] = (float)0;
                                        $html .= "<td class='text-right'>" . $locammnt . "</td>";
                                        $html .= "<td class='text-right'>" . format_number(0, $val[$val2["fieldName"] . "DecimalPlaces"]) . "</td>";
                                    }
                                }
                            }
                        }
                        $html .= "</tr>";
                    }

                     $html .= "<tr>";
                    if ($isLocCost || $isRptCost) {
                        if($isTransCost){
                             $html .= "<td colspan='7'><div style='margin-left: 30px'>Net Balance</div></td>";
                        }else{
                             $html .= "<td colspan='4'><div style='margin-left: 30px'>Net Balance</div></td>";
                        }
                    }
                    if (!empty($fieldNameDetails)) {
                        foreach ($fieldNameDetails as $key => $val2) {
                            if($val2['fieldName'] == "companyLocalAmount"){
                                 //$html .= "<td class='reporttotal' align='right'>" . format_number(array_sum($subtotal[$val2['fieldName']]),$this->common_data['company_data']['company_default_decimal']) . "</td>";
                                $html .= "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2['fieldName']]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                $html .= "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2['fieldName']]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            }
                            if($val2['fieldName'] == "companyReportingAmount"){
                                 //$html .= "<td class='reporttotal' align='right'>" . format_number(array_sum($subtotal[$val2['fieldName']]),$this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                $html .= "<td class='text-right reporttotal'>" . format_number(array_sum($subtotaldeb[$val2['fieldName']]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                $html .= "<td class='text-right reporttotal'>" . format_number(array_sum($subtotalcred[$val2['fieldName']]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            }
                        }
                    }
                     $html .= "</tr>";
                }

                $html .= '</tbody>';
                $html .= '</table>';
                $mpdf->WriteHTML($html, 2);
                if($countCategory != $i){
                    $mpdf->AddPage();
                }
                $html="";

                $i++;
            }
        }
    }
} else {
    $html = warning_message("No Records Found!");
}

$mpdf->Output();