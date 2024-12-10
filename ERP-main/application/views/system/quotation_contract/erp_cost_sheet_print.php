<?php

use Mpdf\Mpdf;

if(($printHeaderFooterYN==1) || ($printHeaderFooterYN==2)) {
    $mpdf = new Mpdf([
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
    ]);
}else{
    $mpdf = new Mpdf([
        'mode'              => 'utf-8',
        'format'            => 'A5',
        'default_font_size' => 9,
        'default_font'      => 'arial',
        'margin_left'       => 5,
        'margin_right'      => 5,
        'margin_top'        => 40,
        'margin_bottom'     => 10,
        'margin_header'     => 20,
        'margin_footer'     => 3,
        'orientation'       => 'P'
    ]);
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
            <div style="text-align: center"><h4>Cost Sheet</h4></div>
            <div class="table-responsive">    
            <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width: 43%">
                    <table style="width: 100%;" class="table-border">
                        <tr>
                            <td style="font-size:10px">Principal</td><!--Customer Name-->
                            <td style="font-size:10px">'.$supplier_details['supplierName'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Client</td>
                            <td style="font-size:10px">'.$header['customerName'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Currency </td>
                            <td style="font-size:10px">'.$header['transactionCurrency'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Delivery (Weeks)</td>
                            <td style="font-size:10px">'.$master['deliveryWeek'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Comm. | '.$commissionPercentage.'%</td>
                            <td style="font-size:10px">'.number_format($po_material_commission,2).'    </td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Mark-up. | '.$header['marginPercentage'].'%</td>
                            <td style="font-size:10px">'.number_format($po_material_markup,2).'    </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 16%"></td>
                <td style="width: 43%">
                    <table style="width: 100%;" class="table-border">
                        <tr>
                            <td style="font-size:10px">Principal Ref.	</td>
                            <td style="font-size:10px">'.$master['principleRef'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Client Ref. </td>
                            <td style="font-size:10px">'.$master['clientRef'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">To '.$compayLocalCurrency.' @	</td>
                            <td style="font-size:10px">'.number_format($po_material_markup_aed['conversion'], 4).'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">MSE Ref.	</td>
                            <td style="font-size:10px">'.$header['contractCode'].'</td>
                        </tr>
                        <tr>
                            <td style="font-size:10px">Mode of Shipment	</td>
                            <td style="font-size:10px">'.get_mode_of_travel($master['modeOfPayment']).'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tbody>
        </table>
    </div>
    </div><br>';

        if($extra['master']['showImageYN']==1){
            $hedcolspan="6";
            $imagede='<th class="theadtr" style="width: 7%">'. $this->lang->line('common_item_image').'</th>';
        }else{
            $hedcolspan="5";
            $imagede='';
        }

$html .= '
    <table class="table  table-border" style="width: 100%;">
        <thead  class="thead">
        
        <tr>
            <th style="width: 4%" class="theadtr">#</th>
            '.$imagede.'
            <th style="width: 40%" class="text-left theadtr">'. $this->lang->line('common_description').'</th><!--Description-->
          ';

            $html .= '<th style="width: 15%" class="theadtr">'. 'Estimate ('.$header['transactionCurrency'].')'.'</th><!--Total-->';
            $html .= '<th style="width: 15%" class="theadtr">'. 'Estimate ('.$compayLocalCurrency.')'.'</th><!--Total-->
        </tr>
        </thead>
        <tbody>';

        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
        $total_aed = 0;
        $net_amount = 0;

        $margin_cost = 0;
        $margin_cost_sales = 0;

    if (!empty($extra['detail'])) {
        $html .= '<tr>
            <td>'.$num.'</td>
            <td>Material Cost (Ex-works Packed) </td>
            <td style="text-align:right; font-size: 12px;">'.number_format($po_material_cost, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            <td style="text-align:right; font-size: 12px;">'.number_format($po_material_cost_aed['convertedAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
        </tr>';

        $total += $po_material_cost;
        $total_aed += $po_material_cost_aed['convertedAmount'];
        $net_amount += ($po_material_cost*10)/100;

        $num += 1;
        foreach ($charge_data as $val) {
            $total_aed += $val['extraCostValueAED'];
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
            <td style="font-size: 12px;width:30%">'. wordwrap($val['extraCostName'],250,"<br>\n") .' - '. $val['comment'].'</td>
            ';
            $html .= '<td style="text-align:right; font-size: 12px;">'. number_format($val['extraCostValue'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
            $html .= '<td style="text-align:right; font-size: 12px;">'. number_format($val['extraCostValueAED'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
        </tr>';

                $num++;
                $total += $val['extraCostValue'];
                $gran_total += $val['extraCostValue'];
                $tax_transaction_total += $val['extraCostValue'];
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
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total_aed, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
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
        //if($extra['master']['isGroupBasedTax'] == 1){
            $html .= '</tbody>
                    <tfoot>
                    <tr>
                        <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="2">
                            '. 'Landed Cost'.' ('.  $extra['master']['transactionCurrency']  .')</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total_aed, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>
                    <tr>
                    <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="2">
                        '. 'Client Quote - Landed Cost + Mark-Up'.' ('.  $extra['master']['transactionCurrency']  .')</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total + $po_material_markup, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
                            class="text-right total">'. number_format($total_aed + $po_material_markup_aed['convertedAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>
                    </tfoot>
                </table>
            <br>';
        // } else {
        //     $html .= '</tbody>
        //                 <tfoot>
        //                 <tr>
        //                     <td style="min-width: 85%  !important; text-align:right; font-size: 12px;" class="text-right sub_total" colspan="8">
        //                         '. $this->lang->line('common_total').' ('.  $extra['master']['transactionCurrency']  .')</td>
        //                     <td style="text-align:right;min-width: 15% !important;font-size: 12px; "
        //                         class="text-right total">'. number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
        //                 </tr>
        //                 </tfoot>
        //             </table>
        //         <br>';
        // }
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

<br>';

    $data['documentCode'] = 'CNT';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['contractAutoID'];
    $html .= $this->load->view('system/tax/tax_detail_view.php',$data,true);

    $net_amount = $salesPriceTotal['total'] + (($commissionPercentage * $po_material_cost) / 100 ) - $total;

    $margin_cost = (($salesPriceTotal['total'] - (($total - $po_material_commission)))/$total)*100;
    $margin_cost_sales =  (($salesPriceTotal['total'] - (($total - $po_material_commission)))/$salesPriceTotal['total'])*100;

    $aed_net_amount = $net_amount * $po_material_markup_aed['conversion'];

    $html .= '<table style="width: 100%">
        <tbody>
        <tr>
            <td style="width: 100%">
                <table style="width: 100%;" class="table-border">
                    <tr>
                        <td style="font-size:10px"><strong>Proposed Total PO Value</strong></td>
                        <td style="text-align:right;font-size:10px"><strong>'.$header['transactionCurrency'].' '.format_number($salesPriceTotal['total'],$extra['master']['transactionCurrencyDecimalPlaces']).'</strong></td>
                        <td style="text-align:right;font-size:10px"><strong>'.$compayLocalCurrency.' '.format_number($salesPriceTotalAED['convertedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</strong></td>
                    </tr>
                    <tr>
                        <td style="font-size:10px"><strong>Net Margin</strong></td>
                        <td style="text-align:right;font-size:10px"><strong>'.$header['transactionCurrency'].' '.format_number($net_amount,$extra['master']['transactionCurrencyDecimalPlaces']).'</strong></td>
                        <td style="text-align:right;font-size:10px"><strong>'.$compayLocalCurrency.' '.format_number($aed_net_amount,$extra['master']['transactionCurrencyDecimalPlaces']).'</strong></td>
                    </tr>
                    <tr>
                        <td style="font-size:10px"><strong>% of Margin on Cost</strong></td>
                        <td style="text-align:right;font-size:10px" colspan="2"><strong>'.format_number($margin_cost,$extra['master']['transactionCurrencyDecimalPlaces']).' %</strong></td>
                    </tr>
                    <tr>
                        <td style="font-size:10px"><strong>% of Margin on Sale</strong></td>
                        <td style="text-align:right;font-size:10px" colspan="2"><strong>'.format_number($margin_cost_sales,$extra['master']['transactionCurrencyDecimalPlaces']).' %</strong></td>
                    </tr>
                             
                </table>
            </td>            
        </tr>
       
        <tbody>
    </table>';

 if ($extra['master']['Note']) {
    //  $html .= '<div class="table-responsive"><br>
    //     <h6>'.$this->lang->line('common_notes').'</h6>
    //         <table style="width: 100%">
    //             <tbody>
    //             <tr>
    //                 <td>'. $extra['master']['Note'].'</td>
    //             </tr>
    //             </tbody>
    //         </table>';
    }


    
    $html .= '<br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:33%;">
                    <table style="width: 100%">
                        <tr>
                            <td>____________________________</td>
                        </tr>
                        <tr>
                            <td>Initiated By</td>
                        </tr>
                        <tr>
                            <td>Name: '. $extra['master']['confirmedYNn'].'</td>
                        </tr>
                    </table>
                </td>
                <td style="width:33%;">
                    <table style="width: 100%">
                        <tr>
                            <td>____________________________</td>
                        </tr>
                        <tr>
                            <td>Verified By</td>
                        </tr>
                        <tr>
                            <td>Name: </td>
                        </tr>
                    </table>
                </td>
                <td style="width:33%;">
                    <table style="width: 100%">
                        <tr>
                            <td>____________________________</td>
                        </tr>
                        <tr>
                            <td>Approved By</td>
                        </tr>
                        <tr>
                            <td>Name: </td>
                        </tr>
                    </table>
                </td>
                
            </tr>
        </table>
    </div>';


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
    $html="";

} else {
    $html = warning_message("No Records Found!");
}

$mpdf->Output();


