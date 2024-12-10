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

$isRptCost = false;
$isLocCost = false;
$isTransCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }

    if (in_array("transactionAmount", $fieldName)) {
        $isTransCost = true;
    }
}
$html = "";
if (!empty($output)) {
    $customerArr = array();
    $customer = get_all_customers();
    if (!empty($customer)) {
        foreach ($customer as $val) {
            $vatNo = ($val["VatNO"]) ? "<br><span>Customer VATIN : <small>".$val["VatNO"]."</small></span>":"<br><span>Customer VATIN : <small>Not Available</small></span>";
            $customerArr[$val["customerSystemCode"] . " - " . $val["customerName"].$vatNo] = $val;
        }
    }
    $count = 8;
    $category = array();
    if ($isTransCost && !$isRptCost && !$isLocCost) {
        foreach ($output as $val) {
            $vatNo = ($val["VatNO"]) ? "<br><span>Customer VATIN : <small>".$val["VatNO"]."</small></span>":"<br><span>Customer VATIN : <small>Not Available</small></span>";
            $category[$val["customerSystemCode"] . " - " . $val["customerName"].$vatNo][$val["transactionAmountcurrency"]][] = $val;
        }
    } else {
        foreach ($output as $val) {
            $vatNo = ($val["VatNO"]) ? "<br><span>Customer VATIN : <small>".$val["VatNO"]."</small></span>":"<br><span>Customer VATIN : <small>Not Available</small></span>";
            $category[$val["customerSystemCode"] . " - " . $val["customerName"].$vatNo][] = $val;
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
                                <img alt="Logo" style="height: 80px" src="' . $this->common_data['company_data']['company_logo'] . '">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:70%;">
                    <table>
                        <tr>
                            <td>
                                <h3><strong>' . $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').' . '</strong></h3>
                                <p>'. $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country'].'</p>
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
                               <h4><strong>Customer Statement</strong></h4>
                            </td>
                        </tr>
                         <tr>
                            <td style="width: 19%">Date :</td>
                            <td>' . date('Y-m-d') . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>';
                $html .= '<table class="table" id="tbl_report" width="100%"><thead><tr><th align="left" style="border-bottom: 1px solid black;border-top:1px solid black">Doc Type</th><th align="left" style="border-bottom: 1px solid black;border-top:1px solid black">Doc Date</th><th align="left" style="border-bottom: 1px solid black;border-top:1px solid black">Doc Number</th><th align="left" style="border-bottom: 1px solid black;border-top:1px solid black">Narration</th>';
                if (!empty($fieldNameDetails)) {
                    foreach ($fieldNameDetails as $val) {
                        if ($val['fieldName'] == 'transactionAmount') {
                            $html .= '<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black">Currency</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Invoice amount</th>';
                            //$html .= '<th align="center" style="border-bottom: 1px solid black;border-top:1px solid black">VAT Identification No</th>';
                            $html .= '<th align="left" style="border-bottom: 1px solid black;border-top:1px solid black">Aging</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Receipt/CN amount</th>';
                            $html .= '<th align="right" style="border-bottom: 1px solid black;border-top:1px solid black">Balance</th>';
                        }
                    }
                }

                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';


                foreach ($currency as $key3 => $customers) {
                    $subtotal = array();
                    $subtotalreceipt = array();
                    $subtotalbalance = array();
                    $type = '';
                    foreach ($customers as $key4 => $val) {
                        if($val["transactionAmount"]-$val["receiptAmount"] != 0) {
                            if($val['type'] == 2 && $type == '')
                            {
                                $type = $val['type'];
                                $html .= "<tr>";
                                $html .= "<td colspan='6'><u><b>Post Dated Cheques</b></u></td>";
                                $html .= "</tr>";
                            }
                            //$vatNo = ($val["VatNO"]) ? $val["VatNO"]:'-';
                            $html .= "<tr>";
                            $html .= "<td>" . $val["document"] . "</td>";
                            $html .= "<td><div style='margin-left: 30px'>" . $val["bookingDate"] . "</div></td>";
                            $html .= '<td>' . $val["bookingInvCode"] . '</td>';
                            $html .= "<td>" . $val["comments"] . "</td>";
                            $html .= "<td>" . $val["transactionAmountcurrency"] . "</td>";
                            $html .= "<td align='right'>" . number_format($val["transactionAmount"], $val["transactionAmountDecimalPlaces"]) . "</td>";
                            //$html .= "<td>" . $vatNo. "</td>";
                            $html .= "<td>" . $val["age"] . "</td>";
                            $html .= "<td align='right'>" . number_format($val["receiptAmount"], $val["transactionAmountDecimalPlaces"]) . "</td>";
                            $html .= "<td align='right'>" . number_format($val["transactionAmount"]-$val["receiptAmount"], $val["transactionAmountDecimalPlaces"]) . "</td>";
                            $html .= "</tr>";
                            $subtotal["transactionAmount"][] = $val["transactionAmount"];
                            $subtotalreceipt["transactionAmount"][] = $val["receiptAmount"];
                            $subtotalbalance["transactionAmount"][] = $val["transactionAmount"]-$val["receiptAmount"];
                            $grandtotal["transactionAmount"][] = $val["transactionAmount"];
                        }
                    }


                    $html .= "<tr>";
                    $html .= "<td colspan='5' align='right'><div><strong>Total</strong></div></td>";
                    if (!empty($fieldNameDetails)) {
                        foreach ($fieldNameDetails as $key => $val2) {
                            $newArray2 = $subtotal[$val2['fieldName']];
                            $newArrayR = $subtotalreceipt[$val2['fieldName']];
                            $newArrayB = $subtotalbalance[$val2['fieldName']];

                            $pos_arr = array();
                            $neg_arr = array();
                            foreach ($newArray2 as $val) {
                                ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;

                            }

                            $positiveAmount = array_sum($newArray2);

                            $negativeAmount = array_sum($newArrayR);
                            $balAmount = array_sum($newArrayB);
                            if ($val2['fieldName'] == "transactionAmount") {
                                $html .= '<td align="right"><strong>' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</strong></td>';
                                $html .= '<td></td>';
                                $html .= '<td align="right"><strong>' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</strong></td>';
                                $html .= '<td align="right"><strong>' . number_format(abs($balAmount), $this->common_data['company_data']['company_default_decimal']) . '</strong></td>';
                            }
                        }
                    }
                    $html .= "</tr>";

                    $html .= "<tr>";
                    $html .= "<td colspan='5' align='right'><div><strong>Net Balance</strong></div></td>";
                    if (!empty($fieldNameDetails)) {
                        foreach ($fieldNameDetails as $key => $val2) {
                            $newArray2 = $subtotalbalance[$val2['fieldName']];
                            $pos_arr = array();
                            $neg_arr = array();
                            foreach ($newArray2 as $val) {
                                ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                            }
                            $positiveAmount = array_sum($pos_arr);
                            $negativeAmount = array_sum($neg_arr);
                            $balance = $positiveAmount + $negativeAmount;
                            if ($val2['fieldName'] == "transactionAmount") {
                                if ($balance < 0) {
                                    $html .= "<td align='right'></td><td align='right'><strong>" . number_format(abs($balance), $this->common_data['company_data']['company_default_decimal']) . "</strong></td>";
                                } else {
                                    if ($balance > 0) {
                                        $html .= "<td align='right'><strong>" . number_format($balance, $this->common_data['company_data']['company_default_decimal']) . "</strong></td><td align='right'></td>";
                                    } else {
                                        $html .= "<td align='right'></td><td align='right'></td>";
                                    }
                                }
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
?>