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
                            <td width="25%" valign="middle" height="40" style="font-size: 16px !important">
                                <img alt="Logo" style="height: 65px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td width="50%" valign="middle" height="40">
                                <table>
                                    <tr>
                                        <th style="text-align:center;font-size: 16px">
                                        <?php echo "RAY-Packers & Plugs Services"; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 12px">
                                       Service Completion Field Ticket															

                                        </th>
                                    </tr>
                                </table>        
                            </td>
                             <td width="25%" valign="middle" height="40" style="font-size: 16px !important;text-align:right">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </table>
        <input type="hidden" name="activity_billing_id_standard" id="activity_billing_id_standard" value="<?php echo $billing_id ?>" />
        <input type="hidden" name="activity_confirmed_yn_standard" id="activity_confirmed_yn_standard" value="<?php echo $report_header['confirmedYN'] ?>" />
    
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table>
                        <tbody>
                        <tr>
                        <td style="width: 43%">
                            <table style="width: 100%;"  class="table table-bordered-1">
                                <tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;">Ticket No:</td><!--Customer Name-->
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;"><?php echo $code ?></td>
                                </tr>
                                <tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;">Field:</td>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;"> <?php echo  $job_master['field_name'] ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Well Name: </td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;"><?php echo $well_name ?> </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Rig / Hoist No.</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;"> <?php echo $job_master['rig_hoist_name'] ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Client Representative: </td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;"><?php echo  $job_master['well_no_op'] ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">P.O.E.T. Number:</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;"><?php echo $job_code ?></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 10%"></td>
                        <td style="width: 47%">
                            <table style="width: 100%;"  class="table table-bordered-1">
                                <tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;">Agreement No:</td><!--Customer Name-->
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;"><?php echo $jobs_master_with_contract['contractCode'] ?> </td>
                                </tr>
                                <tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;">Client:</td>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;"><?php echo $jobs_master_with_contract['customerName'] ?> </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Contract/Block No:</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;"> <?php echo $jobs_master_with_contract['contractCode'] ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">RAY Operator:</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;"> <?php echo $jobs_master_with_contract['contactPersonName'] ?> </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Callout Date:</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">
                                        <table>
                                            <tr><td style="font-size:12px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important"><?php echo date('d/m/Y',strtotime($jobs_master_with_contract['job_date_from'])) ?></td><td style="font-size:12px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important; border-right:0 !important"><?php echo date('h:i',strtotime($jobs_master_with_contract['job_date_from'])) ?>  </td></tr>
                                        </table>    
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Date & Time of Arrival:</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">
                                        <table>
                                            <tr><td style="font-size:12px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important"><?php echo date('d/m/Y',strtotime($jobs_master_with_contract['job_date_from'])) ?></td><td style="font-size:12px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important; border-right:0 !important"><?php echo date('h:i',strtotime($jobs_master_with_contract['job_date_from'])) ?>  </td></tr>
                                        </table>    
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">Date & Time of Job Completion::</td>
                                    <td style="font-size:12px !Important;padding: 2px 5px;">
                                        <table>
                                            <tr><td style="font-size:12px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important"><?php echo date('d/m/Y',strtotime($jobs_master_with_contract['job_date_to'])) ?></td><td style="font-size:12px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important; border-right:0 !important"><?php echo date('h:i',strtotime($jobs_master_with_contract['job_date_to'])) ?>  </td></tr>
                                        </table>    
                                    </td>
                                </tr>
                            </table>
                        </td>
                        </tr>
                        
                    </tbody></table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="20px" style="height:20px">&nbsp;</td></tr>
        </table>    
        
        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <colgroup>
                            <col style="width: 3%; height:25px">
                            <col style="width: 30%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 7%; height:25px">
                            <col style="width: 14%; height:25px">
                            <col style="width: 7%; height:25px">
                            <col style="width: 9%; height:25px">
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">#</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">Description</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">Start Date</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">End Date</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">No. of Days</th>
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">Unit</th>
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">Total</th>
                            </tr>
                            <?php if(count($inv_data)>0) { 
                                foreach($inv_data as $key=>$val){
                            ?>
                            <tr>         
                                <td><?php echo $key+1 ?></td>
                                <td><?php echo $val['description'] ?></td>
                                <td><?php echo $val['fromdate'] ?></td>
                                <td><?php echo $val['todate'] ?></td>
                                <td><?php echo $val['qty'] ?></td>
                                <td> <?php echo $transactionCurrency.' : '.number_format((isset($val['unit']) ? $val['unit']:0),2) ; ?></td>
                                <td> <?php echo $transactionCurrency.' : '.number_format((isset($val['total']) ? $val['total']:0),2) ; ?></td>
                                
                            </tr>     
                           <?php } }else{ ?> 
                            <tr>         
                                <td class="text-center" colspan="7">Record Not Found</td>
                                
                                
                            </tr>  
                            <?php } ?>
                           
                    </tbody></table>
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
                        <colgroup>
                            <col style="width: 3%; height:25px">
                            <col style="width: 27%; height:25px">
                            <col style="width: 50%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">#</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">Description</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">Comments</th>  
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">Unit</th>
                                <th style="text-align:center;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">Days/Qty</th>
                            </tr>

                            <?php if(count($service_data)>0) { 
                                foreach($service_data as $key=>$val){
                            ?>
                            <tr>         
                                <td><?php echo $key+1 ?></td>
                                <td><?php echo $val['description'] ?></td>
                                <td><?php echo $val['comment'] ?></td>
                                <td> <?php echo $transactionCurrency.' : '.number_format((isset($val['unit']) ? $val['unit']:0),2) ; ?></td>
                                <td><?php echo $val['qty'] ?></td>
                            </tr>     
                           <?php } }else{ ?>
                           <tr>         
                                <td class="text-center" colspan="5">Record Not Found</td>
                                
                                
                            </tr>  
                            <?php } ?>
                           
                    </tbody></table>
                </div>
            </div>
            
        </div>

        <table>
            <tr><td height="15px" style="height:15px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-6 col-md-6">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <tbody>
                            <tr>
                                <td scope="col" valign="middle" height="30" style="text-align:left;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 20px;font-size: 12px !important;" colspan="2">Comments:</td>
                            </tr>
                            <tr>
                                <td scope="col" valign="middle" height="100" style="text-align:center" colspan="2">
                                    <textarea name="comment_standard" id="comment_standard"><?php echo $comment ?></textarea>
                                </td>
                            </tr>
                        
                    </tbody></table>
                </div>
            </div>
            <div class="col-6 col-md-6">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <tbody>
                            <tr>
                                <td scope="col" valign="middle" height="30" style="text-align:left;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 20px;font-size: 12px !important;" colspan="2">
                                    
                                        <table>
                                            <tr><td style="font-size:14px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important;font-weight:bold">Total Invoice Charges:</td>
                                            <td style="font-size:14px !Important;padding: 2px 5px;border-top:0 !important; border-bottom:0 !important; border-left:0 !important; border-right:0 !important;font-weight:bold"><?php echo $transactionCurrency.' : '.number_format((isset($total) ? $total:0),2) ; ?> </td></tr>
                                        </table>    
                                </td>
                            </tr>
                            <tr>
                                <td scope="col" valign="middle" height="10" style="text-align:center" colspan="2"></td>
                            </tr>
                            <tr>
                                <td scope="col" valign="middle" height="30" style="text-align:left;background:#ffff99;padding-bottom: 0px;padding-top: 0;height: 20px;font-size: 12px !important;" colspan="2">Client Representative Signature & Stamp:</td>
                            </tr>
                            <tr>
                                <td scope="col" valign="middle" height="60" style="text-align:center" colspan="2"></td>
                            </tr>
                            
                        
                    </tbody></table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <!-- <div class="row">
            <div class="col-12 col-md-12">  
                <button type="button" class="btn btn-primary-new size-sm float-right"><i class="fa fa-save"></i> Submit</button>
            </div>
        </div> -->

    </div>
</div>