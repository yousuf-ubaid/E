<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$taxDetailView = getPolicyValues('TDP', 'All');
$itemBarcodeView = getPolicyValues('SBID', 'All');
$policyPIE = getPolicyValues('PIE', 'All');
$HideOtherTaxDetails = getPolicyValues('HOTAX', 'All');
$retensionEnabled = getPolicyValues('RETO', 'All');

$POView = '';

if(!empty($extra['po_numberEST'])) {
    $po_numberEST=array_unique(array_column($extra['po_numberEST'], 'poNumber'));
    $POView = implode(',&nbsp;&nbsp;', ($po_numberEST));
}


if($emailView!=1)
{

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
    }
    else{
        $mpdf = new Mpdf([
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
                                <img alt="Logo" style="max-height: 100px; max-width:200px;" src="' . $logo . $this->common_data['company_data']['company_logo'] . '">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong>'. $this->common_data['company_data']['company_name'] . '.</strong></h3>
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
    } else if($group_based_tax == 1 && $extra['master']['vatRegisterYN'] == 1 && in_array($isPrintDN,[2,4]))
    {
        $invoiceheaderName = 'Tax Invoice';
    } else {
        $invoiceheaderName = $this->lang->line('sales_markating_view_invoice_sales_invoice');
    }

    $html .= '<hr>
            <div class="table-responsive">
                <div style="text-align: center">
                    <h4>' . $invoiceheaderName . '</h4><!--Sales Invoice -->
                </div>';

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
                    <td> ' . $extra['master']['customerAddress'] . '</td>
    
                    <td><strong>' . $this->lang->line('sales_markating_view_invoice_document_date') . '</strong></td><!--Document Date-->
                    <td><strong>:</strong></td>
                    <td>' . $extra['master']['invoiceDate'] . '</td>
                </tr>';
    }
        
    $html .= '<tr>
                <td><strong>' . $this->lang->line('common_customer_telephone') . ' </strong></td><!--Customer Telephone-->
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['customerTelephone'] . '</td>
    
                <td><strong>' . $this->lang->line('common_reference_number') . '</strong></td><!--Reference Number-->
                <td><strong>:</strong></td>
                <td>' . str_replace(PHP_EOL, '<br /> ',  $extra['master']['referenceNo'])  . '</td>
            </tr>

            <tr>
                <td><strong>' . 'Customer Email' . ' </strong></td><!--Customer Email-->
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['customerEmail'] . '</td>
    
                <td><strong>' . $this->lang->line('common_currency') . ' </strong></td><!--Currency-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )' . '</td>
            </tr>';

    $html .= '<tr>
                <td><strong> ' . $this->lang->line('common_customer_weburl') . '</strong></td>
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['customerWebURL'] . '</td>';


    $html .= '
                <td><strong> ' . $this->lang->line('common_contact_person') . '</strong></td>
                <td><strong>:</strong></td>
                <td> ' . $extra['master']['contactPersonName'] . '</td> </tr>';

    if($extra['master']['invoiceType'] == 'Contract'){
        $html .= '<tr>
            <td><strong> ' . $this->lang->line('common_payment_terms'). ' (Days)' . '</strong></td>
            <td><strong>:</strong></td>
            <td> ' . $extra['master']['contractPaymentTerms'] . '</td>';
    }
  

    if($group_based_tax == 1){
        $html .= '<tr>
                <td><strong>Customer VATIN</strong></td>
                <td><strong>:</strong></td>
                <td>'. $extra['master']['vatIdNo'].'</td>
                <td><strong>&nbsp;</strong></td><!--Narration-->
                <td><strong>&nbsp;</strong></td>
                <td> &nbsp;</td>
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
        $html .= '  <td><strong> ' . $this->lang->line('sales_markating_view_invoice_sales_person') . '</strong></td><!--Sales Person -->
                    <td><strong>:</strong></td>
                    <td> ' . $extra['master']['SalesPersonName'] . ' (' . $extra['master']['SalesPersonCode'] . ')</td>';
    } else {
        $html .= '  <td><strong> ' . $this->lang->line('sales_markating_narration') . ' </strong></td><!--Narration-->
                    <td><strong>:</strong></td>
                    <td> ' . $extra['master']['invoiceNarration'] . '</td>';
    }

    $html .= '      <td><strong>' . $this->lang->line('sales_markating_view_invoice_invoice_due_date') . '</strong></td><!--Invoice Due Date-->
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

    $date_of_supply_view = '';
    if($group_based_tax == 1){
        $date_of_supply_view = '<td><strong>Date Of Supply </strong></td><!--Customer Telephone-->
          <td><strong>:</strong></td>
          <td> '. $date_of_supply.'</td>';
    }

    $html .= '<tr>
            <td><strong>Segment </strong></td><!--Customer Telephone-->
          <td><strong>:</strong></td>
            <td> '. $extra['master']['segDescription'].' ('. $extra['master']['segmentCode'].')</td>
            '.$container.';
            
            '.$date_of_supply_view.'
     
           
        </tr>
        <tr>';
    if($invoiceType == 'Manufacturing')
    {
        $html .='<td><strong> PO Number </strong></td>
                <td><strong>:</strong></td>
                <td>'. $POView .'</td>' ;
    }

    $html .= $container_bl;
    $html .= '</tr><tr>';

    if (!empty($extra['master']['salesPersonID'])) {

        $html .= '<td style="vertical-align: top"><strong> ' . $this->lang->line('sales_markating_narration') . ' </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td>
                    <table>
                        <tr>
                            <td>'. str_replace(PHP_EOL, '<br /> ', $extra['master']['invoiceNarration']).'</td>
                        </tr>
                    </table>
                </td>';
    }

    $html .= '</tr>';
        if($group_based_tax == 1){
            $html .= '
                    <tr>
                       <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td>&nbsp;</td>
                        <td><strong>VATIN</strong></td><!--Narration-->
                        <td><strong>:</strong></td>
                        <td> ' . $extra['master']['companyVatNumber'] . '</td>
                    </tr>';
    }
    
            /*    $html.='<tr>
                        <td><strong>Segment </strong></td>
                        <td><strong>:</strong></td>
                        <td> '. $extra['master']['segDescription'].' ('. $extra['master']['segmentCode'].')</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>';*/

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
                  <td style="text-align: right;">'.number_format($subCategory['boqPreviousClaimPercentage'],2).'%</td>
                  <td style="text-align: right;">'.number_format($subCategory['boqTotalClaimPercentage'],2).'&nbsp;%</td>
                     

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
    $totalAmount = 0;
    $html .= '<table width="100%" cellspacing="0" cellpadding="4" border="1" >
        <tbody>';
    if (!empty($extra['item_detail'])) {
        $html .= '<tr style="font-size: 12px;font-weight: bold ">
            <th colspan="5" style="text-align:center;">Item Detail</th>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <th style="font-size: 10px !important;">Item Description</th>
            <th style="font-size: 10px !important;">UOM</th>
            <th style="font-size: 10px !important;">Qty</th>
            <th style="font-size: 10px !important;">Unit Rate</th>
            <th style="font-size: 10px !important;">Amount</th>
        </tr>';
        if (!empty($extra['item_detail'])) {
            foreach ($extra['item_detail'] as $val) {
                $totalAmount += ($val['unittransactionAmount'] * $val['requestedQty']);
                $html .= '<tr>
                        <td style="width: 320px">' . $val['mfq_item_Description'] . '</td>
                        <td style="width: 80px">' . $val['defaultUOM'] . '</td>
                        <td style="text-align: right; width: 80px">' . $val['requestedQty'] . '</td>
                        <td style="text-align: right; width: 100px">' . number_format($val['unittransactionAmount'], $extra['master']["transactionCurrencyDecimalPlaces"]) . '</td>
                        <td style="text-align: right; width: 100px">' . number_format(($val['unittransactionAmount'] * $val['requestedQty']), $extra['master']["transactionCurrencyDecimalPlaces"]) . '</td>
 
                       </tr>';


            }
        }
    }
    if (!empty($extra['gl_detail'])) {
        $html .= '<tr style="font-size: 12px;font-weight: bold ">
            <th colspan="5" style="text-align:center;">GL Detail</th>
            </tr>
                         
        <tr style="font-size: 12px;font-weight: bold ">
            <th style="font-size: 10px !important;">GL Code Description</th>
            <th style="font-size: 10px !important;">GL Code</th>
            <th style="font-size: 10px !important;">Amount</th>
            <th style="font-size: 10px !important;">Discount</th>
            <th style="font-size: 10px !important;">Net Amount</th>
        </tr>';

        if (!empty($extra['gl_detail'])) {
            foreach ($extra['gl_detail'] as $val) {
                $totalAmount += ($val['transactionAmount']);
                $html .= '<tr>
                        <td style="width: 320px">' . $val['manufacturinggldes'] . '</td>
                        <td style="width: 80px">' . $val['revenueGLAutoID'] . '</td>
                        <td style="text-align: right; width: 80px">' . number_format($val['transactionAmount'] + $val['discountAmount'], $extra['master']["transactionCurrencyDecimalPlaces"]) . '</td>
                        <td style="text-align: right; width: 100px">(' . format_number($val['discountPercentage'], 2) . ' %) ' . format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        <td style="text-align: right; width: 100px">' . number_format(($val['transactionAmount']), $extra['master']["transactionCurrencyDecimalPlaces"]) . '</td>
 
                       </tr>';


            }
        }
    }
    if($totalAmount != 0) {
        $html.='  <tr>
                <td style="text-align: right" colspan="4"><b>Total</b></td>
                <td style="text-align: right"><b>'.number_format($totalAmount,$extra['master']["transactionCurrencyDecimalPlaces"]).'</b></td>
            </tr>';
    }
    $html.='</tbody></table>';


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
                            <td><strong>' . $this->lang->line('common_address') . '</strong></td><!--Branch-->
                            <td><strong>:</strong></td>
                            <td>' . isset($extra['master']['bankChartOfAccount']['bankAddress']) ? $extra['master']['bankChartOfAccount']['bankAddress'] : '' . '</td>
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
                            <td>' . $numberinword . $str1 . ' ' . $extra['master']['transactionCurrency'] . '</td>
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
        $footercolspan = 10;
    } else if($group_based_tax==1){
        $colspan = 7;
        $istaxEnable = 0;
        $footercolspan = 7;
    }
    else {
        $colspan = 4;
        $istaxEnable = 0;
        $footercolspan = 8;
    }

    $col_name = '';
    if (!empty($extra['item_detail'])) {



        if(($istaxEnable == 1) && ($group_based_tax!=1))
        {
            $col_name.='
                <th class="theadtr" style="min-width: 10%">Net Unit Price</th>
                <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_total') . '</th>
                <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_tax') . '</th>';
        }else  if($group_based_tax==1) {
            $col_name.='
                <th class="theadtr" style="min-width: 10%">Tax<br>Applicable<br>Amount</th>
                <th class="theadtr" style="min-width: 10%">VAT %</th>
                <th class="theadtr" style="min-width: 10%">VAT<br>Amount</th>';
            if($HideOtherTaxDetails != 1){
                $col_name.='  
                    <th class="theadtr" style="min-width: 10%">Other<br>Tax</th>';
            }
           
        }
        else {
            $col_name.='<th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_sales_net_unit_price') . '</th>';
        }

        if($retensionEnabled == 1){
            $col_name.='  <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_retension') . '</th>';
        }


        $html .= '<div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%">
            <thead>
            <tr>
                <th class="theadtr" colspan="5">' . $this->lang->line('sales_markating_view_invoice_item_details') . '</th><!--Item Details-->
                <th class="theadtr" colspan="'.$colspan.'">' . $this->lang->line('common_price') . ' (' . $extra['master']['transactionCurrency'] . ') </th><!--Price-->
            </tr>
            <tr>
                <th class="theadtr" style="min-width: 5%">#</th>
                <th class="theadtr" style="min-width: 15%">' . $this->lang->line('sales_markating_view_invoice_item_code') . '</th><!--Item Code-->
                <th class="theadtr" style="min-width: 35%">' . $this->lang->line('sales_markating_view_invoice_item_description') . '</th><!--Item Description-->
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
            }else if($group_based_tax==1) {
                $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number(($val['unittransactionAmount'] - $val['discountAmount'])*$val['requestedQty'] , $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    <td style="text-align:right;font-size: 12px;">' . format_number(($val['taxpercentageLedger']),2) . '</td>';

            }
            else {
                $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'] - $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    <td style="text-align:right;font-size: 12px;">' . format_number((($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            }


            if($itemBarcodeView == 1) {
                $item_description_view = '<td style="font-size: 12px;">' . $contractcd . ' ' .$val['barcode'].' | '. $val['itemDescription']. ' -  ' . $val['remarks'] . '</td>';
            } else {
                $item_description_view = '<td style="font-size: 12px;">' . $contractcd . ' '. $val['itemDescription']. ' -  ' . $val['remarks'] . '</td>';
            }

            if($retensionEnabled == 1){
                $retensionValue = '<td style="font-size: 12px;">' . format_number($val['retensionPercentage'], 2)  . ' %) '. format_number(($val['retensionValue']), $extra['master']['transactionCurrencyDecimalPlaces']). '</td>';
            }else{
                $retensionValue = '';
            }


            $html .= '<tr>
                    <td style="text-align:right;font-size: 12px;">' . $num . '.&nbsp;</td>
                    <td style="text-align:center;font-size: 12px;">' . $val['itemSystemCode']. '</td>'
                    . $item_description_view .
                    '<td style="text-align:center;font-size: 12px;">' . $val['unitOfMeasure'] . '</td>
                    <td style="text-align:right;font-size: 12px;">' . $val['requestedQty'] . '</td>
                    <td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                   
                    <td style="text-align:right;font-size: 12px;">(' . format_number($val['discountPercentage'], 2) . ' %) ' . format_number(($val['discountAmount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    '.$retensionValue.'
                    '.$taxCol.'';

                        if($istaxEnable == 1 &&  $group_based_tax!=1) {
                         $html .= ' 
                           <td style="text-align:right;font-size: 12px;">' . format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        }else if($group_based_tax==1) {

                        
                        $html .= '<td style="text-align:right;font-size: 12px;">' . format_number($val['amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                     
                        if($HideOtherTaxDetails != 1){
                            $html .= '<td style="text-align:right;font-size: 12px;">' . format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        }

                        $amount_tal += $val['amount'];
                        $amount_applicable_tal += ($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty'];
                        $amount_other_tal += $val['taxAmount'] - $val['amount'];
                    }
            $html .= ' <td style="text-align:right;font-size: 12px;">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            $html .='</tr>';

            $num++;
            $gran_total += $val['transactionAmount'];
            $item_total += $val['transactionAmount'];
            $p_total += $val['transactionAmount'];

            $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);

        }

     
        $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="'.$footercolspan.'" style="text-align:right;">' . $this->lang->line('sales_markating_view_invoice_item_total') . '<!--Item Total -->(' . $extra['master']['transactionCurrency'] . ') </td>';
                if ($group_based_tax == 1) {
                    $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">&nbsp;</td>';
                    $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    if($HideOtherTaxDetails != 1){
                        $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_other_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                    }
                    
                }
         $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
            </tr>
            </tfoot>
        </table>
    </div>';
    }

    // echo $html;
    // exit;

    $transaction_total = 0;
    $Local_total = 0;
    $party_total = 0;
    $disc_nettot = 0;
    $colspan = 5;

    $grop_based_col_tax_html = '';
    if($group_based_tax==1) {
        $grop_based_col_tax_html.='<th class="theadtr" style="width: 12%">Tax<br>Applicable<br>Amount</th>
                                   <th class="theadtr" style="width: 12%">VAT %</th>
                                   <th class="theadtr" style="width: 12%">VAT<br> Amount</th>
                                   <th class="theadtr" style="width: 10%">Other<br>Tax</th>';
        $colspan = 4;
     }else {
        $grop_based_col_tax_html.='<th class="theadtr" style="width: 15%">' . $this->lang->line('common_amount') . '(' . $extra['master']['transactionCurrency'] . ') </th>';
    }

    if($retensionEnabled == 1){
        $col_name.= '  <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_retension') . '</th>';
    }

    
    $grop_based_col_tax_html_val = '';
    $amount_total = 0;
    $amount_applicable_tal = 0;
    $amount_total_other = 0;
    if (!empty($extra['gl_detail'])) {
        $html .= '<br>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="theadtr" style="width: 5%">#</th>
                    <th class="theadtr" style="width: 38%;text-align: left;">' . $this->lang->line('common_description') . '</th><!--Description-->
                    <th class="theadtr" style="width: 15%">' . $this->lang->line('common_segment') . '</th><!--Segment-->
                <th class="theadtr" style="width: 12%">Discount</th>
                '.$col_name.'
                '.$grop_based_col_tax_html.'
                    <th class="theadtr" style="width: 15%">Net Amount (' . $extra['master']['transactionCurrency'] . ')</th>
                </tr>
                </thead>
                <tbody>';

            $num = 1;

            foreach ($extra['gl_detail'] as $val) {

                //print_r($val); exit;
                $grop_based_col_tax_html_val = '';
                $tax_deduct = 0;
                $discountAdd = $val['discountAmount'];
                if($group_based_tax==1){

                    $grop_based_col_tax_html_val.='<td style="font-size: 12px;text-align: right"> '.format_number(($val['taxpercentageLedger']),2).'</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number($val['amount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                                                <td style="font-size: 12px;text-align: right"> '.format_number(($val['taxAmount']-$val['amount']), $extra['master']['transactionCurrencyDecimalPlaces']) .'</td>';
                    $amount_total += $val['amount'];
                    $amount_applicable_tal += ($val['transactionAmount'] - $val['taxAmount']);
                    $amount_total_other += $val['taxAmount']-$val['amount'];
                    $tax_deduct = $val['taxAmount'];
                    $discountAdd = 0;
                }


                $html .= '<tr>
                        <td style="text-align:right;font-size: 12px;">' . $num . '.&nbsp;</td>
                        <td style="font-size: 12px;">' . $val['description'] . '</td>
                        <td style="text-align:center;font-size: 12px;">' . $val['segmentCode'] . '</td>
                        <td style="text-align:right;">(' . format_number($val['discountPercentage'], 2) . ' %) ' . format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        <td style="text-align:right;">' . format_number(($val['transactionAmount'] + $discountAdd) - $tax_deduct, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                        '.$grop_based_col_tax_html_val.';
                        
                    
                        <td style="text-align:right;">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
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
                    <td class="text-right sub_total" colspan="'.$colspan.'" style="text-align:right;"> ' . $this->lang->line('common_total') . '</td><!--Total-->';
            if($group_based_tax==1) {
                $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">&nbsp;</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_total_other, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            }
            $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($transaction_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                </tr>
                </tfoot>
            </table>
        </div>';
    }

    if($isDOItemWisePolicy  == 1) { //Delivery Order itemwise (SME-3138)
        $is_item_active = 0;
        // $gran_total = 0;
        $tax_transaction_total = 0;
        $tax_Local_total = 0;
        $tax_customer_total = 0;
        $p_total = 0;

        $colspan = 6;
        $footercolspan = 11;
        $istaxEnable = 0;
        $taxEnabled = getPolicyValues('TAX', 'All');
        if ((($taxEnabled == 1) || ($taxEnabled == null) || ($extra['item_detail_tax'] > 0))&&  ($group_based_tax!=1)) {
            $colspan = 8;
            $istaxEnable = 1;
            $footercolspan = 10;
        } else if($group_based_tax==1){
            $colspan = 6;
            $istaxEnable = 0;
            $footercolspan = 7;
        }
        else {
            $colspan = 6;
            $istaxEnable = 0;
            $footercolspan = 10;
        }


        if (!empty($extra['delivery_order_itemwise'])) {
            $col_name = '';


            if(($istaxEnable == 1) && ($group_based_tax!=1))
            {
                $col_name.='
                    <th class="theadtr" style="min-width: 10%">Net Unit Price</th>
                    <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_total') . '</th>
                    <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_tax') . '</th>';
            }else  if($group_based_tax==1) {
                $col_name.='
                    
                    <th class="theadtr" style="min-width: 10%">Tax</th>';
            }
            else {
                $col_name.='  <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_sales_net_unit_price') . '</th>';
            }



            $html .= '<div class="table-responsive">
            <table class="table table-bordered table-striped" style="width: 100%">
                <thead>
                <tr>
                    <th class="theadtr" colspan="5">' . $this->lang->line('sales_marketing_delivery_order_details') . '</th><!--Delivery Order Details-->
                    <th class="theadtr" colspan="'.$colspan.'">' . $this->lang->line('common_price') . ' (' . $extra['master']['transactionCurrency'] . ') </th><!--Price-->
                </tr>
                <tr>
                    <th class="theadtr" style="min-width: 5%">#</th>
                    <th class="theadtr" style="min-width: 15%">' . $this->lang->line('sales_markating_view_invoice_item_code') . '</th><!--Item Code-->
                    <th class="theadtr" style="min-width: 35%">' . $this->lang->line('sales_markating_view_invoice_item_description') . '</th><!--Item Description-->
                    <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_uom') . '</th><!--UOM-->
                    <th class="theadtr" style="min-width: 10%">' . $this->lang->line('common_qty') . '</th><!--Qty-->
                    <th class="theadtr" style="min-width: 10%">' . $this->lang->line('sales_markating_view_invoice_unit') . '</th><!--Unit-->
                    '.$col_name.'
                    
                    <th class="theadtr">' . $this->lang->line('sales_markating_view_invoice_net_total') . '</th><!--Net Amount-->
                   
                </tr>
                </thead>
                <tbody>';

                // <th class="theadtr" style="width: 10%">' . $this->lang->line('common_due') . '</th>
                // <th class="theadtr" style="width: 10%">' . $this->lang->line('common_amount') . '</th>
                // <th class="theadtr" style="width: 10%">' . $this->lang->line('common_balance') . '</th>

            $num = 1;
            $item_total = 0;
            $is_item_active = 1;
            $amount_tal = 0;
            $amount_other_tal = 0;
            $amount_applicable_tal = 0;
            foreach ($extra['delivery_order_itemwise'] as $val) {
                $docd = '';
                if (!empty($val['DOCode'])) {
                    $docd = '(' . $val['DOCode'] . ')';

                }
                $taxCol = '';
                if(($istaxEnable == 1) && ($group_based_tax!=1))
                {
                    $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'] - $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                                <td style="text-align:right;font-size: 12px;">' . format_number((($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                }else  if($group_based_tax==1) {
                  //  $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number(($val['unittransactionAmount'] - $val['discountAmount'])*$val['requestedQty'] , $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                }
                else {
                    $taxCol = '<td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'] - $val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            //    <td style="text-align:right;font-size: 12px;">' . format_number((($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                }

                $html .= '<tr>
                        <td style="text-align:right;font-size: 12px;">' . $num . '.&nbsp;</td>
                        <td style="text-align:center;font-size: 12px;">' . $val['itemSystemCode'] . '</td>
                        <td style="font-size: 12px;">' . $docd . ' ' . $val['itemDescription'] . ' -  ' . $val['remarks'] . '</td>
                        <td style="text-align:center;font-size: 12px;">' . $val['unitOfMeasure'] . '</td>
                        <td style="text-align:right;font-size: 12px;">' . $val['requestedQty'] . '</td>
                        <td style="text-align:right;font-size: 12px;">' . format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                       


                        '.$taxCol.'';

                        if($istaxEnable == 1 &&  $group_based_tax!=1) {
                            $html .= '<td style="text-align:right;font-size: 12px;">' . format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                        }else if($group_based_tax==1) {
                            $html .= '<td style="text-align:right;font-size: 12px;">' . format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            $amount_tal += $val['amount'];
                            $amount_applicable_tal += ($val['unittransactionAmount'] - $val['discountAmount']) * $val['requestedQty'];
                            $amount_other_tal += $val['taxAmount'] - $val['amount'];
                        }
                $html .= ' <td style="text-align:right;font-size: 12px;">' . format_number(($val['requestedQty'] * $val['unittransactionAmount']) + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                // $html .= ' <td style="text-align:right;font-size: 12px;">' . format_number(($val['due_amount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                // $html .= ' <td style="text-align:right;font-size: 12px;">' . format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                // $html .= ' <td style="text-align:right;font-size: 12px;">' . format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                $html .='</tr>';

                $num++;
                $gran_total += $val['transactionAmount'];
                $item_total += $val['transactionAmount'] + $val['taxAmount'];
                $p_total += $val['transactionAmount'];

                $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);

            }
            $html .= '</tbody>
                <tfoot>
                <tr>
                    <td class="text-right sub_total" colspan="'.$footercolspan.'" style="text-align:right;">' . $this->lang->line('sales_markating_view_invoice_item_total') . '<!--Item Total -->(' . $extra['master']['transactionCurrency'] . ') </td>';
            if ($group_based_tax == 1) {
                // $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_applicable_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                // $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">&nbsp;</td>';
                // $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                // $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($amount_other_tal, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
            }
            $html .= '<td class="text-right sub_total" style="font-size: 12px;text-align:right;">' . format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                </tr>
                </tfoot>
            </table>
        </div>';
        }
    
        $transaction_total = 0;
        $Local_total = 0;
        $party_total = 0;
        $disc_nettot = 0;
        $colspan = 5;

        $grop_based_col_tax_html = '';
        if($group_based_tax==1) {
            $grop_based_col_tax_html.='<th class="theadtr" style="width: 12%">Tax<br>Applicable<br>Amount</th>
                                    <th class="theadtr" style="width: 12%">VAT %</th>
                                    <th class="theadtr" style="width: 12%">VAT<br> Amount</th>
                                    <th class="theadtr" style="width: 10%">Other<br>Tax</th>';
            $colspan = 4;
        }else {
            $grop_based_col_tax_html.='<th class="theadtr" style="width: 15%">' . $this->lang->line('common_amount') . '(' . $extra['master']['transactionCurrency'] . ') </th>';
        }

    }else{ // Delivery Order 
        $transaction_total = 0;
        $Local_total = 0;
        $party_total = 0;
        if($group_based_tax == 1){
            $tax_col = '<th class="theadtr" style="width: 10%">Tax</th>';
            $col_span_foot = 8;
            $col_span_head = 5;
        } else {
            $col_span_foot = 7;
            $col_span_head = 4; 
        }
        if (!empty($extra['delivery_order'])) {
            $html .= '<br>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th colspan="4" class="theadtr">' . $this->lang->line('sales_marketing_delivery_order_based') . '</th>
                        <th colspan="'.$col_span_head.'" class="theadtr">
                        ' . $this->lang->line('common_amount') . '
                            <span class="currency"> (' . $extra['master']['transactionCurrency'] . ' )</span>
                        </th>
                    </tr>
                    <tr>
                        <th class="theadtr" style="width: 5%">#</th>
                        <th class="theadtr" style="min-width: 15%;text-align: left;">' . $this->lang->line('common_code') . '</th>
                        <th class="theadtr" style="width: 15%">' . $this->lang->line('common_date') . '</th>
                        <th class="theadtr" style="width: 10%">' . $this->lang->line('common_reference_no') . '</th>
                        '.$tax_col.'
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
                    if($group_based_tax == 1){
                        $tax_col_det = '<td style="text-align:center; font-size: 12px;">' . format_number($val['taxAmount'], $dPlace).'</td>';
                    }
                    $html .= '<tr>
                            <td style="text-align:right; font-size: 12px;">' . $num . '.&nbsp;</td>
                            <td style="font-size: 12px;">' . $val['DOCode'] . '</td>
                            <td style="text-align:center; font-size: 12px;">' . $val['DODate'] . '</td>
                            <td style="text-align:center; font-size: 12px;">' . $val['referenceNo'] . '</td>
                            '.$tax_col_det.'
                            <td style="text-align:right; font-size: 12px;">' . format_number($val['do_tr_amount'], $dPlace) . '</td>
                            <td style="text-align:right; font-size: 12px;">' . format_number($val['due_amount'], $dPlace) . '</td>
                            <td style="text-align:right; font-size: 12px;">' . format_number($val['transactionAmount'], $dPlace) . '</td>
                            <td style="text-align:right; font-size: 12px;">' . format_number($val['balance_amount'], $dPlace) . '</td>
                        </tr>';
                    $num++;
                    $gran_total += $val['due_amount'];
                    $transaction_total += $val['transactionAmount'];
                    $p_total += $val['transactionAmount'];
                    $tax_transaction_total += ($val['transactionAmount'] - $val['totalAfterTax']);
                }
        
                $html .= '</tbody>
                    <tfoot>
                    <tr>
                        <td class="text-right sub_total" colspan="'.$col_span_foot.'" style="text-align:right;"> ' . $this->lang->line('common_total') . ' </td>
                        <td class="text-right sub_total" style="font-size: 12px; text-align:right;">' . format_number($transaction_total, $dPlace) . '</td>
                    </tr>
                    </tfoot>
                </table>
            </div>';
        }
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
                            <!--<th class="theadtr">' . $this->lang->line('sales_markating_view_invoice_tax') . '</th>--><!--Tax-->
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
                    <!--<td class="text-right" style="font-size: 12px; text-align:right;">' . $value['taxPercentage'] . ' % </td>-->
                    <td class="text-right" style="font-size: 12px; text-align:right;">' . format_number((($value['taxPercentage'] / 100) * ($tax_transaction_total - $disc_nettot + $t_extraCharge)), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>
                    </tr>';
            $x++;
            $gran_total += (($value['taxPercentage'] / 100) * ($tax_transaction_total - $disc_nettot + $t_extraCharge));
            $tr_total_amount += (($value['taxPercentage'] / 100) * ($tax_transaction_total - $disc_nettot + $t_extraCharge));
            }

            $html .= '</tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-right sub_total" style="font-size: 12px; text-align:right;">' . $this->lang->line('sales_markating_view_invoice_tax_total') . '</td><!--Tax Total-->
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

        $taxAmount_do_item = 0;
        $vatAmount_do_item = 0;
        if (!empty($extra['delivery_order_itemwise'])) {
            $taxAmount_do_item = array_sum(array_column($extra['delivery_order_itemwise'], 'taxAmount'));
            $vatAmount_do_item = array_sum(array_column($extra['delivery_order_itemwise'], 'amount'));
        }

        $taxAmount_DO = 0;
        $vatAmount_DO = 0;
        if (!empty($extra['delivery_order'])) {
            $taxAmount_DO = array_sum(array_column($extra['delivery_order'], 'taxAmount'));
            $vatAmount_DO = array_sum(array_column($extra['delivery_order'], 'amount'));
        }
        

        $gran_total_tax = ($taxAmount_gl + $taxAmount_item + $taxAmount_do_item + $taxAmount_DO);
        $grandtotalVAT_amount = $vatAmount_gl + $vatAmount_item + $vatAmount_do_item + $vatAmount_DO;

        $grandtotal_otherTax_amount = ($gran_total_tax - $grandtotalVAT_amount);

        if($group_based_tax == 1 && $extra['master']['transactionCurrencyID'] != $extra['master']['companyLocalCurrencyID']) {
            $html .= '<br><table style="width: 100%">
                        <thead>
                        <th><th>
                        <th></th>
                    </thead>
                    <tr><td width="50%"></td>';
            if($extra['master']['invoiceType'] == 'DeliveryOrder'){
                $html .= '<td width="50%" style="text-align:right;">
                    <div class="table-responsive">
                        <h5 class="text-right" style="text-align:right;font-size: 12px;margin:5px 0px 5px 0px">
                        Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                        &nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number(($gran_total), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
            }else{
                $html .= '<td width="50%" style="text-align:right;">
                    <div class="table-responsive">
                        <h5 class="text-right" style="text-align:right;font-size: 12px;margin:5px 0px 5px 0px">
                        Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                        &nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number(($gran_total - $grandtotalVAT_amount - $grandtotal_otherTax_amount), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
            }

         
            $html .= '<div class="table-responsive">
                        <h5 class="text-right" style="text-align:right;font-size: 12px;margin:5px 0px 5px 0px">
                         VAT (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                        &nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; ' . format_number($grandtotalVAT_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                </div>';

            if($HideOtherTaxDetails != 1){
                $html .= '<div class="table-responsive">
                        <h5 class="text-right" style="text-align:right;font-size: 12px;margin:5px 0px 5px 0px">
                         Other Tax (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                         &nbsp;&nbsp;&nbsp;&nbsp;  : &nbsp; ' . format_number($grandtotal_otherTax_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                </div>';
            }

            if($extra['master']['invoiceType'] == 'DeliveryOrder'){

                $html .= '<div class="table-responsive">
                    <h5 class="text-right" style="text-align:right;font-size: 12px;margin:5px 0px 5px 0px">
                    ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                    &nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number(($gran_total + $grandtotal_otherTax_amount + $grandtotalVAT_amount), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                 </div></td>';
                 
            }else{
                $html .= '<div class="table-responsive">
                    <h5 class="text-right" style="text-align:right;font-size: 12px;margin:5px 0px 5px 0px">
                    ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                    &nbsp;&nbsp;&nbsp;&nbsp; : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                </div></td>';
            }
            
         

            $html .= '</tr></table>';
        } else {
            $html .= '<div class="table-responsive">
                        <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px">
                        Sub Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                         : ' . format_number(($gran_total - $gran_total_tax), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                    </div>';
            $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px">
                            VAT (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                             : ' . format_number($grandtotalVAT_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
            
            if($HideOtherTaxDetails != 1){
                $html .= '<div class="table-responsive">
                                <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px">
                                Other Tax (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                : ' . format_number($grandtotal_otherTax_amount, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
            }

            $html .= '<div class="table-responsive">
                            <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px">
                                ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                                 : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                        </div>';
        }

    } else{
        $html .= '<div class="table-responsive">
                        <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px"> ' . $this->lang->line('common_total') . ' (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
                        : ' . format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
                </div>';
    }


    if ($extra['master']['rebateAmount'] > 0) {
        $html .= '<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px">Rebate (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number($extra['master']['rebateAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
</div>';
        $html .= '<div class="table-responsive">
    <h5 class="text-right" style="text-align:right;margin:5px 0px 5px 0px">Net Total (' . $extra['master']['transactionCurrency'] . ' )<!--Total-->
: ' . format_number(($gran_total - $extra['master']['rebateAmount']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</h5>
    </div>';
    }

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
        $point = format_number(($gran_total - $extra['master']['rebateAmount']), $extra['master']['transactionCurrencyDecimalPlaces']);
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
                <td><strong>' . $this->lang->line('common_address') . '</strong></td><!--Branch-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['bankChartOfAccount']['bankAddress'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('sales_markating_view_invoice_swift_code') . '</strong></td><!--Swift Code-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['invoicebankSwiftCode'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('common_account_name') . '</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['bankChartOfAccount']['GLDescription'] . '</td>
            </tr>
            <tr>
                <td><strong>' . $this->lang->line('common_account') . '</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['invoicebankAccount'] . '</td>
            </tr>
            <tr>
                <td><strong>Amount in words</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>' . $numberinword . $str1 . ' ' . $extra['master']['transactionCurrency'] . '</td>
            </tr>
            <tr>
                <td><strong>Bank Currency</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>' . $extra['master']['bankCurrency'] . '</td>
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
    $do = '';
    if ($printHeaderFooterYN == 1) {
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






