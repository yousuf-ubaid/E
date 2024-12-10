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
                            <img alt="Logo" style="height: 130px" src="'. $logo.$this->common_data['company_data']['company_logo'].'">
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
    <div style="text-align: center"><h4>'. $extra['master']['contractType'].'</h4></div>
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
                <td> '. $extra['master']['customerAddress'].'</td>

                <td width="20%"><strong>'. $extra['master']['contractType'].' '. $this->lang->line('common_date').'</strong></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['contractDate'].'</td>
            </tr>
            <tr>
                <td><strong> '. $this->lang->line('common_telephone').'  / '. $this->lang->line('common_fax').'  </strong></td><!--Telephone/Fax-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax'].'</td>

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
                <td><strong>Customer Email</strong><!--Currency--></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['customerEmail'].'</td>
                <td><strong>'. $extra['master']['contractType'].' '. $this->lang->line('sales_markating_erp_contract_expiry_date').' </strong></td><!--Expiry Date -->   <td><strong>:</strong></td>
                <td colspan="4"> '. $extra['master']['contractExpDate'].'</td>
            </tr>
            <tr>
                <td><strong> '. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['contractNarration'].'</td>
                <td><strong>Payment Terms</strong></td><!--Payment Terms -->   <td><strong>:</strong></td>
                <td colspan="4"> '. $extra['master']['paymentTerms'].  '&nbsp;Days'. '</td>
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
            <th style="min-width: 50%" class="theadtr" colspan="'.$hedcolspan.'">'. $this->lang->line('sales_markating_view_invoice_item_details').' </th><!--Item Details-->';
            if($extra['master']['isGroupBasedTax'] == 1){
                $html .= '<th style="min-width: 50%" class="theadtr" colspan="7">'. $this->lang->line('common_price').' ('.  $extra['master']['transactionCurrency']  .')</th>';
            } else {
                $html .= '<th style="min-width: 50%" class="theadtr" colspan="5">'. $this->lang->line('common_price').' ('.  $extra['master']['transactionCurrency']  .')</th>';
            }
            $html .= '</tr>
        <tr>
            <th style="width: 4%" class="theadtr">#</th>
            '.$imagede.'
            <th style="width: 10%" class="theadtr">'. $this->lang->line('common_code').'</th><!--Code-->
            <th style="width: 40%" class="text-left theadtr">'. $this->lang->line('common_description').'</th><!--Description-->
            <th style="width: 5%" class="theadtr">'. $this->lang->line('common_uom').'</th><!--UOM-->
            <th style="width: 10%" class="theadtr">'. $this->lang->line('common_qty').'</th><!--Qty-->
            <th style="width: 10%" class="theadtr">'. $this->lang->line('common_unit').'</th><!--Unit-->
            <th style="width: 11%" class="theadtr">'. $this->lang->line('common_discount').'</th><!--Discount-->
            <th style="width: 10%" class="theadtr">'. $this->lang->line('sales_markating_erp_contract_net_unit_price').'</th><!--Net Unit Price-->';
            if($extra['master']['isGroupBasedTax'] == 1){
                $html .= '<th style="width: 10%" class="theadtr">'. $this->lang->line('common_tax') .'<!--Tax--></th>
                            <th style="width: 10%" class="theadtr">Tax Amount<!--Tax Amount--></th>';
            }
            $html .= '<th style="width: 15%" class="theadtr">'. $this->lang->line('common_total').'</th><!--Total-->';
            $html .= '<th style="width: 15%" class="theadtr">'. $this->lang->line('common_retension').' '.$extra['master']['retentionPercentage'].'%</th><!--Total-->
       
        </tr>
        </thead>
        <tbody>';

        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
        if (!empty($extra['detail'])) {
        foreach ($extra['detail'] as $val) {
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
            <td class="text-center" style="font-size: 12px;">'. $val['unitOfMeasure'].'</td>
            <td style="text-align:right; font-size: 12px;">'. $val['requestedQtyNotFormated'].'</td>
            <td style="text-align:right; font-size: 12px;">'. number_format(($val['unittransactionAmount']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            <td style="text-align:right; font-size: 12px;">'. number_format($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).' ( '.$val['discountPercentage'].'%)</td>
            <td style="text-align:right; font-size: 12px;">'. number_format($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
            if($extra['master']['isGroupBasedTax'] == 1){
                $html .= '<td style="text-align:right; font-size: 12px;">'. $val['taxDescription'] .'</td>
                <td style="text-align:right; font-size: 12px;">' . number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>';
                $val['transactionAmount'] = $val['transactionAmount'] + $val['taxAmount'];
            }
            $html .= '<td style="text-align:right; font-size: 12px;">'. number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
            $html .= '<td style="text-align:right; font-size: 12px;">'. number_format($val['retensionValue'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            
            </tr>';

                $num++;
                $total += $val['transactionAmount'];
                $gran_total += $val['transactionAmount'];
                $tax_transaction_total += $val['transactionAmount'];
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
                        <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="10">
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
                            <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="8">
                                '. $this->lang->line('common_total').' ('.  $extra['master']['transactionCurrency']  .')</td>
                            <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                                class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        </tr>
                        </tfoot>
                    </table>
                <br>';
        }
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
                            <tr>';
                            if($extra['master']['isGroupBasedTax'] == 1){
                                $html .= '<td class="theadtr" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>'. $this->lang->line('sales_markating_view_invoice_tax_details').'</strong></td><!--Tax Details-->';
                            } else {
                                $html .= '<td class="theadtr" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>'. $this->lang->line('sales_markating_view_invoice_tax_details').'</strong></td><!--Tax Details-->';
                            }
                             $html .= '</tr>
                            <tr>
                                <th class="theadtr">#</th>';
                    if($extra['master']['isGroupBasedTax'] != 1){
                        $html .= '<th class="theadtr">'. $this->lang->line('common_type').'</th><!--Type-->';
                    }
                    $html .= '  <th class="theadtr">'. $this->lang->line('sales_markating_view_invoice_detail').'</th><!--Detail -->';
                    if($extra['master']['isGroupBasedTax'] != 1){
                        $html .= '<th class="theadtr">'. $this->lang->line('sales_markating_view_invoice_tax').'</th><!--Tax-->';
                    }      
                    $html .= '<th class="theadtr">'. $this->lang->line('common_transaction').'('. $extra['master']['transactionCurrency'].') </th><!--Transaction -->

                            </tr>
                        </thead>
                        <tbody>';
                            $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                            foreach ($extra['tax'] as $value) {
                                $html .= '<tr>
                                <td style="font-size: 12px;">'.$x.'</td>';
                                if($extra['master']['isGroupBasedTax'] != 1){
                                    $html .= '<td style="font-size: 12px;">'.$value['taxShortCode'].'</td>';
                                }
                                $html .= '<td style="font-size: 12px;">'.$value['taxDescription'].'</td>';
                                if($extra['master']['isGroupBasedTax'] != 1){
                                    $html .= '<td class="text-right" style="font-size: 12px;">'.$value['taxPercentage'].' % </td>
                                              <td class="text-right" style="font-size: 12px;">'.format_number((($value['taxPercentage']/ 100) * $tax_transaction_total),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                } else {
                                    $html .= '<td class="text-right" style="font-size: 12px;">'.format_number($value['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                }
                                $html .= '</tr>';
                                $x++;
                                if($extra['master']['isGroupBasedTax'] == 1){
                                    $gran_total += $value['amount'];
                                    $tr_total_amount+= $value['amount'];
                                } else {
                                    $gran_total += (($value['taxPercentage']/ 100) * $tax_transaction_total);
                                    $tr_total_amount+=(($value['taxPercentage']/ 100) * $tax_transaction_total);
                                }
                            }
        $html .= '</tbody>
                        <tfoot>
                            <tr>';
                            if($extra['master']['isGroupBasedTax'] == 1){
                                $html .= '<td colspan="2" class="text-right sub_total" style="font-size: 12px;">'. $this->lang->line('sales_markating_view_invoice_tax_total').'</td><!--Tax Total -->';
                            } else {
                                $html .= '<td colspan="4" class="text-right sub_total" style="font-size: 12px;">'. $this->lang->line('sales_markating_view_invoice_tax_total').'</td><!--Tax Total -->';
                            }
                            $html .= '<td class="text-right sub_total" style="font-size: 12px;">'.format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
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

    $data['documentCode'] = 'CNT';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['contractAutoID'];
    $html .= $this->load->view('system/tax/tax_detail_view.php',$data,true);

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

