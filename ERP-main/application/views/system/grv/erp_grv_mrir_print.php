<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$hideCost = getPolicyValues('HCG', 'All');
$itemBatch_policy = getPolicyValues('IB', 'All');

echo fetch_account_review(true,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:35%;">
                    <table>
                        <tbody>
                            <tr>
                            <td>
                                        <img alt="Logo" style="height: 100px" src="<?php
                                        echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width:65%;" align="left">
                    <table>
                        <tbody>
                                <tr>
                                    <td>
                                        <h2 style="font-size: 20px;">Rukun Al Yaqeen International L.L.C.</h2><!--Goods Received Voucher-->
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td align="center" style="width: 100%; border-top:1px solid #000; border-bottom:1px solid #000;">
                    <table>
                        <tr>
                            <td align="center"><h3 style="margin: 2px;font-size: 16px;">MRIR</h3></td>
                        </tr>
                        <tr>
                            <td align="center"><h3 style="margin: 2px;font-size: 16px;">MATERIAL RECEIPT INSPECTION REPORT / INWARD INSPECTION REPORT</h3></td>
                        </tr>
                    </table>
                </td>
            </tr> 
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>            
            <tr>            
                <td style="width:33%;">
                        <table>
                           
                            <tr>
                                <td><strong>Name of Supplier </strong></td><!--Name of Supplier-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['supplier']['supplierName'].' ('.$extra['supplier']['supplierSystemCode'].' ) '; ?></td>
                            </tr>
                            <tr>
                                <td><strong>DO No.</strong></td><!--DO No-->
                                <td><strong>:</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>Location</strong></td><!--Location-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['supplier']['supplierAddress1']; ?></td>
                            </tr>
                        </table>
                </td>
                <td style="width:33%;">
                        <table>
                           
                            <tr>
                                <td><strong>MRIR No.</strong></td><!--MRIR No-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvPrimaryCode']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>RAY PO No. </strong></td><!--RAY PO No-->
                                <td><strong>:</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>RAY PO Date</strong></td><!--RAY PO Date-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvDate']; ?></td>
                            </tr>
                        </table>
                </td>
                <td style="width:33%;">
                        <table>
                           
                            <tr>
                                <td><strong>GRN No.</strong></td><!--GRN No.-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvPrimaryCode']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>GRN Date</strong></td><!--GRN Date-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['grvDate']; ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><strong>:</strong></td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th class='theadtr' rowspan="2" >Part No</th><!--Part No-->
                <th class='theadtr' rowspan="2">Item Description</th>
                <th class='theadtr' rowspan="2">Unit</th>
                <th class='theadtr' rowspan="2">Offered<br>Quantity</th>
                <th class='theadtr' rowspan="2">Passed<br>Quantity</th>
                <th class='theadtr' rowspan="2">Failed<br>Quantity</th>
                <th class='theadtr' colspan="2">Inspection Comments</th>
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 25%">Pass Comment</th>
                <th class='theadtr' style="min-width: 5%">Failure Comment</th>
                
            </tr>
        </thead>
        <tbody id="grv_table_body">
            <?php $requested_total = 0;$received_total = 0;
            if (!empty($extra['detail'])) {
                for ($i=0; $i < count($extra['detail']); $i++) {
                    echo '<tr>';
                    echo '<td>'.($i+1).'</td>';

                
                    echo '<td>'.$extra['detail'][$i]['itemDescription'];
                    if($itemBatch_policy==1){
                        echo '<td class="text-center">'.$extra['detail'][$i]['batchNumber'].'</td>';
                        echo '<td class="text-center">'.$extra['detail'][$i]['batchExpireDate'].'</td>';
                    }

                    if(!empty($extra['detail'][$i]['comment']) && empty($extra['detail'][$i]['partNo']))
                    {
                        echo  ' - '. $extra['detail'][$i]['comment'];
                    }
                    else if(!empty($extra['detail'][$i]['comment']) && !empty($extra['detail'][$i]['partNo']))
                    {
                        echo  ' - '. $extra['detail'][$i]['comment'] . ' - ' . 'Part No : ' . $extra['detail'][$i]['partNo'];
                    }else if(!empty($extra['detail'][$i]['partNo']))
                    {
                        echo ' Part No : ' . $extra['detail'][$i]['partNo'];
                    }


                    '</td>';
                    echo '<td class="text-center">'.$extra['detail'][$i]['unitOfMeasure'].'</td>';
                    echo '<td class="text-right">'.$extra['detail'][$i]['requestedQty'].'</td>';
                    if ($hideCost == 0) {
                        echo '<td class="text-right">'.format_number($extra['detail'][$i]['requestedAmount'],$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                        echo '<td class="text-right">'.format_number(($extra['detail'][$i]['requestedQty']*$extra['detail'][$i]['requestedAmount']),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                    }

                    echo '<td class="text-right">'.$extra['detail'][$i]['receivedQty'].'</td>';

                    echo '<td class="text-right">'.$extra['detail'][$i]['receivedQty'].'</td>';
                    
                    echo '</tr>';
                    $requested_total += ($extra['detail'][$i]['requestedQty']*$extra['detail'][$i]['requestedAmount']);
                    if($isGroupBasedTaxEnable == 1){ 
                    $received_total += (($extra['detail'][$i]['receivedTotalAmount'])+($extra['detail'][$i]['taxAmount']));
                    }else{ 
                        $received_total += ($extra['detail'][$i]['receivedTotalAmount']);
                    }
                }
            }else{
                $norecfound=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="10" class="text-center"><b>'.$norecfound.'</b></td></tr>';
            }
            ?>
            <!--No Records Found-->
        </tbody>
        
    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>            
            <tr>            
                <td style="width:33%;">
                        <table>                          
                            <tr>
                                <td><strong>Note</strong></td><!--Location-->
                                <td><strong>:</strong></td>
                                <td>REJECTION IF ANY FOUND, PLEASE RAISE CAR/NCR AND STORE IN QUARANTINE AREA
                                    RECEIPT OF MATERIAL TEST CERTIFICATE : YES / NO / NOT APPLICABLE
                                    FAT STATUS : CONDUCTED / NOT CONDUCTEREJECTION IF ANY FOUND, PLEASE RAISE CAR/NCR AND STORE IN</td>
                            </tr>
                        </table>
                </td>
                
            </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%;">
        <thead>
            <tr>
                <th class='theadtr'></th>    
                <th class='theadtr'>MATERIAL CONTROLLER</th>
                <th class='theadtr'>NSPECTED BY</th>
                <th class='theadtr'>APPROVED BY CUSTOMER / INDENTER</th>                               
        </thead>
        <tbody>
            <tr>
                <td>NAME:</td>    
                <td></td>
                <td></td>
                <td></td>              
            </tr>             
            <tr>
                <td>SIGNATURE:</td>    
                <td></td>
                <td></td>
                <td></td>              
            </tr>  
            <tr>
                <td>DATE:</td>    
                <td></td>
                <td></td>
                <td></td>              
            </tr>      
        </tbody>
    </table>
</div>

<?php
    $document = array('GRV','GRV-ADD');
    $rcmApplicableYN = 0;
    $data['documentCode'] ="".join("' , '", $document)."";
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['grvAutoID'];
    
    if($extra['rcmApplicableYnpolicy'] ==1){
        $rcmApplicableYN = 1;
    }
    
    $data['isRcmDocument'] = $rcmApplicableYN;

    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);

?>

<br>


<?php if($extra['master']['approvedYN']){ ?>
<?php
if ($signature) { ?>
    <?php
    if ($signature['approvalSignatureLevel'] <= 2) {
        $width = "width: 40%";
    } else {
        $width = "width: 100%";
    }
    ?>
    <div class="table-responsive">
        <table style="<?php echo $width ?>">
            <tbody>
            <tr>
                <?php
                for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                    ?>

                    <td>
                        <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                    </td>

                    <?php
                }
                ?>
            </tr>


            </tbody>
        </table>
    </div>
<?php } ?>
<?php } ?>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Grv/load_grv_conformation'); ?>/<?php echo $extra['master']['grvAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + <?php echo $extra['master']['grvAutoID'] ?> + '/GRV';
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);
</script>