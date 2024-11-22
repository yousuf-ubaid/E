<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage); 
?>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12">
        <div class="pull-right"><a href="#" class="btn btn-excel btn-xs" id="btn-excel" onclick="export_excel_prToGrv()">
                                   <!--onclick="var file = tableToExcel('pr_to_grvtbl', 'PR TO GRV Report'); $(this).attr('href', file);"-->
                <i class="fa fa-file-excel-o" aria-hidden="true"></i><?php echo $this->lang->line('common_excel'); ?> Excel
            </a></div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="reportHeaderColor" style="text-align: center">
            <strong><?php echo current_companyName(); ?></strong></div>
        <div class="reportHeader reportHeaderColor" style="text-align: center">
            <strong> PR to GRV Report</strong>
        </div>
    </div>
</div>
<br>
<?php   if(!empty($purchaserequestmaster))
                {?>
<div class="row" style="margin:1px;margin-top: 5px">
    <div class="col-md-12 " id="tbl_rpt_pr_to_grv">
        <div class="table-responsive" style="height: 500px">
            <table class="table table-bordered table-striped" id="pr_to_grvtbl"
                   style="width: 100%;border: 1px solid #cec8c8;">
                <thead class="thead">
                <tr>
                    <th>#</th>
                    <th> PR <?php echo $this->lang->line('common_number'); ?><!-- Number --></th>
                    <th style="width: 15%;">PR <?php echo $this->lang->line('common_date'); ?><!-- Date --></th>
                    <th style="width: 9%;">PR <?php echo $this->lang->line('common_comment'); ?><!-- Comment --></th>
                    <th style="width: 9%;">PR <?php echo $this->lang->line('common_approved'); ?><!-- Approved --></th>
                    <!--<th style="width: 9%;">PR Status</th>-->
                    <th style="width: 9%;"><?php echo $this->lang->line('procurement_approval_item_code'); ?><!-- Item Code --></th>
                    <th style="width: 30%;"><?php echo $this->lang->line('common_item_description'); ?><!-- Item Description --></th>
                    <th><?php echo $this->lang->line('common_unit'); ?><!-- Unit --></th>
                    <th style="width: 5%;">PR <?php echo $this->lang->line('common_qty'); ?><!-- Qty --></th>
                    <th style="border-left: 2px solid rgb(249 2 2);">PO <?php echo $this->lang->line('common_number'); ?><!-- Number --></th>
                    <th style="width: 10%">ETA</th>
                    <th style="width: 10%;"><?php echo $this->lang->line('common_supplier_code');?><!-- Supplier Code --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('common_supplier_name');?><!-- Supplier Name --></th>
                    <th  style="width: 11%;">PO <?php echo $this->lang->line('common_qty'); ?><!-- Qty --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('common_currency'); ?><!-- Currency --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('common_segment'); ?><!-- Segment --></th>
                    <th  style="width: 11%;">PO <?php echo $this->lang->line('common_cost'); ?><!-- Cost --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('common_confirmed_date'); ?><!-- Confirmed date --></th>
                    <th  style="width: 11%;">PO <?php echo $this->lang->line('procurement_approved_status'); ?><!-- Approved Status --></th>
                    <th  style="width: 11%;border-right: 2px solid rgb(249 2 2);"><?php echo $this->lang->line('common_approved_date'); ?><!-- Approved Date --></th>
                  <!--  <th  style="width: 11%;border-right: 2px solid rgb(249 2 2);">	PO Status</th>-->
                   <th  style="width: 11%;"><?php echo $this->lang->line('procurement_receipt_doc_number'); ?><!-- Receipt Doc Number --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('procurement_receiptDate'); ?><!-- Receipt Date --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('procurement_receipt_qty'); ?><!-- Receipt Qty --></th>
                    <th  style="width: 11%;"><?php echo $this->lang->line('procurement_receipt_status'); ?><!-- Receipt Status --></th>
                  <!--  <th  style="width: 11%;">Receipt Status</th>-->
                   <!-- <th  style="width: 11%;">Receipt Status</th>-->
                </tr>
                </thead>
                <tbody>

                <?php
                if(!empty($purchaserequestmaster))
                {
                $x = $thisPageStartNumber;
                foreach ($purchaserequestmaster as $val){ ?>
                <tr style=" ">
                    <td style=""><?php echo $x ?></td>
                    <td style="">
                        <a href="#" class="drill-down-cursor" onclick="documentPageView_modal('PRQ',<?php echo $val['purchaseRequestID']?>)"> <?php echo $val['purchaseRequestCode'] ?></a>




                    </td>
                    <td style=""><?php echo $val['documentDate'] ?></td>
                    <td style=""><?php echo $val['narration'] ?></td>

                    <td style="text-align: center"><?php
                        if($val['approvedYN']==1) {
                            echo '<span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">'.$this->lang->line('common_approved').' </span>';/* Approved */
                        }else {
                            echo '<span
                                    class="label"
                                        style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">'.$this->lang->line('common_not_approved').'</span>';/* Not Approved */
                        }

                    ?>
                        <?php
                    $item_index = 0;
                        foreach ($itemdetail as $val1){
                        if($val1['purchaseRequestID']  == $val['purchaseRequestID']) {
                            if($item_index > 0) {
                                echo '<tr>';
                                echo '<td colspan="5">&nbsp;</td>';
                            }
                            echo '<td >'.$val1['itemSystemCode'].'</td>';
                            echo '<td >'.$val1['itemDescription'].'</td>';
                            echo '<td >'.$val1['defaultUOM'].'</td>';
                            echo '<td style="border-right: 2px solid rgb(249 2 2);">'.$val1['requestedQty'].'</td>';

                            $PO_Index = 0;
                            if ($purchaseordermaster) {
                                foreach ($purchaseordermaster as $podetail) {
                                    if ($podetail['itemAutoID'] == $val1['itemAutoID'] && $podetail['purchaseRequestDetailsID'] == $val1['purchaseRequestDetailsID'] && $podetail['poamount']>0
                                        && ($podetail['poapprovedYN']!=null  || !empty( $podetail['poapprovedYN'])) && ( $podetail['poorderID']!='N/A')){
                                        if ($PO_Index > 0) {
                                            echo '<tr style="">';
                                            echo '<td colspan="5" >&nbsp;</td>';
                                            echo '<td colspan="4">&nbsp;</td>';

                                        }
                                        echo '<td style="border-left: 2px solid rgb(249 2 2);">
                                            
                                            <a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'PO\','.$podetail['purchaseOrderID'].')">  '.$podetail['purchaseOrderCode'].'</a>
                                          
                                            
                                            </td>';
                                        echo '<td>'.$podetail['podate'].'</td>';
                                        echo '<td>'.$podetail['supplierSystemCode'].'</td>';
                                        echo '<td>'.$podetail['supplierName'].'</td>';
                                        echo '<td>'.$podetail['reqpoqty'].'</td>';
                                        echo '<td>'.$podetail['pocurrency'].'</td>';
                                        echo '<td>'.$podetail['segmentCode'].'</td>';
                                        echo '<td style="text-align: right">'.number_format($podetail['poamount'],$podetail['podecimal']).'</td>';

                                        echo '<td style="text-align: right">'.$podetail['poconfirmeddate'].'</td>';
                                        if($podetail['poapprovedYN']==1) {
                                            echo '<td style="text-align: center"> <span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">'.$this->lang->line('common_yes').' </span> </td>';/* Yes */
                                        }else if($podetail['poapprovedYN']==0 && ( $podetail['poapprovedYN']!=null  || !empty( $podetail['poapprovedYN']))){
                                            echo '<td style="text-align: center"><span
                                    class="label"
                                        style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">'.$this->lang->line('common_no').'</span></td>';/* No */
                                        }else {
                                            echo '<td>&nbsp;</td>';
                                        }
                                        echo '<td style="text-align: right;border-right:  2px solid rgb(249 2 2);">'.$podetail['poapproveddate'].'</td>';

                                        $grv_bsi_index = 0;
                                        $tot = 0;
                                        if ($grvdetail) {

                                            foreach ($grvdetail as $grvbsi) {
                                              /*  if($grvbsi['masterIDcode']!='') {*/

                                                    if ($grvbsi['itemAutoID'] == $podetail['itemAutoID'] && $grvbsi['poorderID'] == $podetail['purchaseOrderDetailsID'] &&($grvbsi['poorderID']!='N/A') ) {

                                                        if ($grv_bsi_index > 0) {
                                                            echo '<tr style="">';
                                                            echo '<td colspan="9" style="border-right: 2px solid rgb(249 2 2);">&nbsp;</td>';
                                                            echo '<td colspan="11" style="border-right: 2px solid rgb(249 2 2);">&nbsp;</td>';

                                                        }


                                                        echo '<td style="border-left: 2px solid rgb(249 2 2);"> 
 <a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\''.$grvbsi['grvbsidoc'].'\','.$grvbsi['grvbsimasterID'].')">  '.$grvbsi['systemcode'].'</a></td>';
                                                        echo '<td > '.$grvbsi['grvbsiDate'].'</td>';
                                                        echo '<td > '.$grvbsi['grvqty'].'</td>';
                                                        /*if($grvbsi['grvbsiapprovedYN']==1) {
                                                            echo '<td style="text-align: center"> <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Yes </span> </td>';
                                                        }else if($grvbsi['grvbsiapprovedYN']==0 && ( $grvbsi['grvbsiapprovedYN']!=null  || !empty( $grvbsi['grvbsiapprovedYN']))){
                                                            echo '<td style="text-align: center"><span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">No</span></td>';
                                                        }else {
                                                            echo '<td>&nbsp;</td>';
                                                        }*/
                                                        if($podetail['reqpoqty'] > $grvbsi['grvqty'] && $grvbsi['grvqty'] > 0) {
                                                            echo '<td style="text-align: center"><span class="label" style="background-color: #f0ad4e; color: #FFFFFF; font-size: 11px;">'.$this->lang->line('procurement_partially_received').'</span></td>';/* Partially Received */
                                                        }else if($podetail['reqpoqty'] <= $grvbsi['grvqty']){
                                                            echo '<td style="text-align: center"> <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">'.$this->lang->line('procurement_fully_received').'</span> </td>';/* Fully Received */
                                                        }else {
                                                            echo '<td style="text-align: center"> <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">'.$this->lang->line('procurement_not_received').'</span> </td>';
                                                        }
                                                      /*  echo '<td style="text-align: center">
                                                            
                                                         <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">  </span> 
                                                
                                                        </td>';*/
                                                        /*echo '<td > '.$grvbsi['systemcode'].'</td>';*/



                                                        if ($grv_bsi_index > 0) {
                                                            echo '</tr>';
                                                        }
                                                        $grv_bsi_index++;
                                                    }


                                        }
                                        }
                                        if ($PO_Index > 0) {
                                            echo '</tr>';
                                        }
                                        $PO_Index++;
                                    }
                                }
                            }

                            $item_index++;
                            if ($item_index > 0) {
                                echo '</tr>';
                            }

                        }
                        }

                        ?>




                </tr>
                <?php
                $x++;
                }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php }else { ?>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert"><?php echo $this->lang->line('common_no_records_found');?><!--No Records found--></div>
        </div>
    </div>
<?php }?>
<script>

    $('#pr_to_grvtbl').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
</script>


