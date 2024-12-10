<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$hideInvoiceDueDatepolicy = getPolicyValues('IDD', 'All'); // policy to hide invoice due date
$taxDetailView = getPolicyValues('TDP', 'All');
$policyPIE = getPolicyValues('PIE', 'All');



if($printHeaderFooterYN==1) {
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
            'margin_bottom'     => 40,
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
    $mpdf->SetHeader();
    $mpdf->SetFooter();
}  else {
    $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
}
$mpdf->WriteHTML($stylesheet, 1);

if(!$policyPIE || $policyPIE == 0){
    $water_mark_status = policy_water_mark_status('All');
    if ($Approved != 1 && $water_mark_status == 1) {
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

$html = "";
echo $extra;
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
    if(empty($extra['customer']['customerSystemCode'])){
        $custnam= $extra['customer']['customerName'];
    }else{
        $custnam= $extra['customer']['customerName'] .' ('. $extra['customer']['customerSystemCode'] .')';
    }

    if($policyPIE && $policyPIE == 1 && $Approved != 1) {
        $invoiceheaderName = 'Preliminary Invoice';
    } else {
        $invoiceheaderName = $this->lang->line('sales_markating_view_invoice_sales_invoice');
    }

    $html .= '<hr>
<div class="table-responsive">
    <div style="text-align: center"><h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $invoiceheaderName .'</h4><!--Sales Invoice --></div>';


    $html .= '<table style="width: 100%">
        <tbody>
        <tr>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> '. $this->lang->line('common_customer_name').'</strong></td><!--Customer Name-->
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $custnam .'</td>

            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_invoice_number').'</strong></td><!--Invoice Number-->
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoiceCode'].'</td>
        </tr>';
    $cussyscodee='';
    if (!empty($extra['customer']['customerSystemCode'])) {
        $html .= '<tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> '. $this->lang->line('sales_markating_view_invoice_customer_address').'</strong></td><!--Customer Address -->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['customer']['customerAddress1'].'</td>

                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_document_date').'</strong></td><!--Document Date-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoiceDate'].'</td>
            </tr>';
    }
    $html .= '<tr>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> Customer Telephone</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['customer']['customerTelephone'].'</td>

            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_reference_number').'</strong></td><!--Reference Number-->
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['referenceNo'].'</td>
        </tr>

        <tr>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> Contact Person</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['master']['contactPersonName'].'</td>

            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_currency').' </strong></td><!--Currency-->
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'.'</td>
        </tr>

        <tr>

            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>Contact Person Tel</strong></td><!--Reference Number-->
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['contactPersonNumber'].'</td>

            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> '. $this->lang->line('sales_markating_view_invoice_invoice_date').'</strong></td><!--Invoice Date-->
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['master']['customerInvoiceDate'].'</td>
        </tr>';

    $html .= '<tr>';
    if (!empty($extra['master']['salesPersonID'])) {
        $html .= '<td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> '. $this->lang->line('sales_markating_view_invoice_sales_person').'</strong></td><!--Sales Person -->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['master']['SalesPersonName'].' ('. $extra['master']['SalesPersonCode'].')</td>';
    }else {
        $html .= '<td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> '. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['master']['invoiceNarration'].'</td>';
    }

    if ($hideInvoiceDueDatepolicy == 0) {
        $html .= '<td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_invoice_due_date') .'</strong></td><!--Invoice Due Date-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['master']['invoiceDueDate'].'</td>';
    }else{
        $html .= '<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>';
    }
    $html .= '</tr>';
    $html .= '<tr>';
    if (!empty($extra['master']['salesPersonID'])) {

        $html .= '<td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong> '. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoiceNarration'].'</td>';
    }else {
        $html .= '<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>';
    }

    $html .= '</tr>

        </tbody>
    </table>
</div><br>';
    $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;

    if(!empty($extra['item_detail'])){
        $html .= '<div class="table-responsive">
        <table border="1" cellspacing="0" cellpadding="0"  class="table table-bordered table-striped" style="width: 100%; border-style: solid">
            <thead>
            <tr>
                <th class="" colspan="5">'. $this->lang->line('sales_markating_view_invoice_item_details').'</th><!--Item Details-->
                <th class="" colspan="6">'. $this->lang->line('common_price').' ('. $extra['master']['transactionCurrency'].') </th><!--Price-->
            </tr>
            <tr>
                <th class="" style="min-width: 5%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                <th class="" style="min-width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_item_code').'</th><!--Item Code-->
                <th class="" style="min-width: 35%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_item_description').'</th><!--Item Description-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_uom').'</th><!--UOM-->
                <th class="" style="min-width: 5%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_qty').'</th><!--Qty-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_unit').'</th><!--Unit-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_discount').'</th><!--Discount-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_sales_net_unit_price').'</th><!--Net Unit Cost-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_total').'</th><!--Total-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_tax').'</th><!--Tax-->
                <th class="" style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_net').'</th><!--Net-->
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
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $num.'.&nbsp;</td>
                    <td style="text-align:center;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['itemSystemCode'].'</td>
                    <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$contractcd.' '.$val['itemDescription'].' -  '. $val['remarks'].'</td>
                    <td style="text-align:center;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['unitOfMeasure'].'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['requestedQty'].'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';

            $num ++;
            $gran_total += $val['transactionAmount'];
            $item_total += $val['transactionAmount'];
            $p_total    += $val['transactionAmount'];
            $tax_total    += $val['taxAmount'];

            $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);

        }
        $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="9" style="text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_item_total').'<!--Item Total -->('. $extra['master']['transactionCurrency'].') </td>
                <td class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($tax_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                <td class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            </tr>
            </tfoot>
        </table>
    </div>';
    }
    $transaction_total = 0;$Local_total = 0;$party_total = 0; $disc_nettot=0;


    if(!empty($extra['gl_detail'])){
        $html .= '<br>
    <div class="table-responsive">  
        <table border="1" cellspacing="0" cellpadding="0" class="table table-bordered table-striped" style="border-style: solid">
            <thead>
            <tr>
                <th class="" style="width: 5%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                <th class="" style="width: 38%;text-align: left;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_description').'</th><!--Description-->
                <th class=" " style="width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_segment').'</th><!--Segment-->
                <th class=" " style="width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_amount').'('. $extra['master']['transactionCurrency'].') </th><!--Amount-->
                <th class=" " style="width: 12%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Discount</th>
                <th class=" " style="width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Net Amount ('. $extra['master']['transactionCurrency'].')</th>
            </tr>
            </thead>
            <tbody>';

        $num =1;
        //font sizes changed GL Table
        foreach ($extra['gl_detail'] as $val) {
            $html .= '<tr>
                    <td style="text-align:right;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $num.'.&nbsp;</td>
                    <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['revenueGLDescription'].'</td>
                    <td style="font-size: 12px;text-align:center;font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['segmentCode'].'</td>
                    <td style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">('. format_number($val['discountPercentage'], 2) .' %) '. format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
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
                <td class="text-right sub_total" colspan="5" style="text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $this->lang->line('common_total').'</td><!--Total-->
                <td class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            </tr>
            </tfoot>
        </table>
    </div>';
    }

    $transaction_total = 0;$Local_total = 0;$party_total = 0;
    if(!empty($extra['delivery_order'])){
        $html .= '<br>
    <div class="table-responsive">
           <table border="1" cellspacing="0" cellpadding="0" class="table table-bordered table-striped" style="border-style: solid">
            <thead>
            <tr>
                <th colspan="4" class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_marketing_delivery_order_based').'</th>
                <th colspan="4" class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">
                   '. $this->lang->line('common_amount').'
                    <span class="currency"> ('. $extra['master']['transactionCurrency'].' )</span>
                </th>
            </tr>
            <tr>
                <th class=" " style="width: 5%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                <th class=" " style="min-width: 15%;text-align: left;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_code').'</th>
                <th class=" " style="width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_date').'</th>
                <th class=" " style="width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_reference_no').'</th>
                <th class=" " style="width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_order_total').'</th>
                <th class=" " style="width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_due').'</th>
                <th class=" " style="width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_amount').'</th>
                <th class=" " style="width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_balance').'</th>
            </tr>
            </thead>
            <tbody>';
        $num =1;
        $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
        foreach ($extra['delivery_order'] as $val) {
            $html .= '<tr>
                    <td style="text-align:right; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $num.'.&nbsp;</td>
                    <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['DOCode'].'</td>
                    <td style="text-align:center; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['DODate'].'</td>
                    <td style="text-align:center; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['referenceNo'].'</td>
                    <td style="text-align:right; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['do_tr_amount'], $dPlace).'</td>
                    <td style="text-align:right; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['due_amount'], $dPlace).'</td>
                    <td style="text-align:right; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['transactionAmount'], $dPlace).'</td>
                    <td style="text-align:right; font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($val['balance_amount'], $dPlace).'</td>
                </tr>';
            $num ++;
            $gran_total         += $val['transactionAmount'];
            $transaction_total  += $val['transactionAmount'];
            $p_total            += $val['transactionAmount'];

        }

        $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="7" style="text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $this->lang->line('common_total').' </td>
                <td class="text-right sub_total" style="font-size: 12px; text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($transaction_total, $dPlace).'</td>
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
                 <table border="1" cellspacing="0" cellpadding="0"  class="'.  table_class().'" style="width: 100%;border-style: solid;">
                 
                        <thead>
                        <tr>
                            <td class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount</strong></td>
                        </tr>
                        <tr>
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Description</th>
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Percentage</th>
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Transaction ('. $extra['master']['transactionCurrency'].') </th>
                        </tr>
                        </thead>
                        <tbody>';
        $x=1;
        foreach ($extra['discount'] as $value) {
            $disc_total=0;
            $disc_total= ($gran_total*$value['discountPercentage'])/100;
            $html .= '<tr>
                  <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$x.'.</td>
                 <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$value['discountDescription'].'</td>
           <td class="text-right" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.format_number($value['discountPercentage'],2).'%</td>
               <td class="text-right" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.format_number($disc_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>';

            $x++;
            $disc_nettot += $disc_total;
        }
        $gran_total=$gran_total-$disc_nettot;
        $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Total</td>
                            <td class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($disc_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
   ';
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
                               <table border="1" cellspacing="0" cellpadding="0"  class="'.  table_class().'" style="width: 100%;border-style: solid;">
                                    <thead>
                                    <tr>
                                        <td class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
                                    </tr>
                                    <tr>
                                        <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                                        <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Description</th>
                                        <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif" >Transaction ('. $extra['master']['transactionCurrency'].') </th>
                                    </tr>
                                    </thead>
                                    <tbody>';

        $x=1;
        $extra_nettot=0;

        foreach ($extra['extracharge'] as $value) {
            $extra_total=0;
            $extra_total= $value['transactionAmount'];
            $html .= '<tr>
                    <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif;">'.$x.'.</td>
                  <td style="font-size: 12px;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$value['extraChargeDescription'].'</td>
           
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
                                        <td colspan="2" class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Total</td>
                                        <td class="text-right sub_total" style="font-size: 12px;text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. format_number($extra_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
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
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                     <table border="1" cellspacing="0" cellpadding="0"  class="'.  table_class().'" style="width: 100%;border-style: solid;">
                        <thead>
                        <tr>
                            <td class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>'. $this->lang->line('sales_markating_view_invoice_tax_details').'</strong></td><!--Tax Details-->
                        </tr>
                        <tr>
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_type').'</th><!--Type-->
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $this->lang->line('sales_markating_view_invoice_detail').'</th><!--Detail-->
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_tax').'</th><!--Tax-->
                            <th class=" " style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('common_transaction').'<!--Transaction -->('. $extra['master']['transactionCurrency'].') </th>

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
                            <td colspan="4" class="text-right sub_total" style="font-size: 12px; text-align:right;font-family: arial">'. $this->lang->line('sales_markating_view_invoice_tax_total').'</td><!--Tax Total-->
                            <td class="text-right sub_total" style="font-size: 12px; text-align:right;font-family: arial">'. format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>';
    }



    $html .= '<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;"> '. $this->lang->line('common_total').' ('. $extra['master']['transactionCurrency'].' )<!--Total-->
: '. format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</h5>
</div>';




    if ($extra['master']['bankGLAutoID']) {
        $a=$this->load->library('NumberToWords');
        if($gran_total > 0)
        {
            $gran_total = $gran_total;
        }else
        {
            $gran_total = 0;
        }
        $numberinword= $this->numbertowords->convert_number($gran_total);
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
        <h6>'.$this->lang->line('sales_markating_view_invoice_remittance_details').'</h6><!--Remittance Details-->
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width: 18%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_bank').'</strong></td><!--Bank-->
                <td style="width: 2%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="width: 80%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoicebank'].'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_branch').'</strong></td><!--Branch-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$extra['master']['invoicebankBranch'].'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_swift_code').'</strong></td><!--Swift Code-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoicebankSwiftCode'].'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_account').'</strong></td><!--Account-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoicebankAccount'].'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>Amount in words</strong></td><!--Account-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $numberinword.$str1.'</td>
            </tr>
            </tbody>
        </table>
    </div>';
    }


    if($taxDetailView == 1) {
        $html .= '<div class="table-responsive"><h6>'. $this->lang->line('common_tax').' '. $this->lang->line('common_details').'</h6><!--Tax Details-->
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
    }


    $html .= '<br>';
    if($extra['master']['approvedYN']){
        $html .= '<div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').' </b></td><!--Electronically Approved By-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$extra['master']['approvedbyEmpName'].'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_date').' </b></td><!--Electronically Approved Date-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['approvedDate'].'</td>
            </tr>
            </tbody>
        </table>
    </div>';
    }

    if ($extra['master']['invoiceNote']) {
        $html .= '<div class="table-responsive"><br>
    <h6 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_notes').'</h6><!--Notes-->
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoiceNote'].'</td>
        </tr>
        </tbody>
    </table>';
    }


    if($printHeaderFooterYN==1)
    {
        $do = '';
        $deliveryorder.=' <div class="table-responsive">
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
                                <h3 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->common_data['company_data']['company_name'].'.</strong></h3>
                                <h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_delivery_note').'</h4><!--Delivery note-->
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_note_number').'</strong></td><!--DN Number-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['deliveryNoteSystemCode'].'</td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_note_date').'</strong></td><!--DN Date-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['invoiceDate'].'</td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_reference_number').'</strong></td><!--Reference Number-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['referenceNo'].'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>';
    }else
    {
        $deliveryorder.='';
        $do.='<div style="text-align: center"><h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Delivery note
</h4></div>';

    }

    if ($extra['master']['isPrintDN']==1 && $html!=1 && $is_item_active==1) {




      $html .= '<pagebreak />
       '.$deliveryorder.'
        <hr>
         '.$do.'
     
    
    
    <div class="table-responsive">
                  
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:23%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_customer_name').' </strong></td><!--Customer Name-->
                <td style="width:2%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="width:75%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $custnam .'</td>
            </tr>';

      if (!empty($extra['master']['customerSystemCode'])) {
            $html .= '<tr>
                    <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_customer_address').'  </strong></td><!--Customer Address-->
                    <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                    <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"> '. $extra['master']['customerAddress'].'</td>
                </tr>
                <tr>
                    <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'.$this->lang->line('common_telephone').'/'. $this->lang->line('common_fax').'</strong></td><!--Telephone / Fax -->
                    <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                    <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'.$extra['master']['customerTelephone'].' / '.$extra['master']['customerFax'].'</td>
                </tr>';
        }
        $html .= '<tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('common_currency').' </strong></td><!--Currency-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'.'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif" colspan="4"> '. $extra['master']['invoiceNarration'].'</td>
            </tr>
            <tr>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_date').'</strong></td><!--Delivery Date-->
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif" colspan="4"> '. $extra['master']['invoiceDueDate'].'</td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>';

        $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0; if(!empty($extra['item_detail'])){
            $html .= '<div class="table-responsive"> 
           <table border="1" cellspacing="0" cellpadding="0"  class="table table-bordered table-striped" style="width: 100%; border-style: solid">
                <thead>
                <tr>
                    <th class=" " colspan="5">'. $this->lang->line('sales_markating_view_invoice_item_details').'</th><!--Item Details-->
                </tr>
                <tr>
                    <th class=" " style="min-width: 5%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">#</th>
                    <th class=" " style="min-width: 15%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_item_code').'</th><!--Item Code-->
                    <th class=" " style="min-width: 65%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_item_description').'</th><!--Item Description-->
                    <th class=" " style="min-width: 10%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_uom').'</th><!--UOM-->
                    <th class=" " style="min-width: 5%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $this->lang->line('sales_markating_view_invoice_qty').'</th><!--Qty-->
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
                    <td style="text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $num .'.&nbsp;</td>
                    <td style="text-align:center;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['itemSystemCode'].'</td>
                    <td>('.$contractcd.')'.$val['itemDescription'].' - '.$val['remarks'].'</td>
                    <td style="text-align:center;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['unitOfMeasure'].'</td>
                    <td style="text-align:right;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $val['requestedQty'].'</td>
                </tr>';

                    $num ++;
                }
            }else{
                echo '<tr class="danger"><td colspan="5" class="text-center">'.$norecordfound.'</td></tr>';
            }
            $html .= '</tbody>
            </table>
        </div>';

            if($extra['master']['approvedYN']){
                $html .= '<div class="table-responsive"><br>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').'</b></td><!--Electronically Approved By -->
                        <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                        <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['approvedbyEmpName'].'</td>
                    </tr>
                    <tr>
                        <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_date').' </b></td><!--Electronically Approved Date-->
                        <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                        <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">'. $extra['master']['approvedDate'].'</td>
                    </tr>
                    </tbody>
                </table>
            </div>';
            }
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

            $html .= '<div class="table-responsive">
            <table style="'. $width .'">
                <tbody>
                <tr>';

            for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {
                $html .= ' <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">
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
    //$mpdf->AddPage();
    $html="";
} else {
    $html = warning_message("No Records Found!");
}
$mpdf->Output();
?>






