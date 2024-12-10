<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this
    ->lang
    ->load('sales_markating_approval', $primaryLanguage);
$this
    ->lang
    ->load('sales_maraketing_transaction', $primaryLanguage);
$this
    ->lang
    ->load('common', $primaryLanguage);
$taxDetailView = getPolicyValues('TDP', 'All');
$itemBarcodeView = getPolicyValues('SBID', 'All');
$policyPIE = getPolicyValues('PIE', 'All');
$POView = '';
if (!empty($extra['po_numberEST']))
{
    //$POView = implode(',&nbsp;&nbsp;', (array_column($extra['po_numberEST'], 'poNumber')));
    $po_numberEST = array_unique(array_column($extra['po_numberEST'], 'poNumber'));
    $POView = implode(',&nbsp;&nbsp;', ($po_numberEST));
}

if ($emailView != 1)
{
    if ($printHeaderFooterYN == 1)
    {
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
            ]
        );
    }
    else
    {
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
                'margin_footer'     => 0,
            ]
        );
    }

    $user = ucwords($this
        ->session
        ->userdata('username'));
    $date = date('l jS \of F Y h:i:s A');
    $stylesheet = file_get_contents('plugins/bootstrap/css/print_style.css');
    if ($printHeaderFooterYN == 0)
    {
        $mpdf->SetHeader();
        $mpdf->SetFooter();
    }
    else
    {
        $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
    }
    $mpdf->WriteHTML($stylesheet, 1);

    if (!$policyPIE || $policyPIE == 0)
    {
        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 && $water_mark_status == 1)
        {
            $waterMark = '';
            switch ($Approved)
            {
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
}

$html = "";
$container = "";
$container_bl = "";
$deliveryorder = "";
if (!empty($extra))
{
    
    $custnam = '';
    if (empty($extra['customer']['customerSystemCode']))
    {
        $custnam = $extra['customer']['customerName'];
    }
    else
    {
        $custnam = $extra['customer']['customerName'] . ' (' . $extra['customer']['customerSystemCode'] . ')';
    }
    if ($policyPIE && $policyPIE == 1 && $Approved != 1)
    {
        $invoiceheaderName = 'Preliminary Invoice';
    }
    else if ($group_based_tax == 1 && $extra['master']['vatRegisterYN'] == 1)
    {
        $invoiceheaderName = 'Tax Invoice';
    }
    else
    {
        $invoiceheaderName = $this
            ->lang
            ->line('sales_markating_view_invoice_sales_invoice');
    }

    if ($invoiceType == 'Project')
    {
        
        $category = array();
        $totalvariationcontract = 0;
        $grandtotalinvoice = 0;
        foreach ($extra['invoiceproject'] as $val)
        {
            $category[$val["isVariation"]][] = $val;
        }
        if (!empty($category))
        {
            foreach ($category as $key => $mainCategory)
            {
                $totalamount = 0;
                $totalinvoiceamount = 0;
                foreach ($mainCategory as $key2 => $subCategory)
                {

                    if ($subCategory['boqPreviousClaimPercentage'] > 0)
                    {
                        $remainingamount = number_format((($subCategory['totalTransCurrency'] - $subCategory['transactionAmount']) - ($subCategory['totalTransCurrency'] * ($subCategory['boqPreviousClaimPercentage'] / 100))) , $extra['master']['transactionCurrencyDecimalPlaces']);
                    }
                    else
                    {
                        $remainingamount = number_format(($subCategory['totalTransCurrency'] - $subCategory['transactionAmount']) , $extra['master']['transactionCurrencyDecimalPlaces']);

                    }
                  
                    $totalamount += $subCategory['totalTransCurrency'];
                    $totalinvoiceamount += $subCategory['transactionAmount'];
                    $totalvariationcontract += $subCategory['totalTransCurrency'];
                    $grandtotalinvoice += $subCategory['transactionAmount'];

                }                

            }
        }
      

    }
    else if ($invoiceType == 'Manufacturing')
    {
        $totalAmount = 0;
        
        if ($extra['master']['bankGLAutoID'])
        {
            $a = $this
                ->load
                ->library('NumberToWords');
            $numberinword = $this
                ->numbertowords
                ->convert_number($totalAmount);
            $point = format_number($totalAmount, $extra['master']['transactionCurrencyDecimalPlaces']);
            $str_arr = explode('.', $point);
            $str1 = '';
            if ($str_arr[1] > 0)
            {
                if ($extra['master']['transactionCurrency'] == "OMR")
                {
                    $str1 = ' and ' . $str_arr[1] . ' / 1000 Only';
                }
                else
                {
                    $str1 = ' and ' . $str_arr[1] . ' / 100 Only';
                }
            }

        }
       
    }
    else
    {

        $is_item_active = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $tax_Local_total = 0;
        $tax_customer_total = 0;
        $p_total = 0;

        $colspan = 6;
        $footercolspan = 11;
        $istaxEnable = 0;
        $taxEnabled = getPolicyValues('TAX', 'All');
        if ((($taxEnabled == 1) || ($taxEnabled == null) || ($extra['item_detail_tax'] > 0)) && ($group_based_tax != 1))
        {
            $colspan = 6;
            $istaxEnable = 1;
            $footercolspan = 10;
        }
        else if ($group_based_tax == 1)
        {
            $colspan = 7;
            $istaxEnable = 0;
            $footercolspan = 7;
        }
        else
        {
            $colspan = 4;
            $istaxEnable = 0;
            $footercolspan = 8;
        }

        if (!empty($extra['item_detail']))
        {
            $col_name = '';

           
         

            $num = 1;
            $item_total = 0;
            $is_item_active = 1;
            $amount_tal = 0;
            $amount_other_tal = 0;
            $amount_applicable_tal = 0;
    
        }
       

   
      
     //   $html .= '<br>';
     

        if ($printHeaderFooterYN == 1)
        {
            $do = '';
            $deliveryorder .= ' <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="max-height: 100px; max-width:200px;" src="' . $logo . $this->common_data['company_data']['company_logo'] . '">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this->common_data['company_data']['company_name'] . '.</strong></h3>
                                <h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $this
                ->lang
                ->line('sales_markating_view_invoice_delivery_note') . '</h4><!--Delivery note-->
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this
                ->lang
                ->line('sales_markating_view_invoice_delivery_note_number') . '</strong></td><!--DN Number-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $extra['master']['deliveryNoteSystemCode'] . '</td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this
                ->lang
                ->line('sales_markating_view_invoice_delivery_note_date') . '</strong></td><!--DN Date-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $extra['master']['invoiceDate'] . '</td>
                        </tr>
                        <tr>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>' . $this
                ->lang
                ->line('common_reference_number') . '</strong></td><!--Reference Number-->
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif"><strong>:</strong></td>
                            <td style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">' . $extra['master']['referenceNo'] . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>';
        }
        else
        {
            $deliveryorder .= '';
            $do .= '<div style="text-align: center"><h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Delivery note
    </h4></div>';

        }

        if ($extra['master']['isPrintDN'] == 3 && $html != 1 && $is_item_active == 1)
        {

            $html .= '' . $deliveryorder . '
        <hr>
         ' . $do . '
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:23%;"><strong>' . $this
                ->lang
                ->line('common_customer_name') . ' </strong></td><!--Customer Name-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:75%;"> ' . $custnam . '</td>
            </tr>';
            if (!empty($extra['master']['customerSystemCode']))
            {
                $html .= '<tr>
                    <td><strong>' . $this
                    ->lang
                    ->line('sales_markating_view_invoice_customer_address') . '  </strong></td><!--Customer Address-->
                    <td><strong>:</strong></td>
                    <td> ' . $extra['master']['customerAddress'] . '</td>
                </tr>
                <tr>
                    <td><strong>' . $this
                    ->lang
                    ->line('common_telephone') . '/' . $this
                    ->lang
                    ->line('common_fax') . '</strong></td><!--Telephone / Fax -->
                    <td><strong>:</strong></td>
                    <td>' . $extra['master']['customerTelephone'] . ' / ' . $extra['master']['customerFax'] . '</td>
                </tr>';
            }
            $html .= '<tr>
                <td><strong>' . $this
                ->lang
                ->line('common_currency') . ' </strong></td><!--Currency-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )' . '</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>' . $this
                ->lang
                ->line('sales_markating_narration') . ' </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td colspan="4"> 
                    <table>
                        <tr>
                            <td>' . str_replace(PHP_EOL, '<br />, ' . $extra['master']['invoiceNarration']) . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><strong>' . $this
                ->lang
                ->line('sales_markating_view_invoice_delivery_date') . '</strong></td><!--Delivery Date-->
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
            if (!empty($extra['item_detail']))
            {
                $html .= '<div class="table-responsive">
            <table class="table table-bordered table-striped" style="width: 100%;">
                <thead>
                <tr>
                    <th class="theadtr" colspan="5">' . $this
                    ->lang
                    ->line('sales_markating_view_invoice_item_details') . '</th><!--Item Details-->
                </tr>
                <tr>
                    <th class="theadtr" style="min-width: 5%">#</th>
                    <th class="theadtr" style="min-width: 15%">' . $this
                    ->lang
                    ->line('sales_markating_view_invoice_item_code') . '</th><!--Item Code-->
                    <th class="theadtr" style="min-width: 65%">' . $this
                    ->lang
                    ->line('sales_markating_view_invoice_item_description') . '</th><!--Item Description-->
                    <th class="theadtr" style="min-width: 10%">' . $this
                    ->lang
                    ->line('sales_markating_view_invoice_uom') . '</th><!--UOM-->
                    <th class="theadtr" style="min-width: 5%">' . $this
                    ->lang
                    ->line('sales_markating_view_invoice_qty') . '</th><!--Qty-->
                </tr>
                </thead>
                <tbody>';

                $norecordfound = $this
                    ->lang
                    ->line('common_no_records_found');
                $num = 1;
                $item_total = 0;
                if (!empty($extra['item_detail']))
                {
                    foreach ($extra['item_detail'] as $val)
                    {
                        $contractcd = '';
                        if (!empty($val['contractCode']))
                        {
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
                }
                else
                {
                    echo '<tr class="danger"><td colspan="5" class="text-center">' . $norecordfound . '</td></tr>';
                }
                $html .= '</tbody>
            </table>
        </div>';

                if ($extra['master']['approvedYN'])
                {
                    $html .= '<div class="table-responsive"><br>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td><b>' . $this
                        ->lang
                        ->line('sales_markating_view_invoice_electronically_approved_by') . '</b></td><!--Electronically Approved By -->
                        <td><strong>:</strong></td>
                        <td>' . $extra['master']['approvedbyEmpName'] . '</td>
                    </tr>
                    <tr>
                        <td><b>' . $this
                        ->lang
                        ->line('sales_markating_view_invoice_electronically_approved_date') . ' </b></td><!--Electronically Approved Date-->
                        <td><strong>:</strong></td>
                        <td>' . $extra['master']['approvedDate'] . '</td>
                    </tr>
                    </tbody>
                </table>
            </div>';
                }
            }
        }
        $html .= '<br>
        <br>
        <br>';
    }

    if ($policyPIE && $policyPIE == 1 && !empty($extra['approvallevels']))
    {
        $html .= '<div class="table-responsive"><br>
        <table style="width: 60%">
            <tbody>';
        foreach ($extra['approvallevels'] as $val)
        {
            $html .= '<tr>
                            <td><b>' . $this
                ->lang
                ->line('sales_markating_view_invoice_electronically_approved_by') . ' (' . $this
                ->lang
                ->line('common_level') . '  ' . $val['approvalLevelID'] . ')</b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td>' . $val['Ename2'] . '</td>
                        </tr>
                        <tr>
                            <td><b>' . $this
                ->lang
                ->line('sales_markating_view_invoice_electronically_approved_date') . ' (' . $this
                ->lang
                ->line('common_level') . '  ' . $val['approvalLevelID'] . ')</b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td>' . $val['ApprovedDate'] . '</td>
                        </tr>';
        }

        $html .= '</tbody>
        </table>
    </div>';
    }
    if ($extra['master']['approvedYN'])
    {
        if ($signature)
        {
            if ($signature['approvalSignatureLevel'] <= 2)
            {
                $width = "width: 50%";
            }
            else
            {
                $width = "width: 100%";
            }

            $html .= '<div class="table-responsive">
            <table style="' . $width . '">
                <tbody>
                <tr>';

            for ($x = 0;$x < $signature['approvalSignatureLevel'];$x++)
            {
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

    if ($emailView != 1)
    {
        $mpdf->WriteHTML($html, 2);
        $html = "";
    }
    else
    {
        echo $html;
    }
}
else
{
    $html = warning_message("No Records Found!");
}
if ($emailView != 1)
{
    $mpdf->Output();
}
