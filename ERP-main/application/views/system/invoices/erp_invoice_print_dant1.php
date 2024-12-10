<?php

use Mpdf\Mpdf;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$taxDetailView = getPolicyValues('TDP', 'All');
$policyPIE = getPolicyValues('PIE', 'All');
$hideInvoiceDueDatepolicy = getPolicyValues('IDD', 'All'); // policy for invoice due date


if($extra['master']['invoiceType']=='Sales Order' || $extra['master']['invoiceType']=='Contract Based') {
    $printHeaderFooterYN = 0;
}
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



if (!empty($extra)) {
if($extra['master']['invoiceType']=='Sales Order' || $extra['master']['invoiceType']=='Contract Based'){

    $html .= '<div class="table-responsive">';

    $printHeaderFooterYN=0;
    if($printHeaderFooterYN==1){

        $html .= '<table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong>'.$this->common_data['company_data']['company_name'].'.</strong></h3>

                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="'. $logo.$this->common_data['company_data']['company_logo'].'">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        </div>';

    }
    $html .= '<div class="table-responsive">
        <table class="table table-bordered" style="width: 50%;">
            <tr><td style="font-size: 11px; line-height: 3px;">Consignee</tr>
            <tr><td style="font-size: 11px; line-height: 3px;">'. $extra['customer']['customerName'].'</td></tr>
            <tr><td style="font-size: 11px; line-height: 3px;">'. $extra['customer']['customerAddress1'].'</td></tr>
            <tr><td style="font-size: 11px; line-height: 3px;">'. $extra['customer']['customerCountry'].'</td></tr>
        </table>

        <br>
        <table  style="width: 100%">
            <tbody>
            <tr>
                <td style="width: 60%;">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td style="width: 20%;"><strong>Invoice No :</strong></td>
                            <td>'. $extra['master']['invoiceCode'].'</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"><strong>PO No :</strong></td>
                            <td>'. $extra['master']['referenceNo'].'</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"><strong>Area :</strong></td>
                            <td>'. $extra['warehousearea']['wareHouseLocation'].'</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 40%;">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td><strong> Date :</strong></td>
                            <td> '. $extra['master']['invoiceDate'].'</td>
                        </tr>';



                        $date_format_policy = date_format_policy();
                        $invDate = $extra['master']['invoiceDate'];
                        $invoiceDate = input_format_date($invDate, $date_format_policy);
                        $dat= (explode("-",$invoiceDate));
                        $monthNum  = $dat[1];
                        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                        $monthName = $dateObj->format('F'); // March

    $html .= '<tr>
                            <td colspan="2">(Month of '. $monthName .' '.$dat[0] .')</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>';


    $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;

    if(!empty($extra['master']['isOpYN']==1 && $extra['master']['retensionInvoiceID'] =='' ) ) {


        $retensionTransactionAmount= ($extra['master']['retensionTransactionAmount'] !='' ? $extra['master']['retensionTransactionAmount']:0);

        $html .= '<div class="table-responsive">
            <table class="table table-bordered table-striped" >
                <thead>
                <tr>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 5%">Sr No</th>
                    <!--   <th class="theadtr" style="font-size: 8px !important; min-width: 15%">Item Code</th>-->
                    <th class="theadtr" style="font-size: 8px !important; min-width: 35%">Item Description</th>
                    <!-- <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Line Details</th>-->
                    <th class="theadtr" style="font-size: 8px !important; min-width: 5%">UOM</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Qty</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Unit Rate ('. $extra['master']['transactionCurrency'].')</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Total Value ('. $extra['master']['transactionCurrency'].')</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Previous Certified (%)</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Current Month Certified (%)</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Cummulative (%)</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Previous Certified Amount ('. $extra['master']['transactionCurrency'].')</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">This Month Invoice ('. $extra['master']['transactionCurrency'].')</th>
                </tr>
                </thead>
                <tbody>';

                $num =1;$item_total = 0;
                $is_item_active = 1;
                foreach ($extra['gl_detail'] as $val) {
                    $invoiceDetailsAutoID=$val['invoiceDetailsAutoID'];
                    $reqqty['requestedQty']=0;
                    $reqqty['amount']=0;
                    $previousCertifiedAmount=0;
                    if($val['previousCertifiedAmount']!=''){
                   $previousCertifiedAmount= $val['previousCertifiedAmount'];
                    }

                    $html .= '<tr>
                        <td style="text-align:right;">'. $num.'.&nbsp;</td>

                        <td>'. $val['itemDescription'].' </td>
                        <td style="text-align:center;">'. $val['unitOfMeasure'].'</td>
                        <td style="text-align:right;">'. format_number( $val['contractQty'],2).'</td>
                        <td style="text-align:right;">'. format_number($val['contractAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align:right;">'. format_number($val['contractAmount']*$val['contractQty'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align:right;">'. round($val['previousCertified'],2) .' %</td>
                        <td style="text-align:right;">'. round($val['currentCertified'],2)  .' %</td>
                        <td style="text-align:right;">'. round($val['previousCertified']+$val['currentCertified'],2) .' %</td>
                        <td style="text-align:right;">'. format_number(('.$previousCertifiedAmount.'), $extra['master']['transactionCurrencyDecimalPlaces'])  .' </td>
                        <td style="text-align:right;">'. format_number( $val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) .' </td>
                    </tr>';

                    $num ++;
                    $item_total += $val['transactionAmount'];

                }
        $html .= '</tbody>
                <tfoot>
                <tr>
                    <td class="sub_total" style="text-align: right;" colspan="10">Total Amount in '. $extra['master']['transactionCurrency'].' </td>
                    <td class="text-right sub_total" style="text-align:right;">'. format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';
                if($retensionTransactionAmount>0){
                    $html .= '<tr>
                        <td class="sub_total" style="text-align: right;" colspan="10">Retention Amount in '. $extra['master']['transactionCurrency'].'</td>
                        <td class="text-right sub_total" style="text-align:right;">'. format_number($extra['master']['retensionTransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    </tr>';
                 }
        $html .= '<tr>';

                    $a=$this->load->library('NumberToWords');
                    $retensionTransactionAmount= ($extra['master']['retensionTransactionAmount'] !='' ? $extra['master']['retensionTransactionAmount']:0);
                    $number=round($item_total-$retensionTransactionAmount, $extra['master']['transactionCurrencyDecimalPlaces']);
                    $numberinword= $this->numbertowords->convert_number($number);
                    $str_arr = explode('.',$number);
                    if($str_arr[1]>0){
                        if($extra['master']['transactionCurrency']=="OMR"){
                            $numinwrd=$numberinword.' and '.$str_arr[1].' / 1000 Only';
                        }else{
                            $numinwrd=$numberinword.' and '.$str_arr[1].' / 100 Only';
                        }
                    }else{
                        $numinwrd=$numberinword.' Only';
                    }

        $html .= '<td colspan="8" class="" style="font-size: 11px;border: none">Amount Chargeable (in words)</td>';
                     if($retensionTransactionAmount>0){
                         $html .= '<td colspan="2"  class="sub_total" style="text-align: right;border: none">Net Amount in '. $extra['master']['transactionCurrency'].'</td>
                        <td colspan="1"  class="sub_total" style="text-align: right"> '. format_number($item_total-$extra['master']['retensionTransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'/td>';
                     }
        $html .= '</tr>
                <tr>
                    <td colspan="11" style="font-size: 11px;"><i>('. $numinwrd .')</i></td>
                </tr>
                </tfoot>
            </table>
        </div>';
      }


    if(!empty($extra['master']['isOpYN']==1 && $extra['master']['retensionInvoiceID'] !='' ) ) {


        $retensionTransactionAmount= ($extra['master']['retensionTransactionAmount'] !='' ? $extra['master']['retensionTransactionAmount']:0);

        $html .= '<div class="table-responsive">
            <table class="table table-bordered table-striped" >
                <thead>
                <tr>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 5%">Sr No</th>

                    <th class="theadtr" style="font-size: 8px !important; min-width: 35%">Description</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 35%">Refrence Invoice No</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Total Value ('. $extra['master']['transactionCurrency'].')</th>

                </tr>
                </thead>
                <tbody>';

                $num =1;$item_total = 0;
                $is_item_active = 1;
                foreach ($extra['gl_detail'] as $val) {
                    $invoiceDetailsAutoID=$val['invoiceDetailsAutoID'];
                    $reqqty['requestedQty']=0;
                    $reqqty['amount']=0;
                    $html .= '<tr>
                        <td style="text-align:right;">'. $num .' &nbsp;</td>

                        <td>'. $val['description'].'</td>
                        <td>'. $extra['master']['retentionInvoiceCode'].' </td>

                        <td style="text-align:right;">'. format_number( $val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).' </td>
                    </tr>';

                    $num ++;
                    $item_total += $val['transactionAmount'];

                }
        $html .= '</tbody>
                <tfoot>
                <tr>
                    <td class="sub_total" style="text-align: right;" colspan="3">Total Amount in '. $extra['master']['transactionCurrency'].' </td>
                    <td class="text-right sub_total" style="text-align:right;">'. format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';

        $html .= '<tr>';

                    $a=$this->load->library('NumberToWords');
                    $retensionTransactionAmount= ($extra['master']['retensionTransactionAmount'] !='' ? $extra['master']['retensionTransactionAmount']:0);
                    $number=round($item_total-$retensionTransactionAmount, $extra['master']['transactionCurrencyDecimalPlaces']);
                    $numberinword= $this->numbertowords->convert_number($number);
                    $str_arr = explode('.',$number);
                    if($str_arr[1]>0){
                        if($extra['master']['transactionCurrency']=="OMR"){
                            $numinwrd=$numberinword.' and '.$str_arr[1].' / 1000 Only';
                        }else{
                            $numinwrd=$numberinword.' and '.$str_arr[1].' / 100 Only';
                        }
                    }else{
                        $numinwrd=$numberinword.' Only';
                    }

        $html .= '<td colspan="4" class="" style="font-size: 11px;border: none">Amount Chargeable (in words)</td>

                </tr>
                <tr>
                    <td colspan="4" style="font-size: 11px;"><i>('. $numinwrd .')</i></td>
                </tr>
                </tfoot>
            </table>
        </div>';
  }

    $html .= '<br>
    <br>';
    $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
    if(!empty($extra['item_detail']) ) {
        $html .= '<div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 5%">Sr No</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 15%">Item Code</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 35%">Item Description</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Line Details</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 5%">UOM</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Qty</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Unit Rate ('. $extra['master']['transactionCurrency'].')</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Total Value ('. $extra['master']['transactionCurrency'].')</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Previous Certified (%)</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Current Month Certified (%)</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Cummulative (%)</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">Previous Certified Amount ('. $extra['master']['transactionCurrency'].')</th>
                    <th class="theadtr" style="font-size: 8px !important; min-width: 10%">This Month Invoice ('. $extra['master']['transactionCurrency'].')</th>
                </tr>
                </thead>
                <tbody>';

                $num =1;$item_total = 0;
                $is_item_active = 1;
                foreach ($extra['item_detail'] as $val) {
                    $invoiceDetailsAutoID=$val['invoiceDetailsAutoID'];
                    $contractDetailsAutoID=$val['contractDetailsAutoID'];
                    $reqqty=$this->db->query("SELECT IFNULL(SUM(requestedQty),0) as requestedQty,IFNULL(SUM(srp_erp_customerinvoicedetails.transactionAmount),0) as amount FROM `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE invoiceDate <= '$invoiceDate' AND invoiceDetailsAutoID !=$invoiceDetailsAutoID AND contractDetailsAutoID = $contractDetailsAutoID")->row_array();

                    $html .= '<tr>
                        <td style="text-align:right;">'. $num .'</td>
                        <td style="text-align:center;">'. $val['seconeryItemCode'].'</td>
                        <td>'. $val['itemDescription'] .' </td>
                        <td style="text-align:center;"> '. $val['remarks'].'</td>
                        <td style="text-align:center;">'. $val['unitOfMeasure'].'</td>
                        <td style="text-align:right;">'.format_number($val['contractQty'],2) .'</td>
                        <td style="text-align:right;">'. format_number($val['contractAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align:right;">'. format_number($val['contractAmount']*$val['contractQty'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                        <td style="text-align:right;">'. round(($reqqty['requestedQty'] / $val['contractQty'])*100,2).' %</td>
                        <td style="text-align:right;">'. round(($val['requestedQty'] / $val['contractQty'])*100,2) .' %</td>
                        <td style="text-align:right;">'. round((($reqqty['requestedQty'] / $val['contractQty'])*100),2).' + '.round((($val['requestedQty'] / $val['contractQty'])*100),2).' %</td>
                        <td style="text-align:right;">'. format_number($reqqty['amount'], $extra['master']['transactionCurrencyDecimalPlaces'])  .' </td>
                        <td style="text-align:right;">'. format_number( $val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).' </td>
                    </tr>';

                    $num ++;
                    $item_total += $val['transactionAmount'];

                }
        $html .= '</tbody>
                <tfoot>
                <tr>
                    <td class="sub_total" style="text-align: center;" colspan="12">Total Amount in '. $extra['master']['transactionCurrency'].' </td>
                    <td class="text-right sub_total" style="text-align:right;">'. format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>
                <tr>';

                    $a=$this->load->library('NumberToWords');
                    $number=round($item_total, $extra['master']['transactionCurrencyDecimalPlaces']);
                    $numberinword= $this->numbertowords->convert_number($number);
                    $str_arr = explode('.',$number);
                    if($str_arr[1]>0){
                        if($extra['master']['transactionCurrency']=="OMR"){
                            $numinwrd=$numberinword.' and '.$str_arr[1].' / 1000 Only';
                        }else{
                            $numinwrd=$numberinword.' and '.$str_arr[1].' / 100 Only';
                        }
                    }else{
                        $numinwrd=$numberinword.' Only';
                    }

        $html .= '<td colspan="7" style="font-size: 11px;">Amount Chargeable (in words)</td>
                    <td colspan="3" style="font-size: 11px;">Retention</td>
                    <td colspan="3" style="font-size: 11px;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="13" style="font-size: 11px;"><i>('. $numinwrd .')</i></td>
                </tr>
                </tfoot>
            </table>
        </div>';
  }

    $html .= '<br>';
    $transaction_total = 0;$Local_total = 0;$party_total = 0; $disc_nettot=0;$t_extraCharge=0;
    if(!empty($extra['gl_detail']) && $extra['master']['isOpYN']==0 ){
        $html .= '<br>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="width: 100%;">
                <thead>
                <tr>
                    <th class="theadtr" style="width: 5%">#</th>
                    <th class="theadtr" style="width: 45%;text-align: left;">'. $this->lang->line('common_description').'</th><!--Description-->
                    <th class="theadtr" style="width: 15%">'. $this->lang->line('common_segment').'</th><!--Segment-->
                    <th class="theadtr" style="width: 15%">'. $this->lang->line('common_amount').'('. $extra['master']['transactionCurrency'].') </th><!--Amount-->
                </tr>
                </thead>
                <tbody>';

                $num =1;
                foreach ($extra['gl_detail'] as $val) {
                    $html .= '<tr>
                        <td style="text-align:right;">'. $num .'&nbsp;</td>
                        <td>'. $val['description'].'</td>
                        <td style="text-align:center;">'. $val['segmentCode'].'</td>
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
                    <td class="text-right sub_total" colspan="3" style="text-align:right;"> '. $this->lang->line('common_total').' </td><!--Total-->
                    <td class="text-right sub_total" style="text-align:right;">'. format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';

               if($extra['master']['retensionTransactionAmount'] > 0){
                   $html .= '<tr>
                        <td class="text-right sub_total" colspan="3" style="text-align:right;"> Retention Amount </td><!--Total-->
                        <td class="text-right sub_total" style="text-align:right;">'. format_number($extra['master']['retensionTransactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>

                    </tr>
                    <tr>
                        <td class="text-right sub_total" colspan="3" style="text-align:right;"> Net Total </td><!--Total-->
                        <td class="text-right sub_total" style="text-align:right;">'. format_number($transaction_total - $extra['master']['retensionTransactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>

                    </tr>';
                 }
        $html .= '</tfoot>
            </table>
        </div>';
 }
    $html .= '<br>';

    $designation='';
    if(!empty($extra['master']['transactionCurrencyDecimalPlaces'])){
        $designation=$extra['master']['DesDescription'];
    }

    $html .= ' <table class="table " style="border: 2px solid #ececec;">
        <tr>
            <th style="border: 2px solid #ececec; font-size: 9px;">Dant Najd Trading & Transport '. $designation.'</th>
            <th style="border: 2px solid #ececec; font-size: 9px;">AHPS Representative</th>
            <th style="border: 2px solid #ececec; font-size: 9px;">AHPS Representative</th>
        </tr>
        <tr>
            <td height="60px;" style="font-size: 9px;">Name :  '. $extra['master']['SalesPersonName'].'</td>
            <td height="60px;" style="border-left: 2px solid #ececec; font-size: 9px;">Name : </td>
            <td height="60px;" style="border-left: 2px solid #ececec; font-size: 9px;">Name : </td>
        </tr>
        <tr>
            <td height="60px;" style="font-size: 9px;">Sign : </td>
            <td height="60px;" style="border-left: 2px solid #ececec; font-size: 9px;">Sign : </td>
            <td height="60px;" style="border-left: 2px solid #ececec; font-size: 9px;">Sign : </td>
        </tr>
        <tr>
            <td height="60px;" style="font-size: 9px;">Date : </td>
            <td height="60px;" style="border-left: 2px solid #ececec; font-size: 9px;">Date : </td>
            <td height="60px;" style="border-left: 2px solid #ececec; font-size: 9px;">Date : </td>
        </tr>
    </table>


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
}else{


    $html .= '<div class="table-responsive">';

    if($printHeaderFooterYN==1){

        $html .= '<table style="width: 100%">
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

if($policyPIE && $policyPIE == 1 && $Approved != 1) {
    $invoiceheaderName = 'Preliminary Invoice';
} else {
    $invoiceheaderName = $this->lang->line('sales_markating_view_invoice_sales_invoice');
}

    $html .= '<hr>
<div class="table-responsive">
    <div style="text-align: center"><h4>'. $invoiceheaderName .'</h4><!--Sales Invoice --></div>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong> '. $this->lang->line('common_customer_name').'</strong></td><!--Customer Name-->
            <td style=""><strong>:</strong></td>
            <td style=""> '. $custnam .'</td>

            <td><strong>'. $this->lang->line('common_invoice_number').'</strong></td><!--Invoice Number-->
            <td><strong>:</strong></td>
            <td>'. $extra['master']['invoiceCode'].'</td>
        </tr>';
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
            <td> '. $extra['master']['invoiceDate'].'</td>
        </tr>

        <tr>
            <td><strong>'. $this->lang->line('sales_markating_narration').' </strong></td><!--Narration-->
            <td><strong>:</strong></td>
            <td> '. $extra['master']['invoiceNarration'].'</td>';
            if ($hideInvoiceDueDatepolicy == 0) {
                $html .='<td><strong>'. $this->lang->line('sales_markating_view_invoice_invoice_due_date').'</strong></td><!--Invoice Due Date-->
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['invoiceDueDate'].'</td>';
            }else{
                $html .= '<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>';
            }

 $html .='</tr>

        <tr>';
          if (!empty($extra['master']['SalesPersonCode'])) {
$html .= '<td><strong> '. $this->lang->line('sales_markating_view_invoice_sales_person').'</strong></td><!--Sales Person-->
                <td><strong>:</strong></td>
                <td> '. $extra['master']['SalesPersonCode'].'</td>';
             }else {
$html .= '<td>&nbsp;</td><!--Currency-->
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
        <table class="table table-bordered table-striped" style="width: 100%">
            <thead>
            <tr>
                <th class="theadtr" colspan="5">'. $this->lang->line('sales_markating_view_invoice_item_details').'</th><!--Item Details-->
                <th class="theadtr" colspan="6">'. $this->lang->line('common_price').'('. $extra['master']['transactionCurrency'].') </th><!--Price-->
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
                <th class="theadtr" style="min-width: 10%">'. $this->lang->line('sales_markating_view_invoice_tax').'</th><!--Tax-->
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
                $html .= ' <tr>
                    <td style="text-align:right; font-size: 12px;">'. $num .'.&nbsp;</td>
                    <td style="text-align:center; font-size: 12px;">'. $val['itemSystemCode'].'</td>
                    <td style="font-size: 12px;"> '.$contractcd.' '.$val['itemDescription'].' -  '. $val['remarks'].'</td>
                    <td style="text-align:center; font-size: 12px;">'.$val['unitOfMeasure'].'</td>
                    <td style="text-align:right; font-size: 12px;">'.format_number( $val['requestedQty'],2).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
                </tr>';

                $num ++;
                $gran_total += $val['transactionAmount'];
                $item_total += $val['transactionAmount'];
                $p_total    += $val['transactionAmount'];

                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);

            }
    $html .= ' </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" style="text-align:right;" colspan="10">'. $this->lang->line('sales_markating_view_invoice_item_total').'<!--Item Total -->('. $extra['master']['transactionCurrency'].') </td>
                <td class="text-right sub_total" style="font-size: 12px; text-align:right;">'. format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</td>
            </tr>
            </tfoot>
        </table>
    </div>';
 }
 $transaction_total = 0;$Local_total = 0;$party_total = 0;
if(!empty($extra['gl_detail'])){
    $html .= '<br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th class="theadtr" style="width: 5%">#</th>
                <th class="theadtr" style="width: 45%;text-align: left;">'. $this->lang->line('common_description').'</th><!--Description-->
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
                    <td style="text-align:right;font-size: 12px;">'. $num .'.&nbsp;</td>
                    <td style="font-size: 12px;">'. $val['description'].'</td>
                    <td style="text-align:center;font-size: 12px;">'. $val['segmentCode'].'</td>
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
    $html .= '<br>';
$transaction_total = 0;$Local_total = 0;$party_total = 0;$disc_nettot=0;$t_extraCharge=0;
if(!empty($extra['delivery_order'])  ){
    $html .= '<div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="theadtr" colspan="4">'. $this->lang->line('sales_marketing_delivery_order_based').'</th>
                <th class="theadtr" colspan="4">
                    '. $this->lang->line('common_amount').'
                    <span class="currency"> ('. $extra['master']['transactionCurrency'].' )</span>
                </th>
            </tr>
            <tr>
                <th class="theadtr" style="width: 5%">#</th>
                <th class="theadtr" style="width: 15%;text-align: left;">'. $this->lang->line('common_code').'</th>
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_date').'</th>
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_reference_no').'</th>
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_order_total').'</th>
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_due').'</th>
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_amount').'</th>
                <th class="theadtr" style="width: 15%">'. $this->lang->line('common_balance').'</th>
            </tr>
            </thead>
            <tbody>';

            $num =1;
            $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
            foreach ($extra['delivery_order'] as $val) {
                $html .= ' <tr>
                    <td style="text-align:right; font-size: 12px;">'. $num.'.&nbsp;</td>
                    <td style="font-size: 12px;">'. $val['DOCode'].'</td>
                    <td style="text-align:center; font-size: 12px;">'. $val['DODate'].'</td>
                    <td style="text-align:center; font-size: 12px;">'. $val['referenceNo'].'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['do_tr_amount'], $dPlace).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['due_amount'], $dPlace).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['transactionAmount'], $dPlace).'</td>
                    <td style="text-align:right; font-size: 12px;">'. format_number($val['balance_amount'], $dPlace).'</td>
                </tr>';

                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                $p_total            += $val['transactionAmount'];

            }

    $html .= '</tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="6" style="text-align:right;"> '. $this->lang->line('common_total').' </td>
                <td class="text-right sub_total" style="text-align:right;">'. format_number($transaction_total, $dPlace).'</td>
                <td class="text-right sub_total" style="text-align:right;"> </td>
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
    $html .= '<div class="table-responsive"><h5 class="text-right" style="text-align:right;"> '. $this->lang->line('common_total').' ('. $extra['master']['transactionCurrency'].' ) : '. format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']).'</h5></div>';


 if ($extra['master']['bankGLAutoID']) {
    $a=$this->load->library('NumberToWords');
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
                <td><strong>'. $this->lang->line('common_account').'</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['invoicebankAccount'].'</td>
            </tr>
            <tr>
                <td><strong>Amount in words</strong></td><!--Account-->
                <td><strong>:</strong></td>
                <td>'. $numberinword.$str1.'</td>
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
                <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').' </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['approvedbyEmpName'].'</td>
            </tr>
            <tr>
                <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_date').' </b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td>'. $extra['master']['approvedDate'].'</td>
            </tr>
            </tbody>
        </table>
    </div>';
}
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
$custnam = '';
if(empty($extra['customer']['customerSystemCode'])){
    $custnam= $extra['customer']['customerName'];
}else{
    $custnam= $extra['customer']['customerName'] .' ('. $extra['customer']['customerSystemCode'] .')';
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
                    <td style="width:23%;"><strong>'. $this->lang->line('common_customer_name').' </strong></td><!--Customer Name-->
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:75%;"> '.$custnam.'</td>
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
                        <td> '. $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax'].'</td>
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
                <table class="table table-bordered table-striped" style="width: 100%;">
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
                        $html .= ' <tr>
                        <td style="text-align:right; font-size: 12px;">'. $num.'.&nbsp;</td>
                        <td style="text-align:center; font-size: 12px;">'. $val['itemSystemCode'].'</td>
                        <td style="font-size: 12px;"> ('.$contractcd.')'.$val['itemDescription'].' - '.$val['remarks'].'</td>
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
            </div>';

            if($extra['master']['approvedYN']){
                $html .= ' <div class="table-responsive"><br>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_by').'</b></td><!--Electronically Approved By -->
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['approvedbyEmpName'].'</td>
                        </tr>
                        <tr>
                            <td><b>'. $this->lang->line('sales_markating_view_invoice_electronically_approved_date').' </b></td><!--Electronically Approved Date-->
                            <td><strong>:</strong></td>
                            <td>'. $extra['master']['approvedDate'].'</td>
                        </tr>
                        </tbody>
                    </table>
                </div>';
          }
 } }

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
    $html .= '<br>
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
                                <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                            </td>';
                        }
            $html .= ' </tr>

                    </tbody>
                </table>
            </div>';
         }
    }
}
    $mpdf->WriteHTML($html, 2);
} else {
    $html = warning_message("No Records Found!");
}
$mpdf->Output();

?>



