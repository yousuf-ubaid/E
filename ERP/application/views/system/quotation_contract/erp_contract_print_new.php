<?php

use Mpdf\Mpdf;

if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)) {
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
}else{
    $mpdf = new Mpdf(
        [
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 40,
            'margin_bottom'     => 10,
            'margin_header'     => 20,
            'margin_footer'     => 3,
            'orientation'       => 'P'
        ]
    );
}
$user = ucwords($this->session->userdata('username'));
$date = date('l jS \of F Y h:i:s A');
$stylesheet = file_get_contents('plugins/bootstrap/css/print_style.css');

if ($printHeaderFooterYN == 0) {
    $mpdf->SetFooter();
} if ($printHeaderFooterYN == 1) {
    $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);

}else if ($printHeaderFooterYN == 2) {
    $mpdf->SetFooter();
}else if ($printHeaderFooterYN == 3) {
    $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
}

$mpdf->WriteHTML($stylesheet, 1);

$water_mark_status = policy_water_mark_status('All');
if ($Approved != 1 and $water_mark_status == 1) {
    $waterMark = '';
    switch ($Approved) {
        case 0;
            $waterMark = 'Draft';
            break;
        case 2;
            $waterMark = 'Referred Back';
            break;
        case 3;
            $waterMark = 'Rejected';
            break;
    }
    $mpdf->SetWatermarkText($waterMark);
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.07;
}

$projectExist = project_is_exist();
$project_arr = [];
$projectsdesc='';

if (!empty($extra['detail']) && $projectExist==1) {
    $projects = array_group_by($extra['detail'], 'projectIDNew');
    if(!empty($projects)){
        foreach ($extra['detail'] as $row) {
            $project_arr[trim($row['projectIDNew'] ?? '')] = trim($row['projectName'] ?? '');
        }
        $a= 1;
        foreach ($project_arr as $row) {
            $projectsdesc .=$a.'. '.$row.'<br/>';
            $a++;
        }
    }
}

$html = "";
if (!empty($extra)) {
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){

        $html .= '<div class="table-responsive">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width:35%;">
                            <table>
                                <tr>
                                    <td style="color: #0070c0" colspan="3">
                                        <h2><strong>WAHT ALMSTQBL ALRYDT</strong></h2>
                                        <p style="font-size: 12px">
                                            Fire & Safety, Electrical Projects, Civil Projects, CCTV, Bio-metric Access control
                                            <strong>Address: P.O.BOX:1666,P.C.23<br/>
                                            Al Noor Building-Al Qurum, Muscat
                                            Sultanate Of Oman <br/>
                                            PH:+968 24480765</strong></p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:30%;">
                            <table>
                                <tr>
                                    <td>
                                        <img alt="Logo" style="height: 130px" src="'. $logo.$this->common_data['company_data']['company_logo'].'">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:35%;">
                            <table>
                                <tr>
                                    <td style="color: #0070c0;text-align:right;"  colspan="3">
                                        
                                        <p style="font-size: 12px;">شركة واحة المستقبل الرائدة 
                                        المشاريع الكهربائيةوالمدنية ،السلامة<br> والحرائق،والكاميرات والعقود الثانوية<br>
                                        الطابق الاول ،الشقة رقم ١٨<br>
                                        الصندوق البريدي :١٦٦٦<br>
                                        الرمز البريدي :٢٣<br>
                                        بناية النور -مسقط -سلطنة عمان</p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>';
    }
    $custnam = '';
    if(empty($extra['customer']['customerSystemCode'])){
        $custnam= $extra['customer']['customerName'];
    }else{
        $custnam= $extra['customer']['customerName'] .' ('. $extra['customer']['customerSystemCode'] .')';
    }

    $html .= ' <div class="table-responsive">
    <h3 style="text-align: center">'. $extra['master']['contractType'].'</h3>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong>'. $this->lang->line('common_customer_name').'</strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> '. $custnam .'</td>

            <td width="20%"><strong>'. $extra['master']['contractType'].' '.$this->lang->line('common_number').' </strong></td>
            <td><strong>:</strong></td>
            <td>'.$extra['master']['contractCode'].'(V '.$extra['master']['versionNo'].')</td>
        </tr>';

    if (!empty($extra['master']['customerSystemCode'])) {
        $html .= '<tr>
                <td><strong>'. $this->lang->line('sales_markating_view_invoice_customer_address').'  </strong></td><!--Customer Address-->
                <td><strong>:</strong></td>
                <td> '. $extra['customer']['customerAddress1'].'</td>

                <td width="20%"><strong>'. $extra['master']['contractType'].' '. $this->lang->line('common_date').'</strong></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['contractDate'].'</td>
            </tr>
            <tr>
                <td><strong> '. $this->lang->line('common_telephone').'  / '. $this->lang->line('common_fax').'  </strong></td><!--Telephone/Fax-->
                <td><strong>:</strong></td>
                <td> '. $extra['customer']['customerTelephone'].' / '.$extra['customer']['customerFax'].'</td>

                <td width="20%"><strong>'.$this->lang->line('common_reference_number').'</strong></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['referenceNo'].'</td>
            </tr>';
    }
    $append = '';
    if(!empty($extra['master']['segmentcodemaster']))
    {
       $append .='<td><strong>Segment</strong></td>
                  <td><strong>:</strong></td>
                  <td>'. $extra['master']['segDescription'].' ('.$extra['master']['segmentcodemaster'].')</td>
                ';
    }

    $html .= '<tr>
                <td><strong>'. $this->lang->line('common_currency').'</strong><!--Currency--></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )</td>
                '.$append.'
              </tr>
            <tr>
            
                <td><strong>'. $extra['master']['contractType'].' '. $this->lang->line('sales_markating_erp_contract_expiry_date').' </strong></td><!--Expiry Date -->   
                <td><strong>:</strong></td>
                <td> '. $extra['master']['contractExpDate'].'</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong> '. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td> '.$extra['master']['contractNarration'].'</td>
            </tr>
       </tbody>
    </table>
</div><br>';

    $html .= ' 
  <div class="table-responsive">
     <!-- <table style="width: 100%">
        <tbody>
        <tr>
            <td width="60%" style=""><strong></strong></td>
            <td >'.$this->lang->line('common_reference_number').' </td>
            <td>:</td>
            <td>'.$extra['master']['contractCode'].'(V '.$extra['master']['versionNo'].')</td>
        </tr>   
        <tr>
            <td width="60%" style=""><strong></strong></td>
            <td >'.$this->lang->line('common_date').'</td>
            <td>:</td>
            <td>'.$extra['master']['contractDate'].'</td>
        </tr>   
       </tbody>
    </table> -->
    <div>
        <table style="width: 100%">
            <tbody>
            <tr>
                <td width="10%" style="font-size:12px;vertical-align: top" >'.$this->lang->line('common_project').':</td>
                <td style="font-size:12px;text-align: left;"><strong>'.$projectsdesc.'</strong></td>
            </tr>   
              
           </tbody>
        </table>
        <div style="text-align: left;padding:5px;">
            <p style="font-size:11px;">Dear Sir/ Madam, <br/> In regards to your enquiry for the above project,kindly find the enclosed offer with the following details that mentioned below</p>
            <p style="font-size:12px;"><Strong><u>BOQ</u></Strong></p>
        </div>
    </div>
  </div>';
if (!empty($extra['detail'])) {
    if ($projectExist == 1) {
        $projectnum = 1;
        $gran_total = 0;
        foreach ($projects as $project) {
            $html .= '
            <table class="table" style="width: 100%;">
            <thead  class="thead">
                <tr>
                <th colspan="6" style="text-align: left">' . $projectnum . '. ' . $project[0]['projectName'] . '</th>
                </tr>
                <tr>
                    <th style="width: 4%;text-transform: uppercase;" class="theadtr" >NO</th>
                    <th style="width: 40%;text-transform: uppercase;" class="text-left theadtr">' . $this->lang->line('common_description') . '</th><!--Description-->
                    <th style="width: 10%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_unit') . '</th><!--Unit-->
                    <th style="width: 10%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_qty') . '</th><!--Qty-->
                    <th style="width: 15%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_rate') . '</th><!--Rate-->
                    <th style="width: 15%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_amount') . '</th><!--Amount-->
                </tr>
            </thead>
            <tbody>';
                $num = 1;
                $total = 0;
                //$gran_total = 0;
                $tax_transaction_total = 0;

                foreach ($project as $val) {

                    $html .= '<tr>
                        <td class="text-right" style="font-size: 12px;">' . $num . '.&nbsp;</td>
                        <td style="font-size: 12px;width:30%">' . $val['itemSystemCode'] . ' - ' . wordwrap($val['itemDescription'], 250, "<br>\n") . ' - ' . $val['comment'] . '</td>
                        <td class="text-center" style="font-size: 12px;">' . $val['unitOfMeasure'] . '</td>
                        <td style="text-align:right; font-size: 12px;">' . $val['requestedQtyNotFormated'] . '</td>
                        <td style="text-align:right; font-size: 12px;">' . number_format($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        <td style="text-align:right; font-size: 12px;">' . number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    </tr>';
                    $num++;
                    $total += $val['transactionAmount'];
                    $gran_total += $val['transactionAmount'];
                    $tax_transaction_total += $val['transactionAmount'];
                }
            $html .= '
            </tbody>
            <tfoot>
                <tr>
                    <td style="min-width: 85%  !important; text-align:center; font-size: 12px;text-transform: uppercase;" class="text-right sub_total" colspan="5">
                        '.$this->lang->line('common_total_amount').'(' . $extra['master']['transactionCurrency'] . ')</td>
                    <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                        class="text-right total">' . number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                </tr>
            </tfoot>
            </table><br/>';
            $projectnum++;
        }
    } else {
        $html .= '
        <table class="table" style="width: 100%;">
            <thead  class="thead">
                <tr>
                <th style="width: 4%;text-transform: uppercase;" class="theadtr">NO</th>
                <th style="width: 40%;text-transform: uppercase;" class="text-left theadtr">' . $this->lang->line('common_description') . '</th><!--Description-->
                <th style="width: 10%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_unit') . '</th><!--Unit-->
                <th style="width: 10%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_qty') . '</th><!--Qty-->
                <th style="width: 15%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_rate') . '</th><!--Rate-->
                <th style="width: 15%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_amount') . '</th><!--Amount-->
                </tr>
            </thead>
            <tbody>';
            $num = 1;
            $total = 0;
            $gran_total = 0;
            $tax_transaction_total = 0;
            foreach ($extra['detail'] as $val) {
                $html .= '<tr>
                    <td class="text-right" style="font-size: 12px;">'. $num.'.&nbsp;</td>
                    <td style="font-size: 12px;width:30%">'. $val['itemSystemCode'] .' - '. wordwrap($val['itemDescription'],250,"<br>\n") .' - '. $val['comment'].'</td>
                    <td class="text-center" style="font-size: 12px;">'. $val['unitOfMeasure'].'</td>
                    <td style="text-align:right; font-size: 12px;">'. $val['requestedQtyNotFormated'].'</td>
                    <td style="text-align:right; font-size: 12px;">'. number_format($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right; font-size: 12px;">'. number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';

                $num++;
                $total += $val['transactionAmount'];
                $gran_total += $val['transactionAmount'];
                $tax_transaction_total += $val['transactionAmount'];
            }
            $html .= '
            </tbody>
            <tfoot>
                <tr >
                    <td class="theadtr" style="min-width: 85%  !important; text-align:center; font-size: 12px;text-transform: uppercase;" class="text-right sub_total" colspan="5">
                       '.$this->lang->line('common_total_amount').' (' . $extra['master']['transactionCurrency'] . ')</td>
                    <td  class="theadtr" style="text-align:right;min-width: 15% !important;font-size: 12px; "
                        class="text-right total">' . number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                </tr>
            </tfoot>
        </table><br/>';
    }
}else{
    $norecordsfound= $this->lang->line('common_no_records_found');
    $html .= '<table class="table" style="width: 100%;">
        <thead  class="thead">
            <tr>
            <th style="width: 4%;text-transform: uppercase;" class="theadtr">NO</th>
            <th style="width: 40%;text-transform: uppercase;" class="text-left theadtr">' . $this->lang->line('common_description') . '</th><!--Description-->
            <th style="width: 10%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_unit') . '</th><!--Unit-->
            <th style="width: 10%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_qty') . '</th><!--Qty-->
            <th style="width: 15%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_rate') . '</th><!--Rate-->
            <th style="width: 15%;text-transform: uppercase;" class="theadtr">' . $this->lang->line('common_amount') . '</th><!--Amount-->
            </tr>
        </thead>
        <tbody><tr class="danger"><td colspan="6"  style="background-color:#f2dede;text-align:center;font-size: 12px;">'.$norecordsfound.'</td></tr></tbody></table>';

}

$html .= '<div class="table-responsive">
    <table style="width: 100%">
        <tr>
           <td style="width:40%;">
                &nbsp;
           </td>
           <td style="width:60%;padding: 0;">';

            if (!empty($extra['tax'])) {
                $html .= '<table style="width: 100%" class="table">
                        <thead>
                            <tr>
                                <td class="theadtr" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>'. $this->lang->line('sales_markating_view_invoice_tax_details').'</strong></td><!--Tax Details-->
                            </tr>
                            <tr>
                                <th class="theadtr">#</th>
                                <th class="theadtr">'. $this->lang->line('common_type').'</th><!--Type-->
                                <th class="theadtr">'. $this->lang->line('sales_markating_view_invoice_detail').'</th><!--Detail -->
                                <th class="theadtr">'. $this->lang->line('sales_markating_view_invoice_tax').'</th><!--Tax-->
                                <th class="theadtr">'. $this->lang->line('common_transaction').'('. $extra['master']['transactionCurrency'].') </th><!--Transaction -->

                            </tr>
                        </thead>
                        <tbody>';
                            $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                            foreach ($extra['tax'] as $value) {
                                $html .= '<tr>
                                <td style="font-size: 12px;">'.$x.'</td>
                                <td style="font-size: 12px;">'.$value['taxShortCode'].'</td>
                                <td style="font-size: 12px;">'.$value['taxDescription'].'</td>
                                <td class="text-right" style="font-size: 12px;">'.$value['taxPercentage'].' % </td>
                                <td class="text-right" style="font-size: 12px;">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                </tr>';
                                $x++;
                                $gran_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                                $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);

                            }
$html .= '</tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right sub_total" style="font-size: 12px;">'. $this->lang->line('sales_markating_view_invoice_tax_total').'</td><!--Tax Total -->
                        <td class="text-right sub_total" style="font-size: 12px;">'.format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>
                </tfoot>
            </table>';
             }
    $html .= '</td>
        </tr>
    </table>
</div>
<div class="table-responsive">
    <h5 class="text-right" style="font-size: 12px; text-align: right;">'. $this->lang->line('common_total').' ('. $extra['master']['transactionCurrency'].' )<!--Total-->
        : '. format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</h5>
</div>
<br>';

 if ($extra['master']['Note']) {
     $html .= '<div class="table-responsive"><br>
    <h6>'.$this->lang->line('common_notes').'</h6>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td>'. $extra['master']['Note'].'</td>
        </tr>
        </tbody>
    </table>';
    }

    $data['documentCode'] = 'CNT';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['contractAutoID'];
    $html .= $this->load->view('system/tax/tax_detail_view.php',$data,true);





    $html .= '<br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:57%;">

                    <table style="width: 100%">
                        <tbody>';
                        if($extra['master']['confirmedYN']==1){
$html .= '<tr>
                                <td><b>'.$this->lang->line('common_confirmed_by').'</b></td>
                                <td><strong>:</strong></td>
                                <td>'. $extra['master']['confirmedYNn'].'</td>
                            </tr>';
                         }
                         if($extra['master']['approvedYN']){
$html .= '<tr>
                    <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').'</b></td><!--Electronically Approved By-->
                    <td><strong>:</strong></td>
                    <td>'. $extra['master']['approvedbyEmpName'].'</td>
                </tr>
                <tr>
                    <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_date').' </b></td><!--Electronically Approved Date-->
                    <td><strong>:</strong></td>
                    <td>'. $extra['master']['approvedDate'].'</td>
                </tr>';
            }
$html .= '</tbody>
        </table>

        </td>
        <td style="width:60%;">
            &nbsp;
        </td>
    </tr>
    </table>
</div>
    <br>
    <br>
    <br>
    <br>';
    if($extra['master']['approvedYN']){

        if ($signature) {
            if ($signature['approvalSignatureLevel'] <= 2) {
                $width = "width: 50%";
            } else {
                $width = "width: 100%";
            }

            $html .= '<div class="table-responsive">
                <table style="'. $width .'">
                    <tbody>
                    <tr><td><span><b>Yours Sincerely, </b></span></td></tr>
                    <tr>';

                        for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                            $html .= '<td>
                                <span>____________________________</span><br><br><span><b>&nbsp;&nbsp; '.$this->lang->line('common_authorized_signature').'</b></span>
                            </td>';


                        }
            $html .= '</tr>


                    </tbody>
                </table>
            </div>';
       }
     }


    $mpdf->WriteHTML($html, 2);
    //$mpdf->AddPage();
    $html="";

} else {
    $html = warning_message("No Records Found!");
}

$mpdf->Output();
?>

