<?php
$d = $this->common_data['company_data']['company_default_decimal'];


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


if (!$pdf) {
    ?>
    <style>
        .customPad {
            padding: 3px 0px;
        }

        #sig_table th {
            padding: 15px 8px 15px 8px;
        }
    </style>
    <span class="pull-right">
    <!-- <button type="button" id="btn_print_sales2" class="btn btn-default btn-xs"> <i
            class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?></button> -->

        <!-- <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Sales_Report.xls"
           onclick="var file = tableToExcel('container_sales_report3', 'Sales Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a> -->

        <a onclick="generatePaymentSalesReportPdf()" target="_blank" class="btn btn-pdf btn-xs">
            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
        </a>
</span>
<?php } ?>
<div id="container_sales_report3">

    <!-- <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div> -->
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            
            <table class="<?php echo table_class_pos(5) ?>">
                <tbody>
                    <tr>
                        <th style="min-width: 30%"><?php echo $companyInfo['company_name'] ?></th>
                        <th class="text-center" style="min-width: 30%">
                        <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </th>
                        <th style="min-width: 30%">
                            <div class="pull-right">
                                TEL. NO.<!--Date-->:
                                    <strong><?php echo $companyInfo['company_phone'] ?></strong>

                                    <br/>FAX NO. <!--Time-->: <strong>
                                    <?php echo $companyInfo['company_phone'] ?></strong>

                                        <br/>EMAIL:<!--Time--> <strong>
                                        <?php echo $companyInfo['company_email'] ?></strong>
                            </div>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <hr style="margin:2px 0px;">

    <h3 class="text-center">SETTLEMENT SHEET </h3>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            
            <table class="<?php echo table_class_pos(5) ?>">
                <!-- <tbody>
                    <tr>
                        <th style="min-width: 10%">Salesman</th>
                        <th>: <?php echo $salesmans['Ename2'] ?></th>
                        <th style="min-width: 10%">No.</th>
                        <th>: 23S05065</th>
                    </tr>
                    <tr>
                        <th style="min-width: 10%">Location</th>
                        <th>: <?php echo $salesmans['EpAddress1'] ?></th>
                        <th style="min-width: 10%">Date</th>
                        <th>: 01-Sep-2023</th>
                    </tr>
                    <tr>
                        <th style="min-width: 10%">Gate Pass #</th>
                        <th>: GP23005087</th>
                        <th style="min-width: 10%">Page No.</th>
                        <th>: Page 1 of 2</th>
                    </tr>
                    <tr>
                        <th style="min-width: 10%">Driver</th>
                        <th>: PRASHANTH EDULAYA</th>
                        <th style="min-width: 10%">Truck No.</th>
                        <th>: -</th>
                    </tr>
                </tbody> -->
            </table>
        </div>
    </div>


    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">

        <table class="<?php echo table_class_pos(5) ?>">
            <thead>
            <tr>
                <th class="text-left" colspan="4" style="min-width: 10%">Salesman</th>
                <th class="text-left" colspan="4">: <?php echo $salesmans['Ename2'] ?></th>
                <th class="text-left" colspan="4" style="min-width: 10%">No.</th>
                <th class="text-left" colspan="5">: <?php echo substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,10); ?></th>
            </tr>
            <tr>
                <th class="text-left" colspan="4" style="min-width: 10%">Location</th>
                <th class="text-left" colspan="4">: <?php echo $salesmans['EpAddress1'] ?></th>
                <th class="text-left" colspan="4" style="min-width: 10%">Date</th>
                <th class="text-left" colspan="5">: <?php echo $this->common_data['current_date']; ?></th>
            </tr>
            <tr>
                <th class="text-left" colspan="4" style="min-width: 10%">Gate Pass #</th>
                <th class="text-left" colspan="4">: <?php echo $salesmans['licenceNo'] ?></th>
                <th class="text-left" colspan="4" style="min-width: 10%">Page No.</th>
                <th class="text-left" colspan="5">: </th>
            </tr>
            <tr>
                <th class="text-left" colspan="4" style="min-width: 10%">Driver</th>
                <th class="text-left" colspan="4">: <?php echo $salesmans['driverName'] ?></th>
                <th class="text-left"  colspan="4" style="min-width: 10%">Truck No.</th>
                <th class="text-left" colspan="5">: -</th>
            </tr>
            <tr>
                <th class="text-left" colspan="13"></th>
            
            </tr>
            <tr>
                <th>Sl. #</th>
                <th>Code</th>
                <th>Customer Name</th>
                <th>Inv. No.</th>
                <th>Product</th>

                <th>Qty</th>
                <th>Foc</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Oths.</th>
                <th>VAT %</th>
                <th>VAT</th>

            </tr>
            </thead>
                <tbody>
                    <?php

                    if (!empty($settlementDetails_cash)) { 
                        $qty_cash=0;
                        $qty_foc_cash=0;
                        $Amount=0;
                        $other=0;
                        $vat=0;
                        ?>
                        <tr>
                        <td class="text-center" colspan="13" style="font-weight: 900 !important;">Cash Sales </td></tr>
                        <?php
                        foreach ($settlementDetails_cash as $key=>$report2) {
                           
                            $Amount += $report2['price'];
                            $other += $report2['amount'];
                            $vat += $report2['taxAmount'];
                            

                            ?>
                            <tr>
                                <td><?php echo $key+1 ?> </td>
                                <td class="">
                                    <?php echo $report2['seconeryItemCode']; ?>
                                </td>
                                <td class="">
                                    <?php 
                                    if($report2['customerName']){
                                        echo $report2['customerName'];
                                    }else{
                                        echo "Cash";
                                    } ?>
                                </td>
                                <td class="">
                                    <?php echo $report2['invoiceCode']; ?>
                                </td>
                                <td class="">
                                    <?php echo $report2['itemDescription']; ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    if($report2['isFoc']==0){
                                        $qty_cash += $report2['qty'];
                                        echo $report2['qty'];
                                    }else{
                                        echo 0;
                                    }
                                     ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                        if($report2['isFoc']==1){
                                            $qty_foc_cash += $report2['qty'];
                                            echo $report2['qty'];
                                        }else{
                                            echo 0;
                                        }
                                     ?>
                                </td>
                                <td class="text-right">
                                    <?php echo $report2['unitOfMeasure']; ?>
                                </td>
                                <td class="text-right">
                                <?php echo  number_format($report2['transactionAmountBeforeDiscount'], $d)?>
                                </td>
                                <td class="text-right">
                                    <?php echo  number_format($report2['qty']*$report2['transactionAmountBeforeDiscount'], $d)?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($report2['amount'], $d)?>
                                </td>
                                <td class="text-right">
                                    <?php echo $report2['taxPercentage']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($report2['taxAmount'], $d)?>
                                </td>
                            
                            </tr>
                            <?php
                            
                        } ?>
                        
                            <tr>
                                <th colspan="5" style="font-weight: 900 !important;"> Total Cash Sales : </th>
                                <th class="text-right" style="font-weight: 900 !important;">
                                    <?php echo $qty_cash ?>
                                </th>
                                <th class="text-right" style="font-weight: 900 !important;">
                                <?php echo $qty_foc_cash ?>
                                </th>
                                <th class="text-right" colspan="3" style="font-weight: 900 !important;">
                                <?php echo   number_format($Amount, $d)?>
                                </th>
                                <th class="text-right" style="font-weight: 900 !important;">
                                <?php echo  number_format($other, $d)?>
                                </th>
                                <th class="text-right" colspan="2" style="font-weight: 900 !important;">
                                <?php echo  number_format($vat, $d)?>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="12" style="font-weight: 900 !important;"> Total </th>
                               
                                <th class="text-right" style="font-weight: 900 !important;">
                                <?php echo  number_format($Amount+$other+$vat, $d) ?> 
                                </th>
                            </tr>
                        
                    <?php } ?>

                    <?php
                    if (!empty($settlementDetails_credit)) {
                        $qty_cash1=0;
                        $qty_foc_cash1=0;
                        $Amount=0;
                        $other=0;
                        $vat=0;
                        ?>
                        <tr>
                        <td class="text-center" colspan="13" style="font-weight: 900 !important;">Credit Sales </td></tr>
                        <?php
                        foreach ($settlementDetails_credit as $key=>$report2) {
                            
                            $Amount += $report2['price'];
                            $other += $report2['amount'];
                            $vat += $report2['taxAmount'];

                            ?>
                            <tr>
                                <td><?php echo $key+1 ?> </td>
                                <td class="">
                                    <?php echo $report2['seconeryItemCode']; ?>
                                </td>
                                <td class="">
                                    <?php 
                                    if($report2['customerName']){
                                        echo $report2['customerName'];

                                    }else{
                                        echo "Credit";
                                    }
                                    ?>
                                </td>
                                <td class="">
                                    <?php echo $report2['invoiceCode']; ?>
                                </td>
                                <td class="">
                                    <?php echo $report2['itemDescription']; ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                        if($report2['isFoc']==0){
                                            $qty_cash1 += $report2['qty'];
                                            echo $report2['qty'];
                                        }else{
                                            echo 0;
                                        }
                                     ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                        if($report2['isFoc']==1){
                                            $qty_foc_cash1 += $report2['qty'];
                                            echo $report2['qty'];
                                        }else{
                                            echo 0;
                                        }
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php echo $report2['unitOfMeasure']; ?>
                                </td>
                                <td class="text-right">
                                <?php echo  number_format($report2['transactionAmountBeforeDiscount'], $d)?>
                                </td>
                                <td class="text-right">
                                <?php echo  number_format($report2['qty']*$report2['transactionAmountBeforeDiscount'], $d)?>
                                </td>
                                <td class="text-right">
                                    <?php echo  number_format($report2['amount'], $d)?>
                                </td>
                                <td class="text-right">
                                    <?php echo $report2['taxPercentage']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo  number_format($report2['taxAmount'], $d)?>
                                </td>
                            
                            </tr>
                            <?php
                        } ?>
                         <tfoot>
                            <tr>
                                <th colspan="5" style="font-weight: 900 !important;"> Total Credit Sales  : </th>
                                <th class="text-right" style="font-weight: 900 !important;">
                                    <?php echo $qty_cash1 ?>
                                </th>
                                <th class="text-right" style="font-weight: 900 !important;">
                                <?php echo $qty_foc_cash1 ?>
                                </th>
                                <th class="text-right" colspan="3" style="font-weight: 900 !important;">
                                <?php echo   number_format($Amount, $d) ?>
                                </th>
                                <th class="text-right" style="font-weight: 900 !important;">
                                <?php echo  number_format($other, $d) ?>
                                </th>
                                <th class="text-right" colspan="2" style="font-weight: 900 !important;">
                                <?php echo  number_format($vat, $d)?>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="12" style="font-weight: 900 !important;"> Total </th>
                               
                                <th class="text-right" style="font-weight: 900 !important;">
                                <?php echo  number_format($Amount+$other+$vat, $d) ?> 
                                </th>
                            </tr>
                            </tfoot>
                    <?php }
                    ?>
                </tbody>
           
        </table>
    </div>

    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">

    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        
        <tr>
            <th>Product</th>
            <th>Particulars</th>
            <th>Sales Qty </th>
            <th>Foc Qty</th>
            <th>Total Qty</th>

            <th>Sales Value</th>
            <th>Others</th>
            <th>VAT</th>
            <th>Avg. Rate</th>

        </tr>
        </thead>
            <tbody>
                <?php

                if (!empty($item_wise_details)) { 
                    $saleQty1=0;
                    $saleFoc1=0;
                    $saleTot1=0;
                    $saleval1=0;
                    $saleOther1=0;
                    $salevat1=0;
                    $vatRate1=0;
                    $itemArray=[];
                    ?>
                    
                    <?php
                    foreach ($item_wise_details as $key=>$report22) {

                        $specific_value=$report22['itemAutoID'];

                        $saleQty1 += $report22['saleQty'];
                        $saleFoc1 += $report22['focQty'];
                        $saleTot1 += $report22['totalQty'];
                        $saleval1 += $report22['saleValue'];
                        $saleOther1 += $report22['others'];
                        $salevat1 += $report22['vat'];
                        $vatRate1 += $report22['vatRate'];

                        if(!in_array($specific_value,$itemArray)){

                            $filtered_array = array_filter($item_wise_details, function ($obj) use ($specific_value) {
                                return $obj['itemAutoID'] == $specific_value;
                            });
                            $itemArray[]=$report22['itemAutoID'];

                            $saleQty=0;
                            $saleFoc=0;
                            $saleTot=0;
                            $saleval=0;
                            $saleOther=0;
                            $salevat=0;
                            $vatRate=0;
                            foreach ($filtered_array as $key1=>$report2) {

                                $saleQty += $report2['saleQty'];
                                $saleFoc += $report2['focQty'];
                                $saleTot += $report2['totalQty'];
                                $saleval += $report2['saleValue'];
                                $saleOther += $report2['others'];
                                $salevat += $report2['vat'];
                                $vatRate += $report2['vatRate'];
                            ?>
                            <tr>
                                <td><?php echo $report2['itemName'] ?> </td>
                                <td class="">
                                    <?php echo $report2['type']; ?>
                                </td>
                            
                                <td class="text-right">
                                    <?php echo $report2['saleQty']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo $report2['focQty']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo $report2['totalQty']; ?>
                                </td>
                                
                                <td class="text-right">
                                <?php echo  number_format($report2['saleValue'], $d)?>
                                </td>
                            
                                <td class="text-right">
                                    <?php echo number_format($report2['others'], $d)?>
                                </td>
                            
                                <td class="text-right">
                                    <?php echo number_format($report2['vat'], $d)?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($report2['vatRate'], $d) ?>
                                </td>
                            
                            </tr>
                            <?php
                            } ?>
                            <tr>
                                <td  colspan="2" style="font-weight: 900 !important;"> Total</td>
                                <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo $saleQty ?></td>
                                <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo $saleFoc ?></td>
                                <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo $saleTot ?></td>
                                <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo  number_format($saleval, $d)?></td>
                                <td  class="text-right"class="" style="font-weight: 900 !important;"><?php echo  number_format($saleOther, $d) ?></td>
                                <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo  number_format($salevat, $d)?></td>
                                <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo  number_format($vatRate, $d)?></td>
                            </tr>

                            

                        <?php
                    } } ?>

                        <tr>
                            <td  colspan="2" style="font-weight: 900 !important;">Grand Total</td>
                            <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo $saleQty1 ?></td>
                            <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo $saleFoc1 ?></td>
                            <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo $saleTot1 ?></td>
                            <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo  number_format($saleval1, $d)?></td>
                            <td  class="text-right"class="" style="font-weight: 900 !important;"><?php echo  number_format($saleOther1, $d) ?></td>
                            <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo  number_format($salevat1, $d)?></td>
                            <td class="text-right" class="" style="font-weight: 900 !important;"><?php echo  number_format($vatRate1, $d)?></td>
                        </tr>
                    
                <?php } ?>
            </tbody>
    
    </table>
    </div>

    <hr>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            
            <table class="" style="border: none;" id="sig_table">
                <tbody>
                    <tr>
                        <th style="min-width: 50%">Received by : ............................................</th>
                        <th style="min-width: 50%"></th>
                       
                    </tr>
                    <tr>
                        <th style="min-width: 50%">Signature : ..............................................</th>
                        <th style="min-width: 50%">Signature : .............................................. </th>
                      
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
   
</div>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<script>

    $(document).ready(function (e) {
        $("#btn_print_sales2").click(function (e) {
            $.print("#container_sales_report3");
        });
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = hour + ":" + minute + " " + ampm;
        $(".pcCurrentTime").html(date);
    })

    function generatePaymentSalesReportPdf() {
        var form = document.getElementById('frm_salesReport2');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_general_report/sales_report_pdf_settlement'); ?>';
        form.submit();
    }


</script>