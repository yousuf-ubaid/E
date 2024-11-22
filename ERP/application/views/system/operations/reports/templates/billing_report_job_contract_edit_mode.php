
<?php
    $columns_order = array();
    $columns_total = array();
    $columns_unit_pricing = array();

   

?>

<input type="hidden" name="activity_billing_id" id="activity_billing_id_modify" value="<?php echo $billing_id ?>" />
<input type="hidden" name="activity_confirmed_yn" id="activity_confirmed_yn_modify" value="<?php echo $report_header['confirmedYN'] ?>" />

<div class="row">
    <div class="col-md-12" style="background: #fff;padding: 25px;">
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                <table>
                        <colgroup>
                            <col style="width: 25%; height:40px" />
                            <col style="width: 50%; height:40px" />
                            <col style="width: 25%; height:40px" />
                        </colgroup>
                        <tr>
                            <td width="25%" valign="middle" height="75px" style="font-size: 16px !important">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td width="50%" valign="middle" height="40">
                                <table>
                                    <tr>
                                        <th style="text-align:center;font-size: 18px">
                                            COST TRACKING ON DAILY BASIS FOR OQ	
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="25%" valign="middle" height="40" style="font-size: 16px !important;text-align:right">
                                <?php echo $job_master['rig_hoist_name'] ?> - <?php echo str_replace($companyCode.'/','',$report_header['code']) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 15%; height:40px" />
                            <col style="width: 35%; height:40px" />
                            <col style="width: 15%; height:40px" />
                            <col style="width: 35%; height:40px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:left;font-size:12px;width: 15%;border: 1px solid #dee2e6;" valign="middle" height="40">Well No : </th>
                            <th style="text-align:left;font-size:12px;width: 35%;border: 1px solid #dee2e6;" valign="middle" height="40"><?php echo $well_name ?></th>
                            <th style="text-align:left;font-size:12px;width: 15%;border: 1px solid #dee2e6;" valign="middle" height="40">Duration : </th>
                            <th style="text-align:left;font-size:12px;width: 35%;border: 1px solid #dee2e6;" valign="middle" height="40"><?php echo date('d/m/Y',strtotime($report_header['dateFrom'])).' - '.date('d/m/Y',strtotime($report_header['dateTo'])) ?></th>
                        </tr>                        
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <!-- <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">DATE</th> -->
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">TIME FROM</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">TIME TO</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">Moving Cost</th>
                            <?php foreach($get_defined_prices as $services){
                                $columns_order[] = $services['contractDetailsAutoID'];
                                $columns_unit_pricing[$services['contractDetailsAutoID']] = $services['unittransactionAmount'];
                                echo '<th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:10%">'.$services['categoryName'].'</th>';
                            } ?>
                            <!-- <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">AUTOMATED CAT WALK </th> -->
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">TOTAL (HOURS)</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">Additional Rental Cost</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:14%">DAILY COST</th>

                            <!-- <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:10%">MOVING (HR) </th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:10%">STANDBY (HR)</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:10%">CREW STACKING (HR)</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">OPERATING (HR) 24 Hour Daily</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">REPAIR (HR) 24 Hour Daily</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">Force Majeure (HR) 24 Hour Daily</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">ZERO (HR) 24 Hour Daily</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:8%">TOTAL (HOURS)</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:14%">DAILY COST</th> -->
                        </tr>
                        
                        <?php foreach($get_defined_shifts as $shifts){
                            $row_total_hours = 0; 
                            $daily_cost = 0;   
                        ?>
                            
                            <tr>
                                <!-- <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo date('Y-m-d',strtotime($shifts['dateFrom'])) ?></td> -->
                                <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo date('Y-m-d H:i:s',strtotime($shifts['dateFrom'])) ?></td>
                                <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo date('Y-m-d H:i:s',strtotime($shifts['dateTo'])) ?></td>
                                <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $transactionCurrency.' : '. number_format($shifts['movingCost'],2) ?></td>
                                <?php $daily_cost = $daily_cost + ($shifts['movingCost'] +$shifts['additionalCost']); ?>
                                <?php foreach($columns_order as $order_id){ 
                                    //print_r($order_id);exit;
                                    ?>
                                    
                                    <?php if($shifts['price_id']==$order_id){
                                        $row_total_hours = $row_total_hours + $shifts['qty'];  
                                        if(isset($columns_total[$order_id])){
                                            $columns_total[$order_id] =  $columns_total[$order_id] + $shifts['qty'];
                                        }else{
                                            $columns_total[$order_id] = $shifts['qty'];
                                        }

                                        $daily_cost = $daily_cost + ($shifts['qty']*$shifts['unit_amount']);
                                        
                                        echo ' <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;">'.round($shifts['qty'],2).'</td>';
                                    }else{
                                        echo ' <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;">'.''.'</td>';
                                    } ?>
                                   
                                <?php } ?>
                                <?php
                                    if(isset($columns_total['total_hrs'])){
                                        $columns_total['total_hrs'] = $columns_total['total_hrs'] + $row_total_hours;
                                    }else{
                                        $columns_total['total_hrs'] = $row_total_hours;
                                    }
                                    
                                    if(isset($columns_total['total_amount'])){
                                        $columns_total['total_amount'] = $columns_total['total_amount'] + $daily_cost;
                                    }else{
                                        $columns_total['total_amount'] = $daily_cost;
                                    }

                                   // $daily_cost = $daily_cost + $shifts['movingCost'];
                                ?>


                                <!-- <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"></td> -->
                                <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $row_total_hours ?></td>
                                <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $transactionCurrency.' : '. number_format($shifts['additionalCost'],2) ?></td>
                                <td style="text-align:right;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $transactionCurrency.' : '. number_format($daily_cost,2) ?></td>
                        
                        <?php } ?>
                        <tr>
                           
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;">TOTAL</td>
                            <?php
                            $row_total_moving=0;
                            $row_total_additional=0;
                                foreach($get_defined_shifts as $shifts){
                                 
                                     //$daily_total = $daily_total + ($shifts['movingCost'] +$shifts['additionalCost']);
                                     $row_total_moving =$row_total_moving+$shifts['movingCost'];
                                     $row_total_additional =$row_total_additional+$shifts['additionalCost'];
                                 }
                            ?>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $transactionCurrency.' : '. number_format($row_total_moving,2) ?></td>
                            <?php foreach($columns_order as $order_id){ ?>
                                <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo isset($columns_total[$order_id]) ? $columns_total[$order_id] : number_format(0,2) ?> </td>
                            <?php } ?>
                            <!-- <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"></td> -->
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo isset($columns_total['total_hrs']) ? $columns_total['total_hrs']:0; ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $transactionCurrency.' : '. number_format($row_total_additional,2) ?></td>
                            <td style="text-align:right;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $transactionCurrency.' : '.number_format((isset($columns_total['total_amount']) ? $columns_total['total_amount']:0),2) ; ?></td>
                        </tr>
                 
          
                        
                    </table>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table">                       
                        <tr>
                            <th style="text-align:left;font-size:12px;width: 30%;" valign="middle" height="40">Well Cost : </th>
                        </tr>                        
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">                        
                        <tr>
                            
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 10%;" valign="middle" height="30">Moving Cost</th>
                            <?php foreach($get_defined_prices as $services){
                                echo '<th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 10%;" valign="middle" height="30">'.$services['categoryName'].'</th>';
                             } ?>
                           
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 10%;" valign="middle" height="30">Additional Rental Cost</th>
                            <th style="text-align:center;background:#FFEB3B;font-size:16px !important;font-weight:bold;border: 1px solid #dee2e6;width: 20%;" valign="middle" height="30">Total</th>
                        </tr>
                        <tr>
                            <?php $daily_total = 0 ?>
                            <?php
                                $col_total_moving = 0; 
                                $col_total_additional = 0;
                                foreach($get_defined_shifts as $shifts){
                                 
                                // $daily_cost = 0;
                                    $daily_total = $daily_total + ($shifts['movingCost'] +$shifts['additionalCost']);
                                    $col_total_moving =$col_total_moving+$shifts['movingCost'];
                                    $col_total_additional =$col_total_additional+$shifts['additionalCost'];
                                }  
                            ?>
                            <td style="text-align:right;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="30"><?php echo $transactionCurrency.' : '.number_format($col_total_moving,2) ?></td>
                            <?php foreach($columns_order as $order_id){
                               // print_r($columns_total[$order_id]);exit;
                                if(isset($columns_total[$order_id]) && $columns_unit_pricing[$order_id]){
                                    $daily_total_price = $columns_total[$order_id] * $columns_unit_pricing[$order_id];
                                }else{
                                    $daily_total_price = 0;
                                }
                               
                                $daily_total = $daily_total + $daily_total_price;
                                echo '<td style="text-align:right;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="30">'. $transactionCurrency.' : '.number_format($daily_total_price,2).'</td>';
                            } ?>
                            
                            <td style="text-align:right;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="30"><?php echo $transactionCurrency.' : '.number_format($col_total_additional,2) ?></td>
                            <td style="text-align:right;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="30"><?php echo $transactionCurrency.' : '.number_format($daily_total,2) ?></td>

                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="15px" style="height:15px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 20%; height:30px" />
                            <col style="width: 80%; height:30px" />
                        </colgroup>
                        <!-- <tr>
                            <th style="text-align:left;font-size:12px;width: 20%;" valign="middle" height="30">Work Order :  </th>
                            <th style="text-align:left;font-size:12px;width: 80%;" valign="middle" height="30">24000109387</th>
                        </tr>         -->
                        <tr>
                            <th style="text-align:left;font-size:12px;width: 20%;" valign="middle" height="30">JOB  # :  </th>
                            <th style="text-align:left;font-size:12px;width: 80%;" valign="middle" height="30"><?php echo $job_code ?></th>
                        </tr> 
                        <!-- <tr>
                            <th style="text-align:left;font-size:12px;width: 20%;" valign="middle" height="30">Approved By :  </th>
                            <th style="text-align:left;font-size:12px;width: 80%;" valign="middle" height="30"> </th>
                        </tr>                  -->
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="15px" style="height:15px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <tbody>

                        <?php foreach($get_defined_prices as $services){
                            echo '<tr>';
                            echo '<td width="40%" style="font-size:12px;border: 1px solid #dee2e6;height:30px">'.$services['categoryName'].' Rate: </td>';
                            echo '<td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0">'.$services['itemReferenceNo'].' '.$transactionCurrency.' : '.number_format($services['unittransactionAmount'],2).'</td>';
                            echo '</tr>';
                       } ?>
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <!-- <div class="row">
            <div class="col-12 col-md-12">  
                <button type="button" class="btn btn-primary-new size-sm float-right" onclick="marked_as_confirm()"><i class="fa fa-save"></i> Confirm</button>
            </div>
        </div> -->
        
        <?php if($report_header['confirmedYN'] == 1){ 
            $confirmedName = explode('-',$report_header['confirmedBy']);    
        ?> 
        <div class="table-responsive"><br>
        <table style="width: 60%">
           <tbody>
                <tr>
                    <td><b>Confirmed By</b></td><!--Electronically Approved By -->
                    <td><strong>:</strong></td>
                    <td><?php echo $confirmedName[1] ?></td>
                </tr>
                <tr>
                    <td><b>Confirmed Date</b></td><!--Electronically Approved By -->
                    <td><strong>:</strong></td>
                    <td><?php echo $report_header['confirmedDate'] ?></td>
                </tr>
           </tbody>
        </table>
        </div>
        <?php } ?>
    </div>
</div>

