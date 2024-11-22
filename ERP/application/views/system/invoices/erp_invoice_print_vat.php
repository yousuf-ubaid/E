<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$taxDetailView = getPolicyValues('TDP', 'All');
$policyPIE = getPolicyValues('PIE', 'All');
$seNoYN = getPolicyValues('SEC', 'All');
$POView = '';
if(!empty($extra['po_numberEST'])) {
    // $POView = implode(',&nbsp;&nbsp;', (array_column($extra['po_numberEST'], 'poNumber')));
    $po_numberEST=array_unique(array_column($extra['po_numberEST'], 'poNumber'));
    $POView = implode(',&nbsp;&nbsp;', ($po_numberEST));

}

if($emailView!=1)
{

    
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
            ]
        );
    }
    else{

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
}

$html = "";
$container = "";
$container_bl = "";
$deliveryorder = "";
if (!empty($extra)) {
    if ($printHeaderFooterYN == 1) {
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
    if($policyPIE && $policyPIE == 1 && $Approved != 1) {
        $invoiceheaderName = 'Preliminary Invoice';
    } else  if($group_based_tax == 1 && $extra['master']['vatRegisterYN'] == 1) {
        $invoiceheaderName = 'Tax Invoice';
    } else {
        $invoiceheaderName = $this->lang->line('sales_markating_view_invoice_sales_invoice');
    }


    $html .= '<hr>
<div class="table-responsive">
   
    <div style="text-align: center"><h4>' . $invoiceheaderName . '</h4><!--Sales Invoice --></div>';

    $html .= '<table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong> ' . $this->lang->line('common_customer_name') . '</strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> ' . $custnam . '</td>

            <td><strong>' . $this->lang->line('common_invoice_number') . '</strong></td><!--Invoice Number-->
            <td><strong>:</strong></td>
            <td>' . $extra['master']['invoiceCode'] . '</td>
        </tr>';
    $cussyscodee = '';
    if (!empty($extra['customer']['customerSystemCode'])) {
        $html .= '<tr>
                <td><strong> ' . $this->lang->line('sales_markating_view_invoice_customer_address') . '</strong></td><!--Customer Address -->
                <td><strong>:</strong></td>
                <td> ' . $extra['customer']['customerAddress1'] . '</td>

                <td><strong>' . $this->lang->line('sales_markating_view_invoice_document_date') . '</strong></td><!--Document Date-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['invoiceDate'] . '</td>
            </tr>';
    }


    $referenceno = '';
    if(!empty($extra['master']['referenceNo'])){
        $referenceno.='
                            <td><strong>Reference Number</strong></td>
                            <td><strong>:</strong></td>
                            <td>'.$extra['master']['referenceNo'].';</td>
                       ';
    }
    if(!empty($invoice_referenceno_so_qut)){
        $referenceno.='
                            <td><strong>Reference Number</strong></td>
                            <td><strong>:</strong></td>
                            <td>'.$invoice_referenceno_so_qut.'</td>
                        ';
    }

    if (!empty($invoice_referenceno) && (empty($extra['master']['referenceNo']))){
        $referenceno.='<td style="vertical-align: top"><strong>Reference Number</strong></td>';
        $referenceno.='<td style="vertical-align: top"><strong>:</strong></td>';
        $referenceno.='<td>';
        if (!empty($invoice_referenceno)){
            $a =1;
        }
        foreach($invoice_referenceno as $val) {
            if($a > 1) {
                $referenceno.='</br>';
            }
            if(!empty($val['referenceno'])) {
                $referenceno.= $val['referenceno'];
                $a++;
            }
        }
        $referenceno.='</td>';
    }

    $date_of_supply_view = '';
    if($group_based_tax == 1){
        $date_of_supply_view = '<td><strong>Date Of Supply </strong></td><!--Customer Telephone-->
                                  <td><strong>:</strong></td>
                                  <td> '. $date_of_supply.'</td>';
    }

    $seView = '';
    if(isset($seNoYN) && $seNoYN == 1){
        $seView = '<td><strong>SE Number </strong></td>
          <td><strong>:</strong></td>
          <td> '. $extra['master']['seNumber'].'</td>';
    }

    $html .= '<tr>
            <td><strong>' . $this->lang->line('common_customer_telephone') . ' </strong></td><!--Customer Telephone-->
            <td><strong>:</strong></td>
            <td> ' . $extra['customer']['customerTelephone'] . '</td>

            '.$referenceno.'
        </tr>

        <tr> <td><strong> ' . $this->lang->line('common_contact_person') . '</strong></td>
            <td><strong>:</strong></td>
            <td> ' . $extra['master']['contactPersonName'] . '</td>'. $seView;

    if($seView != ''){
        $html .= '</tr><tr>
                            <td><strong>&nbsp;</strong></td>
                            <td><strong>&nbsp;</strong></td>
                            <td>&nbsp;</td>';

    }
    $html .= '
                <td><strong>' . $this->lang->line('common_currency') . ' </strong></td><!--Currency-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )' . '</td>
            </tr>';


    if($group_based_tax == 1){
        $html .= '
                        <tr>
                            <td><strong>Customer VATIN</strong></td>
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['vatIdNo'].'</td>
                            <td><strong>VATIN</strong></td><!--Narration-->
                            <td><strong>:</strong></td>
                            <td> ' . $extra['master']['companyVatNumber'] . '</td>
                        </tr>';
    }

    $html .= '<tr>
            <td><strong>' . $this->lang->line('sales_marketing_contact_person_tel') . '</strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td>' . $extra['master']['contactPersonNumber'] . '</td>

            <td><strong> ' . $this->lang->line('sales_markating_view_invoice_invoice_date') . '</strong></td><!--Invoice Date-->
            <td><strong>:</strong></td>
            <td> ' . $extra['master']['customerInvoiceDate'] . '</td>
        </tr>';

    $html .= '<tr>';
    if (!empty($extra['master']['salesPersonID'])) {
        $html .= '<td><strong> ' . $this->lang->line('sales_markating_view_invoice_sales_person') . '</strong></td><!--Sales Person -->
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['SalesPersonName'] . ' (' . $extra['master']['SalesPersonCode'] . ')</td>';
    }

    $html .= '<td><strong>' . $this->lang->line('sales_markating_view_invoice_invoice_due_date') . '</strong></td><!--Invoice Due Date-->
            <td><strong>:</strong></td>
            <td> ' . $extra['master']['invoiceDueDate'] . '</td>
        </tr>';

    $html .= '<tr>';
    if(!empty($extra['master']['logisticContainerNo']))
    {
        $container.=' <td><strong>Container No</strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td>' . $extra['master']['logisticContainerNo'] . '</td>' ;
    }
    if(!empty($extra['master']['logisticBLNo']))
    {
        $container_bl.=' <td><strong>BL No</strong></td><!--Reference Number-->
            <td><strong>:</strong></td>
            <td>' . $extra['master']['logisticBLNo'] . '</td>' ;
    }

    $html .= '<tr>
            <td><strong>Segment </strong></td><!--Customer Telephone-->
          <td><strong>:</strong></td>
            <td> '. $extra['master']['segDescription'].' ('. $extra['master']['segmentCode'].')</td>';

    if($container != "") {
        $html .= $container . '
                </tr>
                <tr>
                    <td><strong>&nbsp;</strong></td>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>';
    }

    $html .= $date_of_supply_view;

    $html .= '</tr>
        <tr>';

    $html .= '<tr>';
    $html .= '<td><strong> ' . $this->lang->line('sales_markating_narration') . ' </strong></td><!--Narration-->
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['invoiceNarration'] . '</td>';
    $html .= '</tr>';
    if($invoiceType == 'Manufacturing')
    {
        $html .='<td><strong> PO Number </strong></td>
                <td><strong>:</strong></td>
                <td>'. $POView .'</td>' ;
    }

    $html .= $container_bl;
    $html .= '</tr>';

    $html.='</tbody>
    </table>
</div><br>';
    if($invoiceType == 'Project') {

        $html .= '<div class="table-responsive">
        <strong>Billing based on Completion %</strong>
        <table class="table table-bordered table-striped" style="width: 100%">
            <thead>
                <tr>
                 <th class="theadtr" style="width: 30%">Description</th>
                 <th class="theadtr" style="width: 20%">Total Amount - As per BOQ selleing price</th>
                 <th class="theadtr" style="width: 10%">Previous % caimed</th>
                 <th class="theadtr" style="width: 10%">current % claimed</th>
                 <th class="theadtr" style="width: 10%">Invoice Amount</th>
                  <th class="theadtr" style="width: 10%">Remaining</th>
                </tr>
            </thead>
            <tbody>';
        $category = array();
        $totalvariationcontract = 0;
        $grandtotalinvoice = 0;
        foreach ($extra['invoiceproject'] as $val) {
            $category[$val["isVariation"]][] = $val;
        }
        if (!empty($category)) {
            foreach ($category as $key => $mainCategory) {
                $totalamount = 0;
                $totalinvoiceamount = 0;
                foreach ($mainCategory as $key2 => $subCategory) {

                    if($subCategory['boqPreviousClaimPercentage'] > 0)
                    {
                        $remainingamount = number_format((($subCategory['totalTransCurrency'] -$subCategory['transactionAmount'])-($subCategory['totalTransCurrency']*($subCategory['boqPreviousClaimPercentage']/100))),$extra['master']['transactionCurrencyDecimalPlaces']);
                    }else
                    {
                        $remainingamount = number_format(($subCategory['totalTransCurrency'] -$subCategory['transactionAmount']),$extra['master']['transactionCurrencyDecimalPlaces']);

                    }
                    $html .= '<tr>
                 <input type="hidden" id="prevclaimedpercentage" name="prevclaimedpercentage" value='.$subCategory['boqPreviousClaimPercentage'].'>
                 <input type="hidden" id="remainingamount" name="remainingamount" value='.$remainingamount.'>

                   <td>'. $subCategory["itemDescription"] .'</td>
                  <td style="text-align: right;">'. number_format($subCategory["totalTransCurrency"],$extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                  <td style="text-align: right;">'.number_format($subCategory['boqPreviousClaimPercentage'],$extra['master']['transactionCurrencyDecimalPlaces']).'%</td>
                  <td style="text-align: right;">'.number_format($subCategory['boqTotalClaimPercentage'],$extra['master']['transactionCurrencyDecimalPlaces']).'&nbsp;%</td>
                     

                    <td style="text-align: right;">
                        '.number_format($subCategory['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'
                      </td>

                      <td style="text-align: right;">
                      
                        <label id="remaining">'.$remainingamount.'</label>
                      </td>



                </tr>';
                    $totalamount+= $subCategory['totalTransCurrency'];
                    $totalinvoiceamount += $subCategory['transactionAmount'];
                    $totalvariationcontract+= $subCategory['totalTransCurrency'];
                    $grandtotalinvoice  += $subCategory['transactionAmount'];



                }
                if($subCategory["isVariation"] == 0)
                {
                    $html .= "
                        <tr style='background: #e1e1e18c'>
                        <td><b>Contract Value</b></td>
                              <td style='text-align: right;'><b>".number_format($totalamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                              <td style='text-align: right;'>&nbsp;</td>
                              <td style='text-align: right;'>&nbsp;</td>
                              <td style='text-align: right;'><b>".number_format($totalinvoiceamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                              <td style='text-align: right;'>&nbsp;</td>
                       </tr>
                        ";
                    $html .="<tr><td colspan='6'>&nbsp;</td></tr>";
                    $html .="<tr style='background: #e1e1e18c'><td colspan='6'><b>Variations</b></td></tr>";
                }

            }
            $html .= "<tr style='background: #e1e1e18c'>

                       <td>total variations Amount</td>
                       <td style='text-align: right;'><b>".number_format($totalamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>".number_format($totalinvoiceamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";
            $html .= "<tr style='background: #e1e1e18c'>
                        <td><b>Total contract Value+ variations Amount</b>   </td>
                        <td style='text-align: right;'><b>".number_format($totalvariationcontract,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                        <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'><b>".number_format($grandtotalinvoice,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                        <td style='text-align: right;'>&nbsp;</td>
                     
                        </tr>";

            $html .= "<tr><td colspan='6'>&nbsp;</td></tr>";
            $html .= "<tr><td colspan='6'>&nbsp;</td></tr>";
            $html .= "<tr style='background: #e1e1e18c'><td colspan='6'><b>Deductions</b></td></tr>";



            $html .= "<tr>

                       <td>Advance
                     </td>
                 
                         <td style='text-align: right;'>&nbsp;</td>
                         <td style='text-align: right;'>&nbsp;</td>
                         <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'><b>".number_format(get_advance_amount($subCategory['invoiceAutoID']),$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                     
                        </tr>";
            $html .= "<tr>

                    <td colspan='4'>Retention (".$subCategory['retensionPercentage']."%)</td>


                  <td style='text-align: right;'><b>".number_format((($grandtotalinvoice)*($subCategory['retensionPercentage']/100)),$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                    </tr>";
            $html .= "<tr style='background: #e1e1e18c'>

                       <td><b>Total Dedections</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>".number_format((get_advance_amount($subCategory['invoiceAutoID'])+(($grandtotalinvoice)*($subCategory['retensionPercentage']/100))),$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";

            $html .= "<tr style='background: #e1e1e18c'>
                       <td><b>Net Total</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>


         ".number_format(($grandtotalinvoice-(get_advance_amount($subCategory['invoiceAutoID'])+(($grandtotalinvoice)*($subCategory['retensionPercentage']/100)))),$extra['master']['transactionCurrencyDecimalPlaces'])."



      </b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";
        }
        $html .= '</tbody>
        
          
        </table>
    </div>';

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

    }
    else if ($invoiceType =='Manufacturing') {
        $gran_total = 0;
        $amount_tal_grand = 0;
        $amount_other_tal_grand = 0;
        $amount_applicable_tal_grand = 0;
        $html .= '</table>
                </div><br>';
        $totalAmount = 0;
        $colspan = 6;
        $colspan_footer = 5;
        $grop_based_col_tax_html = '';
        if($group_based_tax==1) {
            $colspan = 10;
            $colspan_footer = 9;
            $grop_based_col_tax_html.='<th style="font-weight: bold;font-size: 12px;">Tax<br>Applicable<br>Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;" width="6%">VAT %</th>
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
                <td style="font-weight: bold">SN#</td>
                <td style="font-weight: bold">Item Description</td>
                <td style="font-weight: bold">UoM</td>
                <td style="font-weight: bold">Qty</td>
                <td style="font-weight: bold;text-align: center">Unit Rate</td>
                '.$grop_based_col_tax_html.';
                <td style="font-weight: bold">Amount</td>
            </tr>
            <tbody>';
            // $totalAmount = 0;  
            $num = 1;  
            foreach ($extra['item_detail'] as $val) {
                $grop_based_col_tax_html_val = '';
                if($group_based_tax==1) {
                    $totalAmount+=(($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']);
                    $grop_based_col_tax_html_val = ' 
                        <td style="text-align: right">'.number_format($val['unittransactionAmount']*$val['requestedQty'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align: right">'.number_format($val['taxpercentageLedger'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align: right">'.number_format($val['amount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                        <td style="text-align: right">'.format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                         <td style="text-align: right">'.number_format((($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']), $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>';

                         $amount_tal_grand += $val['amount'];
                         $amount_applicable_tal_grand += ($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty'];
                         $amount_other_tal_grand += $val['taxAmount'] - $val['amount']; 
                         $item_total_grand += $val['transactionAmount'];        
                }else {
                    $totalAmount+=($val['unittransactionAmount']*$val['requestedQty']);
                    $grop_based_col_tax_html_val = ' <td style="text-align: right">'.number_format(($val['unittransactionAmount']*$val['requestedQty']), $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>';
                }
                $html.='<tr>
                        <td width="5px">'. $num . '</td>
                        <td width="25%">' . $val['mfq_item_Description'] . '</td>
                        <td width="5%">'.$val['defaultUOM'].'</td>
                        <td style="text-align: right">'.$val['requestedQty'].'</td>
                        <td style="text-align: right">'.number_format($val['unittransactionAmount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                        
                       '.$grop_based_col_tax_html_val.'
                        
                       
                       </tr>';

                       $gran_total += ($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount'];
                       $num++;
            }
        }
        $html.='</tbody></table>';

        if (!empty($extra['gl_detail'])) {
            $grop_based_col_tax_html_Gl = '';
            if($group_based_tax==1) {
                $colspan = 10;
                $grop_based_col_tax_html_Gl.='<th style="font-weight: bold;font-size: 12px;">Tax<br>Applicable<br>Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;" width="6%">VAT %</th>
                                   <th style="font-weight: bold;font-size: 12px;">VAT<br> Amount</th>
                                   <th style="font-weight: bold;font-size: 12px;">Other<br>Tax</th>';
            }

            $html.='<br><table cellspacing="0" cellpadding="4" border="1">
            <tbody><tr style="font-size: 12px;">
                <td colspan="'.$colspan.'" style="text-align:center; font-weight: bold">GL Detail</td>
                </tr>
                            
            <tr style="font-size: 12px;">
                <td style="font-weight: bold">SN#</td>
                <td style="font-weight: bold">GL Code</td>
                <td style="font-weight: bold">GL Code Description</td>
                <td style="font-weight: bold">Qty</td>
                <td style="font-weight: bold">Unit Rate</td>
                '.$grop_based_col_tax_html_Gl.';
                <td style="font-weight: bold">Amount</td>
            </tr>'; 

            $num = 1;  
            foreach ($extra['gl_detail'] as $val) {

                $grop_based_col_tax_html_val = '';
                if($group_based_tax==1) {
                    $totalAmount+=(($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']);
                    $grop_based_col_tax_html_val = ' 
                        <td style="text-align: right">'.number_format($val['unittransactionAmount']*$val['requestedQty'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align: right">'.number_format($val['taxpercentageLedger'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align: right">'.number_format($val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align: right">'.format_number(($val['taxAmount']-$val['amount']),$extra['master']['transactionCurrencyDecimalPlaces']) .'</td>
                         <td style="text-align: right">'.number_format((($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>';

                         $amount_tal_grand += $val['amount'];
                         $amount_applicable_tal_grand += $val['transactionAmount'] + $val['discountAmount'];
                         $amount_other_tal_grand += $val['taxAmount']-$val['amount'];
                }else {
                    $totalAmount+=($val['unittransactionAmount']*$val['requestedQty']);
                    $grop_based_col_tax_html_val = ' <td style="text-align: right">'.number_format(($val['unittransactionAmount']*$val['requestedQty']),2).'</td>';
                }

                $html.='<tr>
                        <td width="5%">'.$num.'</td>
                        <td width="10%">'.$val['revenueGLAutoID'].'</td>
                        <td width="25%">'.$val['manufacturinggldes'].'</td>
                        <td style="text-align: right">'.$val['requestedQty'].'</td>
                        <td style="text-align: right">'.number_format($val['unittransactionAmount'], $extra['master']["transactionCurrencyDecimalPlaces"]).'</td>
                       '.$grop_based_col_tax_html_val.';
                       </tr>';
                       $gran_total += ($val['unittransactionAmount']*$val['requestedQty'])+$val['taxAmount'];
                       $num ++;
            }
        }

        if (!empty($extra['gl_detail']) || !empty($extra['item_detail'])) {
            $html.='<tr>
                        <td style="text-align: right" colspan="'.$colspan_footer.'"><b>Grand Total</b></td>
                        <td style="text-align: right"><b>'.number_format($totalAmount,$extra['master']["transactionCurrencyDecimalPlaces"]).'</b></td>
                    </tr>';
        }
        $html.='</tbody></table>';


        if($group_based_tax == 1) {
            $total = number_format($item_total_grand, $extra['master']['transactionCurrencyDecimalPlaces'], '.', '');
            $dicountExplode = explode('.', $total);
            $numberinword = $this->numbertowords->convert_number($dicountExplode[0]);
            $total_in_word = $numberinword;
            if ($dicountExplode) {
                if (isset($dicountExplode[1])) {
                    if ($extra['master']['transactionCurrencyID'] == 1) {
                        $total_in_word = $numberinword . ' and ' . $dicountExplode[1] . ' / 1000';
                    } else {
                        $total_in_word = $numberinword . ' and ' . $dicountExplode[1] . ' / 100';
                    }
                }
            }
            $html .= '<br>
                    <div class="table-responsive">
                        <h5>(Grand Total in ' . $extra['master']['transactionCurrency'] . ' : ' . $total_in_word . ')</h5>';
            if($group_based_tax == 1 && $extra['master']['transactionCurrencyID'] != $extra['master']['companyLocalCurrencyID']) {
               
                $html .= '
                        <table class="table table-bordered table-striped" style="width: 100%">
                         <thead>
                            <tr>
                                <th class="theadtr" style="width: 30%">Conversion Rate</th>
                                <th class="theadtr" style="width: 15%">Currency</th>
                                <th class="theadtr" style="width: 10%">Taxable Amount</th>
                                <th class="theadtr" style="width: 10%">VAT Amount</th>
                                <th class="theadtr" style="width: 10%">Other Tax Amount</th>
                                <th class="theadtr" style="width: 10%">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-size: 11px;width: 15%; text-align: left;" >Conversion Rate(' . $extra['master']['companyLocalCurrency'] . ') : ' . format_number((1/$extra['master']['companyLocalExchangeRate']), 4) . '</td>
                                <td style="text-align:right;font-size: 11px;" >' . $extra['master']['transactionCurrency'] . '</td>
                                <td style="text-align:right;font-size: 11px;" > ' . format_number(($amount_applicable_tal_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td>
                                <td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_tal_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td>';
                                if($amount_other_tal_grand > 0) {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_other_tal_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td></div>';
                                } else {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(0, $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td></div>';
                                }
                                $html .= '<td style="text-align:right;font-size: 11px;"> ' . format_number(($item_total_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px;width: 15%; text-align: left;" >&nbsp;</td>
                                <td style="text-align:right;font-size: 11px;">' . $extra['master']['companyLocalCurrency'] . '</td>
                                <td style="text-align:right;font-size: 11px;" > ' . format_number(($amount_applicable_tal_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td>
                                <td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_tal_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td>';
                                if($amount_other_tal_grand > 0) {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_other_tal_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td></div>';
                                } else {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(0, $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td></div>';
                                }
                                $html .= '<td style="text-align:right;font-size: 11px;"> ' . format_number(($item_total_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td>
                            </tr>
                        </tbody>
                    </table>';
            }
            $html .= '</div>';
        }

        if($group_based_tax == 1) {
            $taxAmount_gl = array_sum(array_column($extra['gl_detail'], 'taxAmount'));
            $vatAmount_gl = array_sum(array_column($extra['gl_detail'], 'amount'));

            $taxAmount_item = array_sum(array_column($extra['item_detail'], 'taxAmount'));
            $vatAmount_item = array_sum(array_column($extra['item_detail'], 'amount'));

            $gran_total_tax = ($taxAmount_gl + $taxAmount_item);
            $grandtotalVAT_amount = $vatAmount_gl + $vatAmount_item;
            $grandtotal_otherTax_amount = ($gran_total_tax - $grandtotalVAT_amount);

            if($group_based_tax == 1 && $extra['master']['transactionCurrencyID'] != $extra['master']['companyLocalCurrencyID']) {
                $html .= '<br><table style="width: 100%">
                        <tr>';
                $html .= '<td style="width: 50%;">
                            <h5 class="text-right" style="text-align:right;font-size: 12px"> Sub Total (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total--> 
                            &nbsp;&nbsp;&nbsp;&nbsp; :  ' . format_number(($gran_total - $grandtotalVAT_amount) / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                    ';
                $html .= '
                            <h5 class="text-right" style="text-align:right;font-size: 12px"> VAT (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($grandtotalVAT_amount / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                   ';
                $html .= '
                            <h5 class="text-right" style="text-align:right;font-size: 12px"> Other Tax (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total-->
                           &nbsp;&nbsp;&nbsp; : ' . format_number($grandtotal_otherTax_amount / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                    ';
                $html .= ' <h5 class="text-right" style="text-align:right;font-size: 12px"> ' . $this->lang->line('common_total') . ' (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($gran_total / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                   </td>';


                $html .= '<td><div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;: ' . format_number(($gran_total - $grandtotalVAT_amount), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             VAT (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($grandtotalVAT_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             Other Tax (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;  : ' . format_number($grandtotal_otherTax_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div></td>';

                $html .= '</tr></table>';
            } else {
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;">
                            Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                             : ' . format_number(($gran_total - $grandtotalVAT_amount), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;">
                                VAT (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                 : ' . format_number($grandtotalVAT_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                            </div>';
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;">
                                Other Tax (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                 : ' . format_number($grandtotal_otherTax_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;">
                                    ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                     : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                            </div>';
            }

        } else{
            $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;"> ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
        }
        
        if ($extra['master']['bankGLAutoID']) {
            $a = $this->load->library('NumberToWords');
            $numberinword = $this->numbertowords->convert_number($totalAmount);
            $point = format_number($totalAmount, $extra['master']['transactionCurrencyDecimalPlaces']);
            $str_arr = explode('.', $point);
            $str1 = '';
            if ($str_arr[1] > 0) {
                if ($extra['master']['transactionCurrency'] == "OMR") {
                    $str1 = ' and ' . $str_arr[1] . ' / 1000 Only';
                } else {
                    $str1 = ' and ' . $str_arr[1] . ' / 100 Only';
                }
            }

            $html .= '<div class="table-responsive">
                    <h6>' . $this->lang->line('sales_markating_view_invoice_remittance_details') . '</h6><!--Remittance Details-->
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width: 18%"><strong>' . $this->lang->line('common_bank') . '</strong></td><!--Bank-->
                            <td style="width: 2%"><strong>:</strong></td>
                            <td style="width: 80%">' . $extra['master']['invoicebank'] . '</td>
                        </tr>
                        <tr>
                            <td><strong>' . $this->lang->line('common_branch') . '</strong></td><!--Branch-->
                            <td><strong>:</strong></td>
                            <td>' . $extra['master']['invoicebankBranch'] . '</td>
                        </tr>
                        <tr>
                            <td><strong>' . $this->lang->line('sales_markating_view_invoice_swift_code') . '</strong></td><!--Swift Code-->
                            <td><strong>:</strong></td>
                            <td>' . $extra['master']['invoicebankSwiftCode'] . '</td>
                        </tr>
                        <tr>
                            <td><strong>' . $this->lang->line('common_account') . '</strong></td><!--Account-->
                            <td><strong>:</strong></td>
                            <td>' . $extra['master']['invoicebankAccount'] . '</td>
                        </tr>
                        <tr>
                            <td><strong>Amount in words</strong></td><!--Account-->
                            <td><strong>:</strong></td>
                            <td>' . $numberinword . $str1 . '</td>
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
        if ($extra['master']['approvedYN']) {
            $html .= '<div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_by') . ' </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['approvedbyEmpName'] . '</td>
            </tr>
            <tr>
                <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_date') . ' </b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['approvedDate'] . '</td>
            </tr>
            </tbody>
        </table>
    </div><br><br>';
        }
    }
    else {


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
        if ((($taxEnabled == 1) || ($taxEnabled == null) || ($extra['item_detail_tax'] > 0))&&  ($group_based_tax!=1)) {
            $colspan = 6;
            $istaxEnable = 1;
            $footercolspan = 9;
        } else if($group_based_tax==1){
            $colspan = 7;
            $istaxEnable = 0;
            $footercolspan = 6;
        }
        else {
            $colspan = 4;
            $istaxEnable = 0;
            $footercolspan = 7;
        }

        $amount_tal_grand = 0;
        $amount_other_tal_grand = 0;
        $amount_applicable_tal_grand = 0;
        $item_total_grand = 0;
        if (!empty($extra['item_detail'])) {
            $col_name = '';

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
        <table class="table table-bordered table-striped" style="width: 100%">
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
                <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_net') . ' Amount</th><!--Net-->
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
                    <td style="text-align:right;font-size: 12px;">' . format_number(($val['taxpercentageLedger']),2) . '</td>';

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

                $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);

            }
            $html .= '
            <tr>
                <td class="text-right sub_total" colspan="'.$footercolspan.'" style="text-align:right;font-weight: bold">' . $this->lang->line('common_grand_total') . '<!--Item Total -->(' . $extra['master']['transactionCurrency'] . ') </td>';
            if ($group_based_tax == 1) {
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">&nbsp;</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($amount_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .= '<div style="'.$class_hide.'"><td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold" >' . format_number($amount_other_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td></div>';
            }
            $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;font-weight: bold">' . format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
            </tr>';
            $html .= '</tbody>
            <tfoot></tfoot>
        </table>
    </div>';

        }
        $transaction_total = 0;
        $Local_total = 0;
        $party_total = 0;
        $disc_nettot = 0;
        $colspan = 5;

        $grop_based_col_tax_html = '';
        $grop_based_col_tax_html_val = '';
        $amount_total = 0;
        $amount_applicable_tal = 0;
        $amount_total_other = 0;
        if (!empty($extra['gl_detail'])) {
            $taxAmount = array_sum(array_column($extra['gl_detail'], 'taxAmount'));
            $vatAmount = array_sum(array_column($extra['gl_detail'], 'amount'));
            $class_hide = '';
            if(($taxAmount - $vatAmount) <= 0) {
                $class_hide = 'display: none;';
            }

            if($group_based_tax==1) {
                $grop_based_col_tax_html.='<th class="theadtr" style="width: 12%">Taxable<br>Amount</th>
                                   <th class="theadtr" style="width: 12%">VAT %</th>
                                   <th class="theadtr" style="width: 6%">VAT<br> Amount</th>
                                   <div style="'.$class_hide.'"><th class="theadtr" >Other<br>Tax</th></div>';
                $colspan = 4;
            }else {
                $grop_based_col_tax_html.='<th class="theadtr" style="width: 15%">' . $this->lang->line('common_amount') . '(' . $extra['master']['transactionCurrency'] . ') </th>';
            }
            $html .= '<br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="theadtr" style="width: 5%">#</th>
                <th class="theadtr" style="width: 20%;text-align: left;">' . $this->lang->line('common_description') . '</th><!--Description-->
                <th class="theadtr" style="width: 10%">' . $this->lang->line('common_segment') . '</th><!--Segment-->
               <th class="theadtr" style="width: 12%">Discount</th>
               '.$grop_based_col_tax_html.'
                <th class="theadtr" style="width: 15%">Net Amount (' . $extra['master']['transactionCurrency'] . ')</th>
            </tr>
            </thead>
            <tbody>';

            $num = 1;

            foreach ($extra['gl_detail'] as $val) {
                $grop_based_col_tax_html_val = '';
                if($group_based_tax==1){
                    $grop_based_col_tax_html_val.=' <td style="text-align:right;">' . format_number($val['transactionAmount'] + $val['discountAmount'] - $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                                            <td style="font-size: 12px;text-align: right"> '.format_number(($val['taxpercentageLedger']),2).'</td>
                                            <td style="font-size: 12px;text-align: right"> '.format_number($val['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                            <div style="'.$class_hide.'"><td style="font-size: 12px;text-align: right"> '.format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td></div>';
                    $amount_total += $val['amount'];
                    $amount_applicable_tal += $val['transactionAmount'] + $val['discountAmount'] - $val['taxAmount'];
                    $amount_total_other += $val['taxAmount']-$val['amount'];

                    $amount_tal_grand += $val['amount'];
                    $amount_applicable_tal_grand += $val['transactionAmount'] + $val['discountAmount'] - $val['taxAmount'];
                    $amount_other_tal_grand += $val['taxAmount']-$val['amount'];
                } else {
                    $grop_based_col_tax_html_val = ' <td style="text-align:right;">' . format_number($val['transactionAmount'] + $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                }


                $html .= '<tr>
                    <td style="text-align:right;font-size: 12px;">' . $num . '.&nbsp;</td>
                    <td style="font-size: 12px;">' . $val['description'] . '</td>
                    <td style="text-align:center;font-size: 12px;">' . $val['segmentCode'] . '</td>
                    <td style="text-align:right;">(' . format_number($val['discountPercentage'], 2) . ' %) ' . format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                     '.$grop_based_col_tax_html_val.';
                     
                 
                    <td style="text-align:right;">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                </tr>';

                $num++;
                $gran_total += $val['transactionAmount'];
                $transaction_total += $val['transactionAmount'];
                $item_total_grand += $val['transactionAmount'];
                $p_total += $val['transactionAmount'];
                $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);
            }

            $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="'.$colspan.'" style="text-align:right;"> ' . $this->lang->line('common_total') . '</td><!--Total-->';
            if($group_based_tax==1) {
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;">&nbsp;</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;">' . format_number($amount_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .= '<div style="'.$class_hide.'"><td class="text-right sub_total" style="font-size: 11px;text-align:right;">' . format_number($amount_total_other, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td></div>';
            }
            $html .= '<td class="text-right sub_total" style="font-size: 11px;text-align:right;">' . format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
            </tr>
            </tfoot>
        </table>
    </div>';
        }

        if($group_based_tax == 1) {
            if($group_based_tax == 1 && $extra['master']['transactionCurrencyID'] != $extra['master']['companyLocalCurrencyID']) {
                $total = number_format($item_total_grand, $extra['master']['transactionCurrencyDecimalPlaces'], '.', '');
                $dicountExplode = explode('.', $total);
                $numberinword = $this->numbertowords->convert_number($dicountExplode[0]);
                $total_in_word = $numberinword;
                if ($dicountExplode) {
                    if (isset($dicountExplode[1])) {
                        if ($extra['master']['transactionCurrencyID'] == 1) {
                            $total_in_word = $numberinword . ' and ' . $dicountExplode[1] . ' / 1000';
                        } else {
                            $total_in_word = $numberinword . ' and ' . $dicountExplode[1] . ' / 100';
                        }
                    }
                }
                $html .= '<br>
                    <div class="table-responsive">
                        <h5>(Grand Total in ' . $extra['master']['transactionCurrency'] . ' : ' . $total_in_word . ')</h5>
                        <table class="table table-bordered table-striped" style="width: 100%">
                         <thead>
                            <tr>
                                <th class="theadtr" style="width: 30%">Conversion Rate</th>
                                <th class="theadtr" style="width: 15%">Currency</th>
                                <th class="theadtr" style="width: 10%">Taxable Amount</th>
                                <th class="theadtr" style="width: 10%">VAT Amount</th>
                                <th class="theadtr" style="width: 10%">Other Tax Amount</th>
                                <th class="theadtr" style="width: 10%">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-size: 11px;width: 15%; text-align: left;" >Conversion Rate(' . $extra['master']['companyLocalCurrency'] . ') : ' . format_number((1/$extra['master']['companyLocalExchangeRate']), 4) . '</td>
                                <td style="text-align:right;font-size: 11px;" >' . $extra['master']['transactionCurrency'] . '</td>
                                <td style="text-align:right;font-size: 11px;" > ' . format_number(($amount_applicable_tal_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td>
                                <td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_tal_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td>';
                                if($amount_other_tal_grand > 0) {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_other_tal_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td></div>';
                                } else {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(0, $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td></div>';
                                }
                                $html .= '<td style="text-align:right;font-size: 11px;"> ' . format_number(($item_total_grand), $extra['master']['transactionCurrencyDecimalPlaces']) . ' </td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px;width: 15%; text-align: left;" >&nbsp;</td>
                                <td style="text-align:right;font-size: 11px;">' . $extra['master']['companyLocalCurrency'] . '</td>
                                <td style="text-align:right;font-size: 11px;" > ' . format_number(($amount_applicable_tal_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td>
                                <td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_tal_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td>';
                                if($amount_other_tal_grand > 0) {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(($amount_other_tal_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td></div>';
                                } else {
                                    $html .= '<div><td style="text-align:right;font-size: 11px;"> ' . format_number(0, $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td></div>';
                                }
                                $html .= '<td style="text-align:right;font-size: 11px;"> ' . format_number(($item_total_grand / $extra['master']['companyLocalExchangeRate']), $extra['master']['companyLocalCurrencyDecimalPlaces']) . ' </td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            }
        }


        $transaction_total = 0;
        $Local_total = 0;
        $party_total = 0;
        if (!empty($extra['delivery_order'])) {
            $html .= '<br>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th colspan="4" class="theadtr">' . $this->lang->line('sales_marketing_delivery_order_based') . '</th>
                            <th colspan="4" class="theadtr">
                            ' . $this->lang->line('common_amount') . '
                                <span class="currency"> (' . $extra['master']['transactionCurrency'] . ' )</span>
                            </th>
                        </tr>
                        <tr>
                            <th class="theadtr" style="width: 5%">#</th>
                            <th class="theadtr" style="min-width: 15%;text-align: left;">' . $this->lang->line('common_code') . '</th>
                            <th class="theadtr" style="width: 15%">' . $this->lang->line('common_date') . '</th>
                            <th class="theadtr" style="width: 10%">' . $this->lang->line('common_reference_no') . '</th>
                            <th class="theadtr" style="width: 10%">' . $this->lang->line('common_order_total') . '</th>
                            <th class="theadtr" style="width: 10%">' . $this->lang->line('common_due') . '</th>
                            <th class="theadtr" style="width: 15%">' . $this->lang->line('common_amount') . '</th>
                            <th class="theadtr" style="width: 10%">' . $this->lang->line('common_balance') . '</th>
                        </tr>
                        </thead>
                        <tbody>';
                        $num = 1;
                        $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
                        foreach ($extra['delivery_order'] as $val) {
                            $html .= '<tr>
                                <td style="text-align:right; font-size: 12px;">' . $num . '.&nbsp;</td>
                                <td style="font-size: 12px;">' . $val['DOCode'] . '</td>
                                <td style="text-align:center; font-size: 12px;">' . $val['DODate'] . '</td>
                                <td style="text-align:center; font-size: 12px;">' . $val['referenceNo'] . '</td>
                                <td style="text-align:right; font-size: 12px;">' . format_number($val['do_tr_amount'], $dPlace) . '</td>
                                <td style="text-align:right; font-size: 12px;">' . format_number($val['due_amount'], $dPlace) . '</td>
                                <td style="text-align:right; font-size: 12px;">' . format_number($val['transactionAmount'], $dPlace) . '</td>
                                <td style="text-align:right; font-size: 12px;">' . format_number($val['balance_amount'], $dPlace) . '</td>
                            </tr>';
                            $num++;
                            $gran_total += $val['transactionAmount'];
                            $transaction_total += $val['transactionAmount'];
                            $p_total += $val['transactionAmount'];
                            $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);
                        }

                        $html .= '</tbody>
                        <tfoot>
                        <tr>
                            <td class="text-right sub_total" colspan="7" style="text-align:right;"> ' . $this->lang->line('common_total') . ' </td>
                            <td class="text-right sub_total" style="font-size: 11px; text-align:right;">' . format_number($transaction_total, $dPlace) . '</td>
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
                                        <td colspan="3" class="text-right sub_total" style="font-size: 12px;text-align:right;">Total</td>
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
                <table style="width: 100%">
                    <tr>
                        <td style="width:40%;">
                            &nbsp;
                        </td>
                        <td style="width:60%;padding: 0;">
                            <table style="width: 100%" class="' . table_class() . '">
                                <thead>
                                <tr>
                                    <td class="theadtr" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . $this->lang->line('sales_markating_view_invoice_tax_details') . '</strong></td><!--Tax Details-->
                                </tr>
                                <tr>
                                    <th class="theadtr">#</th>
                                    <th class="theadtr">' . $this->lang->line('common_type') . '</th><!--Type-->
                                    <th class="theadtr"> ' . $this->lang->line('sales_markating_view_invoice_detail') . '</th><!--Detail-->
                                    <th class="theadtr">' . $this->lang->line('sales_markating_view_invoice_tax') . '</th><!--Tax-->
                                    <th class="theadtr">' . $this->lang->line('common_transaction') . '<!--Transaction -->(' . $extra['master']['transactionCurrency'] . ') </th>

                                </tr>
                                </thead>
                                <tbody>';

                    $tax_Local_total += ($tax_transaction_total / $extra['master']['companyLocalExchangeRate']);
                    $tax_customer_total += ($tax_transaction_total / $extra['master']['customerCurrencyExchangeRate']);
                    $x = 1;
                    $tr_total_amount = 0;
                    $cu_total_amount = 0;
                    $loc_total_amount = 0;
                    $t_extraCharge = 0;
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
                                    <td colspan="4" class="text-right sub_total" style="font-size: 12px; text-align:right;">' . $this->lang->line('sales_markating_view_invoice_tax_total') . '</td><!--Tax Total-->
                                    <td class="text-right sub_total" style="font-size: 12px; text-align:right;">' . format_number($tr_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                                </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>';
        }


        if($group_based_tax == 1) {
            $taxAmount_gl = array_sum(array_column($extra['gl_detail'], 'taxAmount'));
            $vatAmount_gl = array_sum(array_column($extra['gl_detail'], 'amount'));

            $taxAmount_item = array_sum(array_column($extra['item_detail'], 'taxAmount'));
            $vatAmount_item = array_sum(array_column($extra['item_detail'], 'amount'));

            $gran_total_tax = ($taxAmount_gl + $taxAmount_item);
            $grandtotalVAT_amount = $vatAmount_gl + $vatAmount_item;
            $grandtotal_otherTax_amount = ($gran_total_tax - $grandtotalVAT_amount);

            if($group_based_tax == 1 && $extra['master']['transactionCurrencyID'] != $extra['master']['companyLocalCurrencyID']) {
                $html .= '<br><table style="width: 100%">
                        <tr>';
                $html .= '<td style="width: 50%;">
                            <h5 class="text-right" style="text-align:right;font-size: 12px"> Sub Total (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total--> 
                            &nbsp;&nbsp;&nbsp;&nbsp; :  ' . format_number(($gran_total - $grandtotalVAT_amount) / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                    ';
                $html .= '
                            <h5 class="text-right" style="text-align:right;font-size: 12px"> VAT (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($grandtotalVAT_amount / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                   ';
                $html .= '
                            <h5 class="text-right" style="text-align:right;font-size: 12px"> Other Tax (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total-->
                           &nbsp;&nbsp;&nbsp; : ' . format_number($grandtotal_otherTax_amount / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                    ';
                $html .= ' <h5 class="text-right" style="text-align:right;font-size: 12px"> ' . $this->lang->line('common_total') . ' (' . $extra['master']['companyLocalCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($gran_total / $extra['master']['companyLocalExchangeRate'], $extra['master']['companyLocalCurrencyDecimalPlaces']) . '</h5>
                   </td>';


                $html .= '<td><div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;: ' . format_number(($gran_total - $grandtotalVAT_amount), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             VAT (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($grandtotalVAT_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             Other Tax (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;  : ' . format_number($grandtotal_otherTax_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;font-size: 12px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div></td>';

                $html .= '</tr></table>';
            } else {
                $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;">
                            Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                             : ' . format_number(($gran_total - $grandtotalVAT_amount), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;">
                                VAT (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                 : ' . format_number($grandtotalVAT_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                            </div>';
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;">
                                Other Tax (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                 : ' . format_number($grandtotal_otherTax_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;">
                                    ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                     : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                            </div>';
            }

        } else{
            $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;"> ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                            : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
        }

        if ($extra['master']['rebateAmount'] > 0) {
            $html .= '<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;">Rebate (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number($extra['master']['rebateAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';
            $html .= '<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;">Net Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number(($gran_total - $extra['master']['rebateAmount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';
        }

        /*    $data['documentCode'] = 'CINV';
            $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
            $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
            $data['documentID'] = $extra['master']['invoiceAutoID'];
            $html.= $this->load->view('system/tax/tax_detail_view.php',$data,true);*/
        if($extra['master']['invoiceType'] == 'DeliveryOrder' && $group_based_tax == 1) {
            $data['documentCode'] = 'DOCINV';
            $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
            $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
            $data['documentID'] = $extra['master']['invoiceAutoID'];
            $html .= $this->load->view('system/tax/tax_detail_view.php',$data,true);
        }

        if ($extra['master']['bankGLAutoID']) {
            $a = $this->load->library('NumberToWords');
            $numberinword = $this->numbertowords->convert_number(ROUND($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']));
            $point = format_number($gran_total - $extra['master']['rebateAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
            $str_arr = explode('.', $point);
            $str1 = '';
            if ($str_arr[1] > 0) {
                if ($extra['master']['transactionCurrency'] == "OMR") {
                    $str1 = ' and ' . $str_arr[1] . ' / 1000 Only';
                } else {
                    $str1 = ' and ' . $str_arr[1] . ' / 100 Only';
                }
            }

            $html .= '<div class="table-responsive">
        <h6>' . $this->lang->line('sales_markating_view_invoice_remittance_details') . '</h6><!--Remittance Details-->
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width: 18%"><strong>' . $this->lang->line('common_bank') . '</strong></td><!--Bank-->
                <td style="width: 2%"><strong>:</strong></td>
                <td style="width: 80%">' . $extra['master']['invoicebank'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('common_branch') . '</strong></td><!--Branch-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['invoicebankBranch'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('sales_markating_view_invoice_swift_code') . '</strong></td><!--Swift Code-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['invoicebankSwiftCode'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('common_account') . '</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['invoicebankAccount'] . '</td>
            </tr>
            <tr>
                <td><strong>Amount in words</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>' . $numberinword . $str1 . '</td>
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
        if ($extra['master']['approvedYN']) {
            $html .= '<div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_by') . ' </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['approvedbyEmpName'] . '</td>
            </tr>
            <tr>
                <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_date') . ' </b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['approvedDate'] . '</td>
            </tr>
            </tbody>
        </table>
    </div>';
        }

        if ($extra['master']['invoiceNote']) {
            $html .= '<div class="table-responsive"><br>
    <h6>' . $this->lang->line('sales_markating_view_invoice_notes') . '</h6><!--Notes-->
    <table style="width: 100%">
        <tbody>
        <tr>
            <td>' . $extra['master']['invoiceNote'] . '</td>
        </tr>
        </tbody>
    </table>';
        }
        if ($printHeaderFooterYN == 1) {
            $do = '';
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
        } else {
            $deliveryorder .= '';
            $do .= '<div style="text-align: center"><h4 style="font-family:Segoe,Roboto,Helvetica,arial,sans-serif">Delivery note
</h4></div>';

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
                <td style="vertical-align: top"><strong>' . $this->lang->line('sales_markating_narration') . ' </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td colspan="4"> 
                    <table>
                        <tr>
                            <td>'. str_replace(PHP_EOL, '<br />, '. $extra['master']['invoiceNarration']).'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><strong>'. $this->lang->line('sales_markating_view_invoice_delivery_due_date') .'</strong></td><!--Delivery Date-->
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


                if ($extra['master']['approvedYN']) {
                    $html .= '<div class="table-responsive"><br>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_by') . '</b></td><!--Electronically Approved By -->
                        <td><strong>:</strong></td>
                        <td>' . $extra['master']['approvedbyEmpName'] . '</td>
                    </tr>
                    <tr>
                        <td><b>' . $this->lang->line('sales_markating_view_invoice_electronically_approved_date') . ' </b></td><!--Electronically Approved Date-->
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

    if($emailView!=1)
    {
        $mpdf->WriteHTML($html, 2);
        //$mpdf->AddPage();
        $html="";
    }else
    {
        echo $html;
    }
}else
{
    $html = warning_message("No Records Found!");
}
if($emailView!=1)
{
    $mpdf->Output();
}


/*$mpdf->WriteHTML($html, 2);
//$mpdf->AddPage();
$html="";
} else {
$html = warning_message("No Records Found!");
}
$mpdf->Output();*/
?>






