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
            $waterMark = 'Not Approved';
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



$html = "";
if (!empty($extra)) {
    if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)){
        $html .= '<div class="table-responsive">
<table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="'. $this->common_data['company_data']['company_logo'].'">
                            <img alt="Logo" style="height: 130px" src="'. $this->common_data['company_data']['company_logo'].'">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong>'. $this->common_data['company_data']['company_name'] .'</strong></h3>
                            <p>'. $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country'].'</p>

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
    <div style="text-align: center"><h4> Payment Application </h4></div>
    <div style="text-align: center"><h4> Payment Application </h4></div>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong>'. $this->lang->line('common_customer_name').'</strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> '. $custnam .'</td>

            <td width="20%"><strong>'. $this->lang->line('common_number').' </strong></td>
            <td width="20%"><strong>'. $this->lang->line('common_number').' </strong></td>
            <td><strong>:</strong></td>
            <td>'. $extra['PADocumentID']['documentID'] .'</td>
        </tr>';

    if (!empty($extra['master']['customerSystemCode'])) {
        $html .= '<tr>
                <td><strong>'. $this->lang->line('sales_markating_view_invoice_customer_address').'  </strong></td><!--Customer Address-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['customerAddress'].'</td>

                <td width="20%"><strong>'. $this->lang->line('common_date').'</strong></td>
                <td width="20%"><strong>'. $this->lang->line('common_date').'</strong></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['contractDate'].'</td>
            </tr>
            <tr>
                <td><strong> '. $this->lang->line('common_telephone').'  / '. $this->lang->line('common_fax').'  </strong></td><!--Telephone/Fax-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax'].'</td>

                <td width="20%"><strong>'.$this->lang->line('sales_markating_erp_contract_expiry_date').'</strong></td><!--Expiry Date-->
                <td width="20%"><strong>'.$this->lang->line('sales_markating_erp_contract_expiry_date').'</strong></td><!--Expiry Date-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['contractExpDate'].'</td>
                <td>'. $extra['master']['contractExpDate'].'</td>
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
                <td><strong>Customer Email</strong><!--Currency--></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['customerEmail'].'</td>
                <td><strong>Payment Terms </strong></td><!--Payment Terms -->   <td><strong>:</strong></td>
                <td><strong>Payment Terms </strong></td><!--Payment Terms -->   <td><strong>:</strong></td>
                <td colspan="4"> '. $extra['master']['paymentTerms'].  '&nbsp;Days'. '</td>
            </tr>           
            </tr>           
       </tbody>
    </table>
</div><br>';

        if($extra['master']['showImageYN']==1){
            $hedcolspan="6";
            $imagede='<th class="theadtr" style="width: 7%">'. $this->lang->line('common_item_image').'</th>';
        }else{
            $hedcolspan="5";
            $imagede='';
        }

$html .= '
    <table class="table" style="width: 100%;">
        <thead  class="thead">
       
       
        <tr>
            <th style="width: 4%" class="theadtr">#</th>
            '.$imagede.'
            <th style="width: 10%" class="theadtr">Item </th><!--Item-->
            <th style="width: 10%" class="theadtr">Item </th><!--Item-->
            <th style="width: 40%" class="text-left theadtr">'. $this->lang->line('common_description').'</th><!--Description-->
            <th style="width: 5%" class="theadtr">Cu.Qty</th><!--Cu. Qty-->
            <th style="width: 5%" class="theadtr">Prev.Qty</th><!--Prev. Qty-->
            <th style="width: 10%" class="theadtr">Curt. Qty</th><!--Current Qty-->  
            <th style="width: 10%" class="theadtr">Rate</th><!--Rate-->          
            <th style="width: 10%" class="theadtr">Cu.Amt</th><!--Cu. Amt-->
            <th style="width: 11%" class="theadtr">Prev.Amt</th><!--Prev. Amt-->';
            $html .= '<th style="width: 15%" class="theadtr">Current Amt</th><!--Total-->
        </tr>
        </thead>
        <tbody>';

        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
    if (!empty($extra['detailsReport'])) {
        foreach ($extra['detailsReport'] as $val) {
            if($extra['master']['showImageYN']==1){
                if(!empty($val['itemImage'])){
                    $imagetd='<td class="text-center" style="width: 30%"><a class="thumbnail_custom"><img style="width:250px;" src="'.$this->s3->createPresignedRequest('uploads/itemMaster/'.$val['itemImage'], '1 hour').'" class="imgThumb img-rounded"/></a></td>';
                }else{
                    $imagetd='<td class="text-center" style="width: 30%"><a class="thumbnail_custom"><img style="width:250px;" src="'.$this->s3->createPresignedRequest('images/item/no-image.png', '1 hour').'" class="imgThumb img-rounded"/></a></td>';
                }
            }else{
                $imagetd='';
            }
            $html .= '<tr>
            <td class="text-right" style="font-size: 12px;">'. $num.'.&nbsp;</td>
            '.$imagetd.'
            <td class="text-center" style="font-size: 12px;width:15%" >'. $val['itemSystemCode'].'</td>
            <td style="font-size: 12px;width:30%">'. wordwrap($val['itemDescription'],250,"<br>\n") .' - '. $val['comment'].'</td>
            <td class="text-center" style="font-size: 12px;">'. $val['PAcuQty'].'</td>
            <td class="text-center" style="font-size: 12px;">'. $val['prevQty'].'</td>
            <td style="text-align:right; font-size: 12px;">'. $val['currentQty'].'</td>     
            <td style="text-align:right; font-size: 12px;">'. number_format(($val['unittransactionAmount'] + $val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>       
            <td style="text-align:right; font-size: 12px;">'. number_format($val['cumilativeAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            <td style="text-align:right; font-size: 12px;">'. number_format($val['prevAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
            $html .= '<td style="text-align:right; font-size: 12px;">'. number_format($val['currentAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
        </tr>';

                $num++;
                $total += $val['currentAmount'];
                $gran_total += $val['currentAmount'];
                $tax_transaction_total += $val['currentAmount'];
            }
        } else {
            $norecordsfound= $this->lang->line('common_no_records_found');
            if($extra['master']['showImageYN']==1){
                $html .= '<tr class="danger"><td colspan="10" class="text-center" style="font-size: 12px;">'.$norecordsfound.'</td></tr>';
            }else{
                $html .= '<tr class="danger"><td colspan="9" class="text-center" style="font-size: 12px;">'.$norecordsfound.'</td></tr>';
            }

        }
    if($extra['master']['showImageYN']==1){
        if($extra['master']['isGroupBasedTax'] == 1){
            $html .= '</tbody>
                    <tfoot>
                    <tr>
                        <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="11">
                            '. $this->lang->line('common_total').' ('.  $extra['master']['transactionCurrency']  .')</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>
                    </tfoot>
                </table>
            <br>';
        } else {
            $html .= '</tbody>
                    <tfoot>
                    <tr>
                        <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="9">
                            '. $this->lang->line('common_total').' ('.  $extra['master']['transactionCurrency']  .')</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>
                    </tfoot>
                </table>
            <br>';
        }
    }else{
        if($extra['master']['isGroupBasedTax'] == 1){
            $html .= '</tbody>
                    <tfoot>
                    <tr>
                        <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="9">
                            '. $this->lang->line('common_total').' ('.  $extra['master']['transactionCurrency']  .')</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>
                    </tfoot>
                </table>
            <br>';
        } else {
            $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="9">
                                '. $this->lang->line('common_total').' ('.  $extra['master']['transactionCurrency']  .')</td>
                            <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                                class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        </tr>
                        </tfoot>
                    </table>
                <br>';
        }
    }



$html .= '<br>';
$html .= '<br>';

    $data['documentCode'] = 'CNT';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['contractAutoID'];

    $html .= '<br>
    <div class="table-responsive">
        <div style="text-align: center"><h4> Company Bank Details </h4></div>
        <div style="text-align: center"><h4> Company Bank Details </h4></div>
        <table style="width: 100%">
            <tr>
                <td style="width:50%;">
                <td style="width:50%;">

                    <table style="width: 100%">
                        <tbody>';
                        if($extra['master']['confirmedYN']==1){
                    $html .= '<tr>
                                <td><b>Account Name </b></td>';
                    $html .= '<tr>
                                <td><b>Account Name </b></td><!--Account Name -->
                                <td><strong>:</strong></td>
                                <td>'. $custnam .'</td>
                                <td>'. $custnam .'</td>
                            </tr>';
                         }
                         if($extra['master']['approvedYN']){
                    $html .= '<tr>
                                <td><b>Account No </b></td><!--Account No -->
                                <td><strong>:</strong></td>
                                <td>'. $extra['bankDetails']['bankAccountNumber'].'</td>
                            </tr>';
                        }
                    $html .= '</tbody>
                    </table>';
                    $html .= '<tr>
                                <td><b>Account No </b></td><!--Account No -->
                                <td><strong>:</strong></td>
                                <td>'. $extra['bankDetails']['bankAccountNumber'].'</td>
                            </tr>';
                        }
                    $html .= '</tbody>
                    </table>

                </td>
                <td style="width:50%;">

                    <table style="width: 100%">
                        <tbody>';
                        if($extra['master']['confirmedYN']==1){
                    $html .= '<tr>
                                <td><b>Bank Name </b></td><!--Bank Name -->
                                <td><strong>:</strong></td>
                                <td>'. $extra['bankDetails']['bankName'].'</td>
                            </tr>';
                         }
                         if($extra['master']['approvedYN']){
                    $html .= '<tr>
                                <td><b>IBAN </b></td><!--IBAN -->
                                <td><strong>:</strong></td>
                                <td>'. $extra['bankDetails']['bankSwiftCode'].'</td>
                            </tr>';
                        }
                    $html .= '</tbody>
                    </table>

                </td>
                </td>
                <td style="width:50%;">

                    <table style="width: 100%">
                        <tbody>';
                        if($extra['master']['confirmedYN']==1){
                    $html .= '<tr>
                                <td><b>Bank Name </b></td><!--Bank Name -->
                                <td><strong>:</strong></td>
                                <td>'. $extra['bankDetails']['bankName'].'</td>
                            </tr>';
                         }
                         if($extra['master']['approvedYN']){
                    $html .= '<tr>
                                <td><b>IBAN </b></td><!--IBAN -->
                                <td><strong>:</strong></td>
                                <td>'. $extra['bankDetails']['bankSwiftCode'].'</td>
                            </tr>';
                        }
                    $html .= '</tbody>
                    </table>

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

