<?php

use Mpdf\Mpdf;

$mpdf = "";
if ($mode == 'pdf') {
    
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

    $user = ucwords($this->session->userdata('username'));
    $date = date('l jS \of F Y h:i:s A');
    $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
    $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
    $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
    $mpdf->SetFooter();
    $mpdf->WriteHTML($stylesheet, 1);
    $mpdf->WriteHTML($stylesheet2, 1);
    $mpdf->WriteHTML($stylesheet3, 1);
}
$html = '';

$confirmedUser = fetch_employeeNo($header["createdUserID"]);
$reviewedUser = fetch_employeeNo($header["reviewedBy"]);
$approvedUser = fetch_employeeNo($header["approvedbyEmpID"]);
$currencyCode =$header['transcurrencycode'];
  //fetch_currency_dec($this->common_data["company_data"]["company_default_currency"]); 
  $countrydes = $customercountry['customerCountry'];
  if($customercountry['customerCountry'] == 'Oman')
  { 
      $countrydes ='Sultanate of Oman';
  }

if ($mode == 'html') {
    $html .= '<div id="" class="row review"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank" href="#"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a> </span> </div> </div>';
}

$html .='<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:40%;">
                <img alt="Logo" style="height: 80px;font-size: 12px"
                     src="'.mPDFImage . $this->common_data['company_data']['company_logo'] .'"></td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;margin-top: 10px">
        <tr>
            <td><span style="font-weight: bold;font-size: 12px">
                    Quotation: </span> '.$header["estimateCode"] .'
            </td>
        </tr>
        <tr>
            <td style="height:40px;"><span style="font-weight: bold;font-size: 12px">
                    Date: </span> '.$header["documentDate"] .'
            </td>
        </tr>
        <tr>
            <td style="height:17px;font-size: 12px"><b>
                    To: M/s._<u>'.$header["CustomerName"] .'<u>_
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px"><b>'.$countrydes.'</b>
            </td>
        </tr>
        <tr>
            <td style="height:40px;font-size: 12px"><b><i> Subject: </i></b> : '.$header["description"] .'
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px">
                Dear Sir,
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px">
                <p> Thank you for forwarding us your valued inquiry. Based on information
                    furnished, we are pleased to submit our quotation as follows:</p></strong>
            </td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr>
            <td width="5%" align="right"><span style="font-weight: bold;font-size: 12px">I.</span></td>
            <td><span style="font-weight: bold;font-size: 12px">Scope Of Work </span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px">'.$header["scopeOfWork"] .'</td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">II.</span></td>
            <td><span style="font-weight: bold;font-size: 12px">Exclusion </span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px">'.$header["exclusions"] .'</td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">III.</span></td>
            <td><span style="font-weight: bold;font-size: 12px">Schedule of Price </span></td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="4" border="1" style="font-size: 12px">
        <thead>
        <tr>
            <th style="width: 5%">Item</th>
            <th style="width: 15%">Description</th>
            <th style="width: 5%">Qty</th>
            <th style="width: 10%">Unit Price</th>
            <th style="width:10%">Total Price</th>
        </tr>
        </thead>';

//        $discount = 0;
        $total = 0;
        if (!empty($detail)) {
            if($header['totDiscount'] > 0 && $header['showDiscountYN'] == 1) {
                foreach ($detail as $val) {
//                $discount += $val['discountedPrice'];
                    $expectedQty = 1;
                    if($val['expectedQty']){
                        $expectedQty = $val['expectedQty'];
                    }
                    $totalAmount = ($val['discountedPrice'] * ((100 + $header['totMargin'])/100));
                    $total += $totalAmount;
                    $html .= '<tr>
                    <td style="font-size: 12px">'.$val["itemSystemCode"] .'</td>
                    <td style="font-size: 12px">'.$val["itemDescription"] .'</td>
                    <td align="right" style="font-size: 12px">'.$val["expectedQty"] .'</td>
                    <td align="right" style="font-size: 12px">'.number_format($totalAmount/$expectedQty, $header['decimalPlace']) .'</td>
                    <td align="right" style="font-size: 12px">'.number_format($totalAmount, $header['decimalPlace']) .'</td>
                </tr>';
                }
                $html .= '<tr>
                    <td align="right" colspan="4"><span style="font-weight: bold;font-size: 12px">Sub Total (' . $header['transcurrencycode'] . ')</span></td>
                    <td align="right" style="font-size: 12px">'. number_format($total, $this->common_data['company_data']['company_default_decimal']) .'</td>
                   
                </tr>';
                $discountAmount = ($total / 100) * $header['totDiscount'];
                $html .= '<tr>
                    <td align="right" colspan="4"><span style="font-weight: bold;font-size: 12px">Discount (' . $header['totDiscount'] . '%' . ')</span></td>
                    <td align="right" style="font-size: 12px">'. number_format($discountAmount, $this->common_data['company_data']['company_default_decimal']) .'</td>
                   
                </tr>';
                $total = $total - $discountAmount;
            } else {
                foreach ($detail as $val) {
//                $discount += $val['discountedPrice'];
                    $expectedQty = 1;
                    if($val['expectedQty']){
                        $expectedQty = $val['expectedQty'];
                    }
                    $totalAmount = ($val['discountedPrice'] * ((100 + $header['totMargin'])/100)) * ((100 - $header['totDiscount'])/100);
                    $total += $totalAmount;
                    $html .= '<tr>
                    <td style="font-size: 12px">'.$val["itemSystemCode"] .'</td>
                    <td style="font-size: 12px">'.$val["itemDescription"] .'</td>
                    <td align="right" style="font-size: 12px">'.$val["expectedQty"] .'</td>
                    <td align="right" style="font-size: 12px">'.number_format($totalAmount/$expectedQty, $header['decimalPlace']) .'</td>
                    <td align="right" style="font-size: 12px">'.number_format($totalAmount, $header['decimalPlace']) .'</td>
                </tr>';
                }
            }
        }

        $total = number_format($total, $header['decimalPlace'], '.', '');
        $dicountExplode = explode('.',$total);
        //echo $dicountExplode[0];
        $numberinword= $this->numbertowords->convert_number($dicountExplode[0]);
        $discountAmount=$numberinword;
        if($dicountExplode) {
            if (isset($dicountExplode[1])) {
                if ( $header['transactionCurrencyest'] == 1) {
                    $discountAmount = $numberinword . ' and ' . $dicountExplode[1] . ' / 1000';
                } else {
                    $discountAmount = $numberinword . ' and ' . $dicountExplode[1] . ' / 100';
                }
            }
        }


        $html .='<tr>
            <td align="right" colspan="4"><span style="font-weight: bold;font-size: 12px">Total Amount('.$header['transcurrencycode'].')</span></td>
            <td align="right" style="font-size: 12px">'.number_format($total, $header['decimalPlace']).'</td>
        </tr>
        <tr>
            <td colspan="5" style="font-size: 12px">In words: <b>'.$currencyCode.' – '.$discountAmount.'</b></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;;font-size: 12px">
        <tr>
            <td width="5%" align="right"><span style="font-weight: bold;font-size: 12px">IV.</span></td>
            <td>
                <span style="font-weight: bold;font-size: 12px">Terms of Payment </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px">'.$header["paymentTerms"].'</td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">V.</span></td>
            <td>
               <span style="font-weight: bold;font-size: 12px">Delivery </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px">'.$header["deliveryTerms"].'</td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">VI.</span></td>
            <td>
                <span style="font-weight: bold;font-size: 12px">Warranty</span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px">'.$header["warranty"] .' Months</td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">VII.</span></td>
            <td>
                <span style="font-weight: bold;font-size: 12px">Validity</span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px">'.$header["validity"].'</td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="height:20px;"></td>
        </tr>
        <tr>
            <td style="height:20px;font-size: 12px">
                Please feel free to contact us for any further assistance.
            </td>
        </tr>
        <tr>
            <td style="height:20px;font-size: 12px">
                Yours truly,
            </td>
        </tr>
        <tr>
            <td style="height:20px;"></td>
        </tr>
        <tr>
            <td style="height:20px;font-size: 12px">
                For and on behalf of,
            </td>
        </tr>
        <tr>
            <td><span style="font-weight: bold;font-size: 12px">'.current_companyName().'</span></td>
        </tr>
    </table>
    <br>
    <br>
    <table style="width: 100%;font-size: 12px">
        <tr>
            <td style="width:30%;"><b>Prepared by:</b></td>
            <td style="width:30%;"><b>Reviewed by:</b></td>
            <td style="width:30%;"><b>Approved by:</b></td>
        </tr>
        <tr>
            <td style="width:30%;">'.$confirmedUser["Ename2"].' <br> Tel No: '.$confirmedUser["EcMobile"].' <br> email: '.$confirmedUser["EEmail"].' </td>
            <td style="width:30%;">'.$reviewedUser["Ename2"].' <br> Tel No: '.$reviewedUser["EcMobile"].' <br> email: '.$reviewedUser["EEmail"].' </td>
            <td style="width:30%;">'.$approvedUser["Ename2"].' <br> Tel No: '.$approvedUser["EcMobile"].' <br> email: '.$approvedUser["EEmail"].' </td>
        </tr>
    </table>
</div>';
if ($mode == 'pdf') {
    $mpdf->WriteHTML($html, 2);
    $mpdf->AddPage();
}

$html2 = '<div class="table-responsive"> 
<table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:100%;"><span style="font-weight: bold;font-size: 12px">Terms and Conditions</span></td>
        </tr>
        <tr>
            <td style="width:100%;">'.$header["termsAndCondition"].'</td>
        </tr>
        </tbody>
    </table>
</div>';
if ($mode == 'pdf') {
    $mpdf->WriteHTML($html2, 2);
}

if ($mode == 'html') {
    echo $html;
} else {
    if($pdfType == 1) {
        $mpdf->Output();
    } else {
        $path = UPLOAD_PATH_MFQ . $header["estimateMasterID"] . "-QUT-" . current_userID() . ".pdf";
        $mpdf->Output($path, 'F');
    }
}


?>
<script>
    a_link = "<?php echo site_url('MFQ_Estimate/fetch_quotation_view'); ?>/" + <?php echo $header['estimateMasterID'] ?> ;
    $("#a_link").attr("href", a_link);
</script>