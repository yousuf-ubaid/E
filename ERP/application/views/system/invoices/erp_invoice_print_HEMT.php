<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$hideInvoiceDueDatepolicy = getPolicyValues('IDD', 'All'); // policy for invoice due date
$acknowledgementDateYN = 0; //getPolicyValues('SAD', 'All');
$policyPIE = getPolicyValues('PIE', 'All');

$printHeaderFooterYN = 1;
if($printHeaderFooterYN==1) {
    $mpdf = new Mpdf([
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
    ]);
}else{
    $mpdf = new Mpdf([
        'mode'              => 'utf-8',
        'format'            => 'A4',
        'default_font_size' => 9,
        'default_font'      => 'arial',
        'margin_left'       => 5,
        'margin_right'      => 5,
        'margin_top'        => 40,
        'margin_bottom'     => 10,
        'margin_header'     => 20,
        'margin_footer'     => 0,
    ]);
}
$user = ucwords($this->session->userdata('username'));
$date = date('l jS \of F Y h:i:s A');
$stylesheet = file_get_contents('plugins/bootstrap/css/print_style.css');
if ($printHeaderFooterYN == 0) {
    $mpdf->SetHeader();
    $mpdf->SetFooter();
}  else {
    $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
}
$mpdf->WriteHTML($stylesheet, 1);

$html = "";
if (!empty($extra)) {

    if($printHeaderFooterYN==1) {
        $html .= '<div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="' . $logo . $this->common_data['company_data']['company_logo'] . '">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3"style="font-family: serif">
                                <h2><strong>' . $this->common_data['company_data']['company_name'] . '.</strong></h2>
                                <p>' . $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country'] . '</p>

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
    if($policyPIE && $policyPIE == 1 && $extra['master']['approvedYN'] != 1) {
        $invoiceheaderName = 'Preliminary Invoice';
    }else if($group_based_tax == 1 && $extra['master']['vatRegisterYN'] == 1) {
        $invoiceheaderName = 'Tax Invoice';
    } else {
        $invoiceheaderName = $this->lang->line('sales_markating_view_invoice_sales_invoice');
    }


    $html .= '<hr>
<div class="table-responsive">
    <div style="text-align: center"><h4>'. $invoiceheaderName .'</h4><!--Sales Invoice --></div>';


    $html .= '<table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong> '. $this->lang->line('common_customer_name').'</strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> '. $custnam .'</td>

            <td><strong>'. $this->lang->line('common_invoice_number').'</strong></td><!--Invoice Number-->
            <td><strong>:</strong></td>
            <td>'. $extra['master']['invoiceCode'].'</td>
        </tr>';
    $cussyscodee='';
    if (!empty($extra['customer']['customerSystemCode'])) {
        $html .= '<tr>
                <td><strong> '. $this->lang->line('sales_markating_view_invoice_customer_address').'</strong></td><!--Customer Address -->
                <td><strong>:</strong></td>
                <td> '. $extra['customer']['customerAddress1'].'</td>

                <td><strong>'. $this->lang->line('sales_markating_view_invoice_document_date').'</strong></td><!--Document Date-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['invoiceDate'].'</td>
            </tr>';
    }
    $html .= '<tr>
            <td><strong> Customer Telephone</strong></td>
            <td><strong>:</strong></td>
            <td> '. $extra['customer']['customerTelephone'].'</td>

            <td><strong>'. $this->lang->line('common_reference_number').'</strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td>'. $extra['master']['referenceNo'].'</td>
        </tr>

        <tr>
            <td><strong> Contact Person</strong></td>
            <td><strong>:</strong></td>
            <td> '. $extra['master']['contactPersonName'].'</td>

            <td><strong>'. $this->lang->line('common_currency').' </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td>'. $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'.'</td>
        </tr>

        <tr>

            <td><strong>Contact Person Tel</strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td>'. $extra['master']['contactPersonNumber'].'</td>

            <td><strong> '. $this->lang->line('sales_markating_view_invoice_invoice_date').'</strong></td><!--Invoice Date-->
            <td><strong>:</strong></td>
            <td> '. $extra['master']['customerInvoiceDate'].'</td>
        </tr>';

    $html .= '<tr>';
    if (!empty($extra['master']['salesPersonID'])) {
        $html .= '<td><strong> '. $this->lang->line('sales_markating_view_invoice_sales_person').'</strong></td><!--Sales Person -->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['SalesPersonName'].' ('. $extra['master']['SalesPersonCode'].')</td>';
    }else {
        $html .= '<td><strong> '. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['invoiceNarration'].'</td>';
    }
    if ($hideInvoiceDueDatepolicy == 0) {
        $html .= '<td><strong>'. $this->lang->line('sales_markating_view_invoice_invoice_due_date') .'</strong></td><!--Invoice Due Date-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['invoiceDueDate'].'</td>';
    }else{
        $html .= '<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>';
    }
    $html .= '</tr>';
    if (!empty($extra['master']['salesPersonID'])) {

        $html .= '<tr><td><strong> '. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['invoiceNarration'].'</td></tr>';
    }


    if($group_based_tax == 1){
        $html .= '
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>VATIN</strong></td><!--Narration-->
    <td><strong>:</strong></td>
    <td> ' . $extra['master']['companyVatNumber'] . '</td>
</tr>';

        $date_of_supply_view = '';
    if($group_based_tax == 1){

        $date_of_supply_view = '<td><strong>Date Of Supply </strong></td><!--Customer Telephone-->
          <td><strong>:</strong></td>
          <td> '. $date_of_supply.'</td>';
    }


    }


    $html.='<tr>
                <td><strong>Segment </strong></td>
                <td><strong>:</strong></td>
                <td> '. $extra['master']['segDescription'].' ('. $extra['master']['segmentCode'].')</td>
                '.$date_of_supply_view.';

                ';

            if($acknowledgementDateYN == 1 ) {
                $html.='<td><strong>'. $this->lang->line('sales_marketing_acknowledgment_date') .'</strong></td>
                <td><strong>:</strong></td>
                <td> '. $extra['master']['acknowledgementDate'].'</td>
            </tr>';
            } else {
                $html .= '<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        </tr>';
            }



    $html .= '</tbody>
    </table>
</div><br>';
    if ($invoiceType =='Manufacturing')
    {
        $colspan = 5;
        $colspan_footer = 4;
        $grop_based_col_tax_html = '';
        if($group_based_tax==1) {
            $colspan = 9;
            $colspan_footer = 8;
            $grop_based_col_tax_html.='<th style="font-weight: bold;font-size: 12px;">Tax<br>Applicable<br>Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;">VAT %</th>
                                   <th style="font-weight: bold;font-size: 12px;">VAT<br> Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;">Other<br>Tax</th>';
        }

        if (!empty($extra['item_detail'])) {
            $html.='<table width="100%" cellspacing="0" cellpadding="4" border="1">
            <tbody>
            <tr style="font-size: 12px;">
                <td colspan="'.$colspan.'" style="text-align:center; font-weight: bold">Item Detail</td>
            </tr>
            <tr style="font-size: 12px;">
                <td style="font-weight: bold">Item Description</td>
                <td style="font-weight: bold">UoM</td>
                <td style="font-weight: bold">Qty</td>
                <td style="font-weight: bold">Unit Rate</td>
                '.$grop_based_col_tax_html.';
                <td style="font-weight: bold">Amount</td>
            </tr>
            <tbody>';
            $totalAmount = 0;        
            foreach ($extra['item_detail'] as $val) {


                $grop_based_col_tax_html_val = '';
                if($group_based_tax==1) {
                    $totalAmount+=(($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']);
                    $grop_based_col_tax_html_val = ' 
                        <td style="text-align: right">'.number_format($val['unittransactionAmount']*$val['requestedQty'],2).'</td>
                        <td style="text-align: right">'.number_format($val['taxpercentageLedger'],2).'</td>
                        <td style="text-align: right">'.number_format($val['amount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                        <td style="text-align: right">'.format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                         <td style="text-align: right">'.number_format((($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']), $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>';

                }else {
                    $totalAmount+=($val['unittransactionAmount']*$val['requestedQty']);
                    $grop_based_col_tax_html_val = ' <td style="text-align: right">'.number_format(($val['unittransactionAmount']*$val['requestedQty']), $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>';
                }
                $html.='<tr>
                        <td width="25%">' . $val['mfq_item_Description'] . '</td>
                        <td width="5%">'.$val['defaultUOM'].'</td>
                        <td style="text-align: right">'.$val['requestedQty'].'</td>
                        <td style="text-align: right">'.number_format($val['unittransactionAmount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                        
                       '.$grop_based_col_tax_html_val.'
                        
                       
                       </tr>';
            }
        }

        if (!empty($extra['gl_detail'])) {
            $grop_based_col_tax_html_Gl = '';
            if($group_based_tax==1) {
                $colspan = 9;
                $grop_based_col_tax_html_Gl.='<th style="font-weight: bold;font-size: 12px;">Tax<br>Applicable<br>Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;">VAT %</th>
                                   <th style="font-weight: bold;font-size: 12px;">VAT<br> Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;">Other<br>Tax</th>';
            }

            $html.='<tr style="font-size: 12px;">
                <td colspan="5" style="text-align:center; font-weight: bold">GL Detail</td>
                </tr>
                            
            <tr style="font-size: 12px;">
                <td style="font-weight: bold">GL Code</td>
                <td style="font-weight: bold">GL Code Description</td>
                <td style="font-weight: bold">Qty</td>
                <td style="font-weight: bold">Unit Rate</td>
                '.$grop_based_col_tax_html_Gl.';
                <td style="font-weight: bold">Amount</td>
            </tr>'; 

            foreach ($extra['gl_detail'] as $val) {

                $grop_based_col_tax_html_val = '';
                if($group_based_tax==1) {
                    $totalAmount+=(($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']);
                    $grop_based_col_tax_html_val = ' 
                        <td style="text-align: right">'.number_format($val['unittransactionAmount']*$val['requestedQty'],2).'</td>
                        <td style="text-align: right">'.number_format($val['taxpercentageLedger'],2).'</td>
                        <td style="text-align: right">'.number_format($val['amount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                        <td style="text-align: right">'.format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                         <td style="text-align: right">'.number_format((($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']), $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>';

                }else {
                    $totalAmount+=($val['unittransactionAmount']*$val['requestedQty']);
                    $grop_based_col_tax_html_val = ' <td style="text-align: right">'.number_format(($val['unittransactionAmount']*$val['requestedQty']), $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>';
                }

                $html.='<tr>
                        <td width="25%">'.$val['revenueGLAutoID'].'</td>
                        <td width="25%">'.$val['manufacturinggldes'].'</td>
                        <td style="text-align: right">'.$val['requestedQty'].'</td>
                        <td style="text-align: right">'.number_format($val['unittransactionAmount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                       '.$grop_based_col_tax_html_val.';
                       </tr>';
            }
        }

        if (!empty($extra['gl_detail']) || !empty($extra['item_detail'])) {
            $html.='<tr>
                        <td style="text-align: right" colspan="'.$colspan_footer.'"><b>Total</b></td>
                        <td style="text-align: right"><b>'.number_format($totalAmount,$extra['master']["transactionCurrencyDecimalPlaces"]).'</b></td>
                    </tr>';
        }

        $html.='</tbody></table>';

        $gran_total = $totalAmount;


    }else {
$is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;$disc_nettot=0;$t_extraCharge=0;
    if(!empty($extra['item_detail'])){
        $html .= '<div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%">
            <thead>
            <tr>
                <th class="theadtr" colspan="5">'. $this->lang->line('sales_markating_view_invoice_item_details').'</th><!--Item Details-->
                <th class="theadtr" colspan="7">'. $this->lang->line('common_price').' ('. $extra['master']['transactionCurrency'].') </th><!--Price-->
            </tr>
            <tr>
                <th class="theadtr" style="min-width: 5%">#</th>
                <th class="theadtr" style="min-width: 15%">'. $this->lang->line('sales_markating_view_invoice_item_code').'</th><!--Item Code-->
                <th class="theadtr" style="min-width: 35%">'. $this->lang->line('sales_markating_view_invoice_item_description').'</th><!--Item Description-->
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('common_uom').'</th><!--UOM-->
                <th class="theadtr" style="min-width: 5%">'. $this->lang->line('common_qty').'</th><!--Qty-->
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('sales_markating_view_invoice_unit').'</th><!--Unit-->
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('sales_markating_view_invoice_discount').'</th><!--Discount-->
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('sales_markating_sales_net_unit_price').'</th><!--Net Unit Cost-->
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('common_total').'</th><!--Total-->
                <th class="theadtr" style="min-width: 10%">VAT %</th>
                <th class="theadtr" style="min-width: 10%">VAT<br> Amount</th>
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('sales_markating_view_invoice_net').'</th><!--Net-->
            </tr>
            </thead>
            <tbody>';

        $num =1;$item_total = 0;
        $is_item_active = 1;
        foreach ($extra['item_detail'] as $val) {
            $contractcd='';
            if(!empty($val['contractCode'])){
                $contractcd= '('.$val['contractCode'].')';

            }
            $html .= '<tr>
                    <td style="text-align:right;font-size: 12px;">'. $num.'.&nbsp;</td>
                    <td style="text-align:center;font-size: 12px;">'. $val['itemSystemCode'].'</td>
                    <td style="font-size: 12px;">'.$contractcd.' '.$val['itemDescription'].' -  '. $val['remarks'].'</td>
                    <td style="text-align:center;font-size: 12px;">'. $val['unitOfMeasure'].'</td>
                    <td style="text-align:right;font-size: 12px;">'. $val['requestedQty'].'</td>
                    <td style="text-align:right;font-size: 12px;">'. format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;">'. format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;">'. format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;">'. format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align: right">'.number_format($val['taxpercentageLedger'],2).'</td>
                        <td style="text-align: right">'.number_format($val['amount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                    <td style="text-align:right;font-size: 12px;">'. format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';

            $num ++;
            $gran_total += $val['transactionAmount'];
            $item_total += $val['transactionAmount'];
            $p_total    += $val['transactionAmount'];

            $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);

        }
        $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="9" style="text-align:right;">'.format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                <td class="text-right sub_total" colspan="" style="text-align:right;"></td>
                <td class="text-right sub_total" colspan="" style="text-align:right;">'.number_format($val['amount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                <td class="text-right sub_total" style="font-size: 12px;text-align:right;">'. format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            </tr>
            </tfoot>
        </table>
    </div>';
    }
    $transaction_total = 0;$Local_total = 0;$party_total = 0;

 if(!empty($extra['gl_detail'])){
     $html .= '<br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="theadtr" style="width: 5%">#</th>
                <th class="theadtr" style="min-width: 45%;text-align: left;">'. $this->lang->line('common_description').'</th><!--Description-->
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_segment').'</th><!--Segment-->
               <th class="theadtr" style="width: 15%">'. $this->lang->line('common_amount').'('. $extra['master']['transactionCurrency'].') </th><!--Amount-->
                <th class="theadtr" style="width: 12%">Discount</th>
                <th class="theadtr" style="width: 15%">Net Amount ('. $extra['master']['transactionCurrency'].')</th>

            </tr>
            </thead>
            <tbody>';

            $num =1;
            foreach ($extra['gl_detail'] as $val) {
                $html .= '<tr>
                    <td style="text-align:right; font-size: 12px;">'. $num.'.&nbsp;</td>
                    <td style="font-size: 12px;">'. $val['description'].'</td>
                    <td style="text-align:center; font-size: 12px;">'. $val['segmentCode'].'</td>
                      <td style="text-align:right;">'. format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;">('. format_number($val['discountPercentage'], 2) .' %) '. format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;">'. format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>

                </tr>';

                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                $p_total            += $val['transactionAmount'];


                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);

            }

     $html .= '</tbody>
            <tfoot>
             <tr>
                <td class="text-right sub_total" colspan="5" style="text-align:right;"> '. $this->lang->line('common_total').'</td><!--Total-->
                <td class="text-right sub_total" style="font-size: 12px;text-align:right;">'.format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            </tr>
            </tfoot>
        </table>
    </div>';
 }
    if (!empty($extra['discount'])) {
        $html .= '<br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%; " class="'.  table_class().'">
                        <thead>
                        <tr>
                            <td class="theadtr" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount</strong></td>
                        </tr>
                        <tr>
                            <th class="theadtr">#</th>
                            <th class="theadtr">Description</th>
                            <th class="theadtr">Percentage</th>
                            <th class="theadtr">Transaction ('. $extra['master']['transactionCurrency'].') </th>
                        </tr>
                        </thead>
                        <tbody>';
        $x=1;
        foreach ($extra['discount'] as $value) {
            $disc_total=0;
            $disc_total= ($gran_total*$value['discountPercentage'])/100;
            $html .= '<tr>
                     <td style="font-size: 12px;">'.$x.'.</td>
                    <td style="font-size: 12px;">'.$value['discountDescription'].'</td>
                    <td class="text-right" style="font-size: 12px;text-align:right;">'.format_number($value['discountPercentage'],2).'%</td>
                    <td class="text-right" style="font-size: 12px;text-align:right;">'.format_number($disc_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>';

            $x++;
            $disc_nettot += $disc_total;
        }
        $gran_total=$gran_total-$disc_nettot;
        $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right sub_total" style="font-size: 12px;text-align:right;">Total</td>
                            <td class="text-right sub_total" style="font-size: 12px;text-align:right;">'. format_number($disc_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>';
    }
    if (!empty($extra['extracharge'])) {
        $html .= '<br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width:50%;padding: 0;">
                               <table style="width: 100%; " class="'.  table_class().'">
                                    <thead>
                                    <tr>
                                        <td class="theadtr" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="theadtr">#</th>
                                        <th class="theadtr">Description</th>
                                        <th class="theadtr">Transaction ('. $extra['master']['transactionCurrency'].') </th>
                                    </tr>
                                    </thead>
                                    <tbody>';

        $x=1;
        $extra_nettot=0;
        foreach ($extra['extracharge'] as $value) {

            $extra_total=0;
            $extra_total= $value['transactionAmount'];
            $html .= '<tr>
                    <td style="font-size: 12px;">'.$x.'.</td>
                    <td style="font-size: 12px;">'.$value['extraChargeDescription'].'</td>
                    <td class="text-right" style="font-size: 12px;text-align:right;">'.format_number($extra_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>';

            $x++;
            $extra_nettot += $extra_total;
            if($value['isTaxApplicable']==1){
                $t_extraCharge += $extra_total;
            }
        }
        $gran_total=$gran_total+$extra_nettot;

        $html .= '</tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right sub_total" style="font-size: 12px;text-align:right;">Total</td>
                                        <td class="text-right sub_total" style="font-size: 12px;text-align:right;">'. format_number($extra_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>';
    }
    }
    if (!empty($extra['tax'])) {
        $html .= '<div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%" class="'. table_class().'">
                        <thead>
                        <tr>
                            <td class="theadtr" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>'. $this->lang->line('sales_markating_view_invoice_tax_details').'</strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th class="theadtr">#</th>
                            <th class="theadtr">'. $this->lang->line('common_type').'</th><!--Type-->
                            <th class="theadtr"> '. $this->lang->line('sales_markating_view_invoice_detail').'</th><!--Detail-->
                            <th class="theadtr">'. $this->lang->line('sales_markating_view_invoice_tax').'</th><!--Tax-->
                            <th class="theadtr">'. $this->lang->line('common_transaction').'<!--Transaction -->('. $extra['master']['transactionCurrency'].') </th>

                        </tr>
                        </thead>
                        <tbody>';

        $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
        $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
        $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
        foreach ($extra['tax'] as $value) {
            $html .= '<tr>
                    <td style="font-size: 12px;">'.$x.'.</td>
                    <td style="font-size: 12px;">'.$value['taxShortCode'].'</td>
                    <td style="font-size: 12px;">'.$value['taxDescription'].'</td>
                    <td class="text-right" style="font-size: 12px; text-align:right;">'.$value['taxPercentage'].' % </td>
                    <td class="text-right" style="font-size: 12px; text-align:right;">'.format_number((($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge)),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>';
            $x++;
            $gran_total += (($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge));
            $tr_total_amount+=(($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge));
        }

        $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total" style="font-size: 12px; text-align:right;">'. $this->lang->line('sales_markating_view_invoice_tax_total').'</td><!--Tax Total-->
                            <td class="text-right sub_total" style="font-size: 12px; text-align:right;">'. format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>';
    }
    $total = '';
if ($invoiceType !='Manufacturing')
{
    $total .= $this->lang->line('common_total').' ('. $extra['master']['transactionCurrency'].' )<!--Total-->
: '. format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']);
}
    $html .= '<div class="table-responsive">

    <h5 class="text-right" style="text-align:right;">'.$total.' </h5>
</div>';
 if ($extra['master']['bankGLAutoID']) {
     $html .= '<div class="table-responsive">
        <h6>'. $this->lang->line('sales_markating_view_invoice_remittance_details').'</h6><!--Remittance Details-->
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width: 18%"><strong>'. $this->lang->line('common_bank').'</strong></td><!--Bank-->
                <td style="width: 2%"><strong>:</strong></td>
                <td style="width: 80%">'. $extra['master']['invoicebank'].'</td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('common_branch').'</strong></td><!--Branch-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['invoicebankBranch'].'</td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('sales_markating_view_invoice_swift_code').'</strong></td><!--Swift Code-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['invoicebankSwiftCode'].'</td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('common_account_name').'</strong></td><!--Account Name-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['accountName'].'</td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('common_account').'</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['invoicebankAccount'].'</td>
            </tr>
            </tbody>
        </table>
    </div>';
}

    $a=$this->load->library('NumberToWords');
    $numberinword= $this->numbertowords->convert_number(ROUND($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']));
    $point=format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']);
    $str_arr = explode('.',$point);
    $str1='';
    if($str_arr[1]>0){
        if($extra['master']['transactionCurrency']=="OMR"){
            $str1=' and '.$str_arr[1].' / 1000 Only';
        }else{
            $str1=' and '.$str_arr[1].' / 100 Only';
        }
    }
    $html .= '<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 18%"><strong>Amount In word</strong></td><!--Amount In word-->
                <td style="width: 2%"><strong>:</strong></td>
                <td style="width: 80%">'. $numberinword.$str1 .'</td>
            </tr>
        </tbody>
    </table>
    </div>';

     $html .= '<div class="table-responsive">
        <h6>'. $this->lang->line('common_tax').' '. $this->lang->line('common_details').'</h6><!--Tax Details-->
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 18%"><strong>Tax Identification No</strong></td><!--Tax Identification No-->
                    <td style="width: 2%"><strong>:</strong></td>
                    <td style="width: 80%">'. $extra['master']['textIdentificationNo'].'</td>
                </tr>
                <tr>
                    <td><strong>Tax Card No</strong></td><!--Tax Card No-->
                    <td><strong>:</strong></td>
                    <td>'. $extra['master']['taxCardNo'].'</td>
                </tr>
            </tbody>
        </table>
    </div>';

    $html .= '<div class="table-responsive">
    <table style="margin-top: 20px; width: 100%">
        <tbody>';
if($extra['master']['confirmedYN']==1){
    $html .= '<tr>
        <td><b>Confirmed by</b></td>
        <td><strong>:</strong></td>
        <td>'. $extra['master']['confirmedYNn'].'</td>
    </tr>';
 }
 if($extra['master']['approvedYN']){
     $html .= '<tr>
        <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').' </b></td><!--Electronically Approved By-->
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
</div>';
if ($extra['master']['invoiceNote']) {
    $html .= '<div class="table-responsive"><br>
    <h6>'. $this->lang->line('sales_markating_view_invoice_notes').'</h6><!--Notes-->
    <table style="width: 100%">
        <tbody>
        <tr>
            <td>'. $extra['master']['invoiceNote'].'</td>
        </tr>
        </tbody>
    </table>';
 }

if ($extra['master']['isPrintDN']==1 && $html!=1 && $is_item_active==1) {
    $html .= '<pagebreak />
    <div class="table-responsive">
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
                                <h3><strong>'. $this->common_data['company_data']['company_name'].'.</strong></h3>
                                <h4>'. $this->lang->line('sales_markating_view_invoice_delivery_note').'</h4><!--Delivery note-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_note_number').'</strong></td><!--DN Number-->
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['deliveryNoteSystemCode'].'</td>
                        </tr>
                        <tr>
                            <td><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_note_date').'</strong></td><!--DN Date-->
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['invoiceDate'].'</td>
                        </tr>
                        <tr>
                            <td><strong>'. $this->lang->line('common_reference_number').'</strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['referenceNo'].'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:23%;"><strong>'. $this->lang->line('common_customer_name').' </strong></td><!--Customer Name-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:75%;"> '. $custnam .'</td>
            </tr>';
           if (!empty($extra['master']['customerSystemCode'])) {
               $html .= '<tr>
                    <td><strong>'. $this->lang->line('sales_markating_view_invoice_customer_address').'  </strong></td><!--Customer Address-->
                    <td><strong>:</strong></td>
                    <td> '. $extra['master']['customerAddress'].'</td>
                </tr>
                <tr>
                    <td><strong>'. $this->lang->line('common_telephone').'/'. $this->lang->line('common_fax').'</strong></td><!--Telephone / Fax -->
                    <td><strong>:</strong></td>
                    <td>'. $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax'].'</td>
                </tr>';
            }
    $html .= '<tr>
                <td><strong>'. $this->lang->line('common_currency').' </strong></td><!--Currency-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'.'</td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td colspan="4"> '. $extra['master']['invoiceNarration'].'</td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_date').'</strong></td><!--Delivery Date-->
                <td><strong>:</strong></td>
                <td colspan="4"> '. $extra['master']['invoiceDueDate'].'</td>
            </tr>
            </tbody>
        </table>
    </div><br>';
    $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;

  if(!empty($extra['item_detail'])){
      $html .= '<div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="theadtr" colspan="5">'. $this->lang->line('sales_markating_view_invoice_item_details').'</th><!--Item Details-->
                </tr>
                <tr>
                    <th class="theadtr" style="min-width: 5%">#</th>
                    <th class="theadtr" style="min-width: 15%">'. $this->lang->line('sales_markating_view_invoice_item_code').'</th><!--Item Code-->
                    <th class="theadtr" style="min-width: 65%">'. $this->lang->line('sales_markating_view_invoice_item_description').'</th><!--Item Description-->
                    <th class="theadtr" style="min-width: 10%">'. $this->lang->line('sales_markating_view_invoice_uom').'</th><!--UOM-->
                    <th class="theadtr" style="min-width: 5%">'. $this->lang->line('sales_markating_view_invoice_qty').'</th><!--Qty-->
                </tr>
                </thead>
                <tbody>';

                $norecordfound =    $this->lang->line('common_no_records_found');
                $num =1;$item_total = 0;
                if (!empty($extra['item_detail'])) {
                foreach ($extra['item_detail'] as $val) {
                    $contractcd='';
                    if(!empty($val['contractCode'])){
                        $contractcd=$val['contractCode'];

                    }
                    $html .= '<tr>
                    <td style="text-align:right; font-size: 12px;">'.$num .'.&nbsp;</td>
                    <td style="text-align:center; font-size: 12px;">'. $val['itemSystemCode'].'</td>
                    <td style="font-size: 12px;">('.$contractcd.')'.$val['itemDescription'].' - '.$val['remarks'].'</td>
                    <td style="text-align:center; font-size: 12px;">'. $val['unitOfMeasure'].'</td>
                    <td style="text-align:right; font-size: 12px;">'. $val['requestedQty'].'</td>
                </tr>';

                $num ++;
                }
                }else{
                    echo '<tr class="danger"><td colspan="5" class="text-center" style="font-size: 12px;">'.$norecordfound.'</td></tr>';
                }
                $html .= '</tbody>
            </table>
        </div>
        <div class="table-responsive"><br>
            <table style="width: 100%">
                <tbody>';
                 if($extra['master']['confirmedYN']==1){
                     $html .= '<tr>
                        <td><b>Confirmed By</b></td>
                        <td><strong>:</strong></td>
                        <td>'. $extra['master']['confirmedYNn'].'</td>
                    </tr>';
                 }
                if($extra['master']['approvedYN']){
                    $html .= '<tr>
                        <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').'</b></td><!--Electronically Approved By -->
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
        </div>';
 } }
    $html .= '<br>
    <br>
    <br>';

if($policyPIE && $policyPIE == 1 && !empty($extra['approvallevels'])){
    $html .= '<div class="table-responsive"><br>
    <table style="width: 60%">
        <tbody>';
        foreach ($extra['approvallevels'] as $val) {
            $html .= '<tr>
                        <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_by') . ' (' . $this->lang->line('common_level') . '  ' . $val['approvalLevelID'] . ')</b></td><!--Electronically Approved By -->
                        <td><strong>:</strong></td>
                        <td>' . $val['Ename2'] . '</td>
                    </tr>
                    <tr>
                        <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_date') . ' (' . $this->lang->line('common_level') . '  ' . $val['approvalLevelID'] . ')</b></td><!--Electronically Approved By -->
                        <td><strong>:</strong></td>
                        <td>' . $val['ApprovedDate'] . '</td>
                    </tr>';
        }
        
    $html .= '</tbody>
    </table>
</div>';
}
if($extra['master']['approvedYN']){
    if ($signature) {
        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 50%";
        } else {
            $width = "width: 100%";
        }
        $html .= ' <div class="table-responsive">
            <table style="'. $width .'">
                <tbody>
                <tr>';
                    for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                        $html .= '<td>
                            <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
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

}else {
    $html = warning_message("No Records Found!");
}
$mpdf->Output();





