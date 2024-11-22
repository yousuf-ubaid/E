<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_expanse_claim', $primaryLanguage);
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, true, $approval);

?>

<div class="table-responsive">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
</div>
<div class="table-responsive">
    <table class="table table-bordered" style="width: 50%;">
        <tr><td style="font-size: 11px; border: 1px solid black; border-bottom: none;">Consignee</tr>
        <tr><td style="font-size: 11px; border: 1px solid black; border-bottom: none;"><?php echo $extra['master']['customerName']; ?></td></tr>
        <tr><td style="font-size: 11px; border: 1px solid black; border-bottom: none;"><?php echo $extra['master']['customerAddress1']; ?></td></tr>
        <tr><td style="font-size: 11px; border: 1px solid black;"><?php echo $extra['master']['customerCountry']; ?></td></tr>
    </table>
</div>
<br>
<div class="table-responsive">
    <table  style="width: 100%">
        <tbody>
        <tr>
            <td style="width: 60%;">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="width: 25%;border: 1px solid black; font-size: 11px; border-bottom: none; border-right:none;"><strong>Invoice No :</strong></td>
                        <td style="border: 1px solid black; border-bottom: none;font-size: 13px;"><?php echo $extra['master']['ticketNo']; ?></td>
                    </tr>
                    <tr>
                        <td style="width: 25%;border: 1px solid black; font-size: 11px; border-bottom: none; border-right:none;"><strong>PO No :</strong></td>
                        <td style="border: 1px solid black; border-bottom: none;font-size: 13px;"><?php echo $extra['master']['contractRefNo']; ?></td>
                    </tr>
                    <tr>
                        <td style="width: 25%;border: 1px solid black; font-size: 11px; border-right:none;"><strong>Area :</strong></td>
                        <td style="border: 1px solid black;font-size: 13px;"><?php echo $extra['master']['fieldName']; ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 40%;">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="border: 1px solid black; border-right: none;border-bottom: none;"><strong> Date :</strong></td>
                        <td style="border: 1px solid black;border-bottom: none;font-size: 13px;"> <?php echo $extra['master']['createdDate']; ?></td>
                    </tr>
                    <?php
                    $dat =date('Y', strtotime($extra['master']['createdDateTime']));
                    $mnth =date('F', strtotime($extra['master']['createdDateTime']));

                    $txt="Month of ".$mnth .' '. $dat;
                    ?>
                    <tr>
                        <td colspan="2" style="border: 1px solid black;font-size: 13px;">( <?php echo $txt; ?>)</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<h5>Product Details </h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width:4%; height:20px;" class='theadtr'>Sr No</th>
            <th style="min-width:30%; height:20px;" class="text-left theadtr">Item Description</th>
            <th style="min-width:10%; height:20px;" class='theadtr'>UOM</th>
            <th style="min-width:5%; height:20px;" class='theadtr'>Qty</th>
            <th style="min-width:5%; height:20px;" class='theadtr'>Unit Rate</th>
            <th style="min-width:5%; height:20px;" class='theadtr'>Total Value</th>
            <th style="min-width:10%; height:20px;" class='theadtr'>Previous Certified (%)</th>
            <th style="min-width:10%; height:20px;" class='theadtr'>Current Month Certified (%)</th>
            <th style="min-width:10%; height:20px;" class='theadtr'>Cummulative (%)</th>
            <th style="min-width:10%; height:20px;" class='theadtr'>Previous Certified Amount</th>
            <th style="min-width:10%; height:20px;" class='theadtr'>This Month Invoice</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num =1;$item_total = 0;
        $is_item_active = 1;
        if (!empty($extra['product'])) {
            foreach ($extra['product'] as $value) {

                    $reqqty['requestedQty']=0;
                    $reqqty['amount']=0;
                    $previousCertifiedAmount=0;
                    $companyID=current_companyID();
                    $contractDetailID=$value['contractDetailID'];
                    $addedmnth=$value['addedmnth']-1;
                    $Description=$value['Description'];


               $previousPer = $this->db->query("SELECT
	addedDate,	IFNULL(SUM(percentage) ,0) as previousPer  
FROM
	product_service_details
	LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = product_service_details.Unit 
WHERE
	product_service_details.companyID = '$companyID' 
	AND Description = '$Description'
	AND typeId = '1'
	GROUP BY Description
	HAVING
	 $addedmnth=MONTH(addedDate) ")->row_array();

                    if(!empty($previousPer)){
                        $prvioussum= $previousPer['previousPer'];
                    }else{
                        $prvioussum=0;
                    }
                $callofId=$extra['master']['callofId'];
                $totalPercentage = $this->db->query("SELECT IFNULL(sum(percentage),0) as totalPercentage FROM ticketmaster INNER JOIN product_service_details ON ticketmaster.ticketidAtuto = product_service_details.ticketidAtuto WHERE calloffID = {$callofId} AND typeId = '1' AND Description = '$Description' AND contractDetailID = $contractDetailID ")->row_array();
                    ?>
                <tr>
                    <td><?php echo $num ?></td>
                    <td><?php echo $value['Description'] ?></td>
                    <td><?php echo $value['uom'] ?></td>
                    <td><?php echo $value['Qty'] ?></td>
                    <td><?php echo round($value['UnitRate'],3) ?></td>
                    <td><?php echo round($value['UnitRate']*$value['Qty'],3) ?></td>
                    <td><?php echo round($prvioussum,3) ?> %</td>
                    <td><?php echo $value['percentage'] ?> %</td>
                    <td><?php echo $totalPercentage['totalPercentage'] ?> %</td>
                    <td><?php echo round($prvioussum*$value['UnitRate'],3) ?></td>
                    <td class="text-right"><?php echo round($value['TotalCharges'],3) ?> </td>
                </tr>
                <?php
                $num ++;
                $item_total += $value['TotalCharges'];
            }
        } else {
            $NoRecordsFound =   $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="11" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right" style="font-size: 10px;" colspan="10">Total Amount </td>
            <td class="text-right " style="font-size: 10px;"><?php echo $item_total ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>



<table class="table " style="border: 2px solid #ececec; width: 100%;">
    <tr>
        <th style="border: 2px solid #ececec; font-size: 9px;">Dant Najd Trading & Transport </th>
        <th style="border: 2px solid #ececec; font-size: 9px;">AHPS Representative</th>
        <th style="border: 2px solid #ececec; font-size: 9px;">AHPS Representative</th>
    </tr>
    <tr>
        <td height="60px;" style="font-size: 9px;">Name :  Mohmoud Al Daril</td>
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







