<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$companyID = $this->common_data['company_data']['company_id'];
$print_style_heading = '';
$print_style_notes = '';
$print_style_heading_sub = '';
$print_style_table = '';
$print_style = '';
$print_style_heading .= 'font-family: inherit;font-weight: 10; line-height: 1.1;font-size: 25px; color: #333';
$print_style_heading_sub .= 'font-family: inherit;font-weight: 10; line-height: 1.1;font-size: 18px; color: #333';
$print_style .= 'font-family: inherit; line-height: 1.1;font-size: 10px; color: #333';
$print_style_table .= 'font-family: inherit; line-height: 1.1;font-size: 11px; color: #333';
$print_style_notes .= 'font-family: inherit; line-height: 1.1;font-size: 12px; color: #333';
$taxDetailView = getPolicyValues('TDP', 'All');
if ($emailView != 1) {

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

$user = ucwords($this->session->userdata('username'));
$date = date('l jS \of F Y h:i:s A');
$stylesheet = file_get_contents('plugins/bootstrap/css/print_style.css');

$mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);

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

}
$referenceno = '';
if (!empty($extra['master']['referenceNo'])) {
$referenceno .= '<tr>
                        <td style="' . $print_style . '">Reference Number</td>
                        <td style="' . $print_style . '">:</td>
                        <td style="' . $print_style . '">' . $extra['master']['referenceNo'] . ';</td>
                    </tr>';
}

if (!empty($invoice_referenceno_so_qut)) {
$referenceno .= '<tr>
                        <td style="' . $print_style . '">Reference Number</td>
                        <td style="' . $print_style . '">:</td>
                        <td style="' . $print_style . '">' . $invoice_referenceno_so_qut . '</td>
                    </tr>';
}

if (!empty($invoice_referenceno) && (empty($extra['master']['referenceNo']))) {
$referenceno .= '<tr>';
$referenceno .= '<td style="vertical-align: top;' . $print_style . '">Reference Number</td>';
$referenceno .= '<td style="vertical-align: top;' . $print_style . '">' . ':' . '</td>';
$referenceno .= '<td style="' . $print_style . '">';
if (!empty($invoice_referenceno)) {
    $a = 1;
}
foreach ($invoice_referenceno as $val) {
    if ($a > 1) {
        $referenceno .= '</br>';
    }
    if (!empty($val['referenceno'])) {
        $referenceno .= $val['referenceno'];
        $a++;
    }
}
$referenceno .= '</td>';
$referenceno .= '</tr>';
}


$html = "";
if (!empty($extra)) {
if (($printHeaderFooterYN == 1) || ($printHeaderFooterYN == 2)) {
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
                            <td colspan="3">
                                <h3><strong>' . $this->common_data['company_data']['company_name'] . '.</strong></h3>
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
if (empty($extra['customer']['customerSystemCode'])) {
    $custnam = $extra['customer']['customerName'];
} else {
    $custnam = $extra['customer']['customerName'] . ' (' . $extra['customer']['customerSystemCode'] . ')';
}

$invoiceheaderName = $this->lang->line('sales_markating_view_invoice_sales_invoice');
if($group_based_tax == 1 && $extra['master']['vatRegisterYN'] == 1) {
    $invoiceheaderName = 'Tax Invoice';
}


$html .= '<hr><div class="table-responsive"><div style="text-align: center"><h4>' . $invoiceheaderName . '</h4><!--Sales Invoice --></div>';


$html .= '<div class="table-responsive">';
$html .= '<table cellspacing="0" cellpadding="0" style="width: 100%;margin-left: 18.89px; ">
    <tbody>
    <tr>
        <td style="' . $print_style . ';width: 20%"> ' . $this->lang->line('common_customer_name') . '</td><!--Customer Name-->
        <td style="' . $print_style . ';width: 10%">:</td>
        <td style="' . $print_style . '"> ' . $custnam . '</td>
        <td style="' . $print_style . ';width: 20%"> ' . $this->lang->line('common_invoice_number') . '</td><!--Customer Name-->
        <td style="' . $print_style . ';width: 10%">:</td>
        <td style="' . $print_style . '"> ' . $extra['master']['invoiceCode'] . '</td>

    </tr>';
$cussyscodee = '';
if (!empty($extra['customer']['customerSystemCode'])) {
    $html .= '<tr>
            <td style="' . $print_style . '"> ' . $this->lang->line('sales_markating_view_invoice_customer_address') . '</td><!--Customer Address -->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '"> ' . $extra['customer']['customerAddress1'] . '</td>

            <td style="' . $print_style . '"> ' . $this->lang->line('sales_markating_view_invoice_document_date') . '</td><!--Document Date -->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '"> ' . $extra['master']['invoiceDate'] . '</td>';

    $view_ref = 0;
    $html .= '</tr>';
}
$html .= '<tr>
            <td style="' . $print_style . '">' . $this->lang->line('common_customer_telephone') . '</td>
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '"> ' . $extra['customer']['customerTelephone'] . '</td>';

if (empty($extra['customer']['customerSystemCode'])) {
    $html .= '<td style="' . $print_style . '"> ' . $this->lang->line('sales_markating_view_invoice_document_date') . '</td><!--Document Date -->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '"> ' . $extra['master']['invoiceDate'] . '</td>';
}
$html .= '</tr>';

$contact = '';
if (!empty($extra['master']['contactPersonName']) && !empty($extra['master']['contactPersonNumber'])) {
    $contact = $extra['master']['contactPersonName'] . ' / ' . $extra['master']['contactPersonNumber'];
} elseif (!empty($extra['master']['contactPersonName'])) {
    $contact = $extra['master']['contactPersonName'];
} else {
    $contact = $extra['master']['contactPersonNumber'];
}

$html .= '<tr>
        <td style="' . $print_style . '"> Contact Person / Tel</td>
        <td style="' . $print_style . '">:</td>
        <td style="' . $print_style . '"> ' . $contact . '</td>';

if (!empty($extra['master']['salesPersonID'])) {
    $html .= '<td style="' . $print_style . '"> ' . $this->lang->line('sales_markating_view_invoice_sales_person') . '</td><!--Sales Person -->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '"> ' . $extra['master']['SalesPersonName'] . ' (' . $extra['master']['SalesPersonCode'] . ')</td>';
} else {
    $html .= '<td style="' . $print_style . '">' . $this->lang->line('sales_markating_narration') . '</td><!--Narration-->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '"> ' . $extra['master']['invoiceNarration'] . '</td>';
}

$html .= '</tr>';

if ($group_based_tax == 1) {
    $html .= '<tr>';
    $html .= '<td style="' . $print_style . '">Customer VATIN</td>
        <td style="' . $print_style . '">:</td>
        <td style="' . $print_style . '"> ' . $extra['master']['vatIdNo'] . '</td>';

    $html .= '<td style="' . $print_style . '">VATIN</td>
        <td style="' . $print_style . '">:</td>
        <td style="' . $print_style . '"> ' . $extra['master']['companyVatNumber'] . '</td>';
    $html .= '</tr>';
}
$html .= '<tr>';
if($group_based_tax == 1){
    $html .= '<tr style="' . $print_style . '"><td>Date Of Supply</td>
        <td style="' . $print_style . '">:</td>
        <td style="' . $print_style . '"> '. $date_of_supply.'</td>
        </tr>';
}

if (!empty($extra['master']['salesPersonID'])) {
    $html .= '<td style="' . $print_style . '">' . $this->lang->line('sales_markating_narration') . ' </td><!--Narration-->
            <td style="' . $print_style . '">:</strong></td>
            <td style="' . $print_style . '">' . $extra['master']['invoiceNarration'] . '</td>';
}

$html .= '</tr>';
$html .= '</tbody>
</table>
</div><br>';
$is_item_active = 0;
$gran_total = 0;
$gran_total_2 = 0;
$gran_total3 = 0;
$tax_transaction_total = 0;
$tax_Local_total = 0;
$tax_customer_total = 0;
$p_total = 0;

    $colspan = 6;
    $footercolspan = 11;
    $istaxEnable = 0;
    $taxEnabled = getPolicyValues('TAX', 'All');
    if ((($taxEnabled == 1) || ($taxEnabled == null) || ($extra['item_detail_tax'] > 0))&&  ($group_based_tax!=1)) {
        $colspan = 6;
        $istaxEnable = 1;
        $footercolspan = 8;
    } else if($group_based_tax==1){
        $colspan = 7;
        $istaxEnable = 0;
        $footercolspan = 5;
    }
    else {
        $colspan = 4;
        $istaxEnable = 0;
        $footercolspan = 7;
    }

if (!empty($extra['item_detail'])) {
    
    if(($istaxEnable == 1) && ($group_based_tax!=1))
    {
        $col_name.='
        <th class="theadtr" style="min-width: 10%">Net Unit Price</th>
        <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_total') . '</th>
        <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_tax') . '</th>';
    }else  if($group_based_tax==1) {
        $taxAmount = array_sum(array_column($extra['item_detail'], 'taxAmount'));
        $vatAmount = array_sum(array_column($extra['item_detail'], 'amount'));
        $class_hide = '';
        if(($taxAmount - $vatAmount) <= 0) {
            $colspan = 6;
            $class_hide = 'display: none;';
        }
        $col_name.='
        <th class="theadtr" style="min-width: 10%">Taxable<br>Amount</th>
        <th class="theadtr" style="min-width: 10%">VAT %</th>
        <th class="theadtr" style="display: none; min-width: 8%">VAT<br>Amount</th>
        <div style="'.$class_hide.'"><th class="theadtr" style="min-width: 10%; ">Other<br>Tax</th></div>';
    }
    else {
        $col_name.='  <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_sales_net_unit_price') . '</th>';
    }
    $html .= '<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class="theadtr" colspan="4">' . $this->lang->line('sales_markating_view_invoice_item_details') . '</th><!--Item Details-->
            <th class="theadtr" colspan="'.$colspan.'">' . $this->lang->line('common_price') . ' (' . $extra['master']['transactionCurrency'] . ') </th><!--Price-->
        </tr>
        <tr>
            <th class="theadtr" style="min-width: 5%">#</th>
            <th class="theadtr" style="min-width: 25%">' . $this->lang->line('sales_markating_view_invoice_item_description') . '</th><!--Item Description-->
            <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_uom') . '</th><!--UOM-->
            <th class="theadtr" style="min-width: 5%">' . $this->lang->line('common_qty') . '</th><!--Qty-->
            <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_unit') . '</th><!--Unit-->
            <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_discount') . '</th><!--Discount-->
            '.$col_name.'
            <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_net') . '</th><!--Net-->
        </tr>
        </thead>
        <tbody>';

        $num = 1;
        $item_total = 0;
        $is_item_active = 1;
        $amount_tal = 0;
        $amount_other_tal = 0;
        $amount_applicable_tal = 0;
        foreach ($extra['item_detail'] as $val) {
            $contractcd = '';
            if (!empty($val['contractCode'])) {
                $contractcd = '(' . $val['contractCode'] . ')';

            }
            $taxCol = '';
            if(($istaxEnable == 1) && ($group_based_tax!=1))
            {
                $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'] - $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        <td style="text-align:right;font-size: 12px;">' . format_number((($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        ';
            }else  if($group_based_tax==1) {
                $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number(($val['unittransactionAmount'] - $val['discountAmount'])*$val['requestedQty'] , $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                <td style="text-align:right;font-size: 12px;">' . format_number(($val['taxpercentageLedger'])) . '</td>';

            }
            else {
                $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'] - $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                <td style="text-align:right;font-size: 12px;">' . format_number((($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            }

            $html .= '<tr>
                <td style="text-align:right;font-size: 12px;">' . $num . '.&nbsp;</td>
                <td style="font-size: 12px;">' . $val['itemSystemCode'] . '<br>' . $contractcd . ' ' . $val['itemDescription'] . ' -  ' . $val['remarks'] . '</td>
                <td style="text-align:center;font-size: 12px;">' . $val['unitOfMeasure'] . '</td>
                <td style="text-align:right;font-size: 12px;">' . $val['requestedQty'] . '</td>
                <td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                
            <td style="text-align:right;font-size: 12px;">(' . format_number($val['discountPercentage'], 2) . ' %) ' . format_number(($val['discountAmount']*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                
                '.$taxCol.'';

            if($istaxEnable == 1 &&  $group_based_tax!=1) {
                $html .= ' 
                <td style="text-align:right;font-size: 12px;">' . format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            }else if($group_based_tax==1) {
                $html .= '<td style="text-align:right;font-size: 12px;">' . format_number($val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        <div style="'.$class_hide.'"><td style="text-align:right;font-size: 12px;">' . format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td></div>';
                $amount_tal += $val['amount'];
                $amount_applicable_tal += ($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty'];
                $amount_other_tal += $val['taxAmount'] - $val['amount'];

                $amount_tal_grand += $val['amount'];
                $amount_applicable_tal_grand += ($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty'];
                $amount_other_tal_grand += $val['taxAmount'] - $val['amount'];
                $item_total_grand += $val['transactionAmount'];
            }
            $html .= ' <td style="text-align:right;font-size: 12px;">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            $html .='</tr>';

            $num++;
            $gran_total += $val['transactionAmount'];
            $item_total += $val['transactionAmount'];
            $p_total += $val['transactionAmount'];
            $tax_total += $val['totalAfterTax'];

            $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);
        }
        $html .= '
        <tr>
            <td class="text-right sub_total" colspan="'.$footercolspan.'" style="text-align:right;font-weight: bold">' . $this->lang->line('sales_markating_view_invoice_item_total') . '<!--Item Total -->(' . $extra['master']['transactionCurrency'] . ') </td>';
            $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($tax_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
        if ($group_based_tax == 1) {
            $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">&nbsp;</td>';
            $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($amount_tal_grand, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            $html .= '<div style="'.$class_hide.'"><td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold" >' . format_number($amount_other_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td></div>';
        }
        $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
        </tr>';
        $html .= '</tbody>
        <tfoot></tfoot>
    </table>
</div><br>';
        
}

$transaction_total = 0;
$Local_total = 0;
$party_total = 0;
$disc_nettot = 0;
$amount_total = 0;
$amount_applicable_tal = 0;
$amount_total_other = 0;
if (!empty($extra['gl_detail'])) {
    $html .= '
    <div class="table-responsive" >
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th class="theadtr" style="width: 5%;' . $print_style_table . '">#</th>
                <th class="theadtr" style="width: 25%;text-align: left;' . $print_style_table . '">' . $this->lang->line('common_description') . '</th><!--Description-->
                <th class="theadtr" style="width: 15%;' . $print_style_table . '">' . $this->lang->line('common_segment') . '</th><!--Segment-->
                <th class="theadtr" style="width: 15%;' . $print_style_table . '">' . $this->lang->line('common_amount') . '(' . $extra['master']['transactionCurrency'] . ') </th><!--Amount-->
                <th class="theadtr" style="width: 12%;' . $print_style_table . '">Discount</th>';
    if($group_based_tax == 1) {
        $html .= '<th class="theadtr" style="width: 12%;' . $print_style_table . '">Tax<br>Applicable<br>Amount</th>
                    <th class="theadtr" style="width: 12%;' . $print_style_table . '">VAT %</th>
                    <th class="theadtr" style="width: 12%;' . $print_style_table . '">VAT<br> Amount</th>
                    <th class="theadtr" style="width: 10%;' . $print_style_table . '">Other<br>Tax</th>';
    }
                $html .= '<th class="theadtr" style="width: 15%;' . $print_style_table . '">Net Amount (' . $extra['master']['transactionCurrency'] . ')</th>
            </tr>
            </thead>
            <tbody>';

    $num = 1;
    foreach ($extra['gl_detail'] as $val) {
        $html .= '<tr>
                    <td style="text-align:right;font-size: 12px;">' . $num . '.&nbsp;</td>
                    <td style="font-size: 12px;">' .$val['revenueGLDescription']. '</td>
                    <td style="text-align:center;font-size: 12px;">' . $val['segmentCode'] . '</td>
                    <td style="text-align:right;">' . format_number($val['transactionAmount'] + $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    <td style="text-align:right;">(' . format_number($val['discountPercentage'], 2) . ' %) ' . format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
        if($group_based_tax == 1) {
            $html .= '<td style="text-align:right;">' . format_number($val['transactionAmount'] + $val['discountAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        <td style="font-size: 12px;text-align: right"> '.format_number(($val['taxpercentageLedger']),2).'</td>
                        <td style="font-size: 12px;text-align: right"> '.format_number($val['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="font-size: 12px;text-align: right"> '.format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>';
            $amount_total += $val['amount'];
            $amount_applicable_tal += $val['transactionAmount'] + $val['discountAmount'];
            $amount_total_other += $val['taxAmount']-$val['amount'];
        }
                    $html .= '<td style="text-align:right;' . $print_style . '">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                </tr>';

        $num++;
        $gran_total3 += $val['transactionAmount'];
        $gran_total += $val['transactionAmount'];
        $transaction_total += $val['transactionAmount'];
        $p_total += $val['transactionAmount'];
        $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);
    }

    $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5" style="text-align:right;"> ' . $this->lang->line('common_total') . '</td><!--Total-->';
    if($group_based_tax==1) {
        $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
        $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">&nbsp;</td>';
        $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
        $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_total_other, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
    }
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;">' . format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
            </tr>
            </tfoot>
        </table>
    </div><br>';
}

$transaction_total = 0;
$Local_total = 0;
$party_total = 0;
$col_name = '';
$grop_based_col_tax_html_val = '';

if($group_based_tax == 1) {
    $header_colspan = 9;
    $footercolspan_d = 10;
} else {
    $header_colspan = 5;
    $footercolspan_d =7;
}

if($isDOItemWisePolicy  == 1) { 
    if (!empty($extra['delivery_order_DS_item'])) {
        if ($group_based_tax == 1) {
            $col_name .= '<th class="theadtr" style="min-width: 10%;'.$print_style_table.'">Tax<br>Applicable<br>Amount</th>
                            <th class="theadtr" style="min-width: 10%;'.$print_style_table.'">VAT %</th>
                            <th class="theadtr" style="min-width: 10%;'.$print_style_table.'">VAT<br>Amount</th>
                            <th class="theadtr" style="min-width: 10%;'.$print_style_table.'">Other<br>Tax</th>';
        }
        $html .= '
        <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th colspan="3" class="theadtr" style="'.$print_style_table.'"><strong>' . $this->lang->line('sales_marketing_delivery_order_based') . '</strong></th>
                <th colspan="'.$header_colspan.'" class="theadtr" style="'.$print_style_table.'">Item Details</th>
            </tr>
            <tr>
                <th class="theadtr"  style="'.$print_style_table.'">#</th>
                <th class="theadtr"  style="'.$print_style_table.'">' . $this->lang->line('common_code') . '</th>
                <th class="theadtr"  style="'.$print_style_table.'">' . $this->lang->line('common_date') . '</th>
                <th class="theadtr"  style="'.$print_style_table.'">Item</th>
                <th class="theadtr"  style="'.$print_style_table.'">Qty</td>
                <th class="theadtr"  style="'.$print_style_table.'">UOM</td>
                <th class="theadtr"  style="'.$print_style_table.'">Unit Price</td>
                '.$col_name.'
                <th class="theadtr"  style="'.$print_style_table.'">Total</td>
            </tr>
            </thead>
            <tbody>';
        $num = 1;
        $group_by = array();
        $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
        foreach ($extra['delivery_order_DS_item'] as $val) {
            $group_by[$val["unitOfMeasureID"]][] = $val;
        }
        if(!empty($group_by))
        {
            $arrsizre = 0;
            foreach ($group_by as $key => $dostatus) {
                $totalqty = 0;
                $total_rowwise = 0;
                $totalcountrow = 0;
                $amount_total = 0;
                $amount_total_other = 0;
                $amount_applicable_tal = 0;

                foreach ($dostatus as $key2 => $val2)
                {
                    if($group_based_tax==1){
                        $grop_based_col_tax_html_val ='<td style="text-align:right;font-size: 12px;">' . format_number($val2['transactionAmount'] - $val2['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number(($val2['taxpercentageLedger']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number($val2['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number(($val2['taxAmount']-$val2['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>';
                        $amount_total += $val2['amount'];
                        $amount_applicable_tal += $val2['transactionAmount'] - $val2['taxAmount'];
                        $amount_total_other += $val2['taxAmount']-$val2['amount'];
                    }
                    $arrsizre=count($val2);
                    $arrsizre=$arrsizre-1;
                    $html .= '<tr><td style="text-align:right; font-size: 12px;'.$print_style.'">' . $num . '.&nbsp;</td>
                                    <td style="font-size: 12px;'.$print_style.'">' . $val2['DOCode'] . '</td>
                                    <td style="text-align:center; font-size: 12px;'.$print_style.'" >' . $val2['DODate'] . '</td>
                                    <td style="'.$print_style.'">' . $val2['itemDesc'] . '</td>
                                    <td style="text-align: right;'.$print_style.'">' . $val2['requestedQtyformatted'] . '</td>
                                    <td style="text-align: right;'.$print_style.'">' . $val2['unitOfMeasure'] . '</td>
                                    <td style="text-align: right;'.$print_style.'">' . format_number($val2['unittransactionAmount'], $dPlace) . '</td>
                                    '.$grop_based_col_tax_html_val.'
                                    <td style="text-align: right;'.$print_style.'">' . format_number($val2['transactionAmount'], $dPlace) . '</td>
                    
                        </tr>';
                    $num++;
                    $totalqty += str_replace(',', '',  $val2['requestedQtyformatted']);
                    $total_rowwise += $val2['transactionAmount'];
                    $gran_total_2 += $val2['transactionAmount'];
                    $gran_total += $val2['transactionAmount'];
                    $transaction_total += $val2['transactionAmount'];
                    $p_total += $val2['transactionAmount'];
                    $tax_transaction_total += ($val2['transactionAmount'] - $val2['totalAfterTax']);


                }
        
                if($group_based_tax==1) {
                    $html .= '<tr><td colspan="4" style="text-align: right;font-weight: bold;"><div style="margin-left: 30px"><b>Total </b> &nbsp;</div></td>';
                    $html .= '<td colspan="1"  style="text-align: right; font-weight: bold;">'. format_number(($totalqty), 3) . '</td>';
                    $html .= '<td colspan="3" class="text-right" style="text-align:right;font-weight: bold;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    $html .= '<td class="text-right" style="text-align:right;font-weight: bold;">&nbsp;</td>';
                    $html .= '<td class="text-right" style="text-align:right;font-weight: bold;">' . format_number($amount_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    $html .= '<td class="text-right" style="text-align:right;font-weight: bold;">' . format_number($amount_total_other, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    <td  style="text-align: right; font-weight: bold;">'. format_number(($total_rowwise), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                } else {
                    $html .= '<tr><td colspan="4" style="text-align: right;font-weight: bold;"><div style="margin-left: 30px"><b>Total </b> &nbsp;</div></td>';
                    $html .= '<td colspan="1"  style="text-align: right; font-weight: bold;">'. format_number(($totalqty), 3) . '</td>
                    <td  colspan="3" style="text-align: right; font-weight: bold;">'. format_number(($total_rowwise), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                }

            }
                if($extra['delivery_order_ds_count'] == $num -1 && $group_based_tax != 1)
                {
                // $html .= '<td colspan="6"  style="text-align: right; font-weight: bold;">'. format_number(($gran_total_2), $dPlace) . '</td></tr>';
                } else{
                    if($extra['delivery_order_ds_count'] == $num -1 && $group_based_tax == 1)
                    {
                    // $html .= '<td style="text-align: right; font-weight: bold;">'. format_number(($gran_total_2), $dPlace) . '</td></tr>';
                    }
                }
                    

        }
    }
        
        $html .= '
            </tr>
            </tfoot>
        </table>
    </div>';
    
} else {
    if (!empty($extra['delivery_order_DS'])) {
        if ($group_based_tax == 1) {
            $col_name .= '<th class="theadtr" style="min-width: 10%;'.$print_style_table.'">Tax<br>Applicable<br>Amount</th>
                            <th class="theadtr" style="min-width: 10%;'.$print_style_table.'">VAT %</th>
                            <th class="theadtr" style="min-width: 10%;'.$print_style_table.'">VAT<br>Amount</th>
                            <th class="theadtr" style="min-width: 10%;'.$print_style_table.'">Other<br>Tax</th>';
        }
        $html .= '
        <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th colspan="3" class="theadtr" style="'.$print_style_table.'"><strong>' . $this->lang->line('sales_marketing_delivery_order_based') . '</strong></th>
                <th colspan="'.$header_colspan.'" class="theadtr" style="'.$print_style_table.'">Item Details</th>
            </tr>
            <tr>
                <th class="theadtr"  style="'.$print_style_table.'">#</th>
                <th class="theadtr"  style="'.$print_style_table.'">' . $this->lang->line('common_code') . '</th>
                <th class="theadtr"  style="'.$print_style_table.'">' . $this->lang->line('common_date') . '</th>
                <th class="theadtr"  style="'.$print_style_table.'">Item</th>
                <th class="theadtr"  style="'.$print_style_table.'">Qty</td>
                <th class="theadtr"  style="'.$print_style_table.'">UOM</td>
                <th class="theadtr"  style="'.$print_style_table.'">Unit Price</td>
                '.$col_name.'
                <th class="theadtr"  style="'.$print_style_table.'">Total</td>
            </tr>
            </thead>
            <tbody>';
        $num = 1;
        $group_by = array();
        $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
        foreach ($extra['delivery_order_DS'] as $val) {
            $group_by[$val["unitOfMeasureID"]][] = $val;
        }
        if(!empty($group_by))
        {
            $arrsizre = 0;
            foreach ($group_by as $key => $dostatus) {
                $totalqty = 0;
                $total_rowwise = 0;
                $totalcountrow = 0;
                $amount_total = 0;
                $amount_total_other = 0;
                $amount_applicable_tal = 0;

                foreach ($dostatus as $key2 => $val2)
                {
                    if($group_based_tax==1){
                        $grop_based_col_tax_html_val ='<td style="text-align:right;font-size: 12px;">' . format_number($val2['transactionAmount'] - $val2['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number(($val2['taxpercentageLedger']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number($val2['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number(($val2['taxAmount']-$val2['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>';
                        $amount_total += $val2['amount'];
                        $amount_applicable_tal += $val2['transactionAmount'] - $val2['taxAmount'];
                        $amount_total_other += $val2['taxAmount']-$val2['amount'];
                    }
                    $arrsizre=count($val2);
                    $arrsizre=$arrsizre-1;
                    $html .= '<tr><td style="text-align:right; font-size: 12px;'.$print_style.'">' . $num . '.&nbsp;</td>
                                    <td style="font-size: 12px;'.$print_style.'">' . $val2['DOCode'] . '</td>
                                    <td style="text-align:center; font-size: 12px;'.$print_style.'" >' . $val2['DODate'] . '</td>
                                    <td style="'.$print_style.'">' . $val2['itemDesc'] . '</td>
                                    <td style="text-align: right;'.$print_style.'">' . $val2['requestedQtyformatted'] . '</td>
                                    <td style="text-align: right;'.$print_style.'">' . $val2['unitOfMeasure'] . '</td>
                                    <td style="text-align: right;'.$print_style.'">' . format_number($val2['unittransactionAmount'], $dPlace) . '</td>
                                    '.$grop_based_col_tax_html_val.'
                                    <td style="text-align: right;'.$print_style.'">' . format_number($val2['transactionAmount'], $dPlace) . '</td>
                    
                        </tr>';
                    $num++;
                    $totalqty += str_replace(',', '',  $val2['requestedQtyformatted']);
                    $total_rowwise += $val2['transactionAmount'];
                    $gran_total_2 += $val2['transactionAmount'];
                    $gran_total += $val2['transactionAmount'];
                    $transaction_total += $val2['transactionAmount'];
                    $p_total += $val2['transactionAmount'];
                    $tax_transaction_total += ($val2['transactionAmount'] - $val2['totalAfterTax']);


                }
        
                if($group_based_tax==1) {
                    $html .= '<tr><td colspan="4" style="text-align: right;font-weight: bold;"><div style="margin-left: 30px"><b>Total </b> &nbsp;</div></td>';
                    $html .= '<td colspan="1"  style="text-align: right; font-weight: bold;">'. format_number(($totalqty), 3) . '</td>';
                    $html .= '<td colspan="3" class="text-right" style="text-align:right;font-weight: bold;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    $html .= '<td class="text-right" style="text-align:right;font-weight: bold;">&nbsp;</td>';
                    $html .= '<td class="text-right" style="text-align:right;font-weight: bold;">' . format_number($amount_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    $html .= '<td class="text-right" style="text-align:right;font-weight: bold;">' . format_number($amount_total_other, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    <td  style="text-align: right; font-weight: bold;">'. format_number(($total_rowwise), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                } else {
                    $html .= '<tr><td colspan="4" style="text-align: right;font-weight: bold;"><div style="margin-left: 30px"><b>Total </b> &nbsp;</div></td>';
                    $html .= '<td colspan="1"  style="text-align: right; font-weight: bold;">'. format_number(($totalqty), 3) . '</td>
                    <td  colspan="3" style="text-align: right; font-weight: bold;">'. format_number(($total_rowwise), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                }

            }
                if($extra['delivery_order_ds_count'] == $num -1 && $group_based_tax != 1)
                {
                // $html .= '<td colspan="6"  style="text-align: right; font-weight: bold;">'. format_number(($gran_total_2), $dPlace) . '</td></tr>';
                } else{
                    if($extra['delivery_order_ds_count'] == $num -1 && $group_based_tax == 1)
                    {
                    // $html .= '<td style="text-align: right; font-weight: bold;">'. format_number(($gran_total_2), $dPlace) . '</td></tr>';
                    }
                }
                    

        }
    }
        
        $html .= '
            </tr>
            </tfoot>
        </table>
    </div>';
}

if (!empty($extra['discount'])) {
    $html .= '<br>
    <div class="table-responsive">
        <table style="width: 97%;margin-left: 18.89px;">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%; " class="' . table_class() . '">
                        <thead>
                        <tr>
                            <td class="theadtr" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount</strong></td>
                        </tr>
                        <tr>
                            <th class="theadtr">#</th>
                            <th class="theadtr">Description</th>
                            <th class="theadtr">Percentage</th>
                            <th class="theadtr">Transaction (' . $extra['master']['transactionCurrency'] . ') </th>
                        </tr>
                        </thead>
                        <tbody>';
    $x = 1;
    foreach ($extra['discount'] as $value) {
        $disc_total = 0;
        $disc_total = ($gran_total * $value['discountPercentage']) / 100;
        $html .= '<tr>
                    <td style="font-size: 12px;">' . $x . '.</td>
                    <td style="font-size: 12px;">' . $value['discountDescription'] . '</td>
                    <td class="text-right" style="font-size: 12px;text-align:right;">' . format_number($value['discountPercentage'], 2) . '%</td>
                    <td class="text-right" style="font-size: 12px;text-align:right;">' . format_number($disc_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    </tr>';

        $x++;
        $disc_nettot += $disc_total;
    }
    $gran_total = $gran_total - $disc_nettot;
    $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right sub_total" style="font-size: 12px;text-align:right;' . $print_style_table . '">Total</td>
                            <td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($disc_nettot, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
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
    $html .= '
    <div class="table-responsive">
        <table style="width: 97%;margin-left: 18.89px;">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width:50%;padding: 0;">
                            <table style="width: 100%; " class="' . table_class() . '">
                                    <thead>
                                    <tr>
                                        <td class="theadtr" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="theadtr">#</th>
                                        <th class="theadtr">Description</th>
                                        <th class="theadtr">Transaction (' . $extra['master']['transactionCurrency'] . ') </th>
                                    </tr>
                                    </thead>
                                    <tbody>';

    $x = 1;
    $extra_nettot = 0;
    foreach ($extra['extracharge'] as $value) {

        $extra_total = 0;
        $extra_total = $value['transactionAmount'];
        $html .= '<tr>
                    <td style="font-size: 12px;">' . $x . '.</td>
                    <td style="font-size: 12px;">' . $value['extraChargeDescription'] . '</td>
                    <td class="text-right" style="font-size: 12px;text-align:right;">' . format_number($extra_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    </tr>';

        $x++;
        $extra_nettot += $extra_total;
        if ($value['isTaxApplicable'] == 1) {
            $t_extraCharge += $extra_total;
        }
    }

    $gran_total = $gran_total + $extra_nettot;
    $html .= '</tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right sub_total" style="font-size: 12px;text-align:right;">Total</td>
                                        <td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($extra_nettot, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
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

if (!empty($extra['tax'])) {
    $html .= '<div class="table-responsive">
        <table  style="width: 97%;margin-left: 18.89px;">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%" class="' . table_class() . '">
                        <thead>
                        <tr>
                            <td class="theadtr" style="' . $print_style_table . '" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . $this->lang->line('sales_markating_view_invoice_tax_details') . '</strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th class="theadtr" style="' . $print_style_table . '">#</th>
                            <th class="theadtr" style="' . $print_style_table . '">' . $this->lang->line('common_type') . '</th><!--Type-->
                            <th class="theadtr" style="' . $print_style_table . '"> ' . $this->lang->line('sales_markating_view_invoice_detail') . '</th><!--Detail-->
                            <th class="theadtr" style="' . $print_style_table . '">' . $this->lang->line('sales_markating_view_invoice_tax') . '</th><!--Tax-->
                            <th class="theadtr" style="' . $print_style_table . '">' . $this->lang->line('common_transaction') . '<!--Transaction -->(' . $extra['master']['transactionCurrency'] . ') </th>

                        </tr>
                        </thead>
                        <tbody>';

    $tax_Local_total += ($tax_transaction_total / $extra['master']['companyLocalExchangeRate']);
    $tax_customer_total += ($tax_transaction_total / $extra['master']['customerCurrencyExchangeRate']);
    $x = 1;
    $tr_total_amount = 0;
    $cu_total_amount = 0;
    $loc_total_amount = 0;

    foreach ($extra['tax'] as $value) {
        $html .= '<tr>
                    <td style="font-size: 12px;">' . $x . '.</td>
                    <td style="font-size: 12px;">' . $value['taxShortCode'] . '</td>
                    <td style="font-size: 12px;">' . $value['taxDescription'] . '</td>
                    <td class="text-right" style="font-size: 12px; text-align:right;">' . $value['taxPercentage'] . ' % </td>
                    <td class="text-right" style="font-size: 12px; text-align:right;">' . format_number((($value['taxPercentage'] / 100) * ($tax_transaction_total - $disc_nettot + $t_extraCharge)), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    </tr>';
        $x++;
        $gran_total += (($value['taxPercentage'] / 100) * ($tax_transaction_total - $disc_nettot + $t_extraCharge));
        $tr_total_amount += (($value['taxPercentage'] / 100) * ($tax_transaction_total - $disc_nettot + $t_extraCharge));
    }

    $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total" style="font-size: 11px; text-align:right;">' . $this->lang->line('sales_markating_view_invoice_tax_total') . '</td><!--Tax Total-->
                            <td class="text-right sub_total" style="font-size: 11px; text-align:right;">' . format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>';
}

if ($extra['master']['retensionTransactionAmount'] > 0 || $extra['master']['rebateAmount'] > 0) {
$html .= '<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;margin-right: 1%;margin-bottom:0px"> ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';
}
$rebateRetension = 0;
if ($extra['master']['retensionTransactionAmount'] > 0) {
    $rebateRetension += $extra['master']['rebateAmount'];
    $html .= '<div class="table-responsive">
<h5 class="text-right" style="text-align:right;margin-right: 1%;margin-bottom:0px">Retention Amount (' . $extra['master']['retensionTransactionAmount'] . ' )<!--Total-->
: ' . format_number($extra['master']['rebateAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';
}
if ($extra['master']['rebateAmount'] > 0) {
    $rebateRetension += $extra['master']['rebateAmount'];
    $html .= '<div class="table-responsive">
<h5 class="text-right" style="text-align:right;margin-right: 1%;margin-bottom:0px">Rebate Amount (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number($extra['master']['rebateAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';
}
$html .= '<div class="table-responsive">
<h5 class="text-right" style="text-align:right;margin-right: 1%;margin-bottom:0px">Net Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number(($gran_total - $rebateRetension), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';

if ($extra['master']['bankGLAutoID']) {
    $a = $this->load->library('NumberToWords');
    $numberinword = $this->numbertowords->convert_number(ROUND($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']));
    $point = format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']);
    $str_arr = explode('.', $point);
    $str1 = '';
    if ($str_arr[1] > 0) {
        if ($extra['master']['transactionCurrency'] == "OMR") {
            $str1 = ' and ' . $str_arr[1] . ' / 1000 Only';
        } else {
            $str1 = ' and ' . $str_arr[1] . ' / 100 Only';
        }
    }

    $html .= '<div class="table-responsive" style="padding: 0%">
    <h5 style="' . $print_style . ';margin-left: 18.89px;">' . $this->lang->line('sales_markating_view_invoice_remittance_details') . '</h5><!--Remittance Details-->
    <table cellspacing="0" cellpadding="0" style="width: 100%;margin-left: 18.89px;">
        <tbody>
        <tr>
            <td style="width: 18%;' . $print_style . '">' . $this->lang->line('common_bank') . '</td><!--Bank-->
            <td style="width: 2%;' . $print_style . '">:</td>
            <td style="width: 80%;' . $print_style . '">' . $extra['master']['invoicebank'] . '</td>
        </tr>
        <tr>
            <td style="' . $print_style . '">' . $this->lang->line('common_branch') . '</td><!--Branch-->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '">' . $extra['master']['invoicebankBranch'] . '</td>
        </tr>
        <tr>
            <td style="' . $print_style . '">' . $this->lang->line('sales_markating_view_invoice_swift_code') . '</td><!--Swift Code-->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '">' . $extra['master']['invoicebankSwiftCode'] . '</td>
        </tr>
        <tr>
            <td style="' . $print_style . '">' . $this->lang->line('common_account') . '</td><!--Account-->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '">' . $extra['master']['invoicebankAccount'] . '</td>
        </tr>
        <tr>
            <td style="' . $print_style . '">Amount in words</td><!--Account-->
            <td style="' . $print_style . '">:</td>
            <td style="' . $print_style . '">' . $numberinword . $str1 . '</td>
        </tr>
        </tbody>
    </table>
</div>';
}

if ($taxDetailView == 1) {
    $html .= '<div class="table-responsive"><h6>' . $this->lang->line('common_tax') . ' ' . $this->lang->line('common_details') . '</h6><!--Tax Details-->
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 18%"><strong>Tax Identification No</strong></td><!--Tax Identification No-->
                    <td style="width: 2%"><strong>:</strong></td>
                    <td style="width: 80%">' . $extra['master']['textIdentificationNo'] . '</td>
                </tr>
                <tr>
                    <td><strong>Tax Card No</strong></td><!--Tax Card No-->
                    <td><strong>:</strong></td>
                    <td>' . $extra['master']['taxCardNo'] . '</td>
                </tr>
            </tbody>
        </table>
    </div>';
}

$html .= '<br>';
if ($extra['master']['approvedYN']) {
    $html .= '<div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_by') . ' </b> &nbsp;&nbsp; ' . $extra['master']['approvedbyEmpName'] . ' &nbsp;&nbsp; <b> ON </b> &nbsp;&nbsp; ' . $extra['master']['approvedDate'] . '</td><!--Electronically Approved By-->
            </tr>
            </tbody>
        </table>
    </div>';
}

if ($extra['master']['invoiceNote']) {
    $html .= '<div class="table-responsive">
    <h6 style="' . $print_style . ';margin-left: 18.89px;" >' . $this->lang->line('sales_markating_view_invoice_notes') . '</h6><!--Notes-->
    <table style="width: 100%;margin-left: 18.89px;">
        <tbody>
        <tr>
            <td style="' . $print_style_notes . ';">' . $extra['master']['invoiceNote'] . '</td>
        </tr>
        </tbody>
    </table>';
}
    $do = '';
    $deliveryorder = '';
if (($printHeaderFooterYN == 1) || ($printHeaderFooterYN == 2)) {
    $deliveryorder .= ' <div class="table-responsive">
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
                            <td colspan="3">
                                <h3 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this->common_data['company_data']['company_name'] . '.</strong></h3>
                                <h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $this->lang->line('sales_markating_view_invoice_delivery_note') . '</h4><!--Delivery note-->
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this->lang->line('sales_markating_view_invoice_delivery_note_number') . '</strong></td><!--DN Number-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $extra['master']['deliveryNoteSystemCode'] . '</td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this->lang->line('sales_markating_view_invoice_delivery_note_date') . '</strong></td><!--DN Date-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $extra['master']['invoiceDate'] . '</td>
                        </tr>
                        
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this->lang->line('common_reference_number') . '</strong></td><!--Reference Number-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $extra['master']['referenceNo'] . '</td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>';
} elseif (($printHeaderFooterYN == 0) || ($printHeaderFooterYN == 2)) {
    $deliveryorder .= '';
    $do .= '<div style="text-align: center"><h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Delivery note</h4></div>';

}

if ($extra['master']['isPrintDN'] == 1 && $html != 1 && $is_item_active == 1) {

    $html .= '<pagebreak />
    ' . $deliveryorder . '
    <hr>
        ' . $do . '
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:23%;"><strong>' . $this->lang->line('common_customer_name') . ' </strong></td><!--Customer Name-->
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:75%;"> ' . $custnam . '</td>
        </tr>';
    if (!empty($extra['master']['customerSystemCode'])) {
        $html .= '<tr>
                <td><strong>' . $this->lang->line('sales_markating_view_invoice_customer_address') . '  </strong></td><!--Customer Address-->
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['customerAddress'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('common_telephone') . '/' . $this->lang->line('common_fax') . '</strong></td><!--Telephone / Fax -->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['customerTelephone'] . ' / ' . $extra['master']['customerFax'] . '</td>
            </tr>';
    }
    $html .= '<tr>
            <td><strong>' . $this->lang->line('common_currency') . ' </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td>' . $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )' . '</td>
        </tr>
        <tr>
            <td><strong>' . $this->lang->line('sales_markating_narration') . ' </strong></td><!--Narration-->
            <td><strong>:</strong></td>
            <td colspan="4"> ' . $extra['master']['invoiceNarration'] . '</td>
        </tr>
        <tr>
            <td><strong>' . $this->lang->line('sales_markating_view_invoice_delivery_date') . '</strong></td><!--Delivery Date-->
            <td><strong>:</strong></td>
            <td colspan="4"> ' . $extra['master']['invoiceDueDate'] . '</td>
        </tr>
        </tbody>
    </table>
</div><br>';
    $gran_total = 0;
    $tax_transaction_total = 0;
    $tax_Local_total = 0;
    $tax_customer_total = 0;
    $p_total = 0;
    if (!empty($extra['item_detail'])) {
        $html .= '<div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th class="theadtr" colspan="5">' . $this->lang->line('sales_markating_view_invoice_item_details') . '</th><!--Item Details-->
            </tr>
            <tr>
                <th class="theadtr" style="min-width: 5%">#</th>
                <th class="theadtr" style="min-width: 15%">' . $this->lang->line('sales_markating_view_invoice_item_code') . '</th><!--Item Code-->
                <th class="theadtr" style="min-width: 65%">' . $this->lang->line('sales_markating_view_invoice_item_description') . '</th><!--Item Description-->
                <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_uom') . '</th><!--UOM-->
                <th class="theadtr" style="min-width: 5%">' . $this->lang->line('sales_markating_view_invoice_qty') . '</th><!--Qty-->
            </tr>
            </thead>
            <tbody>';

        $norecordfound = $this->lang->line('common_no_records_found');
        $num = 1;
        $item_total = 0;
        if (!empty($extra['item_detail'])) {
            foreach ($extra['item_detail'] as $val) {
                $contractcd = '';
                if (!empty($val['contractCode'])) {
                    $contractcd = $val['contractCode'];

                }
                $html .= '<tr>
                <td style="text-align:right;">' . $num . '.&nbsp;</td>
                <td style="text-align:center;">' . $val['itemSystemCode'] . '</td>
                <td>(' . $contractcd . ')' . $val['itemDescription'] . ' - ' . $val['remarks'] . '</td>
                <td style="text-align:center;">' . $val['unitOfMeasure'] . '</td>
                <td style="text-align:right;">' . $val['requestedQty'] . '</td>
            </tr>';

                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="5" class="text-center">' . $norecordfound . '</td></tr>';
        }
        $html .= '</tbody>
        </table>
    </div>';
    }
}
$html .= '<br>
<br>';


if ($extra['master']['approvedYN']) {
    if ($signature) {
        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 50%";
        } else {
            $width = "width: 100%";
        }

        $html .= '<div class="table-responsive">
        <table style="' . $width . '">
            <tbody>
            <tr>';

        for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {
            $html .= ' <td>
                        <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                    </td>';
        }
        $html .= '</tr>

            </tbody>
        </table>
    </div>';
    }
}

if ($emailView != 1) {
    $mpdf->WriteHTML($html, 2);
    $html = "";
} else {
    echo $html;
}
} else {
$html = warning_message("No Records Found!");
}
if ($emailView != 1) {
$mpdf->Output();
}